<?php

namespace App\Http\Controllers;

abstract class Controller
{
    function paginate($paginator)
    {
        return [
            'items' => $paginator->items(),
            'pagiantor' => [
                'totalRecords' => $paginator->total(),
                'first' => $paginator->perPage() * ($paginator->currentPage() - 1),
                'rows' => $paginator->perPage(),
                'rowsPerPageOptions' => [10, 20, 30],
                'currentPageReportTemplate' => __('messages.paginatorTemplate'),
                'template' => 'CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown',
            ]
        ];
    }
}
