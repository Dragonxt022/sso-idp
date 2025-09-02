<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::all();

        // Filtra apenas aplicações que o usuário tem alguma role associada
        $allowedApps = $applications->filter(function ($app) {
            return $app->roles()->whereIn('roles.id', Auth::user()->roles->pluck('id'))->exists();
        });

        return view('config.aplicacoes', [
            'applications' => $allowedApps,
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        return view('config.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'link_redirect' => 'required|string|max:255',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $iconPath = ImageService::handleUpload($request->file('icon'), null, 'applications');

        // Concatena com a URL base do SSO
        $redirectUrl = 'https://login.taiksu.com.br/?redirect_uri=' . urlencode($validated['link_redirect']);

        $application = Application::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $iconPath,
            'link_redirect' => $redirectUrl,
        ]);

        if ($request->filled('role_ids')) {
            $application->roles()->sync($validated['role_ids']);
        }

        return redirect()->route('config.index')->with('success', 'Aplicativo criado com sucesso!');
    }

    public function destroy(Application $application)
    {
        // Remove o ícone, se existir
        if ($application->icon) {
            \App\Services\ImageService::handleUpload(null, $application->icon);
        }

        // Remove as relações de roles
        $application->roles()->detach();

        // Remove a aplicação
        $application->delete();

        return redirect()->route('applications.index')->with('success', 'Aplicativo removido com sucesso!');
    }
}
