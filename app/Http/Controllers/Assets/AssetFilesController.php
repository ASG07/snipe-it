<?php

namespace App\Http\Controllers\Assets;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadFileRequest;
use App\Models\Actionlog;
use App\Models\Asset;
use \Illuminate\Http\Response;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Contracts\View\View;
use \Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetFilesController extends Controller
{
    /**
     * Upload a file to the server.
     *
     * @param UploadFileRequest $request
     * @param int $assetId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *@since [v1.0]
     * @author [A. Gianotto] [<snipe@snipe.net>]
     */
    public function store(UploadFileRequest $request, Asset $asset) : RedirectResponse
    {

        $this->authorize('update', $asset);

        if ($request->hasFile('file')) {
            if (! Storage::exists('private_uploads/assets')) {
                Storage::makeDirectory('private_uploads/assets', 775);
            }

            foreach ($request->file('file') as $file) {
                $file_name = $request->handleFile('private_uploads/assets/','hardware-'.$asset->id, $file);
                
                $asset->logUpload($file_name, $request->get('notes'));
            }

            return redirect()->back()->withFragment('files')->with('success', trans('admin/hardware/message.upload.success'));
        }

        return redirect()->back()->with('error', trans('admin/hardware/message.upload.nofiles'));
    }

    /**
     * Check for permissions and display the file.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param  int $assetId
     * @param  int $fileId
     * @since [v1.0]
     */
    public function show(Asset $asset, $fileId = null) : View | RedirectResponse | Response | StreamedResponse | BinaryFileResponse
    {

        $this->authorize('view', $asset);

        if ($log = Actionlog::whereNotNull('filename')->where('item_id', $asset->id)->find($fileId)) {
            $file = 'private_uploads/assets/'.$log->filename;

            if ($log->action_type == 'audit') {
                $file = 'private_uploads/audits/'.$log->filename;
            }

            try {
                 return StorageHelper::showOrDownloadFile($file, $log->filename);
            } catch (\Exception $e) {
                return redirect()->route('hardware.show', $asset)->with('error', trans('general.file_not_found'));
            }

        }

        return redirect()->route('hardware.show', $asset)->with('error', trans('general.log_record_not_found'));


    }

    /**
     * Delete the associated file
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param  int $assetId
     * @param  int $fileId
     * @since [v1.0]
     */
    public function destroy(Asset $asset, $fileId = null) : RedirectResponse
    {
        $this->authorize('update', $asset);
        $rel_path = 'private_uploads/assets';

        if ($log = Actionlog::find($fileId)) {
            if (Storage::exists($rel_path.'/'.$log->filename)) {
                Storage::delete($rel_path.'/'.$log->filename);
            }
            $log->delete();
            return redirect()->back()->withFragment('files')->with('success', trans('admin/hardware/message.deletefile.success'));
        }

        return redirect()->route('hardware.show', $asset)->with('error', trans('general.log_record_not_found'));
    }

    /**
     * Show the form for editing the file note
     *
     * @param  Asset $asset
     * @param  int $fileId
     */
    public function edit(Asset $asset, $fileId = null) : JsonResponse | RedirectResponse
    {
        $this->authorize('update', $asset);

        if ($log = Actionlog::find($fileId)) {
            if (request()->ajax()) {
                return response()->json([
                    'id' => $log->id,
                    'note' => $log->note,
                    'filename' => $log->filename
                ]);
            }
            return redirect()->route('hardware.show', $asset)->withFragment('files');
        }

        return redirect()->route('hardware.show', $asset)->with('error', trans('general.log_record_not_found'));
    }

    /**
     * Update the file note
     *
     * @param  Asset $asset
     * @param  int $fileId
     */
    public function update(Asset $asset, $fileId = null) : RedirectResponse | JsonResponse
    {
        $this->authorize('update', $asset);

        $request = request();
        $request->validate([
            'note' => 'nullable|string|max:50000',
        ]);

        if ($log = Actionlog::find($fileId)) {
            $log->note = $request->input('note');
            $log->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('admin/hardware/message.updatefile.success')
                ]);
            }

            return redirect()->back()->withFragment('files')->with('success', trans('admin/hardware/message.updatefile.success'));
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => trans('general.log_record_not_found')
            ], 404);
        }

        return redirect()->route('hardware.show', $asset)->with('error', trans('general.log_record_not_found'));
    }

}
