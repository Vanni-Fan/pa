<?php
namespace Power\Models;
use PowerModelBase as PMB;
/**
 * 基础模型
 * @author vanni.fan
 */
class RoleExtensions extends PMB{
    public function initialize():void {
        parent::initialize();
        $this->hasOne(
            'role_id',
            Roles::class,
            'role_id',
            ["alias" => "Role"]
        );
        $this->hasOne(
            'extend_id',
            Extensions::class,
            'extend_id',
            ["alias" => "Extension"]
        );
    }
}
