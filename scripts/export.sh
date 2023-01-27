#!/usr/bin/env bash

# -- 开始导出
function start_export() { 
    database='payment_platform'
    user='root'
    password='qwe123QWE'
    bak_file="${database}.SQL"
    tar_file="${database}.tar.gz"
    export MYSQL_PWD='qwe123QWE'
    mysqldump --default-character-set=utf8 -u${user} -p${password} ${database} > $bak_file

    if [[ -e $tar_file ]]; then
        rm -rf $tar_file
    fi
    if [[ -e $bak_file ]]; then
        tar czvf $tar_file $bak_file
        if [[ -e $tar_file ]]; then
            rm -rf $bak_file
        fi
    fi
}

# -- 导出
start_export
