var LY = function(){
	return {
		Page:function(option){
			var defaultOpts = {
					format: '[ <  . (qq -) nncnn (- pp) > ]',// '- [ < (qq -) ncnnnnnn (- pp) >]',// // '[ <  nnnnncnnnn - cn > ]',
					perpage: 10,
					lapping: 0,
					page: null, // we await hashchange() event
					onSelect: function (page) {						
					},
					onRefresh: function(json){						
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
							if (this.active) {
								return "...";
							};
							/*	*/
							/*
						case "leap":
							if (this.active)
								return "...";
							return "";

						case 'fill':
							if (this.active){
								//if(this.pos > 1)	return '...';
								//return "function (data) {if (data.pos > 1)return '...';return 'PAGE ' + data.page + ' OF ' + data.pages;}";
								return '<span id="pagerInfo"> 当前第<span id="pagenumber"> ' + this.page + '</span>页，总共 ' + this.pages + '页，共' +this.number + '条记录</span>';
							}
							return "";	
							*/
						}
						return "";
					}
			}
			var opts = $.extend({},defaultOpts,option || {});
			var target;
			if (opts.id) {
                target = $('#' + opts.id);
            } else {
                target = $('.' + opts.cls);
            }
			return target.paging(opts.count,opts);
			
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

