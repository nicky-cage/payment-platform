// 对于tab延迟加载的处理
layui.use(
    ["element", "jquery", "form", "laydate", "layer", "tree"],
    function () {
        let element = layui.element,
            $ = layui.jquery,
            form = layui.form,
            layer = layui.layer,
            laydate = layui.laydate;

        // 延尺加载tab
        // lay-filtter="sp-lazy-load, 需要将tabs容器指定此属性
        element.on("tab(sp-lazy-load)", function (obj) {
            sp.loadTab(obj.index, $(obj.elem));
        });

        // 取消按钮自动关闭
        // class="sp-btn-cancel", 需要将"取消"、"重置"的button增加此class
        $(document).on("click", ".sp-btn-cancel", function () {
            sp.closeWin();
        });

        // 删除
        $(document).on("click", ".sp-btn-delete", function () {
            let that = $(this);
            let url = that.attr("url");
            if (!url) {
                console.err("缺少属性: url");
                return;
            }

            let url_arr = [];
            let id = "";
            if (that.attr("rid") == undefined) {
                id = $("td:first", that.parent().parent()).text();
            } else {
                id = that.attr("rid");
            }

            if (id) {
                url_arr.push("id=" + id);
            }

            let real_url = url + "?" + url_arr.join("&");
            let title = that.attr("title");
            if (!title) {
                title = "删除信息确认";
            }
            let failure_message = function (result) {
                layer.alert(result.message, {icon: 2, time: 2000});
            };
            let msg =
                "你确定要删除这条记录么? <br /><br />注意: 执行删除成功后将不可恢复, 请谨慎操作!";
            if (url == "/receive_bank_cards/delete") {
                msg = "<span style='color: red'><b>特别注意</b></span><br /><br />你将<span style='color: red'>删除本银行卡</span>,请确认是否需要删除!";
            }
            layer.confirm(msg, {icon: 3, title: title}, function () {
                sp.get(real_url, function (result) {
                    if (result.code != 0) {
                        failure_message(result);
                    } else {
                        layer.msg(
                            "记录删除成功",
                            {icon: 1, time: 2000},
                            function () {
                                let tab_index = that.attr("tab");
                                if (tab_index && tab_index != 0) {
                                    let tab = $(".layui-tab-item:eq(" + tab_index + ")");
                                    tab.attr("loaded", "");
                                    layer.closeAll();
                                    sp.loadTab(tab_index);
                                    return false;
                                } else {
                                    let tbody = that.parent().parent().parent();
                                    let tbody_id = tbody.attr("id");
                                    if (tbody_id) {
                                        layer.closeAll();
                                        sp.loadPager(tbody_id);
                                        return false;
                                    }
                                    console.err("缺少必要信息: TAB INDEX 或 ID");
                                }
                                return false;
                            }
                        );
                    }
                });
            });
        });

        // 自动弹出新的窗口
        // 需要为弹出窗口增加
        // class=".sp-open-link", 将链接设为可弹出窗口
        // url="xx", 指定要打开的链接地址
        // area="100px,200px", 指定要打开的弹窗的大小
        // tab="1", 指定多标签模式下当前窗口所属的标签
        $(document).on("click", ".sp-open-link", function () {
            let that = $(this);
            let url = that.attr("url");
            if (!url) {
                console.err("缺少属性: url");
                return;
            }

            let url_arr = [];
            let id = that.attr("rid"); //优先从本身获取id值
            if (!id) {
                // 如果不存在,  再取默认的表格第一行
                id = $("td:first", that.parent().parent()).text();
            }
            if (id) {
                url_arr.push("id=" + id);
            }
            let tab_id = that.attr("tab");
            if (tab_id) {
                url_arr.push("tab=" + tab_id);
            }
            let area = that.attr("area");
            let width = "96%";
            let height = "96%";
            if (area) {
                if (area.indexOf(",") > 0) {
                    //如果有,号
                    let wh = area.split(",");
                    width = wh[0];
                    height = wh[1];
                } else {
                    width = area;
                    height = area;
                }
            }
            let real_url =
                url + (url_arr.length > 0 ? "?" + url_arr.join("&") : "");
            let title = that.attr("title");
            if (!title) {
                // 如果没有获取到title, 则默认取text
                title = that.text();
            }
            layer.open({
                type: 2,
                area: [width, height],
                shade: 0.5,
                title: title ? title : "编辑信息",
                content: real_url,
            });
            return false;
        });

        // 自动提交表单, 需要将表单的 submit 按钮增加属性: lay-filter="sp-save"
        form.on("submit(sp-save)", function (data) {
            let that = $(data.elem);
            if (that.hasClass("layui-btn-disabled")) {
                return false;
            }
            let current_form = $(data.form);
            let url = current_form.attr("action");
            if (!url) {
                alert("没有设置表单 action!");
                return false;
            }

            that.addClass("layui-btn-disabled");
            sp.post(
                url,
                data.field,
                function (result) {
                    if (result.code || result.errcode) {
                        let code =
                            result.errcode == undefined
                                ? result.code
                                : result.errcode; // 返回数据结构的兼容问题
                        if (code != 0) {
                            // 有错误发生
                            sp.alert(result.message);
                            that.removeClass("layui-btn-disabled");
                            return;
                        }
                    }
                    layui.layer.msg(
                        "操作成功",
                        {icon: 1, time: 1500},
                        function () {
                            let tab_index = sp.getQuery("tab");
                            if (!tab_index || tab_index == "0") {
                                parent.location.reload(); // 刷新上级页面
                            } else {
                                sp.loadTab(
                                    tab_index,
                                    $(parent.document),
                                    function () {
                                        sp.closeWin();
                                    }
                                );
                            }
                        }
                    );
                },
                function () {
                    that.removeClass("layui-btn-disabled");
                }
            );
            return false;
        });

        // 自动选择开关
        // lay-filter="switch", 需要将状态栏指定此属性
        form.on("switch(sp-switch)", function (data) {
            let that = $(data.elem);
            let container = $(data.elem).parent();
            let text = $("em:first", container).text();
            let texts = that.attr("lay-text").split("|");
            let values = that.attr("lay-values").split("|");
            for (let i = 0; i < texts.length; i++) {
                if (text == texts[i]) {
                    that.val(values[i]);
                    return;
                }
            }
        });

        // 自动选择开关 - checkbox
        // lay-filter="sp-form-checkbox", 需要设定此属性
        form.on("checkbox(sp-form-checkbox)", function (data) {
            let that = $(data.elem);
            let values = that.attr("values");
            let value_yes = "1",
                value_no = "0";
            if (values) {
                let arr = values.split("|");
                if (arr.length > 0) {
                    value_no = arr[0]; // 默认未选中的值
                }
                if (arr.length > 1) {
                    value_yes = arr[1]; // 默认选中的值
                }
            }
            let checked = that.attr("checked");
            if (checked && checked == "checked") {
                that.val(value_no);
                that.attr("checked", "");
            } else {
                that.val(value_yes);
                that.attr("checked", "checked");
            }
        });

        // 自动渲染日期控件
        $(".sp-form-date").each(function () {
            let element_id = $(this).attr("id");
            let is_simple = $(this).attr("format");
            laydate.render({
                elem: "#" + element_id,
                type: "date",
                range: is_simple ? false : true,
                value: is_simple ? sp.utils.getCurrentDate() : sp.utils.getCurrentDate() + " - " + sp.utils.getCurrentDate(),
            });
        });

        // 自动渲染日期时间控件
        $(".sp-form-datetime").each(function () {
            let element_id = $(this).attr("id");
            let is_simple = $(this).attr("format");
            laydate.render({
                elem: "#" + element_id,
                type: "datetime",
                range: is_simple ? false : true,
                value: is_simple ? sp.utils.getCurrentDate() + " 00:00:00" : sp.utils.getCurrentDate() + " 00:00:00 - " + sp.utils.getCurrentDate() + " 23:59:59",
            });
        });

        // 自动渲染查看图片
        $(document).on("click", ".sp-show-image", function () {
            let that = $(this);
            let url = that.attr("url"),
                current_index = that.parent().index();
            if (!url) {
                return;
            }
            let table = that.parent().parent().parent().parent();
            let title = $(
                "th:eq(" + current_index + ")",
                $("tr:first", table)
            ).text();
            let real_url = API_UPLOAD + url,
                callback = function (width, height) {
                    layer.open({
                        type: 2,
                        area: [
                            parseInt(width * 1.1) + "px",
                            parseInt(height * 1.2) + "px",
                        ],
                        shade: 0.5,
                        title: (title ? title + ": " : "") + url,
                        content: real_url,
                    });
                };
            $("<img />").attr("src", real_url).load(function () {
                callback(this.width, this.height);
            });
        });

        // 所有checkbox 列表, values: 所有值, value: 当前值, name: name
        $(".sp-form-checkbox-list").each(function () {
            let that = $(this);
            let items = that.attr("items"),
                value = that.attr("value"),
                name = that.attr("name"),
                container = that.parent();
            let list = items.split(","),
                values = "," + value + ",";
            for (let i = 0; i < list.length; i++) {
                let item = list[i].split("|");
                let k = item[0],
                    v = item[1];
                let checkbox = '<input type="checkbox" name="' + name + '[]" value="' + k + '" lay-filter="sp-form-checkbox-list" ' +
                    (values.indexOf("," + k + ",") >= 0 ? "checked" : "") + ' title="' + v + '" />';
                container.append(checkbox);
            }
            container.append('<input type="hidden" value="' + value + '" name="' + name + '" />');
            form.render();
        });

        // 多项选择自动渲染
        form.on("checkbox(sp-form-checkbox-list)", function (data) {
            let that = $(data.elem),
                name = that.attr("name"),
                value = data.value,
                input_name = name.substring(0, name.length - 2),
                checked = $(data.elem).prop("checked");
            let element = $('input[name="' + input_name + '"]:first'),
                current = element.val();
            if (checked) { // 如果选中
                element.val(current ? current + "," + value : value); // 判断是否已经存在值,如果存在则累加
                return;
            }
            // 处理非选中状态
            if (!checked && current == value) {
                element.val("");
                return;
            }
            let from_arr = current.split(","),
                to_arr = [];
            for (let i = 0; i < from_arr.length; i++) {
                if (from_arr[i] == value) {
                    continue;
                }
                to_arr.push(from_arr[i]);
            }
            element.val(to_arr.toString());
        });

        // 所有radio列表, values: 所有值, value: 当前值, name: name
        $(".sp-form-radio-list").each(function () {
            let that = $(this);
            let items = that.attr("items"),
                value = that.attr("value"),
                name = that.attr("name"),
                container = that.parent();
            let list = items.split(","),
                values = "," + value + ",";
            for (let i = 0; i < list.length; i++) {
                let item = list[i].split("|");
                let k = item[0],
                    v = item[1];
                let checkbox = '<input type="radio" name="' + name + '_" value="' + k + '" lay-filter="sp-form-radio-list" ' +
                    (values.indexOf("," + k + ",") >= 0 ? "checked" : "") + ' title="' + v + '" />';
                container.append(checkbox);
            }
            container.append('<input type="hidden" value="' + value + '" name="' + name + '" />');
            form.render();
        });

        // 单项选择自动渲染
        form.on("radio(sp-form-radio-list)", function (data) {
            let that = $(data.elem),
                name = that.attr("name"),
                input_name = name.substring(0, name.length - 1);
            let element = $('input[name="' + input_name + '"]:first');
            element.val(data.value);
        });

        // 自动绑定搜索按钮
        form.on("submit(sp-form-search)", function (data) {
            let current_form = $(data.elem.form);

            let tbody_index = current_form.attr("tbody");

            if (!tbody_index) {
                console.log("不需要TBODY");
                return false;
            }

            let tab = $(".layui-tab-item:eq(" + tbody_index + ")");
            let tbody = $("tbody:first", tab); //先在tab当中查找

            if (tbody.length == 0) {
                tbody = $("tbody:first"); //如果tab当中不存在, 再在全页面查找
                if (tbody.length == 0) {
                    console.log("没有找到TBODY索引: " + tbody_index);
                    return false;
                }
            }

            let url = tbody.attr("url");
            if (!url) {
                console.log("缺少要提交的URL OF TBODY");
                return false;
            }

            let query_str = current_form.serialize();

            let first_url = sp.setQuery("page", "1", url);
            let real_url = sp.setQueries(first_url, query_str);

            tbody.attr("url", real_url);
            let tab_index = tbody.attr("tab");

            if (tab_index) {
                sp.loadTab(tab_index);
                return false;
            }
            let tbody_id = tbody.attr("id");
            sp.loadPager(tbody_id);

            return false;
        });

        // 自动渲染上传图片按钮
        $(".sp-file-upload").each(function () {
            let that = $(this);
            let name = that.attr("name");
            let the_element = name + "_uploader";
            that.attr("name", the_element);
            that.attr("id", the_element);
            let src = that.attr("img");
            let content =
                '<div class="layui-upload-list">' +
                '<img class="layui-upload-img" id="upload-img-' + name + '" src="' + (src ? API_UPLOAD + src : "") + '" style="width: 150px" />' +
                '<p id="upload-show-' + name + '"></p>' +
                '<input type="hidden" name="' + name + '" id="' + name + '" />' +
                "</div>";
            that.parent().append(content);
            sp.upload(the_element, function (result) {
                if (result.code != 0) {
                    layui.layer.msg(result.message, {icon: 2});
                    return;
                }
                $("#upload-img-" + name).attr("src", API_UPLOAD + result.data.path);
                $("#" + name).val(result.data.path);
            });
        });

        form.on("switch(state)", function (data) {
            let that = $(data.elem);
            let val = that.attr("value");
            let url = that.attr("url");
            let msg = "警告: 确定要修改此条记录吗?";
            layer.open({
                icon: 3,
                title: "<span style='color:red'>操作提示</span>",
                content: msg,
                btn: ["取消", "确定"],
                btn2: function () {
                    let name = that.attr("name");
                    let current_text = data.othis[0].innerText;
                    let values_array = that.attr("values").split("|");
                    let texts = that.attr("lay-text");
                    for (
                        let i = 0, arr = texts.split("|");
                        i < arr.length;
                        i++
                    ) {
                        if (current_text == arr[i]) {
                            let value = values_array[i];
                            let id = that.attr("rid");
                            if (!id) {
                                id = $("td:first", that.parent().parent().parent()).txt();
                            }
                            let key = "to_" + name;
                            let url = that.attr("url") + "?id=" + id + "&" + key + "=" + value + "&name=" + name;
                            sp.get(url, function (result) {
                                if (result.code == 0) {
                                    layui.layer.msg("状态修改成功", {
                                        icon: 1,
                                    });
                                    if (val == 1) {
                                        that.attr("value", 2);
                                    } else {
                                        that.attr("value", 1);
                                    }
                                } else {
                                    layui.layer.msg(result.message, {
                                        icon: 2,
                                    });
                                }
                            });
                        }
                    }
                },
                btn1: function () {
                    //先按确定，然后按取消。 有时候会bug  renderswitch 不加载
                    //确认的时候
                    if (val == 1) {
                        that.prop("checked", false);
                    } else {
                        that.prop("checked", true);
                    }
                    form.render();
                    layer.closeAll();
                },
            });
        });
        // 城市联动
        form.on("select(province)", function (data) {
            $(".city select").empty(); //选择省的时候清空城市地区的值
            $(".city select").html('<option value="">城市</option>');
            $(".district select").empty();
            $(".district select").html('<option value="">区/县</option>');
            let province_code = $("#province_id").find("option:selected").attr("data-data");
            if (province_code) {
                let ajaxJson = {
                    province_code: province_code,
                };
                $.post("/merchant_settles/linkage", ajaxJson, function (res) {
                    if (res.code == 0) {
                        if (res.data.length > 0) {
                            let str = "";
                            res.data.forEach(function (ele, index) {
                                str = '<option value="' + ele["id"] + '" data-data="' + ele["code"] + '">' + ele["name"] + "</option>";
                                $("#city_id").append(str);
                            });
                            form.render("select");
                        }
                    }
                });
            }
        });
        // 城市联动
        form.on("select(city)", function (data) {
            $(".district select").empty();
            $(".district select").html('<option value="">区/县</option>');
            let code = $("#city_id").find("option:selected").attr("data-data");
            if (data.value) {
                let ajaxJson = {
                    city_code: code,
                };
                $.post("/merchant_settles/linkage", ajaxJson, function (res) {
                    if (res.code == 0) {
                        if (res.data.length > 0) {
                            let str = "";
                            res.data.forEach(function (ele, index) {
                                str = '<option value="' + ele["id"] + '" data-data="' + ele["code"] + '">' + ele["name"] + "</option>";
                                $("#district_id").append(str);
                            });
                            form.render("select");
                        }
                    }
                });
            }
        });

        sp.loadPager("sp-loaded-table");
        form.render();
        sp.renderSwitch();
    }
);
