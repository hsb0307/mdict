<?php
$roleId = $_SESSION ['RoleId'];
$packageLink = 'myeditpackage.php';
switch ($roleId) {
	case 1 :
		$packageLink = 'myeditpackage.php';
		break;
	case 2 :
		$packageLink = 'myrevisepackage.php';
		break;
	case 3 :
		$packageLink = 'myapprovepackage.php';
		break;
	case 71 :
		$packageLink = 'myeditpackage.php';
		break;
	case 81 :
		$packageLink = 'revisepackage.php';
		break;
	case 82 :
		$packageLink = 'approvepackage.php';
		break;
	case 83 :
		$packageLink = 'approvepackage.php';
		break;
	default :
		$packageLink = 'myeditpackage.php';
}
$wordLink = 'editword.php';
switch ($roleId) {
	case 1 :
		$wordLink = 'editword.php';
		break;
	case 2 :
		$wordLink = 'reviseword.php';
		break;
	case 3 :
		$wordLink = 'approveword.php';
		break;
	case 71 :
		$wordLink = 'myword.php';
		break;
	default :
		$wordLink = 'editword.php';
}
?>
<div id="header" class="container">
	<div id="logo">
		<img src="../../images/logo1.jpg" alt="logo" />
	</div>
	<div id="userinfo" class="offset9">
		<ul class="nav">
			<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" id="userInfo" role="" data-role="<?php echo $_SESSION['RoleId']; ?>" data-user="">欢迎您，<?php echo $_SESSION['RealName']; ?><b class="caret"></b></a>
				<ul class="dropdown-menu">
					<!--  <li><a href="../../control/task/applytask.php"><i class="icon-cog"></i>申请数据包</a></li> -->
<?php if( $roleId == 91) { 
	echo '<li><a href="../../control/admin/dashboard.php" target="_blank"><i class="icon-cog"></i>控制面板</a></li>';
}?>
<?php if( $roleId > 0 && $roleId < 10) {
	echo '<li><a href="../../control/task/'. $packageLink. '"><i class="icon-cog"></i>我的数据包</a></li><li><a href="../../control/word/'. $wordLink.'"><i class="icon-cog"></i>我的词条</a></li>';
} ?>
<?php if( $roleId == 71) {
	echo '<li><a href="../../control/word/'. $wordLink.'"><i class="icon-cog"></i>我的词条</a></li>';
	echo '<li><a href="../../control/word/addword.php"><i class="icon-cog"></i>添加词条</a></li>';
} ?>
<?php if( $roleId > 80 && $roleId < 84) {
	echo '<li><a href="../../control/task/'. $packageLink. '"><i class="icon-cog"></i>数据包管理</a></li>';
} ?>



					
					<li><a href="../../control/user/edituser.php" target="_blank"><i class="icon-cog"></i>修改我的信息</a></li>
					<li><a href="../../control/word/selectword.php" target="_blank"><i class="icon-cog"></i>查询词条</a></li>

<?php if( $roleId == 91) { 
	echo '<li><a href="../../control/log/log.php" target="_blank"><i class="icon-cog"></i>操作日志</a></li>';
} else {
	echo '<li><a href="../../control/log/index.php" target="_blank"><i class="icon-cog"></i>操作日志</a></li>';
}
?>		
					<li><a href="../../control/guestbook/index.php" target="_blank"><i class="icon-cog"></i>留言管理</a></li>
					<li><a href="../../help/index.html" target="_blank"><i class="icon-cog"></i>帮助</a></li>
					<li class="divider"></li>
					<li><a href="../../signout.php"><i class="icon-off"></i>退出</a></li>
				</ul>
			</li>
		</ul>
	</div>
</div>

<!-- <div class="clearfix"></div> -->
