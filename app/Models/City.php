<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'state_id',
        'codigo_ibge',
        'latitude',
        'longitude',
        'capital',
        'codigo_uf',
        'siafi_id',
        'ddd',
        'fuso_horario'
    ];

    /**
     * Relacionamento de cidade com estado.
     * Cada cidade pertence a um estado.
     */
    public function state(): BelongsTo
    {
        // Usando cache para melhorar a performance
        return Cache::remember("state_{$this->state_id}", 60*60, function() {
            return $this->belongsTo(State::class);
        });
    }

    /**
     * Relacionamento de cidade com funcionários.
     * Uma cidade pode ter muitos funcionários.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Busca cidades por estado, com possibilidade de cache.
     */
    public static function getCitiesByState($stateId)
    {
        // Retornar todas as cidades de um estado usando cache para otimizar a performance
        return Cache::remember("cities_by_state_{$stateId}", 60*60, function() use ($stateId) {
            return self::where('state_id', $stateId)->get();
        });
    }
}
