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
        if(count($cookie_value) != count($cookie_field)) throw new \Exception("Token加密的字段未提供完整");
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
    
    /**
     * 获得指定用户的信息，包含可流量的菜单以及菜单的权限
     * @param Int $user_id 用户ID
     * @return array
     */
    public static function getInfo(int $user_id):array{
        $result = self::findFirst($user_id);
        if($result->Role){
            $return['menus'] = [];
            foreach($result->Role->Permissions as $permission){
                if($permission->type == 'menu')
                $return['menus'][$permission->Menu->menu_id] = $permission->value;
            }
            $return['role']   = $result->Role->name;
        }else{
            $return['menus']  = [];
            $return['role']   = '用户未设置角色';
        }
        $return = array_merge($result->toArray(), $return);
        return $return;
    }
    
    public function initialize():void{
        parent::initialize();
        $this->hasOne(
            'role_id',
            Roles::class,
            'role_id',
            ["alias" => "Role"]
        );
        $this->hasMany(
            'user_id',
            Logs::class,
            'user_id',
            ["alias" => "Logs"]
        );
        $this->hasMany(
            'user_id',
            UserConfigs::class,
            'user_id',
            ["alias" => "Configs"]
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
    
    public function afterSave(){
        $find_handler = PA::$config->path('user_handler');
        if($find_handler) $find_handler::afterSave($this);
    }
    public function beforeSave(){
        parent::beforeSave();
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
