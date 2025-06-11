@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('admin/licenses/general.license_seats') }}
@stop

{{-- Page content --}}
@section('content')
    {{-- Page content --}}
    <div class="row">
        <div class="col-md-12">

            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs hidden-print">
                    
                    <li class="active">
                        <a href="#all" data-toggle="tab">{{ trans('general.all') }}
                            <span class="hidden-lg hidden-md">
                            <i class="far fa-file fa-2x" aria-hidden="true"></i>
                          </span>
                            <span class="badge">{{ (isset($total_all_seats)) ? $total_all_seats : '' }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#due" data-toggle="tab">{{ trans('general.audit_due') }}
                          <span class="hidden-lg hidden-md">
                            <i class="far fa-file fa-2x" aria-hidden="true"></i>
                          </span>
                            <span class="badge">{{ (isset($total_due_for_audit)) ? $total_due_for_audit : '' }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#overdue" data-toggle="tab">{{ trans('general.audit_overdue') }}
                            <span class="hidden-lg hidden-md">
                            <i class="far fa-file fa-2x" aria-hidden="true"></i>
                          </span>
                            <span class="badge">{{ (isset($total_overdue_for_audit)) ? $total_overdue_for_audit : '' }}</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">

                    <div class="tab-pane active" id="all">

                        @include('partials.license-bulk-actions',
                                [
                                    'id_divname'  => 'allLicenseSeatEditToolbar',
                                    'id_formname' => 'allLicenseSeatEditForm',
                                    'id_button'   => 'allLicenseSeatEditButton',
                                    'bulk_action_route' => route('license_seats.bulkaudit')
                                ])

                        <!-- Minimalistic Date Range Filter -->
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-12">
                                <div class="well well-sm" style="margin-bottom: 10px; padding: 10px;">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <small class="text-muted" style="line-height: 30px; font-weight: 500;">{{ trans('general.last_audit') }} {{ trans('general.range') }}:</small>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="date" class="form-control" id="all_audit_date_start" placeholder="{{ trans('general.start_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="date" class="form-control" id="all_audit_date_end" placeholder="{{ trans('general.end_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="applyDateFilter('all')">
                                                <i class="fa fa-filter"></i> {{ trans('general.filter') }}
                                            </button>
                                            <button type="button" class="btn btn-default btn-sm" onclick="clearDateFilter('all')">
                                                <i class="fa fa-times"></i> {{ trans('general.clear') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="table table-responsive">
                                <div class="col-md-12">
                                    <table
                                        data-click-to-select="true"
                                        data-columns="{{ \App\Presenters\LicenseSeatPresenter::dataTableLayoutAudit() }}"
                                        data-cookie-id-table="allLicenseSeatAuditListing"
                                        data-pagination="true"
                                        data-id-table="allLicenseSeatAuditListing"
                                        data-search="true"
                                        data-side-pagination="server"
                                        data-show-columns="true"
                                        data-show-fullscreen="true"
                                        data-show-export="true"
                                        data-show-footer="true"
                                        data-show-refresh="true"
                                        data-sort-order="asc"
                                        data-sort-name="license_name"
                                        data-toolbar="#allLicenseSeatEditToolbar"
                                        data-bulk-button-id="#allLicenseSeatEditButton"
                                        data-bulk-form-id="#allLicenseSeatEditForm"
                                        id="allLicenseSeatAuditListing"
                                        class="table table-striped snipe-table"
                                        data-url="{{ route('api.license_seats.all') }}"
                                        data-export-options='{
                                            "fileName": "export-license-seats-all-{{ date('Y-m-d') }}",
                                            "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                                        }'>
                                    </table>
                                </div> <!-- end col-md-12 -->
                            </div><!-- end table-responsive -->
                        </div><!-- end row -->
                    </div><!-- end tab-pane -->

                    <div class="tab-pane" id="due">

                        @include('partials.license-bulk-actions',
                            [
                                'id_divname'  => 'dueLicenseSeatEditToolbar',
                                'id_formname' => 'dueLicenseSeatEditForm',
                                'id_button'   => 'dueLicenseSeatEditButton',
                                'bulk_action_route' => route('license_seats.bulkaudit')
                            ])

                        <!-- Minimalistic Date Range Filter -->
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-12">
                                <div class="well well-sm" style="margin-bottom: 10px; padding: 10px;">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <small class="text-muted" style="line-height: 30px; font-weight: 500;">{{ trans('general.last_audit') }} {{ trans('general.range') }}:</small>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="date" class="form-control" id="due_audit_date_start" placeholder="{{ trans('general.start_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="date" class="form-control" id="due_audit_date_end" placeholder="{{ trans('general.end_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="applyDateFilter('due')">
                                                <i class="fa fa-filter"></i> {{ trans('general.filter') }}
                                            </button>
                                            <button type="button" class="btn btn-default btn-sm" onclick="clearDateFilter('due')">
                                                <i class="fa fa-times"></i> {{ trans('general.clear') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="table table-responsive">
                                <div class="col-md-12">
                                    <table
                                        data-click-to-select="true"
                                        data-columns="{{ \App\Presenters\LicenseSeatPresenter::dataTableLayoutAudit() }}"
                                        data-cookie-id-table="dueLicenseSeatAuditListing"
                                        data-pagination="true"
                                        data-id-table="dueLicenseSeatAuditListing"
                                        data-search="true"
                                        data-side-pagination="server"
                                        data-show-columns="true"
                                        data-show-fullscreen="true"
                                        data-show-export="true"
                                        data-show-footer="true"
                                        data-show-refresh="true"
                                        data-sort-order="asc"
                                        data-sort-name="license_name"
                                        data-toolbar="#dueLicenseSeatEditToolbar"
                                        data-bulk-button-id="#dueLicenseSeatEditButton"
                                        data-bulk-form-id="#dueLicenseSeatEditForm"
                                        id="dueLicenseSeatAuditListing"
                                        class="table table-striped snipe-table"
                                        data-url="{{ route('api.license_seats.due') }}"
                                        data-export-options='{
                                            "fileName": "export-license-seats-due-audit-{{ date('Y-m-d') }}",
                                            "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                                        }'>
                                    </table>
                                </div> <!-- end col-md-12 -->
                            </div><!-- end table-responsive -->
                        </div><!-- end row -->
                    </div><!-- end tab-pane -->

                    <div class="tab-pane" id="overdue">

                        @include('partials.license-bulk-actions',
                                [
                                    'id_divname'  => 'overdueLicenseSeatEditToolbar',
                                    'id_formname' => 'overdueLicenseSeatEditForm',
                                    'id_button'   => 'overdueLicenseSeatEditButton',
                                    'bulk_action_route' => route('license_seats.bulkaudit')
                                ])

                        <!-- Minimalistic Date Range Filter -->
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-12">
                                <div class="well well-sm" style="margin-bottom: 10px; padding: 10px;">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <small class="text-muted" style="line-height: 30px; font-weight: 500;">{{ trans('general.last_audit') }} {{ trans('general.range') }}:</small>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="date" class="form-control" id="overdue_audit_date_start" placeholder="{{ trans('general.start_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="date" class="form-control" id="overdue_audit_date_end" placeholder="{{ trans('general.end_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="applyDateFilter('overdue')">
                                                <i class="fa fa-filter"></i> {{ trans('general.filter') }}
                                            </button>
                                            <button type="button" class="btn btn-default btn-sm" onclick="clearDateFilter('overdue')">
                                                <i class="fa fa-times"></i> {{ trans('general.clear') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="table table-responsive">
                                <div class="col-md-12">
                                    <table
                                        data-click-to-select="true"
                                        data-columns="{{ \App\Presenters\LicenseSeatPresenter::dataTableLayoutAudit() }}"
                                        data-cookie-id-table="overdueLicenseSeatAuditListing"
                                        data-pagination="true"
                                        data-id-table="overdueLicenseSeatAuditListing"
                                        data-search="true"
                                        data-side-pagination="server"
                                        data-show-columns="true"
                                        data-show-fullscreen="true"
                                        data-show-export="true"
                                        data-show-footer="true"
                                        data-show-refresh="true"
                                        data-sort-order="asc"
                                        data-sort-name="license_name"
                                        data-toolbar="#overdueLicenseSeatEditToolbar"
                                        data-bulk-button-id="#overdueLicenseSeatEditButton"
                                        data-bulk-form-id="#overdueLicenseSeatEditForm"
                                        id="overdueLicenseSeatAuditListing"
                                        class="table table-striped snipe-table"
                                        data-url="{{ route('api.license_seats.overdue') }}"
                                        data-export-options='{
                                            "fileName": "export-license-seats-overdue-audit-{{ date('Y-m-d') }}",
                                            "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                                        }'>
                                    </table>
                                </div> <!-- end col-md-12 -->
                            </div><!-- end table-responsive -->
                        </div><!-- end row -->
                    </div><!-- end tab-pane -->
                </div><!-- end tab-content -->
            </div><!-- end nav-tabs-custom -->

        </div><!-- /.col -->
    </div><!-- /.row -->

@stop

@section('moar_scripts')
    @include('partials.bootstrap-table')
    <script>
        function applyDateFilter(tabType) {
            var startDate = document.getElementById(tabType + '_audit_date_start').value;
            var endDate = document.getElementById(tabType + '_audit_date_end').value;
            
            var table = $('#' + tabType + 'LicenseSeatAuditListing');
            var url = table.bootstrapTable('getOptions').url;
            
            // remove existing date parameters
            url = url.split('?')[0];
            
            // add date parameters if they exist
            var params = [];
            if (startDate) {
                params.push('audit_date_start=' + encodeURIComponent(startDate));
            }
            if (endDate) {
                params.push('audit_date_end=' + encodeURIComponent(endDate));
            }
            
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            
            table.bootstrapTable('refresh', {
                url: url
            });
        }
        
        function clearDateFilter(tabType) {
            document.getElementById(tabType + '_audit_date_start').value = '';
            document.getElementById(tabType + '_audit_date_end').value = '';
            
            var table = $('#' + tabType + 'LicenseSeatAuditListing');
            var url = table.bootstrapTable('getOptions').url;
            
            // remove existing date parameters
            url = url.split('?')[0];
            
            table.bootstrapTable('refresh', {
                url: url
            });
        }
    </script>
@stop 