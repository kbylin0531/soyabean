/**
 * Created by lnzhv on 7/7/16.
 */
var dazz = (function () {

    var addon = {
        nav:{
            target:null,
            load:function (data) {
                if(!this.target) this.target = $("#addon_nav");
                soya.utils.each(data,function (item) {
                    this.target.append();
                });
            }
        }
    };

    return {
        addon:addon
    };
})();