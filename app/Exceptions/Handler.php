<?php

namespace App\Exceptions;

use App\Models\ErrorLog;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A lista de exceções que não devem ser reportadas.
     */
    protected $dontReport = [];

    /**
     * A lista de inputs que não devem ser gravados no log.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Reporta uma exceção.
     */
    public function report(Throwable $exception): void
    {
        try {
            $user = Auth::user();

            \App\Models\ErrorLog::create([
                'user_id'       => $user?->id,
                'message'       => $exception->getMessage(),
                'stack_trace'   => collect($exception->getTrace())->map(fn($t) => Arr::except($t, ['args']))->toJson(),
                'file'          => $exception->getFile(),
                'line'          => $exception->getLine(),
                'exception_type'=> get_class($exception),
                'url'           => request()?->fullUrl() ?? 'N/A',
                'method'        => request()?->method() ?? 'N/A',
                'payload'       => json_encode($this->sanitizeInputs(request()?->all() ?? [])),
                'ip'            => request()?->ip() ?? 'N/A',
                'user_agent'    => request()?->userAgent() ?? 'N/A',
            ]);
        } catch (\Throwable $e) {
            // Evita quebrar a aplicação se falhar o log
        }

        parent::report($exception);
    }



    /**
     * Renderiza a exceção.
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Remove dados sensíveis antes de gravar.
     */
    protected function sanitizeInputs(array $inputs): array
    {
        foreach ($this->dontFlash as $key) {
            if (isset($inputs[$key])) {
                $inputs[$key] = '***';
            }
        }
        return $inputs;
    }

    /**
     * Define quais exceções são críticas.
     */
    protected function isCritical(Throwable $exception): bool
    {
        return $exception instanceof \ErrorException
            || $exception instanceof \Illuminate\Database\QueryException
            || $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException;
    }

    /**
     * Notifica o admin sobre erros críticos.
     */
    protected function notifyAdmin(ErrorLog $log): void
    {
        Mail::raw(
            "Erro crítico no sistema:\n\n" .
                "Usuário: {$log->user?->name}\n" .
                "Mensagem: {$log->message}\n" .
                "Tipo: {$log->exception_type}\n" .
                "URL: {$log->url}\n" .
                "IP: {$log->ip}\n" .
                "Data/Hora: {$log->created_at}\n",
            function ($message) {
                $message->to('pissinatti2019@gmail.com')
                    ->subject('Alerta: Erro crítico no sistema');
            }
        );

        // ⚡ Slack ou outros canais podem ser integrados aqui
    }
}
