<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupsModel extends Model
{
    protected $table = 'groups';

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {
            $timestamp = time();
            $model->created_at = $timestamp;
            return true;
        });

        static::updating(function($model) {
            $timestamp = time();
            $model->updated_at = $timestamp;
            return true;
        });
    }
}
