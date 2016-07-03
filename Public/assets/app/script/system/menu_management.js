/**
 * Created by kbylin on 17/05/16.
 */
soya.ready(function () {
    "use strict";

    var active_index = 0;

    //create header-menu and sidebar-menu,all assigned to same group
    var HeaderNestable = Dazzling.nestable.create(1).attachTo("#top_nestable_attach");
    var SiderNestable = Dazzling.nestable.create(1).attachTo("#side_nestable_attach");

    var MenuItemAddModal = Dazzling.modal.create("#MenuItemAddModal", {
        'confirmText': '提交',
        'cancelText': '关闭'
    }).onCancel(function () {
        MenuItemAddModal.hide();/* cancel btn is always to close the window,but confirm not */
    });

    //上下文菜单对象
    var MenuItemContextMenu = Dazzling.contextmenu.create(
        [{'edit':'修改','delete':'删除'}],
        function (element,tabindex) {
            var obj = element.get(0).dataset;
            if('edit' === tabindex){
                operator.menuitem.initMenuItemEdit(element,obj);
            }else if('delete' === tabindex){
                // console.log(element);
                if(element.find('ol>li').length) return Dazzling.toast.warning('删除该节点前请先删除子菜单项!');
                operator.menuitem.deleteMenuItem(element,obj['id']);
            }else{
                throw "Unknown operation!";
            }
        }
    );

    //操作列表
    var operator = (function () {
        var current_index = 0;//current sidebar config index
        var header_menu = null;
        var sidebar_menu = null;
        return {
            //load the top menu(whose index is 1)
            loadHeaderMenu:function (menu) {
                var isfirst = true;
                HeaderNestable.load(menu,function (data, element) {
                    // console.log(element);
                    MenuItemContextMenu.bind(element);
                    if(isfirst) {
                        //active the first element default
                        active_index = data['id'];
                        Dazzling.nestable.active(element);
                        isfirst = false;
                    }
                    // console.log(element);
                });
            },
            loadSidebarMenuByIndex:function (index) {
                if(sidebar_menu.hasOwnProperty(index)){
                    var menu = sidebar_menu[index];
                    if(menu.hasOwnProperty('value')){
                        console.log('^ load side menu config ^',menu['value']);
                        SiderNestable.load(menu['value']);
                    }
                }
            },
            loadMenus : function () {
                var env = this;
                Dazzling.post(public_url + 'getMenus', {}, function (data) {
                    sidebar_menu = data['sidebar'];
                    env.loadHeaderMenu(header_menu = data['header']);
                    // env.loadSidebarMenuByIndex();
                });
            },
            saveTopMenuConfig : function () {
                var value = HeaderNestable.serialize(true);
    //            console.log(value);//获取序列化
                Dazzling.post(public_url+'saveHeaderConfig',{ topset:value});
            },
            saveSideMenuConfig:function () {
                var value = SiderNestable.serialize(true);
                // console.log({sideset:value,id:current_index})
                Dazzling.post(public_url+"saveSidebarMenu",{sideset:value,id:current_index}
                );
            },
            updateIdForCreate : function (id) {
                var target = MenuItemAddModal.getElement('input[name=id]');
                target.val(id.toString());
            },
            menuitem:{
                //init the menuitem edit
                initMenuItemEdit:function (element,obj) {
                    if(!soya.utils.isObject(obj)){
                        // console.log(obj);
                        return Dazzling.toast.error('You click the wrong things!');
                    }
                    var form = MenuItemAddModal.getElement("#MenuItemAddForm");
                    soya.utils.each(obj,function (value, key) {
                        var input = form.find("[name="+key+"]");
                        input.val(value);
                        if(input.attr('name') === 'id') input.attr('readonly','readonly');
                    });

                    MenuItemAddModal.show().onConfirm(function () {
                        var obj = form.fetchObject();
                        Dazzling.post(public_url+"updateMenuItem",obj,function (data, msg,msgtype) {
                            // console.log(msg,msgtype)
                            if(msg && (msgtype > 0)){
                                Dazzling.nestable.updateItemData(element,obj);
                                MenuItemAddModal.hide();
                            }
                        });
                    });
                },
                deleteMenuItem:function (element,id) {/* delete */
                    Dazzling.post(public_url+"deleteMenuItem",{id:id},function (data, msg, type) {
                        if(msg && (type > 0)){
                            element.remove();
                        }
                    });
                }
            }
        };
    })();

    //init event handler registeration
    (function () {
        Dazzling.page.registerAction('全部展开', function () {$(".dd").nestable("expandAll");}, "fa-expand");
        Dazzling.page.registerAction('全部折叠', function () {$(".dd").nestable("collapseAll");}, "fa-compress");
        $("#addTop").click(function () {
            operator.updateIdForCreate();
            MenuItemAddModal.title('添加顶部菜单').show().onConfirm(function () {
                var obj = soya.utils.parseUrl(Dazzling.form.serialize("#MenuItemAddForm"));
                var item = HeaderNestable.createItem(obj);
                if(!item) Dazzling.toast.error('添加失败!');

                MenuItemContextMenu.bind(item);
                operator.updateIdForCreate();
            });
        });
        $("#saveTop").click(function () {
            operator.saveTopMenuConfig();
        });
        $("#addSide").click(function () {
            operator.updateIdForCreate();
            MenuItemAddModal.title('添加侧边栏菜单项').show().onConfirm(function () {
                var obj = $("#MenuItemAddForm").fetchObject();
                // return console.log(object)
                var item = SiderNestable.createItem(obj);
                if(!item) return Dazzling.toast.error('添加失败!');

                MenuItemContextMenu.bind(item);
                operator.updateIdForCreate();
            });
        });
        $("#saveSide").click(function () {
            operator.saveSideMenuConfig();
        });
        HeaderNestable.onItemClick = function (data) {
            operator.loadSidebarMenuByIndex(data.id);
        };
    })();

    operator.loadMenus();
});