<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class ContentController extends BaseController
{
    public function all()
    {
        $rows = $this->baseQuery()
            ->get()
            ->getResultArray();

        return $this->respondWithSubsections($rows);
    }

    // public function byCategory($category=null)
    // {   
    //     // dd($category);
    //     $rows = $this->baseQuery()
    //         ->groupStart()
    //             ->where('LOWER(cat.name)', strtolower($category))
    //             ->orWhere('LOWER(cat.key1)', strtolower($category))
    //             ->orWhere('LOWER(cat.key2)', strtolower($category))
    //             ->orWhere('LOWER(cat.key3)', strtolower($category))
    //         ->groupEnd()
    //         ->get()
    //         ->getResultArray();

    //     return $this->respondWithSubsections($rows, [
    //         'category' => $category
    //     ]);
    // }
    public function byCategory($category = null)
{
    $query = $this->baseQuery();

    if (!empty($category)) {
        $rows = $query
            ->groupStart()
                ->where('LOWER(cat.name)', strtolower($category))
                ->orWhere('LOWER(cat.key1)', strtolower($category))
                ->orWhere('LOWER(cat.key2)', strtolower($category))
                ->orWhere('LOWER(cat.key3)', strtolower($category))
            ->groupEnd()
            ->get()
            ->getResultArray();

        return $this->respondWithSubsections($rows, [
            'category' => $category
        ]);
    }

    $rows = $query->get()->getResultArray();

    return $this->respondGroupedByCategoryAndYear($rows);
}
private function respondGroupedByCategoryAndYear(array $rows)
{
    $rows = $this->attachSubsections($rows);

    $grouped = [];

    foreach ($rows as $row) {
        $categoryName = strtolower($row['category_name'] ?? 'uncategorized');
        $year = !empty($row['validity_date_start']) ? (int) date('Y', strtotime($row['validity_date_start'])) : null;
        // dd($row);
        if (!$year) {
            continue;
        }

        if (!isset($grouped[$categoryName][$year])) {
            $grouped[$categoryName][$year] = [
                'id' => $year,
                'year' => $year,
                'items' => []
            ];
        }

        $grouped[$categoryName][$year]['items'][] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'slug' => $row['slug'],
            'lead' => $row['lead'],
            'description' => $row['description'],
            'image' => $row['image'],
            'date' => !empty($row['validity_date_start']) ? date('Y-m-d', strtotime($row['validity_date_start'])) : null,
            'tags' => $row['tags'] ?? [],
            'rank' => $row['rank'],
            'sections' => array_map(function ($section) {
                return [
                    'id' => $section['id'],
                    'title' => $section['title'],
                    'description' => $section['description'],
                    'image' => $section['image'],
                    'date' => !empty($section['validity_date_start']) ? date('Y-m-d', strtotime($section['validity_date_start'])) : null,
                ];
            }, $row['subsections'] ?? [])
        ];
    }

    $result = [];

    // foreach ($grouped as $categoryName => $years) {
    //     krsort($years); // latest year first

    //     //  foreach ($years as $yearData) {
    //     //     $result[] = [
    //     //         'category_name' => $categoryName,
    //     //         'years' => $yearData
    //     //     ];
    //     // }
    //     // foreach ($years as $yearData) {
    //         $result[] = [
    //             'category_name' => $categoryName,
    //             'years' => $years
    //         ];
    //     // }
    // }
        foreach ($grouped as $categoryName => $years) {
            krsort($years); // latest year first

            $result[] = [
                'category_name' => $categoryName,
                'years' => array_values($years) 
            ];
        }
    return $this->response->setJSON([
        'status' => true,
        'category' => 'category',
        'data' => $result
    ]);
}
    public function year($year)
    {
        $year = (int) $year;

        $rows = $this->baseQuery()
            ->where('c.date_created >=', $year . '-01-01 00:00:00')
            ->where('c.date_created <=', $year . '-12-31 23:59:59')
            ->get()
            ->getResultArray();

        return $this->respondWithSubsections($rows, [
            'year' => $year
        ]);
    }

    public function byCategoryKeys($category, $key1, $key2, $key3)
    {
        $rows = $this->baseQuery()
            ->groupStart()
                ->where('LOWER(cat.name)', strtolower($category))
                ->orWhere('LOWER(cat.key1)', strtolower($category))
                ->orWhere('LOWER(cat.key2)', strtolower($category))
                ->orWhere('LOWER(cat.key3)', strtolower($category))
            ->groupEnd()
            ->where('LOWER(m.key1)', strtolower($key1))
            ->where('LOWER(m.key2)', strtolower($key2))
            ->where('LOWER(m.key3)', strtolower($key3))
            ->get()
            ->getResultArray();

        return $this->respondWithSubsections($rows, [
            'category' => $category,
            'key1' => $key1,
            'key2' => $key2,
            'key3' => $key3,
        ]);
    }

    /**
     * 🔥 BASE QUERY (reuse everywhere)
     */
    private function baseQuery()
    {
        $db = db_connect();

        return $db->table('contents c')
            ->select('
                c.id,
                c.main_content_id,
                c.name as title,
                c.slug,
                c.description as lead,
                c.body as description,
                c.image_path,
                c.image_url as image,
                c.external_link as link,
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
                ->whereIn('s.name', ['Published'])
            ->groupEnd()
            ->orderBy('c.rank', 'ASC')
            ->orderBy('c.id', 'DESC');
    }

    private function respondWithSubsections(array $rows, array $extra = [])
    {
        $rows = $this->attachSubsections($rows);

        return $this->response->setJSON(array_merge([
            'status' => true,
            'count' => count($rows),
            'data' => $rows,
        ], $extra));
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
                c.name as title,
                c.slug,
                c.description as lead,
                c.body as description,
                c.image_path,
                c.image_url as image,
                c.external_link as link,
                c.tags,
                c.rank,
                c.validity_date_start,
                c.validity_date_end,
                c.date_created
            ')
            ->whereIn('c.main_content_id', $ids)
            ->where('c.date_deleted', null)
            ->orderBy('c.rank', 'ASC')
            ->orderBy('c.id', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($subsections as &$sub) {
            $sub['tags'] = $this->decodeTags($sub['tags'] ?? null);
        }
        unset($sub);

        $grouped = [];
        foreach ($subsections as $sub) {
            $grouped[$sub['main_content_id']][] = $sub;
        }

        foreach ($rows as &$row) {
            $row['tags'] = $this->decodeTags($row['tags'] ?? null);
            $row['subsections'] = $grouped[$row['id']] ?? [];
        }
        unset($row);

        return $rows;
    }

    private function decodeTags($tags): array
    {
        if (empty($tags)) return [];

        $decoded = json_decode((string) $tags, true);
        return is_array($decoded) ? $decoded : [];
    }
}