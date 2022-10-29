<?php

namespace App\Console\Commands;

use App\Imports\MilitantesImport;
use Illuminate\Console\Command;

class ImportExcel extends Command
{
    protected $signature = 'import:excel';

    protected $description = 'Importar Militantes';

    public function handle()
    {
        $this->output->title('Iniciar');
        (new MilitantesImport(1))->withOutput($this->output)->import('users.xlsx');
        $this->output->success('Import successful');
    }
}
