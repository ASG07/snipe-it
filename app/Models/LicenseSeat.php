<?php

namespace App\Models;

use App\Models\Traits\Acceptable;
use App\Notifications\CheckinLicenseNotification;
use App\Notifications\CheckoutLicenseNotification;
use App\Presenters\Presentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class LicenseSeat extends SnipeModel implements ICompanyableChild
{
    use CompanyableChildTrait;
    use HasFactory;
    use Loggable;
    use SoftDeletes;

    protected $presenter = \App\Presenters\LicenseSeatPresenter::class;
    use Presentable;

    protected $guarded = 'id';
    protected $table = 'license_seats';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'assigned_to',
        'asset_id',
        'notes',
        'last_audit_date',
        'next_audit_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'last_audit_date' => 'datetime',
        'next_audit_date' => 'datetime:Y-m-d',
    ];

    use Acceptable;

    public function getCompanyableParents()
    {
        return ['asset', 'license'];
    }

    /**
     * Determine whether the user should be required to accept the license
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return bool
     */
    public function requireAcceptance()
    {
        if ($this->license && $this->license->category) {
            return $this->license->category->require_acceptance;
        }
        return false;
    }

    public function getEula()
    {
        return $this->license->getEula();
    }

    /**
     * Establishes the seat -> license relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function license()
    {
        return $this->belongsTo(\App\Models\License::class, 'license_id');
    }

    /**
     * Establishes the seat -> assignee relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to')->withTrashed();
    }

    /**
     * Establishes the seat -> asset relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function asset()
    {
        return $this->belongsTo(\App\Models\Asset::class, 'asset_id')->withTrashed();
    }

    /**
     * Determines the assigned seat's location based on user
     * or asset its assigned to
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return object|null
     */
    public function location()
    {
        if (($this->user) && ($this->user->location)) {
            return $this->user->location;
        } elseif (($this->asset) && ($this->asset->location)) {
            return $this->asset->location;
        }
        
        return null;
    }

    /**
     * Query builder scope to order on department
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order         Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderDepartments($query, $order)
    {
        return $query->leftJoin('users as license_seat_users', 'license_seats.assigned_to', '=', 'license_seat_users.id')
            ->leftJoin('departments as license_user_dept', 'license_user_dept.id', '=', 'license_seat_users.department_id')
            ->whereNotNull('license_seats.assigned_to')
            ->orderBy('license_user_dept.name', $order);
    }

    /**
     * Query license seats due for audit based on next_audit_date
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Models\Setting  $settings
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDueForAudit($query, $settings)
    {
        $interval = $settings->audit_warning_days ?? 0;
        $warningDate = Carbon::now()->addDays($interval);
        
        return $query->whereNotNull('license_seats.next_audit_date')
                     ->whereNull('license_seats.deleted_at')
                     ->where('license_seats.next_audit_date', '<=', $warningDate->format('Y-m-d'))
                     ->where('license_seats.next_audit_date', '>=', Carbon::now()->format('Y-m-d'));
    }

    /**
     * Query license seats overdue for audit
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdueForAudit($query)
    {
        return $query->whereNotNull('license_seats.next_audit_date')
                     ->whereNull('license_seats.deleted_at')
                     ->where('license_seats.next_audit_date', '<', Carbon::now()->format('Y-m-d'));
    }

    /**
     * Query license seats that are due or overdue for audit
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Models\Setting  $settings
     * @return \Illuminate\Database\Eloquent\Builder  
     */
    public function scopeDueOrOverdueForAudit($query, $settings)
    {
        return $query->where(function ($query) {
            $query->overdueForAudit();
        })->orWhere(function ($query) use ($settings) {
            $query->dueForAudit($settings);
        });
    }



    /**
     * Query all license seats for audit purposes
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllSeats($query)
    {
        return $query->whereNull('license_seats.deleted_at');
    }

    /**
     * Query license seats that have been audited (have a last_audit_date)
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAudited($query)
    {
        return $query->whereNotNull('license_seats.last_audit_date')
                     ->whereNull('license_seats.deleted_at');
    }
}
