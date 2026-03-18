<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ContentModel;

class ContentController extends BaseController
{
    public function index()
    {
        helper('dropdown');

        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('contents c')
            ->select('
                c.*,
                p.name as parent_name,
                m.name as module_name,
                cat.name as category_name,
                s.name as status_name
            ')
            ->join('contents p', 'p.id = c.main_content_id', 'left')
            ->join('modules m', 'm.id = c.module_id', 'left')
            ->join('category cat', 'cat.id = c.category_id', 'left')
            ->join('status s', 's.id = c.status_id', 'left')
            ->where('c.date_deleted', null)
            ->where('c.main_content_id', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('c.name', $q)
                ->orLike('c.slug', $q)
                ->orLike('c.description', $q)
                ->orLike('c.body', $q)
                ->orLike('p.name', $q)
                ->orLike('m.name', $q)
                ->orLike('cat.name', $q)
                ->orLike('s.name', $q)
                ->groupEnd();
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $contents = $builder
            ->orderBy('c.main_content_id', 'ASC')
            ->orderBy('c.rank', 'ASC')
            ->orderBy('c.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('admin/contents/index', [
            'contents' => $contents,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper(['dropdown', 'text']);

        $db = db_connect();
        $model = new ContentModel();

        $parentId = (int) ($this->request->getGet('parent_id') ?? 0);

        $moduleOptions = dd_options('modules', 'id', 'name', [], ['name' => 'ASC']);
        $categoryOptions = dd_options('category', 'id', 'name', [], ['name' => 'ASC']);
        $statusOptions = dd_statuses_by_feature('contents');
        $matrixOptions = dd_options('approvermatrix', 'id', 'name', [], ['name' => 'ASC'], false);

        // $parentRows = $db->table('contents')
        //     ->select('id, name')
        //     ->where('date_deleted', null)
        //     ->orderBy('name', 'ASC')
        //     ->get()
        //     ->getResultArray();
        $parentRows = $db->table('contents')
            ->select('id, name')
            ->where('date_deleted', null)
            ->where('main_content_id', null)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
        $parentContentOptions = ['' => 'Top Level'];
        foreach ($parentRows as $row) {
            $parentContentOptions[$row['id']] = $row['name'];
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $tags = trim((string) $this->request->getPost('tags'));
            $tagsJson = $tags !== ''
                ? json_encode(array_values(array_filter(array_map('trim', explode(',', $tags)))))
                : null;

            $imagePath = $this->uploadContentImage('image_file');
            $imageUrl = trim((string) $this->request->getPost('image_url')) ?: null;
            $externalLink = trim((string) $this->request->getPost('external_link')) ?: null;

            $slugInput = trim((string) $this->request->getPost('slug'));
            $slugBase = $slugInput !== '' ? $slugInput : url_title((string) $this->request->getPost('name'), '-', true);
            $slug = $this->generateUniqueSlug($slugBase);

            $model->insert([
                'main_content_id' => $this->request->getPost('main_content_id') ?: null,
                'module_id' => $this->request->getPost('module_id') ?: null,
                'category_id' => $this->request->getPost('category_id') ?: null,
                'name' => trim((string) $this->request->getPost('name')),
                'slug' => $slug,
                'description' => $this->request->getPost('description'),
                'body' => $this->request->getPost('body'),
                'attachment' => null,
                'image_path' => $imagePath,
                'image_url' => $imageUrl,
                'image_description' => $this->request->getPost('image_description'),
                'external_link' => $externalLink,
                'tags' => $tagsJson,
                'validity_date_start' => $this->normalizeDatetime($this->request->getPost('validity_date_start')),
                'validity_date_end' => $this->normalizeDatetime($this->request->getPost('validity_date_end')),
                'rank' => $this->request->getPost('rank') ?: null,
                'status_id' => $this->request->getPost('status_id') ?: null,
                'creator_id' => session()->get('admin_id'),
                'approvermatrix_id' => $this->request->getPost('approvermatrix_id') ?: null,
                'date_created' => date('Y-m-d H:i:s'),
            ]);

            $parentId = $model->getInsertID();

            $subsections = $this->request->getPost('subsections') ?? [];

            foreach ($subsections as $i => $sub) {
                $subName = trim((string) ($sub['name'] ?? ''));
                if ($subName === '') {
                    continue;
                }

                $subImagePath = $this->uploadContentImage('subsection_image_file_' . $i);
                $subImageUrl = trim((string) ($sub['image_url'] ?? '')) ?: null;

                $subSlugInput = trim((string) ($sub['slug'] ?? ''));
                $subSlugBase = $subSlugInput !== '' ? $subSlugInput : url_title($subName, '-', true);
                $subSlug = $this->generateUniqueSlug($subSlugBase);

                $model->insert([
                    'main_content_id' => $parentId,
                    'module_id' => $this->request->getPost('module_id') ?: null,
                    'category_id' => $this->request->getPost('category_id') ?: null,
                    'name' => $subName,
                    'slug' => $subSlug,
                    'description' => $sub['description'] ?? null,
                    'body' => $sub['body'] ?? null,
                    'image_path' => $subImagePath,
                    'image_url' => $subImageUrl,
                    'image_description' =>$sub['description'] ?? null,
                    'rank' => $sub['rank'] ?? null,
                    'status_id' => $this->request->getPost('status_id') ?: null,
                    'creator_id' => session()->get('admin_id'),
                    'approvermatrix_id' => $this->request->getPost('approvermatrix_id') ?: null,
                    'date_created' => date('Y-m-d H:i:s'),
                ]);
            }

            return redirect()->to('/admin/contents')->with('success', 'Content created.');
        }

        return view('admin/contents/form', [
            'mode' => 'create',
            'content' => null,
            'subsections' => [],
            'moduleOptions' => $moduleOptions,
            'categoryOptions' => $categoryOptions,
            'statusOptions' => $statusOptions,
            'matrixOptions' => $matrixOptions,
            'parentContentOptions' => $parentContentOptions,
            'defaultParentId' => $parentId > 0 ? $parentId : null,
        ]);
    }

    public function edit($id)
{
    helper(['dropdown', 'text']);

    $db = db_connect();
    $model = new ContentModel();
    $id = (int) $id;

    $content = $model->where('date_deleted', null)->find($id);

    if (! $content) {
        return redirect()->to('/admin/contents')->with('error', 'Content not found.');
    }

    $moduleOptions = dd_options('modules', 'id', 'name', [], ['name' => 'ASC']);
    $categoryOptions = dd_options('category', 'id', 'name', [], ['name' => 'ASC']);
    $statusOptions = dd_statuses_by_feature('contents');
    $matrixOptions = dd_options('approvermatrix', 'id', 'name', [], ['name' => 'ASC'], false);

    $parentRows = $db->table('contents')
        ->select('id, name')
        ->where('date_deleted', null)
        ->where('main_content_id', null)
        ->where('id !=', $id)
        ->orderBy('name', 'ASC')
        ->get()
        ->getResultArray();

    $parentContentOptions = ['' => 'Top Level'];
    foreach ($parentRows as $row) {
        $parentContentOptions[$row['id']] = $row['name'];
    }

    if (strtolower($this->request->getMethod()) === 'post') {
        $tags = trim((string) $this->request->getPost('tags'));
        $tagsJson = $tags !== ''
            ? json_encode(array_values(array_filter(array_map('trim', explode(',', $tags)))))
            : null;

        $imagePath = $this->uploadContentImage('image_file');
        $imageUrl = trim((string) $this->request->getPost('image_url')) ?: null;
        $externalLink = trim((string) $this->request->getPost('external_link')) ?: null;

        $slugInput = trim((string) $this->request->getPost('slug'));
        $slugBase = $slugInput !== '' ? $slugInput : url_title((string) $this->request->getPost('name'), '-', true);
        $slug = $this->generateUniqueSlug($slugBase, $id);

        $payload = [
            'main_content_id' => $this->request->getPost('main_content_id') ?: null,
            'module_id' => $this->request->getPost('module_id') ?: null,
            'category_id' => $this->request->getPost('category_id') ?: null,
            'name' => trim((string) $this->request->getPost('name')),
            'slug' => $slug,
            'description' => $this->request->getPost('description'),
            'body' => $this->request->getPost('body'),
            'image_url' => $imageUrl,
            'image_description' => $this->request->getPost('image_description'),
            'external_link' => $externalLink,
            'tags' => $tagsJson,
            'validity_date_start' => $this->normalizeDatetime($this->request->getPost('validity_date_start')),
            'validity_date_end' => $this->normalizeDatetime($this->request->getPost('validity_date_end')),
            'rank' => $this->request->getPost('rank') ?: null,
            'status_id' => $this->request->getPost('status_id') ?: null,
            'approvermatrix_id' => $this->request->getPost('approvermatrix_id') ?: null,
            'date_updated' => date('Y-m-d H:i:s'),
        ];

        if ($imagePath !== null) {
            $payload['image_path'] = $imagePath;
        }

        $model->update($id, $payload);

        // Determine true parent for subsection handling
        $parentContentId = empty($content['main_content_id'])
            ? $id
            : (int) $content['main_content_id'];

        $existingSubsections = $db->table('contents')
            ->select('id, image_path')
            ->where('main_content_id', $parentContentId)
            ->where('date_deleted', null)
            ->get()
            ->getResultArray();

        $existingSubMap = [];
        foreach ($existingSubsections as $existingSub) {
            $existingSubMap[(int) $existingSub['id']] = $existingSub;
        }

        $submittedIds = [];
        $subsections = $this->request->getPost('subsections') ?? [];

        foreach ($subsections as $i => $sub) {
            $subName = trim((string) ($sub['name'] ?? ''));
            if ($subName === '') {
                continue;
            }

            $subId = !empty($sub['id']) ? (int) $sub['id'] : null;

            $subImagePath = $this->uploadContentImage('subsection_image_file_' . $i);
            if ($subImagePath === null) {
                $subImagePath = $sub['existing_image_path'] ?? ($subId && isset($existingSubMap[$subId]) ? $existingSubMap[$subId]['image_path'] : null);
            }

            $subImageUrl = trim((string) ($sub['image_url'] ?? '')) ?: null;

            $subSlugInput = trim((string) ($sub['slug'] ?? ''));
            $subSlugBase = $subSlugInput !== '' ? $subSlugInput : url_title($subName, '-', true);
            $subSlug = $this->generateUniqueSlug($subSlugBase, $subId);

            $subPayload = [
                'main_content_id' => $parentContentId,
                'module_id' => $this->request->getPost('module_id') ?: null,
                'category_id' => $this->request->getPost('category_id') ?: null,
                'name' => $subName,
                'slug' => $subSlug,
                'description' => $sub['description'] ?? null,
                'body' => $sub['body'] ?? null,
                'image_path' => $subImagePath,
                'image_url' => $subImageUrl,
                'image_description' => $sub['image_description'] ?? null,
                'rank' => $sub['rank'] ?? null,
                'status_id' => $this->request->getPost('status_id') ?: null,
                'approvermatrix_id' => $this->request->getPost('approvermatrix_id') ?: null,
                'date_updated' => date('Y-m-d H:i:s'),
            ];

            if ($subId) {
                $model->update($subId, $subPayload);
                $submittedIds[] = $subId;
            } else {
                $subPayload['creator_id'] = session()->get('admin_id');
                $subPayload['date_created'] = date('Y-m-d H:i:s');
                $newId = $model->insert($subPayload, true);
                $submittedIds[] = $newId;
            }
        }

        foreach ($existingSubsections as $existingSub) {
            if (!in_array((int) $existingSub['id'], $submittedIds, true)) {
                $model->update($existingSub['id'], [
                    'date_deleted' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return redirect()->to('/admin/contents')->with('success', 'Content updated.');
    }

    // Load subsections using true parent id
    $parentContentId = empty($content['main_content_id'])
        ? $id
        : (int) $content['main_content_id'];

    $subsections = $db->table('contents')
        ->where('main_content_id', $parentContentId)
        ->where('date_deleted', null)
        ->orderBy('rank', 'ASC')
        ->get()
        ->getResultArray();

    return view('admin/contents/form', [
        'mode' => 'edit',
        'content' => $content,
        'moduleOptions' => $moduleOptions,
        'categoryOptions' => $categoryOptions,
        'statusOptions' => $statusOptions,
        'matrixOptions' => $matrixOptions,
        'parentContentOptions' => $parentContentOptions,
        'subsections' => $subsections,
        'defaultParentId' => null,
    ]);
}
    public function delete($id)
    {
        $id = (int) $id;
        $db = db_connect();

        $db->table('contents')
            ->where('id', $id)
            ->update([
                'date_deleted' => date('Y-m-d H:i:s'),
            ]);

        $db->table('contents')
            ->where('main_content_id', $id)
            ->where('date_deleted', null)
            ->update([
                'date_deleted' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/admin/contents')->with('success', 'Content deleted.');
    }

    public function submit($id)
    {
        helper('dropdown');

        $submittedStatusId = dd_status_id('Submitted', 'contents');

        db_connect()->table('contents')
            ->where('id', (int) $id)
            ->where('date_deleted', null)
            ->update([
                'status_id' => $submittedStatusId,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/admin/contents')->with('success', 'Content submitted.');
    }

    public function approve($id)
    {
        helper('dropdown');

        $approvedStatusId = dd_status_id('Approved', 'contents');

        db_connect()->table('contents')
            ->where('id', (int) $id)
            ->where('date_deleted', null)
            ->update([
                'status_id' => $approvedStatusId,
                'date_approved' => date('Y-m-d H:i:s'),
                'curr_approver' => session()->get('admin_id'),
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/admin/contents')->with('success', 'Content approved.');
    }

    public function reject($id)
    {
        helper('dropdown');

        $rejectedStatusId = dd_status_id('Rejected', 'contents');

        db_connect()->table('contents')
            ->where('id', (int) $id)
            ->where('date_deleted', null)
            ->update([
                'status_id' => $rejectedStatusId,
                'curr_approver' => session()->get('admin_id'),
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/admin/contents')->with('success', 'Content rejected.');
    }

    public function view($id)
{
    $db = db_connect();
    $id = (int) $id;

    $content = $db->table('contents c')
        ->select('c.*, m.name as module_name, cat.name as category_name, s.name as status_name')
        ->join('modules m', 'm.id = c.module_id', 'left')
        ->join('category cat', 'cat.id = c.category_id', 'left')
        ->join('status s', 's.id = c.status_id', 'left')
        ->where('c.id', $id)
        ->where('c.date_deleted', null)
        ->get()
        ->getRowArray();

    if (! $content) {
        return redirect()->to('/admin/contents')->with('error', 'Content not found.');
    }

    $parentContentId = empty($content['main_content_id'])
        ? $id
        : (int) $content['main_content_id'];

    $subsections = $db->table('contents')
        ->where('main_content_id', $parentContentId)
        ->where('date_deleted', null)
        ->orderBy('rank', 'ASC')
        ->orderBy('id', 'ASC')
        ->get()
        ->getResultArray();

    return view('admin/contents/view', [
        'content' => $content,
        'subsections' => $subsections,
    ]);
}

    private function normalizeDatetime($value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $timestamp = strtotime($value);

        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    private function uploadContentImage(string $fieldName = 'image_file'): ?string
    {
        $file = $this->request->getFile($fieldName);

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $allowedMime = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
        ];

        if (! in_array($file->getMimeType(), $allowedMime, true)) {
            return null;
        }

        $targetDir = FCPATH . 'uploads/contents';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($targetDir, $newName);

        return 'uploads/contents/' . $newName;
    }

    private function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        helper('text');

        $baseSlug = url_title($slug, '-', true);
        if ($baseSlug === '') {
            $baseSlug = 'content';
        }

        $candidate = $baseSlug;
        $counter = 1;

        $db = db_connect();

        while (true) {
            $builder = $db->table('contents')
                ->where('slug', $candidate)
                ->where('date_deleted', null);

            if ($excludeId !== null) {
                $builder->where('id !=', $excludeId);
            }

            $exists = $builder->countAllResults();

            if ($exists === 0) {
                return $candidate;
            }

            $candidate = $baseSlug . '-' . $counter;
            $counter++;
        }
    }
}