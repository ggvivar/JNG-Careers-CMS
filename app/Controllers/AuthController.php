<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Api\NotificationApiController;
use App\Models\UserModel;
class AuthController extends BaseController
{
    public function loginForm()
    {
        if (session()->get('admin_id')) {
            return redirect()->to('/admin');
        }

        return view('auth/login');
    }
    public function forgotPasswordForm()
    {
        if (session()->get('admin_id')) {
            return redirect()->to('/admin');
        }

        return view('auth/forgot_password');
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

    public function forgot_password()
    {
        helper('text');
        $username = trim((string) $this->request->getPost('username'));
        $email = (string) $this->request->getPost('email');

        $user = db_connect()->table('users u')
            ->select('u.*, r.name as role_name')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->where('u.username', $username)
            ->where('u.email', $email)
            ->where('u.date_deleted', null)
            ->get()
            ->getRowArray();
        // dd($user);
        
        $reset_password = random_string('alnum',8);
        
        $reset_password = 'admin123';
        if (! $user) {
            return redirect()->back()->withInput()->with('error', 'Email or Username not existing');
        }
        $id =$user['id'];

        $email_stat=  NotificationApiController::send($email,[
        'template' =>'forgot_password',
        'password'=>$reset_password]);

        if (strtolower($this->request->getMethod()) === 'post') {

            $payload = [
                'username' => $username,
                'email' => $email ?: null,
                'password' =>  password_hash($reset_password, PASSWORD_DEFAULT),
                'date_updated' => date('Y-m-d H:i:s'),
            ];

            $userModel = new UserModel();
            // dd($userModel);
            $userModel->update($id, $payload);

            return redirect()->to('/')->with('success', 'Password Reset and Send to your Register Email.');
        }

        // session()->set([
        //     'admin_id' => $user['id'],
        //     'admin_name' => $user['name'] ?: $user['username'],
        //     'admin_role_id' => $user['role_id'],
        //     'is_admin_logged_in' => true,
        // ]);

        //generate random password
        $reset_password = random_string(7);

        return redirect()->to('/admin');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}