<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';


    public function getCreatedAtAttribute($date)
    {
        return (new DateTime($date))->format('Y-m-d H:i:s');
    }


    public function getUpdatedAtAttribute($date)
    {
        return (new DateTime($date))->format('Y-m-d H:i:s');
    }
}
