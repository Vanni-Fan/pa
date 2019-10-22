<?php
namespace Power\Models;
use PowerModelBase as PMB;
use Power\Models\Users;

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
    }
}
