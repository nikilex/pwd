<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Password extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'title',
        'username',
        'password',
        'description'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
