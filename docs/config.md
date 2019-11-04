# 系统配置
## 数据库配置
- 默认PA有一个SQLite3的配置，在 data/powerdb.sql3.db 中，开箱即用
- 默认的管理员是：admin；密码是：123456
## 修改数据库配置
- 在 data 目录下有一个 mysql.sql 文件，可以初始化一个 MySQL 的 PA 数据库
- config.php 文件的 pa_db 部分，详细的数据库配置，可参考 [phalcon官方的数据库配置](https://docs.phalcon.io/3.4/en/api/phalcon_db#abstract-class-phalcondbadapter)
```php
    'pa_db'       => [
        'adapter' => 'mysql',
        'dbname'  => 'pa',
        'username'=> 'root',
        'password'=> '123456',
//        'prefix'  => 'pa_'  // 如果需要共用一个DB，那么可以设置表名前缀
    ],
```