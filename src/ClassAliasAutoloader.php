<?php

declare(strict_types=1);

namespace Gokure\HyperfTinker;

use Psy\Shell;

class ClassAliasAutoloader
{
    /**
     * The shell instance.
     *
     * @var \Psy\Shell
     */
    protected $shell;

    /**
     * All of the discovered classes.
     *
     * @var array
     */
    protected $classes = [];

    /**
     * Path to the vendor directory.
     *
     * @var string
     */
    protected $vendorPath;

    /**
     * Explicitly included namespaces/classes.
     *
     * @var \Hyperf\Collection\Collection|\Hyperf\Utils\Collection
     */
    protected $includedAliases;

    /**
     * Excluded namespaces/classes.
     *
     * @var \Hyperf\Collection\Collection|\Hyperf\Utils\Collection
     */
    protected $excludedAliases;

    /**
     * Register a new alias loader instance.
     *
     * @param  \Psy\Shell  $shell
     * @param  string  $classMapPath
     * @param  array   $includedAliases
     * @param  array   $excludedAliases
     * @return static
     */
    public static function register(Shell $shell, $classMapPath, array $includedAliases = [], array $excludedAliases = [])
    {
        $loader = new static($shell, $classMapPath, $includedAliases, $excludedAliases);
        spl_autoload_register([$loader, 'aliasClass']);

        return $loader;
    }

    /**
     * Create a new alias loader instance.
     *
     * @param  \Psy\Shell  $shell
     * @param  string  $classMapPath
     * @param  array  $includedAliases
     * @param  array  $excludedAliases
     * @return void
     */
    public function __construct(Shell $shell, $classMapPath, array $includedAliases = [], array $excludedAliases = [])
    {
        $Collection = class_exists(\Hyperf\Collection\Collection::class)
            ? \Hyperf\Collection\Collection::class
            : \Hyperf\Utils\Collection::class;

        $this->shell = $shell;
        $this->vendorPath = dirname($classMapPath, 2);
        $this->includedAliases = new $Collection($includedAliases);
        $this->excludedAliases = new $Collection($excludedAliases);

        $classes = require $classMapPath;

        $class_basename = function_exists('\Hyperf\Support\class_basename')
            ? '\Hyperf\Support\class_basename'
            : 'class_basename';

        foreach ($classes as $class => $path) {
            if (! $this->isAliasable($class, $path)) {
                continue;
            }

            $name = $class_basename($class);

            if (! isset($this->classes[$name])) {
                $this->classes[$name] = $class;
            }
        }
    }

    /**
     * Find the closest class by name.
     *
     * @param  string  $class
     * @return void
     */
    public function aliasClass($class)
    {
        if (str_contains($class, '\\')) {
            return;
        }

        $fullName = $this->classes[$class] ?? false;

        if ($fullName) {
            $this->shell->writeStdout("[!] Aliasing '{$class}' to '{$fullName}' for this Tinker session.\n");

            class_alias($fullName, $class);
        }
    }

    /**
     * Unregister the alias loader instance.
     *
     * @return void
     */
    public function unregister()
    {
        spl_autoload_unregister([$this, 'aliasClass']);
    }

    /**
     * Handle the destruction of the instance.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->unregister();
    }

    /**
     * Whether a class may be aliased.
     *
     * @param  string  $class
     * @param  string  $path
     */
    public function isAliasable($class, $path)
    {
        if (! str_contains($class, '\\')) {
            return false;
        }

        if (! $this->includedAliases->filter(function ($alias) use ($class) {
            return str_starts_with($class, $alias);
        })->isEmpty()) {
            return true;
        }

        if (str_starts_with($path, $this->vendorPath)) {
            return false;
        }

        if (! $this->excludedAliases->filter(function ($alias) use ($class) {
            return str_starts_with($class, $alias);
        })->isEmpty()) {
            return false;
        }

        return true;
    }
}
