<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'date', 'amount', 'json', 'budget_type_id'
    ];

    public function budgetType()
    {
        return $this->belongsTo(BudgetType::class);
    }

    public function managers()
    {
        return $this->belongsToMany(Manager::class, 'budget_manager');
    }

    public function masters()
    {
        return $this->belongsToMany(Master::class, 'budget_master');
    }

    public function operators()
    {
        return $this->belongsToMany(Operator::class, 'budget_operator');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
