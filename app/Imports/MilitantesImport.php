<?php

namespace App\Imports;

use App\Models\Boleta;
use App\Models\Militante;
use App\Models\NumeroReservado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use function PHPUnit\Framework\isNull;

class MilitantesImport implements ToModel
{
    public function __construct(Request $request)
    {
        $this->idrifa = $request->rifa;
        $this->idvenddor = $request->vendedor;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $militante = Militante::where('documento', $row[3])->firstOrFail();
        /*
        if (!is_null($militante)) {
            if (($boleta->idvendedor === '' || $boleta->idvendedor === null) && $boleta->estado == 1) {
                $boleta->idvendedor = $this->idvenddor;
                $boleta->estado = 2;
                $boleta->save();
            }
        }
*/
        return $militante;
    }
}
