<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'display_name', 'parent_id'];

    /**
     * The roles that belong to the permission.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Children permissions.
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Permission::class, 'parent_id')->with('children.parent');
    }

    /**
     * Parent permissions.
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'parent_id')->with('parent');
    }

    /**
     * Permissions tree.
     *
     * @return Collection
     */
    public static function tree(): Collection
    {
        return self::query()
            ->whereNull('parent_id')
            ->with('children.parent')
            ->get();
    }
}
