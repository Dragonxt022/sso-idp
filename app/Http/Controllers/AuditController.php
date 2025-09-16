<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;


class AuditController extends Controller
{


    // AuditController.php
    public function index(Request $request)
    {
        $query = Audit::with(['user.unidade']);

        // filtros existentes…
        if ($request->filled('name')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$request->name}%"));
        }

        // logs paginados normais
        $logs = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estatísticas para os cards:
        $stats = [
            'login'  => Audit::where('action', 'login')->count(),
            'logout' => Audit::where('action', 'logout')->count(),
            'change_password' => Audit::where('action', 'change_password')->count(),
            'reset_password'  => Audit::where('action', 'reset_password')->count(),
        ];

        // Comparação mês atual x anterior
        $thisMonth  = Audit::whereMonth('created_at', now()->month)->count();
        $lastMonth  = Audit::whereMonth('created_at', now()->subMonth()->month)->count();
        $compare    = [
            'this' => $thisMonth,
            'last' => $lastMonth,
            'percent' => $lastMonth > 0 ? round(($thisMonth - $lastMonth) / $lastMonth * 100, 1) : 100,
        ];

        // Horários por tipo (para gráfico)
        $hourly = Audit::selectRaw('HOUR(created_at) as hour, action, count(*) as total')
            ->groupBy('hour', 'action')
            ->orderBy('hour')
            ->get();

        return view('auditoria.usuarios', compact('logs', 'stats', 'compare', 'hourly'));
    }


    public function fetch(Request $request)
    {
        $query = Audit::with(['user.unidade']); // ✅ Carrega cidade via unidade

        if ($request->filled('name')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$request->name}%"));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', "%{$request->ip}%");
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($logs);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Audit::with('user.unidade'); // ✅ model Audit com relação

        if ($request->filled('name')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->name . '%'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', '%' . $request->ip . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="auditoria.csv"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Usuário', 'Cidade', 'Ação', 'Descrição', 'IP', 'Data/Hora']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->user->name ?? 'N/A',
                    $log->user->unidade->cidade ?? 'N/A',
                    $log->action,
                    $log->description,
                    $log->ip_address,
                    $log->created_at->format('d/m/Y H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
