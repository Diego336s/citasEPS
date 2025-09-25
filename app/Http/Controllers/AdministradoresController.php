<?php

namespace App\Http\Controllers;

use App\Models\administradores;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdministradoresController extends Controller
{

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

        $admin = administradores::where("correo", $request->correo)->first();

        if ($request->clave === $admin->clave) {
            // Generar token con ability de Admin
            $token = $admin->createToken("auth_token", ["Admin"])->plainTextToken;
            return response()->json([
                "success" => true,
                "message" => "Hola $admin->nombre, por seguridad cambia la clave de cuenta.",
                "token" => $token,
                "token_type" => "Bearer"
            ]);

        }

        if (!$admin || !Hash::check($request->clave, $admin->clave)) {

            return response()->json([
                "success" => false,
                "message" => "Credenciales incorrectas"
            ], 401);
        }

        // Generar token con ability de Admin
        $token = $admin->createToken("auth_token", ["Admin"])->plainTextToken;

        return response()->json([
            "success" => true,
            "token" => $token,
            "token_type" => "Bearer"
        ]);
    }

    public function update(Request $request, string $id)
    {
        $administrador = administradores::find($id);
        if (!$administrador) {
            return response()->json(["menssge" => "Especialidad no encontrada"]);
        }

        $validator = Validator::make($request->all(), [
            "nombre" => "string",
            "apellido" => "string",
            "documento" => "integer",
            "telefono" => "integer|min:10",
           
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 400);
        }

        $administrador->update($validator->validated());
        return response()->json([
            "success" => true,
            "message" => "Administrador editado correctamente",
            "datos" => $administrador
        ], 200);
    }

    public function cambiarClave(Request $request, string $id)
    {
        $administrador = administradores::find($id);
        if (!$administrador) {
            return response()->json(["menssge" => "Especialidad no encontrada"]);
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

        $administrador->update([
            "clave" => Hash::make($request->clave)
        ]);
        return response()->json([
            "success" => true,
            "message" => "Cambio de la clave exitosamente"

        ], 200);
    }
}
