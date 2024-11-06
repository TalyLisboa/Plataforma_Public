<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Team;

class PaymentReport extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'amount',
        'status',
        'created_at',
        'updated_at',
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

    // Outros atributos e métodos do modelo

    /**
     * Relação com o Team (Tenant).
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
