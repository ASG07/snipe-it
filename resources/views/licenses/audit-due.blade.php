@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans_choice('general.audit_due_days', $settings->audit_warning_days, ['days' => $settings->audit_warning_days]) }}
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

                    <div class="tab-pane active" id="due">

                        @include('partials.license-bulk-actions',
                            [
                                'id_divname'  => 'dueLicenseEditToolbar',
                                'id_formname' => 'dueLicenseEditForm',
                                'id_button'   => 'dueLicenseEditButton'])

                    <div class="row">
                            <div class="table table-responsive">
                        <div class="col-md-12">
                            <table
                                    data-click-to-select="true"
                                    data-columns="{{ \App\Presenters\LicensePresenter::dataTableLayout() }}"
                                            data-cookie-id-table="dueLicenseAuditListing"
                                    data-pagination="true"
                                            data-id-table="dueLicenseAuditListing"
                                    data-search="true"
                                    data-side-pagination="server"
                                    data-show-columns="true"
                                    data-show-fullscreen="true"
                                    data-show-export="true"
                                    data-show-footer="true"
                                    data-show-refresh="true"
                                    data-sort-order="asc"
                                    data-sort-name="name"
                                            data-toolbar="#dueLicenseEditToolbar"
                                            data-bulk-button-id="#dueLicenseEditButton"
                                            data-bulk-form-id="#dueLicenseEditForm"
                                            id="#dueLicenseAuditListing"
                                    class="table table-striped snipe-table"
                                            data-url="{{ route('api.licenses.list-upcoming', ['action' => 'audits', 'upcoming_status' => 'due']) }}"
                                    data-export-options='{
                "fileName": "export-licenses-due-audit-{{ date('Y-m-d') }}",
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
                                    'id_divname'  => 'overdueLicenseEditToolbar',
                                    'id_formname' => 'overdueLicenseEditForm',
                                    'id_button'   => 'overdueLicenseEditButton'])

                        <div class="row">
                            <div class="table table-responsive">
                                <div class="col-md-12">
                                    <table
                                        data-click-to-select="true"
                                        data-columns="{{ \App\Presenters\LicensePresenter::dataTableLayout() }}"
                                        data-cookie-id-table="overdueLicenseAuditListing"
                                        data-pagination="true"
                                        data-id-table="overdueLicenseAuditListing"
                                        data-search="true"
                                        data-side-pagination="server"
                                        data-show-columns="true"
                                        data-show-fullscreen="true"
                                        data-show-export="true"
                                        data-show-footer="true"
                                        data-show-refresh="true"
                                        data-sort-order="asc"
                                        data-sort-name="name"
                                        data-toolbar="#overdueLicenseEditToolbar"
                                        data-bulk-button-id="#overdueLicenseEditButton"
                                        data-bulk-form-id="#overdueLicenseEditForm"
                                        id="#overdueLicenseAuditListing"
                                        class="table table-striped snipe-table"
                                        data-url="{{ route('api.licenses.list-upcoming', ['action' => 'audits', 'upcoming_status' => 'overdue']) }}"
                                        data-export-options='{
            "fileName": "export-licenses-overdue-audit-{{ date('Y-m-d') }}",
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
@stop 