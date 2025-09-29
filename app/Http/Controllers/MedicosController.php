<?php

namespace App\Http\Controllers;

use App\Models\administradores;
use App\Models\citas;
use App\Models\especialidades;
use App\Models\especialidades_medicos;
use App\Models\medicos;
use App\Models\pacientes;
use App\Models\recepcionistas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Helper\TreeNode;

class MedicosController extends Controller
{
    public function me(Request $request)
    {
        return response()->json([
            "success" => true,
            "user" => $request->user()
        ]);
    }

    public function index()
    {
        $medicos = medicos::all();
        return response()->json($medicos);
    }

    //listar medicos con especialidades 
    public function listarMedicosConEspecialidades()
    {
        $medicos = medicos::join("especialidades_medicos", "medicos.id", "=", "especialidades_medicos.id_medico")
            ->join("especialidades", "especialidades_medicos.id_especialidad", "=", "especialidades.id")
            ->select(
                "medicos.*",
                "especialidades.nombre as especialidad"
            )->get();
        if ($medicos->isEmpty()) {
            return response()->json(["success" => false, "message" => "No hay medicos registrados"]);
        }
        return response()->json([
            "success" => true,
            "medicos" => $medicos
        ]);
    }

    public function filtrarMedicoConEspecialidades($id)
    {
        $medicos = medicos::join("especialidades_medicos", "medicos.id", "=", "especialidades_medicos.id_medico")
            ->join("especialidades", "especialidades_medicos.id_especialidad", "=", "especialidades.id")
            ->select(
                "medicos.*",
                "especialidades.nombre as especialidad",
                "especialidades.id as idEspecialidad"
            )->where("medicos.id", $id)->get();
        if ($medicos->isEmpty()) {
            return response()->json(["success" => false, "message" => "No hay medicos registrados"]);
        }
        return response()->json([
            "success" => true,
            "user" => $medicos
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




    public function store(Request $request, $idEspecialidad)
    {
        $validator = Validator::make($request->all(), [
            "nombre" => "required|string",
            "apellido" => "required|string",
            "documento" => "required|integer",
            "telefono" => "required|integer|min:10",
            "correo" => "required|string|email",
            "clave" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 400);
        }

        // Validar que la especialidad exista
        $existeEspecialidad = especialidades::where("id", $idEspecialidad)->exists();
        if (!$existeEspecialidad) {
            return response()->json([
                "success" => false,
                "message" => "La especialidad con id $idEspecialidad no existe"
            ], 404);
        }

        // Validar correos únicos en todas las tablas
        $correoExistente = medicos::where("correo", $request->correo)->exists()
            || administradores::where("correo", $request->correo)->exists()
            || recepcionistas::where("correo", $request->correo)->exists()
            || pacientes::where("correo", $request->correo)->exists();

        if ($correoExistente) {
            return response()->json([
                "success" => false,
                "message" => "El correo $request->correo ya se encuentra registrado"
            ], 409);
        }

        try {
            DB::beginTransaction();

            // Crear médico
            $medico = medicos::create([
                "nombre" => $request->nombre,
                "apellido" => $request->apellido,
                "documento" => $request->documento,
                "telefono" => $request->telefono,
                "correo" => $request->correo,
                "clave" => Hash::make($request->clave)
            ]);

            // Relacionar con especialidad
            especialidades_medicos::create([
                "id_especialidad" => $idEspecialidad,
                "id_medico" => $medico->id
            ]);

            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "El médico $request->nombre $request->apellido fue registrado exitosamente con su especialidad",
                "user" => $medico,
                "id_especialidad" => $idEspecialidad
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error al registrar el médico: " . $e->getMessage()
            ], 500);
        }
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

        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, $validator->errors()], 400);
        }

        $medicos->update($validator->validated());
        return response()->json([
            "success" => true,
            "message" => "Medico $request->nombre  $request->apellido actualizado correctamente",
            $medicos
        ]);
    }

    public function actualizarMedicoConEspecialida(Request $request, $id, $idEspecialidad)
    {
        $validator = Validator::make($request->all(), [
            "nombre" => "required|string",
            "apellido" => "required|string",
            "documento" => "required|integer",
            "telefono" => "required|integer|min:10"

        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 400);
        }

        // Verificar que el médico exista
        $medico = medicos::find($id);
        if (!$medico) {
            return response()->json([
                "success" => false,
                "message" => "El médico con id $id no existe"
            ], 404);
        }

        // Validar que la especialidad exista
        $existeEspecialidad = especialidades::where("id", $idEspecialidad)->exists();
        if (!$existeEspecialidad) {
            return response()->json([
                "success" => false,
                "message" => "La especialidad con id $idEspecialidad no existe"
            ], 404);
        }




        try {
            DB::beginTransaction();

            // Actualizar médico
            $medico->update([
                "nombre" => $request->nombre,
                "apellido" => $request->apellido,
                "documento" => $request->documento,
                "telefono" => $request->telefono,
            ]);

            // Actualizar relación con especialidad (si ya existe, la actualiza; si no, la crea)
            especialidades_medicos::updateOrCreate(
                ["id_medico" => $medico->id],
                ["id_especialidad" => $idEspecialidad]
            );

            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "El médico {$medico->nombre} {$medico->apellido} fue actualizado exitosamente",
                "user" => $medico,
                "id_especialidad" => $idEspecialidad
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error al actualizar el médico: " . $e->getMessage()
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        $medicos = medicos::find($id);
        if (!$medicos) {
            return response()->json([
                "success" => false,
                "message" => "Medico no encontrado"
            ], 404);
        }
        $medicos->delete();
        return response()->json([
            "success" => true,
            "message" => "Medico eliminado correctamente"
        ], 200);
    }


    public function contarMedicos()
    {
        $totalMedicos = medicos::count();
        return response()->json([
            "success" => true,
            "message" => "Total de doctores: $totalMedicos",
            "total" => $totalMedicos
        ], 200);
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

  public function olvideMiClave(Request $request)
{
    $validator = Validator::make($request->all(), [
        "correo" => "required|string|email",
        "clave"  => "required|string|min:6"
    ]);

    if ($validator->fails()) {
        return response()->json([
            "success" => false,
            "message" => $validator->errors()
        ], 400);
    }

    // Buscar paciente por correo
    $medico = medicos::where("correo", $request->correo)->first();

    if (!$medico) {
        return response()->json([
            "success" => false,
            "message" => "No se encontró un medico con ese correo"
        ], 404);
    }

    // Actualizar clave
    $medico->update([
        "clave" => Hash::make($request->clave)
    ]);

    return response()->json([
        "success" => true,
        "message" => "Cambio de clave exitoso"
    ], 200);
}


    public function cambiarCorreo(Request $request, string $id)
    {
        $medico = medicos::find($id);
        if (!$medico) {
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

        $medico->update($validator->validated());
        return response()->json([
            "success" => true,
            "message" => "Cambio del correo exitoso"

        ], 200);
    }

    public function pacientesAtendidosPorDoctor($doctorId)
    {
        $pacientes = citas::join("pacientes", "citas.id_paciente", "=", "pacientes.id")
            ->where("citas.id_medico", $doctorId)
            ->where("citas.estado", "Finalizada")
            ->select(
                "pacientes.id",
                "pacientes.nombre",
                "pacientes.apellido",
                "pacientes.documento",
                "pacientes.telefono",
                "pacientes.correo"
            )
            ->groupBy(
                "pacientes.id",
                "pacientes.nombre",
                "pacientes.apellido",
                "pacientes.documento",
                "pacientes.telefono",
                "pacientes.correo"
            )
            ->get();

        if ($pacientes->isEmpty()) {
            return response()->json([
                "success" => false,
                "message" => "Este doctor aún no ha atendido pacientes"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "pacientes" => $pacientes
        ]);
    }
}
