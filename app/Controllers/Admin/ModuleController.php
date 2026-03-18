<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ModuleModel;

class ModuleController extends BaseController
{
    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));

        $builder = (new ModuleModel())
            ->where('date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('name', $q)
                ->orLike('key1', $q)
                ->orLike('key2', $q)
                ->orLike('key3', $q)
                ->groupEnd();
        }

        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $total = clone $builder;
        $totalRows = $total->countAllResults();

        $rows = $builder->orderBy('id', 'DESC')->findAll($perPage, $offset);

        return view('admin/modules/index', [
            'modules' => $rows,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $totalRows),
        ]);
    }

    public function create()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            (new ModuleModel())->insert([
                'name' => trim((string) $this->request->getPost('name')),
                'key1' => trim((string) $this->request->getPost('key1')) ?: null,
                'key2' => trim((string) $this->request->getPost('key2')) ?: null,
                'key3' => trim((string) $this->request->getPost('key3')) ?: null,
                'date_created' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/modules')->with('success', 'Module created.');
        }

        return view('admin/modules/form', [
            'mode' => 'create',
            'module' => null,
        ]);
    }

    public function edit($id)
    {
        $model = new ModuleModel();
        $module = $model->where('date_deleted', null)->find((int) $id);

        if (! $module) {
            return redirect()->to('/admin/modules')->with('error', 'Module not found.');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $model->update((int) $id, [
                'name' => trim((string) $this->request->getPost('name')),
                'key1' => trim((string) $this->request->getPost('key1')) ?: null,
                'key2' => trim((string) $this->request->getPost('key2')) ?: null,
                'key3' => trim((string) $this->request->getPost('key3')) ?: null,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/modules')->with('success', 'Module updated.');
        }

        return view('admin/modules/form', [
            'mode' => 'edit',
            'module' => $module,
        ]);
    }

    public function delete($id)
    {
        (new ModuleModel())->update((int) $id, [
            'date_deleted' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/modules')->with('success', 'Module deleted.');
    }
}