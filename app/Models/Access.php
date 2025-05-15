<?php

namespace App\Models;

use App\Models\Traits\Searchable;
use App\Presenters\Presentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;
use App\Http\Traits\UniqueUndeletedTrait;
use Watson\Validating\ValidatingTrait;

/**
 * Model for Access accounts. This represents company accounts on different platforms.
 *
 * @version    v1.0
 */
class Access extends SnipeModel
{
    use HasFactory, SoftDeletes, Loggable, Requestable, Presentable;
    use UniqueUndeletedTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'access';

    /**
     * Whether the model should inject its identifier to the unique
     * validation rules before attempting validation. If this property
     * is not set in the model it will default to true.
     *
     * @var bool
     */
    protected $injectUniqueIdentifier = true;

    use ValidatingTrait;

    protected $rules = [
        'name'                  => 'nullable|string|min:1|max:255',
        'access_tag'            => 'required|min:1|max:255|unique_undeleted:access,access_tag',
        'username'              => 'nullable|string|max:255',
        'url'                   => 'nullable|url|max:255',
        'notes'                 => 'nullable|string',
        'company_id'            => 'nullable|integer|exists:companies,id',
        'category_id'           => 'nullable|integer|exists:categories,id',
        'model_id'              => 'nullable|integer|exists:models,id',
        'status_id'             => 'nullable|integer|exists:status_labels,id',
        'assigned_to'           => 'nullable|integer',
        'expiration_date'       => 'nullable|date',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'access_tag',
        'assigned_to',
        'assigned_type',
        'category_id',
        'company_id',
        'created_by',
        'expiration_date',
        'model_id',
        'name',
        'notes',
        'requestable',
        'status_id',
        'url',
        'username',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'requestable'     => 'boolean',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    use Searchable;

    /**
     * The attributes that should be included when searching the model.
     * 
     * @var array
     */
    protected $searchableAttributes = [
        'name',
        'access_tag',
        'username',
        'url',
        'notes',
    ];

    /**
     * The relations and their attributes that should be included when searching the model.
     * 
     * @var array
     */
    protected $searchableRelations = [
        'category'     => ['name'],
        'company'      => ['name'],
        'model'        => ['name', 'model_number'],
    ];

    /**
     * Establishes the access -> company relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    /**
     * Establishes the access -> category relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    /**
     * Establishes the access -> model relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function model()
    {
        return $this->belongsTo(\App\Models\AssetModel::class, 'model_id');
    }

    /**
     * Establishes the access -> status relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function assetstatus()
    {
        return $this->belongsTo(\App\Models\Statuslabel::class, 'status_id');
    }

    /**
     * Get user who created the access
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function adminuser()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by')->withTrashed();
    }

    /**
     * Get user assignment
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function assigneduser()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to')->withTrashed();
    }

    /**
     * Checks if the access is assigned to a user
     *
     * @return bool
     */
    public function checkedOutToUser()
    {
        return $this->assigned_type == User::class;
    }

    /**
     * Checks if access is assigned to anything
     * 
     * @return bool
     */
    public function isAssigned()
    {
        return !empty($this->assigned_to);
    }

    /**
     * Get the target this access is assigned to
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function assignedTo()
    {
        if ($this->assigned_type === User::class) {
            return $this->belongsTo(User::class, 'assigned_to')->withTrashed();
        }

        return $this->belongsTo(\App\Models\Asset::class, 'assigned_to');
    }

    /**
     * Checks if the access can be deleted
     *
     * @return bool
     */
    public function isDeletable()
    {
        return Gate::allows('delete', $this)
            && ($this->deleted_at == '');
    }
    
    /**
     * Get uploads for this access
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function uploads()
    {
        return $this->hasMany('\App\Models\Actionlog', 'item_id')
            ->where('item_type', '=', Access::class)
            ->where('action_type', '=', 'uploaded')
            ->whereNotNull('filename')
            ->orderBy('created_at', 'desc');
    }
    
    /**
     * Get the type for the assigned item
     *
     * @return string
     */
    public function assignedType()
    {
        return strtolower(class_basename($this->assigned_type));
    }
} 