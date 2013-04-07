$(function () {
    var recordCount = 0;
    var pageSize = 20;
    var pageObj;
    var first;
    var currentPage;
    var currentEntry = 0;
    var operator = "q";
    var role = "";
    if ($.browser.msie) {
        document.getElementById('txtMongolian').SetFontSize(24);
        document.getElementById('txtMongolian').SetMWFontName("Mongolian Baiti");
    } else {
        document.getElementById('txtMongolian').style.display = "none";
    }

    /*user*/
    $.ajax({
        url: "GetUserInfo.php",
        type: "POST",
        dataType: 'json',
        data: {},
        async: false,
        success: function (d) {
            $("#userInfo").html("欢迎您，" + d.userName + "<b class='caret'></b>").attr("role", d.userRole);
            role = d.userRole;
            var content = "";
            if (d.userRole == 1) {
                content = "录入人:" + d.userName
            } else if (d.userRole == 2) {
                content = "编辑人:" + d.userName
            } else if (d.userRole == 3) {
                content = "审核人:" + d.userName
            }
            $(".icon-user").popover({
                title: "人员信息",
                content: content,
                trigger: "hover",
                placement: "bottom"

            })
        }
    });

    $.post("_editwordmanage.php?op=expire", {"Days":warningDays},function(data){
       if(data.count != 0){
    	   $(".alert").alert();
		   $(".alert").show();
       }
     }, "json");

    /*ko object*/
    function wordItemModel() {
        var self = this;
        self.entrys = ko.observableArray();
        self.wordItem = ko.observable(); //ko.observableArray();
        self.wordQuery = ko.observable(false);
        self.wordChinese = ko.observable(false);        
        //self.words = ko.observableArray([{"name":"Alpha"}, {"name":"Beta"},{"name":"Gamma"}]);        
        //console.info(enumerableData.wordCategories);
        self.wordCategories = ko.observableArray(enumerableData.wordCategories);        
        
        self.loadentrys = function () {
            $.ajax({
                url: "_editwordmanage.php?op=top",
                type: "POST",
                dataType: 'json',
                data: {},
                success: function (data) {
                    self.entrys = data;
                    self.wordItem(self.entrys[0]);
                }
            })
        };

        self.loadWordItem = function (WordId) {
            $.ajax({
                url: "_editwordmanage.php?op=get",
                type: "POST",
                dataType: 'json',
                data: { "WordId": WordId },
                success: function (data) {                	
                	if(data.SourceDictionary){
                		data.SourceDictionary = enumerableData.getName(data.SourceDictionary,enumerableData.sourceDictionary,true);               		              		
                	}                	
//            		if(data.Pinyin == "null" || !data.Pinyin){
//            			data.Pinyin = "";
//            		}
//            		if(data.MongolianCyrillic == "null" || !data.MongolianCyrillic){
//            			data.MongolianCyrillic = "";
//            		}
            		            		
            		$.each(data,function(i,n){
            			if(!n || n=="null"){data[i] = "";}            			
            		})           		
                	
            		data.Mongolian = $.trim(data.Mongolian);
                    if ($.browser.msie) {                        
                        if ((data.Mongolian == "null" || !data.Mongolian) && data.MongolianLatin) {
                            //document.getElementById("txtMongolian").SetUnicodeText(latinToMongolian(data.MongolianLatin));
                            data.Mongolian = latinToMongolian(data.MongolianLatin);
                        }
                    } else {
                        if ((data.Mongolian == "null" || !data.Mongolian) && data.MongolianLatin) {
                            //$("#MONGOLIANWord").val(latinToMongolian(data.MongolianLatin));                        	
                            data.Mongolian = latinToMongolian(data.MongolianLatin);
                        }
                    }
                    self.wordItem(data);
                    if ($.browser.msie) {
                    	document.getElementById("txtMongolian").SetUnicodeText($("#MONGOLIANWord").val())
                    }
                    $("#words li").removeClass("wordcolor");
                    $("#" + data.WordId).addClass("wordcolor");

                    var m = $('#MONGOLIANWord');
                    var o = m.offset();
                    if (o.left != $('#txtMongolian').offset().left) {
                    	//var b = $('#txtMongolian').css("left");
                        $('#txtMongolian').css("top", m.offset().top).css("left", m.offset().left);
                        //var a = $('#txtMongolian').css("left");
                    };                   
                    
                    if (data.Japanese == "null" || !data.Japanese) {
                        jQuery.post("_wordcontroller.php?op=jp&q=" + encodeURIComponent(document.getElementById("Chinese").value), {}, function (d) {
                            if (!d) return;
                            //alert(d.charCodeAt(0));
                            if (d.length < 40 && d.charCodeAt(0) == 9) return;
                            var result = eval("(" + d + ")");
                            //console.info(result);
                            document.getElementById("Japanese").value = result.trans_result[0].dst;
                            data.Japanese = result.trans_result[0].dst;
                        });
                    }  
                    
                    $("#txtSearch").val(data.Chinese);
                    $("#aSearch").attr("href","selectword.php?w="+encodeURIComponent($("#txtSearch").val()));
                    var wordId = $("#WordId").text();
                    SetPageInfo($("#" + wordId).attr("record"));
                }
            });
        };

        self.addWordItem = function (data) {
            //console.info(data);
        }

        self.updateWordItem = function (data) {
            data.DataPackageId = $("#dataPackageList option:selected").val();
            if ($.browser.msie) {
                data.Mongolian = document.getElementById("txtMongolian").GetUnicodeText();
            }
            if (wordItemModel.wordChinese) {
                wordItemModel.wordChinese(false);
            }

            if (data.WordId == "") {
                $.ajax({
                    url: "_editwordmanage.php?op=add",
                    type: "POST",
                    dataType: 'json',
                    data: data,
                    success: function (data) {
                        if (data.success) {
                            operator = "a";
                            getWordList(pagination.pageObj.opts.page);
                            self.wordItem(data);                            
                            $(".alert-success").show();
                            setTimeout(function () {
                                $(".alert-success").hide();
                            }, 3000);
                        } else {
                            alert(data.msg);
                        }

                    }
                });
            } else {
                $.ajax({
                    url: "_editwordmanage.php?op=update",
                    type: "POST",
                    dataType: 'json',
                    data: data,
                    success: function (data) {
                        if (data.success) {
                            operator = "u";
                            //console.info(pagination.pageObj);
                            //getWordList(pagination.pageObj.opts.page); 
                            //$("#" + data.WORDID).addClass("wordcolor");
                            self.wordItem(data);
                            SetProgress(data.Count, data.EditCount);
                            $(".alert-success").show();
                            setTimeout(function () {
                                $(".alert-success").hide();
                            }, 3000);
                            //$("#words li").removeClass("wordcolor");
                        } else {
                            alert(data.msg);
                        }
                    }
                });
            }
        };
    }
    var wordItemModel = new wordItemModel();
    ko.applyBindings(wordItemModel);

    $("#dataPackageList").change(function () {
    	$("#txtSearch").val("");
        operator = "q";
        getWordList();
        if ($("#dataPackageList option:selected").val() == "all") {
            wordItemModel.wordQuery(false);
        } else {
            wordItemModel.wordQuery(true);
        }
    });

    /*datapackage*/
    $.ajax({
        url: "getDataPackageByUser.php",
        type: "POST",
        dataType: 'json',
        data: {},
        async: false,
        success: function (d) {
            var current = $("#currentPackage").text();
            //console.info(current);
            var packageId = getQueryStringByName("packageid");
            //console.info(packageId);
            if (packageId) {
                current = packageId;
            }
            $("#dataPackageList").empty();
            $("<option></option>").val("all").text("全部").appendTo("#dataPackageList");
            $.each(d, function (i, n) {
                $("<option></option>").val(this.PACKAGEID).text(this.PACKAGENAME).appendTo("#dataPackageList");
            });

            var i;
            if (current && current != undefined) {
                $("#dataPackageList option").each(function (index) {
                    if (this.value == current) {
                        i = index;
                        if (!$.browser.msie || ($.browser.msie && $.browser.version != "6.0")) {
                            $(this).attr("selected", "selected");
                        }
                        //wordItemModel.wordQuery(true);
                    }
                })
            }

            if ($.browser.msie && $.browser.version == "6.0") {
                setTimeout(function () {
                    $("#dataPackageList option").eq(i).attr("selected", true);
                    $("#dataPackageList").trigger("change");
                }, 1);

            } else {
                $("#dataPackageList").trigger("change");
            }
        }
    });



    /*取词条列表*/
    function getWordList(page) {
        var fields = { "DataPackageId": $("#dataPackageList option:selected").val(), "txtSearch": $("#txtSearch").val() };
        var opts = {
            cls: "ypager",
            getDataUrl: "_get_user_dicta_paginiation.php",
            init: true,
            page: page,
            perpage: pageSize,
            postData: fields,
            onFill: function (rows, o) {
                if (rows.length > 0) {
                    var html = "";
                    $(rows).each(function (i) {
                        if (i == 0 && (operator == "q" || operator == "d" || operator == "a")) {
                            first = this.WordId
                            wordItemModel.loadWordItem(first);
                            if ($.browser.msie) {
                                document.getElementById("txtMongolian").SetUnicodeText($("#MONGOLIANWord").val());
                            }
                        }
                        var headWord = "";
                        if (this.Chinese && this.Chinese.length > 76) {
                            headWord = this.Chinese.substr(0, 78) + "...";
                        } else {
                            headWord = this.Chinese;
                        }

                        var record = o.startRowIndex + i + 1;
                        recordCount = o.count;
                        if (role != 1 && $("#dataPackageList option:selected").val() != "all") {
                            html += "<li id='" + this.WordId + "' tag='" + this.WordId + "' record = '" + record + "'><span>" + headWord + "</span><a href='javascript:void(0)' id='" + this.WordId + "'><i class='icon-remove'></i></a></li>";
                        } else {
                            html += "<li id='" + this.WordId + "' tag='" + this.WordId + "' record = '" + record + "'><span>" + headWord + "</span></li>";
                        }
                    })

                    var tableBody = $("#words");
                    tableBody.html();
                    $("#words").html(html);

                    $("#pagination a").each(function () {
                        $(this).live('click', function () {
                            operator = "q";
                        })
                    })

                    $("#words a").each(function () {
                        $(this).live('click', function () {
                            var id = $(this)[0].id;
                            if (window.confirm("是否删除?")) {
                                $.ajax({
                                    url: "_editwordmanage.php?op=delete",
                                    type: "POST",
                                    dataType: 'json',
                                    data: { "WordId": id },
                                    success: function (data) {
                                        if (data.success) {
                                            alert(data.msg);
                                            operator = "d";
                                            getWordList();
                                        } else {
                                            alert(data.msg);
                                        }
                                    }
                                });
                            }
                        })
                    })

                    $("#words div").each(function () {
                        $(this).hover(function () {
                            $(this).children().eq(1).css("display", "inline-block").css("cursor", "hand");
                        }, function () {
                            $(this).children().eq(1).css("display", "none").css("cursor", "hand");
                        })
                    });

                    /*
                    if (operator == "a") {
                    var wordId = $("#WordId").text();                        
                    SetPageInfo($("#" + wordId).attr("record"));
                    }
                    */
                }
            },
            onSetProgress: function (d) {

                var id = $("#WordId").text();
                //console.info(id);
                if (id != undefined && id != "") {
                    $("#" + id).addClass("wordcolor");
                }

                SetProgress(d.recordCount, d.ecount);

            }
        }
        pageObj = pagination.paging(opts);
    }

    //getWordList();

    /*bind event*/
    $("#words li").live('click', function () {
        if (wordItemModel.wordChinese) {
            wordItemModel.wordChinese(false);
        }
        wordItemModel.loadWordItem(this.id);
    });

    $("#convert").live('click',function(){    	
    	var convert = latinToMongolian($("#MongolianLatin").val());
    	if ($.browser.msie) {    		
    		document.getElementById("txtMongolian").SetUnicodeText(convert);    		
    	} 
    	var obj = wordItemModel.wordItem()
		obj.Mongolian = convert;
		wordItemModel.wordItem(obj);   
    });
    
    $("#btnSearch").click(function () {
        getWordList();
    });
    $("#txtSearch").blur(function () {
    	$("#aSearch").attr("href","selectword.php?w="+encodeURIComponent($("#txtSearch").val()));
    });   

    $("#add").click(function () {
        wordItemModel.wordItem({
            "WordId": "",
            "Chinese": "",
            "Pinyin": "",
            "English": "",
            "Mongolian": "",
            "MongolianLatin": "",
            "MongolianCyrillic": "",
            "Japanese": "",
            "WordCategory":100
        });
        wordItemModel.wordChinese(true);
        if ($.browser.msie) {
            document.getElementById("txtMongolian").SetUnicodeText("");
        }
    })

    $("#history").click(function () {
        wordItemModel.loadentrys();
        currentEntry = 0;
    })

    $("#left").click(function () {
        if (wordItemModel.wordChinese) {
            wordItemModel.wordChinese(false);
        }
        if (wordItemModel.entrys.length == 0) {
            $("#history").trigger("click");
        }
        if (currentEntry > 0) {
            currentEntry--;
            if (currentEntry < 0) { currentEntry = 0; }
            wordItemModel.wordItem(wordItemModel.entrys[currentEntry]);
            if ($.browser.msie) {
                document.getElementById("txtMongolian").SetUnicodeText($("#MONGOLIANWord").val());
            }
        }
    })

    $("#right").click(function () {
        if (wordItemModel.wordChinese) {
            wordItemModel.wordChinese(false);
        }
        if (wordItemModel.entrys.length == 0) {
            $("#history").trigger("click");
        }
        if (currentEntry < 10) {
            currentEntry++;
            if (currentEntry > 9) { currentEntry = 9; }
            wordItemModel.wordItem(wordItemModel.entrys[currentEntry]);
            if ($.browser.msie) {
                document.getElementById("txtMongolian").SetUnicodeText($("#MONGOLIANWord").val());
            }
        }
    })

    $("#btnUp").live('click', function () {
        var i = $("#words .wordcolor").attr("record");
        if (--i > 0) {
            var id = $("#words [record='" + i + "']").attr("id");
            if (id != undefined) {
                wordItemModel.loadWordItem(id);
            }
        }
    })

    $("#btnDown").live('click', function () {
        var i = $("#words .wordcolor").attr("record");
        if (++i <= recordCount) {
            var id = $("#words [record='" + i + "']").attr("id");
            if (id != undefined) {
                wordItemModel.loadWordItem(id);
            }
        }
    })

    function SetProgress(count, ecount) {
        var percent = count == 0 ? 0 : Math.round(ecount / count * 100);
        percent = percent > 100 ? 100 : percent;
        $(".bar").attr("style", "width: " + percent + "%;").html(percent + "%");
    }

    function SetPageInfo(currentRecord) {
        $("#pageinfo").html("当前第" + currentRecord + "/" + recordCount + "条记录  每页" + pageSize + "条")
    }

});