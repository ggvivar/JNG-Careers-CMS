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
        'companies' => [
            'table' => 'companies',
            'searchable' => ['name', 'code', 'address', 'contact_no', 'email'],
            'exclude' => ['id', 'date_deleted'],
            'required' => ['name'],
        ],
        'units' => [
            'table' => 'units',
            'searchable' => ['name', 'code'],
            'exclude' => ['id', 'date_deleted'],
            'required' => ['company_id', 'name', 'code'],
        ],
        'groups' => [
            'table' => 'groups',
            'searchable' => ['name', 'code'],
            'exclude' => ['id', 'date_deleted'],
            'required' => ['unit_id', 'name', 'code'],
        ],
        'jobs' => [
            'table' => 'job',
            'searchable' => ['name', 'job_code', 'description', 'requirement'],
            'exclude' => ['id', 'date_deleted'],
            'required' => ['name', 'job_code'],
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

    public function template($entity)
    {
        if ($entity !== 'companies') {
            return redirect()->back()->with('error', 'Template not found.');
        }

        $path = WRITEPATH . 'templates/company_template.xlsx';

        if (!is_file($path)) {
            return redirect()->back()->with('error', 'Company template file not found.');
        }

        return $this->response
            ->download($path, null)
            ->setFileName('company_template.xlsx');
    }

    public function columns($entity)
    {
        $config = $this->resolveEntity($entity);
        if ($config === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Invalid export entity.',
            ]);
        }

        $columns = $this->getExportableColumns($config);

        // Friendly suggestions for import/export UI
        if ($entity === 'units') {
            $columns = array_values(array_unique(array_merge(
                ['company_code', 'status'],
                $columns
            )));
        }

        if ($entity === 'groups') {
            $columns = array_values(array_unique(array_merge(
                ['unit_code', 'status'],
                $columns
            )));
        }

        if ($entity === 'jobs') {
            $columns = array_values(array_unique(array_merge(
                ['company_code', 'unit_code', 'group_code', 'status'],
                $columns
            )));
        }

        return $this->response->setJSON([
            'status' => true,
            'columns' => $columns,
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
        if ($q !== '' && !empty($config['searchable'])) {
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

        $rows = $builder->limit(10)->get()->getResultArray();
        $rows = $this->transformRowsForFriendlyExport($entity, $rows);

        $defaultColumns = array_keys($rows[0] ?? []);

        if (empty($defaultColumns)) {
            $defaultColumns = $this->getExportableColumns($config);
            $defaultColumns = $this->expandFriendlyColumns($entity, $defaultColumns);
        }

        $requestedColumns = $this->request->getGet('columns');
        $requestedColumns = is_array($requestedColumns) ? array_values(array_filter($requestedColumns)) : [];

        $columns = !empty($requestedColumns)
            ? array_values(array_intersect($defaultColumns, $requestedColumns))
            : $defaultColumns;

        if (empty($columns)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'Please select at least one column.',
            ]);
        }

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
        if (!in_array($format, ['csv', 'xlsx'], true)) {
            $format = 'csv';
        }

        $db = db_connect();
        $builder = $db->table($config['table'])->where('date_deleted', null);

        $q = trim((string) ($this->request->getGet('q') ?? ''));
        if ($q !== '' && !empty($config['searchable'])) {
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
        $rows = $this->transformRowsForFriendlyExport($entity, $rows);

        $defaultColumns = array_keys($rows[0] ?? []);
        if (empty($defaultColumns)) {
            $defaultColumns = $this->expandFriendlyColumns($entity, $this->getExportableColumns($config));
        }

        $requestedColumns = $this->request->getGet('columns');
        $requestedColumns = is_array($requestedColumns) ? array_values(array_filter($requestedColumns)) : [];

        $columns = !empty($requestedColumns)
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

        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'Please upload a valid file.',
            ]);
        }

        $ext = strtolower((string) $file->getExtension());

        if (!in_array($ext, ['csv', 'xlsx'], true)) {
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

        if ($entity === 'companies' && $ext === 'xlsx') {
            try {
                $sheets = $this->previewCompanyTemplateXlsx($file->getTempName());

                return $this->response->setJSON([
                    'status' => true,
                    'headers' => $sheets['headers'],
                    'valid_headers' => $sheets['headers'],
                    'invalid_headers' => [],
                    'missing_required_headers' => [],
                    'row_errors' => [],
                    'preview_rows' => $sheets['preview_rows'],
                    'total_rows' => $sheets['total_rows'],
                    'can_import' => true,
                    'template_mode' => true,
                ]);
            } catch (\Throwable $e) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => false,
                    'message' => 'Failed to preview company template: ' . $e->getMessage(),
                ]);
            }
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

        $mapping = $this->request->getPost('mapping') ?? [];
        if (!empty($mapping) && is_array($mapping)) {
            $rows = $this->applyColumnMapping($rows, $mapping);
        }

        $rows = $this->normalizeImportRowsByEntity($entity, $rows);
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
        $mapping = $this->request->getPost('mapping') ?? [];

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Please upload a valid file.');
        }

        $ext = strtolower((string) $file->getExtension());

        if (!in_array($ext, ['csv', 'xlsx'], true)) {
            return redirect()->back()->with('error', 'Only CSV and XLSX files are allowed.');
        }

        if ($format !== $ext) {
            return redirect()->back()->with('error', 'Selected format does not match uploaded file.');
        }

        if ($entity === 'companies' && $ext === 'xlsx') {
            try {
                return $this->importCompanyTemplateXlsx($file->getTempName());
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Company template import failed: ' . $e->getMessage());
            }
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

        if (!empty($mapping) && is_array($mapping)) {
            $rows = $this->applyColumnMapping($rows, $mapping);
        }

        $rows = $this->normalizeImportRowsByEntity($entity, $rows);
        $validation = $this->validateImportRows($config, $rows);

        if (!empty($validation['invalid_headers'])) {
            return redirect()->back()->with(
                'error',
                'Invalid column(s): ' . implode(', ', $validation['invalid_headers'])
            );
        }

        if (!empty($validation['missing_required_headers'])) {
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

            if (!$hasError) {
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

    protected function importCompanyTemplateXlsx(string $path)
    {
        $spreadsheet = IOFactory::load($path);

        $companyRows = $this->readNamedSheetRows($spreadsheet, 'Company');
        $unitRows    = $this->readNamedSheetRows($spreadsheet, 'Unit');
        $groupRows   = $this->readNamedSheetRows($spreadsheet, 'Group');

        if (empty($companyRows)) {
            return redirect()->back()->with('error', 'Company sheet is empty.');
        }

        $db = db_connect();
        $db->transStart();

        $statusMap = $this->getStatusMapByName();
        $now = date('Y-m-d H:i:s');

        // Companies
        foreach ($companyRows as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            $code = trim((string) ($row['code'] ?? ''));

            if ($name === '') {
                continue;
            }

            $existing = null;
            if ($code !== '') {
                $existing = $db->table('companies')
                    ->where('code', $code)
                    ->where('date_deleted', null)
                    ->get()
                    ->getRowArray();
            }

            $data = [
                'name'         => $name,
                'code'         => $code !== '' ? $code : null,
                'contact_no'   => trim((string) ($row['contact'] ?? '')) ?: null,
                'email'        => trim((string) ($row['email'] ?? '')) ?: null,
                'address'      => trim((string) ($row['address'] ?? '')) ?: null,
                'date_updated' => $now,
            ];

            if (isset($row['status']) || isset($row['status_name'])) {
                $statusValue = $row['status'] ?? $row['status_name'] ?? '';
                $data['status_id'] = $this->resolveStatusValue((string) $statusValue, $statusMap);
            }

            if ($existing) {
                $db->table('companies')->where('id', $existing['id'])->update($data);
            } else {
                $data['date_created'] = $now;
                $db->table('companies')->insert($data);
            }
        }

        $companyCodeToId = $this->getCodeIdMap('companies');

        // Units
        foreach ($unitRows as $row) {
            $companyCode = trim((string) ($row['company_code'] ?? ''));
            $name        = trim((string) ($row['name'] ?? ''));
            $code        = trim((string) ($row['code'] ?? ''));
            $statusValue = trim((string) ($row['status'] ?? $row['status_name'] ?? ''));

            if ($companyCode === '' || $name === '' || $code === '') {
                continue;
            }

            $companyId = $companyCodeToId[strtolower($companyCode)] ?? null;
            if (!$companyId) {
                throw new \RuntimeException("Unknown company_code: {$companyCode}");
            }

            $existing = $db->table('units')
                ->where('code', $code)
                ->where('date_deleted', null)
                ->get()
                ->getRowArray();

            $data = [
                'company_id'   => $companyId,
                'name'         => $name,
                'code'         => $code,
                'status_id'    => $this->resolveStatusValue($statusValue, $statusMap),
                'date_updated' => $now,
            ];

            if ($existing) {
                $db->table('units')->where('id', $existing['id'])->update($data);
            } else {
                $data['date_created'] = $now;
                $db->table('units')->insert($data);
            }
        }

        $unitCodeToId = $this->getCodeIdMap('units');

        // Groups
        foreach ($groupRows as $row) {
            $unitCode    = trim((string) ($row['unit_code'] ?? ''));
            $name        = trim((string) ($row['name'] ?? ''));
            $code        = trim((string) ($row['code'] ?? ''));
            $statusValue = trim((string) ($row['status'] ?? $row['status_name'] ?? ''));

            if ($unitCode === '' || $name === '' || $code === '') {
                continue;
            }

            $unitId = $unitCodeToId[strtolower($unitCode)] ?? null;
            if (!$unitId) {
                throw new \RuntimeException("Unknown unit_code: {$unitCode}");
            }

            $existing = $db->table('groups')
                ->where('code', $code)
                ->where('date_deleted', null)
                ->get()
                ->getRowArray();

            $data = [
                'unit_id'      => $unitId,
                'name'         => $name,
                'code'         => $code,
                'status_id'    => $this->resolveStatusValue($statusValue, $statusMap),
                'date_updated' => $now,
            ];

            if ($existing) {
                $db->table('groups')->where('id', $existing['id'])->update($data);
            } else {
                $data['date_created'] = $now;
                $db->table('groups')->insert($data);
            }
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Company template import failed.');
        }

        return redirect()->back()->with('success', 'Company template imported successfully.');
    }

    protected function previewCompanyTemplateXlsx(string $path): array
    {
        $spreadsheet = IOFactory::load($path);

        $companyRows = $this->readNamedSheetRows($spreadsheet, 'Company');
        $unitRows    = $this->readNamedSheetRows($spreadsheet, 'Unit');
        $groupRows   = $this->readNamedSheetRows($spreadsheet, 'Group');

        $previewRows = [];
        foreach (array_slice($companyRows, 0, 3) as $row) {
            $row['_sheet'] = 'Company';
            $previewRows[] = $row;
        }
        foreach (array_slice($unitRows, 0, 3) as $row) {
            $row['_sheet'] = 'Unit';
            $previewRows[] = $row;
        }
        foreach (array_slice($groupRows, 0, 3) as $row) {
            $row['_sheet'] = 'Group';
            $previewRows[] = $row;
        }

        $headers = ['_sheet', 'id', 'company_code', 'unit_code', 'name', 'code', 'contact', 'email', 'address', 'status'];

        return [
            'headers' => $headers,
            'preview_rows' => $previewRows,
            'total_rows' => count($companyRows) + count($unitRows) + count($groupRows),
        ];
    }

    protected function applyColumnMapping(array $rows, array $mapping): array
    {
        $mappedRows = [];

        foreach ($rows as $row) {
            $mapped = [];

            foreach ($mapping as $sourceHeader => $targetField) {
                $sourceHeader = (string) $sourceHeader;
                $targetField = trim((string) $targetField);

                if ($targetField === '') {
                    continue;
                }

                $mapped[$targetField] = $row[$sourceHeader] ?? null;
            }

            $mappedRows[] = $mapped;
        }

        return $mappedRows;
    }

    protected function normalizeImportRowsByEntity(string $entity, array $rows): array
    {
        return match ($entity) {
            'units' => $this->normalizeUnitImportRows($rows),
            'groups' => $this->normalizeGroupImportRows($rows),
            'jobs' => $this->normalizeJobImportRows($rows),
            'companies' => $this->normalizeCompanyImportRows($rows),
            default => $rows,
        };
    }

    protected function normalizeCompanyImportRows(array $rows): array
    {
        $statusMap = $this->getStatusMapByName();

        foreach ($rows as &$row) {
            if (isset($row['contact']) && !isset($row['contact_no'])) {
                $row['contact_no'] = $row['contact'];
            }

            if ((isset($row['status']) || isset($row['status_name'])) && !isset($row['status_id'])) {
                $statusValue = $row['status'] ?? $row['status_name'] ?? '';
                $row['status_id'] = $this->resolveStatusValue((string) $statusValue, $statusMap);
            }

            unset($row['id'], $row['contact'], $row['status'], $row['status_name']);
        }

        return $rows;
    }

    protected function normalizeUnitImportRows(array $rows): array
    {
        $companyMap = $this->getCodeIdMap('companies');
        $statusMap = $this->getStatusMapByName();

        foreach ($rows as &$row) {
            if (isset($row['company_code']) && !isset($row['company_id'])) {
                $companyCode = strtolower(trim((string) $row['company_code']));
                $row['company_id'] = $companyMap[$companyCode] ?? null;
            }

            if ((isset($row['status']) || isset($row['status_name'])) && !isset($row['status_id'])) {
                $statusValue = $row['status'] ?? $row['status_name'] ?? '';
                $row['status_id'] = $this->resolveStatusValue((string) $statusValue, $statusMap);
            }

            unset($row['id'], $row['company_code'], $row['status'], $row['status_name']);
        }

        return $rows;
    }

    protected function normalizeGroupImportRows(array $rows): array
    {
        $unitMap = $this->getCodeIdMap('units');
        $statusMap = $this->getStatusMapByName();

        foreach ($rows as &$row) {
            if (isset($row['unit_code']) && !isset($row['unit_id'])) {
                $unitCode = strtolower(trim((string) $row['unit_code']));
                $row['unit_id'] = $unitMap[$unitCode] ?? null;
            }

            if ((isset($row['status']) || isset($row['status_name'])) && !isset($row['status_id'])) {
                $statusValue = $row['status'] ?? $row['status_name'] ?? '';
                $row['status_id'] = $this->resolveStatusValue((string) $statusValue, $statusMap);
            }

            unset($row['id'], $row['unit_code'], $row['status'], $row['status_name']);
        }

        return $rows;
    }

    protected function normalizeJobImportRows(array $rows): array
    {
        $companyMap = $this->getCodeIdMap('companies');
        $unitMap    = $this->getCodeIdMap('units');
        $groupMap   = $this->getCodeIdMap('groups');
        $statusMap  = $this->getStatusMapByName();

        foreach ($rows as &$row) {
            if (isset($row['company_code']) && !isset($row['company_id'])) {
                $companyCode = strtolower(trim((string) $row['company_code']));
                $row['company_id'] = $companyMap[$companyCode] ?? null;
            }

            if (isset($row['unit_code']) && !isset($row['unit_id'])) {
                $unitCode = strtolower(trim((string) $row['unit_code']));
                $row['unit_id'] = $unitMap[$unitCode] ?? null;
            }

            if (isset($row['group_code']) && !isset($row['group_id'])) {
                $groupCode = strtolower(trim((string) $row['group_code']));
                $row['group_id'] = $groupMap[$groupCode] ?? null;
            }

            if ((isset($row['status']) || isset($row['status_name'])) && !isset($row['status_id'])) {
                $statusValue = $row['status'] ?? $row['status_name'] ?? '';
                $row['status_id'] = $this->resolveStatusValue((string) $statusValue, $statusMap);
            }

            unset(
                $row['id'],
                $row['company_code'],
                $row['unit_code'],
                $row['group_code'],
                $row['status'],
                $row['status_name']
            );
        }

        return $rows;
    }

    protected function transformRowsForFriendlyExport(string $entity, array $rows): array
    {
        if (empty($rows)) {
            return $rows;
        }

        $db = db_connect();
        $statusNameMap = $this->getIdNameMap('status');

        if ($entity === 'companies') {
            foreach ($rows as &$row) {
                $row['status'] = !empty($row['status_id']) ? ($statusNameMap[(int) $row['status_id']] ?? '') : '';
                unset($row['status_id']);
            }
        }

        if ($entity === 'units') {
            $companyCodeMap = $this->getIdCodeMap('companies');

            foreach ($rows as &$row) {
                $row['company_code'] = !empty($row['company_id']) ? ($companyCodeMap[(int) $row['company_id']] ?? '') : '';
                $row['status'] = !empty($row['status_id']) ? ($statusNameMap[(int) $row['status_id']] ?? '') : '';
                unset($row['company_id'], $row['status_id']);
            }
        }

        if ($entity === 'groups') {
            $unitCodeMap = $this->getIdCodeMap('units');

            foreach ($rows as &$row) {
                $row['unit_code'] = !empty($row['unit_id']) ? ($unitCodeMap[(int) $row['unit_id']] ?? '') : '';
                $row['status'] = !empty($row['status_id']) ? ($statusNameMap[(int) $row['status_id']] ?? '') : '';
                unset($row['unit_id'], $row['status_id']);
            }
        }

        if ($entity === 'jobs') {
            $companyCodeMap = $this->getIdCodeMap('companies');
            $unitCodeMap    = $this->getIdCodeMap('units');
            $groupCodeMap   = $this->getIdCodeMap('groups');

            foreach ($rows as &$row) {
                $row['company_code'] = !empty($row['company_id']) ? ($companyCodeMap[(int) $row['company_id']] ?? '') : '';
                $row['unit_code'] = !empty($row['unit_id']) ? ($unitCodeMap[(int) $row['unit_id']] ?? '') : '';
                $row['group_code'] = !empty($row['group_id']) ? ($groupCodeMap[(int) $row['group_id']] ?? '') : '';
                $row['status'] = !empty($row['status_id']) ? ($statusNameMap[(int) $row['status_id']] ?? '') : '';
                unset($row['company_id'], $row['unit_id'], $row['group_id'], $row['status_id']);
            }
        }

        return $rows;
    }

    protected function expandFriendlyColumns(string $entity, array $columns): array
    {
        if ($entity === 'companies') {
            $columns = array_diff($columns, ['status_id']);
            $columns[] = 'status';
        }

        if ($entity === 'units') {
            $columns = array_diff($columns, ['company_id', 'status_id']);
            $columns[] = 'company_code';
            $columns[] = 'status';
        }

        if ($entity === 'groups') {
            $columns = array_diff($columns, ['unit_id', 'status_id']);
            $columns[] = 'unit_code';
            $columns[] = 'status';
        }

        if ($entity === 'jobs') {
            $columns = array_diff($columns, ['company_id', 'unit_id', 'group_id', 'status_id']);
            $columns[] = 'company_code';
            $columns[] = 'unit_code';
            $columns[] = 'group_code';
            $columns[] = 'status';
        }

        return array_values(array_unique($columns));
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
            return !in_array($field, $config['exclude'] ?? [], true);
        }));
    }

    protected function validateImportRows(array $config, array $rows): array
    {
        $allowedFields = $this->getExportableColumns($config);
        $requiredFields = $config['required'] ?? [];

        $headers = [];
        if (!empty($rows[0])) {
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
                if (!array_key_exists($requiredField, $row) || trim((string) $row[$requiredField]) === '') {
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

        if (!$handle) {
            throw new \RuntimeException('Unable to open CSV file.');
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
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

    protected function readNamedSheetRows(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet, string $sheetName): array
    {
        $sheet = $spreadsheet->getSheetByName($sheetName);

        if (!$sheet) {
            return [];
        }

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

    protected function getStatusMapByName(): array
    {
        $rows = db_connect()->table('status')
            ->select('id, name')
            ->where('date_deleted', null)
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $map[strtolower(trim((string) $row['name']))] = (int) $row['id'];
        }

        return $map;
    }

    protected function resolveStatusValue(?string $value, array $statusMap): ?int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $key = strtolower($value);
        return $statusMap[$key] ?? null;
    }

    protected function getCodeIdMap(string $table): array
    {
        $rows = db_connect()->table($table)
            ->select('id, code')
            ->where('date_deleted', null)
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            if (!empty($row['code'])) {
                $map[strtolower(trim((string) $row['code']))] = (int) $row['id'];
            }
        }

        return $map;
    }

    protected function getIdCodeMap(string $table): array
    {
        $rows = db_connect()->table($table)
            ->select('id, code')
            ->where('date_deleted', null)
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['id']] = (string) ($row['code'] ?? '');
        }

        return $map;
    }

    protected function getIdNameMap(string $table): array
    {
        $rows = db_connect()->table($table)
            ->select('id, name')
            ->where('date_deleted', null)
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['id']] = (string) ($row['name'] ?? '');
        }

        return $map;
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