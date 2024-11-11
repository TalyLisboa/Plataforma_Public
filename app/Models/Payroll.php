<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;

class Payroll extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'team_id',
        'salary_amount',
        'inss',
        'irrf',
        'fgts',
        'deductions',
        'other_deductions',
        'bonuses',
        'net_pay',
        'payment_date',
        'payment_method',
        'comments',
    ];

    /**
     * Define os casts para os atributos monetários.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'salary_amount'      => 'decimal:2',
        'inss'               => 'decimal:2',
        'irrf'               => 'decimal:2',
        'fgts'               => 'decimal:2',
        'deductions'         => 'decimal:2',
        'other_deductions'   => 'decimal:2',
        'bonuses'            => 'decimal:2',
        'net_pay'            => 'decimal:2',
        'payment_date'       => 'date:d/m/Y',
    ];

    /**
     * Relação com o modelo Employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relação com o Team (Tenant).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
