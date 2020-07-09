<?php

namespace plugins\Tables\Models;

use Phalcon\Di\FactoryDefault;
use Phalcon\Text;
use plugins\DataSource\Models\DataSources;

class BaseModel
{
    public static function get(int $source_id, string $table_name){
        $class_name = Text::camelize($table_name.$source_id);
        $full_class_name = 'plugins\\Tables\\Models\\Cache\\'.$class_name;
        $di = new FactoryDefault();
        $di->set('db', DataSources::getDBbyId($source_id));
        return new $full_class_name(null, $di);
    }

    public static function del(int $source_id, string $table_name){
        $class_name = Text::camelize($table_name.$source_id);
        $file = __DIR__ .'/Cache/'.$class_name.'.php';
        if(file_exists($file)) unlink($file);
        return true;
    }

    public static function add(int $source_id, string $table_name){
        $class_name = Text::camelize($table_name.$source_id);
        $file = __DIR__ .'/Cache/'.$class_name.'.php';
        $status = true;
        if(!file_exists(dirname($file))) $status &= mkdir(dirname($file));
        if(!file_exists($file)){
            $status &= file_put_contents($file,<<<Out
<?php
namespace plugins\Tables\Models\Cache;
class $class_name extends \PowerModelBase{
    public function initialize(){
        \$this->setSource('$table_name');
    }
}
Out
            );
        }
        return $status;
    }
}