<?php

namespace App\Http\Controllers;

use App\Models\especialidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EspecialidadesController extends Controller
{

    public function index()
    {
        $especialidades = especialidades::all();
        return response()->json($especialidades);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nombre" => "required|string"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $especialidades = especialidades::create($validator->validated());
        return response()->json($especialidades, 201);
    }

    public function show(string $id)
    {
        $especialidades = especialidades::find($id);
        if (!$especialidades) {
            return response()->json(["message" => "Especialidad no encontrada"], 400);
        }
        return response()->json($especialidades);
    }

    public function update(Request $request, string $id)
    {
        $especialidades = especialidades::find($id);
        if (!$especialidades) {
            return response()->json(["menssge" => "Especialidad no encontrada"]);
        }

        $validator = Validator::make($request->all(), [
            "nombre" => "string"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $especialidades->update($validator->validated());
        return response()->json($especialidades);
    }

    public function destroy(string $id)
    {
        $especialidades = especialidades::find($id);
        if (!$especialidades) {
            return response()->json(["message" => "Especialidad no encontrada"], 400);
        }
        $especialidades->delete();
        return response()->json(["message" => "Especialidad eliminada correctamente"], 200);
    }

      public function contarEspecialidades()
    {
        $totalEspecialidades = especialidades::count();
        return response()->json(["message" => "Total de especialidades: $totalEspecialidades"], 200);
        
    }
}
