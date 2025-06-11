@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('admin/licenses/form.bulk_audit') }}
@stop

{{-- Page content --}}
@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('admin/licenses/form.bulk_audit') }}</h3>
                <div class="box-tools pull-right">
                    <span class="label label-default">{{ $seats->count() }} {{ trans_choice('admin/licenses/general.seats', $seats->count()) }}</span>
                </div>
            </div>
            <div class="box-body">
                <form method="POST" action="{{ route('license_seats.storeaudit') }}" class="form-horizontal" role="form" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group">
                        <label for="note" class="col-md-3 control-label">{{ trans('admin/hardware/form.notes') }}</label>
                        <div class="col-md-8">
                            <textarea class="form-control" id="note" name="note" placeholder="{{ trans('admin/licenses/form.audit_note_placeholder') }}">{{ old('note', 'Bulk audit') }}</textarea>
                            <p class="help-block">{{ trans('admin/licenses/form.audit_note_help') }}</p>
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('next_audit_date') ? ' has-error' : '' }}">
                        <label for="next_audit_date" class="col-md-3 control-label">{{ trans('general.next_audit_date') }}</label>
                        <div class="col-md-8">
                            <div class="input-group date col-md-5" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-clear-btn="true">
                                <input type="text" class="form-control" placeholder="{{ trans('general.next_audit_date') }}" id="next_audit_date" name="next_audit_date" value="{{ old('next_audit_date', $next_audit_date) }}" required>
                                <span class="input-group-addon"><x-icon type="calendar" /></span>
                            </div>
                            {!! $errors->first('next_audit_date', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                            <p class="help-block">{!! trans('general.next_audit_date_help') !!}</p>
                        </div>
                    </div>

                    <!-- Hidden fields to store seat IDs -->
                    <input type="hidden" name="seat_ids" value="{{ $seats->pluck('id')->implode(',') }}">
                    
                    <!-- Display list of seats being audited -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">{{ trans('admin/licenses/general.seats_to_audit') }}</label>
                        <div class="col-md-8">
                            <div class="well well-sm" style="max-height: 200px; overflow-y: auto;">
                                @foreach($seats as $seat)
                                    <div class="row" style="margin-bottom: 5px;">
                                        <div class="col-md-6">
                                            <strong>{{ $seat->license->name }}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            @if($seat->assigned_to)
                                                {{ trans('general.assigned_to') }}: {{ $seat->user->present()->fullName() }}
                                            @elseif($seat->asset_id)
                                                {{ trans('general.assigned_to') }}: {{ $seat->asset->present()->name() }}
                                            @else
                                                <span class="text-muted">{{ trans('general.unassigned') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="box-footer">
                        <div class="text-left col-md-6">
                            <a class="btn btn-link" href="{{ URL::previous() }}">{{ trans('button.cancel') }}</a>
                        </div>
                        <div class="text-right col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <x-icon type="checkmark" />
                                {{ trans('general.audit') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop 

@section('moar_scripts')
    @include ('partials.bootstrap-table')
@stop 