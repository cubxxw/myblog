(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-38004ba1"],{"29dd":function(t,e,a){"use strict";var n=a("5b20"),i=a("e8e0"),s=a("3a32"),o=a("f50a"),r="toString",c=RegExp.prototype,l=c[r],d=s((function(){return"/a/b"!=l.call({source:"a",flags:"b"})})),u=l.name!=r;(d||u)&&n(RegExp.prototype,r,(function(){var t=i(this),e=String(t.source),a=t.flags,n=String(void 0===a&&t instanceof RegExp&&!("flags"in c)?o.call(t):a);return"/"+e+"/"+n}),{unsafe:!0})},"46d5":function(t,e,a){"use strict";var n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"wbs-title-format"},[a("table",{staticClass:"wbs-form-table"},[a("tbody",[a("tr",[a("th",{staticClass:"row w8em"},[t._v("标题")]),a("td",[a("div",{staticClass:"input-with-count"},[a("wbs-title-format",{attrs:{cnf:{dropdownItems:t.cnf.titleVariables},opt:t.tdk[0]},on:{change:t.title_handler}}),a("span",{staticClass:"count"})],1),a("p",{staticClass:"description"},[t._v("一般不超过30个中文字符。")])])]),a("tr",[a("th",[t._v("关键词")]),a("td",[t.cnf.kwMode?a("div",{staticClass:"input-with-count"},[a("wbs-kw-format",{attrs:{cnf:{dropdownItems:t.cnf.titleVariables,variableOnly:!0},opt:t.tdk[1]},on:{change:t.kw_handler}})],1):a("label",{staticClass:"input-with-count wb-tags-module"},[a("wbs-tags-module",{attrs:{tags:t.tdk[1]},on:{"set-tags":t.kw_cpt}})],1),a("p",{staticClass:"description"},[t._v("建议3-5个为宜，切勿进行关键词堆叠。")])])]),a("tr",[a("th",[t._v("描述")]),a("td",[a("div",{staticClass:"input-with-count"},[a("wbs-description-format",{staticClass:"wbs-input-textarea",attrs:{cnf:{dropdownItems:t.cnf.titleVariables},opt:t.tdk[2]},on:{change:t.desc_handler}})],1),a("p",{staticClass:"description"},[t._v("不超100个中文字符，建议50-100字为宜。")])])]),""!=t.tdk[0]||""!=t.tdk[2]?a("tr",[a("th",[t._v("预览")]),a("td",[a("div",{staticClass:"preview-box"},[a("div",{staticClass:"pvb-display"},[a("a",{staticClass:"preview-title"},[t._v(t._s(t.title_preview))]),a("p",{staticClass:"preview-desc"},[t._v(t._s(t.desc_preview))]),a("p",{staticClass:"preview-link"},[t._v(t._s(t.home_url("")))])]),a("div",{staticClass:"pvb-ft"},[t._v("* 搜索引擎收录结果展示预览")])])])]):t._e()])])])},i=[],s=(a("63ba"),a("a336"),a("9065"),a("71d0"),a("86be"),a("29dd"),a("9cda")),o=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("label",{staticClass:"wb-tags-ctrl"},[a("div",{staticClass:"tag-items"},t._l(t.taglist,(function(e,n){return t.taglist.length?a("div",{staticClass:"tag-item"},[a("span",{staticClass:"vam ib",domProps:{innerHTML:t._s(t.displayFormat(e))}}),a("a",{staticClass:"del",attrs:{"data-del-val":e},on:{click:function(e){return t.delItem(n)}}})]):t._e()})),0),a("input",{directives:[{name:"model",rawName:"v-model",value:t.cur_input,expression:"cur_input"}],staticClass:"wb-tag-input",attrs:{type:"text",placeholder:"输入选项值, 回车确认 *"},domProps:{value:t.cur_input},on:{keyup:t.wbTagInput,input:function(e){e.target.composing||(t.cur_input=e.target.value)}}})])},r=[],c=(a("2cde"),a("b170"),a("a08b"),a("5938"),{name:"wbsTags",props:{tags:String,format:String},data:function(){return{cur_input:"",taglist:[],keycode:null}},created:function(){this.taglist=this.tags?this.tags.split(","):[]},methods:{wbTagInput:function(t){var e=this,a=t.keyCode;13===a&&e.wbTagPush()},wbTagPush:function(t){var e=this;return""!=e.cur_input&&(e.taglist.indexOf(e.cur_input.trim())>-1?(wbui.toast("已存在"),e.cur_input="",!1):(e.taglist.push(e.cur_input),e.$emit("set-tags",e.taglist.join(",")),e.cur_input="",!1))},delItem:function(t){var e=this;wbui.confirm("确认删除该选项？",(function(){e.taglist.splice(t,1),e.$emit("set-tags",e.taglist.join(",")),wbui.toast("已移除")}))},displayFormat:function(t){var e=t.split(":");return e.length>1?e[1]+'<em class="wk">(别名:'+e[0]+")</em>":t}},computed:{}}),l=c,d=a("5d22"),u=Object(d["a"])(l,o,r,!1,null,null,null),p=u.exports,_={name:"TdkSetter",props:{opt:{type:Array,value:[]},cnf:{type:Object,value:{separator:"",titleVariables:{},kwMode:0}}},components:{"wbs-title-format":s["a"],"wbs-kw-format":s["a"],"wbs-description-format":s["a"],"wbs-tags-module":p},data:function(){var t=this;return{tdk:t.opt||["","",""],title_preview:"",desc_preview:"",variables_keys_title:Object.keys(t.cnf.titleVariables)}},computed:{},created:function(){},mounted:function(){var t=this;t.title_preview=t.preview_handler(t.opt[0],t.cnf.titleVariables),t.desc_preview=t.preview_handler(t.opt[2],t.cnf.titleVariables)},methods:{title_handler:function(t){var e=this;e.tdk[0]=t,e.title_preview=e.preview_handler(t,e.cnf.titleVariables),e.$emit("change",e.tdk)},kw_handler:function(t){var e=this;t=t.replace(/(%\w+%)/g,",$1,"),t=t.replace(/,,/g,","),t=t.replace(/^,/g,""),t=t.replace(/,$/gi,""),e.tdk[1]=t,e.$emit("change",e.tdk)},kw_cpt:function(t){var e=this;e.tdk[1]=t,e.$emit("change",e.tdk)},desc_handler:function(t){var e=this;e.tdk[2]=t,e.desc_preview=e.preview_handler(t,e.cnf.titleVariables),e.$emit("change",e.tdk)},preview_handler:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"",e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},a=this,n=Object.keys(e);return""==t?"":(n.forEach((function(a){if(t.indexOf(a)<0)return!1;t=t.replace(new RegExp(a,"g")," "+e[a]+" ")})),t=t.replace(new RegExp("分隔符","g"),a.cnf.separator),t=t.replace(new RegExp("站点标题","g"),a.$cnf.site_info.site_title),t=t.replace(new RegExp("站点副标题","g"),a.$cnf.site_info.site_subtitle),t)},home_url:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"",e=this;return e.$cnf.home_url+t}},watch:{opt:{handler:function(t){var e=this;e.tdk=t}}}},f=_,v=Object(d["a"])(f,n,i,!1,null,null,null);e["a"]=v.exports},6006:function(t,e,a){"use strict";a.r(e);var n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"wbs-content-inner"},[a("div",{staticClass:"wbs-main"},[t.is_loaded?a("div",[t._m(0),a("table",{staticClass:"wbs-form-table"},[a("tbody",[a("tr",[a("th",{staticClass:"row w8em"},[t._v("索引")]),a("td",{staticClass:"selector-bar"},[a("label",[a("input",{directives:[{name:"model",rawName:"v-model",value:t.search_noindex,expression:"search_noindex"}],attrs:{type:"radio",value:"0"},domProps:{checked:t._q(t.search_noindex,"0")},on:{click:function(e){return t.set_noindex(e,"search")},change:function(e){t.search_noindex="0"}}}),t._v(" 索引")]),a("label",[a("input",{directives:[{name:"model",rawName:"v-model",value:t.search_noindex,expression:"search_noindex"}],attrs:{type:"radio",value:"1"},domProps:{checked:t._q(t.search_noindex,"1")},on:{click:function(e){return t.set_noindex(e,"search")},change:function(e){t.search_noindex="1"}}}),t._v(" 不索引")])])])])]),a("wbs-tdk-setter",{attrs:{cnf:{separator:t.opt.separator,titleVariables:t.title_variables.search,kwMode:1},opt:t.opt.search},on:{change:function(e){t.opt.search=e}}}),t._m(1),a("table",{staticClass:"wbs-form-table"},[a("tbody",[a("tr",[a("th",{staticClass:"row w8em"},[t._v("索引")]),a("td",{staticClass:"selector-bar"},[a("label",[a("input",{directives:[{name:"model",rawName:"v-model",value:t.tag_noindex,expression:"tag_noindex"}],attrs:{type:"radio",value:"0"},domProps:{checked:t._q(t.tag_noindex,"0")},on:{click:function(e){return t.set_noindex(e,"tag")},change:function(e){t.tag_noindex="0"}}}),t._v(" 索引")]),a("label",[a("input",{directives:[{name:"model",rawName:"v-model",value:t.tag_noindex,expression:"tag_noindex"}],attrs:{type:"radio",value:"1"},domProps:{checked:t._q(t.tag_noindex,"1")},on:{click:function(e){return t.set_noindex(e,"tag")},change:function(e){t.tag_noindex="1"}}}),t._v(" 不索引")])])])])]),a("wbs-tdk-setter",{attrs:{cnf:{separator:t.opt.separator,titleVariables:t.title_variables.tag,kwMode:1},opt:t.opt.tag},on:{change:function(e){t.opt.tag=e}}}),t._m(2),a("table",{staticClass:"wbs-form-table"},[a("tbody",[a("tr",[a("th",{staticClass:"row w8em"},[t._v("索引")]),a("td",{staticClass:"selector-bar"},[a("label",[a("input",{directives:[{name:"model",rawName:"v-model",value:t.author_noindex,expression:"author_noindex"}],attrs:{type:"radio",value:"0"},domProps:{checked:t._q(t.author_noindex,"0")},on:{click:function(e){return t.set_noindex(e,"author")},change:function(e){t.author_noindex="0"}}}),t._v(" 索引")]),a("label",[a("input",{directives:[{name:"model",rawName:"v-model",value:t.author_noindex,expression:"author_noindex"}],attrs:{type:"radio",value:"1"},domProps:{checked:t._q(t.author_noindex,"1")},on:{click:function(e){return t.set_noindex(e,"author")},change:function(e){t.author_noindex="1"}}}),t._v(" 不索引")])])])])]),a("wbs-tdk-setter",{attrs:{cnf:{separator:t.opt.separator,titleVariables:t.title_variables.author,kwMode:1},opt:t.opt.author},on:{change:function(e){t.opt.author=e}}})],1):t._e(),a("wbs-var-doc")],1),t.is_pro?t._e():a("wbs-more-sources"),a("wbs-ctrl-bar",{on:{submit:t.update_data}})],1)},i=[function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("h3",{staticClass:"sc-title-sub"},[a("span",[t._v("搜索结果页")])])},function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("h3",{staticClass:"sc-title-sub"},[a("span",[t._v("标签列表")])])},function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("h3",{staticClass:"sc-title-sub"},[a("span",[t._v("作者归档")])])}],s=(a("5938"),a("365c")),o=a("46d5"),r=a("6bd9"),c={name:"TdkMore",components:{"wbs-tdk-setter":o["a"],"wbs-var-doc":r["a"]},data:function(){var t=this;return{form_changed:0,is_loaded:0,is_pro:t.$cnf.is_pro,opt:{},search_noindex:0,tag_noindex:0,author_noindex:0,separator:[],title_variables:{}}},created:function(){var t=this;t.get_data()},methods:{set_noindex:function(t,e){var a=this,n=a.opt.noindex.indexOf(e),i=1*t.target.value;i&&n<0?a.opt.noindex.push(e):!i&&n>-1&&a.opt.noindex.splice(n,1)},get_data:function(){var t=this;Object(s["b"])({action:t.$cnf.action.act,op:"get_options",key:"tdk",type:"tag,search,author"}).then((function(e){var a=e.data;t.opt=a.opt,t.opt.noindex?(t.opt.noindex.indexOf("search")>-1&&(t.search_noindex=1),t.opt.noindex.indexOf("tag")>-1&&(t.tag_noindex=1),t.opt.noindex.indexOf("author")>-1&&(t.author_noindex=1)):t.opt.noindex=[],t.separator=a.separator,t.title_variables=a.title_variables,t.is_loaded=1}))},update_data:function(t){var e=this;Object(s["c"])({action:e.$cnf.action.act,op:"update_options",opt:e.opt,key:"tdk",type:"more"}).then((function(a){wbui.toast("设置保存成功"),e.form_changed=1,t&&t()}))}},computed:{},watch:{opt:{handler:function(){this.form_changed++},deep:!0}},beforeRouteLeave:function(t,e,a){var n=this;n.form_changed>1?wbui.open({content:"您修改的设置尚未保存，确定离开此页面吗？",btn:["保存并离开","放弃修改"],yes:function(){return a(!1),n.update_data((function(){a()})),!1},no:function(){return a(),!1}}):a()}},l=c,d=a("5d22"),u=Object(d["a"])(l,n,i,!1,null,null,null);e["default"]=u.exports},"6bd9":function(t,e,a){"use strict";var n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"mt log-box"},[a("table",{staticClass:"wbs-form-table"},[a("tbody",[a("tr",[a("th",{staticClass:"row w8em"}),a("td",[a("el-button",{attrs:{size:"medium",type:"primary",plain:"",icon:"el-icon-collection"},on:{click:function(e){t.unfold=!t.unfold}}},[t._v("变量注释")]),a("table",{directives:[{name:"show",rawName:"v-show",value:t.unfold,expression:"unfold"}],staticClass:"wbs-table wbs-table-doc table-hover mt"},[t._m(0),a("tbody",t._l(t.doc_items,(function(e){return a("tr",[a("td",[a("b",[t._v(t._s(e.name))])]),a("td",{domProps:{innerHTML:t._s(e.desc)}})])})),0)])],1)])])])])},i=[function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("thead",[a("tr",[a("td",[t._v("变量名称")]),a("td",[t._v("变量说明")])])])}],s=a("365c"),o={name:"comVariableDoc",data:function(){return{doc_items:[],unfold:!1}},created:function(){var t=this,e=localStorage.getItem("WB_SST_DOC");e=e?JSON.parse(e):null,e&&e.ver==t.$cnf.pd_version?t.doc_items=e.data:Object(s["b"])({action:t.$cnf.action.act,op:"doc"}).then((function(e){t.doc_items=e["data"],localStorage.setItem("WB_SST_DOC",JSON.stringify({ver:t.$cnf.pd_version,data:e["data"]}))}))}},r=o,c=a("5d22"),l=Object(c["a"])(r,n,i,!1,null,null,null);e["a"]=l.exports},"86be":function(t,e,a){var n=a("14b8"),i=a("cc50"),s=a("af0d"),o=a("aeec"),r=a("15b7").f,c=a("d7eb").f,l=a("7e76"),d=a("f50a"),u=a("d0da"),p=a("5b20"),_=a("3a32"),f=a("4183").set,v=a("bac2"),h=a("0ebe"),b=h("match"),m=i.RegExp,g=m.prototype,w=/a/g,x=/a/g,k=new m(w)!==w,y=u.UNSUPPORTED_Y,C=n&&s("RegExp",!k||y||_((function(){return x[b]=!1,m(w)!=w||m(x)==x||"/a/i"!=m(w,"i")})));if(C){var $=function(t,e){var a,n=this instanceof $,i=l(t),s=void 0===e;if(!n&&i&&t.constructor===$&&s)return t;k?i&&!s&&(t=t.source):t instanceof $&&(s&&(e=d.call(t)),t=t.source),y&&(a=!!e&&e.indexOf("y")>-1,a&&(e=e.replace(/y/g,"")));var r=o(k?new m(t,e):m(t,e),n?this:g,$);return y&&a&&f(r,{sticky:a}),r},O=function(t){t in $||r($,t,{configurable:!0,get:function(){return m[t]},set:function(e){m[t]=e}})},E=c(m),j=0;while(E.length>j)O(E[j++]);g.constructor=$,$.prototype=g,p(i,"RegExp",$)}v("RegExp")},a08b:function(t,e,a){"use strict";var n=a("bde0"),i=a("bd65"),s=a("52fa"),o=a("1201"),r=[].join,c=i!=Object,l=o("join",",");n({target:"Array",proto:!0,forced:c||!l},{join:function(t){return r.call(s(this),void 0===t?",":t)}})},aeec:function(t,e,a){var n=a("b0f9"),i=a("eaf1");t.exports=function(t,e,a){var s,o;return i&&"function"==typeof(s=e.constructor)&&s!==a&&n(o=s.prototype)&&o!==a.prototype&&i(t,o),t}}}]);