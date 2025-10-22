<?php

namespace App\Console\Commands;

use App\Models\CodigoVerificacion;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BorrarCodigosExpirados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:borrar-codigos-expirados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina los codigos de verificacion expirados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        CodigoVerificacion::where("expira_en", "<", Carbon::now())->delete();
        $this->info("Codigos expirados eliminados correcatamente");
    }
}
