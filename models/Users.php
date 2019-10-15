<?php
namespace Power\Models;
use PowerModelBase as PMB;
use Utils;
use PA;
/**
 * 用户模型，可指定Handler函数
 * @author vanni.fan
 */
class Users extends PMB{
    public static function makeToke(array $user){
        $cookie_field = PA::$config['cookie_fields']->toArray();
        $cookie_value = array_intersect_key($user, $cookie_field);
        if(count($cookie_value) != count($cookie_field)) throw \Exception("Token加密的字段未提供完整");
        if(!empty($cookie_value['login_ip'])){
            $cookie_value['login_ip'] = ip2long($cookie_value['login_ip']);
        }
        return base64_encode(
            Utils::encrypt(
                Utils::pack($cookie_field, $cookie_value),
                PA::$config['cookie_key']
            )
        );
    }
    public static function parseToken(string $token){
        $token = Utils::unpack(
            PA::$config['cookie_fields']->toArray(),
            Utils::decrypt(
                base64_decode($token),
                PA::$config['cookie_key']
            )
        );
        if(!empty($token['login_ip'])){
            $token['login_ip'] = long2ip($token['login_ip']);
        }
        return $token;
    }
    
    public static function getInfo($user_id){
        $result = self::findFirst($user_id);
        $return['rules']  = $result->roles ? json_decode($result->roles->rules,1) : [];
        $return['role']   = $result->roles ? $result->roles->name : '未指定角色';
        $return['extensions'] = $result->roles ? json_decode($result->roles->extensions, 1) : [];
        $return = array_merge($result->toArray(), $return);
        return $return;
    }
    
    public function initialize(){
        parent::initialize();
        $this->hasOne(
            'role_id',
            Roles::class,
            'role_id',
            ["alias" => "roles"]
        );
        $this->hasMany(
            'user_id',
            Logs::class,
            'user_id',
            ["alias" => "Logs"]
        );
    }
    
    public function afterSave(){
        $find_handler = PA::$config->path('user_handler');
        if($find_handler) $find_handler::afterSave($this);
    }
    public function beforeSave(){
        $find_handler = PA::$config->path('user_handler');
        if($find_handler) $find_handler::beforeSave($this);
    }
    public function afterFetch(){
        $find_handler = PA::$config->path('user_handler');
        if($find_handler) $find_handler::afterFetch($this);
    }
    public function afterDelete(){
        $find_handler = PA::$config->path('user_handler');
        if($find_handler) $find_handler::afterDelete($this);
    }
    public function beforeDelete(){
        $find_handler = PA::$config->path('user_handler');
        if($find_handler) $find_handler::beforeDelete($this);
    }
}
