<?php
namespace Power\Models;
use PowerModelBase as PMB;
/**
 * 基础模型
 * @author vanni.fan
 */
class Permissions extends PMB{
    public function initialize():void{
        parent::initialize();
        $this->belongsTo(
            'role_id',
            Roles::class,
            'role_id',
            ["alias" => "Role"]
        );
        $this->belongsTo(
            'menu_id',
            Menus::class,
            'menu_id',
            ["alias" => "Menu"]
        );
        $this->belongsTo(
            'config_id',
            Configs::class,
            'config_id',
            ["alias" => "Config"]
        );
        $this->belongsTo(
            'created_user',
            Users::class,
            'user_id',
            ["alias" => "CreatedUser"]
        );
        $this->belongsTo(
            'updated_user',
            Users::class,
            'user_id',
            ["alias" => "UpdatedUser"]
        );
    }
}
