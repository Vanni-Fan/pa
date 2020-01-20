#!/bin/bash

#set -x

for dir in /var/log/mysql /tmp/mysql
do
   if [ ! -d $dir ]; then
      mkdir -p ${dir}
   fi
   chown mysql:mysql $dir
done

echo "############################################################"
#echo "execute ${BASH_SOURCE}"
if [ ! -v PA_DATABASE_NAME ]; then
  PA_DATABASE_NAME=pa
#  MYSQL_DATABASE=pa
fi
cp -f /docker-entrypoint-initdb.d/pa.data.template /docker-entrypoint-initdb.d/$PA_DATABASE_NAME.sql
if [ ! -v PA_PREFIX ]; then
  PA_PREFIX=
fi
if [ ! -v PA_ADMIN_PASSWORD ]; then
  # 默认密码： 123456
  PA_ADMIN_PASSWORD='$2y$10$TKQCJlKzwLsOcvlW9cEso.ImT8c4E2OsYy5pMZu.a2xQ75gS/ygDi'
fi


# 替换前缀
sed "s/_PA_PREFIX_/${PA_PREFIX}/g" -i /docker-entrypoint-initdb.d/$PA_DATABASE_NAME.sql

# 替换数据名称
sed "s/_PA_DATABASE_NAME_/${PA_DATABASE_NAME}/g" -i /docker-entrypoint-initdb.d/$PA_DATABASE_NAME.sql

# 替换管理用户密码
sed 's#_PA_ADMIN_PASSWORD_#'$PA_ADMIN_PASSWORD'#g' -i /docker-entrypoint-initdb.d/$PA_DATABASE_NAME.sql

#source /usr/local/bin/docker-entrypoint.sh
source docker-entrypoint.sh "$@"
_main "$@"