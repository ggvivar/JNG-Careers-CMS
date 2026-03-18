<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFeature implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('admin_id')) {
            return redirect()->to('/admin/login');
        }

        $featureCode = $arguments[0] ?? null;
        $permission = $arguments[1] ?? 'can_view';

        if (! $featureCode) {
            return redirect()->to('/admin')->with('error', 'Feature rule missing.');
        }

        helper('rbac');

        if (! rbac_can_feature($featureCode, $permission)) {
            return redirect()->to('/admin')->with('error', 'Access denied.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}