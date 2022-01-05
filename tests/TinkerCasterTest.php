<?php

namespace Gokure\HyperfTinker\Tests;

use App\Model\User;
use Hyperf\Utils\Collection;
use Gokure\HyperfTinker\TinkerCaster;
use Hyperf\Utils\Stringable;
use PHPUnit\Framework\TestCase;

class TinkerCasterTest extends TestCase
{
    public function testCanCastCollection()
    {
        $caster = new TinkerCaster();

        $result = $caster->castCollection(new Collection(['foo', 'bar']));

        $this->assertSame([['foo', 'bar']], array_values($result));
    }

    public function testCancastStringable()
    {
        $caster = new TinkerCaster();

        $result = $caster->castStringable(new Stringable('foobar'));

        $this->assertSame(['foobar'], array_values($result));
    }

    public function testCanCastModel()
    {
        $caster = new TinkerCaster();

        $user = new User(['first_name' => 'Gang', 'last_name' => 'Wu', 'secret' => 'my secret']);

        $result = $caster->castModel($user);

        $this->assertSame(['Gang', 'Wu', 'my secret', 'Gang Wu'], array_values($result));
    }
}
