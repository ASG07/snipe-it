<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Transformers\LicenseSeatsTransformer;
use App\Models\LicenseSeat;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LicenseSeatsAuditController extends Controller
{
    /**
     * Returns license seats that are due for audit
     *
     * @param Request $request
     * @return JsonResponse|array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function due(Request $request): JsonResponse|array
    {
        $this->authorize('audit', \App\Models\License::class);
        
        $settings = Setting::getSettings();
        
        $licenseSeatsDue = LicenseSeat::with('license', 'user', 'asset', 'user.department')
            ->dueForAudit($settings);

        // handle date range filter for last audit date
        if ($request->filled('audit_date_start') || $request->filled('audit_date_end')) {
            if ($request->filled('audit_date_start')) {
                $licenseSeatsDue = $licenseSeatsDue->where('license_seats.last_audit_date', '>=', Carbon::parse($request->input('audit_date_start'))->startOfDay());
            }
            if ($request->filled('audit_date_end')) {
                $licenseSeatsDue = $licenseSeatsDue->where('license_seats.last_audit_date', '<=', Carbon::parse($request->input('audit_date_end'))->endOfDay());
            }
        }

        // handle search parameter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $licenseSeatsDue = $licenseSeatsDue->leftJoin('licenses', 'license_seats.license_id', '=', 'licenses.id')
                ->leftJoin('users', 'license_seats.assigned_to', '=', 'users.id')
                ->leftJoin('assets', 'license_seats.asset_id', '=', 'assets.id')
                ->select('license_seats.*')
                ->where(function ($query) use ($search) {
                    $query->where('licenses.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('licenses.serial', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('assets.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('assets.asset_tag', 'LIKE', '%' . $search . '%');
                });
        }

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        
        // set the order
        if ($request->input('sort') == 'license_name') {
            $licenseSeatsDue->leftJoin('licenses as sort_licenses', 'license_seats.license_id', '=', 'sort_licenses.id')
                ->select('license_seats.*')
                ->orderBy('sort_licenses.name', $order);
        } elseif ($request->input('sort') == 'license_serial') {
            $licenseSeatsDue->leftJoin('licenses as sort_licenses', 'license_seats.license_id', '=', 'sort_licenses.id')
                ->select('license_seats.*')
                ->orderBy('sort_licenses.serial', $order);
        } else {
            $licenseSeatsDue->orderBy('license_seats.updated_at', $order);
        }
        
        $total = $licenseSeatsDue->count();
        
        // make sure the offset and limit are actually integers and do not exceed system limits
        $offset = ($request->input('offset') > $licenseSeatsDue->count()) ? $licenseSeatsDue->count() : app('api_offset_value');
        
        if ($offset >= $total) {
            $offset = 0;
        }
        
        $limit = app('api_limit_value');
        
        $licenseSeatsDue = $licenseSeatsDue->skip($offset)->take($limit)->get();
        
        return (new LicenseSeatsTransformer)->transformLicenseSeats($licenseSeatsDue, $total);
    }
    
    /**
     * Returns license seats that are overdue for audit
     *
     * @param Request $request
     * @return JsonResponse|array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function overdue(Request $request): JsonResponse|array
    {
        $this->authorize('audit', \App\Models\License::class);
        
        $licenseSeatsOverdue = LicenseSeat::with('license', 'user', 'asset', 'user.department')
            ->overdueForAudit();
        
        // handle date range filter for last audit date
        if ($request->filled('audit_date_start') || $request->filled('audit_date_end')) {
            if ($request->filled('audit_date_start')) {
                $licenseSeatsOverdue = $licenseSeatsOverdue->where('license_seats.last_audit_date', '>=', Carbon::parse($request->input('audit_date_start'))->startOfDay());
            }
            if ($request->filled('audit_date_end')) {
                $licenseSeatsOverdue = $licenseSeatsOverdue->where('license_seats.last_audit_date', '<=', Carbon::parse($request->input('audit_date_end'))->endOfDay());
            }
        }
        
        // handle search parameter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $licenseSeatsOverdue = $licenseSeatsOverdue->leftJoin('licenses', 'license_seats.license_id', '=', 'licenses.id')
                ->leftJoin('users', 'license_seats.assigned_to', '=', 'users.id')
                ->leftJoin('assets', 'license_seats.asset_id', '=', 'assets.id')
                ->select('license_seats.*')
                ->where(function ($query) use ($search) {
                    $query->where('licenses.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('licenses.serial', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('assets.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('assets.asset_tag', 'LIKE', '%' . $search . '%');
                });
        }
        
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        
        // set the order
        if ($request->input('sort') == 'license_name') {
            $licenseSeatsOverdue->leftJoin('licenses as sort_licenses', 'license_seats.license_id', '=', 'sort_licenses.id')
                ->select('license_seats.*')
                ->orderBy('sort_licenses.name', $order);
        } elseif ($request->input('sort') == 'license_serial') {
            $licenseSeatsOverdue->leftJoin('licenses as sort_licenses', 'license_seats.license_id', '=', 'sort_licenses.id')
                ->select('license_seats.*')
                ->orderBy('sort_licenses.serial', $order);
        } else {
            $licenseSeatsOverdue->orderBy('license_seats.updated_at', $order);
        }
        
        $total = $licenseSeatsOverdue->count();
        
        // make sure the offset and limit are actually integers and do not exceed system limits
        $offset = ($request->input('offset') > $licenseSeatsOverdue->count()) ? $licenseSeatsOverdue->count() : app('api_offset_value');
        
        if ($offset >= $total) {
            $offset = 0;
        }
        
        $limit = app('api_limit_value');
        
        $licenseSeatsOverdue = $licenseSeatsOverdue->skip($offset)->take($limit)->get();
        
        return (new LicenseSeatsTransformer)->transformLicenseSeats($licenseSeatsOverdue, $total);
    }
    
    /**
     * Returns all license seats
     *
     * @param Request $request
     * @return JsonResponse|array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function all(Request $request): JsonResponse|array
    {
        $this->authorize('audit', \App\Models\License::class);
        
        $licenseSeatsAll = LicenseSeat::with('license', 'user', 'asset', 'user.department')
            ->allSeats();
        
        // handle date range filter for last audit date
        if ($request->filled('audit_date_start') || $request->filled('audit_date_end')) {
            if ($request->filled('audit_date_start')) {
                $licenseSeatsAll = $licenseSeatsAll->where('license_seats.last_audit_date', '>=', Carbon::parse($request->input('audit_date_start'))->startOfDay());
            }
            if ($request->filled('audit_date_end')) {
                $licenseSeatsAll = $licenseSeatsAll->where('license_seats.last_audit_date', '<=', Carbon::parse($request->input('audit_date_end'))->endOfDay());
            }
        }
        
        // handle search parameter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $licenseSeatsAll = $licenseSeatsAll->leftJoin('licenses', 'license_seats.license_id', '=', 'licenses.id')
                ->leftJoin('users', 'license_seats.assigned_to', '=', 'users.id')
                ->leftJoin('assets', 'license_seats.asset_id', '=', 'assets.id')
                ->select('license_seats.*')
                ->where(function ($query) use ($search) {
                    $query->where('licenses.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('licenses.serial', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('assets.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('assets.asset_tag', 'LIKE', '%' . $search . '%');
                });
        }
        
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        
        // set the order
        if ($request->input('sort') == 'license_name') {
            $licenseSeatsAll->leftJoin('licenses as sort_licenses', 'license_seats.license_id', '=', 'sort_licenses.id')
                ->select('license_seats.*')
                ->orderBy('sort_licenses.name', $order);
        } elseif ($request->input('sort') == 'license_serial') {
            $licenseSeatsAll->leftJoin('licenses as sort_licenses', 'license_seats.license_id', '=', 'sort_licenses.id')
                ->select('license_seats.*')
                ->orderBy('sort_licenses.serial', $order);
        } else {
            $licenseSeatsAll->orderBy('license_seats.updated_at', $order);
        }
        
        $total = $licenseSeatsAll->count();
        
        // make sure the offset and limit are actually integers and do not exceed system limits
        $offset = ($request->input('offset') > $licenseSeatsAll->count()) ? $licenseSeatsAll->count() : app('api_offset_value');
        
        if ($offset >= $total) {
            $offset = 0;
        }
        
        $limit = app('api_limit_value');
        
        $licenseSeatsAll = $licenseSeatsAll->skip($offset)->take($limit)->get();
        
        return (new LicenseSeatsTransformer)->transformLicenseSeats($licenseSeatsAll, $total);
    }
    

}
