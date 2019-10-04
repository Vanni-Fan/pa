<?php
namespace Power\Models;
use PowerModelBase as PMB;
use Utils;
use Logger\Logger;
/**
 * 基础模型
 * @author vanni.fan
 */
class Rules extends PMB{
    /**
     * 获得全部菜单
     */
//    public static function all($ids = []){
//        $menus  = [];
//        $parent = [0=>&$menus];
//        $query  = ['order'=>'level,index'];
//        if($ids){
//            $query['conditions'] = 'rule_id IN ({rule_ids:array})';
//            $query['bind']['rule_ids'] = $ids;
//        }
////        exit(print_r(self::find($query)->toArray()));
//        foreach(self::find($query) as $row){
//            $row  = self::getRuleExtend($row->toArray());
//            $pid  = $row['parent_id'];
//            $subs = count($parent[$pid]);
//            $parent[$pid][$subs] = $row;// 将自己添加到父级里面
//            $parent[$row['rule_id']] = &$parent[$pid][$subs]['sub'];
//        }
//        return $menus;
//    }
    
    /**
     * 将多维菜单，展开成2维菜单，去掉 sub ，添加 level
     * @param array $rules
     * @param array $menus
     */
    public static function expandMenu(array &$rules, array &$menus, int $level=0){
        foreach($rules as $rule){
            $index = count($menus);
            $menus[$index] = $rule;
            $menus[$index]['level'] = $level;
            unset($menus[$index]['sub']);
            if($rule['sub']) self::expandMenu($rule['sub'], $menus, $level+1);
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
    public static function allBySubRule(array $sub_rules){
        $rule_ids = [];
        foreach($sub_rules as $rule_id){
            $pids = [];
            self::getParentIds($rule_id,$pids);
            $rule_ids = array_merge($rule_ids, $pids);
        }
//        return self::all(array_column($rule_ids, 'rule_id'));
        $out = [];
        self::getChildIds($out,0, array_column($rule_ids, 'rule_id'));
        return $out;
    }
    
    /**
     * 获得指定的父级菜单下面的所有子菜单
     */
    public static function getChildIds(array &$out, int $prent_id=0, array $include_ids=[]){
        $query = ['conditions'=>'parent_id=?0','bind'=>[$prent_id],'order'=>'index'];
        if($include_ids){
            $query['conditions'] .= ' AND rule_id IN ({rule_ids:array})';
            $query['bind']['rule_ids'] = $include_ids;
        }
        
        foreach(self::find($query) as $item){
            $index = count($out);
            $out[$index] = self::getRuleExtend($item->toArray());
            $out[$index]['sub'] = [];
            self::getChildIds($out[$index]['sub'], $item->rule_id, $include_ids);
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
        Rules::expandMenu($all_menus, $out_menus);
        return $out_menus;
    }

    /**
     * 获得指定菜单的所有上级菜单
     */
    public static function getParentIds(int $rule_id, array &$parents){
        static $rules = [];
        if($rule_id){
            if(isset($rules[$rule_id])) {
                $parent = $rules[$rule_id];
            }else{
                $rule = Rules::findFirstByRuleId($rule_id);
                if($rule){
                    $parent = self::getRuleExtend($rule->toArray());
                    $rules[$rule_id] = $parent;
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
}
