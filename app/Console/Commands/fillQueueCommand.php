<?php

namespace App\Console\Commands;

use App\Jobs\AlterTableJob;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class fillQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uuid:table-add {getAllTablesName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update tables in chunks add uuid and populate it';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tables = DB::connection()->getSchemaBuilder()->getAllTables();
        $table_names = array_map(fn($table) => $table->{$this->argument('getAllTablesName')}, $tables);

        $record_count = 0;

        foreach($table_names as $table_name) {
            $table_record_count = DB::table($table_name)->count();

            $record_count += $table_record_count;

            ////////////////////////////////////////////////////////////////////

            $chunk_count = round($table_record_count / 1_000_000, 0, PHP_ROUND_HALF_UP);

            if(!$chunk_count){
                $chunk_count = 1;
            }

            $this->info("There are $table_record_count records in the {$table_name} table. Chunk Count $chunk_count");

            if (!Schema::hasColumn($table_name, 'id')) {
                AlterTableJob::dispatch(
                    table_name: $table_name,
                );
            }else {
                // call alter table job for chunks of a million records for 10 million records
                for ($i = 1; $i <= $chunk_count; $i++) {
                    $start_range = $i * 1000000;
                    $end_range   = $start_range + 1000000;

                    AlterTableJob::dispatch(
                        table_name: $table_name,
                        start_range: $start_range,
                        end_range: $end_range
                    );
                }
            }

            ////////////////////////////////////////////////////////////////////
        }

        $this->info('You have a total of ' . $record_count . ' records in your database.');

        return self::SUCCESS;
    }
}
