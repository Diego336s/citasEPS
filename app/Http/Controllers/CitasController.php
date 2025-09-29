<?php

namespace App\Http\Controllers;

use App\Models\citas;
use Dotenv\Util\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CitasController extends Controller
{

    public function index()
    {

        $citas = citas::all();
        return response()->json($citas);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "descripcion" => "required|string",

            "id_medico" => "required|integer",
            "id_paciente" => "required|integer",
            "fecha" => "required|date",
            "hora_inicio" => "required",
            "estado" => "required|in:Pendiente,Confirmada,Cancelada,Finalizada"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $validarFechaRepetida = citas::where("fecha", $request->fecha)->where("hora_inicio", $request->hora_inicio)->exists();
        if ($validarFechaRepetida) {
            return response()->json([
                "success" => false,
                "message" => "Ya existe un cita con esa fecha: $request->fecha y hora: $request->hora_inicio"
            ], 400);
        }


        $citas = citas::create($validator->validated());
        return response()->json(["success" => true, $citas], 201);
    }

    public function show(string $id)
    {
        $citas = citas::find($id);
        if (!$citas) {
            return response()->json(["success" => false, "message" => "Cita no encontrada"], 400);
        }

        return response()->json(["success" => true, $citas]);
    }

    public function update(Request $request, string $id)
    {
        $citas = citas::find($id);
        if (!$citas) {
            return response()->json([
                "success" => false,
                "message" => "Cita no encontrada"
            ], 400);
        }


        $validator = Validator::make($request->all(), [
            "descripcion" => "sometimes|string",
            "id_medico" => "sometimes|integer",
            "id_paciente" => "sometimes|integer",
            "fecha" => "sometimes|date",
            "hora_inicio" => "sometimes|string",
            "estado" => "sometimes|in:Pendiente,Confirmada,Cancelada,Finalizada"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 400);
        }

        $citas->update($validator->validated());
        return response()->json([
            "success" => true,
            $citas
        ], 200);
    }

    public function cambiarEstadoCita(Request $request, string $id)
    {
        $citas = citas::find($id);
        if (!$citas) {
            return response()->json([
                "success" => false,
                "message" => "Cita no encontrada",
            ], 400);
        }


        $validator = Validator::make($request->all(), [
            "estado" => "in:Pendiente,Confirmada,Cancelada,Finalizada"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $citas->update($validator->validated());
        return response()->json([
            "success" => true,
            "message" => "La cita ha sido cancelada correctamente.",
            $citas
        ], 200);
    }



    public function destroy(string $id)
    {
        $citas = citas::find($id);
        if (!$citas) {
            return response()->json(["message" => "Cita no encontrada"], 400);
        }

        $citas->delete();
        return response()->json(["message" => "Cita eliminada correctamente"], 200);
    }

    public function citasEsteMesPorPaciente($pacienteId)
    {
        $estado = "Confirmada";
        $citasMes = citas::where('id_paciente', $pacienteId)
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->where("estado", $estado)
            ->count();


        return response()->json([
            "success" => true,
            "citas" => $citasMes
        ]);
    }


    public function totalCitasPorPaciente($pacienteId)
    {
        $estado = "Finalizada";
        $totalCitas = citas::where('id_paciente', $pacienteId)
            ->where("estado", $estado)
            ->count();
        return response()->json([
            "success" => true,
            "citas" => $totalCitas
        ]);
    }

    public function citasConfirmdas()
    {
        $citas = citas::join('medicos', 'citas.id_medico', '=', 'medicos.id')
            ->join('pacientes', 'citas.id_paciente', '=', 'pacientes.id')
            ->select(
                'citas.*',
                'medicos.nombre as nombre_medico',
                'pacientes.nombre as nombre_paciente'
            )
            ->where('citas.estado', 'Confirmada')
            ->get();

        if ($citas->isEmpty()) {
            return response()->json([
                "success" => false,
                "message" => "No se encontraron citas pendientes"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "citas" =>   $citas
        ]);
    }

    public function citasPendientes()
    {
        $citas = citas::join('medicos', 'citas.id_medico', '=', 'medicos.id')
            ->join('pacientes', 'citas.id_paciente', '=', 'pacientes.id')
            ->select(
                'citas.*',
                'medicos.nombre as nombre_medico',
                'medicos.apellido as apellido_medico',
                'pacientes.nombre as nombre_paciente',
                'pacientes.apellido as apellido_paciente'
            )
            ->where('citas.estado', 'Pendiente')
            ->get();

        if ($citas->isEmpty()) {
            return response()->json([
                "success" => false,
                "message" => "No se encontraron citas pendientes"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "citas" =>   $citas
        ]);
    }

    public function citasPorPaciente(string $documento)
    {
        $citas = Citas::join('pacientes', 'citas.id_paciente', '=', 'pacientes.id')
            ->join('medicos', 'citas.id_medico', '=', 'medicos.id')
            ->join("especialidades_medicos", "medicos.id", "=", "especialidades_medicos.id_medico")
            ->join("especialidades", "especialidades_medicos.id_especialidad", "=", "especialidades.id")
            ->select(
                'citas.*',
                'pacientes.nombre as nombre_paciente',
                'medicos.nombre as nombre_medico',
                'medicos.apellido as apellido_medico',
                'especialidades.nombre as especialidad'
            )
            ->where('pacientes.documento', $documento)
            ->get();

        if ($citas->isEmpty()) {
            return response()->json(["message" => "No se encontraron citas para este paciente"], 404);
        }

        return response()->json($citas);
    }

    public function listarCitasConDoctorEspecialidad()
    {
        $citas = Citas::join('pacientes', 'citas.id_paciente', '=', 'pacientes.id')
            ->join('medicos', 'citas.id_medico', '=', 'medicos.id')
            ->join("especialidades_medicos", "medicos.id", "=", "especialidades_medicos.id_medico")
            ->join("especialidades", "especialidades_medicos.id_especialidad", "=", "especialidades.id")
            ->select(
                'citas.*',
                'pacientes.nombre as nombre_paciente',
                'pacientes.apellido as apellido_paciente',
                'pacientes.documento as documento_paciente',
                'medicos.nombre as nombre_medico',
                'medicos.apellido as apellido_medico',
                'especialidades.nombre as especialidad'
            )
            ->get();

        if ($citas->isEmpty()) {
            return response()->json(["message" => "No se encontraron citas para este paciente"], 404);
        }

        return response()->json($citas);
    }

    public function listarCitasPorDoctorConEspecialidad(string $id)
    {
        $citas = Citas::join('pacientes', 'citas.id_paciente', '=', 'pacientes.id')
            ->join('medicos', 'citas.id_medico', '=', 'medicos.id')
            ->join("especialidades_medicos", "medicos.id", "=", "especialidades_medicos.id_medico")
            ->join("especialidades", "especialidades_medicos.id_especialidad", "=", "especialidades.id")
            ->select(
                'citas.*',
                'pacientes.nombre as nombre_paciente',
                'pacientes.apellido as apellido_paciente',
                'pacientes.documento as documento_paciente',
                'medicos.nombre as nombre_medico',
                'medicos.apellido as apellido_medico',
                'especialidades.nombre as especialidad'
            )->where("citas.id_medico", $id)
            ->get();

        if ($citas->isEmpty()) {
            return response()->json(["message" => "No se encontraron citas para este paciente"], 404);
        }

        return response()->json($citas);
    }

    public function citasPorDoctorConfirmadas(string $idMedico)
    {
        $estado = "Confirmada";
        $citas = Citas::join('pacientes', 'citas.id_paciente', '=', 'pacientes.id')
            ->join('medicos', 'citas.id_medico', '=', 'medicos.id')
            ->join("especialidades_medicos", "medicos.id", "=", "especialidades_medicos.id_medico")
            ->join("especialidades", "especialidades_medicos.id_especialidad", "=", "especialidades.id")
            ->select(
                'citas.*',
                'pacientes.nombre as nombre_paciente',
                'medicos.nombre as nombre_medico',
                'medicos.apellido as apellido_medico',
                'especialidades.nombre as especialidad'
            )
            ->where('citas.id_medico', $idMedico)
            ->where("citas.estado", $estado)
            ->get();

        if ($citas->isEmpty()) {
            return response()->json([
                "success" => false,
                "message" => "No se encontraron citas para este paciente"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "citas" => $citas
        ]);
    }

    public function citasPorPacienteConfirmadas(string $documento)
    {
        $estado = "Confirmada";
        $citas = Citas::join('pacientes', 'citas.id_paciente', '=', 'pacientes.id')
            ->join('medicos', 'citas.id_medico', '=', 'medicos.id')
            ->join("especialidades_medicos", "medicos.id", "=", "especialidades_medicos.id_medico")
            ->join("especialidades", "especialidades_medicos.id_especialidad", "=", "especialidades.id")
            ->select(
                'citas.*',
                'pacientes.nombre as nombre_paciente',
                'medicos.nombre as nombre_medico',
                'medicos.apellido as apellido_medico',
                'especialidades.nombre as especialidad'
            )
            ->where('pacientes.documento', $documento)
            ->where("citas.estado", $estado)
            ->get();

        if ($citas->isEmpty()) {
            return response()->json(["message" => "No se encontraron citas para este paciente"], 404);
        }

        return response()->json($citas);
    }
    public function citasDeHoy()
    {
        $citas = Citas::join('pacientes', 'citas.id_paciente', '=', 'pacientes.id')
            ->join('medicos', 'citas.id_medico', '=', 'medicos.id')
            ->select(
                'citas.*',
                'pacientes.nombre as nombre_paciente',
                'medicos.nombre as nombre_medico'
            )
            ->whereDate('citas.fecha', today())
            ->get();

        return response()->json($citas);
    }

    public function contarCitas()
    {
        $totalCitas = citas::count();
        return response()->json(["message" => "Total de citas: $totalCitas"], 200);
    }

    public function totalCitasPorMes()
    {
        $estado = "Finalizada";
        $mesActual = now()->month;
        $anioActual = now()->year;

        $totalCitas = citas::where("estado", $estado)
            ->whereMonth("fecha", $mesActual)   // columna fecha de la cita
            ->whereYear("fecha", $anioActual)
            ->count();

        return response()->json([
            "success" => true,
            "citas" => $totalCitas
        ]);
    }

    public function totalCitasPendiente()
    {
        $estado = "Pendiente";


        $totalCitas = citas::where("estado", $estado)->count();

        return response()->json([
            "success" => true,
            "citas" => $totalCitas
        ]);
    }

    public function totalCitasConfirmadasDoctor(string $id)
    {
        $estado = "Confirmada";


        $totalCitas = citas::where("estado", $estado)
            ->where("id_medico", $id)
            ->count();

        return response()->json([
            "success" => true,
            "citas" => $totalCitas
        ]);
    }


    public function totalCitasPorMesDoctor(string $id)
    {
        $estado = "Finalizada";
        $mesActual = now()->month;
        $anioActual = now()->year;

        $totalCitas = citas::where("estado", $estado)
            ->where("id_medico", $id)
            ->whereMonth("fecha", $mesActual)   // columna fecha de la cita
            ->whereYear("fecha", $anioActual)
            ->count();

        return response()->json([
            "success" => true,
            "citas" => $totalCitas
        ]);
    }
}
