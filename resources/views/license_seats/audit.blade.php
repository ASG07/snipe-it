@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('admin/licenses/general.audit') }}
    {{ $licenseSeat->license->name ?? 'Unknown License' }} -
    {{ $licenseSeat->license->serial ?? 'No Serial' }}
@parent
@stop

@section('header_right')
    <a href="{{ URL::previous() }}" class="btn btn-primary pull-right">
        {{ trans('general.back') }}</a>
@stop

{{-- Page content --}}
@section('content')

    <div class="row">
        <div class="col-md-8 offset-md-2">

            <form class="form-horizontal" method="post" action="{{ route('license_seats.audit.store', $licenseSeat) }}" autocomplete="off">
                {{csrf_field()}}
                <input type="hidden" name="redirect_back" value="1">

                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('admin/licenses/general.audit') }}</h3>
                    </div>
                    <div class="box-body">

                        <!-- Asset Name -->
                        <div class="form-group">
                            <label class="control-label col-md-3">{{ trans('admin/licenses/form.name') }}</label>
                            <div class="col-md-8">
                                <p class="form-control-static">{{ $licenseSeat->license->name }}</p>
                            </div>
                        </div>

                        <!-- Serial -->
                        <div class="form-group">
                            <label class="control-label col-md-3">{{ trans('admin/licenses/form.serial') }}</label>
                            <div class="col-md-8">
                                <p class="form-control-static">{{ $licenseSeat->license->serial }}</p>
                            </div>
                        </div>

                        <!-- Seat Info -->
                        <div class="form-group">
                            <label class="control-label col-md-3">{{ trans('admin/licenses/form.seat') }}</label>
                            <div class="col-md-8">
                                <p class="form-control-static">
                                    @if ($licenseSeat->user)
                                        {{ trans('admin/licenses/form.user') }}: {{ $licenseSeat->user->present()->fullName() }}
                                    @elseif ($licenseSeat->asset)
                                        {{ trans('admin/licenses/form.asset') }}: {{ $licenseSeat->asset->present()->name() }}
                                    @else
                                        {{ trans('admin/licenses/form.unassigned') }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Location -->
                        @php
                            $location = $licenseSeat->location();
                            $locationName = $location && is_object($location) && isset($location->name) ? trim($location->name) : '';
                        @endphp
                        @if ($locationName)
                            <div class="form-group">
                                <label class="control-label col-md-3">{{ trans('general.location') }}</label>
                                <div class="col-md-8">
                                    <p class="form-control-static">{{ $locationName }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Last Audit -->
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                {{ trans('general.last_audit') }}
                            </label>
                            <div class="col-md-8">
                                <p class="form-control-static">
                                    @if ($licenseSeat->last_audit_date)
                                        {{ Helper::getFormattedDateObject($licenseSeat->last_audit_date, 'datetime', false) }}
                                    @else
                                        {{ trans('admin/settings/general.none') }}
                                    @endif
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
                        <div class="form-group {{ $errors->has('note') ? 'error' : '' }}">
                            <label for="note" class="col-md-3 control-label">{{ trans('admin/hardware/form.notes') }}</label>
                            <div class="col-md-8">
                                <textarea class="col-md-6 form-control" id="note" name="note">{{ old('note', $licenseSeat->notes) }}</textarea>
                                {!! $errors->first('note', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                            </div>
                        </div>
                        
                    </div> <!--/.box-body-->
                    <div class="box-footer">
                        <a class="btn btn-link" href="{{ URL::previous() }}">{{ trans('button.cancel') }}</a>
                        <button type="submit" class="btn btn-success pull-right"><i class="fas fa-check icon-white" aria-hidden="true"></i> {{ trans('general.audit') }}</button>
                    </div>
                </div> <!--/.box.box-default-->
            </form>
        </div> <!--/.col-md-8-->
    </div>
@stop 