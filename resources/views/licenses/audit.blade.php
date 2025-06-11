@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.audit') }}
    @parent
@stop

{{-- Page content --}}
@section('content')

    <style>
        .input-group {
            padding-left: 0px !important;
        }
    </style>

    <div class="row">
        <!-- left column -->
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-default">

                <form method="POST" action="{{ route('licenses.audit.store', $license) }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">

                                    <div class="box-header with-border">
                    <h2 class="box-title"> {{ trans('general.audit') }}: {{ $license->name }}</h2>
                    <p class="help-block">{{ trans('admin/licenses/general.audit_help_text') }}</p>
                </div>
                    <div class="box-body">
                    {{csrf_field()}}

                    <!-- License Name -->
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">
                            {{ trans('admin/licenses/form.name') }}
                        </label>
                        <div class="col-md-8">
                            <p class="form-control-static">{{ $license->name }}</p>
                        </div>
                    </div>

                    <!-- Serial -->
                    @if ($license->serial)
                    <div class="form-group{{ $errors->has('serial') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">
                            {{ trans('admin/licenses/form.serial') }}
                        </label>
                        <div class="col-md-8">
                            <p class="form-control-static">{{ $license->serial }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Seats information -->
                    <div class="form-group">
                        <label class="control-label col-md-3">
                            {{ trans('admin/licenses/form.seats') }}
                        </label>
                        <div class="col-md-8">
                            <p class="form-control-static">
                                {{ $license->seats }} {{ trans('admin/licenses/general.seats_total') }}
                                @if($license->licenseseats->count() > 0)
                                    <br><span class="text-success">{{ $license->licenseseats->count() }} {{ trans_choice('admin/licenses/general.seats', $license->licenseseats->count()) }} {{ trans('admin/licenses/general.will_be_audited') }}</span>
                                @else
                                    <br><span class="text-warning">{{ trans('admin/licenses/general.no_seats_to_audit') }}</span>
                                @endif
                                <br><small class="text-muted">{{ trans('admin/licenses/general.bulk_audit_note') }}</small>
                            </p>
                        </div>
                    </div>

                    <!-- Next Audit -->
                    <div class="form-group{{ $errors->has('next_audit_date') ? ' has-error' : '' }}">
                        <label for="next_audit_date" class="col-sm-3 control-label">
                            {{ trans('general.next_audit_date') }}
                        </label>
                        <div class="col-md-8">
                            <div class="input-group date col-md-5" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-clear-btn="true">
                                <input type="text" class="form-control" placeholder="{{ trans('general.next_audit_date') }}" name="next_audit_date" id="next_audit_date" value="{{ old('next_audit_date', $next_audit_date) }}">
                                <span class="input-group-addon"><x-icon type="calendar" /></span>
                            </div>
                            {!! $errors->first('next_audit_date', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                                <p class="help-block">{!! trans('general.next_audit_date_help') !!}</p>
                        </div>
                    </div>

                    <!-- Note -->
                    <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                        <label for="note" class="col-sm-3 control-label">
                            {{ trans('general.notes') }}
                        </label>
                        <div class="col-md-8">
                            <textarea class="col-md-6 form-control" id="note" name="note">{{ old('note') }}</textarea>
                            {!! $errors->first('note', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    </div> <!--/.box-body-->

                    <x-redirect_submit_options
                            index_route="licenses.index"
                            :button_label="trans('general.audit')"
                            :options="[
                                'index' => trans('admin/hardware/form.redirect_to_all', ['type' => trans('general.licenses')]),
                                'item' => trans('admin/hardware/form.redirect_to_type', ['type' => trans('general.license')]),
                                'other_redirect' => trans('general.audit_due')
                               ]"
                    />

                </form>
            </div>
        </div> <!--/.col-md-7-->
    </div>
@stop 