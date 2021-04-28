<?php

namespace App;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{

    protected $casts = [
        'is_local' => 'boolean',
    ];
    protected $primaryKey = 'container_id';
    protected $fillable = [
        'container_number',
        'container_type',
        'capacity',
        'created_by',
        'is_local',
        'local_code'
    ];

    public static function findOrCreate($containerNumber, $userId)
    {
        $container = static::where('container_number', $containerNumber)->first();

        if (!$container) {
            $containerCode = preg_replace('/[0-9]+/', '', $containerNumber);

            $containerDetail = Arr::first(containerType(), function ($detail) use ($containerCode) {
                return $detail['code'] == $containerCode;
            });

            if (!$containerDetail) {
                throw new Exception('Container type not found.', 400);
            }

            $container = new self();
            $container->container_number = $containerNumber;
            $container->container_type = $containerDetail['id'];
            $container->capacity = 100;
            $container->created_by = $userId;

            $container->save();
        }

        return $container;
    }
}
