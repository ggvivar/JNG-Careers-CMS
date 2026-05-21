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
        $q  = trim((string) $this->request->getGet('q'));

        $builder = $db->table('contents c')
            ->select('
                c.*,
                p.name  as parent_name,
                m.name  as module_name,
                cat.name as category_name,
                s.name  as status_name
            ')
            ->join('contents p',  'p.id = c.main_content_id', 'left')
            ->join('modules m',   'm.id = c.module_id',       'left')
            ->join('category cat','cat.id = c.category_id',   'left')
            ->join('status s',    's.id = c.status_id',       'left')
            ->where('c.date_deleted', null)
            ->groupStart()
                ->where('c.main_content_id', null)
                ->orWhere('c.main_content_id', 0)
            ->groupEnd();

        if ($q !== '') {
            $builder->groupStart()
                ->like('c.name',    $q)
                ->orLike('c.slug',  $q)
                ->orLike('c.description', $q)
                ->orLike('c.body',  $q)
                ->orLike('p.name',  $q)
                ->orLike('m.name',  $q)
                ->orLike('cat.name',$q)
                ->orLike('s.name',  $q)
            ->groupEnd();
        }

        $perPage = 10;
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset  = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total        = $countBuilder->countAllResults();

        $contents = $builder
            ->orderBy('c.main_content_id', 'ASC')
            ->orderBy('c.rank',            'ASC')
            ->orderBy('c.id',              'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('admin/contents/index', [
            'contents'         => $contents,
            'searchQuery'      => $q,
            'paginationLinks'  => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  CREATE
    // ─────────────────────────────────────────────────────────────
    public function create()
    {
        helper(['dropdown', 'text']);

        $db    = db_connect();
        $model = new ContentModel();

        $parentId = (int) ($this->request->getGet('parent_id') ?? 0);

        $moduleOptions   = dd_options('modules',       'id', 'name', [], ['name' => 'ASC']);
        $categoryOptions = dd_options('category',      'id', 'name', [], ['name' => 'ASC']);
        $statusOptions   = dd_statuses_by_feature('contents');
        $matrixOptions   = dd_options('approvermatrix','id', 'name', [], ['name' => 'ASC'], false);

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

            /* ── tags ── */
            $tags     = trim((string) $this->request->getPost('tags'));
            $tagsJson = $tags !== ''
                ? json_encode(array_values(array_filter(array_map('trim', explode(',', $tags)))))
                : null;

            /* ── main images ── */
            [$firstImagePath, $firstImageUrl, $firstImageDesc, $imagesJson] =
                $this->buildImagesJson($this->request->getPost('images') ?? []);

            /* ── slug ── */
            $slugInput = trim((string) $this->request->getPost('slug'));
            $slugBase  = $slugInput !== '' ? $slugInput : url_title((string) $this->request->getPost('name'), '-', true);
            $slug      = $this->generateUniqueSlug($slugBase);

            $externalLink = trim((string) $this->request->getPost('external_link')) ?: null;

            /* ── insert parent ── */
            $model->insert([
                'main_content_id'    => $this->request->getPost('main_content_id') ?: null,
                'module_id'          => $this->request->getPost('module_id')  ?: null,
                'category_id'        => $this->request->getPost('category_id') ?: null,
                'name'               => trim((string) $this->request->getPost('name')),
                'slug'               => $slug,
                'lead'               => $this->request->getPost('lead')    ?: null,
                'excerpt'            => $this->request->getPost('excerpt') ?: null,
                'description'        => $this->request->getPost('description'),
                'body'               => $this->request->getPost('body'),
                'attachment'         => null,
                'image_path'         => $firstImagePath,
                'image_url'          => $firstImageUrl,
                'image_description'  => $firstImageDesc,
                'images'             => $imagesJson,
                'external_link'      => $externalLink,
                'tags'               => $tagsJson,
                'validity_date_start'=> $this->normalizeDatetime($this->request->getPost('validity_date_start')),
                'validity_date_end'  => $this->normalizeDatetime($this->request->getPost('validity_date_end')),
                'rank'               => $this->request->getPost('rank') ?: null,
                'status_id'          => $this->request->getPost('status_id') ?: null,
                'creator_id'         => session()->get('admin_id'),
                'approvermatrix_id'  => $this->request->getPost('approvermatrix_id') ?: null,
                'date_created'       => date('Y-m-d H:i:s'),
            ]);

            $newParentId = $model->getInsertID();

            /* ── subsections ── */
            $subsections = $this->request->getPost('subsections') ?? [];

            foreach ($subsections as $i => $sub) {
                $subName = trim((string) ($sub['name'] ?? ''));
                // if ($subName === '') continue;

                [$subImagePath, $subImageUrl, $subImageDesc, $subImagesJson] =
                    $this->buildSubsectionImagesJson($sub['images'] ?? [], $i);

                $subSlugInput = trim((string) ($sub['slug'] ?? ''));
                $subSlugBase  = $subSlugInput !== '' ? $subSlugInput : url_title($subName, '-', true);
                $subSlug      = $this->generateUniqueSlug($subSlugBase);

                $model->insert([
                    'main_content_id'   => $newParentId,
                    'module_id'         => $this->request->getPost('module_id')  ?: null,
                    'category_id'       => $this->request->getPost('category_id') ?: null,
                    'name'              => $subName,
                    'slug'              => $subSlug,
                    'description'       => $sub['description'] ?? null,
                    'body'              => $sub['body']        ?? null,
                    'image_path'        => $subImagePath,
                    'image_url'         => $subImageUrl,
                    'image_description' => $subImageDesc,
                    'images'            => $subImagesJson,
                    'rank'              => $sub['rank']        ?? ($i + 1),
                    'status_id'         => $this->request->getPost('status_id') ?: null,
                    'creator_id'        => session()->get('admin_id'),
                    'approvermatrix_id' => $this->request->getPost('approvermatrix_id') ?: null,
                    'date_created'      => date('Y-m-d H:i:s'),
                ]);
            }

            return redirect()->to('/admin/contents')->with('success', 'Content created.');
        }

        return view('admin/contents/form', [
            'mode'                => 'create',
            'content'             => null,
            'subsections'         => [],
            'moduleOptions'       => $moduleOptions,
            'categoryOptions'     => $categoryOptions,
            'statusOptions'       => $statusOptions,
            'matrixOptions'       => $matrixOptions,
            'parentContentOptions'=> $parentContentOptions,
            'defaultParentId'     => $parentId > 0 ? $parentId : null,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  EDIT
    // ─────────────────────────────────────────────────────────────
    public function edit($id)
    {
        helper(['dropdown', 'text']);

        $db    = db_connect();
        $model = new ContentModel();
        $id    = (int) $id;

        $content = $model->where('date_deleted', null)->find($id);
        if (! $content) {
            return redirect()->to('/admin/contents')->with('error', 'Content not found.');
        }

        $moduleOptions   = dd_options('modules',       'id', 'name', [], ['name' => 'ASC']);
        $categoryOptions = dd_options('category',      'id', 'name', [], ['name' => 'ASC']);
        $statusOptions   = dd_statuses_by_feature('contents');
        $matrixOptions   = dd_options('approvermatrix','id', 'name', [], ['name' => 'ASC'], false);
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

            /* ── tags ── */
            $tags     = trim((string) $this->request->getPost('tags'));
            $tagsJson = $tags !== ''
                ? json_encode(array_values(array_filter(array_map('trim', explode(',', $tags)))))
                : null;

            /* ── main images ── */
            [$firstImagePath, $firstImageUrl, $firstImageDesc, $imagesJson] =
                $this->buildImagesJson($this->request->getPost('images') ?? []);
            /* ── slug ── */
            $slugInput    = trim((string) $this->request->getPost('slug'));
            $slugBase     = $slugInput !== '' ? $slugInput : url_title((string) $this->request->getPost('name'), '-', true);
            $slug         = $this->generateUniqueSlug($slugBase, $id);
            $externalLink = trim((string) $this->request->getPost('external_link')) ?: null;

            $payload = [
                'main_content_id'    => $this->request->getPost('main_content_id') ?: null,
                'module_id'          => $this->request->getPost('module_id')  ?: null,
                'category_id'        => $this->request->getPost('category_id') ?: null,
                'name'               => trim((string) $this->request->getPost('name')),
                'slug'               => $slug,
                'lead'               => trim((string) $this->request->getPost('lead')),
                'excerpt'            => trim((string) $this->request->getPost('excerpt')),
                'description'        => $this->request->getPost('description'),
                'body'               => $this->request->getPost('body'),
                'image_url'          => $firstImageUrl,
                'image_description'  => $firstImageDesc,
                'images'             => $imagesJson,
                'external_link'      => $externalLink,
                'tags'               => $tagsJson,
                'validity_date_start'=> $this->normalizeDatetime($this->request->getPost('validity_date_start')),
                'validity_date_end'  => $this->normalizeDatetime($this->request->getPost('validity_date_end')),
                'rank'               => $this->request->getPost('rank') ?: null,
                'status_id'          => $this->request->getPost('status_id') ?: null,
                'approvermatrix_id'  => $this->request->getPost('approvermatrix_id') ?: null,
                'date_updated'       => date('Y-m-d H:i:s'),
            ];

            if ($firstImagePath !== null) {
                $payload['image_path'] = $firstImagePath;
            }

            $model->update($id, $payload);

            /* ── subsections ── */
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
            $subsections  = $this->request->getPost('subsections') ?? [];

            foreach ($subsections as $i => $sub) {
                $subName = trim((string) ($sub['name'] ?? ''));
                // if ($subName === '') continue;

                $subId = ! empty($sub['id']) ? (int) $sub['id'] : null;

                [$subImagePath, $subImageUrl, $subImageDesc, $subImagesJson] =
                    $this->buildSubsectionImagesJson($sub['images'] ?? [], $i);

                // fall back to existing path if no new upload
                if ($subImagePath === null && $subId && isset($existingSubMap[$subId])) {
                    $subImagePath = $existingSubMap[$subId]['image_path'];
                }

                $subSlugInput = trim((string) ($sub['slug'] ?? ''));
                $subSlugBase  = $subSlugInput !== '' ? $subSlugInput : url_title($subName, '-', true);
                $subSlug      = $this->generateUniqueSlug($subSlugBase, $subId);

                $subPayload = [
                    'main_content_id'   => $parentContentId,
                    'module_id'         => $this->request->getPost('module_id')  ?: null,
                    'category_id'       => $this->request->getPost('category_id') ?: null,
                    'name'              => $subName,
                    'slug'              => $subSlug,
                    'description'       => $sub['description'] ?? null,
                    'body'              => $sub['body']        ?? null,
                    'image_path'        => $subImagePath,
                    'image_url'         => $subImageUrl,
                    'image_description' => $subImageDesc,
                    'images'            => $subImagesJson,
                    'rank'              => $sub['rank']        ?? ($i + 1),
                    'status_id'         => $this->request->getPost('status_id') ?: null,
                    'approvermatrix_id' => $this->request->getPost('approvermatrix_id') ?: null,
                    'date_updated'      => date('Y-m-d H:i:s'),
                ];

                if ($subId) {
                    $model->update($subId, $subPayload);
                    $submittedIds[] = $subId;
                } else {
                    $subPayload['creator_id']   = session()->get('admin_id');
                    $subPayload['date_created'] = date('Y-m-d H:i:s');
                    $newId = $model->insert($subPayload, true);
                    $submittedIds[] = $newId;
                }
            }

            // soft-delete removed subsections
            foreach ($existingSubsections as $existingSub) {
                if (! in_array((int) $existingSub['id'], $submittedIds, true)) {
                    $model->update($existingSub['id'], ['date_deleted' => date('Y-m-d H:i:s')]);
                }
            }

            return redirect()->to('/admin/contents')->with('success', 'Content updated.');
        }

        /* ── load subsections for GET ── */
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
            'mode'                => 'edit',
            'content'             => $content,
            'moduleOptions'       => $moduleOptions,
            'categoryOptions'     => $categoryOptions,
            'statusOptions'       => $statusOptions,
            'matrixOptions'       => $matrixOptions,
            'parentContentOptions'=> $parentContentOptions,
            'subsections'         => $subsections,
            'defaultParentId'     => null,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  DELETE
    // ─────────────────────────────────────────────────────────────
    public function delete($id)
    {
        $id = (int) $id;
        $db = db_connect();

        $db->table('contents')->where('id', $id)->update(['date_deleted' => date('Y-m-d H:i:s')]);
        $db->table('contents')->where('main_content_id', $id)->where('date_deleted', null)
            ->update(['date_deleted' => date('Y-m-d H:i:s')]);

        return redirect()->to('/admin/contents')->with('success', 'Content deleted.');
    }

    // ─────────────────────────────────────────────────────────────
    //  WORKFLOW
    // ─────────────────────────────────────────────────────────────
    public function submit($id)
    {
        helper('dropdown');
        $statusId = dd_status_id('Submitted', 'contents');

        db_connect()->table('contents')
            ->where('id', (int) $id)->where('date_deleted', null)
            ->update(['status_id' => $statusId, 'date_updated' => date('Y-m-d H:i:s')]);

        return redirect()->to('/admin/contents')->with('success', 'Content submitted.');
    }

    public function approve($id)
    {
        helper('dropdown');
        $statusId = dd_status_id('Approved', 'contents');

        db_connect()->table('contents')
            ->where('id', (int) $id)->where('date_deleted', null)
            ->update([
                'status_id'    => $statusId,
                'date_approved'=> date('Y-m-d H:i:s'),
                'curr_approver'=> session()->get('admin_id'),
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/admin/contents')->with('success', 'Content approved.');
    }

    public function reject($id)
    {
        helper('dropdown');
        $statusId = dd_status_id('Rejected', 'contents');

        db_connect()->table('contents')
            ->where('id', (int) $id)->where('date_deleted', null)
            ->update([
                'status_id'    => $statusId,
                'curr_approver'=> session()->get('admin_id'),
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/admin/contents')->with('success', 'Content rejected.');
    }

    // ─────────────────────────────────────────────────────────────
    //  VIEW
    // ─────────────────────────────────────────────────────────────
    public function view($id)
    {
        $db = db_connect();
        $id = (int) $id;

        $content = $db->table('contents c')
            ->select('c.*, m.name as module_name, cat.name as category_name, s.name as status_name')
            ->join('modules m',    'm.id = c.module_id',   'left')
            ->join('category cat', 'cat.id = c.category_id','left')
            ->join('status s',     's.id = c.status_id',   'left')
            ->where('c.id', $id)
            ->where('c.date_deleted', null)
            ->get()->getRowArray();

        if (! $content) {
            return redirect()->to('/admin/contents')->with('error', 'Content not found.');
        }

        $parentContentId = empty($content['main_content_id']) ? $id : (int) $content['main_content_id'];

        $subsections = $db->table('contents')
            ->where('main_content_id', $parentContentId)
            ->where('date_deleted', null)
            ->orderBy('rank', 'ASC')
            ->orderBy('id',   'ASC')
            ->get()->getResultArray();

        return view('admin/contents/view', [
            'content'     => $content,
            'subsections' => $subsections,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────

    /**
     * Build the images JSON array for the main content or any record.
     * Returns [$firstImagePath, $firstImageUrl, $firstImageDesc, $imagesJsonString|null]
     */
    private function buildImagesJson(array $imagesPost): array
    {
        $collected = [];
        foreach ($imagesPost as $ii => $imgMeta) {
            $uploaded      = $this->uploadContentImage('image_file_' . $ii);
            // $existingPath  = trim((string) ($imgMeta['existing_path'] ?? '')) ?: null;
            $existingPath  = trim((string) ($imgMeta['existing_path'] ?? '')) ?: null;
            $path          = $uploaded ?? $existingPath;
            $url           = trim((string) ($imgMeta['url']     ?? '')) ?: null;
            $caption       = trim((string) ($imgMeta['caption'] ?? '')) ?: null;
            $order         = (int) ($imgMeta['order'] ?? ($ii + 1));
            if ($path === null && $url === null) continue;

            $collected[] = compact('path', 'url', 'caption', 'order');
        }
        usort($collected, fn($a, $b) => $a['order'] <=> $b['order']);

        $first          = $collected[0] ?? [];
        $firstImagePath = $first['path']    ?? null;
        $firstImageUrl  = $first['url']     ?? null;
        $firstImageDesc = $first['caption'] ?? null;
        $imagesJson     = ! empty($collected) ? json_encode($collected) : null;

        return [$firstImagePath, $firstImageUrl, $firstImageDesc, $imagesJson];
    }

    /**
     * Build images JSON for a subsection at index $subIdx.
     * Returns [$firstImagePath, $firstImageUrl, $firstImageDesc, $imagesJsonString|null]
     */
    private function buildSubsectionImagesJson(array $imagesPost, int $subIdx): array
    {
        $collected = [];

        foreach ($imagesPost as $si => $imgMeta) {
            $uploaded     = $this->uploadContentImage('subsection_image_file_' . $subIdx . '_' . $si);
            // $existingPath = trim((string) ($imgMeta['existing_path'] ?? '')) ?: null;
            $existingPath = trim((string) ($imgMeta['path'] ?? '')) ?: null;
            $path         = $uploaded ?? $existingPath;
            $url          = trim((string) ($imgMeta['url']     ?? '')) ?: null;
            $caption      = trim((string) ($imgMeta['caption'] ?? '')) ?: null;
            $order        = (int) ($imgMeta['order'] ?? ($si + 1));

            if ($path === null && $url === null) continue;

            $collected[] = compact('path', 'url', 'caption', 'order');
        }

        usort($collected, fn($a, $b) => $a['order'] <=> $b['order']);

        $first          = $collected[0] ?? [];
        $firstImagePath = $first['path']    ?? null;
        $firstImageUrl  = $first['url']     ?? null;
        $firstImageDesc = $first['caption'] ?? null;
        $imagesJson     = ! empty($collected) ? json_encode($collected) : null;

        return [$firstImagePath, $firstImageUrl, $firstImageDesc, $imagesJson];
    }

    private function normalizeDatetime($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') return null;
        $timestamp = strtotime($value);
        return $timestamp === false ? null : date('Y-m-d H:i:s', $timestamp);
    }

    private function uploadContentImage(string $fieldName = 'image_file'): ?string
    {
        $file = $this->request->getFile($fieldName);
        if (! $file || ! $file->isValid() || $file->hasMoved()) return null;

        $allowedMime = ['image/jpeg','image/png','image/webp','image/gif'];
        if (! in_array($file->getMimeType(), $allowedMime, true)) return null;

        $targetDir = FCPATH . 'uploads/contents';
        if (! is_dir($targetDir)) mkdir($targetDir, 0775, true);

        $newName = $file->getRandomName();
        $file->move($targetDir, $newName);

        return 'uploads/contents/' . $newName;
    }

    private function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        helper('text');
        $baseSlug  = url_title($slug, '-', true);
        if ($baseSlug === '') $baseSlug = 'content';

        $candidate = $baseSlug;
        $counter   = 1;
        $db        = db_connect();

        while (true) {
            $builder = $db->table('contents')
                ->where('slug', $candidate)
                ->where('date_deleted', null);

            if ($excludeId !== null) $builder->where('id !=', $excludeId);

            if ($builder->countAllResults() === 0) return $candidate;

            $candidate = $baseSlug . '-' . $counter++;
        }
    }
}