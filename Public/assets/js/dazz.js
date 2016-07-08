/**
 * Created by lnzhv on 7/7/16.
 */
soya.ready(function () {
    //依赖于页面上的元素，故必须在页面加载完毕！后进行
    window.dazz = (function () {
        var tabnav = $("ul.tab-nav");
        var sidenav_items = $(".sidenav>li");
        var _tip = $(".normal_tips");
        if(tabnav.children('li').length === 0) tabnav.css('display','none');
        if($("ul.sub_tab>li").length === 0) $(".sub-tab-nav").css('display','none');
        if(_tip.length) _tip.css('display','none');
        if(sidenav_items.length === 0) $(".sidebar").css('display','none');

        return {
            nav:{
                tip:function (msg) {
                    if(_tip){
                        _tip.find('.txt').html(msg);
                        _tip.fadeIn();
                    }
                }
            }
        };
    })();
});