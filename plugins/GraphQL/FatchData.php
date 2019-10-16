<?php
namespace plugins\GraphQL;
use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;
use Power\Models\Logs;

class FatchData{
    private static $data;
    public static function columns($source, $args, $context, \GraphQL\Type\Definition\ResolveInfo $info){ # context 用不上
        $rs = Executor::defaultFieldResolver($source, $args, $context, $info);
        if(!is_null($rs)) return $rs;
        
        echo "\n++++++【{$info->fieldName}】+++++\n";
        echo '参数：',print_r($args,1);
        echo '来源：',print_r($source,1);
        echo '期望返回：', $info->returnType, "\n";
        echo "+++++++++++\n\n\n";
        
        $type = $info->returnType;
        # 不属于List并且不需要ModelType
        if(!($type instanceof ListOfType) && !($type instanceof ModelType)){
            throw new \Exception('未实现此类型');
        }
        $model = $info->returnType instanceof ListOfType ? $info->returnType->ofType : ($info->returnType instanceof ModelType ? $info->returnType->model : '\Exception');
        
        
        switch($info->fieldName){
            case 'Logs':
                return Logs::find(['user_id=?0','bind'=>[$source['user_id']],'limit'=>10]);
        }
    }
    
    /**
     * 根据参数，从数据库的表中查询多条记录，返回
     * 如果有子集，在此方法里面需要设置为 null 然后在字段数据中设置数据，才可以使用子集的参数
     * @param                                      $source
     * @param                                      $args
     * @param                                      $context
     * @param \GraphQL\Type\Definition\ResolveInfo $info
     * @return array|mixed|string|null
     * @throws \Exception
     */
    public static function rows($source, $args, $context, \GraphQL\Type\Definition\ResolveInfo $info){
        $rs = Executor::defaultFieldResolver($source, $args, $context, $info);
        if($rs !== null) return $rs;
        
        $class = '\\Power\\Models\\'.ucfirst($info->fieldName);
        $model = call_user_func([$class,'find'],['limit'=>10]);
        return $model->toArray();
        
        
//        $id = random_int(1,20);
//        $name = ['AA','BB','CC'][random_int(0,2)];
//        print_r("\n\n\n--------【{$info->fieldName}】------\n");
//        var_dump('参数：',$args);
//        var_dump('上下文：',$context);
//        var_dump('来源：',$source);
//        echo "随机ID：$id, 随机名称：$name \n";
//        print_r("--------------\n\n\n");
//        switch($info->fieldName){
//            case 'logs':
//                return [[
//                    'log_id'=>$id,
//                    'log_name'=>$name
//                ]];
//            case 'users':
//                return [[
//                    'user_id'=>$id,
//                    'user_name'=>$name,
//                    'email'=>'test@a.com',
//                    'logs'=>''
//                ]];
//                break;
//            default:
//                return '其他字段内容';
//        }
    }
}
