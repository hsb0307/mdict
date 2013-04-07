<?php
require_once '../../lib/dao/RevisePackageDao.php';
$where ="";
$taskDao = new RevisePackageDao();
$rowCount = $taskDao->GetCountByPackageId($where);
echo $rowCount;
?>