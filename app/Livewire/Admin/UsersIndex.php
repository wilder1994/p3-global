<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;

class UsersIndex extends Component
{
    public $users;

    public function mount()
    {
        $this->users = User::with('roles')->get();
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);

        // Cambiar estado
        $user->is_active = !$user->is_active;
        $user->save();

        // Recargar lista de usuarios
        $this->users = User::with('roles')->get();

        session()->flash('success', 'Estado del usuario actualizado.');
    }

    public function render()
    {
        return view('livewire.admin.users-index')
            ->layout('layouts.app');
    }
}
