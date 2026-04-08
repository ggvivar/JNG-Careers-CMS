<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class CommonController extends BaseController
{
    protected string $modelClass;
    protected string $viewPath;
    protected string $baseRoute;

    protected function getModel()
    {
        return new $this->modelClass();
    }

    protected function getListParams(): array
    {
        return [
            'q' => $this->request->getGet('q'),
            'sortBy' => $this->request->getGet('sortBy'),
            'sortDirection' => $this->request->getGet('sortDirection'),
            'page' => $this->request->getGet('page') ?? 1,
            'perPage' => 10,
        ];
    }

    protected function buildPagination(array $result): string
    {
        return service('pager')->makeLinks(
            $result['page'],
            $result['perPage'],
            $result['total']
        );
    }

    public function index()
    {
        $model = $this->getModel();

        $params = $this->getListParams();

        $result = $model->list($params);

        return view($this->viewPath . '/index', [
            'rows' => $result['rows'],
            'searchQuery' => $params['q'] ?? '',
            'paginationLinks' => $this->buildPagination($result),

            'sortBy' => $params['sortBy'] ?? 'name',
            'sortDirection' => $params['sortDirection'] ?? 'asc',
            'sortOptions' => method_exists($model, 'sortOptions')
                ? $model->sortOptions()
                : [],
        ]);
    }
}