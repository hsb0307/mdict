
// 全局变量，全部定义在这里
var packageCount = 500;
var warningDays = 10;

// 判断浏览器
var Sys = {};
var ua = navigator.userAgent.toLowerCase();
var s;
(s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
(s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
(s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
(s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
(s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;
// 使用：判断某种浏览器只需用if(Sys.ie)或if(Sys.firefox)等形式，
// 而判断浏览器版本只需用if(Sys.ie == '8.0')或if(Sys.firefox == '3.0')等形式

// 枚举数据 ==========================================
var enumerableData = function (){ 
	return {
		logCategories:[
		     		  {id:1, name:"登录"},
		     		  //{id:2, name:"注销"},
		     		  {id:3, name:"页面访问"},
		     		  {id:4, name:"数据操作"},
		     		  //{id:5, name:"已撤销"},
		     		  {id:900, name:"出错"},
		     		  {id:901, name:"执行SQL语句"}
		     		  //{id:999, name:"已删除"}
		     		],
		sysModules:[
{id:1, name:"用户管理"},
{id:2, name:"任务管理"},
{id:3, name:"词条管理"},
{id:4, name:"日志管理"},
{id:5, name:"系统管理"}
		            ],
		operations:[
{id:101, name:"用户注册"},
{id:102, name:"用户审核"},
{id:103, name:"登录"},
{id:104, name:"修改用户资料"},

{id:201, name:"建立录入数据包"},
{id:202, name:"提交录入数据包"},
{id:203, name:"撤销录入数据包"},

{id:204, name:"建立编辑数据包"},
{id:205, name:"提交编辑数据包"},
{id:206, name:"撤销编辑数据包"},

{id:207, name:"建立审定数据包"},
{id:208, name:"提交审定数据包"},
{id:209, name:"撤销审定数据包"},

{id:301, name:"录入时修改词条"},
{id:302, name:"编辑时修改词条"},
{id:303, name:"审定时修改词条"},

{id:304, name:"编辑时删除词条"},
{id:305, name:"审定时删除词条"},
{id:306, name:"编辑时添加词条"},
{id:307, name:"审定时添加词条"}
		            ],
		
		packageStatus:[
		  {id:0, name:"已分配"},
		  {id:1, name:"备用"},
		  {id:2, name:"已提交"},
		  {id:3, name:"备用"},
		  {id:4, name:"已撤销"},
		  {id:999, name:"已删除"}
		],
		wordStatus:[
			{id:0, name:"新建"},  
			{id:2, name:"录入中"},  
			{id:4, name:"录入完成"},  
			{id:6, name:"编辑中"},  
			{id:8, name:"编辑完成"},  
			{id:10, name:"审定中"},
			{id:12, name:"审定时新增"}, 
			{id:14, name:"审定时未认可"}, 
			{id:16, name:"审定时认可"},
			{id:18, name:"同意审定时新增"},
			{id:20, name:"审定完成"},
			{id:22, name:"已完成"},  
			{id:24, name:"已公布"},  
			{id:999, name:"已删除"}         
		],
		wordCategories:[
{"id":1,"name":"化学"},
{"id":2,"name":"物理学"},
{"id":3,"name":"数学"},
{"id":4,"name":"计算机"},

{"id":6,"name":"生物学"},
{"id":7,"name":"生态学"},
{"id":8,"name":"地理学"},

{"id":10,"name":"法律学"},

{"id":12,"name":"逻辑学"},
{"id":13,"name":"体育学"},

{"id":15,"name":"文学"},
{"id":16,"name":"新闻学"},
{"id":17,"name":"语言学"},

{"id":19,"name":"心理学"},
{"id":20,"name":"哲学"},
{"id":21,"name":"统计学"},
{"id":22,"name":"历史"},
{"id":23,"name":"公安"},
{"id":24,"name":"通信"},
{"id":25,"name":"金融"},
{"id":26,"name":"军事"},
{"id":91,"name":"综合"}],
		sourceDictionary:[
{id:0, name:"保留", author:"保留", publisher:"保留",year:"保留"},
{id:1, name:"化学名词术语", author:"杨巴雅尔", publisher:"内蒙古教育出版社",year:"2005"},
{id:2, name:"物理学名词术语", author:"仁钦苏荣", publisher:"内蒙古教育出版社",year:"2004"},
{id:3, name:"数学名词术语", author:"其其格", publisher:"内蒙古教育出版社",year:"2004"},
{id:4, name:"计算机科技名词术语", author:"布日古都", publisher:"内蒙古教育出版社",year:"2005"},
{id:5, name:"汉蒙计算机词典", author:"嘎日迪", publisher:"内蒙古人民出版社",year:"2001"},
{id:6, name:"生物学名词术语", author:"吉日木图", publisher:"内蒙古教育出版社",year:"2004"},
{id:7, name:"生态学名词术语", author:"萨日娜", publisher:"内蒙古教育出版社",year:"2006"},
{id:8, name:"地理学名词术语", author:"仁钦道尔吉", publisher:"内蒙古教育出版社",year:"2005"},
{id:9, name:"地理学名词术语", author:"松迪", publisher:"内蒙古教育出版社",year:"1988"},
{id:10, name:"法律学名词术语", author:"额尔和木", publisher:"内蒙古教育出版社",year:"2000"},
{id:11, name:"法律学名词术语", author:"松迪", publisher:"内蒙古教育出版社",year:"1986"},
{id:12, name:"逻辑学名词术语", author:"图·乌力吉", publisher:"内蒙古教育出版社",year:"2007"},
{id:13, name:"体育名词术语", author:"札米尔", publisher:"内蒙古教育出版社",year:"2006"},
{id:14, name:"体育名词术语", author:"松迪", publisher:"内蒙古教育出版社",year:"1987"},
{id:15, name:"文学名词术语 ", author:"松迪", publisher:"内蒙古教育出版社",year:"1989"},
{id:16, name:"新闻学名词术语", author:"巴干", publisher:"内蒙古教育出版社",year:"2005"},
{id:17, name:"语言学名词术语", author:"纳·官其格苏荣", publisher:"内蒙古教育出版社",year:"2005"},
{id:18, name:"语言学名词术语", author:"松迪", publisher:"内蒙古教育出版社",year:"1985"},
{id:19, name:"心理学名词术语", author:"七十三", publisher:"内蒙古教育出版社",year:"2005"},
{id:20, name:"哲学名词术语", author:"松迪", publisher:"内蒙古教育出版社",year:"1988"},
{id:21, name:"汉蒙统计词汇", author:"巴·布仁吉日格勒", publisher:"内蒙古人民出版社",year:"1997"},
{id:22, name:"历史名词术语", author:"松迪", publisher:"内蒙古教育出版社",year:"1985"},
{id:23, name:"公安名词术语", author:"那顺乌日图", publisher:"内蒙古大学出版社",year:"1997"},
{id:24, name:"汉蒙通信词汇", author:"权禧", publisher:"内蒙古人民出版社",year:"1999"},
{id:25, name:"汉英蒙金融词典", author:"金桩", publisher:"内蒙古人民出版社",year:"2008"},
{id:26, name:"汉蒙军事词汇", author:"舍·宝音涛克涛夫", publisher:"内蒙古人民出版社",year:"1990"},

{id:101, name:"社会征集", author:"", publisher:"",year:""},

{id:901, name:"出版社", author:"松迪", publisher:"内蒙古教育出版社",year:"1986"},
{id:902, name:"报社", author:"松迪", publisher:"内蒙古教育出版社",year:"1986"},
{id:903, name:"个人", author:"松迪", publisher:"内蒙古教育出版社",year:"1986"}
	                  ],
	                  dictionaryCategories:[
{"id":1001,"name":"法学"},

{"id":2001,"name":"军事"},

{"id":3001,"name":"医学"},
{"id":3002,"name":"中医"},
{"id":3003,"name":"医学英语"},

{"id":4001,"name":"口语英语"},
{"id":4002,"name":"口译英语"},
{"id":4003,"name":"体育"},
{"id":4004,"name":"广播"},

{"id":5001,"name":"专利"},
{"id":5002,"name":"交通"},
{"id":5003,"name":"信息学英语"},
{"id":5004,"name":"农业英语"},
{"id":5005,"name":"农牧林"},
{"id":5006,"name":"冶金"},
{"id":5007,"name":"化学"},
{"id":5008,"name":"化工"},
{"id":5009,"name":"地质"},
{"id":5010,"name":"建筑"},
{"id":5011,"name":"数学"},
{"id":5012,"name":"机械"},
{"id":5013,"name":"林业"},
{"id":5014,"name":"水利"},
{"id":5015,"name":"汽车"},
{"id":5016,"name":"物理"},
{"id":5017,"name":"环境"},
{"id":5018,"name":"电信"},
{"id":5019,"name":"电力"},
{"id":5020,"name":"电梯"},
{"id":5021,"name":"畜牧兽医"},
{"id":5022,"name":"石油"},
{"id":5023,"name":"纺织"},
{"id":5024,"name":"经贸"},
{"id":5025,"name":"能源"},
{"id":5026,"name":"航海"},
{"id":5027,"name":"航空"},
{"id":5028,"name":"船舶"},
{"id":5029,"name":"航道"},
{"id":5030,"name":"计算机"},
{"id":5031,"name":"通信网络"},
{"id":5032,"name":"造纸"}],	                  
		userRole:[
{id:0, name:"未使用"},
{id:1, name:"录入人"},
{id:2, name:"编辑人"},
{id:3, name:"审定人"},
{id:71, name:"词条征集人"},
{id:81, name:"录入管理员"},
{id:82, name:"编辑管理员"},
{id:83, name:"审定管理员"},
{id:91, name:"系统管理员"}
		],
		getName:function(id, array, isDict){
			var retValue = "";
			for(var i = 0, l = array.length; i < l; i++) {
			    //console.log(array[i]);
			    if(array[i].id == id){
			    	if(isDict){
			    		retValue = array[i].name + ".&nbsp;" + array[i].author + ".&nbsp;" + array[i].publisher+ ".&nbsp;" + array[i].year;
			    	}else {
			    		retValue = array[i].name;
			    	}
			    	
			    	break;
			    }
			}
			return retValue;
		},
		getId:function(name, array){
			var retValue = 1;
			for(var i = 0, l = array.length; i < l; i++) {
			    if(array[i].name == name){
			    	retValue = array[i].id;
			    	break;
			    }
			}
			return retValue;
		}
		
	};
}();
// jquery.cookie
(function(a){if(typeof define==='function'&&define.amd&&define.amd.jQuery){define(['jquery'],a)}else{a(jQuery)}}(function($){var m=/\+/g;function raw(s){return s}function decoded(s){return decodeURIComponent(s.replace(m,' '))}function converted(s){if(s.indexOf('"')===0){s=s.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,'\\')}try{return n.json?JSON.parse(s):s}catch(er){}}var n=$.cookie=function(a,b,c){if(b!==undefined){c=$.extend({},n.defaults,c);if(typeof c.expires==='number'){var d=c.expires,t=c.expires=new Date();t.setDate(t.getDate()+d)}b=n.json?JSON.stringify(b):String(b);return(document.cookie=[encodeURIComponent(a),'=',n.raw?b:encodeURIComponent(b),c.expires?'; expires='+c.expires.toUTCString():'',c.path?'; path='+c.path:'',c.domain?'; domain='+c.domain:'',c.secure?'; secure':''].join(''))}var e=n.raw?raw:decoded;var f=document.cookie.split('; ');var g=a?undefined:{};for(var i=0,l=f.length;i<l;i++){var h=f[i].split('=');var j=e(h.shift());var k=e(h.join('='));if(a&&a===j){g=converted(k);break}if(!a){g[j]=converted(k)}}return g};n.defaults={};$.removeCookie=function(a,b){if($.cookie(a)!==undefined){$.cookie(a,'',$.extend(b,{expires:-1}));return true}return false}}));
// 拉丁转蒙文
function AscToMsMongol(iKey)
{
  var iRtn;
  switch( iKey )
  {
	case 'A': iRtn = 'ᠠ';  break;
	case 'E': iRtn = 'ᠡ';  break;
	case 'e': iRtn = 'ᠧ';  break;
	case 'I':  iRtn = 'ᠢ';    break;
	case 'i':  iRtn = 'ᠢ᠍';    break;
	case '0':  iRtn = 'ᠣ';    	break;
	case 'V':  iRtn = 'ᠤ';    break;
	case 'O':  iRtn = 'ᠥ';    break;
	case 'U':  iRtn = 'ᠦ';    break;
	case 'N':  iRtn = 'ᠨ';    break;
	case 'B':  iRtn = 'ᠪ';    break;
	case 'P':  iRtn = 'ᠫ';    break;
	case 'M':  iRtn = 'ᠮ';    break;
	case 'H':  iRtn = 'ᠬ';    break;
	case 'G':  iRtn = 'ᠭ';    break;
	case 'L':  iRtn = 'ᠯ';    break;
	case 'S':  iRtn = 'ᠰ';    break;
	case '$':  iRtn = 'ᠱ';    break;
	case 'D':  iRtn = 'ᠳ';    break;
	case 'd':  iRtn ='ᠳ᠋'; break;
	case 'T':  iRtn = 'ᠲ';    break;
	case 't': iRtn = 'ᠲ᠋';  break;
	case 'C':  iRtn = 'ᠴ';    break;
	case 'J':  iRtn = 'ᠵ';    break;
	case 'Y':  iRtn = 'ᠶ';    break;
	case 'R':  iRtn = 'ᠷ';    break;
	case 'W':  iRtn = 'ᠸ';    break;
	case 'n':  iRtn = 'ᠩ';    break;
	case 'F':  iRtn = 'ᠹ';    break;
	case 'Z':  iRtn = 'ᠽ';    break;
	case 'z': iRtn = 'ᡁ';  break; 
	case 'c': iRtn = 'ᡂ';  break;
	case 'c':  iRtn = 'ᠼ';    break;
	case 'h':  iRtn = 'ᠾ';    break;
	case 'K':  iRtn = 'ᠺ';    break; 
	case 'r':  iRtn = 'ᠿ';    break;
	case '-':  iRtn = ' ';break;
	case '_':  iRtn = '᠎';    break;
	case ';':  iRtn = '᠋';    break;
	case '"':  iRtn = '᠌';    break;
	case "'":   iRtn='᠋᠋'; break;
	case '`':  iRtn = '᠍';    break;
	case '*':  iRtn = '‍';break;
case '%':  iRtn = '᠋';break;
	case '/':  iRtn = '‍';
	default:  iRtn = iKey;break;
  }
  return iRtn;
}  

function latinToMongolian(latin) {
	var mongolian ="";
	//var latin = document.getElementById("MongolianLatin").value;
	//var a = latin.split("");
	var length = latin.length ;
	if(length == 1) {
		mongolian = AscToMsMongol(latin.charAt(0));
	} else {
		
	var prev = null;
	for(var i = 0; i < length ; i++ ){
		//charCodeAt
		//fromCharCode
		//parseInt()
		var c = latin.charAt(i);
		
		var charCode=  c.charCodeAt(0);
		if(charCode > 47 && charCode < 58) {//如果阿拉伯数字：0-9
			if(prev){
				var prevCode = prev.charCodeAt(0);
				if(prevCode > 47 && prevCode < 58) {
					mongolian += c;
				} else {
					mongolian += AscToMsMongol(c);
				}
			} else {
				if(i < length - 1) {
					var nextCode = latin.charAt(i + 1).charCodeAt(0);
					if(nextCode > 47 && nextCode < 58) {
						mongolian += c;
						mongolian += latin.charAt(i + 1);
						i = i+ 1;
					} else {
						mongolian += AscToMsMongol(c);
					}
				} else {
					mongolian += AscToMsMongol(c);
				}
			}
		} else {
			mongolian += AscToMsMongol(c);
		}
		//mongolian += AscToMsMongol(latin.charAt(i));
		prev = c;
	}// end for
	}
	
	//alert(mongolian);

	//document.getElementById("txtMongolian").SetUnicodeText(mongolian);
	return mongolian;
}
// 表格分页功能
var pagination = function (){ 
	return {
		paging:function(options){
			var defaults = {
					target:"#pager",
					page:1,
					init:true,
					pageSize:10,
					onFill: function(){
			        	
			        },
			        onFormat: function (type) {
						switch (type) {
						case 'block':
							if (!this.active)
								return '<span class="disabled">' + this.value + '</span>';
							else if (this.value != this.page)
								return '<a href="javascript:void(0);' + this.value + '">' + this.value + '</a>';
							return '<span class="current">' + this.value + '</span>';
						case 'right':
						case 'left':
							if (!this.active) {
								return "";
							}
							return '<a href="javascript:void(0);' + this.value + '">' + this.value + '</a>';
						case 'next':
							if (this.active) {
								return '<a href="javascript:void(0);' + this.value + '" class="next">&raquo;</a>';
							}
							return '<span class="disabled">&raquo;</span>';
						case 'prev':
							if (this.active) {
								return '<a href="javascript:void(0);' + this.value + '" class="prev">&laquo;</a>';
							}
							return '<span class="disabled">&laquo;</span>';
						case 'first':
							if (this.active) {
								return '<a href="javascript:void(0);' + this.value + '" class="first">|<</a>';
							}
							return '<span class="disabled">|<</span>';
						case 'last':
							if (this.active) {
								return '<a href="javascript:void(0);' + this.value + '" class="prev">>|</a>';
							}
							return '<span class="disabled">>|</span>';
						
						case 'fill':
							if (this.active){
								return "...";
								//return '<span id="pagerInfo"> 当前第<span id="pagenumber"> ' + this.page + '</span>页，总共 ' + this.pages + '页，共' +this.number + '条记录</span>';
							}
							return "";
						case "leap":
							if (this.active){
								//return '<span id="pagerInfo"> 当前第<span id="pagenumber">1</span>/'+ this.number + '条记录 ,每页' + this.perpage + '条</span>';//  ' + this.page + '
								//return "...";
							}
							return "";
						}
						return "";
					}
			};
			
			function executePaging(response, args){
				if(!response.Count) response.Count = 0;
				$(args.target).paging(response.Count, {
					format: '. [ <   (qq -) nncnn (- pp) > ]',
					perpage:args.pageSize,
					lapping: 0,
					page: args.page, // we await hashchange() event
					onSelect: function (page) {
						args.page = page;
						if(!args.init){
							initData(args);
						}
						//console.info(page);
						args.init = false;
					},
					onFormat: args.onFormat
				});
			};//executePaging
			//getRows
			function getRows(response, args){
				var rows = response.Rows;
				var o = {count:response.Count, 
						countHandled:response.CountHandled,
						total:response.Total,
						otherData : response.OtherData,
						pageSize: response.Count > args.pageSize?args.pageSize:response.Count,
						startRowIndex: (args.page -1)*args.pageSize,
						first:response.First, 
						page:args.page };
				args.onFill(rows, o);		
			};// end getRows

			function initData(args){
				args.postData.PageSize = args.pageSize;
				var url = args.getDataUrl;
				if(url.indexOf("?") > 0) {
					url += "&page=" + args.page;
				} else {
					url += "?page=" + args.page;
				}
				jQuery.post(url, args.postData, function (data) {
					var response = eval("(" + data + ")");
					//args.page = page;
					//args.init = init;
					getRows(response, args);
					if(args.init){
						executePaging(response, args);
					}
					
				});
			};//
			
			var opts =  $.extend({}, defaults, options || {});
			
			initData(opts);
		}
	}
}();
// 读取查询字符串
function getQueryStringByName(name){
    var result = location.search.match(new RegExp("[\?\&]" + name+ "=([^\&]+)","i"));
    if(result == null || result.length < 1){
        return "";
    }
    return result[1];
}
//验证
;Array.prototype.remove=function(dx)
{
　//if(isNaN(dx)||dx>this.length){return false;}
 if(this.length==0){return false;}
　for(var i=0,n=0;i<this.length;i++)
　{
　　　if(this[i]!=this[dx])
　　　{
　　　　　this[n++]=this[i]
　　　}
　}
　this.length-=1
};
function showTip(id, status, msg){
    var el = $(id), parent = el.parent(), node = el[0], nodes = showTip.nodes;
    node =el.next();
    if(node) node = node[0];
    if(node.nodeName !== "SPAN") node = $(node).next()[0];
    switch(status){
        case 0 :
            //parent.parent.removeClass("okey");// error
            parent.parent().addClass("error");// error
            if(node){
                node.innerHTML = msg
                if($.inArray(nodes, node) == -1){//用去统计当前页面有多少个验证没有被通过
                    nodes.push(node);                    
                }
            }
            break;
        case 1:
            //parent.addClass("okey");
            parent.parent().removeClass("error");// 
            if(node){
                node.innerHTML = "";
                nodes.remove(node);                
                //Array.remove(nodes, node);
            }
            break
        case 2:
            //parent.removeClass("okey");
            parent.parent().removeClass("error");
            if(node){
                node.innerHTML = "";
                nodes.remove(node);
                showTip.count--;
                //Array.remove(nodes, node);
            }
            break
    }
}
showTip.nodes = [];
showTip.count = 0;
function validate(root, name, obj, checktype){
    checktype = checktype || "blur"
    $(root).delegate(name, checktype, function(){
        var ok = true
        for(var msg in obj){
        	var b = obj[ msg ](this);
            if(!b){
                showTip(name, 0 , msg );//失败了就显示红色的错误提示
                ok = false;
                break;
            }
        }
        if(ok){
            showTip(this, 1);//显示成功提示，绿色的勾号
        }
    } ).delegate(name, "focus",function(){
    	if($("#UserId").length == 0 &&  $(this.parentNode.parentNode).hasClass("error")){
    		//this.value = "";
    	}
    	
        showTip(this, 2);//隐藏所有提示！
    })
};
//获取时间间隔
function dateDiff(startTime, endTime, diffType) {
    //将xxxx-xx-xx的时间格式，转换为 xxxx/xx/xx的格式 
	if(typeof(startTime) === 'string'){
		startTime = startTime.replace(/\-/g, "/");
	}
	if(typeof(endTime) === 'string'){
		endTime = endTime.replace(/\-/g, "/");
	}
	var sTime = new Date();      //开始时间
    var eTime = new Date();  //结束时间
	
	if( !((typeof startTime=='object') && startTime.constructor==Date)){
		sTime = new Date(startTime);      //开始时间
	}
	if( !((typeof endTime=='object') && endTime.constructor==Date)){
	    eTime = new Date(endTime);  //结束时间
	}
    //将计算间隔类性字符转换为小写
    diffType = diffType.toLowerCase();
    
    //作为除数的数字
    var divNum = 1;
    switch (diffType) {
        case "second":
            divNum = 1000;
            break;
        case "minute":
            divNum = 1000 * 60;
            break;
        case "hour":
            divNum = 1000 * 3600;
            break;
        case "day":
            divNum = 1000 * 3600 * 24;
            break;
        default:
            break;
    }
    return parseInt((eTime.getTime() - sTime.getTime()) / parseInt(divNum));
}
