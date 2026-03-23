<?php

if (! function_exists('template_source_tables')) {
    function template_source_tables(): array
    {
        return [
            'applicants'       => 'Applicants',
            'job_applications' => 'Applications',
            'job_list'         => 'Job Posts',
            'job'              => 'Jobs',
            'users'            => 'Users',
            'contents'         => 'Contents',
            'companies'        => 'Companies',
            'departments'      => 'Departments',
        ];
    }
}

if (! function_exists('template_variable_sources')) {
    function template_variable_sources(): array
    {
        return [
            'applicants' => [
                'applicant' => 'applicants',
            ],

            'job_applications' => [
                'application' => 'job_applications',
                'applicant'   => 'applicants',
                'job'         => 'job',
            ],

            'job_list' => [
                'job' => 'job_list',
            ],

            'job' => [
                'job' => 'job',
            ],

            'users' => [
                'user' => 'users',
            ],

            'contents' => [
                'content' => 'contents',
            ],

            'companies' => [
                'company' => 'companies',
            ],

            'departments' => [
                'department' => 'departments',
            ],
        ];
    }
}

if (! function_exists('template_variables_from_table')) {
    function template_variables_from_table(?string $table): array
    {
        $table = trim((string) $table);
        if ($table === '') {
            return [];
        }

        $db = db_connect();
        $sourceMap = template_variable_sources();

        if (! isset($sourceMap[$table])) {
            return [];
        }

        $vars = [];

        foreach ($sourceMap[$table] as $prefix => $sourceTable) {
            if (! $db->tableExists($sourceTable)) {
                continue;
            }

            $fields = $db->getFieldNames($sourceTable);

            foreach ($fields as $field) {
                $vars[] = '{{' . $prefix . '.' . $field . '}}';
            }
        }

        if ($table === 'job_applications') {
            $vars[] = '{{applicant.name}}';
            $vars[] = '{{applicant.email}}';
            $vars[] = '{{job.name}}';
            $vars[] = '{{application.status}}';
            $vars[] = '{{application.created_at}}';
        }

        if ($table === 'applicants') {
            $vars[] = '{{applicant.name}}';
            $vars[] = '{{applicant.email}}';
        }

        return array_values(array_unique($vars));
    }
}