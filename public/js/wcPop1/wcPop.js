/**
	* @title 		弹窗插件【仿微信】wcPop-v1.0 beta (UTF-8)
	* @Create		hison
	* @Timer		2018/03/30 11:30:45 GMT+0800 (中国标准时间)
	* @bolg			https://www.cnblogs.com/xiaoyan2017   Q：282310962  wx：xy190310
*/

!function(f){var g=f.document,d=g.documentElement,c=0,a={$:function(h){return g.getElementById(h)},touch:function(j,i,h){j.addEventListener(i||"click",function(k){h.call(this,k)},!1)},jspath:function(){for(var j=g.getElementsByTagName("script"),h=j.length;h>0;h--){if(j[h-1].src&&j[h-1].src.match(/wcPop[\w\-\.]*\.js/)!=null){return j[h-1].src.substring(0,j[h-1].src.lastIndexOf("/")+1)}}},extend:function(k,j){for(var h in j){if(!(h in k)){k[h]=j[h]}}return k},timer:{},show:{},end:{},direction:function(i,h,k,j){return Math.abs(i-h)>=Math.abs(k-j)?(i-h>0?"left":"right"):(k-j>0?"up":"down")},swipe:function(i,h){a.touch(i,"touchstart",function(o){var m,l,k,j,n;m=Math.floor(o.touches[0].pageX);l=Math.floor(o.touches[0].pageY);a.touch(i,"touchmove",function(p){p.preventDefault();k=p.changedTouches[0].pageX;j=p.changedTouches[0].pageY});a.touch(i,"touchend",function(r){if((k&&Math.abs(m-k)>50)||(j&&Math.abs(l-j)>50)){n=a.direction(m,k,l,j)}for(var q=0,p=h.swipe.length;q<p;q++){if(h.swipe[q].direction==n){typeof h.swipe[q].fn==="function"&&h.swipe[q].fn(r)}}m=l=null;i.removeEventListener("touchmove",this,false)})})},chkPosition:function(l,i,h,m,k,o){var j=(l+h)>k?(l-h):l;var n=(i+m)>o?(i-m):i;return[j,n]}},e=function(i){var j=this,h={id:"wcPop",title:"",content:"",style:"",skin:"",icon:"",shade:true,shadeClose:true,opacity:"",xclose:false,anim:"scaleIn",position:"",follow:null,time:0,zIndex:9999,swipe:null,btns:null};j.opts=a.extend(i,h);j.init()};e.prototype={init:function(){var k=this,h=k.opts,j=null,i=function(){if(!h.btns){return}var m="";for(var l in h.btns){m+='<span class="btn" data-index="'+l+'" style="'+(h.btns[l].style?h.btns[l].style:"")+'">'+h.btns[l].text+"</span>"}return m}();a.$(h.id)?(j=a.$(h.id)):(j=g.createElement("div"),j.id=h.id);h.skin&&(j.setAttribute("type",h.skin));j.setAttribute("index",c);j.setAttribute("class","wcPop wcPop"+c);j.innerHTML=['<div class="popui__modal-panel">',h.shade?('<div class="popui__modal-mask" style="'+(h.opacity!=null?"opacity:"+h.opacity:"")+"; z-index:"+(k.maxIndex()+1)+'"></div>'):"",'<div class="popui__panel-main" style="z-index:'+(k.maxIndex()+2)+'">						<div class="popui__panel-section">							<div class="popui__panel-child '+(h.anim?"anim-"+h.anim:"")+" "+(h.skin?"popui__"+h.skin:"")+" "+(h.position?h.position:"")+'" style="'+h.style+'">',h.title?('<div class="popui__panel-tit">'+h.title+"</div>"):"",h.content?('<div class="popui__panel-cnt">'+(h.skin=="toast"&&h.icon?('<div class="popui__toast-icon"><img class="'+(h.icon=="loading"?"anim-loading":"")+'" src="'+a.jspath()+"skin/"+h.icon+'.png" /></div>'):"")+h.content+"</div>"):"",h.btns?'<div class="popui__panel-btn">'+i+"</div>":"",h.xclose?('<span class="popui__xclose"></span>'):"","</div>						</div>					</div>				</div>"].join("");g.body.appendChild(j);g.getElementsByTagName("body")[0].classList.add("popui__overflow");h.show&&h.show.call(this);this.index=c++;k.callback()},callback:function(){var r=this,h=r.opts,v=a.$(h.id);if(h.time){a.timer[r.index]=setTimeout(function(){b.close(r.index)},h.time*1000)}if(h.btns){for(var k=v.getElementsByClassName("popui__panel-btn")[0].children,s=k.length,q=0;q<s;q++){a.touch(k[q],"click",function(o){var i=this.getAttribute("data-index"),j=h.btns[i];typeof j.onTap==="function"&&j.onTap(o)})}}if(h.shade&&h.shadeClose){var w=v.getElementsByClassName("popui__modal-mask")[0];a.touch(w,"click",function(){b.close(r.index)})}if(h.xclose){var z=v.getElementsByClassName("popui__xclose")[0];a.touch(z,"click",function(){b.close(r.index)})}var p=v.getElementsByClassName("popui__panel-btn")[0];var q=v.getElementsByClassName("popui__modal-mask")[0];p&&p.addEventListener("contextmenu",function(i){i.preventDefault()},!1);q&&q.addEventListener("contextmenu",function(i){i.preventDefault()},!1);if(h.follow){var u=v.getElementsByClassName("popui__panel-child")[0];var t,l,y,n;t=u.clientWidth;l=u.clientHeight;y=window.innerWidth;n=window.innerHeight;console.log("dom宽度："+t);console.log("dom高度："+l);console.log("屏幕宽度："+y);console.log("屏幕高度："+n);var A=a.chkPosition(h.follow[0],h.follow[1],t,l,y,n);console.log(A);u.style.left=A[0]+"px";u.style.top=A[1]+"px"}var m=v.getElementsByClassName("popui__panel-main")[0];h.swipe&&a.swipe(m,h);h.end&&(a.end[r.index]=h.end)},maxIndex:function(){for(var j=this.opts.zIndex,l=g.getElementsByTagName("*"),k=0,h=l.length;k<h;k++){j=Math.max(j,l[k].style.zIndex)}return j}};var b=(function(){fn=function(h){var i=new e(h);return i.index};fn.close=function(h){var h=h||"";var i=g.getElementsByClassName("wcPop"+h)[0];if(i){i.setAttribute("class","wcPop-close");setTimeout(function(){g.body.removeChild(i);clearTimeout(a.timer[h]);delete a.timer[h];typeof a.end[h||0]=="function"&&a.end[h||0].call(this);delete a.end[h||0]},200);g.getElementsByTagName("body")[0].classList.remove("popui__overflow")}};fn.closeAll=function(){for(var k=g.getElementsByClassName(["wcPop"]),j=0,h=k.length;j<h;j++){fn.close(0|k[0].getAttribute("index"))}};fn.load=function(k){for(var h=g.createElement("link"),l=g.getElementsByTagName("link"),j=l.length;
j>0;j--){if(l[j-1].href==k){return}}h.type="text/css";h.rel="stylesheet";h.href=a.jspath()+k;g.getElementsByTagName("head")[0].appendChild(h)};fn.moreAPI=function(k,i,j){var h={title:k,content:i,time:j};fn(h)};return fn}());b.load("skin/wcPop.css");f.wcPop=b}(window);