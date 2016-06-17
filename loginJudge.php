<?php
$loginRst = new stdClass();
$code = 0;
$actionCode = 0;
require_once 'def.php';
require_once 'db/dbconst.php';
if(!isset($_POST['type']) || !isset($_POST['auto']))
{
	$code |= _CM_POSTPARAM_ERROR;
}
else
{
	$type = $_POST['type'];
	$auto = $_POST['auto'];
	require_once 'db/TClubDBOperator.php';
	$dbo = new TClubDBOperator();
	$userSql = null;
	$userDataArr = null;
	$userDB = null;
	if(_LOGINTYPE_USERNAMEPASSWD == $type)
	{
		// use username & password
		if(!isset($_POST['username']) || !isset($_POST['password']))
		{
			$code |= _CM_POSTPARAM_ERROR;
		}
		else
		{
			$username = $_POST['username'];
			$passwd = $_POST['password'];
			$userSql = $dbo->getUserDataByUP($username, $passwd);
			$userSql->setFetchMode(PDO::FETCH_ASSOC);
			$userDataArr = $userSql->fetchAll();
			if (count($userDataArr) > 0)
			{
				$userDB = $userDataArr[0];
			}
			else
			{
			  $code |= _CM_USERIDENTIFY_INVALID;
			}
		}
	}
	else
	{
		if(isset($_COOKIE['tclub_login']))
		{
			$clean = array();
			$mysql = array();
			
			$now = time();
			
			list($identifier, $token) = array_pad(explode(':', $_COOKIE['tclub_login'], 2), 2, null);
			if(!is_null($identifier) && !is_null($token) && ctype_alnum($identifier) && ctype_alnum($token))
			{
				$clean['identifier'] = $identifier;
				$clean['token'] = $token;
				
				$mysql['identifier'] = mysql_real_escape_string($clean['identifier']);
				$userSql = $dbo->getUserToken($mysql['identifier']);
				$userSql->setFetchMode(PDO::FETCH_ASSOC);
				$userDataArr = $userSql->fetchAll();
				if(count($userDataArr))
				{
					$userDB = $userDataArr[0];
					if($clean['token'] != $userDB['token'])
					{
						// token error
						$code |= _CM_USERIDENTIFY_TOKENERROR;
					}
					elseif($now > $userDB['timeout'])
					{
						// timeout
						$code |= _CM_USERIDENTIFY_TIMEOUT;
					}
				}
				else
				{
					$code |= _CM_USERIDENTIFY_TOKENERROR;
				}
			}
			else
			{
				$code |= _CM_USERIDENTIFY_TOKENERROR;
			}
		}
		else
		{
			$code |= _CM_USERIDENTIFY_ERROR;
		}
	}
}

if(0 == $code)
{
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
		$username = $userDB['name'];
		session_start();
		$_SESSION['username'] = $username;
		$_SESSION['userid'] = $userDB['id'];
		$_SESSION['status'] = $userDB['status'];
		session_commit();

		if($auto)
		{
			// update identifier and token and write into cookie
			$identifier = md5(_ENC_SALT.md5($username._ENC_SALT));
			$token = md5(uniqid(rand(), TRUE));
			$timeout = time() + 60 * 60 * 24 * 7;
			
			$dbo->updateUserToken($userDB['id'], $identifier, $token, $timeout);
			setcookie('tclub_login', "$identifier:$token", $timeout);
		}
		else
		{
			setcookie('tclub_login', '', time() - 3600);
		}
	}
}
$loginRst->code = $code;
header('Content-type: application/json');
echo json_encode($loginRst);
?>
