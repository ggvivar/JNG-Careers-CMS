<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DefaultSeeder extends Seeder
{
    public function run()
    {
        $this->call('ModuleSeeder');
        $this->call('RoleSeeder');
        $this->call('CompanySeeder');
        $this->call('DepartmentSeeder');
        $this->call('FeatureSeeder');
        $this->call('StatusSeeder');
        $this->call('RoleFeaturePermissionSeeder');
        $this->call('AdminUserSeeder');
        $this->call('JobSeeder');
        $this->call('JobPostSeeder');
        $this->call('ApplicantSeeder');
        $this->call('ApplicationSeeder');
    }
}