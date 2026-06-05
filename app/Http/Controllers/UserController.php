<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rig;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('rig')->orderBy('role')->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $rigs = Rig::orderBy('name')->get();
        return view('users.create', compact('rigs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,rig_manager,supervisor',
            'rig_id'   => 'nullable|exists:rigs,id',
        ]);

        User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'role'              => $data['role'],
            'rig_id'            => $data['rig_id'] ?? null,
            'active'            => true,
            'email_verified_at' => now(),
        ]);

        AuditService::log('creó', 'usuario', "Creó usuario {$data['name']} ({$data['role']})");
        return redirect()->route('users.index')->with('success', "Usuario {$data['name']} creado correctamente.");
    }

    public function edit(User $user)
    {
        $rigs = Rig::orderBy('name')->get();
        return view('users.edit', compact('user', 'rigs'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,rig_manager,supervisor',
            'rig_id'   => 'nullable|exists:rigs,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->update([
            'name'   => $data['name'],
            'email'  => $data['email'],
            'role'   => $data['role'],
            'rig_id' => $data['rig_id'] ?? null,
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        AuditService::log('actualizó', 'usuario', "Actualizó usuario {$user->name}");
        return redirect()->route('users.index')->with('success', "Usuario {$user->name} actualizado.");
    }

    public function toggle(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }
        $user->update(['active' => !$user->active]);
        $estado = $user->active ? 'activado' : 'desactivado';
        AuditService::log($estado, 'usuario', ucfirst($estado) . " cuenta de {$user->name}");
        return back()->with('success', "Usuario {$user->name} {$estado}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        AuditService::log('eliminó', 'usuario', "Eliminó usuario {$user->name} ({$user->email})");
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }
}
