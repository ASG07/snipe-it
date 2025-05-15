@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('admin/access/general.checkin') }}
    @parent
@stop

{{-- Page content --}}
@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h2 class="box-title">{{ trans('admin/access/general.checkin') }} - {{ $access->access_tag }}</h2>
                </div>

                <div class="box-body">
                    <form class="form-horizontal" method="post" action="{{ route('access.checkin.store', $access->id) }}" autocomplete="off">
                        {{ csrf_field() }}

                        <!-- Current Assigned To -->
                        <div class="form-group">
                            <label class="col-md-3 control-label">{{ trans('general.currently_assigned_to') }}</label>
                            <div class="col-md-8">
                                <p class="form-control-static">
                                    @if ($access->assignedto)
                                        {{ $access->assignedto->present()->fullName() }}
                                    @else
                                        <span class="text-danger">{{ trans('general.not_assigned_to_user') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Note -->
                        <div class="form-group {{ $errors->has('note') ? ' has-error' : '' }}">
                            <label for="note" class="col-md-3 control-label">{{ trans('admin/hardware/form.notes') }}</label>
                            <div class="col-md-8">
                                <textarea class="form-control" id="note" name="note">{{ old('note') }}</textarea>
                                {!! $errors->first('note', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                            </div>
                        </div>

                        <div class="box-footer">
                            <a class="btn btn-link" href="{{ route('access.show', $access->id) }}">{{ trans('button.cancel') }}</a>
                            <button type="submit" class="btn btn-primary pull-right"><i class="fas fa-check icon-white" aria-hidden="true"></i> {{ trans('general.checkin') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop 