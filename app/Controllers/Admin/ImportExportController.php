<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ImportExportController extends BaseController
{
    protected array $entities = [
        'users' => [
            'table' => 'users',
            'searchable' => ['username', 'email', 'name'],
            'exclude' => ['id', 'date_deleted'],
            'required' => ['username'],
        ],
        'roles' => [
            'table' => 'roles',
            'searchable' => ['name', 'description'],
            'exclude' => ['id', 'date_deleted'],
            'required' => ['name'],
        ],
        'jobs' => [
            'table' => 'job',
            'searchable' => ['name', 'code', 'description'],
            'exclude' => ['id', 'date_deleted'],
            'required' => ['name'],
        ],
        'job-posts' => [
            'table' => 'job_list',
            'searchable' => ['name', 'title', 'description'],
            'exclude' => ['id', 'date_deleted'],
            'required' => ['name'],
        ],
        'applicants' => [
            'table' => 'applicants',
            'searchable' => ['name', 'email', 'mobile'],
            'exclude' => ['id', 'date_deleted'],
            'required' => ['name'],
        ],
        'applications' => [
            'table' => 'job_applications',
            'searchable' => ['status'],
            'exclude' => ['id', 'date_deleted'],
            'required' => [],
        ],
        'contents' => [
            'table' => 'contents',
            'searchable' => ['name', 'slug', 'description', 'body'],
            'exclude' => ['id', 'date_deleted'],
            'required' => ['name'],
        ],
        'message-templates' => [
            'table' => 'message_templates',
            'searchable' => ['name', 'template_key', 'channel', 'source_table', 'subject'],
            'exclude' => ['id', 'date_deleted'],
            'required' => ['name', 'template_key'],
        ],
        'document-templates' => [
            'table' => 'document_templates',
            'searchable' => ['name', 'template_key', 'template_type', 'source_table', 'file_name_pattern', 'description'],
            'exclude' => ['id', 'date_deleted'],
            'required' => ['name', 'template_key'],
        ],
    ];

    public function columns($entity)
    {
        $config = $this->resolveEntity($entity);
        if ($config === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Invalid export entity.',
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'columns' => $this->getExportableColumns($config),
            'required' => $config['required'] ?? [],
        ]);
    }

    public function exportPreview($entity)
    {
        $config = $this->resolveEntity($entity);
        if ($config === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Invalid export entity.',
            ]);
        }

        $db = db_connect();
        $builder = $db->table($config['table'])->where('date_deleted', null);

        $q = trim((string) ($this->request->getGet('q') ?? ''));
        if ($q !== '' && ! empty($config['searchable'])) {
            $builder->groupStart();
            foreach ($config['searchable'] as $index => $column) {
                if ($index === 0) {
                    $builder->like($column, $q);
                } else {
                    $builder->orLike($column, $q);
                }
            }
            $builder->groupEnd();
        }

        $defaultColumns = $this->getExportableColumns($config);

        $requestedColumns = $this->request->getGet('columns');
        $requestedColumns = is_array($requestedColumns) ? array_values(array_filter($requestedColumns)) : [];

        $columns = ! empty($requestedColumns)
            ? array_values(array_intersect($defaultColumns, $requestedColumns))
            : $defaultColumns;

        if (empty($columns)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'Please select at least one column.',
            ]);
        }

        $rows = $builder->limit(10)->get()->getResultArray();
        $previewRows = [];

        foreach ($rows as $row) {
            $previewRow = [];
            foreach ($columns as $column) {
                $previewRow[$column] = $row[$column] ?? null;
            }
            $previewRows[] = $previewRow;
        }

        return $this->response->setJSON([
            'status' => true,
            'columns' => $columns,
            'rows' => $previewRows,
            'count' => count($previewRows),
        ]);
    }

    public function export($entity)
    {
        $config = $this->resolveEntity($entity);
        if ($config === null) {
            return redirect()->back()->with('error', 'Invalid export entity.');
        }

        $format = strtolower((string) ($this->request->getGet('format') ?? 'csv'));
        if (! in_array($format, ['csv', 'xlsx'], true)) {
            $format = 'csv';
        }

        $db = db_connect();
        $builder = $db->table($config['table'])->where('date_deleted', null);

        $q = trim((string) ($this->request->getGet('q') ?? ''));
        if ($q !== '' && ! empty($config['searchable'])) {
            $builder->groupStart();
            foreach ($config['searchable'] as $index => $column) {
                if ($index === 0) {
                    $builder->like($column, $q);
                } else {
                    $builder->orLike($column, $q);
                }
            }
            $builder->groupEnd();
        }

        $rows = $builder->get()->getResultArray();

        $defaultColumns = $this->getExportableColumns($config);

        $requestedColumns = $this->request->getGet('columns');
        $requestedColumns = is_array($requestedColumns) ? array_values(array_filter($requestedColumns)) : [];

        $columns = ! empty($requestedColumns)
            ? array_values(array_intersect($defaultColumns, $requestedColumns))
            : $defaultColumns;

        if (empty($columns)) {
            return redirect()->back()->with('error', 'Please select at least one column to export.');
        }

        if ($format === 'xlsx') {
            return $this->exportXlsx($entity, $columns, $rows);
        }

        return $this->exportCsv($entity, $columns, $rows);
    }

    public function previewImport($entity)
    {
        $config = $this->resolveEntity($entity);
        if ($config === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Invalid import entity.',
            ]);
        }

        $file = $this->request->getFile('import_file');
        $format = strtolower((string) $this->request->getPost('format'));

        if (! $file || ! $file->isValid()) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'Please upload a valid file.',
            ]);
        }

        $ext = strtolower((string) $file->getExtension());

        if (! in_array($ext, ['csv', 'xlsx'], true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'Only CSV and XLSX files are allowed.',
            ]);
        }

        if ($format !== $ext) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'Selected format does not match uploaded file.',
            ]);
        }

        try {
            $rows = $format === 'xlsx'
                ? $this->readXlsxRows($file->getTempName())
                : $this->readCsvRows($file->getTempName());
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Failed to read import file: ' . $e->getMessage(),
            ]);
        }

        $validation = $this->validateImportRows($config, $rows);

        return $this->response->setJSON([
            'status' => true,
            'headers' => $validation['headers'],
            'valid_headers' => $validation['valid_headers'],
            'invalid_headers' => $validation['invalid_headers'],
            'missing_required_headers' => $validation['missing_required_headers'],
            'row_errors' => $validation['row_errors'],
            'preview_rows' => array_slice($validation['normalized_rows'], 0, 10),
            'total_rows' => count($validation['normalized_rows']),
            'can_import' => empty($validation['invalid_headers']) && empty($validation['missing_required_headers']),
        ]);
    }

    public function import($entity)
    {
        $config = $this->resolveEntity($entity);
        if ($config === null) {
            return redirect()->back()->with('error', 'Invalid import entity.');
        }

        $file = $this->request->getFile('import_file');
        $format = strtolower((string) $this->request->getPost('format'));

        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'Please upload a valid file.');
        }

        $ext = strtolower((string) $file->getExtension());

        if (! in_array($ext, ['csv', 'xlsx'], true)) {
            return redirect()->back()->with('error', 'Only CSV and XLSX files are allowed.');
        }

        if ($format !== $ext) {
            return redirect()->back()->with('error', 'Selected format does not match uploaded file.');
        }

        try {
            $rows = $format === 'xlsx'
                ? $this->readXlsxRows($file->getTempName())
                : $this->readCsvRows($file->getTempName());
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to read import file: ' . $e->getMessage());
        }

        if (empty($rows)) {
            return redirect()->back()->with('error', 'The uploaded file is empty.');
        }

        $validation = $this->validateImportRows($config, $rows);

        if (! empty($validation['invalid_headers'])) {
            return redirect()->back()->with(
                'error',
                'Invalid column(s): ' . implode(', ', $validation['invalid_headers'])
            );
        }

        if (! empty($validation['missing_required_headers'])) {
            return redirect()->back()->with(
                'error',
                'Missing required column(s): ' . implode(', ', $validation['missing_required_headers'])
            );
        }

        $insertRows = [];
        foreach ($validation['normalized_rows'] as $rowIndex => $row) {
            $hasError = false;

            foreach ($validation['row_errors'] as $error) {
                if ((int) $error['row'] === ($rowIndex + 2)) {
                    $hasError = true;
                    break;
                }
            }

            if (! $hasError) {
                $insertRows[] = $row;
            }
        }

        if (empty($insertRows)) {
            return redirect()->back()->with('error', 'No valid rows found to import.');
        }

        $db = db_connect();

        try {
            $db->table($config['table'])->insertBatch($insertRows);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        $skipped = count($validation['normalized_rows']) - count($insertRows);
        $message = count($insertRows) . ' row(s) imported successfully.';

        if ($skipped > 0) {
            $message .= ' ' . $skipped . ' row(s) skipped due to validation errors.';
        }

        return redirect()->back()->with('success', $message);
    }

    protected function resolveEntity(string $entity): ?array
    {
        return $this->entities[$entity] ?? null;
    }

    protected function getExportableColumns(array $config): array
    {
        $db = db_connect();
        $fields = $db->getFieldNames($config['table']);

        return array_values(array_filter($fields, function ($field) use ($config) {
            return ! in_array($field, $config['exclude'] ?? [], true);
        }));
    }

    protected function validateImportRows(array $config, array $rows): array
    {
        $db = db_connect();
        $allowedFields = $this->getExportableColumns($config);
        $requiredFields = $config['required'] ?? [];

        $headers = [];
        if (! empty($rows[0])) {
            $headers = array_keys($rows[0]);
        }

        $validHeaders = array_values(array_intersect($headers, $allowedFields));
        $invalidHeaders = array_values(array_diff($headers, $allowedFields));
        $missingRequiredHeaders = array_values(array_diff($requiredFields, $headers));

        $normalizedRows = [];
        $rowErrors = [];

        foreach ($rows as $index => $row) {
            $normalized = [];

            foreach ($allowedFields as $field) {
                $normalized[$field] = array_key_exists($field, $row) ? $row[$field] : null;
            }

            if (in_array('date_created', $allowedFields, true) && empty($normalized['date_created'])) {
                $normalized['date_created'] = date('Y-m-d H:i:s');
            }

            foreach ($requiredFields as $requiredField) {
                if (! array_key_exists($requiredField, $row) || trim((string) $row[$requiredField]) === '') {
                    $rowErrors[] = [
                        'row' => $index + 2,
                        'field' => $requiredField,
                        'message' => 'Required field is empty.',
                    ];
                }
            }

            $normalizedRows[] = $normalized;
        }

        return [
            'headers' => $headers,
            'valid_headers' => $validHeaders,
            'invalid_headers' => $invalidHeaders,
            'missing_required_headers' => $missingRequiredHeaders,
            'normalized_rows' => $normalizedRows,
            'row_errors' => $rowErrors,
        ];
    }

    protected function exportCsv(string $entity, array $columns, array $rows)
    {
        $filename = $entity . '-' . date('Ymd-His') . '.csv';

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $columns);

        foreach ($rows as $row) {
            $line = [];
            foreach ($columns as $column) {
                $line[] = $row[$column] ?? '';
            }
            fputcsv($handle, $line);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }

    protected function exportXlsx(string $entity, array $columns, array $rows)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $colIndex = 1;
        foreach ($columns as $column) {
            $sheet->setCellValueByColumnAndRow($colIndex, 1, $column);
            $colIndex++;
        }

        $rowIndex = 2;
        foreach ($rows as $row) {
            $colIndex = 1;
            foreach ($columns as $column) {
                $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $row[$column] ?? '');
                $colIndex++;
            }
            $rowIndex++;
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        @unlink($tempFile);

        $filename = $entity . '-' . date('Ymd-His') . '.xlsx';

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }

    protected function readCsvRows(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');

        if (! $handle) {
            throw new \RuntimeException('Unable to open CSV file.');
        }

        $headers = fgetcsv($handle);
        if (! $headers) {
            fclose($handle);
            return [];
        }

        $headers = array_map(fn($header) => trim((string) $header), $headers);

        while (($data = fgetcsv($handle)) !== false) {
            if ($this->isEmptyRow($data)) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $header) {
                if ($header === '') {
                    continue;
                }
                $row[$header] = $data[$index] ?? null;
            }
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    protected function readXlsxRows(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, false);

        if (empty($data)) {
            return [];
        }

        $headers = array_map(fn($header) => trim((string) $header), $data[0]);

        $rows = [];
        for ($i = 1, $count = count($data); $i < $count; $i++) {
            $line = $data[$i];

            if ($this->isEmptyRow($line)) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $header) {
                if ($header === '') {
                    continue;
                }
                $row[$header] = $line[$index] ?? null;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    protected function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}