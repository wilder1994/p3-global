<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use App\Models\User;

class CreateUser extends Component
{
    public $name;
    public $email;
    public $password;
    public $role; // aquí se guarda el rol seleccionado

    public $roles = [];

    public function mount()
    {
        // Trae los nombres de roles en orden
        $this->roles = Role::orderBy('name')->pluck('name')->toArray();
    }

    public function save()
    {
        $this->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required',
        ]);

        $user = User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => bcrypt($this->password),
        ]);

        // Asignar rol al usuario
        $user->assignRole($this->role);

        session()->flash('message', 'Usuario creado correctamente con rol: ' . $this->role);

        // Limpiar campos
        $this->reset(['name', 'email', 'password', 'role']);
    }

    public function render()
    {
        return view('livewire.admin.create-user')
            ->layout('layouts.app'); // usar tu layout principal
    }
}
