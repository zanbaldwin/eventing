/**
 * Head JS
 * @author    Tero Piirainen  <http://cloudpanic.com/about.html>
 * @copyright Tero Piirainen  <http://headjs.com>
 * @license   MIT/X11         <http://j.mp/mit-license>
 * @version   0.8
 */
(function(w){var d=w.documentElement,l=navigator.userAgent.toLowerCase().indexOf("msie")!=-1,j=false,n=[],m={},a={},g=w.createElement("script").async===true||"MozAppearance" in w.documentElement.style||window.opera;var v=window.head_conf&&head_conf.head||"head",k=window[v]=(window[v]||function(){k.ready.apply(null,arguments)});var o=0,u=1,r=2,c=3;if(g){k.js=function(){var x=arguments,z=x[x.length-1],y=[];if(!e(z)){z=null}b(x,function(B,A){if(B!=z){B=t(B);y.push(B);f(B,z&&A==x.length-2?function(){if(q(y)){z()}}:null)}});return k}}else{k.js=function(){var x=arguments,z=[].slice.call(x,1),y=z[0];if(!j){n.push(function(){k.js.apply(null,x)});return k}if(y){b(z,function(A){if(!e(A)){i(t(A))}});f(t(x[0]),e(y)?y:function(){k.js.apply(null,z)})}else{f(t(x[0]))}return k}}k.ready=function(z,A){if(e(z)){A=z;z="ALL"}var y=a[z];if(y&&y.state==c||z=="ALL"&&q()){A();return k}var x=m[z];if(!x){x=m[z]=[A]}else{x.push(A)}return k};function p(y){var A=y.split("/"),x=A[A.length-1],z=x.indexOf("?");return z!=-1?x.substring(0,z):x}function t(z){var x;if(typeof z=="object"){for(var A in z){if(z[A]){x={name:A,url:z[A]}}}}else{x={name:p(z),url:z}}var B=a[x.name];if(B){return B}for(var y in a){if(a[y].url==x.url){return a[y]}}a[x.name]=x;return x}function b(x,z){if(!x){return}if(typeof x=="object"){x=[].slice.call(x)}for(var y=0;y<x.length;y++){z.call(x,x[y],y)}}function e(x){return Object.prototype.toString.call(x)=="[object Function]"}function q(y){y=y||a;for(var x in y){if(y[x].state!=c){return false}}return true}function s(x){x.state=o;b(x.onpreload,function(y){y.call()})}function i(x,y){if(!x.state){x.state=u;x.onpreload=[];h({src:x.url,type:"cache"},function(){s(x)})}}function f(x,y){if(x.state==c&&y){return y()}if(x.state==r){return k.ready(x.name,y)}if(x.state==u){return x.onpreload.push(function(){f(x,y)})}x.state=r;h(x.url,function(){x.state=c;if(y){y()}b(m[x.name],function(z){z()});if(q()){b(m.ALL,function(z){if(!z.done){z()}z.done=true})}})}function h(y,z){var x=w.createElement("script");x.type="text/"+(y.type||"javascript");x.src=y.src||y;x.async=false;x.onreadystatechange=x.onload=function(){var A=x.readyState;if(!z.done&&(!A||/loaded|complete/.test(A))){z();z.done=true}};d.appendChild(x)}setTimeout(function(){j=true;b(n,function(x){x()})},300);if(!w.readyState&&w.addEventListener){w.readyState="loading";w.addEventListener("DOMContentLoaded",handler=function(){w.removeEventListener("DOMContentLoaded",handler,false);w.readyState="complete"},false)}})(document);
/**
 * HeadCatchJS
 *
 * @author    Alexander Baldwin, Nerv Interactive <http://nerv.co.uk/>.
 * @copyright 2011 nerv Interactive <http://nerv.co.uk>
 * @license   MIT/X11 <http://bit.ly/mit-license>
 * @link      http://github.com/mynameiszanders/headcatch
 * @version   1.0 (first public release)
 */
(function(a){if(typeof a=="function"&&window.s&&s instanceof Array){var b=document.createElement("a"),c="/test";b.href=c,b.href==c?window.ie&&ie instanceof Array&&s.push.apply(s,ie):window.x&&x instanceof Array&&s.push.apply(s,x);var d=s.slice(0);typeof f=="function"&&d.push(f),a.apply(null,d)}})(head.js)
