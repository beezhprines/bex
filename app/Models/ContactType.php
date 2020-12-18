<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactType extends Model
{
    use HasFactory, SoftDeletes, ModelBase, ClearsResponseCache;

    protected $fillable = [
        'title', 'code'
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
