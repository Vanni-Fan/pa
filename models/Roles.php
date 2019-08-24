<?php
namespace Power\Models;
use PowerModelBase as PMB;
/**
 * 基础模型
 * @author vanni.fan
 */
class Roles extends PMB{
    public function initialize(){
        parent::initialize();
        $this->belongsTo('role_id','Power\\Models\\Users','role_id',["alias" => "User"]);
    }
}
