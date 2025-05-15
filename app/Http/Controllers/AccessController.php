<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\ImageUploadRequest;
use App\Models\Actionlog;
use App\Http\Requests\UploadFileRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Access;
use App\Models\AssetModel;
use App\Models\Company;
use App\Models\Location;
use App\Models\Setting;
use App\Models\Statuslabel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * This class controls all actions related to Access Accounts for
 * the Snipe-IT Asset Management application.
 *
 * @version    v1.0
 */
class AccessController extends Controller
{
    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the access listing, which is generated in getDatatable.
     *
     * @since [v1.0]
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('index', Access::class);
        $company = Company::find($request->input('company_id'));

        return view('access/index')->with('company', $company);
    }

    /**
     * Returns a view that presents a form to create a new access.
     *
     * @since [v1.0]
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Access::class);
        $view = view('access/edit')
            ->with('statuslabel_list', Helper::statusLabelList())
            ->with('item', new Access);

        if ($request->filled('model_id')) {
            $selected_model = AssetModel::find($request->input('model_id'));
            $view->with('selected_model', $selected_model);
        }

        return $view;
    }

    /**
     * Validate and process new access form data.
     *
     * @since [v1.0]
     * @param ImageUploadRequest $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Access::class);
        
        // Create a new access
        $access = new Access();
        
        // Get the validation rules specific to this access
        $rules = $access->getRules();

        // Validate the request
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create the access
        $access->fill($request->all());
        $access->user_id = auth()->id();
        $access->created_by = auth()->id();

        if ($access->save()) {
            return redirect()->route('access.index')
                ->with('success', trans('admin/access/message.create.success'));
        }

        return redirect()->back()->withInput()
            ->with('error', trans('admin/access/message.create.error'));
    }

    /**
     * Returns a view that presents a form to edit an existing access.
     *
     * @since [v1.0]
     * @param int $accessId
     * @return View
     */
    public function edit($accessId = null): View
    {
        // Check if the access exists
        if (!$item = Access::find($accessId)) {
            return redirect()->route('access.index')->with('error', trans('admin/access/message.does_not_exist'));
        }

        $this->authorize('update', $item);

        return view('access/edit', compact('item'))
            ->with('statuslabel_list', Helper::statusLabelList());
    }

    /**
     * Validate and process the form data to update an existing access.
     *
     * @since [v1.0]
     * @param Request $request
     * @param int $accessId
     * @return RedirectResponse
     */
    public function update(Request $request, $accessId = null): RedirectResponse
    {
        // Check if the access exists
        if (!$access = Access::find($accessId)) {
            return redirect()->route('access.index')->with('error', trans('admin/access/message.does_not_exist'));
        }

        $this->authorize('update', $access);

        // Get the validation rules specific to this access
        $rules = $access->getRules();

        // Validate the request
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update the access
        $access->fill($request->all());

        if ($access->save()) {
            return redirect()->route('access.show', ['access' => $access->id])
                ->with('success', trans('admin/access/message.update.success'));
        }

        return redirect()->back()->withInput()
            ->with('error', trans('admin/access/message.update.error'));
    }

    /**
     * Deletes a access.
     *
     * @since [v1.0]
     * @param int $accessId
     * @return RedirectResponse
     */
    public function destroy($accessId): RedirectResponse
    {
        // Check if the access exists
        if (!$access = Access::find($accessId)) {
            return redirect()->route('access.index')->with('error', trans('admin/access/message.does_not_exist'));
        }

        $this->authorize('delete', $access);

        if ($access->delete()) {
            return redirect()->route('access.index')
                ->with('success', trans('admin/access/message.delete.success'));
        }

        return redirect()->back()
            ->with('error', trans('admin/access/message.delete.error'));
    }

    /**
     * Show a specific access
     *
     * @since [v1.0]
     * @param int $accessId
     * @return View
     */
    public function show($accessId = null): View
    {
        $access = Access::withTrashed()->find($accessId);
        $this->authorize('view', $access);

        if (!$access) {
            return redirect()->route('access.index')
                ->with('error', trans('admin/access/message.does_not_exist'));
        }

        $additional_fields = [];

        return view('access/view', compact('access', 'additional_fields'));
    }

    /**
     * Returns a view containing a form to checkout an access to a user.
     *
     * @since [v1.0]
     * @param int $accessId
     * @return View
     */
    public function getCheckout($accessId): View
    {
        // Check if the access exists
        if (!$access = Access::find($accessId)) {
            return redirect()->route('access.index')->with('error', trans('admin/access/message.does_not_exist'));
        }

        $this->authorize('checkout', $access);

        // Get all users
        $users = User::select(['users.id', 'users.username', 'users.employee_num'])->whereNull('deleted_at')->orderBy('username', 'asc')->get();

        // Get list of assets
        $assets = array('' => 'Select an asset') + Asset::orderBy('name', 'ASC')->whereNull('deleted_at')->get()->pluck('name', 'id')->toArray();

        // Return checkout view
        return view('access/checkout', compact('access', 'users', 'assets'));
    }

    /**
     * Validate and process the form data to checkout an access.
     *
     * @since [v1.0]
     * @param Request $request
     * @param int $accessId
     * @return RedirectResponse
     */
    public function postCheckout(Request $request, $accessId): RedirectResponse
    {
        // Check if the access exists
        if (!$access = Access::find($accessId)) {
            return redirect()->route('access.index')->with('error', trans('admin/access/message.does_not_exist'));
        }

        $this->authorize('checkout', $access);

        // Check if the access is already checked out
        if ($access->isAssigned()) {
            return redirect()->route('access.checkin.create', $access->id)
                ->with('error', trans('admin/access/message.checkout.already_checked_out'));
        }

        // Get target
        if ($request->filled('assigned_user')) {
            $target = User::find($request->input('assigned_user'));
            $access->assigned_type = User::class;
        } elseif ($request->filled('assigned_asset')) {
            $target = Asset::find($request->input('assigned_asset'));
            $access->assigned_type = Asset::class;
        }

        if (!isset($target)) {
            return redirect()->back()->with('error', trans('admin/access/message.checkout.user_or_asset_required'));
        }

        // Update the access data
        $access->assigned_to = $request->input('assigned_user');
        $access->expiration_date = $request->input('expiration_date');
        $access->notes = $request->input('note');

        if ($access->save()) {
            return redirect()->route('access.index')
                ->with('success', trans('admin/access/message.checkout.success'));
        }

        return redirect()->back()->withInput()
            ->with('error', trans('admin/access/message.checkout.error'));
    }

    /**
     * Returns a view containing a form to check in an access.
     *
     * @since [v1.0]
     * @param int $accessId
     * @param Request $request
     * @return View
     */
    public function getCheckin($accessId, Request $request): View
    {
        // Check if the access exists
        if (!$access = Access::find($accessId)) {
            return redirect()->route('access.index')->with('error', trans('admin/access/message.does_not_exist'));
        }

        $this->authorize('checkin', $access);

        if (!$access->isAssigned()) {
            return redirect()->route('access.index')->with('error', trans('admin/access/message.checkin.not_checked_out'));
        }

        return view('access/checkin', compact('access'));
    }

    /**
     * Validate and process a form to check in an access.
     *
     * @since [v1.0]
     * @param int $accessId
     * @param Request $request
     * @return RedirectResponse
     */
    public function postCheckin(Request $request, $accessId = null): RedirectResponse
    {
        // Check if the access exists
        if (!$access = Access::find($accessId)) {
            return redirect()->route('access.index')->with('error', trans('admin/access/message.does_not_exist'));
        }

        $this->authorize('checkin', $access);

        if (!$access->isAssigned()) {
            return redirect()->route('access.index')->with('error', trans('admin/access/message.checkin.not_checked_out'));
        }

        // Reset the access data
        $access->assigned_to = null;
        $access->assigned_type = null;
        $access->expiration_date = null;

        if ($access->save()) {
            return redirect()->route('access.show', ['access' => $access->id])
                ->with('success', trans('admin/access/message.checkin.success'));
        }

        return redirect()->back()->withInput()
            ->with('error', trans('admin/access/message.checkin.error'));
    }
} 