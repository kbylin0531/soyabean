/**
 * Dazzling dazz粲
 * @type object
 */
var Dazzling = {};
soya.ready(function () {
    Dazzling = (function () {
        "use strict";
        if (!jQuery) throw "Require Jquery!";

        var convention = {
            requestInterval: 400,//post刷新间隔
            //respond break point
            sizeXS: 480,// extra small
            sizeSM: 768,// small
            sizeMD: 992,// medium
            sizeLG: 1200// large
        };

        var global = {
            header_active_index:-1,
            sidebar_active_index:-1
        };

        var thishtml = $('html');
        var thisbody = $("body");

        //Conponents on page
        var page = (function () {
            var header = {
                header: null ,
                header_menu:null,
                getHeader:function () {
                    if(!this.header) this.header = $('.page-header');
                    if(!this.header.length) throw  "page-header not found!";
                    return this.header;
                },
                getHeaderMenu:function () {
                    if(!this.header_menu) this.header_menu = $('.page-header>.page-header-inner>.hor-menu>.nav.navbar-nav');
                    if(!this.header_menu.length) throw "page-header-menu not found!";
                    return this.header_menu;
                },

                setSearchHandler:function (handler) {
                    if (!handler) handler = function () { console.log('No handler has been set for header search');};

                    var pageheader = this.getHeader();
                    pageheader.on('click', '.search-form', function () {
                        var form_controll = pageheader.find('.form-control');
                        $(this).addClass("open");
                        form_controll.focus();
                        /* 主动聚焦 */
                        form_controll.on('blur', function () {/* 失去焦点时自动关闭 */
                            $(this).closest('.search-form').removeClass("open");
                            $(this).unbind("blur");
                        });
                    });

                    // handle hor menu search form on enter press
                    pageheader.on('keypress', '.hor-menu .search-form .form-control', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var val = $(this).closest('.search-form').find('.form-control').val();
                        if (e.which == 13) {/* 按下回车自动提交 */
                            return handler(val);
                        }
                        return false;
                    });

                    // handle header search button click
                    pageheader.on('mousedown', '.search-form.open .submit', function (e) {/* .submit是超链接,需要阻止事件的传播 */
                        e.preventDefault();
                        e.stopPropagation();
                        var val = $(this).closest('.search-form').find('.form-control').val();
                        if (typeof handler === 'function') return handler(val);
                        return false;
                    });
                },
                menu : {
                    /**
                     * create and return the handler of topmenu
                     * @returns {{}}
                     */
                    getInstance: function () {
                        var instance = soya.newInstance(this);
                        instance.target = page.header.getHeaderMenu();
                        return instance;
                    },
                    //get the children attribute of element.It will return an array if attribute exist but null while not exist
                    _getChildren: function (element, childrenattrname) {
                        if (!childrenattrname) childrenattrname = 'children';
                        return (element.hasOwnProperty(childrenattrname) && element[childrenattrname]) ?
                            element[childrenattrname] : false;
                    },
                    /**
                     * create and return the anchor by config
                     * @param config
                     * @param isappend default prepend to anchar
                     * @param callback define Anchor href by self,format like 'function(element,config){...}'
                     * @returns {jQuery}
                     * @private
                     */
                    _createAnchor: function (config, isappend, callback) {
                        var a = $(document.createElement('a'));
                        config.hasOwnProperty('title') || (config.title = 'Untitled');
                        a.text(" " + config.title + " ");
                        var href;
                        callback && (href = callback(a, config));//return true value can prevent default value setting
                        if (!href) {
                            // console.log(config,config.hasOwnProperty('href'))
                            config.hasOwnProperty('href') || (config.href = '#');
                            a.attr('href', config.href);
                        }
                        //有子菜单,设置下拉属性
                        this._getChildren(config) && a.attr('data-toggle', 'dropdown');
                        //设置了图标的情况下创建<i class="XX"></i>
                        if (config.hasOwnProperty('icon') && config.icon) {
                            var icon = $(document.createElement('i')).addClass(config.icon);
                            isappend ? icon.appendTo(a) : icon.prependTo(a);
                            // isPrepend?icon.prependTo(a):icon.appendTo(a);
                        }
                        return a;
                    },
                    /**
                     * create and return the
                     * @param menuitemsconf
                     * @param isappend
                     * @param callback for create anchor
                     * @returns {*|jQuery}
                     * @private
                     */
                    _createUnorderedLists: function (menuitemsconf, isappend, callback) {
                        //不存在子菜单或者子菜单为空的情况下时直接返回
                        var children = this._getChildren(menuitemsconf);
                        var list = $(document.createElement('ul')).addClass('dropdown-menu');
                        if (children) {
                            var env = this;
                            isappend = isappend ? true : false;
                            //创建并添加ul
                            soya.utils.each(children, function (child) {
                                var li = $(document.createElement('li'));
                                li.attr('menu-id', menuitemsconf['id']);//set menu-id for li
                                li.append(env._createAnchor(child, isappend, callback));
                                list.append(li);
                                if (env._getChildren(child)) {
                                    li.addClass('dropdown-submenu');
                                    li.append(env._createUnorderedLists(child));
                                }
                            });
                        }
                        return list;
                    },
                    /**
                     * load data from header menu
                     * @param data loading data
                     * @param callback create anchor callback
                     * @returns {{}}
                     */
                    load: function (data, callback) {
                        var env = this;
                        soya.utils.each(data, function (menuitem) {
                            // console.log(menuitem)
                            var li = $(document.createElement('li'));
                            li.addClass('classic-menu-dropdown');
                            li.attr('menu-id', menuitem['id']);//set menu-id for li

                            var haschild = env._getChildren(menuitem) ? true : false;

                            menuitem['icon'] = haschild?'icon-angle-down':false;
                            li.append(env._createAnchor(menuitem, true, callback));//true is due to 'icon-angle-down' is append
                            if (haschild) li.append(env._createUnorderedLists(menuitem, false, callback));// icon is aheand the inner title

                            env.target.append(li);
                        });
                        return env;
                    },
                    active:function (index) {
                        if(undefined === index) index = global.header_active_index;
                        var menu = page.header.getHeaderMenu().find("[menu-id="+index+"]");
                        menu.addClass("active");
                        menu.parents('#dazz_header_menu li').addClass('active');
                    }
                },
                user: {
                    /**
                     * init user menu with certain config and element on page
                     * @param menu menu config of an array/object
                     */
                    setMenu: function (menu) {
                        var ele = $(".dropdown-user>.dropdown-menu");
                        soya.utils.each(menu, function (item) {
                            var li = document.createElement('li');
                            var a = document.createElement('a');
                            a.innerHTML = item['title'];
                            li.appendChild(a);

                            if (!item.hasOwnProperty('href')) item['href'] = '#';
                            a.setAttribute('href', soya.context.getBaseUri()+item['href']);

                            if (item.hasOwnProperty('icon')) {
                                var i = document.createElement('i');
                                i.setAttribute('class',item['icon']);
                                a.appendChild(i);
                            }
                            ele.append($(li));
                        });
                    }
                }
            };
            var sidebar = {
                sidebar: null ,
                sidebar_menu: null,
                getSidebar:function(){
                    if(!this.sidebar) this.sidebar = $('.page-sidebar');
                    if(!this.sidebar.length) throw "could not find the page-sidebar";
                    return this.sidebar;
                },
                getSidebarMenu:function () {
                    if(!this.sidebar_menu) this.sidebar_menu = $('.page-sidebar-menu');
                    if(!this.sidebar_menu.length) throw "could not find the page-sidebar";
                    return this.sidebar_menu;
                },
                checkInit:function () {
                    this.getSidebar();
                    this.getSidebarMenu();
                },
                nowIsClosed:function(){
                    this.checkInit();
                    return thisbody.hasClass("page-sidebar-closed");
                },
                lastIsClosed:function () {
                    this.checkInit();
                    return parseInt(soya.utils.cookie.get('_sc_'))?true:false;//sidebar_closed => _sc
                },
                open:function () {
                    this.checkInit();
                    thisbody.removeClass("page-sidebar-closed");
                    this.sidebar_menu.removeClass("page-sidebar-menu-closed");
                    soya.utils.cookie.set('_sc_',0,0);
                },
                close:function () {
                    this.checkInit();
                    thisbody.addClass("page-sidebar-closed");
                    this.sidebar_menu.addClass("page-sidebar-menu-closed");
                    soya.utils.cookie.set('_sc_',1,0);
                },
                menu:{
                    getInstance: function () {
                        var instance = soya.newInstance(this);
                        instance.target = page.sidebar.getSidebarMenu();
                        return instance;
                    },
                    _getAnchor: function (attrs, hasSubmenu) {
                        hasSubmenu || (hasSubmenu = attrs.hasOwnProperty('children'));
                        var a = $(document.createElement('a')).addClass(hasSubmenu ? 'nav-link nav-toggle' : 'nav-link');

                        //default icon
                        if (!attrs.hasOwnProperty('icon') || !attrs['icon']) attrs['icon'] = 'fa-slack';//默认图标

                        //链接
                        a.append($('<i class="fa ' + attrs['icon'] + '"></i>')).append($('<span class="title"> ' + attrs['title'] + ' </span>'));

                        if (hasSubmenu) {
                            //如果有子菜单，追加下拉
                            a.append($('<i class="float-right fa fa-angle-right"></i>'));
                        } else {
                            //create link for this anchor
                            if (attrs['value']) {
                                attrs['value'] = soya.context.getBaseUri() + attrs['value'];
                            } else {
                                attrs['value'] = 'javascript:void(0);';
                            }
                            a.attr('href', attrs['value']);
                            a.attr('data-id',attrs['id']);
                        }

                        return a;
                    },
                    _getUnorderedLists: function (menuitem) {
                        if (!menuitem.hasOwnProperty('children') || !menuitem.children) return;//不存在子菜单时直接返回
                        var li_ul = $('<ul class="sub-menu"></ul>');

                        var env = this;
                        soya.utils.each(menuitem.children, function (subitem) {
                            var hasSubmenu = subitem.hasOwnProperty('children');

                            var li_navitem = $(document.createElement('li'));
                            li_navitem.addClass('nav-item');
                            li_navitem.append(env._getAnchor(subitem, hasSubmenu));
                            li_ul.append(li_navitem);
                            if (hasSubmenu) {
                                li_navitem.append(env._getUnorderedLists(subitem));
                            }
                        });
                        return li_ul;
                    },
                    /**
                     * load side menu config
                     * @param data sidebar menu config
                     * @param path
                     * @returns {Object}
                     */
                    load: function (data, path) {
                        var env = this;
                        var result = this.findOuter(data, path, 'value');
                        if(false === result){
                            throw "No !!!";
                        }
                        global.header_active_index = result[0];
                        global.sidebar_active_index = result[1]['id'];
                        // console.log(global);

                        var sideconf = data[result[0]];
                        var sidemenu = sideconf['value'];

                        soya.utils.each(sidemenu, function (topitem) {
                            // console.log(topitem);
                            var li_navitem = $(document.createElement('li')).addClass('nav-item');
                            var hasSubmenu = topitem.hasOwnProperty('children');

                            var a = env._getAnchor(topitem, hasSubmenu);
                            li_navitem.append(a);
                            hasSubmenu && li_navitem.append(env._getUnorderedLists(topitem));
                            // console.log(li_navitem,env.target)
                            env.target.append(li_navitem);
                        });
                        return env;
                    },
                    findInner: function (item, compval,feature,featurecompcallback) {
                        // console.log(item, compval,feature);
                        feature || (feature = 'value');
                        var env = this;
                        if(item) {
                            if(soya.utils.isArray(item)){
                                return soya.utils.each(item,function (subitem) {
                                    // console.log(subitem);
                                    var result = env.findInner(subitem, compval,feature,featurecompcallback);
                                    // console.log(result);
                                    if (result) return result;
                                });
                            }else{
                                if(item.hasOwnProperty('value')){
                                    var value = item['value'];//对发现的值进行比较
                                    // console.log(item,value,compval,soya.utils.isFunc(featurecompcallback),value === compval)

                                    if(soya.utils.isFunc(featurecompcallback)){
                                        if(featurecompcallback(item,compval)) return item;
                                    }else{
                                        if (value === compval) {
                                            // console.log('XXXXXXXXXXXX',item);
                                            return item;
                                        }
                                    }

                                    if (item.hasOwnProperty('children')) {
                                        var result = env.findInner(item['children'], compval,feature,featurecompcallback);
                                        // console.log(result);
                                        if (result) return result;
                                    }
                                }
                            }
                        }
                        return false;
                    },
                    /**
                     * find what the menu hold the menuitem of this id
                     * @param menus the menu to go through
                     * @param compval compatable value
                     * @param feature what the attrbute to compore,default to 'id'
                     * @param featurecompcallback feature compare callback
                     * @returns {*} return the index while find but false on failure
                     */
                    findOuter: function (menus, compval,feature,featurecompcallback) {
                        var env = this;
                        var result = soya.utils.each(menus,function (menu,id) {
                            if(menu.hasOwnProperty('value') && menu['value']){
                                // console.log(menu['value'], compval);
                                var result = env.findInner(menu['value'], compval,feature,featurecompcallback);
                                // console.log(result)
                                if(false !== result){
                                    return [id,result];
                                }
                            }
                        });
                        // console.log('######## Side Result #########',result);
                        return result !== undefined?result:false;
                    },
                    active:function (index) {
                        if(undefined === index) index = global.sidebar_active_index;
                        var target = page.sidebar.getSidebarMenu().find("[data-id="+index+"]");
                        target.parents('li.nav-item').addClass("active");
                    }
                }
            };
            var adjustHeight = function () {
                var height = soya.context.getViewPort().height;
                var target = arguments[0];
                for(var i = 1 ; i < arguments.length;i++){
                    var element = arguments[i];
                    element = util.toJquery(element);
                    height -= element.outerHeight();
                    // console.log(height)
                }
                target.css('min-height' , height + 'px');
            };

            return {
                setTitle:function (title) {
                    $("title").text(title);
                },
                setLogo:function (logoText) {
                    $(".text-logo").html(logoText);
                },
                //attributs
                header: header,
                content: {
                    content:null,
                    getContent:function () {
                        if(!this.content) this.content = $('.page-content');
                        return this.content;
                    }
                },

                //operate the sidebar status
                sidebar: sidebar,

                footer: {
                    footer:null,
                    getFooter:function(){
                        if(!this.footer) this.footer = $('.page-footer');
                        if(!this.footer.length) throw "footer not found ";
                        return this.footer;
                    },
                    setCopyright:function (copyright) {
                        this.getFooter().find(".page-copyright").html(copyright);
                    }
                },

                behavior:{
                    adjustHeight:adjustHeight,
                    autoContentHeight: function () {
                        adjustHeight($('.page-content'),page.header.getHeader(),page.footer.getFooter());
                    }
                },

                //handle the element size change while window resized
                resizer:{
                    handlers:[],
                    push:function (handler) {
                        this.handlers.push(handler);
                    },
                    exec:function (index) {
                        if(undefined === index) {
                            for (var i = 0; i < this.handlers.length; i++)  this.handlers[i].call();//执行调整函数
                        }else{
                            if((index >= 0) && (index <this.handlers.length)){
                                this.handlers[index].call();
                            }
                        }
                    }
                },

                init: function (selector) {
                    if (undefined === selector) selector = '.page-toolbar .dropdown-menu';//默认的选择器
                    !this.page_action_list && (this.page_action_list = util.toJquery(selector));
                },
                page_action_list: null,
                //注册操作:操作名称,点击时候的回调函数
                registerAction: function (actionName, callback, icon) {
                    !this.page_action_list && (this.page_action_list = $('.page-toolbar .dropdown-menu'));
                    this.init();
                    var li = $("<li></li>");
                    var a;
                    if (icon) {
                        a = $('<a href="javascript:void(0);" id="la_' + soya.utils.guid() + '"><i class="fa ' + icon + '"></i> ' + actionName + '</a>');
                    } else {
                        a = $('<a href="javascript:void(0);" id="la_' + soya.utils.guid() + '"> ' + actionName + '</a>');
                    }
                    this.page_action_list.append(li.append(a));
                    a.click(callback);
                },
                DataManager: {}
            };
        })();

        //do some compatibility relatid work
        (function () {
            $.prototype.fetchObject = function () {
                var values = {};
                var inputs = this.find("input[name],select[name],checkbox");
                // console.log(inputs)
                for (var i = 0; i < inputs.length; i++) {
                    var input = inputs.eq(i);
                    var inputname = input.attr('name');
                    values[inputname] = $.trim(input.val());
                }
                return values;
            };
        })();

        //general kits
        var util = (function () {
            return {
                /**
                 * 按图（索骥）
                 * 自动按照键值对映射将选择器的属性填写到指定的元素中
                 * @param selectores 例如：["avatar.src" , ".avatar.src" ,".avatar" , "avatar"]
                 * @param kvmap 例如： {id: "1", username: "admin", sex: "1", nickname: "Administrator", phone: "15658070289"…}
                 */
                antu: function (selectores, kvmap) {
                    // console.log(selectores,kvmap);
                    soya.utils.each(selectores, function (id) {
                        var index = id.indexOf('.');
                        var selector = true;//id selector
                        if(-1 === index){
                            //只是设置innerHtml
                            kvmap.hasOwnProperty(id) && $("#" + id).html(kvmap[id])
                        }else if(0 === index){//在'ID'前面加'.'可以将之声明为类选择器
                            var len = id.length;
                            id = id.substr(1,len);
                            selector = false;//class selector
                        }
                        var temp = id.split('.');
                        if(kvmap.hasOwnProperty(temp[0])){
                            selector = selector?$("#" + temp[0]):$("." + temp[0]);
                            selector.attr(temp[1], kvmap[temp[0]]);
                        }
                    });
                },
                toJquery: function (selector) {
                    switch (typeof selector){
                        case 'object':
                            if (selector instanceof HTMLElement) {
                                return $(selector);
                            } else if (selector instanceof jQuery) {
                                return selector;
                            }
                            break;
                        case 'string':
                            return $(selector);
                    }
                    console.log(selector);
                    throw "错误的数据类型";
                },
                /**
                 * last request time
                 */
                _lqt : 0,
                /**
                 * @param url 请求地址
                 * @param data 请求数据对象
                 * @param callback 服务器响应时的回调,如果回调函数返回false或者无返回值,则允许系统进行通知处理,返回true表示已经处理完毕,无需其他的操作
                 * @param datatype 期望返回的数据类型 json xml html script json jsonp text 中的一种
                 * @param async 是否异步,希望同步的清空下使用false,默认为true
                 * @returns {*}
                 */
                doPost: function (url, data, callback, datatype, async) {
                    var curmillitime = (new Date()).valueOf();
                    if (!this._lqt) {
                        this._lqt = curmillitime;
                    } else {
                        var gap = curmillitime - this._lqt;
                        this._lqt = curmillitime;
                        if (gap < parseInt(convention['requestInterval'])) {
                            return Dazzling.toast.warning('请勿频繁刷新!');
                        }
                    }

                    !datatype && (datatype = "json");
                    !async && (async = true);

                    if (typeof data === 'string') {
                        data = {
                            '_KBYLIN_': data //后台会进行分解
                        };
                    }

                    return jQuery.ajax({
                        url: url,
                        type: 'post',
                        dataType: datatype,
                        async: async,
                        data: data,
                        success: function (data) {
                            var message_type;
                            // check if is the system-defined message format(has '' and '' attribute)
                            var isMsg = (data instanceof Object) && data.hasOwnProperty('_type') && data.hasOwnProperty('_message');
                            //通知处理
                            isMsg && (message_type = parseInt(data['_type']));

                            if (callback && callback(data, isMsg, message_type)) return;//如果用户的回调明确声明返回true,表示已经处理得当,无需默认的参与

                            if (isMsg) {
                                if (message_type > 0) {
                                    return Dazzling.toast.success(data['_message']);
                                } else if (message_type < 0) {
                                    return Dazzling.toast.warning(data['_message']);
                                } else {
                                    return Dazzling.toast.error(data['_message']);
                                }
                            }
                        }
                    });
                }
            };
        })();

        var bootmodal = {
            /**
             * 创建一个Modal对象,会将HTML中指定的内容作为自己的一部分拐走
             * @param selector 要把哪些东西添加到modal中的选择器
             * @param option modal配置
             * @returns {*}
             */
            'create': function (selector, option) {
                var config = {
                    'title': null,
                    'confirmText': '提交',
                    'cancelText': '取消',
                    'fade': true,

                    //确认和取消的回调函数
                    'confirm': null,
                    'cancel': null,

                    'show': null,//即将显示
                    'shown': null,//显示完毕
                    'hide': null,//即将隐藏
                    'hidden': null,//隐藏完毕

                    'backdrop': 'static',
                    'keyboard': true
                };
                config = soya.utils.initOption(config, option);

                var instance = soya.newInstance(this);
                var id = 'modal_' + soya.utils.guid();

                var modal = $('<div class="modal" id="' + id + '" aria-hidden="true" role="dialog"></div>');
                if (typeof config['backdrop'] !== "string") config['backdrop'] = config['backdrop'] ? 'true' : 'false';
                if (config['fade']) modal.addClass('fade');
                modal.attr('data-backdrop', config['backdrop']);
                modal.attr('data-keyboard', config['keyboard'] ? 'true' : 'false');
                thisbody.append(modal);

                var dialog = $('<div class="modal-dialog"></div>');
                modal.append(dialog);
                var content = $('<div class="modal-content"></div>');
                dialog.append(content);

                //设置title部分
                var header = $('<div class="modal-header"></div>');
                var close = $('<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>');
                header.append(close);
                content.append(header);

                //设置体部分
                var body = $('<div class="modal-body"></div>');
                body.appendTo(content);
                body.append(util.toJquery(selector));

                //设置足部
                var cancel = $('<button type="button" class="btn btn-default cancelbtn" data-dismiss="modal">' + config['cancelText'] + '</button>');
                var confirm = $('<button type="button" class="btn btn-primary confirmbtn">' + config['confirmText'] + '</button>');
                content.append($('<div class="modal-footer"></div>').append(cancel).append(confirm));

                //确认和取消事件注册
                confirm.click(instance.confirm);
                cancel.click(instance.cancel);
                instance.target = modal.modal('hide');

                config['title'] && instance.title(config['title']);

                //事件注册
                soya.utils.each(['show', 'shown', 'hide', 'hidden'], function (eventname) {
                    modal.on(eventname + '.bs.modal', function () {
                        // console.log(eve
                        //handle the element size change while window resizedntname,config[eventname]);
                        config[eventname] && (config[eventname])();
                    });
                });
                return instance;
            },
            //get the element of this.target while can not found in global jquery selector
            getElement: function (eleselector) {
                return this.target.find(eleselector);
            },
            confirm: function () {
                console.log('You click the confirm button,but not resister anything!')
            },
            cancel: function () {
                console.log('You click the cancel button,but not resister anything!')
            },
            onConfirm: function (callback) {
                // this.confirm = callback;
                // var btn = this.target.find(".confirmbtn");//it worked worse ,why?
                this.target.find(".confirmbtn").unbind("click").click(callback);
                return this;
            },
            onCancel: function (callback) {
                // this.cancel = callback;
                this.target.find(".cancelbtn").unbind("click").click(callback);
                return this;
            },
            //update title
            title: function (newtitle) {
                var title = this.target.find(".modal-title");
                if (!title.length) {
                    this.target.find(".modal-header").append($('<h4 class="modal-title">' + newtitle + '</h4>'));
                }
                title.text(newtitle);
                return this;
            },
            'show': function () {
                this.target.modal('show');
                return this;
            },
            'hide': function () {
                this.target.modal('hide');
                return this;
            }
        };

        return {
            //start the application
            'start': function (infos,itemsIds) {
                var resizehandler;
                var currentHeight;
                var browerinfo = soya.context.getBrowserInfo();
                var isIE8 = browerinfo.type === 'ie' && 8 === browerinfo.version;
                var isIE9 = browerinfo.type === 'ie' && 9 === browerinfo.version;
                var isIE10 = browerinfo.type === 'ie' && 10 === browerinfo.version;

                isIE8 && thishtml.addClass('ie8 ie'); // detect ie8 version
                isIE9 && thishtml.addClass('ie9 ie'); // detect ie9 version
                isIE10 && thishtml.addClass('ie10 ie'); // detect IE10 version
                (isIE8 || isIE9) && ('placeholder' in jQuery) && $('input, textarea').placeholder();//该插件存在时候开启placeholder

                //****************************************
                //init auto adjuest
                //****************************************
                $(window).resize(function () {
                    //quite event since only body resized not window.
                    if (isIE8 && (currentHeight == document.documentElement.clientHeight)) return;
                    if (resizehandler) clearTimeout(resizehandler);
                    resizehandler = setTimeout(function () {
                        page.resizer.exec();
                        // for (var i = 0; i < resizeHandlers.length; i++)  resizeHandlers[i].call();//执行调整函数
                    }, 75); // 等待window调整完成
                    // store last body client height
                    // 注意 document.body.clientHeight 和 document.documentElement.clientHeight 的区别
                    isIE8 && (currentHeight = document.documentElement.clientHeight);
                });
                //****************************************
                //init sidebar
                //****************************************
                // 控制sidebar的显示和隐藏
                if(soya.context.getViewPort().width <= convention['sizeSM'] || page.sidebar.lastIsClosed()){
                    page.sidebar.close();
                }else{
                    page.sidebar.open();
                }
                thisbody.find(".sidebar-toggler").click(function () {
                    page.sidebar.nowIsClosed() ? page.sidebar.open() : page.sidebar.close();
                    $(window).trigger('resize');
                });

                //****************************************
                // init others
                //****************************************
                page.header.setSearchHandler(); // handles horizontal menu
                page.resizer.push(page.behavior.autoContentHeight);

                var userinfo = infos['user'];
                var pageinfo = infos['page'];

                util.antu(itemsIds, userinfo);

                page.header.user.setMenu(pageinfo['user_menu']);

                //设置标题
                pageinfo.hasOwnProperty('title') && page.setTitle(pageinfo['title']);
                pageinfo.hasOwnProperty('logo') && page.setLogo(pageinfo['logo']);
                pageinfo.hasOwnProperty('coptright') && page.footer.setCopyright(pageinfo['coptright']);

                //处理顶部菜单
                page.sidebar.menu.getInstance().load(pageinfo['sidebar_menu'], pageinfo['request_path']).active();//.active(sideractiveindex);
                page.header.menu.getInstance().load(pageinfo['header_menu']).active();

                $(window).trigger('resize');
            },
            //定制的方法,定制过程中避免对jquery中的方法进行修改
            'post': util.doPost,
            //工具箱
            'utils': util,
            //页面工具
            'page': page,
            //datatable表格工具,一次只能操作一个表格API对象
            //datatable.find("tbody").on('dblclick','tr',function () {});//可以设置为双击编辑
            //改造成return new this;
            datatables: {
                'tableApi': null,//datatable的API对象
                "dtElement": null, // datatable的jquery对象
                'current_row': null,//当前操作的行,可能是一群行
                //设置之后的操作所指定的DatatableAPI对象
                'bind': function (dtJquery, options) {
                    dtJquery = util.toJquery(dtJquery);
                    var newinstance = soya.newInstance(this);
                    newinstance.dtElement = dtJquery;

                    var convention = {
                        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
                    };
                    soya.utils.each(options,function (value, key) {
                        convention[key] = value;
                    });
                    // console.log(convention);
                    newinstance.tableApi = dtJquery.DataTable(convention);
                    return newinstance;
                    /* this 对象同于链式调用 */
                },
                //为tableapi对象加载数据,参数二用于清空之前的数据
                'load': function (data, clear) {
                    if (!this.tableApi) return Dazzling.toast.error("No Datatable API binded!");
                    if (undefined === clear || clear) this.tableApi.clear();//clear为true或者未设置时候都会清除之前的表格内容
                    this.tableApi.rows.add(data).draw();
                    return this;
                },
                //表格发生了draw事件时设置调用函数(表格加载,翻页都会发生draw事件)
                'onDraw': function (callback) {
                    if (!this.dtElement) return Dazzling.toast.error("No Datatables binded!");
                    this.dtElement.on('draw.dt', function (event, settings) {
                        callback(event, settings);
                    });
                    return this;
                },
                //获取表格指定行的数据
                'data': function (element) {
                    this.current_row = element;
                    return this.tableApi.row(element).data();
                },
                'update': function (newdata, line) {
                    (line === undefined) && (line = this.current_row);
                    if (line) {
                        if (soya.utils.isArray(line)) {
                            for (var i = 0; i < line.length; i++) {
                                this.update(newdata, line[i]);
                            }
                        } else {
                            //注意:如果出现这样的错误"DataTables warning: table id=[dtable 实际的表的ID] - Requested unknown parameter ‘acceptId’ for row X 第几行出现了错误 "
                            return this.tableApi.row(line).data(newdata).draw(false);
                        }
                    }
                }
            },
            //页面的Toast工具,toast对象直接属于window对象
            toast: {
                'init': function () {
                    toastr.options.closeButton = true;
                    toastr.options.newestOnTop = true;
                },
                'success': function (msg, title) {
                    this.init();
                    window.toastr.success(msg, title);
                },
                'warning': function (msg, title) {
                    this.init();
                    toastr.warning(msg, title);
                },
                'error': function (msg, title) {
                    this.init();
                    toastr.error(msg, title);
                },
                'info': function (msg, title) {
                    this.init();
                    toastr.info(msg, title);
                },
                'clear': function () {
                    toastr.clear();
                }
            },
            //上下文菜单工具
            contextmenu: {
                //Uncaught TypeError: Cannot read property 'left' of undefined
                //while the target menu do not exist,it will throw the error
                /**
                 * create a menu-handler object
                 * @param menus format like "[{'index':'edit','title':'Edit'}]"
                 * @param handler callback while click the context menu item
                 * @param onItem
                 * @param before
                 */
                'create': function (menus, handler, onItem, before) {
                    var instance = soya.newInstance(this);

                    var id = 'cm_' + soya.utils.guid();
                    var contextmenu = $(document.createElement('div'));
                    contextmenu.attr('id', id);
                    var ul = $(document.createElement('ul'));
                    ul.addClass('dropdown-menu');
                    ul.attr('role', "menu");
                    contextmenu.append(ul);

                    var flag = false;
                    //菜单项
                    soya.utils.each(menus, function (group) {
                        flag && ul.append($('<li class="divider"></li>'));//对象之间划割
                        soya.utils.each(group, function (value, key) {
                            ul.append('<li><a tabindex="' + key + '">' + value + '</a></li>');
                        });
                        flag = true;
                    });
                    $("body").prepend(contextmenu);

                    before || (before = function (e, c) {
                    });
                    onItem || (onItem = function (c, e) {
                    });
                    handler || (handler = function (element, tabindex, title) {
                    });

                    //这里的target的上下文意思是 公共配置组
                    instance.target = {
                        target: '#' + id,
                        // execute on menu item selection
                        onItem: function (context, event) {
                            onItem(context, event);
                            var target = event.target;
                            handler(context, target.getAttribute('tabindex'), target.innerText);
                        },
                        // execute code before context menu if shown
                        before: before
                    };
                    return instance;
                },
                bind: function (selector) {
                    selector = util.toJquery(selector);
                    selector.contextmenu(this.target);
                }
            },
            //拟态框
            modal: bootmodal,
            form: {
                /**
                 * 自动填写表单
                 * @param form 表单对象或者表单选择器
                 * @param data 待设置的数据 如 {'key':'value'}
                 * @param map 数据映射 如[],{'data_key':'input_name'}
                 */
                'autoFill': function (form, data, map) {
                    if (typeof form === 'string') form = $(form);
                    if (!(form instanceof jQuery))  return Dazzling.toast.error("Parameter 1 expect to be jquery ");
                    var target, key;
                    var mapDefined = soya.utils.isObject(map, 'Object');

                    for (key in data) {
                        if (!data.hasOwnProperty(key)) continue;

                        if (mapDefined && map.hasOwnProperty(key)) {
                            target = form.find("[name=" + map[key] + "]");
                        } else {
                            target = form.find("[name=" + key + "]");
                        }

                        if (target.length) {/*表单中存在这个name的输入元素*/
                            if (target.length > 1) {/* 出现了radio或者checkbox的清空 */
                                for (var x in target) {
                                    if (!target.hasOwnProperty(x)) continue;
                                    if (('radio' === target[x].type) && parseInt(target[x].value) == parseInt(data[key])) {
                                        target[x].checked = true;
                                    }
                                }
                            } else {
                                target.val(data[key]);
                            }
                        } else {
                            form.append($('<input name="' + key + '" value="' + data[key] + '" type="hidden">'));
                        }
                    }

                },
                //fetch an url serialise from a form
                'serialize': function (selector) {
                    selector = util.toJquery(selector);
                    return selector.serialize();
                }
            },
            nestable: {
                //create nestable list and return a new instance
                create: function (group) {
                    var instance = soya.newInstance(this);

                    var id = 'nestable_' + soya.utils.guid();
                    var dd = $('<div class="dd" id="' + id + '"></div>');

                    instance.target = dd.nestable({group: group ? group : id});

                    return instance;
                },
                //load the data for this.target
                load: function (data, callback) {
                    callback || (callback = null);//显示声明为空
                    // console.log('####### header data #######',data);
                    data && this.createItemList(data, this.target, callback);
                    return this;
                },
                //创建LI节点
                createItem: function (object, target, callback) {
                    var env = this;
                    //检查基本的两个属性
                    if (soya.utils.checkProperty(object, ['id', 'title']) < 1) {
                        return console.log('id/title should not be empty!',object);
                    }
                    var handle = $('<div class="dd-handle dd3-handle">');
                    var content = $('<div class="dd3-content">' + object['title'] + '</div>');
                    var linode = $('<li class="dd-item dd3-item"></li>').append(handle).append(content);

                    //点击激活当前区域
                    content.click(function (e) {
                        if (false === env.onItemClick(object, e.target, e)) return; /* prevent the status change while callback return a false */
                        env.passiveAll();
                        env.active(e.target);
                    });
                    //set attribute for this item expect 'children'
                    this.updateItemData(linode, object, function (ele, obj) {return '<i class="' + obj['icon'] + '"></i> ' + obj['title'];});


                    // return console.log(linode,object);
                    //设置attach目标
                    if (!target) target = this.target;
                    if (!target) return console.log('No target to attach!');

                    var tagname = target.get(0).tagName.toUpperCase();
                    // console.log(target)
                    switch (tagname) {
                        case 'DIV'://直接点击添加时候
                        case 'LI':
                            //设置ol
                            var targetol = target.children('ol');
                            if (!targetol.length) {
                                //不存在ol链表时创建
                                this.createItemList([], target);
                                targetol = target.children('ol');
                            }
                            targetol.prepend(linode);
                            break;
                        case 'OL':
                            target.append(linode);
                            break;
                        default:
                            throw "无法在该元素上创建列表:" + tagname;
                    }
                    // callback && callback(object, linode);//每次遍历一项回调

                    // console.log('##### item itemlist ol ######',object,soya.utils.checkProperty(object, 'children'));
                    //look through children if attach success
                    if(soya.utils.checkProperty(object, 'children') > 0){
                        // console.log(object['children'])
                        soya.utils.each(object['children'],function (child) {
                            env.createItem(child,linode,callback);
                            // child.hasOwnProperty('children') && env.createItemList(child['children'], linode, callback);
                        });
                    }
                    return linode;
                },
                /**
                 * 创建OL节点及其子节点LI,children为子元素数组,target为创建的列表附加的目标(目标缺失时选用this.target,即dd)
                 * @param objectlist []
                 * @param target
                 * @param callback
                 * @returns {*|jQuery|HTMLElement}
                 */
                createItemList: function (objectlist, target, callback) {
                    objectlist = soya.utils.toObject(objectlist);
                    var env = this;
                    var ol = $('<ol class="dd-list"></ol>');
                    soya.utils.each(objectlist, function (object) {
                        // console.log('####### craete item list -- <li> #######',object);
                        env.createItem(object, ol, callback);
                    });

                    //寻找附加target
                    if (!(target = target ? target : this.target)) return console.log('Nestable require a target to attach!');

                    //如果target本身是ol节点,将不符合规则(ol下只能存在li,li下能存在ol)
                    // console.log(target.get(0).tagName);
                    switch (target.get(0).tagName.toUpperCase()) {
                        case 'DIV':
                        case 'LI':
                            //设置ol
                            var targetol = target.children('ol');
                            if (targetol.length) targetol.remove();//深处原来的ol
                            target.append(ol);
                            break;
                        case 'OL':
                        default:
                            throw "无法在该元素上创建列表";
                    }
                    return ol;
                },
                //update the data hold by item node,but except 'children'
                updateItemData: function (linode, data, titlecallback) {//titlecallback means update the display text(include html tag)
                    soya.utils.each(data, function (value, key) {
                        if (key === 'children') return;
                        switch (typeof value) {
                            case 'string':
                            case 'number':
                                linode.attr("data-" + key, value);
                                break;
                            case 'boolean':
                                linode.attr("data-" + key, value ? 'true' : 'false');
                                break;
                            default:
                        }
                    });
                    //update the showing content
                    titlecallback || (titlecallback = function (ele, obj) {
                        return '<i class="' + obj['icon'] + '"></i> ' + obj['title'];
                    });
                    var title = titlecallback(linode, data);
                    !title && (title = ('title' in data) ? data['title'] : 'Untitled');
                    linode.children(".dd3-content").html(title);
                },
                //cancel all active status
                passiveAll: function () {
                    this.target.find('.dd3-content').removeClass('active');//deactive others at first
                },
                //active the element
                active: function (element) {
                    element = util.toJquery(element);
                    // this.passiveAll();//cancel all active status
                    // console.log(element,element.hasClass('dd3-content'));
                    if (element.hasClass('dd3-content')) {
                        element.addClass('active');
                        // console.log(element)
                    } else {
                        element.children('.dd3-content').addClass('active');
                    }
                },
                _serialize: function (data) {
                    var env = this;
                    if ($.isArray(data)) {
                        var array = [];
                        soya.utils.each(data, function (value, key) {
                            array[key] = env._serialize(value);
                        });
                        return array;
                    } else {
                        var object = {};
                        soya.utils.each(data, function (value, key) {
                            switch (key) {
                                case 'id':
                                case 'href':
                                case 'icon':
                                case 'title':
                                    object[key] = value;
                                    break;
                                case 'children':
                                    //actually remove children while empty
                                    if (value.length) object['children'] = env._serialize(value);
                                    break;
                            }
                        });
                        return object;
                    }
                },
                //获得序列化的值,可以是对象或者数组
                serialize: function (tostring) {
                    var value = this.target.nestable('serialize');
                    if (tostring) {
                        if (!JSON) return Dazzling.toast.warning('你的浏览器不支持JSON对象!');
                        value = this._serialize(value);
                        // console.log(value);
                        value = JSON.stringify(value);
                    }
                    return value;
                },
                /**
                 * callback when element clicked
                 * @param data data of element attached
                 * @param element dom
                 * @param event
                 */
                onItemClick: function (data, element, event) {
                },
                attachTo: function (selector, append) {
                    selector = util.toJquery(selector);
                    if (append) {
                        selector.append(this.target);
                    } else {
                        selector.prepend(this.target);
                    }
                    return this;
                },
                prependTo: function (attatchment) {
                    attatchment = util.toJquery(attatchment);
                    attatchment.html('');
                    if (attatchment.length) {
                        attatchment.prepend(this.target);
                        return true;
                    }
                    return false;
                },
                appendTo: function (attatchment) {
                    attatchment = util.toJquery(attatchment);
                    attatchment.html('');
                    if (attatchment.length) {
                        attatchment.appendTo(this.target);
                        return true;
                    }
                    return false;
                }
            },
            tab: {
                _createNav: function (config) {
                    var id = soya.utils.guid();
                    var nav = $('<ul id="' + id + '" class="nav nav-tabs"></ul>');
                    var isfirst = true;
                    var node, ul;
                    for (var x = 0; x < config.length; x++) {
                        var item = config[x];
                        if (!item.hasOwnProperty('title')) return Dazzling.toast.warning('Tab require a title!');
                        if (item.hasOwnProperty('children')) {/*下拉*/
                            var guid = soya.utils.guid();
                            var children = item['children'];
                            node = $(document.createElement('li'));
                            node.append($('<a href="javascript:void(0);" id="' + guid + '" class="dropdown-toggle" data-toggle="dropdown">' + item['title'] + '<b class="caret"></b></a>'));
                            ul = $('<ul class="dropdown-menu" role="menu" aria-labelledby="' + guid + '"></ul>');
                            for (var y in children) {
                                if (!children.hasOwnProperty(y)) continue;
                                ul.append(this._createNode(children[y]));
                            }
                            node.append(ul);
                        } else {
                            if (!item.hasOwnProperty('id')) return Dazzling.toast.warning('Tab must be related with an ID!');
                            node = $('<li><a href="#' + item['id'] + '" data-toggle="tab">' + item['title'] + '</a></li>');
                        }

                        if (isfirst) {/* 激活第一个 */
                            node.addClass('active');
                            isfirst = false;
                        }

                        nav.append(node);
                    }
                    return nav;
                },
                _createNode: function (node) {
                    if (!node.hasOwnProperty('title')) return Dazzling.toast.warning('Tab require a title!');
                    if (!node.hasOwnProperty('id')) return Dazzling.toast.warning('Tab must be related with an ID!');
                    return $('<li><a href="#' + node['id'] + '" data-toggle="tab">' + node['title'] + '</a></li>');
                },
                _createContent: function (config) {
                    var content = $('<div id="' + soya.utils.guid() + '" class="tab-content"></div>');
                    for (var x = 0; x < config.length; x++) {
                        if (config[x].hasOwnProperty('children')) {/*下拉*/
                            for (var y in config[x]['children']) {
                                if (!config[x]['children'].hasOwnProperty(y)) continue;
                                content.append(this._createNode4Content(config[x]['children'][y]));
                            }
                        } else {
                            content.append(this._createNode4Content(config[x]));
                        }
                    }
                    return content;
                },
                _createNode4Content: function (config) {
                    if (!config.hasOwnProperty('id') || !config.hasOwnProperty('content')) return Dazzling.toast.warning('Tab must be related with an ID!');
                    var div = $('<div class="tab-pane fade" id="' + config['id'] + '"></div>');
                    var content = util.toJquery(config['content']);
                    div.append(content);
                    return div;
                },
                create: function (config, attachment) {
                    var nav = this._createNav(config);
                    var content = this._createContent(config);
                    if (attachment) {
                        attachment = util.toJquery(attachment);
                        attachment.html('');
                        attachment.append(nav).append(content);
                    }
                    return [nav, content];
                }
            }
        };
    })();
});


