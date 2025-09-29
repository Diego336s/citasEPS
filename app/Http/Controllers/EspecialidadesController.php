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
        return response()->json([
            "success" => true,
            "especialidades" => $especialidades
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nombre" => "required|string"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->errors()], 400);
        }

        $especialidades = especialidades::create($validator->validated());
        return response()->json([
            "success" => true,
            "especialidad" => $especialidades,
            "message" => "Especialidad $request->nombre se registro correctamente"
        ], 201);
    }

    public function show(string $id)
    {
        $especialidades = especialidades::find($id);
        if (!$especialidades) {
            return response()->json(["success" => false, "message" => "Especialidad no encontrada"], 400);
        }
        return response()->json([
            "success" => true,
            "especialidades" => $especialidades
        ]);
    }

    public function update(Request $request, string $id)
    {
        $especialidades = especialidades::find($id);
        if (!$especialidades) {
            return response()->json(["success" => false, "menssge" => "Especialidad no encontrada"]);
        }

        $validator = Validator::make($request->all(), [
            "nombre" => "string"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, $validator->errors()], 400);
        }

        $especialidades->update($validator->validated());
        return response()->json([
            "success" => true,
            "message" => "Especialidad actualizada correctamente.",
            $especialidades
        ], 200);
    }

    public function destroy(string $id)
    {
        $especialidades = especialidades::find($id);
        if (!$especialidades) {
            return response()->json(["success" => false,"message" => "Especialidad no encontrada"], 400);
        }
        $especialidades->delete();
        return response()->json(["success" => true,"message" => "Especialidad eliminada correctamente"], 200);
    }

    public function contarEspecialidades()
    {
        $totalEspecialidades = especialidades::count();
        return response()->json([
            "success" => true,
            "message" => "Total de especialidades: $totalEspecialidades",
            "total" => $totalEspecialidades
        ], 200);
    }
}
