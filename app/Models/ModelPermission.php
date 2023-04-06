<?php

namespace App\Models;
// PermissionAccess.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelPermission extends Model
{
    protected $fillable = [
        'module_id',
        'delete_access',
        'edit_access',
        'add_access',
        'view_access'
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
