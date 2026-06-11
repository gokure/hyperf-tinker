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

use App\Model\User;
use Gokure\HyperfTinker\TinkerCaster;
use Hyperf\Collection\Collection;
use Hyperf\Stringable\Stringable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class TinkerCasterTest extends TestCase
{
    public function testCanCastCollection()
    {
        $result = TinkerCaster::castCollection(new Collection(['foo', 'bar']));

        $this->assertSame([['foo', 'bar']], array_values($result));
    }

    public function testCancastStringable()
    {
        $result = TinkerCaster::castStringable(new Stringable('foobar'));

        $this->assertSame(['foobar'], array_values($result));
    }

    public function testCanCastModel()
    {
        $user = new User(['first_name' => 'Gang', 'last_name' => 'Wu', 'secret' => 'my secret']);

        $result = TinkerCaster::castModel($user);

        $this->assertSame(['Gang', 'Wu', 'my secret', 'Gang Wu'], array_values($result));
    }
}
