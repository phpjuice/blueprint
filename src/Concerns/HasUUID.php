<?php

namespace PHPJuice\Blueprint\Concerns;

use Illuminate\Support\Str;

trait HasUUID
{
    /**
     * @codeCoverageIgnore
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
        // when saving a model we make sure
        // uuid didn't change
        self::saving(function ($model) {
            $original_uuid = $model->getOriginal('uuid');
            if ($original_uuid !== $model->uuid) {
                $model->uuid = $original_uuid;
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
