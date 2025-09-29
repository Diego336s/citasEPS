<?php

namespace App\Http\Controllers;

use App\Models\especialidades_medicos;
use App\Models\medicos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EspecialidadesMedicosController extends Controller
{

    public function index()
    {
        $especialidadesMedicos = especialidades_medicos::all();
        return response()->json($especialidadesMedicos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id_especialidad" => "required|integer",
            "id_medico" => "required|integer"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $especialidadesMedicos = especialidades_medicos::create($validator->validated());
        return response()->json($especialidadesMedicos, 201);
    }

    public function filtrar_medicos_por_especialidad(string $id_Especialidad)
    {
        $especialidadesMedicos = especialidades_medicos::where("id_especialidad", $id_Especialidad)->get();

        if ($especialidadesMedicos->isEmpty()) {
            return response()->json([
                "success" => false,
                "message" => "No hay médicos disponibles con esa especialidad"
            ], 404);
        }

        $informacionMedicos = $especialidadesMedicos->map(function ($especialidadMedico) {
            return medicos::find($especialidadMedico->id_medico);
        });

        return response()->json([
            "success" => true,
            "medicos" => $informacionMedicos
        ]);
    }


    public function update(Request $request, string $id)
    {

        $especialidadesMedicos = especialidades_medicos::find($id);
        if (!$especialidadesMedicos) {
            return response()->json(["message" => "Especialidad del medico no encontrada"], 400);
        }

        $validator = Validator::make($request->all(), [
            "id_especialidad" => "integer",
            "id_medico" => "integer"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $especialidadesMedicos->update($validator->validated());
        return response()->json($especialidadesMedicos, 200);
    }

    public function destroy(string $id)
    {

        $especialidadesMedicos = especialidades_medicos::find($id);
        if (!$especialidadesMedicos) {
            return response()->json(["message" => "Especialidad del medico no encontrada"], 400);
        }

        $especialidadesMedicos->delete();
        return response()->json(["message" => "Especialidad del medico eliminada correctamente"]);
    }
}
