<?php

namespace App\Http\Controllers;

use App\Models\citas;
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
            "estado" => "required|in:Pendiente,Confirmada,Cancelada"
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
            return response()->json(["message" => "Cita no encontrada"], 400);
        }

        return response()->json($citas);
    }

    public function update(Request $request, string $id)
    {
        $citas = citas::find($id);
        if (!$citas) {
            return response()->json(["message" => "Cita no encontrada"], 400);
        }


        $validator = Validator::make($request->all(), [
            "descripcion" => "string",

            "id_medico" => "integer",
            "id_paciente" => "integer",
            "fecha" => "date",
            "hora_inicio" => "string",
            "estado" => "in:Pendiente,Confirmada,Cancelada"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $citas->update($validator->validated());
        return response()->json($citas, 200);
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

    public function citasConfirmadas()
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

        return response()->json($citas);
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
}
