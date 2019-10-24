## 安装
- 到 [https://github.com/vanni-fan/pa](https://github.com/vanni-fan/pa) 去下载
- 配置 Nginx，设置 root 为 public 目录，并配置 try_files 即可，其他配置都是标准配置 
```
server {

    # ....标准配置....

    root 你的PA所在目录/public;
    location / {  
        try_files $uri $uri/ /index.php?_url=$uri&$args;
    }

    location ~ \.php$ {

        # ....标准配置....

    }
}
```
- 配置 Apache TODO 
- 配置 IIS TODO

## 目录结构
```text
+---controllers         控制器
+---data                数据目录
|   +---cache           缓存文件
|   \---$other          其他插件用来保存数据的文件夹，文件夹名称一般用插件名来命名
+---library             类库目录
|   +---HtmlBuilder     自带的HTMLBuilder库
|   +---Logger          自带的日志库
|   +---Power           PA系统的启动库
|   \---vendor          第三方依赖库
+---models              数据模型
+---plugins             插件目录
+---public              网站公开目录
|   +---index.php       入口文件
|   \---dist            资源文件
\---views               视图目录
    \---templates
        +---include     板块模板
        +---layouts     布局模板
        \---$page       具体的页面模板

```
