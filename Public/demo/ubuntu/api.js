(function(){var d,f=this,k=function(a,c,b){a=a.split(".");b=b||f;a[0]in b||!b.execScript||b.execScript("var "+a[0]);for(var e;a.length&&(e=a.shift());)a.length||void 0===c?b=b[e]?b[e]:b[e]={}:b[e]=c},l=Date.now||function(){return+new Date};var p=function(a){this.D=a||{cookie:""}},r=/\s*;\s*/;d=p.prototype;d.isEnabled=function(){return navigator.cookieEnabled};d.aa=function(a){return!/[;=\s]/.test(a)};d.ba=function(a){return!/[;\r\n]/.test(a)};
d.set=function(a,c,b,e,g,h){if(!this.aa(a))throw Error('Invalid cookie name "'+a+'"');if(!this.ba(c))throw Error('Invalid cookie value "'+c+'"');void 0!==b||(b=-1);g=g?";domain="+g:"";e=e?";path="+e:"";h=h?";secure":"";b=0>b?"":0==b?";expires="+(new Date(1970,1,1)).toUTCString():";expires="+(new Date(l()+1E3*b)).toUTCString();this.ha(a+"="+c+g+e+b+h)};d.V=function(){return this.H().keys};d.Y=function(){return this.H().values};d.ha=function(a){this.D.cookie=a};
d.W=function(){return(this.D.cookie||"").split(r)};d.H=function(){for(var a=this.W(),c=[],b=[],e,g,h=0;g=a[h];h++)e=g.indexOf("="),-1==e?(c.push(""),b.push(g)):(c.push(g.substring(0,e)),b.push(g.substring(e+1)));return{keys:c,values:b}};var u={},v=function(a,c,b){this.b=a;this.J=c;this.C=b;this.u=Math.floor((new Date).getTime()/1E3);this.m=8035200;this.i=void 0};v.prototype.ka=function(){return this.b+"$"+this.J+":"+this.C};v.prototype.la=function(){var a=this.b+"$"+this.J+":"+this.u+":"+this.m;"string"==typeof this.i&&(a=a.concat(":",this.i));return a};v.prototype.ja=function(a,c,b){this.u=a;this.m=c;this.i=b};v.prototype.$=function(){return(new Date).getTime()>(new Date(1E3*(this.u+this.m))).getTime()};
var y=function(a){var c={},b=new p(document);if(!b.isEnabled())return c;var e=b.V(),b=b.Y();if(null==e||null==b)return c;for(var g=0;g<e.length;g++)if("__utmx"==e[g])for(var h=x(b[g],a),n=0;n<h.length;n++){var m=/([^$]+)\$([^:]+):(.*)/.exec(h[n]);null!==m&&4==m.length&&(m=new v(m[1],m[2],m[3]),c[m.b]=m)}g={};for(h=0;h<e.length;h++)if("__utmxx"==e[h])for(n=x(b[h],a),m=0;m<n.length;m++){var q=/([^$]+)\$([^:]+):([^:]+):([^:]+):?(.*)/.exec(n[m]);if(null!==q&&5<=q.length){var w=q[1],t=c[w];"undefined"!=
typeof t&&(t.ja(parseInt(q[3],10),parseInt(q[4],10),6==q.length?q[5]:void 0),t.$()||(g[w]=t))}}return g},x=function(a,c){var b=a.split(".");return"string"==typeof c&&0<b.length&&b[0]!=c?[]:b.slice(1)};var z=function(a,c,b,e,g){this.b=g||z.DEFAULT_ID;this.a=a;this.g=c;this.h=b;this.f=e;this.L=!1};z.experiments_={"IbIACoOrTnG8eSnRY64nWQ":{"api_version":"V1","method":"experiments.get","error":{"code":404,"message":"Experiment not available"}}};z.DEFAULT_ID="IbIACoOrTnG8eSnRY64nWQ";d=z.prototype;d.s=function(a){if(!this.l()&&!this.I()){var c=new p(document);if(A(c.isEnabled(),"Unable to write cookies")){var b=this.v(a),e=this.G(),g=y(e);g[this.b]=new v(this.b,"0",b);var b=e,h;for(h in g)var n=g[h],b=b.concat(".",n.ka()),e=e.concat(".",n.la());c.set("__utmx",b,this.f,this.g,this.j());c.set("__utmxx",e,this.f,this.g,this.j());-2!=a[0]&&this.M(a)}}};
d.M=function(a){window.gaData||(window.gaData={});window.gaData.expId=this.b;window.gaData.expVar=this.v(a)};d.j=function(){if("string"==typeof this.a&&""!=this.a&&"none"!=this.a&&"auto"!=this.a)return this.a;if("none"==this.a)return null;var a=""+document.domain;return 0==a.indexOf("www.")?a.substring(4):a};
d.G=function(){if(!this.h)return"1";var a=this.j();if(a){var c=1,b=0,e;if(a)for(c=0,e=a.length-1;0<=e;e--)b=a.charCodeAt(e),c=(c<<6&268435455)+b+(b<<14),b=c&266338304,c=0!=b?c^b>>21:c;a=String(c)}else a="1";return a};d.v=function(a){for(var c=0;c<a.length;c++)if(-2==a[c])return"";return a.join("-")};d.o=function(a){if(0==a.length)return-2;a=a.split("-");for(var c=[],b=0;b<a.length;b++)c[b]=parseInt(a[b],10);return c};var A=function(a,c){!a&&c&&console&&console.error&&console.error(c);return a};
d=z.prototype;d.l=function(){var a=window._gaUserPrefs;return a&&a.ioo&&a.ioo()};d.K=function(){if(!this.L){var a=z.experiments_[this.b];this.L=!0;if(!A("object"==typeof a,"Could not load experiment")||a.error)return!1;this.c=a.data}return A("object"===typeof this.c,"Could not load experiment")};d.F=function(){if(this.l())return-2;var a=this.I();if(a)return a;a=y(this.G())[this.b];if(!a)return-1;a=this.o(a.C);-2!=a&&this.M(a);return a};
d.R=function(){if(this.l()||!this.K())return 0;var a=this.F();if(-2==a||this.Z(a))return 0;if(-1!==a)return a;if(!this.X())return this.s([-2]),0;a=this.ca();this.s(a);return a};d.X=function(a){return("number"==typeof a?a:Math.random())<this.c.participation};
d.ca=function(a){A("undefined"===typeof _gaq,"Variations should be chosen before hit is sent to GA");a="number"==typeof a?a:Math.random();for(var c=0;c<this.c.items.length;c++){var b=this.c.items[c];if(a<b.weight)return this.o(b.id);a-=b.weight}A(!1,"The combinations weights did not add up to 1");return[0]};d.U=function(a){return this.K()&&A(0<=a&&a<this.c.items.length)?this.c.items[a]:null};
d.I=function(){var a=/#utmxid=[-_a-zA-Z0-9]{22};utmxpreview=(\d{1,2});/.exec(window.location.hash);return a?(a=this.U(parseInt(a[1],10)),this.o(a.id)):null};d.Z=function(a){if("number"===typeof a)return!1;a=this.v(a);for(var c=0;c<this.c.items.length;c++){var b=this.c.items[c];if(b.id==a)return b.disabled}A(!1,"Unable to locate combination with id "+a);return!0};u=u||{};u.P=0;k("cxApi.ORIGINAL_VARIATION",u.P,void 0);u.O=-1;k("cxApi.NO_CHOSEN_VARIATION",u.O,void 0);u.N=-2;k("cxApi.NOT_PARTICIPATING",u.N,void 0);u.A="auto";k("cxApi.DEFAULT_DOMAIN",u.A,void 0);u.B=48211200;k("cxApi.DEFAULT_EXPIRATION_SECONDS",u.B,void 0);u.w="/";k("cxApi.DEFAULT_COOKIE_PATH",u.w,void 0);u.a=u.A;u.g=u.w;u.h=!0;u.f=u.B;u.ea=function(a,c){A("string"==typeof c||"undefined"==typeof c,"Invalid experiment id: "+c)&&(new z(u.a,u.g,u.h,u.f,c)).s([a])};
k("cxApi.setChosenVariation",u.ea,void 0);u.ia=function(a){A("string"==typeof a,"Invalid domain name: "+a)&&(u.a=a)};k("cxApi.setDomainName",u.ia,void 0);u.ga=function(a){A("string"==typeof a,"Invalid cookie path: "+a)&&(u.g=a)};k("cxApi.setCookiePath",u.ga,void 0);u.da=function(a){A("boolean"==typeof a,"Invalid value for allowHash: "+typeof a)&&(u.h=a)};k("cxApi.setAllowHash",u.da,void 0);u.fa=function(a){A("number"==typeof a,"Invalid cookieExpirationSeconds: "+a)&&(u.f=a)};
k("cxApi.setCookieExpirationSeconds",u.fa,void 0);u.S=function(a){A("string"==typeof a||"undefined"==typeof a,"Invalid experiment id: "+a);a=(new z(u.a,u.g,u.h,u.f,a)).R();return"number"===typeof a?a:a[0]};k("cxApi.chooseVariation",u.S,void 0);u.T=function(a){A("string"==typeof a||"undefined"==typeof a,"Invalid experiment id: "+a);a=(new z(u.a,u.g,u.h,u.f,a)).F();return"number"===typeof a?a:a[0]};k("cxApi.getChosenVariation",u.T,void 0);})();
