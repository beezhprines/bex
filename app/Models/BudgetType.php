<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetType extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'title', 'code', 'income'
    ];

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function sign()
    {
        return $this->income ? 1 : -1;
    }
}
