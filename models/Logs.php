<?php
namespace Power\Models;
use PowerModelBase as PMB;
/**
 * 基础模型
 * @author vanni.fan
 */
class Logs extends PMB{
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
            ['alias' => 'Menu']
        );
    }
}
