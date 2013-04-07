<?php
session_start();
if(!isset($_SESSION["UserId"]))
{
	header('Location: ../../login.htm?returnurl='.$_SERVER['REQUEST_URI']);
	die();
	exit();
}

$Category = $_SESSION['WordCategory'];

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>录入数据包管理</title>
<?php include("../../css.php"); ?>

<style type="text/css">
#userinfo {
	position: relative;
}

#userinfo ul {
	position: absolute;
	top: 30px;
	padding-right: 40px;
}

.progress{
	margin-bottom:0px;
}

.navbar-form {
    margin-bottom: 2px;
}
</style>
</head>
<body>
<?php include("../../header.php"); ?>
<div class="container">
<!-- 		<div class="nav pull-left"> -->
		<!-- 
			<form action="assigneditpackage.php?op=create" method="post">
			
				<input type="submit" value="申请数据包">
			</form> -->
			
			<form class="navbar-form pull-left">
			选择您要编辑的词条类别：<select id="categories" name="categories" style="width:100px"></select>
			数据包名称：<input type="text" id="txtPackage" value="" maxlength="127">
			 申请条数：<input type="text" id="txtApplyCount" value="500" style="width:100px">
			<input type="button" id="btnPackage" value="申请数据包" class="btn" ><!-- style="margin-top:-10px" -->
			可分配词条：<span id="applyMsg">0</span>
			<span id="Category" style="display:none"><?php echo $Category; ?></span>
			</form>
<!-- 		</div>		 -->
		<table id="packagerows"	class="table table-bordered table-striped table-hover">
		
		<!--  模板方式
			<thead>
				<tr>
					<th>数据包名</th>
					<th>用户序号</th>
					<th>用户帐号</th>
					<th>姓名</th>
					<th>状态</th>
					<th>日期</th>
					<th>进度</th>					
					<th>操作</th>
				</tr>
			</thead>
			
			<tbody
				data-bind="template: { name: 'package-template', foreach: packages }">
			</tbody>
			<script type="text/html" id="package-template">
  				<tr>
				<td data-bind="text: PackageName,css:{'text-error':Warning}" style='width:150px;'></td>
				<td data-bind="text: UserId" style='width:80px;'>
				<td data-bind="text: Username" style='width:60px;'>
				<td data-bind="text: RealName" style='width:60px;'>
				<td data-bind="text: Status" style='width:60px;'></td>
				<td data-bind="text: CreateDate" style='width:60px;'></td>
				<td style='width:200px;'><div class="progress" id="progress"><div class="bar" data-bind="text:Progress+'%',style : { width :Progress+'%' }" ></div></div></td>
				<td style='width:60px;'><a data-bind="attr: { href: url}" >详情</a>&nbsp;&nbsp;<a href='javascript:void(0);' data-bind="visible: enable,click: Progress>0?$root.updated:$root.cancel,text:Progress>0?'提交':'撤销'" ></a></td>
				</tr>	
    		</script>-->
					
    		<thead >
    			<tr data-bind="foreach:headers">
    				<th data-bind="style: {width:width}" style="text-align:center;">
    					<a href="#" data-bind="text: displayText, click: $root.sort,style:{display:value == ''?'none':'inline'}"></a>
    					<i class="icon-circle-arrow-up" data-bind="visible: value == $root.orderBy() && $root.isAsc()"> </i>
                        <i class="icon-circle-arrow-down" data-bind="visible: value == $root.orderBy() && !$root.isAsc()" ></i> 
    					<span data-bind="text: displayText,style:{display:value != ''?'none':'inline'}"></span>    					 
    				</th>
    			</tr>
    		</thead>
    		<tbody data-bind="foreach:packages">
    			<tr>
				<td data-bind="text: PackageName,css:{'text-error':Warning}"></td>
				<!--  <td data-bind="text: UserId" style='width:80px;'>-->
				<td data-bind="text: RealName">
				<!--<td data-bind="text: RealName" style='width:60px;'>-->
				<td data-bind="text: Status"></td>
				<td data-bind="text: CreateDate"></td>
				<td><div class="progress" id="progress"><div class="bar" data-bind="text:Progress+'%',style : { width : Progress+'%' }" ></div></div></td>
				<td><a data-bind="attr: { href: url}" >详情</a>&nbsp;&nbsp;<a href='javascript:void(0);' data-bind="visible: enable,click: Progress>0?$root.updated:$root.cancel,text:Progress>0?'提交':'撤销'" ></a></td>
				</tr>
    		</tbody>
		</table>
		<div id="bottomPager">
			<div class="ypager" id="pager"></div>
		</div>
	</div>

<?php include("../../footer.php"); ?>
<script type="text/javascript" src="../../js/jquery.paging.js"></script>
<script type="text/javascript" src="../../js/knockout-2.0.0.js"></script>
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>
<script type="text/javascript" src="../../js/util.js"></script>
<script type="text/javascript">	
$(function () {
	/*KO */
	function viewModel() {
        var self = this;        
        self.packages = ko.observableArray();
        self.headers = ko.observableArray([
            {displayText: "数据包名", value: "e.PackageName", width: "150px"},
            {displayText: "姓名", value: "u.RealName", width: "60px"},
            {displayText: "状态", value: "e.Status", width: "60px"},
            {displayText: "日期", value: "e.CreateDate", width: "100px"},
            {displayText: "进度", value: "", width: "200px"},
            {displayText: "操作", value: "", width: "60px"}
        ]);
        self.orderBy = ko.observable();
        self.isAsc = ko.observable();
        self.defaultOrderBy = "CreateDate";
        self.sort = function (header) {
            if (self.orderBy() == header.value) {
                self.isAsc(!self.isAsc());
            }else{
            	self.isAsc(false);
            }
            self.orderBy(header.value);
            opts.page = pagination.pageObj.opts.page;            
            opts.postData={"UserId":<?php echo $_SESSION ['UserId']; ?>,"orderBy":self.orderBy(),"isAsc":self.isAsc()},
            pagination.paging(opts);            
        };        
        self.loadData = function (data) {
        	$(data).each(function(){            	 
        		if(this.Status === "0" &&  (dateDiff(this.CreateDate, (new Date()), 'day') > warningDays) ){
        			this.PackageName = this.PackageName+"(超期)";
        			this.Warning = true;
        		}else{
        			this.Warning = false;
            	}
        		this.Progress = this.Status != 0?100:this.UTOTAL == 0?0:Math.round(this.UTOTAL/this.TOTAL*100);  
            	if(this.Status == 0){
            		this.Status = "已分配";
            		this.enable = true;
                }else if(this.Status == 2){
                	this.Status = "已提交";
                	this.enable = false;
                }else{
                	this.Status = "已撤销";
                	this.enable = false;
                }            	
            	this.url =  "../word/editword.php?packageid="+this.PackageId              
            }) 
            //console.info(data);          
             self.packages(data);                        
        };
        self.updated = function(data){
           var undo = data.TOTAL - data.UTOTAL;//未处理词条数
           var r = true;           
           if(undo > 0){
        	   r = confirm("本数据包共" + data.TOTAL + "个词条，尚有" + undo + "个词条未处理，您确实要提交吗？" );
           }
           if(r){        	   
        	   $.ajax({
                   url: "assigneditpackage.php?op=update",
                   type: "POST",
                   dataType: 'json',
                   data: {"packageId":data.PackageId,"packageName":data.PackageName},
                   success: function (data) {                       
                       if (data.success != undefined) {
                           alert(data.msg);
                       } 
                       //console.info(pagination.pageObj.opts.page);
                       opts.page = pagination.pageObj.opts.page;
                       pagination.paging(opts);
                   }
               });
           }         
        }; 
        self.cancel = function(data){
            var r = confirm("您确实要撤销录入数据包 吗？" );
            if(r){
            	$.ajax({
                    url: "assigneditpackage.php?op=cancel",
                    type: "POST",
                    dataType: 'json',
                    data: {"packageId":data.PackageId,"packageName":data.PackageName},
                    success: function (data) {                       
                        if (data.success != undefined) {
                            alert(data.msg);
                        } 
                        //console.info(pagination.pageObj.opts.page);
                        opts.page = pagination.pageObj.opts.page;
                        pagination.paging(opts);
                        //console.info(pagination.pageObj.opts.page);
                    }
                });
            }
        }        
    };    
    var viewModel = new viewModel();
	ko.applyBindings(viewModel);
	var opts = {
		cls: "ypager",
		perpage: 10,
		getDataUrl:"_get_editpackage_pagination.php",		
		pageSize:10,
		postData:{"UserId":<?php echo $_SESSION ['UserId']; ?>,"orderBy":viewModel.defaultOrderBy,"isAsc":viewModel.isAsc()},
		onFill:function(rows){			 
			viewModel.loadData(rows);			     			     				
		}
	};
	pagination.paging(opts);	

	/*申请数据包*/
	$("#btnPackage").click(function(){
		//console.info('test');
		var applyCount = $("#txtApplyCount").val();
		if(!(/^[1-9]\d*$/.test(applyCount))){
			alert("申请条数必须为整数");
			return;
		}

		/*
		if(parseInt(applyCount) == 0){
			alert("申请条数不能为0");
			return;
		}
*/
		
		$.ajax({
            url: "assigneditpackage.php?op=create",
            type: "POST",
            dataType: 'json',
            data: {
                "category":$("#categories option:selected").val(),
                "packageName":$("#txtPackage").val(),
                "applyCount":$("#txtApplyCount").val()
                },
            success: function (data) {
                //console.info(data);  
                if (data.success != undefined) {
                    alert(data.msg);
                } 
                pagination.paging(opts);

                jQuery.post("assigneditpackage.php?op=wordcount",{"category":$("#Category").text()},function(d){
            		$("#applyMsg").text(d.wordcount);	
            	},"json")
            }
        });
	});


	jQuery.post("../common.php?wordcategory=1", {}, function (d) {
		var $categories = $("#categories");
		$categories.empty();        
        $.each(d, function (i, n) {
            $("<option></option>").val(this.id).text(this.name).appendTo($categories);
        });
        	    
	    if(d.length > 0){
	    	var i;
	    	var current = $("#Category").text();
            if (current && current != undefined) {
                $("#categories option").each(function (index) {
                    if (this.value == current) {
                        i = index;
                        if (!$.browser.msie || ($.browser.msie && $.browser.version != "6.0")) {
                            $(this).attr("selected", "selected");
                        }             
                    }
                })
            }	    	
	    }else{	    	
	    	$categories.options[0].selected=true;  
		}
	    
	},"json");

	jQuery.post("assigneditpackage.php?op=wordcount",{"category":$("#Category").text()},function(d){
		$("#applyMsg").text(d.wordcount);	
	},"json")

		
});		
</script>
</body>
</html>