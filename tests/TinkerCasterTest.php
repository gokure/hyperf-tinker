<?php

namespace Gokure\HyperfTinker\Tests;

use App\Model\User;
use Hyperf\Utils\Collection;
use Gokure\HyperfTinker\TinkerCaster;
use PHPUnit\Framework\TestCase;

class TinkerCasterTest extends TestCase
{
    public function testCanCastCollection()
    {
        $result = TinkerCaster::castCollection(new Collection(['foo', 'bar']));

        $this->assertSame([['foo', 'bar']], array_values($result));
    }

    public function testCancastStringable()
    {
        if (class_exists('Hyperf\Utils\Stringable')) {
            $result = TinkerCaster::castStringable(new \Hyperf\Utils\Stringable('foobar'));

            $this->assertSame(['foobar'], array_values($result));
        } else {
            $this->markTestSkipped('skipped.');
        }
    }

    public function testCanCastModel()
    {
        $user = new User(['first_name' => 'Gang', 'last_name' => 'Wu', 'secret' => 'my secret']);

        $result = TinkerCaster::castModel($user);

        $this->assertSame(['Gang', 'Wu', 'my secret', 'Gang Wu'], array_values($result));
    }
}
