layui.config({
    base: '/static/layuiadmin/' //静态资源所在路径
}).use(['form', 'jquery', 'layer'], function() {
    let $ = layui.$
    let layer = layui.layer;
    $(".listrisk_pass").click(function () {
        var info = $(this).attr("id")
        var ts = $(this)
        var arr = {"id": info, "type": "1"}

        function re() {
            ts.parent().parent().remove()
        }

        layer.open({
            title: "审核通过",
            shade: 0.5,
            type: 2,
            area: ['800px', '400px'],
            offset: 'auto',
            btn: ['取消', '确定'],
            content: ["/risk_audits_detail_view", 'no'],
            success: function (layero, index) {
                $.ajax({
                    url: "/risk_audits/detail",
                    type: "get",
                    data: {"id": info},
                    success: function (data) {

                        var iframe = window['layui-layer-iframe' + index];//拿到iframe元素
                        iframe.child(JSON.stringify(data))//向此iframe层方法 传递参数
                    }
                })
            },
            btn2: function () {
                $.ajax({
                    type: "post",
                    url: "/risk_audits/savess",
                    data: JSON.stringify(arr),
                    success: function () {
                        layer.open({
                            title: false
                            , content: '<div style="width:100%;text-align:center;"><b>已通过</b></div>',
                            time: 1000,
                            btn: false,
                            closeBtn: 0,
                        })
                        re()
                    },
                    error: function () {
                        layer.open({
                            title: false
                            , content: '<div style="width:100%;text-align:center;"><b>程序错误</b></div>',
                            time: 1000,
                            btn: false,
                            closeBtn: 0,


                        })
                    }
                })
            }
        })
    })
    $(".listhandup").click(function () {
        var id = $(this).attr("id")
        var ts = $(this)

        function re() {
            ts.parent().parent().remove()
        }

        layer.open({
            title: "审核挂起",
            shade: 0.5,
            type: 2,
            area: ['600px', '400px'],
            offset: 'auto',
            btn: ['取消', '确定'],
            content: ["/risk_audits/handup", 'no'],
            btn2: function (index, layero) {
                var body = layer.getChildFrame('body', index)
                var cause = body.find("input[name='cause_failure']").val()
                var arr = {"id": id, "cause_failure": cause, "type": "2"}
                $.ajax({
                    type: "post",
                    url: "/risk_audits/savess",
                    data: JSON.stringify(arr),
                    success: function () {
                        layer.open({
                            title: false
                            , content: '<div style="width:100%;text-align:center;"><b>已挂起</b></div>',
                            time: 1000,
                            btn: false,
                            closeBtn: 0,


                        })
                        re()
                        //还要把他加到审核挂起列表？或者就不用?
                    },
                    error: function () {
                        layer.open({
                            title: false
                            , content: '<div style="width:100%;text-align:center;"><b>程序错误</b></div>',
                            time: 1000,
                            btn: false,
                            closeBtn: 0,
                        })
                    }
                })
            }
        })

    })
    $(".listrefuse").click(function () {
        var id = $(this).attr("id")
        var ts = $(this)
        function re() {
            ts.parent().parent().remove()
        }

        layer.open({
            title: "审核拒绝",
            shade: 0.5,
            type: 2,
            area: ['600px', '450px'],
            offset: 'auto',
            btn: ['取消', '确定'],
            content: ["/risk_audits/refuse", 'no'],
            btn2: function (index, layero) {
                var body = layer.getChildFrame('body', index)
                var a = body.find("select[name='fail_type'] option:selected").text()
                var b = body.find("textarea[name='case_filure']").val()
                var reson = a + b;
                var message = body.find("input[name='message']:checked").val()
                var remark = body.find("textarea[name='remark']").val()

                var arr = {"id": id, "type": "3", "cause_failure": reson, "remark": remark,"message":message}
                $.ajax({
                    url: "/risk_audits/savess",
                    type: "post",
                    data: JSON.stringify(arr),
                    success: function (data) {
                        layer.open({
                            title: false
                            , content: '<div style="width:100%;text-align:center;"><b>已拒绝</b></div>',
                            time: 1000,
                            btn: false,
                            closeBtn: 0,
                        });
                        re()
                    },
                    error: function () {
                        layer.open({
                            title: false
                            , content: '<div style="width:100%;text-align:center;"><b>程序错误</b></div>',
                            time: 1000,
                            btn: false,
                            closeBtn: 0,
                        })
                    }

                })
            }

        })
    })

})