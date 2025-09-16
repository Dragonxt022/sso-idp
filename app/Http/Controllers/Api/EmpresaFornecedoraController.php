<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmpresaFornecedora;
use App\Services\ImageService;
use Illuminate\Http\Request;

class EmpresaFornecedoraController extends Controller
{
    public function index()
    {
        $empresas = EmpresaFornecedora::paginate(15);

        $empresas->getCollection()->transform(function ($empresa) {
            $empresa->logo = $empresa->logo
                ? asset('frontend/empresas/' . $empresa->logo)
                : null;
            return $empresa;
        });

        return response()->json($empresas);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'cnpj' => 'required|string|unique:empresas_fornecedoras,cnpj',
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'inscricao_estadual' => 'nullable|string|max:50',
            'telefone' => 'nullable|string|max:20',
            'email_contato' => 'nullable|email|max:255',
            'endereco' => 'nullable|string',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:9',
            'user_id' => 'nullable|exists:users,id',
            'logo' => 'nullable',
        ]);

        // normalizar CNPJ
        $validated['cnpj'] = preg_replace('/[^\d]/', '', $validated['cnpj']);

        // tratar logo
        if ($request->hasFile('logo')) {
            $photoPath = ImageService::handleUpload($request->file('logo'), null, 'frontend/empresas');
            $validated['logo'] = $photoPath ? basename($photoPath) : null;
        }

        $empresa = EmpresaFornecedora::create($validated);

        return response()->json($empresa, 201);
    }

    public function update(Request $request, $id)
    {
        $empresa = EmpresaFornecedora::findOrFail($id);

        $validated = $request->validate([
            'cnpj' => 'string|unique:empresas_fornecedoras,cnpj,' . $empresa->id,
            'razao_social' => 'string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'inscricao_estadual' => 'nullable|string|max:50',
            'telefone' => 'nullable|string|max:20',
            'email_contato' => 'nullable|email|max:255',
            'endereco' => 'nullable|string',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:9',
            'user_id' => 'nullable|exists:users,id',
            'logo' => 'nullable',
        ]);

        // normalizar CNPJ
        if (!empty($validated['cnpj'])) {
            $validated['cnpj'] = preg_replace('/[^\d]/', '', $validated['cnpj']);
        }

        // atualizar logo
        if ($request->hasFile('logo')) {
            $photoPath = ImageService::handleUpload($request->file('logo'), null, 'frontend/empresas');
            $validated['logo'] = $photoPath ? basename($photoPath) : $empresa->logo;
        }

        $empresa->update($validated);

        return response()->json($empresa);
    }

    public function show($id)
    {
        $empresa = EmpresaFornecedora::findOrFail($id);
        return response()->json($empresa);
    }

    public function destroy($id)
    {
        $empresa = EmpresaFornecedora::findOrFail($id);

        // remover logo do servidor
        if ($empresa->logo && file_exists(public_path('frontend/empresas/' . $empresa->logo))) {
            unlink(public_path('frontend/empresas/' . $empresa->logo));
        }

        $empresa->delete();

        return response()->json(['message' => 'Empresa fornecedora excluída com sucesso.']);
    }

}
