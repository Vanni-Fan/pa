## Docker 的使用方法
1. Windows下面安装Docker：
   - 到官方下载最新版 [docker](https://hub.docker.com/editions/community/docker-ce-desktop-windows)
   - 如果运行docker出错，请卸载，用[virtual box](https://www.virtualbox.org/)吧，在virtual box里面安装CentOS
      - windows运行出错的原因千差万别，有些甚至需要重装系统才能装上，所以别折腾
2. Linux下面安装Docker，先安装 CentOS7，然后执行下面的脚本初始化
    ```sh
    sh install-docker-in-centos.sh
    ```
## 单独运行 PA 管理端
- 测试容器
```
docker run --rm -it -p 80:80 registry.cn-hangzhou.aliyuncs.com/vanni/phalcon:7.4-apache
```
- 运行容器
```
docker run -d -p 80:80 --name pa_test registry.cn-hangzhou.aliyuncs.com/vanni/phalcon:7.4-apache
```
- 访问容器 http://localhost/admin

## 基于 PA 进行二次开发
- 准备好你的项目，参考 demo/zx.com 里面的结构
    ```
    . 
    |- .htaccess             apache的访问控制文件
    |- public
    |   |- dist              前端静态文件
    |   \- index.php         入口文件
    |- modules               模块目录
    |   |- api               api子模块
    |   |   |- Controllers   控制器
    |   |   \- Models        模型
    |   \- web               web子模块
    |       |- Controllers
    |       \- Views         视图
    \- library               类库模块
        |- composer.json     可选的 php-composer 依赖配置
        |- vendor            可选的依赖库目录
        \- your-library      你的类文件或目录
    ```
- 配置 apache 的目录 ./sites-enabled/1.default.conf
    ```conf
    <VirtualHost *:80>
        LogLevel debug
        ServerAdmin webmaster@localhost
        ServerAlias h5.zx.com www.zx.com in.zx.com api.zx.com oa.zx.com # 配置域名
        DocumentRoot /var/www/zx/public # 配置目录
        <Directory /var/www/zx/public>  # 设置权限
            Options Indexes FollowSymLinks
            AllowOverride None
            Require all granted
        </Directory>
    </VirtualHost>
    ```
- 配置 public/.htaccess 文件
    ```conf
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php?_url=/$1 [QSA,L]
    ```
- 配置 docker-compose 文件，保存为 demo-compose.yml
    ```conf
    version: "3"
        services:
            zxpt:  # 容器名称, 
                image: registry.cn-hangzhou.aliyuncs.com/vanni/phalcon:7.4-apache  # 这个是打包好的镜像
                volumes:
                  - ../demo/zx.com:/var/www/zx                    # 映射文件到容器里面   本地文件:容器文件
                  - ./sites-enabled/1.default.conf:/etc/apache2/sites-enabled/1.default.conf    # 映射apache虚拟文件
                ports:
                  - 888:80                                        # 映射端口    本地端口:容器端口
                restart: always
    ```
- 启动容器
    ```
    docker-compose -f demo-compose.yml up -d
    ```
- 设置本机 hosts 对应指定的域名
    ```
    127.0.0.1 h5.zx.com www.zx.com in.zx.com api.zx.com oa.zx.com
    ```
- 访问你的页面
  - http://h5.zx.com/api
  - http://api.zx.com
  - http://www.zx.com
  - http://in.zx.com/web  http://in.zx.com/api
