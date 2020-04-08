### 它是什么
- [文档](https://pa-docs.readthedocs.io/zh/latest/)
- 基于 Phalcon 的通用管理后台
- 基本权限，8个粒度，1个字节存储
  - 普通权限（针对用户自己创建的内容）
    - 增删改查，自己的数据
  - 超级权限（针对不是自己的内容）
    - 增删改查，所有数据
- 扩展权限
  - 有管理员分配给角色的扩展权限，比如：
    - 某权限页面的：仓库列表（限制某些角色只能看某哪些仓库的库存），发货提醒按钮等，不同角色权限不同

- 扩展属性
  - 用户可以自己配置的属性，比如：
    - 某权限页面的：显示行数，默认查询条件等，每个用户都有自己的属性配置

- 注意，Windows环境的 git 请设置 **行结束符** 配置
  - 文件：`C:\Users\Administrator\.gitconfig`, 添加修改如下行，否则Windows拉取下来的文件会被自动添加`\r\n`结束符 
    ```
    [core]
      autocrlf = false
      safecrlf = false
    ```
- 测试环境
  - 在 docker 目录下面执行 `docker-composer up -d` 
  - 访问 http://localhost:886/admin
  - 里面有一个 demo ，配置本机的 hosts 文件，添加
    ```
    127.0.0.1 www.zx.com api.zx.com h5.zx.com in.zx.com oa.zx.com
    ```
  - 然后使用 http://www.zx.com:886 来访问Demo