@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('admin/access/general.checkout') }}
    @parent
@stop

{{-- Page content --}}
@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h2 class="box-title">{{ trans('admin/access/general.checkout') }} - {{ $access->access_tag }}</h2>
                </div>
                <div class="box-body">
                    <form class="form-horizontal" method="post" action="{{ route('access.checkout.store', $access->id) }}" autocomplete="off">
                        {{ csrf_field() }}

                        <!-- User -->
                        <div class="form-group {{ $errors->has('assigned_user') ? ' has-error' : '' }}">
                            <label for="assigned_user" class="col-md-3 control-label">{{ trans('general.assign_to') }}</label>
                            <div class="col-md-8">
                                <select class="js-data-ajax" data-endpoint="users" data-placeholder="{{ trans('general.select_user') }}" name="assigned_user" style="width: 100%" id="assigned_user">
                                    @if ($access->assigned_to)
                                        <option value="{{ $access->assigned_to }}">
                                            {{ $access->assignedto->present()->fullName }} ({{ $access->assignedto->username }})
                                        </option>
                                    @endif
                                </select>
                                {!! $errors->first('assigned_user', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                            </div>
                        </div>

                        <!-- Expiration Date -->
                        <div class="form-group {{ $errors->has('expiration_date') ? ' has-error' : '' }}">
                            <label for="expiration_date" class="col-md-3 control-label">{{ trans('admin/access/general.expiration_date') }}</label>
                            <div class="col-md-8">
                                <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control" placeholder="{{ trans('general.select_date') }}" name="expiration_date" id="expiration_date" value="{{ old('expiration_date', $access->expiration_date) }}">
                                    <span class="input-group-addon"><i class="fas fa-calendar"></i></span>
                                </div>
                                {!! $errors->first('expiration_date', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
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
                            <button type="submit" class="btn btn-primary pull-right"><i class="fas fa-check icon-white" aria-hidden="true"></i> {{ trans('general.checkout') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop

@section('moar_scripts')
    @include('partials/assets-assigned')
@stop 