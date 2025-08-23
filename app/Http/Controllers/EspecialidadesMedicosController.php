<?php

namespace App\Http\Controllers;

use App\Models\especialidades_medicos;
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

    public function show(string $id)
    {
        $especialidadesMedicos = especialidades_medicos::find($id);
        if (!$especialidadesMedicos) {
            return response()->json(["message" => "Especialidad del medico no encontrada"], 400);
        }
        return response()->json($especialidadesMedicos);
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
