// 元素状态切换
function elemAddClass(elem, cla) {
    if (elem.hasClass(cla)) {
        elem.removeClass(cla);
    } else {
        elem.addClass(cla);
    }
}

// 手机版顶部菜单
$('.toggle-btn').on('click', function() {
    elemAddClass($('.head-menu'), 'active');
});
// 搜索
$('.search-btn').on('click', function() {
    elemAddClass($('.head-search'), 'active');
});
// 夜间模式
$('.light-btn').on('click', function() {
    elemAddClass($('body'), 'neon');
});
// 显示返回顶部按钮
$(window).on('scroll', function() {
    let scroll = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
    scroll >= window.innerHeight / 2 ? $('.to-top').addClass("active") : $('.to-top').removeClass("active");
});

// 如果是在文章页，生成动态目录
if ($(".post-content").length > 0 || $('.page-content').length > 0) {
    $('body').addClass('has-trees');
    let trees = $('<aside class="article-list"></aside>');
    trees.append('<h3>文章目录：</h3>');

    $('.post-content').find("h1, h2, h3, h4, h5, h6").each(function (i) {
        let cur = $(this),
            a = $('<a></a>');
        // 初始化标签
        a.text(cur.text());
        a.prop('href', '#' + cur.text());
        // 定位标题锚点
        cur.prop('id', cur.text());

        // 生成导航样式
        switch (cur.prop("tagName")) {
            case "H2":
                a.addClass("item-2");
                break;
            case "H3":
                a.addClass("item-3");
                break;
            case "H4":
                a.addClass("item-4");
                break;
            case "H5":
                a.addClass("item-5");
                break;
            case "H6":
                a.addClass("item-6");
                break;
        }

        trees.append(a);
    });

    $('.wrap').append(trees);

    $('#add-comment').on('click', function () {
        if ($('[name="id"]').val() == '' || $('[name="text"]').val() == '' || $('[name="email"]').val() == '' || $('[name="author"]').val() == '') {
            layer.msg('不要闹，请输入完整信息~');
            return false;
        }
        let params = {
            'id': $('[name="id"]').val(),
            'url': $('[name="url"]').val(),
            'text': $('[name="text"]').val(),
            'email': $('[name="email"]').val(),
            'author': $('[name="author"]').val(),
        };
        $.post('/Article/comment.html', params, function (data) {
            if (typeof data == 'string') {
                data = eval('(' + data + ')');
            }
            if (data.code != 0) {
                layer.alert(data.msg);
            } else {
                layer.msg('评论发布成功');
                setTimeout(function () {
                    window.location.reload();
                }, 3000);
            }
        });

        return false;
    });
}
