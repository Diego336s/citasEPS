<?php

namespace App\Http\Controllers;

use App\Models\pacientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PacientesController extends Controller
{
    public function index()
    {
        $pacientes = pacientes::all();
        return response()->json($pacientes);
    }

    public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        "correo" => "required|email",
        "clave" => "required"
    ]);

    if ($validator->fails()) {
        return response()->json([
            "success" => false,
            "error" => $validator->errors()
        ], 422);
    }

    $paciente = pacientes::where("correo", $request->correo)->first();

    if (!$paciente || !Hash::check($request->clave, $paciente->clave)) {
        return response()->json([
            "success" => false,
            "message" => "Credenciales incorrectas"
        ], 401);
    }

    // Generar token con ability de Paciente
    $token = $paciente->createToken("auth_token", ["Paciente"])->plainTextToken;

    return response()->json([
        "success" => true,
        "token" => $token,
        "token_type" => "Bearer"
    ]);
}


     public function logout(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No hay usuario autenticado o token inválido'
        ], 401);
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

        $pacientes = pacientes::create([ 
            "nombre" =>  $request->nombre,
            "apellido" => $request->apellido,
            "documento" => $request->documento,
            "telefono" => $request->telefono,
            "fecha_nacimiento" => $request->fecha_nacimiento,
            'rh' => $request->rh,
            "sexo" => $request->sexo,
            "nacionalidad" => $request->nacionalidad,
            "correo" => $request->correo,
            "clave" => Hash::make($request->clave)
        ]);
        $token = $pacientes->createToken("auth_token", ["Paciente"])->plainTextToken;
        return response()->json([
            "seccess" => true,
            "message" => "El paciente $request->nombre $request->apellido sea registrado exitosamente",
            "user" => $pacientes,
            "token_access" => $token,
            "token_type" => "Bearer"
        ], 201);
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
            "correo" => "email"
          
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
