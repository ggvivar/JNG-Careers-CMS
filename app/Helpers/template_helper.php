<?php

if (! function_exists('render_template')) {
    function render_template(string $template, array $data = []): string
    {
        return preg_replace_callback('/\{\{\s*(.*?)\s*\}\}/', function ($matches) use ($data) {
            $key = trim((string) $matches[1]);
            if ($key === '') {
                return $matches[0];
            }

            $segments = explode('.', $key);
            $value = $data;

            foreach ($segments as $segment) {
                if (is_array($value) && array_key_exists($segment, $value)) {
                    $value = $value[$segment];
                } else {
                    return $matches[0];
                }
            }

            return is_scalar($value) || $value === null
                ? (string) $value
                : $matches[0];
        }, $template);
    }
}

if (! function_exists('template_data_resolver')) {
    function template_data_resolver(string $sourceTable, int $id): array
    {
        switch ($sourceTable) {
            case 'job_applications':
                return get_job_application_template_data($id);

            case 'applicants':
                return get_applicant_template_data($id);

            case 'job':
                return get_job_template_data($id);

            case 'job_list':
                return get_job_list_template_data($id);

            case 'users':
                return get_user_template_data($id);

            case 'companies':
                return get_company_template_data($id);

            case 'departments':
                return get_department_template_data($id);

            case 'contents':
                return get_content_template_data($id);

            default:
                return [];
        }
    }
}

if (! function_exists('get_job_application_template_data')) {
    function get_job_application_template_data(int $applicationId): array
    {
        $db = db_connect();

        $row = $db->table('job_applications a')
            ->select('
                a.*,
                ap.id as applicant_id,
                ap.name as applicant_name,
                ap.email as applicant_email,
                j.id as job_id,
                j.name as job_name
            ')
            ->join('applicants ap', 'ap.id = a.applicant_id', 'left')
            ->join('job j', 'j.id = a.job_id', 'left')
            ->where('a.id', $applicationId)
            ->get()
            ->getRowArray();

        if (! $row) {
            return [];
        }

        return [
            'application' => [
                'id'         => $row['id'] ?? null,
                'status'     => $row['status'] ?? null,
                'created_at' => $row['created_at'] ?? null,
                'updated_at' => $row['updated_at'] ?? null,
            ],
            'applicant' => [
                'id'    => $row['applicant_id'] ?? null,
                'name'  => $row['applicant_name'] ?? null,
                'email' => $row['applicant_email'] ?? null,
            ],
            'job' => [
                'id'   => $row['job_id'] ?? null,
                'name' => $row['job_name'] ?? null,
            ],
        ];
    }
}

if (! function_exists('get_applicant_template_data')) {
    function get_applicant_template_data(int $id): array
    {
        $db = db_connect();
        $row = $db->table('applicants')->where('id', $id)->get()->getRowArray();

        if (! $row) {
            return [];
        }

        return [
            'applicant' => $row,
        ];
    }
}

if (! function_exists('get_job_template_data')) {
    function get_job_template_data(int $id): array
    {
        $db = db_connect();
        $row = $db->table('job')->where('id', $id)->get()->getRowArray();

        if (! $row) {
            return [];
        }

        return [
            'job' => $row,
        ];
    }
}

if (! function_exists('get_job_list_template_data')) {
    function get_job_list_template_data(int $id): array
    {
        $db = db_connect();
        $row = $db->table('job_list')->where('id', $id)->get()->getRowArray();

        if (! $row) {
            return [];
        }

        return [
            'job' => $row,
        ];
    }
}

if (! function_exists('get_user_template_data')) {
    function get_user_template_data(int $id): array
    {
        $db = db_connect();
        $row = $db->table('users')->where('id', $id)->get()->getRowArray();

        if (! $row) {
            return [];
        }

        return [
            'user' => $row,
        ];
    }
}

if (! function_exists('get_company_template_data')) {
    function get_company_template_data(int $id): array
    {
        $db = db_connect();
        $row = $db->table('companies')->where('id', $id)->get()->getRowArray();

        if (! $row) {
            return [];
        }

        return [
            'company' => $row,
        ];
    }
}

if (! function_exists('get_department_template_data')) {
    function get_department_template_data(int $id): array
    {
        $db = db_connect();
        $row = $db->table('departments')->where('id', $id)->get()->getRowArray();

        if (! $row) {
            return [];
        }

        return [
            'department' => $row,
        ];
    }
}

if (! function_exists('get_content_template_data')) {
    function get_content_template_data(int $id): array
    {
        $db = db_connect();
        $row = $db->table('contents')->where('id', $id)->get()->getRowArray();

        if (! $row) {
            return [];
        }

        return [
            'content' => $row,
        ];
    }
}