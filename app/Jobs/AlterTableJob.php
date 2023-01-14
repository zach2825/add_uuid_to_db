<?php

namespace App\Jobs;

use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AlterTableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public string $table_name, public $start_range = null, public $end_range = null)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!Schema::hasColumn($this->table_name, 'uuid')) {
            Schema::table($this->table_name, function (Blueprint $table) {
                $table->uuid('uuid');
            });
        }
//        print "Doing {$this->table_name} from {$this->start_range} to {$this->end_range}";

        $uuid = Str::uuid();
        if($this->start_range) {
            DB::statement("UPDATE {$this->table_name} SET uuid = '{$uuid}' WHERE id BETWEEN {$this->start_range} AND {$this->end_range} AND uuid IS NULL");
        }else {
            DB::statement("UPDATE {$this->table_name} SET uuid = '{$uuid}' WHERE uuid IS NULL");
        }
    }
}
