<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DefaultSeeder extends Seeder
{
    public function run()
    {
        // $this->call('ModuleSeeder');
        $this->call('RoleSeeder');//check
        $this->call('CompanySeeder');//check
        $this->call('DepartmentSeeder');//check
        $this->call('ModuleSeeder');//check
        $this->call('FeatureSeeder');//check
        $this->call('StatusSeeder');//check
        $this->call('RoleFeaturePermissionSeeder');//check
        $this->call('AdminUserSeeder');//check
        $this->call('JobSeeder');//check
        $this->call('JobPostSeeder');//check
        $this->call('ApplicantSeeder');//check
        $this->call('ApplicantEducationSeeder');//check
        $this->call('ApplicantJobHistorySeeder');//check
        $this->call('ApplicationSeeder');//check
        $this->call('CommonDefaultSeeder');//check
        $this->call('WorkflowTransitionSeeder');//check

    }
}