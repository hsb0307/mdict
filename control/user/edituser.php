<?php
session_start();
require_once '../../lib/dao/UserDao.php';
require_once '../../lib/common/_common.php';
$userDao = new UserDao();
$userid = 1;
if(isset( $_SESSION ['UserId']))
{
	$userid = $_SESSION ['UserId'];
}
if(isset( $_GET ['id']))
{
	$userid =$_GET ['id'];
}
$user = $userDao->GetById( $userid);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>修改您的帐号信息</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/bootstrap-responsive.min.css" rel="stylesheet">
    <style type="text/css">
        #header{ background-image: url("../../images/headerbg.jpg"); background-repeat: repeat-x; height:70px;} /* repeat-X */
		#logo {position:relative;}
		#logo img{ top:22px; left:30px;position:absolute;}
		#currentUser {  position:relative;}
        #currentUser a { top:30px; right:100px;position:absolute;}
        #currentUser a:hover { color:red;text-decoration:none;}
		.form-horizontal .control-group {margin-bottom: 10px;}/* 默认值为20 */
		/* .form-horizontal .control-label {width: 120px;} */ /* 默认值为160 */
#cancel {margin-left:20px;}
.span6{width:500px;}
.input-xlarge{width:200px}
	</style>
	<!--  
	<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.0.min.js"></script>
	--> 
	<script type="text/javascript" src="../../js/jquery-1.8.2.min.js"></script>
    <script>!window.jQuery && document.write('<script src="http://code.jquery.com/jquery-1.8.0.min.js"><\/script>');</script>
    
	<script type="text/javascript">
	
	 $(function() {
         //$("#userForm :input[type='text']:enabled:first").focus();
		 $("#UserName").focus();
		 //$("#userForm").load("?control=msgs", {}, function() { 
  		//	$('#UserName').focus();
		 //});
		 //var txtBox=document.getElementById("UserName");
		 //txtBox.focus();
		 $("#formActions").css("padding-left", ($("#userForm").width()/2 -30) + "px");
		 $("#RoleId").width($("#Birthday").width());
		 var role ="<?php   echo $user->RoleId;?>" ;
		 
		 var gender = "<?php   echo $user->Gender;?>";
		 
		 document.getElementById('Gender_Male').checked = gender == "男"? true : false;
		 document.getElementById('Gender_Female').checked = gender == "女"? true : false;

		 function fillWordCategory(categoryId){
				var html = "";
				var rows = enumerableData.wordCategories;
				for (var i = 0; i < rows.length; i++) {
					html += "<option label='"+ rows[i].name +"' value='"+ rows[i].id +"'>" + rows[i].name + "</option>";
				}
				var c = $("#WordCategory");
			    c.html();
			    c.html(html);
			    if(rows.length > 0){
			    	var packages1 = document.getElementById("WordCategory");
			    	if(!categoryId) categoryId = 0;
			    	var selectedIndex = 0;
			    	for(var i = 0, j = rows.length;i < j; i++){
				    	if(packages1.options[i].value == categoryId) {
				    		selectedIndex = i;
				    		break;
				    	}
			    	}
			    	packages1.options[selectedIndex].selected=true;
			    }
		}
		fillWordCategory(<?php   echo $user->WordCategory;?>);
		

		 var required = {"必填":function(el){ return $.trim(el.value).length != 0 }};
		 var isEmail = {"格式不正确":function(el){var val = $.trim(el.value); return /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/.test(val)}};
		 var isMobile = {"格式不正确":function(el){var val = $.trim(el.value);return /^(13|15|18|14)\d{9}$/.test(val)}};
		 var isBirthday = {"格式不正确":function(el){var val = $.trim(el.value);return /^\d{4}-\d{2}-\d{2}$/.test(val)}};
		 var isPINCodes = {"格式不正确":function(el){var val = $.trim(el.value);return /^(^\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/.test(val)}};

		 validate("#userForm", "#UserName", {
			 "必填":function(el){ return $.trim(el.value).length != 0 },
			 "用户已存在":function(el){ 
				 
				 var postData = {"Username" : el.value};
				 //alert(el.value);// _usercontroller.php
				 jQuery.post("_userlogout.php?op=exist", postData,function(data) {
				 	var cnt = eval("(" + data + ")");
				 	if(parseInt(cnt) > 1) {
				 		 showTip(el, 0, "用户已存在");
				 	} else {
				 		// 验证通过
				 		showTip(el, 1);
				 	}
				 	
				 });
				 return true;
				}
			});
		 validate("#userForm", "#Password", required);
		 validate("#userForm", "#Password2", {
			 "必填":function(el){ return $.trim(el.value).length != 0 },
			 "必须一致":function(el){ return el.value == document.getElementById('Password').value; }
		 });
		 validate("#userForm", "#Birthday", isBirthday);
		 validate("#userForm", "#PINCodes", isPINCodes);
		 validate("#userForm", "#PasswordQuestion", required);
		 validate("#userForm", "#Mobile", isMobile);
		 validate("#userForm", "#PasswordAnswer", required);
		 validate("#userForm", "#Email", isEmail);
		 validate("#userForm", "#RealName", required);
		 validate("#userForm", "#Company", required);
		 
		 $("#cancel").click(function () {
			 var role ="<?php   echo $user->RoleId;?>" ;
			 if(role === "0"){
					//alert("登录成功");
				} else if (role ==="1") {
					location = '../word/editword.php';
				} else if(role === "2"){
					location = '../word/reviseword.php';
				} else if (role ==="3"){
					location = '../word/approveword.php';
				} else {
					location = '../admin/dashboard.php';
				}

		});
		 $("#save").click(function () {
			 if (showTip.nodes.length) {
			        alert("数据验证没有通过，请检查数据格式是否正确");
			        return;
			    }
			    if ($.trim(document.getElementById("UserName").value).length == 0 || $.trim(document.getElementById("Password").value).length == 0 || $.trim(document.getElementById("RealName").value).length == 0) {
			        alert("请先输入正确的数据");
			        return;
			    }
			 var role ="<?php   echo $user->RoleId;?>" ;
			 var fields = {"UserId": document.getElementById("UserId").value,
					 "UserName": document.getElementById("UserName").value,
						"Password": document.getElementById("Password").value,
						"PasswordQuestion": document.getElementById("PasswordQuestion").value,
						"PasswordAnswer": document.getElementById("PasswordAnswer").value,
						"RealName": document.getElementById("RealName").value,
						"Gender": document.getElementById('Gender_Male').checked ? "男":"女",
						"Birthday": document.getElementById("Birthday").value,
						"PINCodes": document.getElementById("PINCodes").value,
						"Mobile": document.getElementById("Mobile").value,
						"Telephone": document.getElementById("Telephone").value,
						"Company": document.getElementById("Company").value,
						"Email": document.getElementById("Email").value,
						"QQ": "",
						"IsApproved": document.getElementById("IsApproved").value,
						"Description": $("#Description").val(),//  document.getElementById("Description").value,
						"RoleId": role == "91"? 91 : $("#RoleId").val(),
						"WordCategory": $("#WordCategory").val(),
						"PageSize":10};
			 jQuery.post("SaveUser.php?op=edit", fields, function (data){
				 var user = eval("(" + data + ")");
				 if(user.RoleId === "0"){
						//alert("登录成功");
					} else if (user.RoleId ==="1") {
						location = '../word/editword.php';
					} else if(user.RoleId === "2"){
						location = '../word/reviseword.php';
					} else if (user.RoleId ==="3"){
						location = '../word/approveword.php';
					} else if (user.RoleId ==="91"){
						location = '../admin/dashboard.php';
					} else {
						alert("修改失败");
					}
				});
		 });
     });

	</script>
  </head>

<body>
<div class="container">
	<div id="header">
	<div id="logo" ><img src="../../images/logo1.jpg" ></img></div>
	<!--  <div id="currentUser" ><a href="javascript:void(0);" >一些提示信息</a></div> --><!--?php echo $currentUsername;?-->
	</div>
</div>
<!-- <div class="clearfix"></div> -->
<div class="container">
<form id="userForm" class="form-horizontal well" ><!-- action="SaveUser.php" method="post" -->
        <fieldset>
          <legend>修改您的帐号信息</legend>
		<input type="hidden"  name="UserId" id="UserId" value="<?php echo $user->UserId;?>" />
		<input type="hidden"  name="IsApproved" id="IsApproved" value="<?php echo $user->IsApproved;?>" />
          <div style="height:10px;" >&nbsp;</div>
          <div class="row">
		 <div class="span6">
          <div class="control-group">
            <label class="control-label" for="UserName">用户名</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="UserName" value="<?php echo $user->UserName;?>" />
              <span>*</span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="Password">密码</label>
            <div class="controls">
              <input type="password" class="input-xlarge" id="Password"  /> <!-- value="<?php echo $user->Password;?>" -->
              <span>*</span><!--  <span class="help-inline">格式不正确！</span> -->
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="Password2">确认密码</label>
            <div class="controls">
              <input type="password" class="input-xlarge" id="Password2"  /> <!-- value="<?php echo $user->Password;?>" -->
              <span>*</span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="PasswordQuestion">密码提示</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="PasswordQuestion" value="<?php echo $user->PasswordQuestion;?>" />
              <span>*</span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="PasswordAnswer">提示答案</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="PasswordAnswer" value="<?php echo $user->PasswordAnswer;?>" />
              <span>*</span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="RealName">真实姓名</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="RealName" value="<?php echo $user->RealName;?>" />
              <span>*</span>
            </div>
        </div>
         <div class="control-group">
            <label class="control-label" for="Gender">性别</label>
            <div class="controls">
              <!-- 
              <input type="text" class="input-xlarge" id="Gender" value="<?php echo $user->Gender;?>" />
              <span>*</span>
               -->
              <input type="radio" name="Gender" id="Gender_Male" value="男" />男
              <input type="radio" name="Gender" id="Gender_Female" value="女"/>女
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="Telephone">固定电话</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="Telephone" value="<?php echo $user->Telephone;?>" />
              <span>*</span>
            </div>
        </div>
        
        </div>
        <div class="span6">
        <div class="control-group">
            <label class="control-label" for="RoleId">用户角色</label>
            <div class="controls">
              <select id="RoleId">
                <option label="输入人" value="1" <?php if($user->RoleId == 1) echo 'selected';?> >输入人</option>
				<option label="编辑人" value="2" <?php if($user->RoleId == 2) echo 'selected';?> >编辑人</option>
				<option label="审核人" value="3" <?php if($user->RoleId == 3) echo 'selected';?> >审核人</option>
				<option label="录入管理员" value="81" <?php if($user->RoleId == 81) echo 'selected';?> >录入管理员</option>
				<option label="编辑管理员" value="82" <?php if($user->RoleId == 82) echo 'selected';?> >编辑管理员</option>
				<option label="审定管理员" value="83" <?php if($user->RoleId == 83) echo 'selected';?> >审定管理员</option>
				
				<!-- <option label="系统管理员" value="91" <?php if($user->RoleId == 91) echo 'selected';?> >系统管理员</option>  -->
              </select>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="WordCategory">工作词条类别</label>
            <div class="controls">
              <select id="WordCategory"></select>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="Birthday">出生日期</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="Birthday" value="<?php echo $user->Birthday;?>" /><img onclick="WdatePicker({el:'Birthday'})" src="../../js/My97DatePicker/skin/datePicker.gif" width="16" height="22" align="absmiddle" style="margin-left: 3px;">
              <span>*</span>
            </div>
        </div>
          <div class="control-group">
            <label class="control-label" for="PINCodes">身份证号</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="PINCodes" value="<?php echo $user->PINCodes;?>" />
              <span>*</span><!--  <span class="help-inline">格式不正确！</span> -->
            </div>
        </div>
          <div class="control-group">
            <label class="control-label" for="Mobile">移动电话</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="Mobile" value="<?php echo $user->Mobile;?>" />
              <span>*</span>
            </div>
        </div>
		  <div class="control-group">
            <label class="control-label" for="Email">e-mail</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="Email" value="<?php echo $user->Email;?>" />
              <span>*</span>
            </div>
        </div>
          <div class="control-group">
            <label class="control-label" for="Company">工作单位</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="Company" value="<?php echo $user->Company;?>" />
              <span>*</span>
            </div>
        </div>
		 
        
         <div class="control-group">
            <label class="control-label" for="Description">备注</label>
            <div class="controls">
              <textarea class="input-xlarge" id="Description" rows="3"><?php echo $user->Description;?></textarea>
            </div>
          </div>
          
          </div>
          </div>
          
          <div class="form-actions" id="formActions">
            <button type="button" class="btn btn-primary" id="save">保存</button>
            <button type="reset" class="btn" id="cancel" id="cancel">取消</button>
          </div>
        </fieldset>
      </form>
</div>
<script language="javascript" type="text/javascript" src="../../js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="../../js/mongoliandictionary.js"></script>

</body>
</html>