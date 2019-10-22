<?php
namespace Power\Models;
use PowerModelBase as PMB;
/**
 * 基础模型
 * @author vanni.fan
 */
class Configs extends PMB{
    public function initialize():void{
        parent::initialize();
        $this->belongsTo(
            'user_id',
            Users::class,
            'user_id',
            ['alias' => 'User']
        );
        $this->belongsTo(
            'rule_id',
            Rules::class,
            'rule_id',
            ["alias" => "Rule"]
        );
    }
}
