<?php

namespace App\Http\Controllers;


use App\Models\recepcionistas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class RecepcionistasController extends Controller
{

    public function index()
    {
        $recepcionistas = recepcionistas::all();
        return response()->json($recepcionistas);
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

        $recepcionistas = recepcionistas::create($validator->validated());
        return response()->json($recepcionistas, 201);
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
}