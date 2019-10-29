<?php
namespace Power\Models;
use PowerModelBase as PMB;
/**
 * 基础模型
 * @author vanni.fan
 */
class UserConfigs extends PMB{
    public function initialize():void{
        parent::initialize();
        $this->belongsTo(
            'user_id',
            Users::class,
            'user_id',
            ['alias' => 'User']
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
