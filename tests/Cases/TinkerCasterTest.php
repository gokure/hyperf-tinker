<?php

namespace Gokure\HyperfTinker\Tests\Cases;

use App\Model\User;
use Gokure\HyperfTinker\TinkerCaster;
use PHPUnit\Framework\TestCase;

class TinkerCasterTest extends TestCase
{
    public function testCanCastCollection()
    {
        $Collection = class_exists(\Hyperf\Collection\Collection::class)
            ? \Hyperf\Collection\Collection::class
            : \Hyperf\Utils\Collection::class;
        $result = TinkerCaster::castCollection(new $Collection(['foo', 'bar']));

        $this->assertSame([['foo', 'bar']], array_values($result));
    }

    public function testCancastStringable()
    {
        $Stringable = class_exists(\Hyperf\Stringable\Stringable::class)
            ? \Hyperf\Stringable\Stringable::class
            : \Hyperf\Utils\Stringable::class;
        $result = TinkerCaster::castStringable(new $Stringable('foobar'));

        $this->assertSame(['foobar'], array_values($result));
    }

    public function testCanCastModel()
    {
        $user = new User(['first_name' => 'Gang', 'last_name' => 'Wu', 'secret' => 'my secret']);

        $result = TinkerCaster::castModel($user);

        $this->assertSame(['Gang', 'Wu', 'my secret', 'Gang Wu'], array_values($result));
    }
}
