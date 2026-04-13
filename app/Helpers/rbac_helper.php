<?php

if (! function_exists('rbac_is_super_admin')) {
    function rbac_is_super_admin(): bool
    {
        $roleId = (int) (session()->get('admin_role_id') ?? 0);
        if (! $roleId) {
            return false;
        }

        $row = db_connect()->table('roles')
            ->select('name')
            ->where('id', $roleId)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        return $row && strtolower(trim((string) $row['name'])) === 'super admin';
    }
}

if (! function_exists('rbac_can_feature')) {
    function rbac_can_feature(string $featureCode, string $permission = 'can_view'): bool
    {
        if (rbac_is_super_admin()) {
            return true;
        }

        $allowed = ['can_view', 'can_add', 'can_edit', 'can_delete', 'can_export', 'can_import'];
        if (! in_array($permission, $allowed, true)) {
            return false;
        }

        $roleId = (int) (session()->get('admin_role_id') ?? 0);
        if (! $roleId) {
            return false;
        }
        
        $row = db_connect()->table('role_feature_permissions rfp')
            ->select('rfp.' . $permission)
            ->join('features f', 'f.id = rfp.feature_id', 'inner')
            ->where('rfp.role_id', $roleId)
            ->where('LOWER(TRIM(f.code))', strtolower(trim($featureCode)))
            ->where('f.date_deleted', null)
            ->get()
            ->getRowArray();
        // dd( $row);
        return $row && ! empty($row[$permission]);
    }
}

if (! function_exists('rbac_features_by_module')) {
    function rbac_features_by_module(string $moduleName): array
    {
        if (rbac_is_super_admin()) {
            return db_connect()->table('features f')
                ->select('f.*')
                ->join('modules m', 'm.id = f.module_id', 'inner')
                ->where('LOWER(TRIM(m.name))', strtolower(trim($moduleName)))
                ->where('f.date_deleted', null)
                ->orderBy('f.sort_order', 'ASC')
                ->get()
                ->getResultArray();
        }

        $roleId = (int) (session()->get('admin_role_id') ?? 0);
        if (! $roleId) {
            return [];
        }

        return db_connect()->table('role_feature_permissions rfp')
            ->select('f.*')
            ->join('features f', 'f.id = rfp.feature_id', 'inner')
            ->join('modules m', 'm.id = f.module_id', 'inner')
            ->where('rfp.role_id', $roleId)
            ->where('rfp.can_view', 1)
            ->where('LOWER(TRIM(m.name))', strtolower(trim($moduleName)))
            ->where('f.date_deleted', null)
            ->orderBy('f.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }
}

// function rbac_can($featureCode, $permission = 'can_view')
// {
//     $roleId = session()->get('admin_role_id');

//     if (!$roleId) {
//         return false;
//     }

//     $cacheKey = 'rbac_'.$roleId;

//     $permissions = cache($cacheKey);

//     if (!$permissions) {

//         $db = db_connect();

//         $rows = $db->table('role_feature_permissions p')
//             ->select('f.code,p.can_view,p.can_add,p.can_edit,p.can_delete,p.can_export,p.can_import')
//             ->join('features f','f.id=p.feature_id')
//             ->where('p.role_id',$roleId)
//             ->get()
//             ->getResultArray();

//         $permissions = [];

//         foreach($rows as $r){
//             $permissions[$r['code']] = $r;
//         }

//         cache()->save($cacheKey,$permissions,3600);
//     }

//     return !empty($permissions[$featureCode][$permission]);
// }