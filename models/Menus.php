<?php
namespace Power\Models;
use PowerModelBase as PMB;
use Utils;
use Logger\Logger;
/**
 * 基础模型
 * @author vanni.fan
 */
class Menus extends PMB{
    
    /**
     * 将多维菜单，展开成2维菜单，去掉 sub ，添加 level
     * @param array $original_menus
     * @param array $plat_menus
     * $all_menus, $out_menus
     */
    public static function expandMenu(array &$original_menus, array &$plat_menus, int $level=0){
        foreach($original_menus as $menu){
            $index = count($plat_menus);
            $plat_menus[$index] = $menu;
            $plat_menus[$index]['level'] = $level;
            unset($plat_menus[$index]['sub']);
            if($menu['sub']) self::expandMenu($menu['sub'], $plat_menus, $level+1);
        }
    }

    /**
     * 1个字节，8位 0x00000000 对应权限： [增+, 删+, 改+, 查+, 增, 删, 改, 查]
     * @param string $action 事件，比如：[append|new,   delete,   display|update,   index|list|*]
     * @param int    $owner 数据所有者
     * @param int    $operator 数据操作者
     * @param int    $operator_power 数据操作者权限
     * @return bool
     */
    public static function isAllowed(string $action, int $owner, int $operator, int $operator_power):bool{
        $action_list = [
            # 增
            'append' =>0b00001000,
            'new'    =>0b00001000,
            # 删
            'delete' =>0b00000100,
            # 改
            'display'=>0b00000010,
            'update' =>0b00000010,
            # 查
            'index'  =>0b00000001,
            'list'   =>0b00000001
        ];
        $power = $action_list[$action] ?? 0b00000001; # 如果是自定义权限，默认为查询权限
        if($owner !== $operator){
            $power |= $power << 4; // 左移4位，看是否有超级权限
        }
        return ($operator_power & $power) === $power;
    }

    /**
     * 获得指定子菜单的全部关联菜单
     * @param array $rule
     * @return array
     */
    public static function getRuleExtend(array $rule):array{
        $rule['parent_id'] = $rule['parent_id'] ?: 0;
        $rule['params']    = $rule['params'] ? json_decode($rule['params'],1) : [];
        $rule['router']    = $rule['router'] ? json_decode($rule['router'],1) : [];
        $rule['name']      = Utils::replaceTemplateVal($rule['name'],$rule['params']); # 替换菜 url 和 name 中的模板信息
        $rule['sub']       = [];
        return $rule;
    }

    /**
     * 获得指定菜单的所有菜单
     */
    public static function allBySubRule(array $sub_menus){
        // 获得子菜单的完整路径
        $menu_ids = [];
        foreach($sub_menus as $menu_id){
            $pids = [];
            self::getParentIds($menu_id,$pids);
            $menu_ids = array_merge($menu_ids, $pids);
        }
//        return self::all(array_column($menu_ids, 'menu_id'));
        $out = [];
        self::getChildIds($out,0, array_column($menu_ids, 'menu_id'));
        return $out;
    }
    
    /**
     * 获得指定的父级菜单下面的所有子菜单
     */
    public static function getChildIds(array &$out, int $prent_id=0, array $include_ids=[]){
        $bind  = [];
        $where = 'is_enabled=1 ';
        if(!$prent_id){
            $where .= 'and parent_id is null';
        }else{
            $where .= 'and parent_id = ?0';
            $bind[] = $prent_id;
        }
        $query = ['conditions'=>$where,'bind'=>$bind,'order'=>'index'];
        if($include_ids){
            $query['conditions'] .= ' AND menu_id IN ({menu_ids:array})';
            $query['bind']['menu_ids'] = $include_ids;
        }
        
        foreach(self::find($query) as $item){
            $index = count($out);
            $out[$index] = self::getRuleExtend($item->toArray());
            $out[$index]['sub'] = [];
            self::getChildIds($out[$index]['sub'], $item->menu_id, $include_ids);
        }
    }

    /**
     * 获得子菜单
     * @param int $parent_id
     * @return array
     */
    public static function getMenus(int $parent_id=0){
        $out = [];
        self::getChildIds($out, $parent_id);
        return $out;
    }

    /**
     * 获得扁平化的菜单
     * @param int $parent_id
     * @return array
     */
    public static function getFlatMenus(int $parent_id=0){
        $all_menus = self::getMenus($parent_id);
        $out_menus = [];
        Menus::expandMenu($all_menus, $out_menus);
        return $out_menus;
    }

    /**
     * 获得指定菜单的所有上级菜单
     */
    public static function getParentIds(int $menu_id, array &$parents){
        static $menus = [];
        if($menu_id){
            if(isset($menus[$menu_id])) {
                $parent = $menus[$menu_id];
            }else{
                $rule = Menus::findFirstByMenuId($menu_id);
                if($rule){
                    $parent = self::getRuleExtend($rule->toArray());
                    $menus[$menu_id] = $parent;
                }
            }
            if(isset($parent)){
                array_unshift($parents, $parent);
                if($parent['parent_id']){
                    self::getParentIds($parent['parent_id'], $parents);
                }
            }
        }
    }
    
    /**
     * 删除指定的权限
     */
    public static function deleteRule(int $menu_id){
        $where = ['menu_id=?0','bind'=>[$menu_id]];
        # 删除自己
        self::findFirst($where)->delete();
        
        # 删除对应的角色中的配置
        RoleMenus::find($where)->delete();
        
        # 删除对应的扩展中的信息
        Configs::find($where)->delete();
        
        # 删除配置表中的记录
        UserConfigs::find($where)->delete();
    }
    
    public function initialize():void{
        parent::initialize();
        $this->hasMany(
            'menu_id',
            UserConfigs::class,
            'menu_id',
            ['alias' => 'UserConfigs']
        );
        $this->hasMany(
            'menu_id',
            Configs::class,
            'menu_id',
            ["alias" => "Configs"]
        );
        $this->hasMany(
            'menu_id',
            Permissions::class,
            'menu_id',
            ["alias" => "Permissions"]
        );
        $this->hasMany(
            'menu_id',
            Logs::class,
            'menu_id',
            ["alias" => "Logs"]
        );
        $this->hasMany(
            'menu_id',
            Menus::class,
            'parent_id',
            ["alias" => "SubMenus"]
        );
        $this->belongsTo(
            'parent_id',
            Menus::class,
            'menu_id',
            ["alias" => "Parent"]
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
}
