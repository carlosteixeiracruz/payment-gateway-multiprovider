<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\QueryException; // Importante para erros de banco
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'api/webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // 1. Tratamento para erro de Banco de Dados (ex: MySQL caiu)
        $exceptions->render(function (QueryException $e) {
            return response()->json([
                'error' => 'Serviço temporariamente indisponível',
                'message' => 'Não foi possível conectar ao banco de dados.'
            ], 503);
        });

        // 2. Tratamento para rotas que não existem
        $exceptions->render(function (NotFoundHttpException $e) {
            return response()->json([
                'error' => 'Recurso não encontrado',
                'message' => 'A rota ou registro solicitado não existe.'
            ], 404);
        });

        // 3. Tratamento genérico para qualquer outro erro crítico
        $exceptions->render(function (Throwable $e) {
            if (app()->environment('production')) {
                return response()->json([
                    'error' => 'Erro interno',
                    'message' => 'Ocorreu um erro inesperado em nosso servidor.'
                ], 500);
            }
        });
    })->create();
