// 数据类型转换
function getData(data) {
    if (typeof data == 'string') {
        try {
            data = eval('(' + data + ')');
        } catch (error) {
            console.log(error);
        }
    }

    return data;
}

/*设置cookie*/
function setCookie(name, value, days){
	if(days == null || days == ''){
		days = 300;
	}
	var exp  = new Date();
	exp.setTime(exp.getTime() + days*24*60*60*1000);
	document.cookie = name + "="+ escape (value) + "; path=/;expires=" + exp.toGMTString();
}

/*获取cookie*/
function getCookie(name) {
	var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
	if(arr = document.cookie.match(reg))
		return unescape(arr[2]);
	else
		return null;
}

/*ajax请求*/
function ajax(url, param, datat, callback) {
	$.ajax({
		type: "post",
		url: url,
		data: param,
		dataType: datat,
		success: function(data){
			callback;
		},
		error: function () {
			alert("失败..");
		}
	});
}

// 左侧菜单点击
$(".side-menu").on('click', 'li a', function (e) {
    let animationSpeed = 300;
    let $this = $(this);
    let checkElement = $this.next();

    if (checkElement.is('.menu-item-child') && checkElement.is(':visible')) {
        checkElement.slideUp(animationSpeed, function () {
            checkElement.removeClass('menu-open');
        });
        checkElement.parent("li").removeClass("active");
    } else if ((checkElement.is('.menu-item-child')) && (!checkElement.is(':visible'))) {
        // 如果菜单是不可见的
        // 获取上级菜单
        let parent = $this.parents('ul').first();
        //从父级开始找所有打开的菜单并关闭
        let ul = parent.find('ul:visible').slideUp(animationSpeed);
        //在父级中移出 menu-open 标记
        ul.removeClass('menu-open');
        //获取父级 li
        let parent_li = $this.parent("li");
        //打开菜单时添加 menu-open 标记
        checkElement.slideDown(animationSpeed, function () {
            //添加样式 active 到父级 li
            checkElement.addClass('menu-open');
            parent.find('li.active').removeClass('active');
            parent_li.addClass('active');
        });
    }
    //防止有链接跳转
    e.preventDefault();

    addIframe($this);
});

// 添加 iframe
function addIframe(cur) {
    let $this = cur;
    let h = $this.attr("href"),
        m = $this.data("index"),
        label = $this.find("span").text(),
        isHas = false;
    if (h == "" || $.trim(h).length == 0) {
        return false;
    }

    let fullWidth = $(window).width();
    if (fullWidth >= 750) {
        $(".layout-side").show();
    } else {
        $(".layout-side").hide();
    }

    $(".content-tab").each(function () {
        if ($(this).data("id") == h) {
            if (!$(this).hasClass("active")) {
                $(this).addClass("active").siblings(".content-tab").removeClass("active");
                addTab(this);
            }
            isHas = true;
        }
    });
    if (isHas) {
        $(".body-iframe").each(function () {
            if ($(this).data("id") == h) {
                $(this).show().siblings(".body-iframe").hide();
            }
        });
    }
    if (!isHas) {
        let tab = "<a href='javascript:;' class='content-tab active' data-id='" + h + "'>" + label + " <i class='icon-font'>&#xe617;</i></a>";
        $(".content-tab").removeClass("active");
        $(".tab-nav-content").append(tab);
        let iframe = "<iframe class='body-iframe' name='iframe" + m + "' width='100%' height='99%' src='" + h + "' frameborder='0' data-id='" + h + "' seamless></iframe>";
        $(".layout-main-body").find("iframe.body-iframe").hide().parents(".layout-main-body").append(iframe);
        addTab($(".content-tab.active"));
    }

    return false;
}


// 添加 tab
function addTab(cur) {
    let prev_all = tabWidth($(cur).prevAll()),
        next_all = tabWidth($(cur).nextAll());
    let other_width = tabWidth($(".layout-main-tab").children().not(".tab-nav"));
    let navWidth = $(".layout-main-tab").outerWidth(true) - other_width;//可视宽度
    let hidewidth = 0;
    if ($(".tab-nav-content").width() < navWidth) {
        hidewidth = 0
    } else {
        if (next_all <= (navWidth - $(cur).outerWidth(true) - $(cur).next().outerWidth(true))) {
            if ((navWidth - $(cur).next().outerWidth(true)) > next_all) {
                hidewidth = prev_all;
                let m = cur;
                while ((hidewidth - $(m).outerWidth()) > ($(".tab-nav-content").outerWidth() - navWidth)) {
                    hidewidth -= $(m).prev().outerWidth();
                    m = $(m).prev()
                }
            }
        } else {
            if (prev_all > (navWidth - $(cur).outerWidth(true) - $(cur).prev().outerWidth(true))) {
                hidewidth = prev_all - $(cur).prev().outerWidth(true)
            }
        }
    }
    $('title').text($(cur).text().split(' ')[0] + " - He110's Blog");
    $(".tab-nav-content").animate({
        marginLeft: 0 - hidewidth + "px"
    }, "fast");
}

// 获取宽度
function tabWidth(tabarr) {
    let allwidth = 0;
    $(tabarr).each(function () {
        allwidth += $(this).outerWidth(true)
    });
    return allwidth;
}

// 左按钮事件
$(".btn-left").on("click", leftBtnFun);
// 右按钮事件
$(".btn-right").on("click", rightBtnFun);
// 选项卡切换事件
$(".tab-nav-content").on("click", ".content-tab", navChange);
// 选项卡关闭事件
$(".tab-nav-content").on("click", ".content-tab i", closePage);
// 选项卡双击关闭事件
$(".tab-nav-content").on("dblclick", ".content-tab", closePage);


// 左按钮方法
function leftBtnFun() {
    let ml = Math.abs(parseInt($(".tab-nav-content").css("margin-left")));
    let other_width = tabWidth($(".layout-main-tab").children().not(".tab-nav"));
    let navWidth = $(".layout-main-tab").outerWidth(true) - other_width;//可视宽度
    let hidewidth = 0;
    if ($(".tab-nav-content").width() < navWidth) {
        return false
    } else {
        let tabIndex = $(".content-tab:first");
        let n = 0;
        while ((n + $(tabIndex).outerWidth(true)) <= ml) {
            n += $(tabIndex).outerWidth(true);
            tabIndex = $(tabIndex).next();
        }
        n = 0;
        if (tabWidth($(tabIndex).prevAll()) > navWidth) {
            while ((n + $(tabIndex).outerWidth(true)) < (navWidth) && tabIndex.length > 0) {
                n += $(tabIndex).outerWidth(true);
                tabIndex = $(tabIndex).prev();
            }
            hidewidth = tabWidth($(tabIndex).prevAll());
        }
    }
    $(".tab-nav-content").animate({
        marginLeft: 0 - hidewidth + "px"
    }, "fast");
}

// 右按钮方法
function rightBtnFun() {
    let ml = Math.abs(parseInt($(".tab-nav-content").css("margin-left")));
    let other_width = tabWidth($(".layout-main-tab").children().not(".tab-nav"));
    let navWidth = $(".layout-main-tab").outerWidth(true) - other_width;//可视宽度
    let hidewidth = 0;
    if ($(".tab-nav-content").width() < navWidth) {
        return false
    } else {
        let tabIndex = $(".content-tab:first");
        let n = 0;
        while ((n + $(tabIndex).outerWidth(true)) <= ml) {
            n += $(tabIndex).outerWidth(true);
            tabIndex = $(tabIndex).next();
        }
        n = 0;
        while ((n + $(tabIndex).outerWidth(true)) < (navWidth) && tabIndex.length > 0) {
            n += $(tabIndex).outerWidth(true);
            tabIndex = $(tabIndex).next()
        }
        hidewidth = tabWidth($(tabIndex).prevAll());
        if (hidewidth > 0) {
            $(".tab-nav-content").animate({
                marginLeft: 0 - hidewidth + "px"
            }, "fast");
        }
    }
}

// 选项卡切换方法
function navChange() {
    if (!$(this).hasClass("active")) {
        let k = $(this).data("id");
        $(".body-iframe").each(function () {
            if ($(this).data("id") == k) {
                $(this).show().siblings(".body-iframe").hide();
                return false
            }
        });
        $(this).addClass("active").siblings(".content-tab").removeClass("active");
        addTab(this);
    }
}

// 选项卡关闭方法
function closePage() {
    let url = $(this).parents(".content-tab").data("id");
    let cur_width = $(this).parents(".content-tab").width();
    if ($(this).parents(".content-tab").hasClass("active")) {
        if ($(this).parents(".content-tab").next(".content-tab").length) {
            let next_url = $(this).parents(".content-tab").next(".content-tab:eq(0)").data("id");
            $(this).parents(".content-tab").next(".content-tab:eq(0)").addClass("active");
            $(".body-iframe").each(function () {
                if ($(this).data("id") == next_url) {
                    $(this).show().siblings(".body-iframe").hide();
                    return false
                }
            });
            let n = parseInt($(".tab-nav-content").css("margin-left"));
            if (n < 0) {
                $(".tab-nav-content").animate({
                    marginLeft: (n + cur_width) + "px"
                }, "fast")
            }
            $(this).parents(".content-tab").remove();
            $(".body-iframe").each(function () {
                if ($(this).data("id") == url) {
                    $(this).remove();
                    return false
                }
            })
        }
        if ($(this).parents(".content-tab").prev(".content-tab").length) {
            let prev_url = $(this).parents(".content-tab").prev(".content-tab:last").data("id");
            $(this).parents(".content-tab").prev(".content-tab:last").addClass("active");
            $(".body-iframe").each(function () {
                if ($(this).data("id") == prev_url) {
                    $(this).show().siblings(".body-iframe").hide();
                    return false
                }
            });
            $(this).parents(".content-tab").remove();
            $(".body-iframe").each(function () {
                if ($(this).data("id") == url) {
                    $(this).remove();
                    return false
                }
            })
        }
    } else {
        $(this).parents(".content-tab").remove();
        $(".body-iframe").each(function () {
            if ($(this).data("id") == url) {
                $(this).remove();
                return false
            }
        });
        addTab($(".content-tab.active"))
    }
    return false
}


// 循环菜单
function initMenu(menu, parent) {
    for (let i = 0; i < menu.length; i++) {
        let item = menu[i];
        let str = "";
        try {
            if (item.isHeader == "1") {
                str = "<li class='menu-header'>" + item.name + "</li>";
                $(parent).append(str);
                if (item.childMenus != "") {
                    initMenu(item.childMenus, parent);
                }
            } else {
                item.icon == "" ? item.icon = "&#xe610" : item.icon = item.icon;
                if (item.childMenus == "") {
                    str = "<li><a href='" + item.url + "'><i class='icon-font'>" + item.icon + "</i><span>" + item.name + "</span></a></li>";
                    $(parent).append(str);
                } else {
                    str = "<li><a href='" + item.url + "'><i class='icon-font '>" + item.icon + "</i><span>" + item.name + "</span><i class='icon-font icon-right'>&#xe60b;</i></a>";
                    str += "<ul class='menu-item-child' id='menu-child-" + item.id + "'></ul></li>";
                    $(parent).append(str);
                    let childParent = $("#menu-child-" + item.id);
                    initMenu(item.childMenus, childParent);
                }
            }
        } catch (e) { }
    }
}



// 头部下拉框移入移出
$(document).on("mouseenter", ".header-bar-nav", function () {
    $(this).addClass("open");
});
$(document).on("mouseleave", ".header-bar-nav", function () {
    $(this).removeClass("open");
});

// 左侧菜单展开和关闭按钮事件
$(document).on("click", ".layout-side-arrow", function () {
    if ($(".layout-side").hasClass("close")) {
        $(".layout-side").removeClass("close");
        $(".layout-main").removeClass("full-page");
        $(".layout-footer").removeClass("full-page");
        $(this).removeClass("close");
        $(".layout-side-arrow-icon").removeClass("close");
    } else {
        $(".layout-side").addClass("close");
        $(".layout-main").addClass("full-page");
        $(".layout-footer").addClass("full-page");
        $(this).addClass("close");
        $(".layout-side-arrow-icon").addClass("close");
    }
});

// 头部菜单按钮点击事件
$(".header-menu-btn").click(function () {
    $(".layout-side").removeClass("close");
    $(".layout-main").removeClass("full-page");
    $(".layout-footer").removeClass("full-page");
    $(".layout-side-arrow").removeClass("close");
    $(".layout-side-arrow-icon").removeClass("close");

    $(".layout-side").slideToggle();
});

// 左侧菜单响应式
$(window).resize(function () {
    let width = $(this).width();
    if (width >= 750) {
        $(".layout-side").show();
    } else {
        $(".layout-side").hide();
    }
});

// 皮肤选择
$(".dropdown-skin li a").click(function () {
    let v = $(this).attr("data-val");
    $("#layout-skin").prop("href", '/static/css/' + v + '.css');

    setCookie("skin", v);
});

// 获取 cookie 中的皮肤
function getSkinByCookie() {
    let v = getCookie("skin");
    let hrefStr = $("#layout-skin").attr("href");
    if (v == null || v == "") {
        v = "skin-default";
    }
    if (hrefStr != undefined) {
        $("#skin").prop("href", '/static/css/' + v + '.css');
    }
}

// 随机颜色
function getMathColor() {
    let arr = new Array();
    arr[0] = "#ffac13";
    arr[1] = "#83c44e";
    arr[2] = "#2196f3";
    arr[3] = "#e53935";
    arr[4] = "#00c0a5";
    arr[5] = "#16A085";
    arr[6] = "#ee3768";

    let le = $(".menu-item > a").length;
    for (let i = 0; i < le; i++) {
        let num = Math.round(Math.random() * 5 + 1);
        let color = arr[num - 1];
        $(".menu-item > a").eq(i).find("i:first").css("color", color);
    }
}

/*
  初始化加载
*/
$(function () {
    /*菜单json*/
    let menu = [];
    $.get('/Api/Menu/get.html', function (data) {
        menu = getData(data);

        initMenu(menu, $(".side-menu"));
        $(".side-menu > li").addClass("menu-item");
    });
});

let a = 1;
let vv = 1;
