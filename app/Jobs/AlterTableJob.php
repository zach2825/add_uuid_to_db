<?php

namespace App\Jobs;

use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;

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
        if ($this->table_name == 'jobs' || $this->table_name == 'migrations') {
            return;
        }

        if (!Schema::hasColumn($this->table_name, 'uuid')) {
            Schema::table($this->table_name, function (Blueprint $table) {
                if (Schema::hasColumn($this->table_name, 'id')) {
                    $table->uuid('uuid')->nullable()->after('id');
                } else {
                    $table->uuid('uuid')->nullable();
                }
            });
        }
//        print "Doing {$this->table_name} from {$this->start_range} to {$this->end_range}";

        dispatch(new FillUuidJob($this->table_name, $this->start_range, $this->end_range));
    }
}
