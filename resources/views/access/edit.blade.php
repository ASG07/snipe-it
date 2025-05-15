@extends('layouts/edit-form', [
    'createText' => trans('admin/access/general.create'),
    'updateText' => trans('admin/access/general.update'),
    'topSubmit' => true,
    'formAction' => (isset($item->id)) ? route('access.update', ['access' => $item->id]) : route('access.store'),
])

{{-- Page content --}}
@section('inputFields')

@include ('partials.forms.edit.name', ['translated_name' => trans('general.name')])
@include ('partials.forms.edit.asset-tag', ['translated_name' => trans('admin/access/general.access_tag')])
@include ('partials.forms.edit.status', ['statuslabel_types' => $statuslabel_types ?? null ])
@include ('partials.forms.edit.model')

<div class="form-group {{ $errors->has('username') ? ' has-error' : '' }}">
    <label for="username" class="col-md-3 control-label">{{ trans('admin/access/general.username') }}</label>
    <div class="col-md-7 col-sm-12">
        <input class="form-control" type="text" name="username" id="username" value="{{ old('username', $item->username) }}" />
        {!! $errors->first('username', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>

<div class="form-group {{ $errors->has('url') ? ' has-error' : '' }}">
    <label for="url" class="col-md-3 control-label">{{ trans('admin/access/general.url') }}</label>
    <div class="col-md-7 col-sm-12">
        <input class="form-control" type="text" name="url" id="url" value="{{ old('url', $item->url) }}" />
        {!! $errors->first('url', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>

<div class="form-group {{ $errors->has('expiration_date') ? ' has-error' : '' }}">
    <label for="expiration_date" class="col-md-3 control-label">{{ trans('admin/access/general.expiration_date') }}</label>
    <div class="col-md-7 col-sm-12">
        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-end-date="0d">
            <input class="form-control" type="text" name="expiration_date" id="expiration_date" value="{{ old('expiration_date', $item->expiration_date) }}" placeholder="{{ trans('general.select_date') }}">
            <span class="input-group-addon"><i class="fas fa-calendar"></i></span>
        </div>
        {!! $errors->first('expiration_date', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>

@include ('partials.forms.edit.company-select', ['translated_name' => trans('general.company'), 'fieldname' => 'company_id'])
@include ('partials.forms.edit.notes')

<!-- Custom Fields -->
@if ($item->model && $item->model->fieldset)
    @include("custom_fields.fieldsets.edit-fields", ["asset_fieldset" => $item->model->fieldset])
@endif

@stop 