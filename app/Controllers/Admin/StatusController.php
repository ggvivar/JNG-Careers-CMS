<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StatusModel;
use App\Models\StatusFeatureModel;

class StatusController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('status s')
            ->select("
                s.id,
                s.name,
                GROUP_CONCAT(CONCAT(m.name, ' / ', f.name) ORDER BY m.name, f.sort_order SEPARATOR ', ') as feature_names
            ")
            ->join('status_features sf', 'sf.status_id = s.id', 'left')
            ->join('features f', 'f.id = sf.feature_id', 'left')
            ->join('modules m', 'm.id = f.module_id', 'left')
            ->where('s.date_deleted', null)
            ->groupBy('s.id');

        if ($q !== '') {
            $builder->groupStart()
                ->like('s.name', $q)
                ->orLike('f.name', $q)
                ->orLike('m.name', $q)
                ->groupEnd();
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = count($countBuilder->get()->getResultArray());
        $statuses = $builder
            ->orderBy('s.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();
          
        return view('admin/status/index', [
            'statuses' => $statuses,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        $db = db_connect();

        $features = $db->table('features f')
            ->select('f.id, f.name, f.code, m.name as module_name')
            ->join('modules m', 'm.id = f.module_id', 'left')
            ->where('f.date_deleted', null)
            ->orderBy('m.name', 'ASC')
            ->orderBy('f.sort_order', 'ASC')
            ->get()
            ->getResultArray();

        $featureOptions = [];
        foreach ($features as $f) {
            $featureOptions[$f['id']] = $f['module_name'] . ' / ' . $f['name'];
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $statusModel = new StatusModel();
            $statusFeatureModel = new StatusFeatureModel();

            $statusModel->insert([
                'name' => trim((string) $this->request->getPost('name')),
                'date_created' => date('Y-m-d H:i:s'),
            ]);

            $statusId = $statusModel->getInsertID();
            $featureIds = $this->request->getPost('feature_ids') ?? [];
            $now = date('Y-m-d H:i:s');

            foreach ($featureIds as $featureId) {
                $statusFeatureModel->insert([
                    'status_id' => $statusId,
                    'feature_id' => (int) $featureId,
                    'date_created' => $now,
                    'date_updated' => $now,
                ]);
            }

            return redirect()->to('/admin/status')->with('success', 'Status created.');
        }

        return view('admin/status/form', [
            'mode' => 'create',
            'status' => null,
            'featureOptions' => $featureOptions,
            'selectedFeatures' => [],
        ]);
    }

    public function edit($id)
    {
        $id = (int) $id;
        $db = db_connect();

        $statusModel = new StatusModel();
        $statusFeatureModel = new StatusFeatureModel();

        $status = $statusModel->where('date_deleted', null)->find($id);

        if (! $status) {
            return redirect()->to('/admin/status')->with('error', 'Status not found.');
        }

        $features = $db->table('features f')
            ->select('f.id, f.name, f.code, m.name as module_name')
            ->join('modules m', 'm.id = f.module_id', 'left')
            ->where('f.date_deleted', null)
            ->orderBy('m.name', 'ASC')
            ->orderBy('f.sort_order', 'ASC')
            ->get()
            ->getResultArray();

        $featureOptions = [];
        foreach ($features as $f) {
            $featureOptions[$f['id']] = $f['module_name'] . ' / ' . $f['name'];
        }

        $existing = $statusFeatureModel->where('status_id', $id)->findAll();
        $selectedFeatures = array_map(
            fn ($row) => (int) $row['feature_id'],
            $existing
        );

        if (strtolower($this->request->getMethod()) === 'POST') {
            $statusModel->update($id, [
                'name' => trim((string) $this->request->getPost('name')),
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

            $statusFeatureModel->where('status_id', $id)->delete();

            $featureIds = $this->request->getPost('feature_ids') ?? [];
            $now = date('Y-m-d H:i:s');

            foreach ($featureIds as $featureId) {
                $statusFeatureModel->insert([
                    'status_id' => $id,
                    'feature_id' => (int) $featureId,
                    'date_created' => $now,
                    'date_updated' => $now,
                ]);
            }

            return redirect()->to('/admin/status')->with('success', 'Status updated.');
        }

        return view('admin/status/form', [
            'mode' => 'edit',
            'status' => $status,
            'featureOptions' => $featureOptions,
            'selectedFeatures' => $selectedFeatures,
        ]);
    }

    public function delete($id)
    {
        (new StatusModel())->update((int) $id, [
            'date_deleted' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/status')->with('success', 'Status deleted.');
    }
}