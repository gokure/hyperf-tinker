<?php

declare(strict_types=1);
/**
 * This file is part of gokure/hyperf-cors.
 *
 * @link     https://github.com/gokure/hyperf-cors
 * @document https://github.com/gokure/hyperf-cors/blob/main/README.md
 * @contact  gokure@gmail.com
 * @license  https://github.com/hyperf/hyperf-cors/blob/main/LICENSE
 */

namespace Gokure\HyperfTinker;

use Hyperf\Collection\Collection;
use Psy\Shell;

use function Hyperf\Support\class_basename;

class ClassAliasAutoloader
{
    /**
     * The shell instance.
     */
    protected Shell $shell;

    /**
     * All of the discovered classes.
     */
    protected array $classes = [];

    /**
     * Path to the vendor directory.
     */
    protected string $vendorPath;

    /**
     * Explicitly included namespaces/classes.
     */
    protected Collection $includedAliases;

    /**
     * Excluded namespaces/classes.
     */
    protected Collection $excludedAliases;

    /**
     * Create a new alias loader instance.
     */
    public function __construct(Shell $shell, string $classMapPath, array $includedAliases = [], array $excludedAliases = [])
    {
        $this->shell = $shell;
        $this->vendorPath = dirname($classMapPath, 2);
        $this->includedAliases = new Collection($includedAliases);
        $this->excludedAliases = new Collection($excludedAliases);

        $classes = require $classMapPath;

        foreach ($classes as $class => $path) {
            if (! $this->isAliasable($class, $path)) {
                continue;
            }

            $name = class_basename($class);

            if (! isset($this->classes[$name])) {
                $this->classes[$name] = $class;
            }
        }
    }

    /**
     * Handle the destruction of the instance.
     */
    public function __destruct()
    {
        $this->unregister();
    }

    /**
     * Register a new alias loader instance.
     *
     * @param mixed $classMapPath
     */
    public static function register(Shell $shell, string $classMapPath, array $includedAliases = [], array $excludedAliases = []): static
    {
        $loader = new static($shell, $classMapPath, $includedAliases, $excludedAliases);
        spl_autoload_register([$loader, 'aliasClass']);

        return $loader;
    }

    /**
     * Find the closest class by name.
     * @param mixed $class
     */
    public function aliasClass($class): void
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
     */
    public function unregister(): void
    {
        spl_autoload_unregister([$this, 'aliasClass']);
    }

    /**
     * Whether a class may be aliased.
     *
     * @param string $class
     * @param string $path
     */
    public function isAliasable($class, $path): bool
    {
        if (! str_contains($class, '\\')) {
            return false;
        }

        if (! $this->includedAliases->filter(function ($alias) use ($class) {
            return str_starts_with($class, (string) $alias);
        })->isEmpty()) {
            return true;
        }

        if (str_starts_with($path, $this->vendorPath)) {
            return false;
        }

        if (! $this->excludedAliases->filter(function ($alias) use ($class) {
            return str_starts_with($class, (string) $alias);
        })->isEmpty()) {
            return false;
        }

        return true;
    }
}
