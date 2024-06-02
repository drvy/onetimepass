<?php

namespace App\Models;

use DateTime;
use App\Abstracts\Models\Model;

class Message extends Model
{
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table   = 'messages';
    protected $keyType = 'string';

    protected $fillable = [];


    public function getCreatedAtAttribute($date)
    {
        return (new DateTime($date))->format('Y-m-d H:i:s');
    }


    public function getUpdatedAtAttribute($date)
    {
        return (new DateTime($date))->format('Y-m-d H:i:s');
    }
}
