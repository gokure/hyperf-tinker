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

namespace Gokure\HyperfTinker\Tests\Cases;

use Bar;
use Gokure\HyperfTinker\ClassAliasAutoloader;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psy\Shell;
use Three;

/**
 * @internal
 * @coversNothing
 */
class ClassAliasAutoloaderTest extends TestCase
{
    protected string $classmapPath;

    protected ?ClassAliasAutoloader $loader = null;

    protected function setUp(): void
    {
        $this->classmapPath = dirname(__FILE__, 2) . '/fixtures/vendor/composer/autoload_classmap.php';
    }

    protected function tearDown(): void
    {
        if ($this->loader) {
            $this->loader->unregister();
        }
    }

    public function testCanAliasClasses()
    {
        $this->loader = ClassAliasAutoloader::register(
            $shell = Mockery::mock(Shell::class),
            $this->classmapPath
        );

        $shell->shouldReceive('writeStdout')
            ->with("[!] Aliasing 'Bar' to 'App\\Foo\\Bar' for this Tinker session.\n")
            ->once();

        $this->assertTrue(class_exists('Bar'));
        $this->assertInstanceOf(\App\Foo\Bar::class, new Bar());
    }

    public function testCanExcludeNamespacesFromAliasing()
    {
        $this->loader = ClassAliasAutoloader::register(
            $shell = Mockery::mock(Shell::class),
            $this->classmapPath,
            [],
            ['App\Baz']
        );

        $shell->shouldNotReceive('writeStdout');

        $this->assertFalse(class_exists('Qux'));
    }

    public function testVendorClassesAreExcluded()
    {
        $this->loader = ClassAliasAutoloader::register(
            $shell = Mockery::mock(Shell::class),
            $this->classmapPath
        );

        $shell->shouldNotReceive('writeStdout');

        $this->assertFalse(class_exists('Three'));
    }

    public function testVendorClassesCanBeWhitelisted()
    {
        $this->loader = ClassAliasAutoloader::register(
            $shell = Mockery::mock(Shell::class),
            $this->classmapPath,
            ['One\Two']
        );

        $shell->shouldReceive('writeStdout')
            ->with("[!] Aliasing 'Three' to 'One\\Two\\Three' for this Tinker session.\n")
            ->once();

        $this->assertTrue(class_exists('Three'));
        $this->assertInstanceOf(\One\Two\Three::class, new Three());
    }
}
