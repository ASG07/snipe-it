<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Transformers\LicensesTransformer;
use App\Http\Transformers\SelectlistTransformer;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Company;

class LicensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     *
     */
    public function index(Request $request) : JsonResponse | array
    {
        $this->authorize('view', License::class);

        if ($request->filled('action') && $request->filled('upcoming_status')) {
            return $this->getDueForActionList($request->input('action'), $request->input('upcoming_status'));
        }
        
        $licenses = License::with('company', 'manufacturer', 'supplier','category', 'adminuser')->withCount('freeSeats as free_seats_count');

        if ($request->filled('company_id')) {
            $licenses->where('licenses.company_id', '=', $request->input('company_id'));
        }

        if ($request->filled('name')) {
            $licenses->where('licenses.name', '=', $request->input('name'));
        }

        if ($request->filled('product_key')) {
            $licenses->where('licenses.serial', '=', $request->input('product_key'));
        }

        if ($request->filled('order_number')) {
            $licenses->where('order_number', '=', $request->input('order_number'));
        }

        if ($request->filled('purchase_order')) {
            $licenses->where('purchase_order', '=', $request->input('purchase_order'));
        }

        if ($request->filled('license_name')) {
            $licenses->where('license_name', '=', $request->input('license_name'));
        }

        if ($request->filled('license_email')) {
            $licenses->where('license_email', '=', $request->input('license_email'));
        }

        if ($request->filled('manufacturer_id')) {
            $licenses->where('manufacturer_id', '=', $request->input('manufacturer_id'));
        }

        if ($request->filled('supplier_id')) {
            $licenses->where('supplier_id', '=', $request->input('supplier_id'));
        }

        if ($request->filled('category_id')) {
            $licenses->where('category_id', '=', $request->input('category_id'));
        }

        if ($request->filled('depreciation_id')) {
            $licenses->where('depreciation_id', '=', $request->input('depreciation_id'));
        }

        if ($request->filled('created_by')) {
            $licenses->where('created_by', '=', $request->input('created_by'));
        }

        if (($request->filled('maintained')) && ($request->input('maintained')=='true')) {
            $licenses->where('maintained','=',1);
        } elseif (($request->filled('maintained')) && ($request->input('maintained')=='false')) {
            $licenses->where('maintained','=',0);
        }

        if (($request->filled('expires')) && ($request->input('expires')=='true')) {
            $licenses->whereNotNull('expiration_date');
        } elseif (($request->filled('expires')) && ($request->input('expires')=='false')) {
            $licenses->whereNull('expiration_date');
        }

        if ($request->filled('search')) {
            $licenses = $licenses->TextSearch($request->input('search'));
        }

        if ($request->input('deleted')=='true') {
            $licenses->onlyTrashed();
        }

        // Make sure the offset and limit are actually integers and do not exceed system limits
        $offset = ($request->input('offset') > $licenses->count()) ? $licenses->count() : app('api_offset_value');
        $limit = app('api_limit_value');

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';

        switch ($request->input('sort')) {
                case 'manufacturer':
                    $licenses = $licenses->leftJoin('manufacturers', 'licenses.manufacturer_id', '=', 'manufacturers.id')->orderBy('manufacturers.name', $order);
                break;
            case 'supplier':
                $licenses = $licenses->leftJoin('suppliers', 'licenses.supplier_id', '=', 'suppliers.id')->orderBy('suppliers.name', $order);
                break;
            case 'category':
                $licenses = $licenses->leftJoin('categories', 'licenses.category_id', '=', 'categories.id')->orderBy('categories.name', $order);
                break;
            case 'depreciation':
                $licenses = $licenses->leftJoin('depreciations', 'licenses.depreciation_id', '=', 'depreciations.id')->orderBy('depreciations.name', $order);
                break;
            case 'company':
                $licenses = $licenses->leftJoin('companies', 'licenses.company_id', '=', 'companies.id')->orderBy('companies.name', $order);
                break;
            case 'created_by':
                $licenses = $licenses->OrderByCreatedBy($order);
                break;
            default:
                $allowed_columns =
                    [
                        'id',
                        'name',
                        'purchase_cost',
                        'expiration_date',
                        'purchase_order',
                        'order_number',
                        'notes',
                        'purchase_date',
                        'serial',
                        'company',
                        'category',
                        'license_name',
                        'license_email',
                        'free_seats_count',
                        'seats',
                        'termination_date',
                        'depreciation_id',
                        'min_amt',
                    ];
                $sort = in_array($request->input('sort'), $allowed_columns) ? e($request->input('sort')) : 'created_at';
                $licenses = $licenses->orderBy($sort, $order);
                break;
        }

        $total = $licenses->count();

        $licenses = $licenses->skip($offset)->take($limit)->get();
        return (new LicensesTransformer)->transformLicenses($licenses, $total);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request) : JsonResponse
    {
        $this->authorize('create', License::class);
        $license = new License;
        $license->fill($request->all());

        if ($license->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', $license, trans('admin/licenses/message.create.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $license->getErrors()));
    }

    /**
     * Display the specified resource.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param  int  $id
     */
    public function show($id) : JsonResponse | array
    {
        $this->authorize('view', License::class);
        $license = License::withCount('freeSeats as free_seats_count')->findOrFail($id);
        $license = $license->load('assignedusers', 'licenseSeats.user', 'licenseSeats.asset');

        return (new LicensesTransformer)->transformLicense($license);
    }

    /**
     * Update the specified resource in storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id) : JsonResponse | array
    {
        //
        $this->authorize('update', License::class);

        $license = License::findOrFail($id);
        $license->fill($request->all());

        if ($license->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', $license, trans('admin/licenses/message.update.success')));
        }

        return Helper::formatStandardApiResponse('error', null, $license->getErrors());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  int  $id
     */
    public function destroy($id) : JsonResponse
    {
        $license = License::findOrFail($id);
        $this->authorize('delete', $license);

        if ($license->assigned_seats_count == 0) {
            // Delete the license and the associated license seats
            DB::table('license_seats')
                ->where('id', $license->id)
                ->update(['assigned_to' => null, 'asset_id' => null]);

            $licenseSeats = $license->licenseseats();
            $licenseSeats->delete();
            $license->delete();

            // Redirect to the licenses management page
            return response()->json(Helper::formatStandardApiResponse('success', null, trans('admin/licenses/message.delete.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/licenses/message.assoc_users')));
    }

    /**
     * Gets a paginated collection for the select2 menus
     *
     * @see \App\Http\Transformers\SelectlistTransformer
     */
    public function selectlist(Request $request) : array
    {
        $licenses = License::select([
            'licenses.id',
            'licenses.name',
        ]);

        if ($request->filled('search')) {
            $licenses = $licenses->where('licenses.name', 'LIKE', '%'.$request->get('search').'%');
        }

        $licenses = $licenses->orderBy('name', 'ASC')->paginate(50);

        return (new SelectlistTransformer)->transformSelectlist($licenses);
    }

    /**
     * Audit a license
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function auditLicense(Request $request, $id) : JsonResponse
    {
        $this->authorize('audit', License::class);

        $license = License::findOrFail($id);
        $settings = \App\Models\Setting::getSettings();
        $dt = \Carbon\Carbon::now()->addMonths($settings->audit_interval)->toDateString();

        $originalValues = $license->getRawOriginal();

        $license->next_audit_date = $request->filled('next_audit_date') 
            ? $request->input('next_audit_date') 
            : $dt;
        $license->last_audit_date = date('Y-m-d H:i:s');

        if ($license->save()) {
            $license->logAudit($request->input('note'), null, null, $originalValues);
            
            $payload = [
                'id' => $license->id,
                'name' => $license->name,
                'serial' => $license->serial,
                'note' => $request->input('note'),
                'next_audit_date' => \App\Helpers\Helper::getFormattedDateObject($license->next_audit_date),
            ];
            
            return response()->json(Helper::formatStandardApiResponse('success', $payload, trans('admin/licenses/message.audit.success')));
        }
        
        return response()->json(Helper::formatStandardApiResponse('error', null, $license->getErrors()));
    }

    /**
     * Get list of licenses due for audit or other action
     * Note: License auditing now operates on license seats, not licenses directly
     *
     * @param  string  $action
     * @param  string  $upcoming_status
     * @return array
     */
    public function getDueForActionList($action, $upcoming_status) : array
    {
        $this->authorize('audit', License::class);
        
        // Redirect audit requests to license seats
        if ($action == 'audits') {
            // Return empty array since license auditing is now handled at seat level
            return (new \App\Http\Transformers\LicensesTransformer)->transformLicenses(collect(), 0);
        }
        
        $licenses = License::with(['category', 'company', 'manufacturer', 'supplier', 'adminuser'])
            ->withCount('freeSeats as free_seats_count');
        
        $licenses = Company::scopeCompanyables($licenses);
        
        // Make sure the offset and limit are actually integers and do not exceed system limits
        $offset = request('offset') ? (int) request('offset') : 0;
        $limit = request('limit') ? (int) request('limit') : 50;
        
        $order = request('order') === 'asc' ? 'asc' : 'desc';
        $sort = in_array(request('sort'), ['id', 'name', 'serial', 'purchase_date']) ? request('sort') : 'created_at';
        
        $total = $licenses->count();
        $licenses = $licenses->skip($offset)->take($limit)->orderBy($sort, $order)->get();
        
        return (new \App\Http\Transformers\LicensesTransformer)->transformLicenses($licenses, $total);
    }
}
