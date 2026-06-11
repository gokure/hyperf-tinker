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

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'commands' => [
                TinkerCommand::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for Tinker.',
                    'source' => __DIR__ . '/../publish/tinker.php',
                    'destination' => BASE_PATH . '/config/autoload/tinker.php',
                ],
            ],
        ];
    }
}
