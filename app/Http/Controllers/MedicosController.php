<?php

namespace App\Http\Controllers;

use App\Models\especialidades_medicos;
use App\Models\medicos;
use Dotenv\Util\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicosController extends Controller
{
    public function index()
    {
        $medicos = medicos::all();
        return response()->json($medicos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nombre" => "required|string",
            "apellido" => "required|string",
            "documento" => "required|integer",
            "telefono" => "required|integer|min:10",
            "correo" => "required|string",
            "clave" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $medicos = medicos::create($validator->validated());
        return response()->json($medicos, 201);
    }

    public function show(string $id)
    {
        $medicos = medicos::find($id);
        if (!$medicos) {
            return response()->json(["message" => "Medico no encontrado"], 404);
        }

        return response()->json($medicos);
    }


    public function update(Request $request, string $id)
    {
        $medicos = medicos::find($id);
        if (!$medicos) {
            return response()->json(["message" => "Medico no encontrado"], 404);
        }

        $validator = Validator::make($request->all(), [
            "nombre" => "string",
            "apellido" => "string",
            "documento" => "integer",
            "telefono" => "integer|min:10",
            "correo" => "string",
            "clave" => "string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $medicos->update($validator->validated());
        return response()->json($medicos);
    }

    public function destroy(string $id)
    {
        $medicos = medicos::find($id);
        if (!$medicos) {
            return response()->json(["message" => "Medico no encontrado"], 404);
        }
        $medicos->delete();
        return response()->json(["message" => "Medico eliminado correctamente"], 200);
    }


    public function contarMedicos()
    {
        $totalMedicos = medicos::count();
        return response()->json(["message" => "Total de doctores: $totalMedicos"], 200);
        
    }

    public function medicoPorDucumento(string $documneto){
    $medico = medicos::where("documento", $documneto)->get();
    return response()->json($medico);
    }
}
