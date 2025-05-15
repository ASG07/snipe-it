<?php

namespace App\Presenters;

use App\Models\Access;
use App\Models\Setting;

/**
 * Class AccessPresenter
 */
class AccessPresenter extends Presenter
{
    /**
     * Json Column Layout for bootstrap table
     * @return string
     */
    public static function dataTableLayout()
    {
        $layout = [
            [
                'field' => 'checkbox',
                'checkbox' => true
            ],
            [
                'field' => 'id',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.id'),
                'visible' => false
            ],
            [
                'field' => 'name',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.name'),
                'visible' => true,
                'formatter' => 'accessLinkFormatter',
            ],
            [
                'field' => 'access_tag',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/access/general.access_tag'),
                'visible' => true,
                'formatter' => 'accessLinkFormatter',
            ],
            [
                'field' => 'username',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/access/general.username'),
                'visible' => true,
            ],
            [
                'field' => 'url',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/access/general.url'),
                'visible' => true,
                'formatter' => 'urlFormatter',
            ],
            [
                'field' => 'expiration_date',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/access/general.expiration_date'),
                'visible' => true,
                'formatter' => 'dateDisplayFormatter',
            ],
            [
                'field' => 'status',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.status'),
                'visible' => true,
                'formatter' => 'statuslabelFormatter',
            ],
            [
                'field' => 'assigned_to',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.assigned_to'),
                'visible' => true,
                'formatter' => 'assignedToFormatter',
            ],
            [
                'field' => 'company',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.company'),
                'visible' => Setting::getSettings()->show_company_in_list == '1',
                'formatter' => 'companiesLinkObjFormatter',
            ],
            [
                'field' => 'category',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.category'),
                'visible' => true,
                'formatter' => 'categoriesLinkObjFormatter',
            ],
            [
                'field' => 'notes',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.notes'),
                'visible' => true,
            ],
            [
                'field' => 'created_at',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.created_at'),
                'visible' => false,
                'formatter' => 'dateDisplayFormatter',
            ],
            [
                'field' => 'updated_at',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('general.updated_at'),
                'visible' => false,
                'formatter' => 'dateDisplayFormatter',
            ],
            [
                'field' => 'actions',
                'searchable' => false,
                'sortable' => false,
                'switchable' => false,
                'title' => trans('table.actions'),
                'visible' => true,
                'formatter' => 'accessActionsFormatter',
            ]
        ];

        return json_encode($layout);
    }

    /**
     * Pregenerated link to this access
     * @return string
     */
    public function nameUrl()
    {
        return (string) link_to_route('access.show', $this->name, $this->id);
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('access.show', $this->id);
    }
} 