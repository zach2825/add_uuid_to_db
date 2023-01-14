<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FillUuidJob implements ShouldQueue
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
//        $uuid = Str::uuid4();


        $hasIdColumn = Schema::hasColumn($this->table_name, 'id');

        if ($this->start_range && $hasIdColumn) {
            $response = DB::table($this->table_name)
                ->whereNull('uuid')
                ->whereBetween('id', [$this->start_range, $this->end_range])
                ->update(['uuid' => DB::raw('BIN_UUID_V4()')]);
        } else {
            $response = DB::table($this->table_name)
                ->whereNull('uuid')
                ->update(['uuid' => DB::raw('BIN_UUID_V4()')]);
        }
        $stop = 'here';
    }
}
