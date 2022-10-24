<?php

namespace App\Imports;

use App\Http\Controllers\MilitanteController;
use App\Models\Boleta;
use App\Models\Militante;
use App\Models\NumeroReservado;
use App\Models\Rol;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use function PHPUnit\Framework\isNull;

class MilitantesImport implements ToModel, SkipsEmptyRows, SkipsOnError, SkipsOnFailure, WithHeadingRow, WithValidation, WithChunkReading
{
    public function __construct(Request $request)
    {
        ini_set('max_execution_time', 1200);
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $militante = null;
        if ($row['cedula'] != '' && $row['cedula'] != null) {
            $militante = Militante::where('documento', $row['cedula'])
                                   ->first();
        } elseif ($row['correo_electronico'] != '' && $row['correo_electronico'] != null) {
            $militante = Militante::where('email', $row['correo_electronico'])
                ->first();
        } elseif ($row['celular'] != '' && $row['celular'] != null) {
            $militante = Militante::where('movil', $row['celular'])
                ->first();
        }

        if (is_null($militante)) {
            try{
                DB::beginTransaction();
                $militante = new Militante();
                $militante->fechaingreso = $row['fecha_de_ingreso']?Carbon::createFromFormat('m/d/Y', $row['fecha_de_ingreso'])->format('Y-m-d'):null;
                $militante->idinscripcion = $row['id_inscripcion'];
                $militante->iddepartamento = $row['id_departamento'];
                $militante->idciudad = $row['id_ciudad'];
                $militante->documento = $row['cedula'];
                $militante->fechanacimiento = $row['fecha_de_nacimiento']?Carbon::createFromFormat('m/d/Y', $row['fecha_de_nacimiento'])->format('Y-m-d'):null;
                $militante->nombre = $row['nombre_completo'];
                $militante->movil = $row['celular'];
                $militante->email = $row['correo_electronico'];
                $militante->direccion = $row['direccion'];

                $militante->username = $row['cedula'];
                $militante->idtipos_documento = 1;
                $militante->idpais = 1;
                $militante->observaciones = 'Importado';

                $militante->password = Hash::make($militante->documento);
                $militante->estado = 3;
                $militante->changedpassword = null;
                $militante->save();
                $rol = Rol::where('id', 3)->first();
                $militante->assignRole($rol->nombre);
                MilitanteController::setHistorial($militante->id, 1, $militante->observaciones);
                DB::commit();

            } catch (Throwable $e){
                DB::rollBack();

            }
        }

        return $militante;
    }

    public function rules() : array
    {
        return [
            'fecha_de_ingreso' => 'nullable|date_format:m/d/Y', //'nullable|regex:/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/',
            'id_inscripcion' => 'nullable|numeric',
            'id_departamento' => 'nullable|numeric',
            'id_ciudad' => 'nullable|numeric',
            //'cedula' => 'nullable|string|numeric',
            'fecha_de_nacimiento' => 'nullable|date_format:m/d/Y', //'nullable|regex:/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/',
            'nombre_completo' => 'nullable|string',
            //'celular' => 'nullable|numeric',
            //'correo_electronico' => 'nullable|string',
            'direccion' => 'nullable|string'
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        dd($failures);
        return $failures;
    }

    public function onError(\Throwable $e)
    {
        return $e;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

}
