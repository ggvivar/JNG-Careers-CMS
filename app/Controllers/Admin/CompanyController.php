<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CompanyModel;
use App\Models\UnitModel;
use App\Models\GroupModel;

class CompanyController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('companies c')
            ->select('
                c.*,
                s.name as status_name,
                COUNT(DISTINCT u.id) as unit_count,
                COUNT(DISTINCT g.id) as group_count
            ')
            ->join('status s', 's.id = c.status_id', 'left')
            ->join('units u', 'u.company_id = c.id AND u.date_deleted IS NULL', 'left')
            ->join('groups g', 'g.unit_id = u.id AND g.date_deleted IS NULL', 'left')
            ->where('c.date_deleted', null)
            ->groupBy('c.id');

        if ($q !== '') {
            $builder->groupStart()
                ->like('c.name', $q)
                ->orLike('c.code', $q)
                ->orLike('c.address', $q)
                ->orLike('c.contact_no', $q)
                ->orLike('c.email', $q)
                ->orLike('s.name', $q)
                ->groupEnd();
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = count($countBuilder->get()->getResultArray());

        $companies = $builder
            ->orderBy('c.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('admin/companies/index', [
            'companies' => $companies,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper('dropdown');
        $statusOptions = dd_statuses_by_feature('companies');

        if (strtolower($this->request->getMethod()) === 'post') {
            $companyModel = new CompanyModel();

            $companyId = $companyModel->insert([
                'name' => trim((string) $this->request->getPost('name')),
                'code' => trim((string) $this->request->getPost('code')) ?: null,
                'address' => trim((string) $this->request->getPost('address')) ?: null,
                'contact_no' => trim((string) $this->request->getPost('contact_no')) ?: null,
                'email' => trim((string) $this->request->getPost('email')) ?: null,
                'status_id' => $this->request->getPost('status_id') ?: null,
                'date_created' => date('Y-m-d H:i:s'),
            ], true);

            $this->saveUnitsAndGroups((int) $companyId, $this->request->getPost('units') ?? []);

            return redirect()->to('/admin/companies')->with('success', 'Company created.');
        }

        return view('admin/companies/form', [
            'mode' => 'create',
            'company' => null,
            'statusOptions' => $statusOptions,
            'unitsWithGroups' => [],
        ]);
    }

    public function edit($id)
    {
        helper('dropdown');
        $statusOptions = dd_statuses_by_feature('companies');

        $model = new CompanyModel();
        $company = $model->where('date_deleted', null)->find((int) $id);

        if (! $company) {
            return redirect()->to('/admin/companies')->with('error', 'Company not found.');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $model->update((int) $id, [
                'name' => trim((string) $this->request->getPost('name')),
                'code' => trim((string) $this->request->getPost('code')) ?: null,
                'address' => trim((string) $this->request->getPost('address')) ?: null,
                'contact_no' => trim((string) $this->request->getPost('contact_no')) ?: null,
                'email' => trim((string) $this->request->getPost('email')) ?: null,
                'status_id' => $this->request->getPost('status_id') ?: null,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

            $this->saveUnitsAndGroups((int) $id, $this->request->getPost('units') ?? []);

            return redirect()->to('/admin/companies')->with('success', 'Company updated.');
        }

        return view('admin/companies/form', [
            'mode' => 'edit',
            'company' => $company,
            'statusOptions' => $statusOptions,
            'unitsWithGroups' => $this->getUnitsWithGroups((int) $id),
        ]);
    }

    public function delete($id)
    {
        (new CompanyModel())->update((int) $id, [
            'date_deleted' => date('Y-m-d H:i:s'),
        ]);

        (new UnitModel())
            ->where('company_id', (int) $id)
            ->set(['date_deleted' => date('Y-m-d H:i:s')])
            ->update();

        return redirect()->to('/admin/companies')->with('success', 'Company deleted.');
    }

    private function getUnitsWithGroups(int $companyId): array
    {
        $unitModel = new UnitModel();
        $groupModel = new GroupModel();

        $units = $unitModel
            ->where('company_id', $companyId)
            ->where('date_deleted', null)
            ->orderBy('id', 'ASC')
            ->findAll();

        foreach ($units as &$unit) {
            $unit['groups'] = $groupModel
                ->where('unit_id', $unit['id'])
                ->where('date_deleted', null)
                ->orderBy('id', 'ASC')
                ->findAll();
        }

        return $units;
    }

    private function saveUnitsAndGroups(int $companyId, array $submittedUnits): void
    {
        $unitModel = new UnitModel();
        $groupModel = new GroupModel();
        $now = date('Y-m-d H:i:s');

        $existingUnits = $unitModel
            ->where('company_id', $companyId)
            ->where('date_deleted', null)
            ->findAll();

        $existingUnitIds = array_map(fn($row) => (int) $row['id'], $existingUnits);
        $keptUnitIds = [];

        foreach ($submittedUnits as $unit) {
            $unitName = trim((string) ($unit['name'] ?? ''));
            if ($unitName === '') {
                continue;
            }

            $unitData = [
                'company_id' => $companyId,
                'name' => $unitName,
                'code' => trim((string) ($unit['code'] ?? '')) ?: null,
                'status_id' => $unit['status_id'] ?: null,
            ];

            $unitId = !empty($unit['id']) ? (int) $unit['id'] : 0;

            if ($unitId > 0) {
                $unitData['date_updated'] = $now;
                $unitModel->update($unitId, $unitData);
            } else {
                $unitData['date_created'] = $now;
                $unitId = (int) $unitModel->insert($unitData, true);
            }

            $keptUnitIds[] = $unitId;

            $existingGroups = $groupModel
                ->where('unit_id', $unitId)
                ->where('date_deleted', null)
                ->findAll();

            $existingGroupIds = array_map(fn($row) => (int) $row['id'], $existingGroups);
            $keptGroupIds = [];

            foreach (($unit['groups'] ?? []) as $group) {
                $groupName = trim((string) ($group['name'] ?? ''));
                if ($groupName === '') {
                    continue;
                }

                $groupData = [
                    'unit_id' => $unitId,
                    'name' => $groupName,
                    'code' => trim((string) ($group['code'] ?? '')) ?: null,
                    'status_id' => $group['status_id'] ?: null,
                ];

                $groupId = !empty($group['id']) ? (int) $group['id'] : 0;

                if ($groupId > 0) {
                    $groupData['date_updated'] = $now;
                    $groupModel->update($groupId, $groupData);
                } else {
                    $groupData['date_created'] = $now;
                    $groupId = (int) $groupModel->insert($groupData, true);
                }

                $keptGroupIds[] = $groupId;
            }

            $groupIdsToDelete = array_diff($existingGroupIds, $keptGroupIds);
            if (!empty($groupIdsToDelete)) {
                foreach ($groupIdsToDelete as $deleteId) {
                    $groupModel->update($deleteId, ['date_deleted' => $now]);
                }
            }
        }

        $unitIdsToDelete = array_diff($existingUnitIds, $keptUnitIds);
        if (!empty($unitIdsToDelete)) {
            foreach ($unitIdsToDelete as $deleteUnitId) {
                $unitModel->update($deleteUnitId, ['date_deleted' => $now]);

                $groups = $groupModel
                    ->where('unit_id', $deleteUnitId)
                    ->where('date_deleted', null)
                    ->findAll();

                foreach ($groups as $group) {
                    $groupModel->update((int) $group['id'], ['date_deleted' => $now]);
                }
            }
        }
    }
}