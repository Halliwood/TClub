<?php
require_once dirname(dirname(__FILE__)).'/def.php';
function checkPreorderLiftTime($builderDB)
{
	if($builderDB->actionTime + _PREORDERTIMELIFE > time())
	{
		// preorder is alive		
		return 1;
	}
	return 0;
}
?>
