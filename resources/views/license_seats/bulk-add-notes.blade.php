@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('admin/licenses/form.bulk_add_notes') }}
@stop

{{-- Page content --}}
@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('admin/licenses/form.bulk_add_notes') }}</h3>
                <div class="box-tools pull-right">
                    <span class="badge">{{ $seats->count() }} {{ trans_choice('admin/licenses/general.license_seats', $seats->count()) }}</span>
                </div>
            </div>
            <div class="box-body">
                <form method="POST" action="{{ route('license_seats.storebulknotes') }}" class="form-horizontal" role="form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="notes" class="col-md-3 control-label">{{ trans('admin/hardware/form.notes') }}</label>
                        <div class="col-md-8">
                            <textarea class="form-control" id="notes" name="notes" rows="4" required>{{ old('notes') }}</textarea>
                            <p class="help-block">{{ trans('admin/licenses/form.notes_help') }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-3">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="overwrite" value="1" {{ old('overwrite') ? 'checked' : '' }}>
                                    {{ trans('admin/licenses/form.overwrite_existing_notes') }}
                                </label>
                                <p class="help-block">{{ trans('admin/licenses/form.overwrite_notes_help') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields to store seat IDs -->
                    <input type="hidden" name="seat_ids" value="{{ $seats->pluck('id')->implode(',') }}">
                    
                    <div class="box-footer">
                        <div class="text-left col-md-6">
                            <a class="btn btn-link" href="{{ URL::previous() }}">{{ trans('button.cancel') }}</a>
                        </div>
                        <div class="text-right col-md-6">
                            <button type="submit" class="btn btn-primary">{{ trans('button.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop 