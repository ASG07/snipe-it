<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Transformers\AccessTransformer;
use App\Http\Transformers\SelectlistTransformer;
use App\Models\Access;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * This controller handles all API actions related to Account Access for
 * the Snipe-IT Asset Management application.
 *
 * @version    v1.0
 */
class AccessController extends Controller
{
    /**
     * Display a listing of Access accounts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('index', Access::class);
        $allowed_columns = [
            'id',
            'name',
            'access_tag',
            'username',
            'url',
            'notes',
            'expiration_date',
            'created_at',
            'updated_at',
            'company',
            'category',
            'status_label',
            'assigned_to',
            'assigned_type'
        ];

        $access = Access::select([
            'access.id',
            'access.name',
            'access.access_tag',
            'access.username',
            'access.url',
            'access.notes',
            'access.expiration_date',
            'access.created_at',
            'access.updated_at',
            'access.company_id',
            'access.category_id',
            'access.status_id',
            'access.assigned_to',
            'access.assigned_type',
            'access.user_id'
        ]);

        if ($request->filled('search')) {
            $access = $access->TextSearch($request->input('search'));
        }

        if ($request->filled('company_id')) {
            $access->where('access.company_id', '=', $request->input('company_id'));
        }

        if ($request->filled('status_id')) {
            $access->where('access.status_id', '=', $request->input('status_id'));
        }

        // Set the offset to the API call's offset, unless the offset is higher than the actual count of items in which
        // case we override with the actual count, so we should return 0 items.
        $offset = (($access) && ($request->get('offset') > $access->count())) ? $access->count() : $request->get('offset', 0);

        // Check to make sure the limit is not higher than the max allowed
        ((config('app.max_results') >= $request->input('limit')) && ($request->filled('limit'))) ? $limit = $request->input('limit') : $limit = config('app.max_results');

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $sort = in_array($request->input('sort'), $allowed_columns) ? $request->input('sort') : 'created_at';

        $access = $access->orderBy('access.' . $sort, $order);

        $total = $access->count();
        $access = $access->skip($offset)->take($limit)->get();

        return (new AccessTransformer)->transformAccessList($access, $total);
    }

    /**
     * Store a newly created Access account in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Access::class);
        $access = new Access;
        $access->fill($request->all());
        $access->created_by = auth()->id();

        if ($access->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', $access, trans('admin/access/message.create.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $access->getErrors()));
    }

    /**
     * Display the specified Access account.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $this->authorize('view', Access::class);
        $access = Access::findOrFail($id);

        return (new AccessTransformer)->transformAccess($access);
    }

    /**
     * Update the specified Access account in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->authorize('update', Access::class);
        $access = Access::findOrFail($id);
        $access->fill($request->all());

        if ($access->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', $access, trans('admin/access/message.update.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $access->getErrors()));
    }

    /**
     * Remove the specified Access account from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->authorize('delete', Access::class);
        $access = Access::findOrFail($id);

        if ($access->delete()) {
            return response()->json(Helper::formatStandardApiResponse('success', null, trans('admin/access/message.delete.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, trans('admin/access/message.delete.error')));
    }

    /**
     * Gets a paginated collection for the select2 menus
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function selectlist(Request $request): JsonResponse
    {
        $this->authorize('view', Access::class);
        $access = Access::select([
            'access.id',
            'access.name',
            'access.access_tag',
        ]);

        if ($request->filled('search')) {
            $access = $access->where('access.name', 'LIKE', '%' . $request->get('search') . '%')
                ->orWhere('access.access_tag', 'LIKE', '%' . $request->get('search') . '%');
        }

        $access = $access->orderBy('access.access_tag', 'ASC')->paginate(50);

        return (new SelectlistTransformer)->transformSelectlist($access);
    }
} 