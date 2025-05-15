@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('admin/access/general.view') }} - {{ $access->access_tag }}
@parent
@stop

@section('header_right')
<div class="btn-group pull-right">
  @can('update', $access)
    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">{{ trans('button.actions') }}
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        @if ($access->assigned_to != '')
            @can('checkin', $access)
                <li><a href="{{ route('access.checkin.show', $access->id) }}">{{ trans('admin/access/general.checkin') }}</a></li>
            @endcan
        @else
            @can('checkout', $access)
                <li><a href="{{ route('access.checkout.show', $access->id) }}">{{ trans('admin/access/general.checkout') }}</a></li>
            @endcan
        @endif
        @can('update', $access)
            <li><a href="{{ route('access.edit', $access->id) }}">{{ trans('admin/access/general.edit') }}</a></li>
        @endcan
    </ul>
  @endcan
</div>
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#details" data-toggle="tab">
                        <span class="hidden-lg hidden-md">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </span>
                        <span class="hidden-xs hidden-sm">{{ trans('general.details') }}</span>
                    </a>
                </li>
                <li>
                    <a href="#history" data-toggle="tab">
                        <span class="hidden-lg hidden-md">
                            <i class="fas fa-history fa-2x"></i>
                        </span>
                        <span class="hidden-xs hidden-sm">{{ trans('general.history') }}</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="details">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>{{ trans('general.name') }}</td>
                                            <td>{{ $access->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ trans('admin/access/general.access_tag') }}</td>
                                            <td>{{ $access->access_tag }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ trans('admin/access/general.username') }}</td>
                                            <td>{{ $access->username }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ trans('admin/access/general.url') }}</td>
                                            <td>
                                                @if ($access->url)
                                                    <a href="{{ $access->url }}" target="_blank">{{ $access->url }}</a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ trans('admin/access/general.expiration_date') }}</td>
                                            <td>
                                                @if ($access->expiration_date)
                                                    {{ \App\Helpers\Helper::getFormattedDateObject($access->expiration_date, 'date') }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ trans('general.status') }}</td>
                                            <td>
                                                @if ($access->assetstatus)
                                                    <span class="label label-default" style="background-color: {{ $access->assetstatus->color }}">
                                                        {{ $access->assetstatus->name }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ trans('general.assigned_to') }}</td>
                                            <td>
                                                @if ($access->assigned_to)
                                                    @if ($access->assignedto->deleted_at=='')
                                                        <a href="{{ route('users.show', $access->assignedto->id) }}">
                                                            {{ $access->assignedto->present()->fullName() }}
                                                        </a>
                                                    @else
                                                        <del>{{ $access->assignedto->present()->fullName() }}</del>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ trans('general.company') }}</td>
                                            <td>
                                                @if ($access->company)
                                                    <a href="{{ route('companies.show', $access->company->id) }}">
                                                        {{ $access->company->name }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ trans('general.category') }}</td>
                                            <td>
                                                @if ($access->category)
                                                    <a href="{{ route('categories.show', $access->category->id) }}">
                                                        {{ $access->category->name }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ trans('general.notes') }}</td>
                                            <td>
                                                {!! nl2br(e($access->notes)) !!}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- End first column -->

                        <div class="col-md-4">
                            @if ($access->created_at)
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">{{ trans('general.created_at') }}</h3>
                                    </div>
                                    <div class="panel-body">
                                        {{ \App\Helpers\Helper::getFormattedDateObject($access->created_at, 'datetime') }}
                                    </div>
                                </div>
                            @endif

                            @if ($access->updated_at)
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">{{ trans('general.updated_at') }}</h3>
                                    </div>
                                    <div class="panel-body">
                                        {{ \App\Helpers\Helper::getFormattedDateObject($access->updated_at, 'datetime') }}
                                    </div>
                                </div>
                            @endif
                        </div> <!-- End second column -->
                    </div> <!-- End row -->
                </div> <!-- End details tab -->

                <div class="tab-pane" id="history">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ trans('general.date') }}</th>
                                        <th>{{ trans('general.admin') }}</th>
                                        <th>{{ trans('general.action') }}</th>
                                        <th>{{ trans('general.user') }}</th>
                                        <th>{{ trans('general.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($access->assetlog as $log)
                                        <tr>
                                            <td>{{ \App\Helpers\Helper::getFormattedDateObject($log->created_at, 'datetime') }}</td>
                                            <td>
                                                @if ($log->adminlog)
                                                    <a href="{{ route('users.show', $log->adminlog->id) }}">{{ $log->adminlog->present()->fullName() }}</a>
                                                @endif
                                            </td>
                                            <td>{{ $log->action_type }}</td>
                                            <td>
                                                @if ($log->userlog)
                                                    <a href="{{ route('users.show', $log->userlog->id) }}">{{ $log->userlog->present()->fullName() }}</a>
                                                @endif
                                            </td>
                                            <td>{{ $log->note }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- End history tab -->
            </div> <!-- End tab content -->
        </div> <!-- End nav-tabs-custom -->
    </div> <!-- End col-md-12 -->
</div> <!-- End row -->

@stop 