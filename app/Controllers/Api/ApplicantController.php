<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\ApplicantMailer;
use App\Models\ApplicantModel;
use App\Models\ApplicantPasswordResetModel;
use App\Models\ApplicantTokenModel;

class ApplicantController extends BaseController
{
    public function register()
    {
        $model = new ApplicantModel();

        $firstname = trim((string) $this->request->getPost('firstname'));
        $lastname  = trim((string) $this->request->getPost('lastname'));
        $email     = trim((string) $this->request->getPost('email'));
        $password  = (string) $this->request->getPost('password');

        if ($firstname === '' || $lastname === '' || $email === '' || $password === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'Firstname, lastname, email, and password are required.',
            ]);
        }

        $exists = $model->where('email', $email)->where('date_deleted', null)->first();
        if ($exists) {
            return $this->response->setStatusCode(409)->setJSON([
                'status' => false,
                'message' => 'Email already exists.',
            ]);
        }

        $username = strtolower(preg_replace('/[^a-z0-9]+/i', '', $firstname . $lastname));
        if ($username === '') {
            $username = 'applicant' . time();
        }

        $baseUsername = $username;
        $i = 1;
        while ($model->where('username', $username)->first()) {
            $username = $baseUsername . $i;
            $i++;
        }

        $model->insert([
            'username'     => $username,
            'password'     => password_hash($password, PASSWORD_DEFAULT),
            'firstname'    => $firstname,
            'middlename'   => trim((string) $this->request->getPost('middlename')) ?: null,
            'lastname'     => $lastname,
            'suffix'       => trim((string) $this->request->getPost('suffix')) ?: null,
            'email'        => $email,
            'phone'        => trim((string) $this->request->getPost('phone')) ?: null,
            'birthdate'    => $this->request->getPost('birthdate') ?: null,
            'gender'       => trim((string) $this->request->getPost('gender')) ?: null,
            'civil_status' => trim((string) $this->request->getPost('civil_status')) ?: null,
            'nationality'  => trim((string) $this->request->getPost('nationality')) ?: null,
            'address'      => trim((string) $this->request->getPost('address')) ?: null,
            'city'         => trim((string) $this->request->getPost('city')) ?: null,
            'province'     => trim((string) $this->request->getPost('province')) ?: null,
            'zip_code'     => trim((string) $this->request->getPost('zip_code')) ?: null,
            'cover_letter' => $this->request->getPost('cover_letter') ?: null,
            'date_created' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Applicant registered successfully.',
        ]);
    }

    public function login()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $device = trim((string) $this->request->getPost('device_name')) ?: 'unknown device';

        $db = db_connect();
        $applicant = $db->table('applicants')
            ->where('email', $email)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $applicant || ! password_verify($password, $applicant['password'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'message' => 'Invalid credentials.',
            ]);
        }

        $token = bin2hex(random_bytes(64));

        (new ApplicantTokenModel())->insert([
            'applicant_id' => $applicant['id'],
            'token' => $token,
            'device_name' => $device,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Login successful.',
            'data' => [
                'token' => $token,
                'applicant' => [
                    'id' => $applicant['id'],
                    'username' => $applicant['username'],
                    'firstname' => $applicant['firstname'],
                    'lastname' => $applicant['lastname'],
                    'email' => $applicant['email'],
                ],
            ],
        ]);
    }

    public function logout()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches);
        $token = trim($matches[1] ?? '');

        db_connect()->table('applicant_tokens')
            ->where('token', $token)
            ->update([
                'revoked_at' => date('Y-m-d H:i:s'),
            ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me()
    {
        $applicant = service('request')->applicant;
        unset($applicant['password']);

        return $this->response->setJSON([
            'status' => true,
            'data' => $applicant,
        ]);
    }

    public function edit()
    {
        $applicant = service('request')->applicant;
        $id = (int) $applicant['id'];
        $db = db_connect();

        $email = trim((string) $this->request->getPost('email'));
        if ($email !== '') {
            $exists = $db->table('applicants')
                ->where('email', $email)
                ->where('id !=', $id)
                ->where('date_deleted', null)
                ->get()
                ->getRowArray();

            if ($exists) {
                return $this->response->setStatusCode(409)->setJSON([
                    'status' => false,
                    'message' => 'Email already exists.',
                ]);
            }
        }

        $payload = [
            'firstname'    => trim((string) $this->request->getPost('firstname')) ?: $applicant['firstname'],
            'middlename'   => trim((string) $this->request->getPost('middlename')) ?: null,
            'lastname'     => trim((string) $this->request->getPost('lastname')) ?: $applicant['lastname'],
            'suffix'       => trim((string) $this->request->getPost('suffix')) ?: null,
            'email'        => $email ?: $applicant['email'],
            'phone'        => trim((string) $this->request->getPost('phone')) ?: null,
            'birthdate'    => $this->request->getPost('birthdate') ?: null,
            'gender'       => trim((string) $this->request->getPost('gender')) ?: null,
            'civil_status' => trim((string) $this->request->getPost('civil_status')) ?: null,
            'nationality'  => trim((string) $this->request->getPost('nationality')) ?: null,
            'address'      => trim((string) $this->request->getPost('address')) ?: null,
            'city'         => trim((string) $this->request->getPost('city')) ?: null,
            'province'     => trim((string) $this->request->getPost('province')) ?: null,
            'zip_code'     => trim((string) $this->request->getPost('zip_code')) ?: null,
            'cover_letter' => $this->request->getPost('cover_letter') ?: null,
            'date_updated' => date('Y-m-d H:i:s'),
        ];

        $newPassword = (string) $this->request->getPost('password');
        if ($newPassword !== '') {
            $payload['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $db->table('applicants')->where('id', $id)->update($payload);

        $updated = $db->table('applicants')->where('id', $id)->get()->getRowArray();
        unset($updated['password']);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Applicant profile updated.',
            'data' => $updated,
        ]);
    }

    public function dashboard()
    {
        $applicant = service('request')->applicant;
        $applicantId = (int) $applicant['id'];
        $db = db_connect();

        $applications = $db->table('job_applications ja')
            ->select('ja.id, ja.applied_at, s.name as status_name, j.name as job_name')
            ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('status s', 's.id = ja.status_id', 'left')
            ->where('ja.applicant_id', $applicantId)
            ->where('ja.date_deleted', null)
            ->orderBy('ja.id', 'DESC')
            ->get()
            ->getResultArray();

        $notifications = $db->table('applicant_notifications')
            ->where('applicant_id', $applicantId)
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'profile' => [
                    'id' => $applicant['id'],
                    'firstname' => $applicant['firstname'],
                    'lastname' => $applicant['lastname'],
                    'email' => $applicant['email'],
                ],
                'applications' => $applications,
                'notifications' => $notifications,
            ],
        ]);
    }

    public function uploadResume()
    {
        $applicant = service('request')->applicant;
        $id = (int) $applicant['id'];

        $file = $this->request->getFile('resume');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'Valid resume file is required.',
            ]);
        }

        $allowed = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        if (! in_array($file->getMimeType(), $allowed, true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'Only PDF and Word files are allowed.',
            ]);
        }

        $targetDir = FCPATH . 'uploads/resumes';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($targetDir, $newName);

        db_connect()->table('applicants')
            ->where('id', $id)
            ->update([
                'resume' => 'uploads/resumes/' . $newName,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Resume uploaded successfully.',
            'data' => [
                'resume' => 'uploads/resumes/' . $newName,
            ],
        ]);
    }

    public function forgotPassword()
    {
        $email = trim((string) $this->request->getPost('email'));
        if ($email === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'Email is required.',
            ]);
        }

        $db = db_connect();
        $applicant = $db->table('applicants')
            ->where('email', $email)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $applicant) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'If the email exists, a reset link has been sent.',
            ]);
        }

        $token = bin2hex(random_bytes(32));

        (new ApplicantPasswordResetModel())->insert([
            'applicant_id' => $applicant['id'],
            'reset_token' => $token,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        (new ApplicantMailer())->passwordReset(
            $applicant['email'],
            trim(($applicant['firstname'] ?? '') . ' ' . ($applicant['lastname'] ?? '')),
            $token
        );

        return $this->response->setJSON([
            'status' => true,
            'message' => 'If the email exists, a reset link has been sent.',
        ]);
    }

    public function resetPassword()
    {
        $token = trim((string) $this->request->getPost('token'));
        $password = (string) $this->request->getPost('password');

        if ($token === '' || $password === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'Token and password are required.',
            ]);
        }

        $db = db_connect();
        $reset = $db->table('applicant_password_resets')
            ->where('reset_token', $token)
            ->where('used_at', null)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();

        if (! $reset) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Invalid or expired reset token.',
            ]);
        }

        $db->table('applicants')
            ->where('id', $reset['applicant_id'])
            ->update([
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

        $db->table('applicant_password_resets')
            ->where('id', $reset['id'])
            ->update([
                'used_at' => date('Y-m-d H:i:s'),
            ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Password reset successful.',
        ]);
    }
}