<?php
namespace Power\Models;
use PowerModelBase as PMB;
/**
 * 基础模型
 * @author vanni.fan
 */
class Extensions extends PMB{
    public function initialize(){
        parent::initialize();
        $this->hasOne(
            'rule_id',
            Rules::class,
            'rule_id',
            ["alias" => "rule"]
        );
    }
    
    public static function getExtensionsByUser($user_id=0)
    {
        // 获得用户的默认权限和属性
        $user   = self::query()->createBuilder()->from(['u' => Users::class])
                      ->innerJoin(Roles::class, 'u.role_id=r.role_id', 'r')
                      ->columns('r.extensions')
                      ->where('u.user_id=?0', [$user_id])
                      ->limit(1)
                      ->getQuery()->execute();
        $config = Configs::find(['user_id=0 or user_id=?0', 'bind' => [$user_id]]);

        # 用户所属角色分配的权限
        $return = ['rule' => [], 'attribute' => []];//user_extensions = $user_configs = [];
        if (count($user)) $return['rule'] = $user[0]->extensions ? json_decode($user[0]->extensions, 1) : [];
        if ($config) foreach ($config as $c) $return['attribute'][$c->rule_id][$c->name] = $c->value;
    
        # 合并全局的默认权限进入用户分配的权限里面
        foreach (self::find() ?: [] as $a) {
            if (isset($return[$a->type][$a->rule_id][$a->extend_name])) {
                if ($a->extend_items_type === 'null') continue;
            
                $items   = $a->extend_items_type === 'callback' ? call_user_func($a->extend_items) : json_decode($a->extend_items, 1);
                $org_val = $return[$a->type][$a->rule_id][$a->extend_name];
                if (is_array($org_val)) {
                    $new_val = [];
                    foreach ($org_val as $key) $new_val[$key] = $items[$key];
                    $return[$a->type][$a->rule_id][$a->extend_name] = $new_val;
                } else {
                    $return[$a->type][$a->rule_id][$a->extend_name] = $items[$org_val];
                }
                continue;
            } # 如果用户有这个权限，就用这个权限，否则用默认权限
            $default = null;
            switch ($a->extend_value_type) {
                case 'text':
                    $default = $a->extend_value;
                    break;
                case 'hash':
                case 'list':
                    $default = json_decode($a->extend_value, 1);
                    break;
            }
            $return[$a->type][$a->rule_id][$a->extend_name] = $default;
        }
        return $return;
    }
    
    public static function getExtensions(string $type=null):array {
        $where = $type ? ['type=?0','bind'=>[$type]] : [];
        $extends = [];
        foreach(Extensions::find($where) as $e){
            $extends[$e->rule_id][] = $e->toArray();
        }
        return $extends;
    }
}
