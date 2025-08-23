<?php

namespace App\Http\Controllers;

use App\Models\pacientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PacientesController extends Controller
{
    public function index()
    {
        $pacientes = pacientes::all();
        return response()->json($pacientes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nombre" => "required|string",
            "apellido" => "required|string",
            "documento" => "required|integer",
            "telefono" => "required|integer|min:10",
            "fecha_nacimiento" => "required|date",
            'rh' => 'required|string',
            "sexo" => "required|string",
            "nacionalidad" => "required|string",
            "correo" => "required|email",
            "clave" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $medicos = pacientes::create($validator->validated());
        return response()->json($medicos, 201);
    }

    public function show(string $id)
    {
        $medicos = pacientes::find($id);
        if (!$medicos) {
            return response()->json(["message" => "Paciente no encontrado"], 400);
        }

        return response()->json($medicos);
    }

    public function update(Request $request, string $id)
    {
        $pacientes = pacientes::find($id);
        if (!$pacientes) {
            return response()->json(["message" => "Paciente no encontrado"], 404);
        }

        $validator = Validator::make($request->all(), [
            "nombre" => "string",
            "apellido" => "string",
            "documento" => "integer",
            "telefono" => "integer|min:10",
            "fecha_nacimiento" => "date",
            "rh" => "string",
            "sexo" => "string",
            "nacionalidad" => "string",
            "correo" => "email",
            "clave" => "string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $pacientes->update($validator->validated());
        return response()->json($pacientes);
    }

    public function destroy(string $id)
    {
        $pacientes = pacientes::find($id);
        if (!$pacientes) {
            return response()->json(["message" => "Paciente no encontrado"], 404);
        }

        $pacientes->delete();
        return response()->json(["message" => "Paciente eliminado correctamente"]);
    }

    public function filtrarPacientesPorSexo(string $sexo)
    {
        $pacientes = pacientes::where("sexo", $sexo)->get();
        return response()->json($pacientes);
    }

    public function pacientePorNacionalidad(string $nacionalidad)
    {
        $pacientes = pacientes::where("nacionalidad", $nacionalidad)->get();
        return response()->json($pacientes);
    }

      public function pacientePorRh(string $rh)
    {
        $pacientes = pacientes::where("rh", $rh)->get();
        return response()->json($pacientes);
    }
}
