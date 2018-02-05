layui.define(['form', 'layer', 'jquery', 'laypage'], function(exports) {
    var $ = layui.jquery
        ,form = layui.form
        ,layer = layui.layer
        ,laypage = layui.laypage;

    layer.msg('Hello Blog');

    laypage.render({
        elem: 'post-page',
        count: 1000,
        groups: 3,
    });

    exports('blog');
});
