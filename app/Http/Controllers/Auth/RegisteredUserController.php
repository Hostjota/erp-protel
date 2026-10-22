<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validação completa com os novos campos da base de dados
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'telefone' => ['nullable', 'string', 'max:255'],
            'morada' => ['nullable', 'string'],
            'role' => ['required', 'string', 'in:user,admin,superadmin'], // Valida os cargos permitidos na hierarquia
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Criação do utilizador com todos os atributos
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'morada' => $request->morada,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        // Dispara o evento de registo (opcional, útil caso queiras enviar email de boas-vindas)
        event(new Registered($user));

        // REMOVIDO: Auth::login($user); -> Para não deslogar o Admin que está a criar a conta!

        // 3. Redirecionamento de volta para a lista de utilizadores com feedback de sucesso
        return redirect()->route('utilizadores.index')->with('success', 'Utilizador criado com sucesso!');
    }

    public function destroy(User $user): RedirectResponse
    {
        $currentUser = auth()->user();

        // 1. Impedir que o utilizador elimine a sua própria conta
        if ($currentUser->id === $user->id) {
            return redirect()->route('utilizadores.index')->with('error', 'Não podes eliminar a tua própria conta!');
        }

        // 2. Se quem tenta apagar for 'admin' (e não superadmin)
        if ($currentUser->role === 'admin') {
            // Um admin NÃO pode eliminar outro admin nem um superadmin
            if ($user->role === 'admin' || $user->role === 'superadmin') {
                return redirect()->route('utilizadores.index')->with('error', 'Não tens permissão para eliminar este nível de utilizador.');
            }
        }

        // 3. Se for 'user' comum, nem sequer deve conseguir chegar aqui (mas por segurança bloqueamos)
        if ($currentUser->role !== 'admin' && $currentUser->role !== 'superadmin') {
            return redirect()->route('utilizadores.index')->with('error', 'Acesso não autorizado.');
        }

        // Se passar pelas validações, executa a eliminação
        $user->delete();

        return redirect()->route('utilizadores.index')->with('success', 'Utilizador eliminado com sucesso!');
    }
}
