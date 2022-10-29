<?php

namespace App\Jobs;

use App\Imports\MilitantesImport;
use App\Mail\Notificaciones;
use App\Http\Controllers\RifaController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file, $id, $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file, $id, $user)
    {
        $this->file = $file;
        $this->id = $id;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Excel::import(new MilitantesImport($this->id, Auth::user()), $this->file);
    }
}
