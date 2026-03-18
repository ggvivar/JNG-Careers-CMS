<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FeatureModel;

class FeatureController extends BaseController
{
    public function index()
    {
        helper('dropdown');
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('features f')
            ->select('f.*, m.name as module_name')
            ->join('modules m', 'm.id = f.module_id', 'left')
            ->where('f.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('f.name', $q)
                ->orLike('f.code', $q)
                ->orLike('m.name', $q)
                ->orLike('f.description', $q)
                ->groupEnd();
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $features = $builder
            ->orderBy('m.name', 'ASC')
            ->orderBy('f.sort_order', 'ASC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('admin/features/index', [
            'features' => $features,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper('dropdown');
        $moduleOptions = dd_options('modules', 'id', 'name', [], ['name' => 'ASC']);

        if (strtolower($this->request->getMethod()) === 'post') {
            (new FeatureModel())->insert([
                'module_id' => $this->request->getPost('module_id') ?: null,
                'name' => trim((string) $this->request->getPost('name')),
                'code' => trim((string) $this->request->getPost('code')),
                'sort_order' => (int) ($this->request->getPost('sort_order') ?: 0),
                'description' => trim((string) $this->request->getPost('description')) ?: null,
                'date_created' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/features')->with('success', 'Feature created.');
        }

        return view('admin/features/form', [
            'mode' => 'create',
            'feature' => null,
            'moduleOptions' => $moduleOptions,
        ]);
    }

    public function edit($id)
    {
        helper('dropdown');
        $moduleOptions = dd_options('modules', 'id', 'name', [], ['name' => 'ASC']);

        $model = new FeatureModel();
        $feature = $model->where('date_deleted', null)->find((int) $id);

        if (! $feature) {
            return redirect()->to('/admin/features')->with('error', 'Feature not found.');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $model->update((int) $id, [
                'module_id' => $this->request->getPost('module_id') ?: null,
                'name' => trim((string) $this->request->getPost('name')),
                'code' => trim((string) $this->request->getPost('code')),
                'sort_order' => (int) ($this->request->getPost('sort_order') ?: 0),
                'description' => trim((string) $this->request->getPost('description')) ?: null,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/features')->with('success', 'Feature updated.');
        }

        return view('admin/features/form', [
            'mode' => 'edit',
            'feature' => $feature,
            'moduleOptions' => $moduleOptions,
        ]);
    }

    public function delete($id)
    {
        (new FeatureModel())->update((int) $id, [
            'date_deleted' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/features')->with('success', 'Feature deleted.');
    }
}