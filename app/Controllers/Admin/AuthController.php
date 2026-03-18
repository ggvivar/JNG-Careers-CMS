<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    public function loginForm()
    {
        if (session()->get('admin_id')) {
            return redirect()->to('/admin');
        }

        return view('admin/auth/login');
    }

    public function login()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $user = db_connect()->table('users u')
            ->select('u.*, r.name as role_name')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->where('u.username', $username)
            ->where('u.date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid login credentials.');
        }

        session()->set([
            'admin_id' => $user['id'],
            'admin_name' => $user['name'] ?: $user['username'],
            'admin_role_id' => $user['role_id'],
            'is_admin_logged_in' => true,
        ]);

        return redirect()->to('/admin');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}