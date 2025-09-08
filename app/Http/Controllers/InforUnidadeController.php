<?php

namespace App\Http\Controllers;

use App\Models\InforUnidade;
use Illuminate\Http\Request;

class InforUnidadeController extends Controller
{
    /**
     * Lista todos os registros.
     */
    public function index()
    {
        return response()->json(InforUnidade::all(), 200);
    }

    /**
     * Cria um novo registro.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cep' => 'required|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18|unique:infor_unidade,cnpj',
        ]);

        $unidade = InforUnidade::create($validated);

        return response()->json($unidade, 201);
    }

    /**
     * Mostra um registro específico.
     */
    public function show($id)
    {
        $unidade = InforUnidade::findOrFail($id);
        return response()->json($unidade, 200);
    }

    /**
     * Atualiza um registro existente.
     */
    public function update(Request $request, $id)
    {
        $unidade = InforUnidade::findOrFail($id);

        $validated = $request->validate([
            'cep' => 'required|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18|unique:infor_unidade,cnpj,' . $id,
        ]);

        $unidade->update($validated);

        return response()->json($unidade, 200);
    }

    /**
     * Remove um registro.
     */
    public function destroy($id)
    {
        $unidade = InforUnidade::findOrFail($id);
        $unidade->delete();

        return response()->json(['message' => 'Unidade removida com sucesso'], 200);
    }
}
