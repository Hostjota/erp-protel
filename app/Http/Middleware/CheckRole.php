<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  Cargo mínimo exigido (ex: 'admin')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Utiliza o método de hierarquia do Model
        if ($user->hasRole($role)) {
            return $next($request);
        }

        abort(403, 'Acesso não autorizado para o seu nível de privilégio.');
    }
}