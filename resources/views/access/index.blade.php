@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('general.access') }}
@parent
@stop

@section('header_right')
    @can('create', \App\Models\Access::class)
        <a href="{{ route('access.create') }}" class="btn btn-primary pull-right"> {{ trans('general.create') }}</a>
    @endcan
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-body">
                <div class="table-responsive">
                    <table
                        data-columns="{{ \App\Presenters\AccessPresenter::dataTableLayout() }}"
                        data-cookie-id-table="accessTable"
                        data-pagination="true"
                        data-search="true"
                        data-side-pagination="server"
                        data-show-columns="true"
                        data-show-export="true"
                        data-show-footer="true"
                        data-show-refresh="true"
                        data-sort-order="asc"
                        data-sort-name="name"
                        id="accessTable"
                        class="table table-striped snipe-table"
                        data-url="{{ route('api.access.index') }}"
                        data-export-options='{
                            "fileName": "export-access-{{ date('Y-m-d') }}",
                            "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                            }'>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('moar_scripts')
@include('partials.bootstrap-table')
@stop 