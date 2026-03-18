<?php

if (! function_exists('template_source_tables')) {
    function template_source_tables(): array
    {
        return [
            'applicants' => 'Applicants',
            'job_applications' => 'Applications',
            'job_list' => 'Job Posts',
            'job' => 'Jobs',
            'users' => 'Users',
            'contents' => 'Contents',
            'companies' => 'Companies',
            'departments' => 'Departments',
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

        if (! $db->tableExists($table)) {
            return [];
        }

        $fields = $db->getFieldNames($table);
        $vars = [];

        foreach ($fields as $field) {
            $vars[] = '{{' . $field . '}}';
        }

        if ($table === 'applicants') {
            $vars[] = '{{name}}';
        }

        if ($table === 'job_applications') {
            $vars[] = '{{job_title}}';
            $vars[] = '{{status}}';
            $vars[] = '{{applied_at}}';
        }

        return array_values(array_unique($vars));
    }
}