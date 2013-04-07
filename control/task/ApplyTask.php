<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>词典录入窗口</title>
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
#header {
	background-image: url("../../images/headerbg.jpg");
	background-repeat: repeat-x;
	height: 70px;
	position: relative;
} /* repeat-X */
#logo {
	position: relative;
}

#logo img {
	top: 22px;
	left: 30px;
	position: absolute;
}

#currentUser {
	position: relative;
}

#currentUser a {
	top: 30px;
	right: 100px;
	position: absolute;
}


#dataPackage{
	/*border:1px solid red;*/
    top: 30px;
	right: 20px;
	width:70px;
	position: absolute;
}

#currentUser a:hover {
	color: red;
	text-decoration: none;
}

.span9 {
	width: 866px
}

.span9 {
	height: 566px
}

.span9 {
	position: relative;
} /* position:relative; */
.span9 .ypager {
	position: absolute;
	bottom: 10px;
	left: 10px;
} /* position:absolute; */
</style>
<!--  
	<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.0.min.js"></script>
	-->
<script type="text/javascript" src="../../js/jquery-1.8.2.min.js"></script>
<script>!window.jQuery && document.write('<script src="http://code.jquery.com/jquery-1.8.2.min.js"><\/script>');</script>
<script type="text/javascript" src="../../js/jquery.paging.min.js"></script>
<script type="text/javascript" src="../../js/knockout-2.0.0.js"></script>
<script type="text/javascript" src="../../js/Lib.js"></script>
<script type="text/javascript">
    $(function(){
        $("#btn1").click(function(){
        
        	$.ajax({
                url: "saveapplytask.php",
                type: "POST",
                dataType: 'json',
                data: {"op":"apply"},                    
                success: function (d) {
                    if(d.success){
                        alert(d.msg);
                     }else{
                       	 alert(d.msg);
                     }
                },
                error: function (data) {
                    
                }
            });
        })
     })
    </script>
</head>
<body>
	<div class="container">
		<div id="header">
			<div id="logo">
				<img src="../../images/logo1.jpg"></img>
			</div>
			<!--  
			<div id="currentUser">
				<a href="javascript:void(0);">一些提示信息</a>
			</div>
			<div id="dataPackage">
			<a href="javascript:location = '../../control/task/applytask.php'">申请数据包</a>
			</div>-->
			<!--?php echo $currentUsername;?-->
		</div>
	</div>
 
<div id="main" class="container"><input type="button" id="btn1" value="申请" class="btn"></div>
</body>
</html>
 