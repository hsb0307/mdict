var pagination = function (){ 
	return {
		pageObj:undefined,
		paging:function(options){
			var opts;			
			var defaults = {
					format: '. [ <   (qq -) nncnn (- pp) > ]',
					page:1,
					init:true,					
					onFill: function(){			        	
			        },
			        onSetProgress:function(o){			        	
			        },
			        onSelect:function(page){			        	
						opts.page = page;					
						if(!opts.init){
							initData(opts);
						};
						opts.init = false;
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
			
			function executePaging(data, opts){				
				var o = {recordCount:data.Count,ecount:data.EditCount}
				opts.onSetProgress.call(null,o);				
				pagination.pageObj = opts.target.paging(data.Count, opts);				
			};	
			
			function getRows(data, opts){
				var rows = data.Rows;				
				var o = {count:data.Count, startRowIndex: (opts.page -1)*opts.perpage, page:opts.page };
				opts.onFill.call(null,rows, o);		
			};

			function initData(opts){
				opts.postData.PageSize = opts.perpage;
				var url = opts.getDataUrl;
				if(url.indexOf("?") > 0) {
					url += "&page=" + opts.page;
				} else {
					url += "?page=" + opts.page;
				}
				
				jQuery.ajax({
                    url: url,
                    type: "POST",
                    dataType: 'json',
                    data: opts.postData,
                    //async:false,
                    success: function (data) {                    				
    					getRows(data, opts);
    					if(opts.init){
    						executePaging(data, opts);					
    					}                       
                    }
                });				
			};
			
			opts =  $.extend({}, defaults, options || {});			
			if (opts.id) {
				opts.target = $('#' + opts.id);
            } else {
            	opts.target = $('.' + opts.cls);
            }
			initData(opts);			
		}
	}
}();

(function ($) {
	$.ajaxSetup({
        type: "POST",
        error: function (XMLHttpRequest, textStatus, errorThrown) {/* 扩展AJAX出现错误的提示 */ 
        	//console.info(XMLHttpRequest.responseText);
            alert(XMLHttpRequest.responseText);            
        }
    });
})(jQuery);