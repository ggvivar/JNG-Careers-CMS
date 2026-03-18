<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class CategoryController extends BaseController
{
    public function index()
    {
        helper('dropdown');
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('category c')
            ->select('c.*, m.name as module_name')
            ->join('modules m', 'm.id = c.module_id', 'left')
            ->where('c.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('c.name', $q)
                ->orLike('m.name', $q)
                ->groupEnd();
        }

        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $rows = $builder->orderBy('c.id', 'DESC')->limit($perPage, $offset)->get()->getResultArray();

        return view('admin/categories/index', [
            'categories' => $rows,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper('dropdown');
        $moduleOptions = dd_options('modules', 'id', 'name', [], ['name' => 'ASC']);

        if (strtolower($this->request->getMethod()) === 'post') {
            (new CategoryModel())->insert([
                'module_id' => $this->request->getPost('module_id') ?: null,
                'name' => trim((string) $this->request->getPost('name')),
                'key1' => trim((string) $this->request->getPost('key1')) ?: null,
                'key2' => trim((string) $this->request->getPost('key2')) ?: null,
                'key3' => trim((string) $this->request->getPost('key3')) ?: null,
                'date_created' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/categories')->with('success', 'Category created.');
        }

        return view('admin/categories/form', [
            'mode' => 'create',
            'category' => null,
            'moduleOptions' => $moduleOptions,
        ]);
    }

    public function edit($id)
    {
        helper('dropdown');
        $moduleOptions = dd_options('modules', 'id', 'name', [], ['name' => 'ASC']);
        $model = new CategoryModel();
        $category = $model->where('date_deleted', null)->find((int) $id);

        if (! $category) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found.');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $model->update((int) $id, [
                'module_id' => $this->request->getPost('module_id') ?: null,
                'name' => trim((string) $this->request->getPost('name')),
                'key1' => trim((string) $this->request->getPost('key1')) ?: null,
                'key2' => trim((string) $this->request->getPost('key2')) ?: null,
                'key3' => trim((string) $this->request->getPost('key3')) ?: null,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/categories')->with('success', 'Category updated.');
        }

        return view('admin/categories/form', [
            'mode' => 'edit',
            'category' => $category,
            'moduleOptions' => $moduleOptions,
        ]);
    }

    public function delete($id)
    {
        (new CategoryModel())->update((int) $id, [
            'date_deleted' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/categories')->with('success', 'Category deleted.');
    }
}