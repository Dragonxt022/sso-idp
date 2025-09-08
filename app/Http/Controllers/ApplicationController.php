<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::orderBy('order')->get();

        return view('config.aplicacoes', compact('applications'));
    }

    public function updateOrder(Request $request)
    {
        $order = $request->input('order'); // array com IDs na nova ordem

        foreach ($order as $index => $id) {
            Application::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function toggleActive(Application $application)
    {
        $application->active = !$application->active;
        $application->save();

        return response()->json([
            'success' => true,
            'active' => $application->active
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

        $redirectUrl = 'https://login.taiksu.com.br/?redirect_uri=' . urlencode($validated['link_redirect']);

        $application = Application::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $iconPath,
            'link_redirect' => $redirectUrl,
        ]);

        // Criar permissão com o nome da aplicação (caso não exista)
        $permissionName = $validated['name'];
        $permission = Permission::firstOrCreate(
            ['name' => $permissionName, 'guard_name' => 'api']
        );

        // Vincula as roles existentes ao app
        if ($request->filled('role_ids')) {
            $application->roles()->sync($validated['role_ids']);
        }

        return redirect()->route('applications.index')->with('success', 'Aplicativo criado com sucesso!');
    }

    public function edit(Application $application)
    {
        $roles = Role::all();
        $selectedRoles = $application->roles->pluck('id')->toArray();

        $redirectUri = '';

        if ($application->link_redirect) {
            $parts = parse_url($application->link_redirect);

            if (isset($parts['query'])) {
                parse_str($parts['query'], $queryParams);
                if (isset($queryParams['redirect_uri'])) {
                    $redirectUri = urldecode($queryParams['redirect_uri']); // pega tudo após redirect_uri=
                }
            }
        }

        return view('config.edit', compact('application', 'roles', 'selectedRoles', 'redirectUri'));
    }



    public function update(Request $request, Application $application)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'link_redirect' => 'required|string|max:255',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        // Atualiza ícone
        $iconPath = ImageService::handleUpload($request->file('icon'), $application->icon, 'applications');

        // Monta nova URL de redirecionamento
        $redirectUrl = 'https://login.taiksu.com.br/?redirect_uri=' . urlencode($validated['link_redirect']);

        // Atualiza aplicação
        $application->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $iconPath,
            'link_redirect' => $redirectUrl,
        ]);

        // Atualiza roles
        if ($request->filled('role_ids')) {
            $application->roles()->sync($validated['role_ids']);
        } else {
            $application->roles()->detach();
        }

        return redirect()->route('applications.index')->with('success', 'Aplicativo atualizado com sucesso!');
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
