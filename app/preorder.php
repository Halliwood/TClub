<?php
$preorderRst = new stdClass();
$code = 0;
$actionCode = 0;
require_once '../def.php';
if(!isset($_POST['builder']))
{
	$code |= _CM_POSTPARAM_ERROR;
}
else
{
	$builder = $_POST['builder'];
	$username = '';
	$userid = 0;
	$coin = 0;
	session_start();
	if(!isset($_SESSION['username']) || !isset($_SESSION['userid']))
	{
		$code |= _CM_USERIDENTIFY_ERROR;
	}
	else
	{
		$username = $_SESSION['username'];
		$userid = $_SESSION['userid'];
		session_commit();
		require_once '../db/TClubDBOperator.php';
		$dbo = new TClubDBOperator();
		$userData = $dbo->getUserData($username);
		$userData->setFetchMode(PDO::FETCH_ASSOC);
		$userDataArr = $userData->fetchAll();
		require_once '../db/dbconst.php';
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
		  	// check coin
		  	$coin = $userDB['coin'];
		    if($coin < _PREORDERPRICE)
			{
				$actionCode |= _PREORDER_MASK_COINNOTENOUGH;
			}
			else
			{
				// check if already preordered some by self
				$builderInfoDB= $dbo->getLatestPreorderInfoByUserid($userid);
				$builderDB = $builderInfoDB->fetchObject();
				if(isset($builderDB))
				{
					if(0 != checkPreorderLiftTime($builderDB))
					{
						// preorder is alive
						$actionCode |= _PREORDER_MASK_SELFPREORDERED;
					}
				}
				
				// check if already preordered by other
				$builderInfoDB= $dbo->getLatestPreorderInfoByBuilder($builder);
				$builderDB = $builderInfoDB->fetchObject();
				if(isset($builderDB))
				{
					if(0 != checkPreorderLiftTime($builderDB))
					{
						// preorder is alive
						$actionCode |= _PREORDER_MASK_OTHEROCCUPIED;
					}
				}
				
				// preorder
				if(0 == $actionCode)
				{
					$dbo->preorderBuilder($userid, $builder, _PREORDERPRICE);	
				}	
			}
		  }
		}
		else
		{
		  $code |= _CM_USERIDENTIFY_INVALID;
		}	
	}
}

$preorderRst->code = $code;
$preorderRst->actionCode = $actionCode;
header('Content-type: application/json');
echo json_encode($preorderRst);

function checkPreorderLiftTime($builderDB)
{
	$preorderTime = date_create($builderDB->actionTime);
	$nowDatetime = date_create('now');
	$interval = date_diff($preorderTime, $nowDatetime);
	$sec = $interval->h * 3600 + $interval->i * 60 + $interval->s;
	if($sec < _PREORDERTIMELIFE)
	{
		// preorder is alive
		return 0;
	}
	return 1;
}
?>
