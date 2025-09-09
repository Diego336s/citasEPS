<?php

namespace App\Http\Controllers;


use App\Models\recepcionistas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class RecepcionistasController extends Controller
{

    public function index()
    {
        $recepcionistas = recepcionistas::all();
        return response()->json($recepcionistas);
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

        $recepcionista = recepcionistas::where("correo", $request->correo)->first();

        if (!$recepcionista || !Hash::check($request->clave, $recepcionista->clave)) {
            return response()->json([
                "success" => false,
                "message" => "Credenciales incorrectas"
            ], 401);
        }

        // Generar token con ability específica
        $token = $recepcionista->createToken("auth_token", ["Recepcionista"])->plainTextToken;

        return response()->json([
            "success" => true,
            "token" => $token,
            "token_type" => "Bearer"
        ]);
    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "nombre" => "required|string",
            "apellido" => "required|string",
            "documento" => "required|integer",
            "telefono" => "required|integer|min:10",
            "correo" => "required|email",
            "clave" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $recepcionistas = recepcionistas::create([
            "nombre" => $request->nombre,
            "apellido" =>  $request->apellido,
            "documento" => $request->documento,
            "telefono" =>  $request->telefono,
            "correo" =>  $request->correo,
            "clave" => Hash::make($request->clave)
        ]);
        $token = $recepcionistas->createToken("auth_token", ["Recepcionista"])->plainTextToken;
        return response()->json([
            "seccess" => true,
            "message" => "El recepcionista $request->nombre $request->apellido sea registrado exitosamente",
            "user" => $recepcionistas,
            "token_access" => $token,
            "token_type" => "Bearer"
        ], 201);
    }

    public function show(string $id)
    {

        $recepcionistas = recepcionistas::find($id);
        if (!$recepcionistas) {
            return response()->json(["messsage" => "Recepcionista no encontrado"], 404);
        }

        return response()->json($recepcionistas);
    }

    public function update(Request $request, string $id)
    {
        $recepcionistas = recepcionistas::find($id);
        if (!$recepcionistas) {
            return response()->json(["message" => "Recepcionista no encontrado"], 404);
        }

        $validator = Validator::make($request->all(), [
            "nombre" => "string",
            "apellido" => "string",
            "documento" => "integer",
            "telefono" => "integer|min:10",
            "correo" => "email",
            "clave" => "string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $recepcionistas->update($validator->validated());
        return response()->json($recepcionistas);
    }

    public function destroy(string $id)
    {
        $recepcionistas = recepcionistas::find($id);
        if (!$recepcionistas) {
            return response()->json(["message" => "Recepcionista no encontrado"], 404);
        }

        $recepcionistas->delete();
        return response()->json(["message" => "Recepcionista eliminado correctamente"], 200);
    }

    public function cambiarClave(Request $request, string $id)
    {
        $recepcionistas = recepcionistas::find($id);
        if (!$recepcionistas) {
            return response()->json(["menssge" => "Recepcionista no encontrado"]);
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

        $recepcionistas->update([
            "clave" => Hash::make($request->clave)
        ]);
        return response()->json([
            "success" => true,
            "message" => "Cambio de la clave exitosamente"

        ], 200);
    }
}
