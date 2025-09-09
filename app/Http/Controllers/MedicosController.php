<?php

namespace App\Http\Controllers;


use App\Models\medicos;

use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MedicosController extends Controller
{
    public function index()
    {
        $medicos = medicos::all();
        return response()->json($medicos);
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

        $medico = Medicos::where("correo", $request->correo)->first();

        if (!$medico || !Hash::check($request->clave, $medico->clave)) {
            return response()->json([
                "success" => false,
                "message" => "Credenciales incorrectas"
            ], 401);
        }

        // Generar token con ability de Médico
        $token = $medico->createToken("auth_token", ["Medico"])->plainTextToken;

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
            "correo" => "required|string",
            "clave" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $medicos = medicos::create([
            "nombre" => $request->nombre,
            "apellido" => $request->apellido,
            "documento" => $request->documento,
            "telefono" => $request->telefono,
            "correo" => $request->correo,
            "clave" => Hash::make($request->clave)
        ]);

        $token = $medicos->createToken("auth_token", ["Medico"])->plainTextToken;
        return response()->json([
            "seccess" => true,
            "message" => "El Medico $request->nombre $request->apellido sea registrado exitosamente",
            "user" => $medicos,
            "token_access" => $token,
            "token_type" => "Bearer"
        ], 201);
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
            "correo" => "string"
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

    public function medicoPorDucumento(string $documneto)
    {
        $medico = medicos::where("documento", $documneto)->get();
        return response()->json($medico);
    }

    public function cambiarClave(Request $request, string $id)
    {
        $medico = medicos::find($id);
        if (!$medico) {
            return response()->json(["menssge" => "Especialidad no encontrado"]);
        }

        $validator = Validator::make($request->all(), [
            "clave" => "string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 400);
        }

        $medico->update([
            "clave" => Hash::make($request->clave)
        ]);
        return response()->json([
            "success" => true,
            "message" => "Cambio de la clave exitosamente"

        ], 200);
    }
}
