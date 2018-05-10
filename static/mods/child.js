layui.define(['form', 'layer', 'table', 'jquery', 'laydate'], function(exports) {
    var $ = layui.jquery
        ,form = layui.form
        ,layer = parent.layer === undefined ? layui.layer : parent.layer
        ,table = layui.table
        ,laydate = layui.laydate;

    /////////////
    // 登陆页面 //
    /////////////

    form.on('submit(login)', function(obj) {
        var index = layer.open({
            type: 3,
            icon: 1,
        });
        $.post('/Api/User/login.html', obj.field, function(data) {
            if (typeof data == 'string') {
                data = eval('(' + data + ')');
            }
            layer.close(index);
            if (data.code) {
                layer.alert(data.msg);
            } else {
                layer.msg('登陆成功，正在为您跳转...');
                setTimeout(function() {
                    window.location.href = '/Admin/Index/index.html';
                }, 3000);
            }
        });

        return false;
    });

    ////////////////
    // 发布文章页面 //
    ////////////////

    // 渲染日期选择器
    laydate.render({
        elem: '[name="date"]',
        type: 'datetime',
        max: 0,
        calendar: true
    });

    // 监听发布文章
    form.on('submit(add-article)', function(data) {
        var index = layer.open({
            type: 3,
            icon: 1,
        });
        $.post('/Api/Article/add.html', data.field, function(res) {
            if (typeof res == 'string') {
                res = eval('(' + res + ')');
            }
            layer.close(index);
            if (res.code) {
                layer.alert(res.msg);
            } else {
                layer.msg('提交成功');
                $('[type="reset"]').trigger('click');
            }
        });

        return false;
    });

    ////////////////
    // 文章编辑页面 //
    ////////////////

    form.on('submit(edit-article)', function(data) {
        var index = layer.open({
            type: 3,
            icon: 1,
        });
        var id = $('form').prop('id');
        var field =  $.extend({}, data.field, {id: id});
        $.post('/Api/Article/edit.html', field, function(res) {
            if (typeof res == 'string') {
                res = eval('(' + res + ')');
            }
            layer.close(index);
            if (res.code) {
                layer.alert(res.msg);
            } else {
                layer.msg('提交成功');
            }
            window.location.href = '/Article/detail.html?id=' + id;
        });

        return false;
    });

    ////////////////
    // 文章管理页面 //
    ////////////////

    // 渲染表格
    var articleList = table.render({
        elem: '#article-list',
        page: true,
        data: [],
        url: '/Api/Article/get.html',
        cols: [[
            { type: 'checkbox' },
            { field: 'title', title: '文章标题', align: 'center', },
            { field: 'date', title: '发布时间', align: 'center', width: '10%', },
            { field: 'category', title: '文章分类', align: 'center', width: '20%', },
            { fixed: 'right', title: '操作', align: 'center', width: 150, toolbar: '#tools' }
        ]]
    });

    // 监听文章管理
    form.on('submit(article)', function(data) {
        var event = $(data.elem).attr('lay-event');
        if (event == 'search') {
            articleList.reload({
                where: {
                    keyword: $('[name="keyword"]').val(),
                }
            });
        } else if(event == 'del') {
            layer.confirm('确定删除这些文章么', function(index) {
                var list = table.checkStatus('article-list');
                var params = [];
                for (var i = 0; i < list.data.length; ++i) {
                    params.push(list.data[i]['id']);
                }
                $.post('/Api/Article/del.html', {list: JSON.stringify(params)}, function(data) {
                    if (typeof data == 'string') {
                        data = eval('(' + data + ')');
                    }
                    if (data.code) {
                        layer.alert(data.msg);
                    } else {
                        articleList.reload({});
                        layer.msg('删除成功');
                    }
                });
                layer.close(index);
            });
        }

        return false;
    });

    // 工具栏监听
    table.on('tool(article-list)', function(obj) {
        var data = obj.data;
        var event = obj.event;

        if (event == 'prev') {
            window.open('/Article/detail.html?id=' + data.id);
        } else if(event == 'del') {
            layer.confirm('确定删除这篇文章？', {title: '提示'}, function(index) {
                layer.close(index);
                $.post('/Api/Article/del', {id: data.id}, function(data) {
                    if (typeof data == 'string') {
                        data = eval('(' + data + ')');
                    }
                    if (data.code) {
                        layer.alert(data.msg);
                    } else {
                        obj.del();
                        layer.msg('删除成功');
                    }
                });
            });
        } else if(event == 'edit') {
            var index = layui.layer.open({
                title: "编辑文章",
                type: 2,
                content: "/Admin/Article/edit.html?id=" + obj.data.id,
                success: function(layero, index) {
                    setTimeout(function() {
                        layui.layer.tips('点击此处返回', '.layui-layer-setwin .layui-layer-close', {
                            tips: 3
                        });
                    }, 500);
                }
            });
            // 改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
            $(window).resize(function() {
                layui.layer.full(index);
            })
            layui.layer.full(index);
        }
    });

    ////////////////
    // 分类管理界面 //
    ////////////////

    // 渲染表格
    var categoryList = table.render({
        elem: '#cate-list',
        page: true,
        data: [],
        url: '/Api/Category/all',
        cols: [[
            { type: 'checkbox' },
            { field: 'title', title: '分类标题', align: 'center', width: 200, edit: 'text' },
            { fixed: 'right', title: '操作', align: 'center', width: 90, toolbar: '#tools' }
        ]]
    });

    // 监听分类管理
    form.on('submit(category)', function(obj) {
        var event = $(obj.elem).attr('lay-event');

        if (event == 'add') {
            var index = layer.open({
                type: 3,
                icon: 1,
            });
            $.post('/Api/Category/add', {title: $('[name="cate"]').val()}, function(data) {
                if (typeof data == 'string') {
                    data = eval('(' + data + ')');
                }
                layer.close(index);
                if (data.code) {
                    layer.alert(data.msg);
                } else {
                    layer.msg('添加成功');
                    categoryList.reload({});
                }
            });
        } else if(event == 'del') {
            layer.confirm('确定删除这些文章么', function(index) {
                var list = table.checkStatus('cate-list');
                var params = [];
                for (var i = 0; i < list.data.length; ++i) {
                    params.push(list.data[i]['id']);
                }
                $.post('/Api/Category/del', {list: JSON.stringify(params)}, function(data) {
                    if (typeof data == 'string') {
                        data = eval('(' + data + ')');
                    }
                    if (data.code) {
                        layer.alert(data.msg);
                    } else {
                        categoryList.reload({});
                        layer.msg('删除成功');
                    }
                });
                layer.close(index);
            });
        }

        return false;
    });

    // 监听单元格编辑
    table.on('edit(cate-list)', function(obj) {
        $.post('/Api/Category/edit', {id: obj.data.id, field: obj.field, value: obj.value}, function(data) {
            data = eval('(' + data + ')');
            if (data.code) {
                layer.msg(data.msg);
                categoryList.reload({});
            }
        });
    });

    // 工具栏监听
    table.on('tool(cate-list)', function(obj) {
        var data = obj.data;
        var event = obj.event;

        if (event == 'del') {
            layer.confirm('确定删除这个分类？', {title: '提示'}, function(index) {
                layer.close(index);
                $.post('/Api/Category/del', {id: data.id}, function(data) {
                    if (typeof data == 'string') {
                        data = eval('(' + data + ')');
                    }
                    if (data.code) {
                        layer.alert(data.msg);
                    } else {
                        obj.del();
                        layer.msg('删除成功');
                    }
                });
            });
        }
    });

    ////////////////
    // 友情链接页面 //
    ////////////////

    // 渲染表格
    var linkList = table.render({
        elem: '#link-list',
        page: true,
        data: [],
        url: '/Api/Link/all',
        cols: [
            [{
                type: 'checkbox'
            }, {
                field: 'title',
                title: '网站名',
                align: 'center',
                width: 200,
                edit: 'text'
            }, {
                field: 'url',
                title: '友链地址',
                align: 'center',
                edit: 'text'
            }, {
                fixed: 'right',
                title: '操作',
                align: 'center',
                width: 90,
                toolbar: '#tools'
            }]
        ]
    });

    // 监听单元格编辑
    table.on('edit(link-list)', function(obj) {
        $.post('/Api/Link/edit', {id: obj.data.id, field: obj.field, value: obj.value}, function(data) {
            data = eval('(' + data + ')');
            if (data.code) {
                layer.msg(data.msg);
                linkList.reload({});
            }
        });
    });

    // 工具栏监听
    table.on('tool(link-list)', function(obj) {
        var data = obj.data;
        var event = obj.event;

        if (event == 'del') {
            layer.confirm('确定删除这个友情链接？', {title: '提示'}, function(index) {
                layer.close(index);
                $.post('/Api/Link/del', {id: data.id}, function(data) {
                    if (typeof data == 'string') {
                        data = eval('(' + data + ')');
                    }
                    if (data.code) {
                        layer.alert(data.msg);
                    } else {
                        obj.del();
                        layer.msg('删除成功');
                    }
                });
            });
        }
    });

    // 监听分类管理
    $('[data-type="del"]').on('click', function() {
        layer.confirm('确定删除这些友情链接么', function(index) {
            var list = table.checkStatus('link-list');
            var params = [];
            for (var i = 0; i < list.data.length; ++i) {
                params.push(list.data[i]['id']);
            }
            $.post('/Api/Link/del', {list: JSON.stringify(params)}, function(data) {
                if (typeof data == 'string') {
                    data = eval('(' + data + ')');
                }
                if (data.code) {
                    layer.alert(data.msg);
                } else {
                    linkList.reload({});
                    layer.msg('删除成功');
                }
            });
            layer.close(index);
        });
    });

    // 监听分类管理
    form.on('submit(link)', function(obj) {
        var event = $(obj.elem).attr('lay-event');

        if (event == 'add') {
            var index = layer.open({
                type: 3,
                icon: 1,
            });
            $.post('/Api/Link/add', obj.field, function(data) {
                if (typeof data == 'string') {
                    data = eval('(' + data + ')');
                }
                layer.close(index);
                if (data.code) {
                    layer.alert(data.msg);
                } else {
                    layer.msg('添加成功');
                    linkList.reload({});
                }
            });
        }

        return false;
    });


    exports('blog');
});
