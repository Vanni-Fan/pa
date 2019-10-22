<?php
namespace Power\Models;
use PowerModelBase as PMB;
/**
 * 基础模型
 * @author vanni.fan
 */
class RoleRules extends PMB{
    public function initialize():void{
        parent::initialize();
        $this->belongsTo(
            'role_id',
            Roles::class,
            'role_id',
            ["alias" => "Role"]
        );
        $this->belongsTo(
            'rule_id',
            Rules::class,
            'rule_id',
            ["alias" => "Rule"]
        );
    }
}
