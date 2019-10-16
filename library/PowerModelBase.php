<?php
use Phalcon\Mvc\Model;
/**
 * 基础模型
 * @author vanni.fan
 */
abstract class PowerModelBase extends Model{
    private static $instances = [];
    private static $fields = [];
    public function initialize(){
        PA::$di->set('db',PA::$db);
        $this->setDi(PA::$di);
        $this->setSource(PA::$config->path('pa_db.prefix').$this->getSource());
    }
    
    /**
     * 活动字段信息
     * @return array
     */
    public static function fields():array{
        if(empty(self::$fields[static::class])) {
            $obj = self::getInstance();
            self::$fields[static::class] = $obj->getModelsMetaData()->getDataTypes($obj);
        }
        return self::$fields[static::class];
    }
    
    /**
     * 获得模型的单例对象，如果需要使用 $model->save() 的时候，请谨慎使用单例
     * @param mixed ...$params
     * @return mixed|PowerModelBase
     */
    public static function getInstance(...$params){
        $key = md5(json_encode($params).static::class);
        return self::$instances[$key] ?? (self::$instances[$key] = new static(...$params));
    }
}
