(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["components/zhuige-swiper"],{2797:function(t,e,n){"use strict";n.r(e);var i=n("510e"),u=n("f2d7");for(var r in u)["default"].indexOf(r)<0&&function(t){n.d(e,t,(function(){return u[t]}))}(r);n("8428");var a=n("828b"),f=Object(a["a"])(u["default"],i["b"],i["c"],!1,null,null,null,!1,i["a"],void 0);e["default"]=f.exports},"439d":function(t,e,n){},"510e":function(t,e,n){"use strict";n.d(e,"b",(function(){return i})),n.d(e,"c",(function(){return u})),n.d(e,"a",(function(){}));var i=function(){var t=this.$createElement;this._self._c},u=[]},6786:function(t,e,n){"use strict";(function(t){var i=n("47a9");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var u=i(n("7aea")),r={name:"zhuige-swiper",props:{type:{type:String,default:""},items:{type:Array,default:[]},previousMargin:{type:String,default:"0rpx"},nextMargin:{type:String,default:"0rpx"}},data:function(){return{}},methods:{clickItem:function(e){if(this.items[e].link)u.default.openLink(this.items[e].link);else{var n=[];this.items.forEach((function(t){n.push(t.image)})),t.previewImage({current:e,urls:n})}}}};e.default=r}).call(this,n("df3c")["default"])},8428:function(t,e,n){"use strict";var i=n("439d"),u=n.n(i);u.a},f2d7:function(t,e,n){"use strict";n.r(e);var i=n("6786"),u=n.n(i);for(var r in i)["default"].indexOf(r)<0&&function(t){n.d(e,t,(function(){return i[t]}))}(r);e["default"]=u.a}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'components/zhuige-swiper-create-component',
    {
        'components/zhuige-swiper-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('df3c')['createComponent'](__webpack_require__("2797"))
        })
    },
    [['components/zhuige-swiper-create-component']]
]);
