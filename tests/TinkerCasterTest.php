<?php

namespace Gokure\HyperfTinker\Tests;

use Hyperf\Utils\Collection;
use Gokure\HyperfTinker\TinkerCaster;
use PHPUnit\Framework\TestCase;

class TinkerCasterTest extends TestCase
{
    public function testCanCastCollection()
    {
        $caster = new TinkerCaster;

        $result = $caster->castCollection(new Collection(['foo', 'bar']));

        $this->assertSame([['foo', 'bar']], array_values($result));
    }
}
