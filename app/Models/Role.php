<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $fillable = ['name', 'description'];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id');
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }
    public function hasAccess($employeeType, $access)
    {

        foreach ($this->permissions as $permission) {
            if ($permission->hasAccess($employeeType, $access)) {
                return true;
            }
        }
        return false;
    }
}
