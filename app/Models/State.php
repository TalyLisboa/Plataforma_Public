<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'uf', // Adicionando campo 'uf' (sigla do estado)
    ];

    /**
     * Relacionamento de estado com funcionários.
     * Um estado pode ter muitos funcionários.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Relacionamento de estado com cidades.
     * Um estado pode ter muitas cidades.
     */
    public function cities(): HasMany
    {
        // Usando cache para melhorar a performance
        return Cache::remember("state_{$this->id}_cities", 60 * 60, function () {
            return $this->hasMany(City::class);
        });
    }

    /**
     * Obter todos os estados do Brasil com cache.
     */
    public static function getAllStates()
    {
        return Cache::remember('all_states', 60 * 60, function () {
            return self::select('id', 'name', 'uf')->get();
        });
    }
}
