#!/bin/bash

# Hyperf 默认加载方案
#php ./bin/hyperf.php start
# 开发环境热加载方案
#php ./bin/dev.php start
php_bin='/usr/local/bin/php'
alias php=$php_bin
branch='main'
app_port=`cat .env| grep APP_PORT | awk '{ print $3 }'`

function kill_process() { 
    for p in `ps aux | grep 'watcher.php start' | grep -v grep  | awk '{ print $2 }'`; do 
        kill -9 $p
    done
    for p in `ps aux | grep 'hyperf.php server:watch' | grep -v grep | awk '{ print $2 }'`; do
        kill -9 $p
    done
    for p in `lsof -i:${app_port} | awk 'NR > 1  { print $2 }'`; do
        kill $p
    done
}

# 实时调试模式
function start_dev() { 
    echo 'Killing the running processes ...'
    kill_process
    composer dump-autoload --optimize
    $php_bin bin/hyperf.php server:watch
}

# 后台运行模式
function start() {
    #export APP_ENV='prod' # 设置为生产环境
    if [[ ! -d ../logs ]]; then # 如果不存在日志目录则创建
        mkdir logs
    fi
    echo 'Killing the running processes ...'
    kill_process
    composer dump-autoload --optimize
    log_file="./run.log"
    $php_bin bin/hyperf.php server:watch > $log_file 2>&1 &
}

# 检测文件
function check() { 
    # shellcheck disable=SC2044
    for f in $(find ./app -name "*.php" -print); do
        result=$(php -l "$f")
        echo -n $f
        if [[ "$(echo "$result" | grep 'No syntax errors detected')" != "" ]]; then
            echo '... Ok'
        else
            echo '... Failure'
            echo "$result"
            exit
        fi
    done
}


# 重启并在后台执行
function restart() { 
    kill_process
    start
}

# 重启并在前台执行
function restart_dev() { 
    kill_process
    start_dev
}

# 检测所有php文件
function check_php_files() {
  # shellcheck disable=SC2044
  for f in $(find ./app -name "*.php" -print); do
    $php_bin -l "$f"
  done
}

# 检测自动升级
function auto_git() {
  while [ 1 ]; do
    git pull origin $branch
    sleep 5
  done
}

# 监控日志
function start_watch() { 
    tail -f ./run.log
}

# 主执行函数
function main() { 
    case "$1" in 
        "start")
            start;;
        "dev")
            start_dev;;
        "restart")
            restart;;
        "restart_dev")
            restart_dev;;
        "check")
            check;;
        "git")
            auto_git;;
        "watch")
            start_watch;;
        "stop")
            kill_process;;
        *)
            echo "必须输入参数: start|dev|restart|restart_dev|check|stop|git"
            echo "start:        生产环境,后台执行"
            echo "dev:          开发环境,实时日志"
            echo "restart:      测试环境/生产环境重启"
            echo "restart_dev:  在开发环境重启"
            echo "check:        检测目录下php文件语法错误"
            echo "stop:         停止当前进程"
            exit;;
    esac
}

# shellcheck disable=SC2068
main $@
