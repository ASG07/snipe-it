<div class="modal fade" id="editFileNoteModal" tabindex="-1" role="dialog" aria-labelledby="editFileNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title" id="editFileNoteModalLabel">{{ trans('general.edit_note')  }}</h2>
            </div>
            <form
                method="POST"
                action=""
                accept-charset="UTF-8"
                id="editFileNoteForm"
            >
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="alert alert-danger" id="edit_modal_error_msg" style="display:none"></div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="edit_file_note">{{ trans('general.notes') }}</label>
                            <textarea class="form-control" id="edit_file_note" name="note" rows="4"></textarea>
                            <span class="help-block">
                                <small>{{ trans('general.edit_note_help') }}</small>
                            </span>
                        </div>
                    </div>
                    
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-12">
                            <strong>{{ trans('general.file_name') }}:</strong> <span id="edit_file_filename"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('button.cancel') }}</button>
                    <button type="submit" class="btn btn-primary pull-right" id="edit-modal-save">{{ trans('general.save') }}</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div> 