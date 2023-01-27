const API_DOMAIN = location.protocol + "//" + location.hostname + "/"; // 请求域名
//const API_UPLOAD = location.protocol + "//" + (location.hostname == "admin.sp" ? "static.sp" : "shipu-develop-static.dixao.com"); //上传文件域名
const API_UPLOAD = STATIC_URL
    ? STATIC_URL
    : location.protocol +
    "//" +
    (location.hostname == "admin.sp"
        ? "static.sp"
        : "shipu-develop-static.dixao.com"); //上传文件域名
const PAGE_SIZE = 15; // 默认 分页

// shipu后台js
let sp = {};
sp.utils = {
    //常用功能函数
    getCurrentDate: function () {
        let date = new Date();
        let sep = "-";
        let year = date.getFullYear(); //获取完整的年份(4位)
        let month = date.getMonth() + 1; //获取当前月份(0-11,0代表1月)
        let day = date.getDate(); //获取当前日
        if (month <= 9) {
            month = "0" + month;
        }
        if (day <= 9) {
            day = "0" + day;
        }
        return year + sep + month + sep + day;
    },
};

// 加载分页信息, 生成分页信息
// td_count, td 的数量
// pager_id, 指定的pager的id, 获取方式: let pager = $("#" + pager_id);
sp.getPager = function (td_count, pager_id) {
    return (
        "<tbody><tr>" +
        '<td colspan="' +
        td_count +
        '" style="border: none; padding: 0px;">' +
        '<div class="layui-card">' +
        '<div class="layui-card-body" style="padding-left: 0px;">' +
        '<div id="' +
        pager_id +
        '"></div>' +
        "</div>" +
        "</div>" +
        "</td>" +
        "</tr></tbody>"
    );
};

// 加载tab页
// 参数: [索引, document/parent.document, callback]
// 索引: 即此tab在tab容器当中的索引, 从0开始
// document/parent.document, 指定获取tab的页面, 可以指定为上级窗口
// callback, 回调函数
sp.loadTab = function (index) {
    let $ = layui.jquery;
    if (index == 0) {
        // 如果索引为0, 则表示默认加载的页面, 不进行处理
        return;
    }
    let tab = null,
        url = null,
        container = null;
    if (arguments.length >= 2) {
        //表示是由其他窗体调用
        container = arguments[1];
        if (container.hasClass("sp-tab-container")) {
            tab = $(".sp-tab-area:eq(" + index + ")", container); //获取要切换到的tab项
        } else {
            tab = $(".layui-tab-item:eq(" + index + ")", container); //获取要切换到的tab项
        }
        tab.attr("loaded", ""); //表示是由其他窗体调用, 则需要立即刷新
    } else {
        //这里要将 user,跟普通的分开
        tab = $(".layui-tab-item:eq(" + index + ")"); //获取要切换到的tab项
    }

    (!url || url == "") && (url = tab.attr("url"));
    if (!url || url == "") {
        return;
    }
    let callback = null;
    if (arguments.length >= 3) {
        callback = arguments[2];
    }
    let loaded = tab.attr("loaded"); //检测是否已经加载过, 默认没有 loaded 这个属性
    if (!loaded) {
        //检测有没有动态加载, 如果还没有加载,则进行加载
        sp.load(tab.attr("url"), function (res) {
            let tbody = tab.hasClass("sp-tab-body")
                ? tab
                : $("tbody:first", tab); //兼容模式, 1:数据表格;2:自设内容区域
            tbody.empty();
            let url_id = url.split("?")[0].replace(/\//g, "_");
            tbody.attr("url", url);
            tbody.attr("id", url_id);

            tbody.append(res); // ajax请求页面并追加到现有页面

            // 加载分页信息
            let tr = "";
            let total = "";
            let pager_id = url_id + "_pager";
            let pager = $("#" + pager_id, tab);

            if (tab.hasClass("sp-tab-body")) {
                tr = $("tr:nth-child(1)", tbody);
                total = tr.eq(1).attr("total");
            } else {
                tr = $("tr:first", tbody);
                total = tr.attr("total");
            }

            if (total && total > PAGE_SIZE) {
                //如果有分页信息, 则加载分页信息, 如果没有此属性, 则不加载分页信息
                let td_count = $("td", tr).length;
                if (pager.length == 0) {
                    let pager_content = sp.getPager(td_count, pager_id);
                    if (tbody.parent().has("tbody").length > 0) {
                        tbody.parent().append(pager_content);
                    } else {
                        tbody.parent().children("tbody").remove();
                        tbody.parent().append(pager_content);
                    }

                    pager = $("#" + pager_id, tab);
                }
                //tab索引
                // 显示分页信息
                if (tab.hasClass("sp-tab-body")) {
                    $("#" + pager_id).attr("url", url);
                    $("#" + pager_id).attr("tab", index);
                    sp.userShowPager(
                        total,
                        pager_id,
                        tab,
                        container,
                        sp.loadTab
                    );
                } else {
                    pager.attr("url", url);
                    pager.attr("tab", index);
                    sp.showPager(total, pager_id, tab, sp.loadTab);
                }
            } else {
                //如果没有分页信息
                if (pager.length > 0) {
                    pager.parent().parent().parent().parent().parent().remove();
                }
                //jiejueg 分页bug
                if (tab.hasClass("sp-tab-body")) {
                    if (tbody.parent().has("tbody").length > 0) {
                        tbody.parent().children("tbody").remove();
                    }
                }
            }

            tab.attr("loaded", "loaded");
            sp.renderSwitch(tbody);
            layui.form.render();
            if (callback) {
                callback();
            }
        });
    }
};

// 参数: 展现列表数据的tbody的id
sp.loadPager = function (element_id) {
    let $ = layui.jquery;
    let that = $("#" + element_id);
    if (that.length == 0) {
        //表示没有找到
        return;
    }
    let url = that.attr("url");
    if (!url) {
        return;
    }
    let loaded = that.attr("loaded");
    let url_id = url.split("?")[0].replace(/\//g, "_");
    let pager_id = url_id + "_pager";
    let append_pager = function () {
        let tr = $("tr:first", that);
        let total = tr.attr("total");
        if (!total && total > PAGE_SIZE) {
            let pager = $("#" + pager_id);
            if (pager.length > 0) {
                pager.parent().parent().parent().parent().remove();
            }
            return;
        }
        let td_count = $("td", tr).length;
        let tab = that.parent();
        let pager = $("#" + pager_id, tab);
        if (pager.length == 0 && total && total > PAGE_SIZE) {
            let pager_content = sp.getPager(td_count, pager_id);
            tab.append(pager_content);
            pager = $("#" + pager_id, tab);
        }

        pager.attr("url", url); //
        pager.attr("tab", element_id); //tab索引
        // 显示分页信息
        layui.form.render();
        sp.renderSwitch(that);
        sp.showPager(total, pager_id, that, sp.loadPager);
    };

    if (!loaded) {
        //如果没有加载, 则进行加载
        sp.load(url, function (result) {
            that.empty();
            that.append(result);
            append_pager();
        });
    } else {
        append_pager();
        that.attr("loaded", "");
    }
};

// 改装post方法
sp.post = function (url, data) {
    let error_callback = null;
    if (arguments.length >= 4) {
        error_callback = arguments[3]; //操作失败回调
    }
    let callback = function (result) {
        layui.layer.msg("操作成功", {icon: 6, time: 1500}, function () {
            //关闭弹窗之后, 刷新上页列表数据
            // 此操作只适应只有一个tab的上页, 如果有多个, 请手工操作
            parent.location.reload();
        });
    };
    if (arguments.length >= 3) {
        //操作成功回调
        callback = arguments[2];
    }
    let real_url =
        url.indexOf("/") == 0
            ? API_DOMAIN.substring(0, API_DOMAIN.length - 1) + url
            : API_DOMAIN + url;
    layui.jquery.ajax({
        type: "POST",
        url: real_url,
        data: JSON.stringify(data),
        dataType: "json",
        contentType: "application/json",
        headers: {
            "Content-Type": "application/json; charset=UTF-8", //,
            //"Authorization": localStorage.getItem("Authorization")
        },
        async: false,
        success: function (result) {
            let code =
                result.errcode == undefined ? result.code : result.errcode;
            if (code != 0) {
                //如果有错误信息
                // let message = result.message.split(",").join("<br />"); message //jira说提示简单的中文就行，
                let options = {
                    icon: 5,
                    title: "错误",
                    cancel: function () {
                        if (error_callback) {
                            error_callback();
                        }
                    },
                };
                layui.layer.alert(result.message, options, function (i, l) {
                    if (error_callback) {
                        error_callback();
                    }
                    layer.close(i);
                });
            } else {
                callback(result);
            }
        },
        error: error_callback,
    });
};

// 改装get方法
sp.get = function (url, callback) {
    let real_url =
        url.indexOf("/") == 0
            ? API_DOMAIN.substring(0, API_DOMAIN.length - 1) + url
            : API_DOMAIN + url;
    layui.jquery.ajax({
        type: "GET",
        url: real_url,
        data: {},
        dataType: "json",
        contentType: "application/json",
        headers: {
            "Content-Type": "application/json; charset=UTF-8", //,
            //"Authorization": localStorage.getItem("Authorization")
        },
        success: function (res) {
            callback(res);
        },
    });
};

// 动态加载页面
sp.load = function (url, callback) {
    let real_url =
        url.indexOf("/") == 0
            ? API_DOMAIN.substring(0, API_DOMAIN.length - 1) + url
            : API_DOMAIN + url;
    layui.jquery.ajax({
        type: "GET",
        url: real_url,
        data: {},
        //dataType: "json",
        //contentType: "application/json",
        //headers: {
        //    "Content-Type": "application/json; charset=UTF-8" //,
        //    //"Authorization": localStorage.getItem("Authorization")
        //},
        success: function (res) {
            callback(res);
        },
    });
};

// 上传组件
sp.upload = function (element_id, callback) {
    layui.use(["upload", "jquery"], function () {
        let upload = layui.upload,
            $ = layui.jquery;
        upload.render({
            //执行实   例
            elem: "#" + element_id, //绑定元素
            url: API_UPLOAD + "/upload?field=" + element_id, //上传接口
            headers: {
                //"Content-Type": "application/json;charset=UTF-8" //,
                //"Content-Type": "multipart/form-data"
                //"Authorization": localStorage.getItem("Authorization")
            },
            done: callback,
            error: function () {
            },
        });
    });
};

// 关闭当前的弹出窗口
sp.closeWin = function () {
    let index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
};

// 关闭当前窗口, 并且重新加载上级窗口
sp.closeAndReload = function (index) {
    sp.loadTab(index, layui.jquery(parent.document), function () {
        sp.closeWin();
    });
};

// 获取location.search当中的某个值
sp.getQuery = function (key) {
    let url = location.search;
    if (arguments.length >= 2) {
        url = arguments[1];
    }
    if (url.indexOf("?") < 0) {
        return "";
    }
    let query = url.split("?")[1];
    let kv = query.split("&");
    for (let i = 0; i < kv.length; i++) {
        let item = kv[i];
        if (item.indexOf("=") <= 0) {
            continue;
        }
        let current = item.split("=");
        if (current[0] == key) {
            return current.length == 1 ? "" : current[1];
        }
    }
};

// 设置请求参数
sp.setQuery = function (key, value, origin_url) {
    let queries = origin_url.split("?");
    if (queries.length == 1) {
        return origin_url + "?" + key + "=" + value;
    }
    let url = queries[0];
    let query_array = queries[1].split("&");
    let to_query = [];
    let has_key = false;
    for (let i = 0; i < query_array.length; i++) {
        let current = query_array[i];
        let array = current.split("=");
        if (array.length != 2) {
            continue;
        }
        if (array[0] != key) {
            to_query.push(current);
        } else if (array[0] == key) {
            if (value == "") {
                continue;
            }
            has_key = true;
            to_query.push(key + "=" + value);
        }
    }
    if (!has_key && value != "") {
        to_query.push(key + "=" + value);
    }
    return url + "?" + to_query.join("&");
};

// 向url追加数据
sp.setQueries = function (origin_url, query_str) {
    let filterKey = null;
    let filterVal = null;
    if (arguments.length >= 3) {
        filterKey = arguments[2];
    }
    if (arguments.length >= 4) {
        filterValue = arguments[3];
    }
    let spArr = query_str.split("&");
    let result = origin_url;
    for (let i = 0; i < spArr.length; i++) {
        let sp = spArr[i].split("=");
        let key = sp[0];
        let value = sp[1];
        if (value == "") {
            //要过滤掉空值
            result = this.setQuery(key, value, result);
            continue;
        }
        if (filterKey) {
            if (filterKey == key) {
                if (filterVal) {
                    result = this.setQuery(filterKey, filterVal, result);
                } else {
                    result = this.setQuery(filterKey, "", result);
                }
            } else {
                result = this.setQuery(key, value, result);
            }
        } else {
            result = this.setQuery(key, value, result);
        }
    }
    return result;
};

// 显示分页
sp.showPager = function (total, element_id, tab) {
    let callback = null;
    if (arguments.length >= 4) {
        callback = arguments[3];
    }
    layui.use(["laypage", "jquery"], function () {
        let laypage = layui.laypage,
            $ = layui.jquery;
        let pager = $("#" + element_id);
        let index = pager.attr("tab");
        //let tab = $(".layui-tab-item:eq(" + index + ")"); //获取要切换到的tab项
        let current_page = sp.getQuery("page", tab.attr("url"));
        if (!current_page) {
            current_page = 1;
        }
        laypage.render({
            elem: element_id,
            limit: 15,
            count: total,
            curr: current_page,
            jump: function (obj, first) {
                if (first) {
                    return;
                }

                let from_url = pager.attr("url");
                let to_url = sp.setQuery("page", obj.curr, from_url);

                tab.attr("url", to_url);
                tab.attr("loaded", "");
                if (callback) {
                    callback(index);
                }
            },
        });
    });
};

//user分页
sp.userShowPager = function (total, element_id, tab, container) {
    let callback = null;
    if (arguments.length >= 5) {
        callback = arguments[4];
    }
    layui.use(["laypage", "jquery"], function () {
        let laypage = layui.laypage,
            $ = layui.jquery;
        let pager = $("#" + element_id);

        let index = pager.attr("tab");
        //let tab = $(".layui-tab-item:eq(" + index + ")"); //获取要切换到的tab项
        let current_page = sp.getQuery("page", tab.attr("url"));
        if (!current_page) {
            current_page = 1;
        }
        laypage.render({
            elem: element_id,
            limit: 15,
            count: total,
            curr: current_page,
            jump: function (obj, first) {
                if (first) {
                    return;
                }
                let from_url = pager.attr("url");
                let to_url = sp.setQuery("page", obj.curr, from_url);
                tab.attr("url", to_url);
                tab.attr("loaded", "");
                if (callback) {
                    callback(index, container);
                }
            },
        });
    });
};
// 渲染switch
sp.renderSwitch = function () {
    // 自动修改状态
    let $ = layui.jquery,
        form = layui.form;
    let tbody = arguments.length >= 1 ? arguments[0] : $(document);
    $(".sp-btn-state", tbody).each(function () {
        let that = $(this);
        let container = that.parent();
        if (!container.hasClass("layui-form")) {
            let form = $("form:first", container);
            if (form.length == 0) {
                //如果form为0则自动追加0
                let form = $(
                    '<form class="layui-form" style="margin: 0px; padding: 0px;"></form>'
                );
                that.appendTo(form);
                container.append(form);
            }
        }
        let current_value = that.val();
        if (current_value == "1") {
            that.prop("checked", true);
        } else {
            that.prop("checked", false);
        }
    });
    form.render();
    if (parent.layui && parent.layui.form) {
        parent.layui.form.render();
    }
};

// 成功的提示
sp.alertSuccess = function (message) {
    let icon = {icon: 6};
    if (arguments.length > 1) {
        layui.layer.alert(message, icon, arguments[1]);
    } else {
        layui.layer.alert(message, icon);
    }
};

// 警告提示
sp.alert = function (message) {
    let icon = {icon: 5};
    layui.layer.alert(message, icon);
};

// 打开窗口
sp.open = function (url, width, height, title) {
    let real_url =
        url.indexOf("/") == 0
            ? API_DOMAIN.substring(0, API_DOMAIN.length - 1) + url
            : API_DOMAIN + url;
    layer.open({
        type: 2,
        area: [width + "px", height + "px"],
        shade: 0.5,
        title: title ? title : "编辑信息",
        content: real_url,
    });
};

// 弹出窗口
sp.confirm = function (message, callback) {
    let title = "操作提醒";
    if (arguments.length >= 3) {
        title = arguments[2];
    }
    let icon = {icon: 7, title: title};
    layui.layer.confirm(message, icon, callback);
};
