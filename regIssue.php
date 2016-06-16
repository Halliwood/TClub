<?php
$regRst = new stdClass();
$code = 0;
$actionCode = 0;
require_once 'def.php';
if(!isset($_POST['username']) || !isset($_POST['password']))
{
	$code |= _CM_POSTPARAM_ERROR;
}
else
{
	$username = $_POST['username'];
	$passwd = $_POST['password'];
	require_once 'db/TClubDBOperator.php';
	$dbo = new TClubDBOperator();
	// check if exist
	$userData = $dbo->getUserData($username);
	$userData->setFetchMode(PDO::FETCH_ASSOC);
	$userDataArr = $userData->fetchAll();
	if (count($userDataArr) > 0)
	{
		// username exist
		$code |= _CM_DEFAULT_ERROR;
		$actionCode |= _REG_MASK_EXIST;
	}
	else
	{
		require_once 'db/dbconst.php';
		$dbReturn = $dbo->reg($username, $passwd);
		$userData = $dbo->getUserData($username);
		$userData->setFetchMode(PDO::FETCH_ASSOC);
		$userDataArr = $userData->fetchAll();
		if (count($userDataArr) < 1)
		{
			$code |= _CM_DEFAULT_ERROR;
			$actionCode |= _REG_MASK_ERROR;
		}
	}
}
$regRst->code = $code;
$regRst->actionCode = $actionCode;
header('Content-type: application/json');
echo json_encode($regRst);
?>
