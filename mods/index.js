layui.define(['form', 'layer', 'jquery', 'code'], function(exports) {
    var $ = layui.jquery
        ,form = layui.form
        ,layer = layui.layer
        ,code = layui.code();

    layer.msg('Hello Blog');

    layui.code({
        elem: 'pre>code',
        height: '200px',
        encode: true,
        title: '代码如下',
        about: false
    });

    exports('blog');
});
