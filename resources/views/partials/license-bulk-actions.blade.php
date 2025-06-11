<div id="{{ (isset($id_divname)) ? $id_divname : 'licensesBulkEditToolbar' }}" style="min-width:400px">
    <form
    method="POST"
    action="{{ (isset($bulk_action_route)) ? $bulk_action_route : route('licenses.bulkedit') }}"
    accept-charset="UTF-8"
    class="form-inline"
    id="{{ (isset($id_formname)) ? $id_formname : 'licensesBulkForm' }}"
>
    @csrf

    {{-- The sort and order will only be used if the cookie is actually empty (like on first-use) --}}
    <input name="sort" type="hidden" value="licenses.id">
    <input name="order" type="hidden" value="asc">
    <label for="bulk_actions">
        <span class="sr-only">
            {{ trans('button.bulk_actions') }}
        </span>
    </label>
    <select name="bulk_actions" class="form-control select2" aria-label="bulk_actions" style="min-width: 350px;">
        @if((isset($status)) && ($status == 'Deleted'))
            @can('delete', \App\Models\License::class)
                <option value="restore">{{trans('button.restore')}}</option>
            @endcan
        @else
            @if(!(isset($bulk_action_route) && $bulk_action_route == route('license_seats.bulkaudit')))
                @can('update', \App\Models\License::class)
                    <option value="edit">{{ trans('button.edit') }}</option>
                @endcan
                @can('delete', \App\Models\License::class)
                    <option value="delete">{{ trans('button.delete') }}</option>
                @endcan
                @can('audit', \App\Models\License::class)
                    <option value="audit">{{ trans('audit') }}</option>
                @endcan
                <option value="labels" {{$snipeSettings->shortcuts_enabled == 1 ? "accesskey=l" : ''}}>{{ trans_choice('button.generate_labels', 2) }}</option>
            @else
                @can('audit', \App\Models\License::class)
                    <option value="audit">{{ trans('Audit') }}</option>
                    <option value="add_notes">{{ trans('admin/licenses/form.add_notes') }}</option>
                @endcan
            @endif
        @endif
    </select>

    <button class="btn btn-primary" id="{{ (isset($id_button)) ? $id_button : 'bulkLicenseEditButton' }}" disabled>{{ trans('button.go') }}</button>
    </form>
</div>