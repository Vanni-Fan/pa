<?php
namespace Power\Models;
use Power\Models\Users;
use PowerModelBase as PMB;

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
        $this->hasMany(
            'role_id',
            Permissions::class,
            'role_id',
            ["alias" => "Permissions"]
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
    /**
     * 获得角色的信息，包括角色当前的权限信息
     */
    public static function getPermissions(int $role_id):array{
        $info = self::findFirst($role_id);
        $rules = []; // 包含 ['menu','config'] 两个下标，menu 表示菜单权限； config 表示配置权限
        $defined_config = []; //已经配置的权限
        foreach($info->Permissions as $permission){
            if($permission->type === 'menu'){
                $rules['menu'][ $permission->menu_id ] = $permission->value;
            }else{
                $config = $permission->Config;
                $value  = ($config->var_type === 'text') ? $permission->value : json_decode($permission->value,1);
                $defined_config[ $permission->menu_id ?? 0 ][$config->var_name] = $value;
            }
        }
        // 查询配置中的默认权限，未分配给角色的权限
        foreach(Configs::getConfigs('rule') as $config){
            $menu_id = $config['menu_id'] ?: 0;
            if(!isset($defined_config[$menu_id][$config['var_name']])){
                $value = ($config['var_type'] === 'text') ? $config['var_default'] : json_decode($config['var_default'],1);
                $defined_config[$menu_id][$config['var_name']] = $value;
            }
        }
        $rules['config'] = $defined_config;
        return $rules;
    }
}
