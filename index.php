<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>标题</title>
</head>
	<body>
<script type="text/javascript">
<?php
if( !isset( $_SESSION ['UserId'] ) ){ 
    echo "location = 'login.htm';";
} else  {
	if( isset($_SESSION["RoleId"])){
		$roleId = $_SESSION["RoleId"];
		switch ($roleId) {
			case 1 :
				echo 'location = "control/task/myeditpackage.php";';
				break;
			case 2 :
				echo 'location = "control/task/myrevisepackage.php";';
				break;
			case 3 :
				echo 'location = "control/task/myapprovepackage.php";';
				break;
			case 71:
				echo 'location = "control/word/addword.php";';
				break;
			case 81:
				echo 'location = "control/task/editpackage.php";';
				break;
			case 82:
				echo 'location = "control/task/resivepackage.php";';
				break;
			case 83:
				echo 'location = "control/task/approvepackage.php";';
				break;
			case 91:
				echo 'location = "control/admin/dashboard.php";';
				break;
			default:
				echo 'location = "signout.php";';
		}
		
    }
}
?>
</script>
	</body>
</html>