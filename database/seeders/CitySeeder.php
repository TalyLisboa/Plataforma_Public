<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CitySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ajustar limites de memória e tempo de execução
        ini_set('memory_limit', '2048M'); // 2GB, ajuste conforme necessário
        ini_set('max_execution_time', '0'); // Sem limite de tempo

        // Carregar o arquivo CSV com as cidades
        $filePath = database_path('seeders/cities_brazil.csv'); // Caminho do arquivo CSV

        if (!file_exists($filePath)) {
            echo "Arquivo de cidades não encontrado em: {$filePath}\n";
            return;
        }

        // Carregar todos os estados em cache para otimizar a busca
        $states = State::all()->keyBy('codigo_uf'); // Verifica se 'codigo_uf' está correto

        if ($states->isEmpty()) {
            echo "Nenhum estado foi encontrado no banco de dados. Verifique a inserção de estados.\n";
            return;
        }

        // Abrir o arquivo CSV
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Ler o cabeçalho
            $header = fgetcsv($handle, 1000, ',');

            // Iniciar transação
            DB::beginTransaction();

            try {
                $batchSize = 1000; // Tamanho do lote
                $citiesBatch = [];

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $row = array_combine($header, $data);

                    // Buscar o estado pelo 'codigo_uf' do cache carregado
                    $state = $states->get($row['codigo_uf']);

                    if ($state) {
                        $citiesBatch[] = [
                            'name' => $row['nome'],
                            'state_id' => $state->id,
                            'codigo_ibge' => $row['codigo_ibge'],
                            'latitude' => $row['latitude'],
                            'longitude' => $row['longitude'],
                            'capital' => filter_var($row['capital'], FILTER_VALIDATE_BOOLEAN),
                            'codigo_uf' => $row['codigo_uf'],
                            'siafi_id' => $row['siafi_id'],
                            'ddd' => $row['ddd'],
                            'fuso_horario' => $row['fuso_horario'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } else {
                        echo "Estado com código '{$row['codigo_uf']}' não encontrado para a cidade '{$row['nome']}' na linha " . ftell($handle) . ".\n";
                    }

                    // Inserir em lotes
                    if (count($citiesBatch) === $batchSize) {
                        DB::table('cities')->insert($citiesBatch);
                        $citiesBatch = [];
                        echo "Inseridos mais {$batchSize} registros.\n";
                    }
                }

                // Inserir quaisquer registros restantes
                if (!empty($citiesBatch)) {
                    DB::table('cities')->insert($citiesBatch);
                    echo "Inseridos os últimos " . count($citiesBatch) . " registros.\n";
                }

                // Commit da transação
                DB::commit();
                echo "CitySeeder concluído com sucesso.\n";
            } catch (\Exception $e) {
                // Rollback em caso de erro
                DB::rollBack();
                echo "Erro ao inserir cidades: " . $e->getMessage() . "\n";
            }

            fclose($handle);
        } else {
            echo "Não foi possível abrir o arquivo de cidades.\n";
        }
    }
}
