<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'title',
        'description'
    ];

    public function passwords(): HasMany
    {
        return $this->hasMany(Password::class);
    }
}
