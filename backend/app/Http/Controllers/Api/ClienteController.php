<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Traits\AuditoriaTrait;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    use AuditoriaTrait;

    public function index(Request $request)
    {
        $inactivos = $request->boolean('inactivos');

        $query = Cliente::with('motocicletas')->orderBy('primer_nombre', 'ASC');

        if ($inactivos) {
            $query->where('estadoA', false);
        } else {
            $query->where('estadoA', true);
        }

        $clientes = $query->get();

        return response()->json(['clientes' => $clientes]);
    }

    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->load('motocicletas');

        return response()->json(['cliente' => $cliente]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ci' => 'required|string|regex:/^\d+$/|unique:TClientes,ci',
            'primer_nombre' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'segundo_nombre' => 'nullable|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'apellido_paterno' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'apellido_materno' => 'nullable|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'telefono' => 'nullable|string|regex:/^\d{8}$/',
            'nit' => 'nullable|string|regex:/^\d+$/|unique:TClientes,nit',
            'direccion' => 'nullable|string|max:500',
        ], [
            'ci.regex' => 'Solo números',
            'ci.unique' => 'Ya registrado',
            'primer_nombre.regex' => 'Solo letras',
            'apellido_paterno.regex' => 'Solo letras',
            'telefono.regex' => '8 números',
            'nit.regex' => 'Solo números',
        ]);

        $usuarioId = auth()->id();

        $cliente = Cliente::create([
            'ci' => $validated['ci'],
            'primer_nombre' => $validated['primer_nombre'],
            'segundo_nombre' => $validated['segundo_nombre'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'],
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'telefono' => $validated['telefono'],
            'nit' => $validated['nit'],
            'direccion' => $validated['direccion'],
            'estadoA' => true,
            'usuarioA' => $usuarioId,
            'fechahoraA' => now(),
        ]);

        $valoresCliente = implode('|', [
            $validated['ci'],
            $validated['primer_nombre'],
            $validated['segundo_nombre'] ?? '',
            $validated['apellido_paterno'],
            $validated['apellido_materno'] ?? '',
            $validated['fecha_nacimiento'] ?? '',
            $validated['telefono'] ?? '',
            $validated['nit'] ?? '',
            $validated['direccion'] ?? '',
        ]);

        $this->registrarAuditoria(
            'TClientes',
            $cliente->id_cliente,
            'I',
            null,
            null,
            $valoresCliente,
            'Creación de cliente'
        );

        return response()->json([
            'message' => 'Cliente creado exitosamente',
            'cliente' => $cliente,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'ci' => 'sometimes|string|regex:/^\d+$/|unique:TClientes,ci,' . $id . ',id_cliente',
            'primer_nombre' => 'sometimes|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'segundo_nombre' => 'nullable|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'apellido_paterno' => 'sometimes|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'apellido_materno' => 'nullable|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'telefono' => 'nullable|string|regex:/^\d{8}$/',
            'nit' => 'sometimes|string|regex:/^\d+$/|unique:TClientes,nit,' . $id . ',id_cliente',
            'direccion' => 'nullable|string|max:500',
        ], [
            'ci.regex' => 'Solo números',
            'ci.unique' => 'Ya registrado',
            'primer_nombre.regex' => 'Solo letras',
            'apellido_paterno.regex' => 'Solo letras',
            'telefono.regex' => '8 números',
            'nit.regex' => 'Solo números',
            'nit.unique' => 'Ya registrado',
        ]);

        $usuarioId = auth()->id();
        $camposModificados = [];
        $valoresAnteriores = [];
        $valoresNuevos = [];

        foreach ($validated as $campo => $valor) {
            if ($cliente->{$campo} !== $valor) {
                $camposModificados[] = $campo;
                $valoresAnteriores[] = $cliente->{$campo} ?? '';
                $valoresNuevos[] = $valor ?? '';
            }
        }

        if (!empty($camposModificados)) {
            $cliente->update($validated);
            $cliente->update([
                'usuarioA' => $usuarioId,
                'fechahoraA' => now(),
            ]);

            $this->registrarAuditoria(
                'TClientes',
                $id,
                'U',
                implode('|', $camposModificados),
                implode('|', $valoresAnteriores),
                implode('|', $valoresNuevos),
                'Actualización de cliente'
            );
        }

        return response()->json([
            'message' => 'Cliente actualizado exitosamente',
            'cliente' => $cliente,
        ]);
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        $usuarioId = auth()->id();

        $cliente->update([
            'estadoA' => false,
            'usuarioA' => $usuarioId,
            'fechahoraA' => now(),
        ]);

        $this->registrarAuditoria(
            'TClientes',
            $id,
            'U',
            'estadoA',
            '1',
            '0',
            'Desactivación de cliente'
        );

        return response()->json(['message' => 'Cliente desactivado exitosamente']);
    }

    public function reactivar($id)
    {
        $cliente = Cliente::findOrFail($id);
        $usuarioId = auth()->id();

        $cliente->update([
            'estadoA' => true,
            'usuarioA' => $usuarioId,
            'fechahoraA' => now(),
        ]);

        $this->registrarAuditoria(
            'TClientes',
            $id,
            'U',
            'estadoA',
            '0',
            '1',
            'Reactivación de cliente'
        );

        return response()->json(['message' => 'Cliente reactivado exitosamente']);
    }
}
