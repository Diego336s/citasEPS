<?php

namespace App\Http\Controllers;

use App\Models\administradores;
use App\Models\medicos;
use App\Models\pacientes;
use App\Models\recepcionistas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdministradoresController extends Controller
{

     public function me(Request $request)
    {
        return response()->json([
            "success" => true,
            "user" => $request->user()
        ]);
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

     public function cambiarCorreo(Request $request, string $id)
    {
        $paciente = administradores::find($id);
        if (!$paciente) {
            return response()->json(["menssge" => "Paciente no encontrado"]);
        }

        $validator = Validator::make($request->all(), [
            "correo" => "string|email"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 400);
        }

        $correoExistenteMedicos = medicos::where("correo", $request->correo)->exists();
        $correoExistenteAdmin = administradores::where("correo", $request->correo)->exists();
        $correoExistenteRecepcionista = recepcionistas::where("correo", $request->correo)->exists();
        $correoExistentePaciente = pacientes::where("correo", $request->correo)->exists();
        if ($correoExistenteAdmin || $correoExistenteMedicos || $correoExistenteRecepcionista || $correoExistentePaciente) {
            return response()->json([
                "success" => false,
                "message" => "El correo $request->correo ya se encuentra registrado"
            ]);
        }

        $paciente->update($validator->validated());
        return response()->json([
            "success" => true,
            "message" => "Cambio del correo exitoso"

        ], 200);
    }

    public function olvideMiClave(Request $request){       

        $validator = Validator::make($request->all(), [
            "clave" => "string|min:6",
            "correo" => "string|email"
        ]);

          if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 400);
        }
         $administrador = administradores::find($request->correo);
        if (!$administrador) {
            return response()->json(["success"=> false,"menssge" => "Especialidad no encontrado"]);
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
