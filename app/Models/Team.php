<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany};

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    /**
     * Relação com Employees
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Relação com Departments (Ajuste Necessário)
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Relação com Members (usuários)
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Relação com Payrolls (Folhas de Pagamento)
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}
