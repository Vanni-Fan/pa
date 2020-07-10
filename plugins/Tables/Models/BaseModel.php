<?php

namespace plugins\Tables\Models;

use Phalcon\Mvc\ModelInterface;
use Phalcon\Text;

class BaseModel
{
    public static function get(int $source_id, string $table_name):ModelInterface{
        $class_name = Text::camelize($table_name.$source_id);
        $full_class_name = 'plugins\\Tables\\Models\\Cache\\'.$class_name;
        return new $full_class_name;
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
use plugins\DataSource\Models\DataSources;
use PowerModelBase, PA;

class $class_name extends PowerModelBase {
    public function initialize(){
        \$this->setDi(PA::\$di);
        \$db = DataSources::getDBbyId($source_id);
        \$db->setEventsManager(PA::\$em);
        PA::\$di->set('table_source_$source_id', \$db);
        \$this->setConnectionService('table_source_$source_id');
        \$this->setSource('$table_name');
    }
}
Out
            );
        }
        return $status;
    }
}
