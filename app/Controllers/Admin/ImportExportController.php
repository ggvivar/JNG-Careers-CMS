<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ImportExportController extends BaseController
{
    public function export($entity)
    {
        $format = strtolower((string) ($this->request->getGet('format') ?? 'csv'));

        return redirect()->back()->with('success', 'Export for "' . $entity . '" in ' . strtoupper($format) . ' is ready for implementation.');
    }

    public function import($entity)
    {
        $file = $this->request->getFile('import_file');
        $format = strtolower((string) $this->request->getPost('format'));

        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'Please upload a valid file.');
        }

        return redirect()->back()->with('success', 'Import for "' . $entity . '" (' . strtoupper($format) . ') is ready for implementation.');
    }
}