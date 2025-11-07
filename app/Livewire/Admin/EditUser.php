<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class EditUser extends Component
{
    public $user;
    public $name;
    public $email;
    public $roles = [];
    public $allRoles = [];

    // 👇 nuevos campos para la contraseña
    public $password;
    public $password_confirmation;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->roles = $user->roles->pluck('name')->toArray();

        $this->allRoles = Role::pluck('name')->toArray();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'roles' => 'required|array',
            'password' => 'nullable|min:8|confirmed', // 👈 validación solo si hay password
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        // 👇 si el admin escribió nueva contraseña, la guardamos hasheada
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $this->user->update($data);

        $this->user->syncRoles($this->roles);

        session()->flash('success', 'Usuario actualizado correctamente.');

        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.edit-user')
            ->layout('layouts.app');
    }
}
