<?php
$preorderRst = new stdClass();
$code = 0;
$actionCode = 0;
$dbReturn = '';
require_once dirname(dirname(__FILE__)).'/def.php';
require_once 'tools.php';
$action = 0;
if(!isset($_POST['action']))
{
	$code |= _CM_POSTPARAM_ERROR;
}
else
{
	$action = $_POST['action'];
	if(0 != $action && 1 != $action)
	{
		$code |= _CM_POSTPARAM_ERROR;
	}
	else
	{
		$orderID = 0;
		$builder = 0;
		if(0 == $action)
		{
			if(!isset($_POST['orderID']))
			{
				$code |= _CM_POSTPARAM_ERROR;
			}
			else
			{
				$orderID = $_POST['orderID'];
			}
		}
		else
		{
			if(!isset($_POST['builder']))
			{
				$code |= _CM_POSTPARAM_ERROR;
			}
			else
			{
				$builder = $_POST['builder'];
			}
		}
	}
}
if(0 == $code)
{
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
		$userDBarr = $userData->fetchAll();
		require_once '../db/dbconst.php';
		if (count($userDBarr))
		{
			$userDB = $userDBarr[0];
			if(0 == $action)
			{
				// cancel
				$dbReturn = $dbo->cancelOrderBuilder($orderID);
			}
			else
			{
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
						if(!empty($builderDB))
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
						if(!empty($builderDB))
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
							$dbReturn = $dbo->orderBuilder($userid, $builder, _PREORDERPRICE);
							$builderInfoDB= $dbo->getLatestPreorderInfoByBuilder($builder);
							$builderDB = $builderInfoDB->fetchObject();
							$isSuccess = FALSE;
							if(!empty($builderDB))
							{
								if(1 == checkPreorderLiftTime($builderDB))
								{
									// preorder is alive
									$preorderRst->builder = (int)$builderDB->builder;
									$preorderRst->orderID = (int)$builderDB->id;
									$preorderRst->actionTime = (int)$builderDB->actionTime;
									$preorderRst->coinDelta = -((int)_PREORDERPRICE);
									$isSuccess = TRUE;
								}
							}
							
							if(!$isSuccess)
							{
								$actionCode |= _PREORDER_MASK_ERROR;
							}
						}	
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
$preorderRst->dbReturn = $dbReturn;
header('Content-type: application/json');
echo json_encode($preorderRst);
?>
