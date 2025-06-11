<?php

namespace App\Presenters;

/**
 * Class LicensePresenter
 */
class LicenseSeatPresenter extends Presenter
{
    public function name()
    {
        return $this->model->license->name;
    }

    /**
     * Json Column Layout for bootstrap table for License Seat audits
     * @return string
     */
    public static function dataTableLayoutAudit()
    {
        $layout = [
            [
                'field' => 'checkbox',
                'checkbox' => true,
                'titleTooltip' => trans('general.select_all_none'),
            ],
            [
                'field' => 'id',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.id'),
                'visible' => false,
            ],
            [
                'field' => 'license_name',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/licenses/form.name'),
                'visible' => true,
                'formatter' => 'licenseLinkFormatter',
            ],
            [
                'field' => 'license_serial',
                'searchable' => true,
                'sortable' => true,
                'title' => "Serial",
                'visible' => true,
            ],
            [
                'field' => 'assigned_user',
                'searchable' => true,
                'sortable' => true,
                'title' => "Checked out to",
                'visible' => true,
                'formatter' => 'polymorphicItemFormatter',
            ],
            [
                'field' => 'asset',
                'searchable' => true,
                'sortable' => true,
                'title' => "Asset",
                'visible' => true,
                'formatter' => 'licenseSeatAssetFormatter',
            ],
            [
                'field' => 'location',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/hardware/table.location'),
                'visible' => true,
                'formatter' => 'deployedLocationFormatter',
            ],
            [
                'field' => 'last_audit_date',
                'searchable' => false,
                'sortable' => true,
                'visible' => true,
                'title' => trans('general.last_audit'),
                'formatter' => 'dateDisplayFormatter',
            ],
            [
                'field' => 'next_audit_date',
                'searchable' => false,
                'sortable' => true,
                'visible' => true,
                'title' => trans('general.next_audit_date'),
                'formatter' => 'dateDisplayFormatter',
            ],
            [
                'field' => 'notes',
                'searchable' => true,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.notes'),
            ],
            [
                'field' => 'actions',
                'searchable' => false,
                'sortable' => false,
                'switchable' => false,
                'title' => trans('table.actions'),
                'visible' => true,
                'formatter' => 'licenseSeatAuditActionsFormatter',
            ],
        ];

        return json_encode($layout);
    }
}
