<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes, HasTimestamps;

    protected $connection = 'mongodb';

    protected $primaryKey = 'id';

    protected $fillable = [
        "content",
        "user_id"
    ];

    protected function casts(): array
    {
        return [
            "content" => "string",
        ];
    }
}
