<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CodigoVerificacion;
use App\Mail\NotificacionUsuario;
use App\Models\administradores;
use App\Models\medicos;
use App\Models\pacientes;
use App\Models\recepcionistas;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CodigoVerificacionController extends Controller
{
    public function enviarCodigo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->errors()], 400);
        }


        $correoExistenteMedicos = medicos::where("correo", $request->email)->exists();
        $correoExistenteAdmin = administradores::where("correo", $request->email)->exists();
        $correoExistenteRecepcionista = recepcionistas::where("correo", $request->email)->exists();
        $correoExistentePaciente = pacientes::where("correo", $request->email)->exists();
        if (!$correoExistenteAdmin && !$correoExistenteMedicos && !$correoExistenteRecepcionista && !$correoExistentePaciente) {
            return response()->json([
                "success" => false,
                "message" => "El correo $request->correo no se encuentra registrado"
            ]);
        }
        // Generar código de 6 dígitos aleatorio
        $codigo = random_int(100000, 999999);

        // Borrar códigos previos del mismo correo
        CodigoVerificacion::where('email', $request->email)->delete();

        // Guardar el nuevo código con expiración 15 minutos
        CodigoVerificacion::create([
            'email' => $request->email,
            'codigo' => $codigo,
            'expira_en' => Carbon::now()->addMinutes(15),
        ]);

        // Enviar correo
        $datos = [
            'nombre' => 'Usuario',
            'url' => null,
            'codigo' => $codigo,
        ];

        Mail::to($request->email)->send(new NotificacionUsuario($datos));

        return response()->json([
            "success" => true,
            'message' => "Código de verificación enviado al correo: $request->email"            
        ]);
    }

    public function verificarCodigo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "correo" => "required|email",
            "codigo" => "required|integer|digits:6"
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->errors()], 400);
        }

        $verificacion = CodigoVerificacion::where("email", $request->correo)->where("codigo", $request->codigo)->first();
        if (!$verificacion) {
            CodigoVerificacion::where("email", $request->correo)->delete();
            return response()->json([
                "success" => false,
                "message" => "Codigo incorrecto"
            ], 400);
        }

        if (Carbon::parse($verificacion->expira_en)->isPast()) {
            CodigoVerificacion::where("email", $request->correo)->delete();
            return response()->json([
                "success" => false,
                "message" => "El codigo ha expirado, genera uno nuevo"
            ], 400);
        }
        CodigoVerificacion::where("email", $request->correo)->delete();
        return response()->json([
            "success" => true,
            "message" => "Codigo verficado exitosamente"
        ], 200);
    }

   
}
