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
use Hyperf\Database\Model\Model;
use Hyperf\Stringable\Stringable;
use Symfony\Component\VarDumper\Caster\Caster;

class TinkerCaster
{
    /**
     * Get an array representing the properties of a collection.
     *
     * @param Collection $collection
     * @return array
     */
    public static function castCollection($collection)
    {
        return [
            Caster::PREFIX_VIRTUAL . 'all' => $collection->all(),
        ];
    }

    /**
     * Get an array representing the properties of a fluent string.
     *
     * @param Stringable $stringable
     * @return array
     */
    public static function castStringable($stringable)
    {
        return [
            Caster::PREFIX_VIRTUAL . 'value' => (string) $stringable,
        ];
    }

    /**
     * Get an array representing the properties of a model.
     *
     * @param Model $model
     * @return array
     */
    public static function castModel($model)
    {
        $attributes = array_merge(
            $model->getAttributes(),
            $model->getRelations()
        );

        $visible = array_flip(
            $model->getVisible() ?: array_diff(array_keys($attributes), $model->getHidden())
        );

        $hidden = array_flip($model->getHidden());

        $appends = (function () {
            return array_combine($this->appends, $this->appends);
        })->bindTo($model, $model)();

        foreach ($appends as $appended) {
            $attributes[$appended] = $model->{$appended};
        }

        $results = [];

        foreach ($attributes as $key => $value) {
            $prefix = '';

            if (isset($visible[$key])) {
                $prefix = Caster::PREFIX_VIRTUAL;
            }

            if (isset($hidden[$key])) {
                $prefix = Caster::PREFIX_PROTECTED;
            }

            $results[$prefix . $key] = $value;
        }

        return $results;
    }
}
