yum_config(){
    rm -f /etc/yum.repos.d/CentOS*
    rm -f /etc/yum.repos.d/epel*
    rm -f /etc/yum.repos.d/docker* 
    curl -o /etc/yum.repos.d/CentOS-Base.repo http://mirrors.aliyun.com/repo/Centos-7.repo
    curl -o /etc/yum.repos.d/epel.repo http://mirrors.aliyun.com/repo/epel-7.repo
    curl -o /etc/yum.repos.d/docker-ce.repo http://mirrors.aliyun.com/docker-ce/linux/centos/docker-ce.repo
    yum clean all
    yum makecache fast
    yum update -y --disablerepo=docker*
    yum install unzip zip lsof vim lrzsz bind-utils device-mapper-persistent-data chrony lvm2 wget iptables-services -y
}

# 软件安装
install(){
    # 安装最新版的 docker
    curl -sSL https://get.docker.com/ | sh
    mkdir -p /etc/docker
    # 配置阿里的镜像加速，Vanni在阿里上的是 https://osvemmc3.mirror.aliyuncs.com，请替换你自己的地址
    tee /etc/docker/daemon.json <<-'EOF'
    {
        "registry-mirrors": ["https://osvemmc3.mirror.aliyuncs.com"],
        "log-driver":"json-file",
        "log-opts" : {"max-size":"10M","max-file":"1"}
    }
EOF
    systemctl daemon-reload
    systemctl restart docker
    systemctl enable docker

    # 安装最新帮的 docker-compose
    last_version=`curl -sSL 'https://api.github.com/repos/docker/compose/releases?page=1&per_page=1'|grep '"tag_name":'|awk -F '"' '{print $4}'`
    file_name="$last_version/docker-compose-$(uname -s)-$(uname -m)"
    echo fecth remote file: "https://github.com/docker/compose/releases/download/$file_name"
    curl -L "https://github.com/docker/compose/releases/download/$file_name" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
}

ntp(){
    # 时间同步
    timedatectl set-timezone Asia/Hong_Kong
    systemctl start chronyd
    systemctl enable chronyd
    timedatectl set-ntp true
}

main(){
    yum_config
    install
    ntp
}

main
# sed -i "s/SELINUX=permissive/SELINUX=disabled/g" /etc/selinux/config
# sed -i "s/SELINUX=enforcing/SELINUX=disabled/g" /etc/selinux/config

reboot