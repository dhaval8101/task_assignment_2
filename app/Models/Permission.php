<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description'];
    protected $hidden = [
        'updated_at',
        'created_at'
    ];
    public function access()
    {
        return $this->hasMany(ModelPermission::class);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'model_permissions', 'permission_id', 'module_id')->withPivot(['module_id', 'permission_id', 'add_access', 'view_access', 'edit_access', 'delete_access']);
    }
    public function hasAccess($employeeType, $access)
    {
        foreach ($this->modules as $module) {
            $model_permissions = $module->where('name', $employeeType)->first();
            $getPivot = $module->pivot;
            if ($model_permissions && $getPivot->$access) {
                return true;
            }
        }
        return false;
    }
}
