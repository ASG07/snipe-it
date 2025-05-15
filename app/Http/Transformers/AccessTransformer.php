<?php

namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\Access;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

class AccessTransformer
{
    /**
     * Transform a collection of Access accounts
     *
     * @param Collection $access
     * @param int $total
     * @return array
     */
    public function transformAccessList(Collection $access, int $total)
    {
        $array = [
            'total' => $total,
            'rows' => []
        ];

        foreach ($access as $item) {
            $array['rows'][] = self::transformAccess($item);
        }

        return $array;
    }

    /**
     * Transform a single Access account
     *
     * @param Access $access
     * @return array
     */
    public function transformAccess(Access $access)
    {
        $array = [
            'id' => (int) $access->id,
            'name' => e($access->name),
            'access_tag' => e($access->access_tag),
            'username' => e($access->username),
            'url' => e($access->url),
            'notes' => e($access->notes),
            'expiration_date' => Helper::getFormattedDateObject($access->expiration_date, 'date'),
            'created_at' => Helper::getFormattedDateObject($access->created_at, 'datetime'),
            'updated_at' => Helper::getFormattedDateObject($access->updated_at, 'datetime'),
            'assigned_to' => $this->transformAssignedTo($access),
            'company' => ($access->company) ? [
                'id' => (int) $access->company->id,
                'name' => e($access->company->name)
            ] : null,
            'category' => ($access->category) ? [
                'id' => (int) $access->category->id,
                'name' => e($access->category->name)
            ] : null,
            'model' => ($access->model) ? [
                'id' => (int) $access->model->id,
                'name' => e($access->model->name)
            ] : null,
            'status_label' => ($access->assetstatus) ? [
                'id' => (int) $access->assetstatus->id,
                'name' => e($access->assetstatus->name),
                'status_type' => e($access->assetstatus->getStatuslabelType()),
                'status_meta' => e($access->assetstatus->getStatuslabelType())
            ] : null,
            'user_can_checkout' => false,
            'available_actions' => $this->transformActionPermissions($access)
        ];

        // Check checkout/checkin permissions
        $permissions_array['checkout'] = Gate::allows('checkout', Access::class);
        $permissions_array['checkin'] = Gate::allows('checkin', Access::class);
        $permissions_array['update'] = Gate::allows('update', Access::class);
        $permissions_array['delete'] = Gate::allows('delete', Access::class);
        $array['user_permissions'] = $permissions_array;

        if ((!$access->assigned_to) && ($array['user_permissions']['checkout'])) {
            $array['user_can_checkout'] = true;
        }

        return $array;
    }

    /**
     * Transform assigned_to details for Access account
     *
     * @param Access $access
     * @return array|null
     */
    public function transformAssignedTo(Access $access)
    {
        if ($access->assigned_to) {
            return [
                'id' => (int) $access->assigned_to,
                'username' => e($access->assignedto->username),
                'name' => e($access->assignedto->getFullNameAttribute()),
                'first_name' => e($access->assignedto->first_name),
                'last_name' => e($access->assignedto->last_name),
            ];
        }

        return null;
    }

    /**
     * Transform action permissions for an Access account
     *
     * @param Access $access
     * @return array
     */
    public function transformActionPermissions(Access $access)
    {
        $actions = [];

        if (Gate::allows('update', $access)) {
            $actions[] = ['label' => 'Update', 'route' => route('access.update', $access)];
        }

        if (Gate::allows('delete', $access)) {
            $actions[] = ['label' => 'Delete', 'route' => route('access.destroy', $access)];
        }

        if (Gate::allows('checkout', $access)) {
            $actions[] = ['label' => 'Checkout', 'route' => route('access.checkout.show', $access)];
        }

        if ($access->isAssigned() && Gate::allows('checkin', $access)) {
            $actions[] = ['label' => 'Checkin', 'route' => route('access.checkin.show', $access)];
        }

        return $actions;
    }
} 