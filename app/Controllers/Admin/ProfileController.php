<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ProfileController extends BaseController
{
    public function index()
    {
        $adminId = (int) (session()->get('admin_id') ?? 0);

        if (! $adminId) {
            return redirect()->to('/admin/login');
        }

        $user = db_connect()->table('users u')
            ->select('u.id, u.username, u.name, u.role_id, r.name as role_name')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->where('u.id', $adminId)
            ->where('u.date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $user) {
            session()->destroy();
            return redirect()->to('/admin/login')->with('error', 'User account not found.');
        }

        return view('admin/profile/index', [
            'user' => $user,
        ]);
    }

    public function updatePassword()
    {
        $adminId = (int) (session()->get('admin_id') ?? 0);

        if (! $adminId) {
            return redirect()->to('/admin/login');
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword     = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[8]|max_length[255]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (! $this->validate($rules, [
            'current_password' => [
                'required' => 'Current password is required.',
            ],
            'new_password' => [
                'required'   => 'New password is required.',
                'min_length' => 'New password must be at least 8 characters.',
                'max_length' => 'New password is too long.',
            ],
            'confirm_password' => [
                'required' => 'Please confirm your new password.',
                'matches'  => 'Confirm password does not match the new password.',
            ],
        ])) {
            return redirect()->back()->withInput()->with('error', $this->validator->getFirstError());
        }

        $user = db_connect()->table('users')
            ->select('id, username, name, password')
            ->where('id', $adminId)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $user) {
            session()->destroy();
            return redirect()->to('/admin/login')->with('error', 'User account not found.');
        }

        if (! password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Current password is incorrect.');
        }

        if (password_verify($newPassword, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'New password must be different from your current password.');
        }

        db_connect()->table('users')
            ->where('id', $adminId)
            ->update([
                'password'     => password_hash($newPassword, PASSWORD_DEFAULT),
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/admin/profile')->with('success', 'Password changed successfully.');
    }
}