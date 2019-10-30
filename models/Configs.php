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
            'menu_id',
            Menus::class,
            'menu_id',
            ["alias" => "Menu"]
        );
        $this->hasMany(
            'config_id',
            UserConfigs::class,
            'config_id',
            ["alias" => "UserConfigs"]
        );
        $this->hasMany(
            'config_id',
            Permissions::class,
            'config_id',
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
     * 获得用户的配置信息，包含两个下标： rule 和 attribute
     *
     * [
     *    rule      => [ menu_id => [ 权限英文名 => 值 ] ] ,
     *    attribute => [ menu_id => [ 属性英文名 => 值 ] ]
     * ]
     *
     * 其中 menu_id 为 0 的话，表示全局，否则对应特点的权限菜单
     *
     * @param int $user_id
     * @return array
     */
    public static function getConfigsByUser($user_id=0)
    {
        $return = ['rule'=>[], 'attribute'=>[]];

        // 获得用户配置的权限
        $user_permissions = self::query()->createBuilder()->from(['u' => Users::class])
            ->InnerJoin(Roles::class,'u.role_id=r.role_id and u.is_enabled=1 and r.is_enabled=1', 'r')
            ->InnerJoin(Permissions::class,'p.role_id=r.role_id', 'p')
            ->InnerJoin(Configs::class,'p.config_id=c.config_id and c.is_enabled=1', 'c')
            ->columns('c.var_name, c.menu_id, p.value')
            ->where('c.type=?0 and u.user_id=?1', ['rule',$user_id])
            ->getQuery()->execute();
        foreach($user_permissions as $permission){
            $return['rule'][$permission->menu_id??0][$permission->var_name] = $permission->value;
        }

        // 获得用户配置的属性
        $user_attributes = self::query()->createBuilder()->from(['c' => Configs::class])
            ->LeftJoin(UserConfigs::class, 'uc.config_id=c.config_id and c.is_enabled=1 and uc.is_enabled=1', 'uc')
            ->LeftJoin(Users::class, 'uc.user_id=u.user_id and u.is_enabled=1', 'u')
            ->columns('c.var_name, c.var_default, uc.value, c.menu_id')
            ->where('c.type=?0 and (u.user_id is null or u.user_id=?1)', ['attribute', $user_id])
            ->getQuery()->execute();
        foreach($user_attributes as $config){
            $return['attribute'][$config->menu_id??0][$config->var_name] = $config->value ?? $config->var_default;
        }
//        echo '<pre>',print_r($return,1);
//        exit;
        return $return;
    }
    
    public static function getConfigs(string $type=null, bool $group=false):array {
        $where = $type ? ['type=?0 and is_enabled=1','bind'=>[$type]] : [];
        $extends = [];
        foreach(Configs::find($where) as $e){
            $e = $e->toArray();
            $e['menu_id'] = $e['menu_id'] ?: 0;
            if($group) {
                $extends[$e['menu_id']][] = $e;
            }else{
                $extends[] = $e;
            }
        }
        return $extends;
    }
}
