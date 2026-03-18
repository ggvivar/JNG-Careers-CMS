<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $role = $db->table('roles')->where('name', 'Super Admin')->get()->getRowArray();
        $company = $db->table('companies')->where('code', 'JNG')->get()->getRowArray();
        $department = $db->table('departments')->where('code', 'IT')->get()->getRowArray();

        $exists = $db->table('users')->where('username', 'admin')->get()->getRowArray();

        $data = [
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'email' => 'admin@example.com',
            'name' => 'Super Admin',
            // 'company_id' => $company['id'] ?? null,
            'department_id' => $department['id'] ?? null,
            'role_id' => $role['id'] ?? null,
            'date_updated' => $now,
        ];

        if ($exists) {
            $db->table('users')->where('id', $exists['id'])->update($data);
        } else {
            $data['date_created'] = $now;
            $db->table('users')->insert($data);
        }
    }
}