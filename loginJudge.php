<?php
$loginRst = new stdClass();
$code = 0;
require_once 'def.php';
if(!isset($_POST['username']) || !isset($_POST['password']))
{
	$code |= _CM_USERIDENTIFY_ERROR;
}
else
{
	$username = $_POST['username'];
	$passwd = $_POST['password'];
	require_once 'db/TClubDBOperator.php';
	$dbo = new TClubDBOperator();
	$userData = $dbo->getUserDataByUP($username, $passwd);
	$userData->setFetchMode(PDO::FETCH_ASSOC);
	$userDataArr = $userData->fetchAll();
	require_once 'db/dbconst.php';
	if (count($userDataArr) > 0)
	{
	  $userDB = $userDataArr[0];
	  if(_USERSTATUS_MASK_BANNED & $userDB['status'])
	  {
	  	// banned
	    $code |= _CM_USERIDENTIFY_BANNED;
	  }
	  else
	  {
	    $timeout = time() + 60 * 60 * 24 * 7;
	    setcookie("tclub_login", $username, $timeout);
	    if(_USERSTATUS_MASK_ADMIN & $userDB['status'])
		{
			$code |= _CM_USERIDENTIFY_ADMIN;
		}
		session_start();
		$_SESSION['username'] = $username;
		$_SESSION['userid'] = $userDB['id'];
		$_SESSION['status'] = $userDB['status'];
		$_SESSION['coin'] = $userDB['coin'];
		session_commit();
	  }
	}
	else
	{
	  $code |= _CM_USERIDENTIFY_INVALID;
	}
}
$loginRst->code = $code;
header('Content-type: application/json');
echo json_encode($loginRst);
?>
