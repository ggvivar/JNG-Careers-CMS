<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class ContentController extends BaseController
{
    public function all()
    {
        $db = db_connect();

        $rows = $db->table('contents c')
            ->select('
                c.id,
                c.main_content_id,
                c.name,
                c.description,
                c.body,
                c.image_path,
                c.image_url,
                c.external_link,
                c.tags,
                c.rank,
                c.validity_date_start,
                c.validity_date_end,
                c.date_created,
                c.date_updated,
                m.name as module_name,
                m.key1 as module_key1,
                m.key2 as module_key2,
                m.key3 as module_key3,
                cat.name as category_name,
                cat.key1 as category_key1,
                cat.key2 as category_key2,
                cat.key3 as category_key3,
                s.name as status_name,
                p.name as parent_name
            ')
            ->join('modules m', 'm.id = c.module_id', 'left')
            ->join('category cat', 'cat.id = c.category_id', 'left')
            ->join('status s', 's.id = c.status_id', 'left')
            ->join('contents p', 'p.id = c.main_content_id', 'left')
            ->where('c.date_deleted', null)
            ->where('c.main_content_id', null)
            ->groupStart()
                ->where('s.name', 'Published')
                ->orWhere('s.name', 'Approved')
                ->orWhere('s.name', 'Active')
            ->groupEnd()
            ->orderBy('c.rank', 'ASC')
            ->orderBy('c.id', 'DESC')
            ->get()
            ->getResultArray();

        $rows = $this->attachSubsections($rows);
        
        return $this->response->setJSON([
            'status' => true,
            'count' => count($rows),
            'data' => $rows,
        ]);
    }

    public function byCategory($category)
    {
        $db = db_connect();

        $rows = $db->table('contents c')
            ->select('
                c.id,
                c.main_content_id,
                c.name,
                c.description,
                c.body,
                c.image_path,
                c.image_url,
                c.external_link,
                c.tags,
                c.rank,
                c.validity_date_start,
                c.validity_date_end,
                c.date_created,
                c.date_updated,
                m.name as module_name,
                m.key1 as module_key1,
                m.key2 as module_key2,
                m.key3 as module_key3,
                cat.name as category_name,
                cat.key1 as category_key1,
                cat.key2 as category_key2,
                cat.key3 as category_key3,
                s.name as status_name,
                p.name as parent_name
            ')
            ->join('modules m', 'm.id = c.module_id', 'left')
            ->join('category cat', 'cat.id = c.category_id', 'left')
            ->join('status s', 's.id = c.status_id', 'left')
            ->join('contents p', 'p.id = c.main_content_id', 'left')
            ->where('c.date_deleted', null)
            ->where('c.main_content_id', null)
            ->groupStart()
                ->where('LOWER(cat.name)', strtolower($category))
                ->orWhere('LOWER(cat.key1)', strtolower($category))
                ->orWhere('LOWER(cat.key2)', strtolower($category))
                ->orWhere('LOWER(cat.key3)', strtolower($category))
            ->groupEnd()
            ->groupStart()
                ->where('s.name', 'Published')
                ->orWhere('s.name', 'Approved')
                ->orWhere('s.name', 'Active')
            ->groupEnd()
            ->orderBy('c.rank', 'ASC')
            ->orderBy('c.id', 'DESC')
            ->get()
            ->getResultArray();

        $rows = $this->attachSubsections($rows);

        return $this->response->setJSON([
            'status' => true,
            'category' => $category,
            'count' => count($rows),
            'data' => $rows,
        ]);
    }
    public function year($year)
    {
        $db = db_connect();
        $date = date('Y');
        dd($date);
        dd(date_format($date,'%Y'));
        $rows = $db->table('contents c')
            ->select('
                c.id,
                c.main_content_id,
                c.name,
                c.description,
                c.body,
                c.image_path,
                c.image_url,
                c.external_link,
                c.tags,
                c.rank,
                c.validity_date_start,
                c.validity_date_end,
                c.date_created,
                c.date_updated,
                m.name as module_name,
                m.key1 as module_key1,
                m.key2 as module_key2,
                m.key3 as module_key3,
                cat.name as category_name,
                cat.key1 as category_key1,
                cat.key2 as category_key2,
                cat.key3 as category_key3,
                s.name as status_name,
                p.name as parent_name
            ')
            ->join('modules m', 'm.id = c.module_id', 'left')
            ->join('category cat', 'cat.id = c.category_id', 'left')
            ->join('status s', 's.id = c.status_id', 'left')
            ->join('contents p', 'p.id = c.main_content_id', 'left')
            ->where('c.date_deleted', null)
            ->where(date_format(date('c.date_created'),'%Y'),$year)
            ->where('c.main_content_id', null)
            ->groupStart()
                ->where('LOWER(cat.name)', strtolower($category))
                ->orWhere('LOWER(cat.key1)', strtolower($category))
                ->orWhere('LOWER(cat.key2)', strtolower($category))
                ->orWhere('LOWER(cat.key3)', strtolower($category))
            ->groupEnd()
            ->groupStart()
                ->where('s.name', 'Published')
                ->orWhere('s.name', 'Approved')
                ->orWhere('s.name', 'Active')
            ->groupEnd()
            ->orderBy('c.rank', 'ASC')
            ->orderBy('c.id', 'DESC')
            ->get()
            ->getResultArray();

        $rows = $this->attachSubsections($rows);

        return $this->response->setJSON([
            'status' => true,
            'category' => $category,
            'count' => count($rows),
            'data' => $rows,
        ]);
    }

    public function byCategoryKeys($category, $key1, $key2, $key3)
    {
        $db = db_connect();

        $rows = $db->table('contents c')
            ->select('
                c.id,
                c.main_content_id,
                c.name,
                c.description,
                c.body,
                c.image_path,
                c.image_url,
                c.external_link,
                c.tags,
                c.rank,
                c.validity_date_start,
                c.validity_date_end,
                c.date_created,
                c.date_updated,
                m.name as module_name,
                m.key1 as module_key1,
                m.key2 as module_key2,
                m.key3 as module_key3,
                cat.name as category_name,
                cat.key1 as category_key1,
                cat.key2 as category_key2,
                cat.key3 as category_key3,
                s.name as status_name,
                p.name as parent_name
            ')
            ->join('modules m', 'm.id = c.module_id', 'left')
            ->join('category cat', 'cat.id = c.category_id', 'left')
            ->join('status s', 's.id = c.status_id', 'left')
            ->join('contents p', 'p.id = c.main_content_id', 'left')
            ->where('c.date_deleted', null)
            ->where('c.main_content_id', null)
            ->groupStart()
                ->where('LOWER(cat.name)', strtolower($category))
                ->orWhere('LOWER(cat.key1)', strtolower($category))
                ->orWhere('LOWER(cat.key2)', strtolower($category))
                ->orWhere('LOWER(cat.key3)', strtolower($category))
            ->groupEnd()
            ->where('LOWER(m.key1)', strtolower($key1))
            ->where('LOWER(m.key2)', strtolower($key2))
            ->where('LOWER(m.key3)', strtolower($key3))
            ->groupStart()
                ->where('s.name', 'Published')
                ->orWhere('s.name', 'Approved')
                ->orWhere('s.name', 'Active')
            ->groupEnd()
            ->orderBy('c.rank', 'ASC')
            ->orderBy('c.id', 'DESC')
            ->get()
            ->getResultArray();

        $rows = $this->attachSubsections($rows);

        return $this->response->setJSON([
            'status' => true,
            'category' => $category,
            'key1' => $key1,
            'key2' => $key2,
            'key3' => $key3,
            'count' => count($rows),
            'data' => $rows,
        ]);
    }

    private function attachSubsections(array $rows): array
    {
        if (empty($rows)) {
            return [];
        }

        $db = db_connect();
        $ids = array_column($rows, 'id');

        $subsections = $db->table('contents c')
            ->select('
                c.id,
                c.main_content_id,
                c.name,
                c.description,
                c.body,
                c.image_path,
                c.image_url,
                c.external_link,
                c.tags,
                c.rank,
                c.validity_date_start,
                c.validity_date_end,
                c.date_created,
                c.date_updated
            ')
            ->whereIn('c.main_content_id', $ids)
            ->where('c.date_deleted', null)
            ->orderBy('c.rank', 'ASC')
            ->orderBy('c.id', 'ASC')
            ->get()
            ->getResultArray();

        $grouped = [];
        foreach ($subsections as $sub) {
            $parentId = (int) $sub['main_content_id'];
            $grouped[$parentId][] = $sub;
        }

        foreach ($rows as &$row) {
            $row['subsections'] = $grouped[(int) $row['id']] ?? [];
            $row['tags'] = $this->decodeTags($row['tags'] ?? null);
        }
        unset($row);

        foreach ($subsections as &$sub) {
            $sub['tags'] = $this->decodeTags($sub['tags'] ?? null);
        }
        unset($sub);

        return $rows;
    }

    private function decodeTags($tags): array
    {
        if (empty($tags)) {
            return [];
        }

        $decoded = json_decode((string) $tags, true);
        return is_array($decoded) ? $decoded : [];
    }
}