<?php

// app/Http/Controllers/ErrorLogController.php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Throwable;
class ErrorLogController extends Controller
{
    /**
     * Exibe a interface de auditoria de erros.
     */
    public function index()
    {
        // Estatísticas para os cards e gráficos
        $stats = [
            'total_erros' => ErrorLog::count(),
            'erros_hoje' => ErrorLog::whereDate('created_at', now())->count(),
        ];

        // Dados para o gráfico, agrupados por tipo de exceção
        $exceptionsByType = ErrorLog::select('exception_type')
            ->selectRaw('count(*) as total')
            ->groupBy('exception_type')
            ->orderBy('total', 'desc')
            ->get();

        // Retorna a view e passa os dados para ela
        return view('auditoria.errosLogs', compact('stats', 'exceptionsByType'));
    }

    /**
     * Retorna os dados dos logs de erro para a tabela, com paginação e filtros.
     */
    public function fetch(Request $request)
    {
        $query = ErrorLog::with('user');


        // Filtro por mensagem
        if ($request->has('message') && $request->input('message')) {
            $query->where('message', 'like', '%' . $request->input('message') . '%');
        }

        // Filtro por tipo de exceção
        if ($request->has('type') && $request->input('type')) {
            $query->where('exception_type', $request->input('type'));
        }

        // Filtro por data de início
        if ($request->has('start_date') && $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        // Filtro por data de fim
        if ($request->has('end_date') && $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        $errors = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($errors);
    }

    /**
     * Deleta todos os logs de erro do banco de dados.
     */
    public function clearLogs()
    {
        try {
            // Usa o método truncate() para apagar todos os registros de forma rápida
            ErrorLog::truncate();
            return response()->json(['success' => true, 'message' => 'Logs de erro limpos com sucesso!']);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Falha ao limpar os logs: ' . $e->getMessage()], 500);
        }
    }
}
