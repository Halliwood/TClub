<?php
$loginRst = new stdClass();
$code = 0;
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
		else if(!(_USERSTATUS_MASK_RECOGNISED & $userDB['status']))
		{
			// not recognised
			$code |= _CM_USERIDENTIFY_NORECOGNISED;
		}
		else
		{
			session_start();
			$_SESSION['username'] = $username;
			$_SESSION['userid'] = $userDB['id'];
			$_SESSION['status'] = $userDB['status'];
			session_commit();
		
			// update identifier and token and write into cookie
			$identifier = md5(_ENC_SALT.md5($username._ENC_SALT));
			$token = md5(uniqid(rand(), TRUE));
			$timeout = time() + 60 * 60 * 24 * 7;
			
			$dbo->updateUserToken($userDB['id'], $identifier, $token, $timeout);
			setcookie('tclub_login', "$identifier:$token", $timeout);
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
