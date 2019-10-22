<?php
namespace Power\Models;
use Power\Models\Users;
use PowerModelBase as PMB;

/**
 * 基础模型
 * @author vanni.fan
 */
class Roles extends PMB{
    public function initialize():void{
        parent::initialize();
        $this->belongsTo(
            'role_id',
            Users::class,
            'role_id',
            ["alias" => "User"]
        );
        $this->hasMany(
            'role_id',
            RoleExtensions::class,
            'role_id',
            ["alias" => "Extensions"]
        );
        $this->hasMany(
            'role_id',
            RoleRules::class,
            'role_id',
            ["alias" => "Rules"]
        );
    }
}
