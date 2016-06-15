<?php
define('_CM_USERIDENTIFY_ERROR', 1);
define('_CM_USERIDENTIFY_INVALID', 2);
define('_CM_USERIDENTIFY_BANNED', 4);
define('_CM_USERIDENTIFY_ADMIN', 8);
define('_CM_POSTPARAM_ERROR', 16);

define('_PREORDER_MASK_COINNOTENOUGH', 1);
define('_PREORDER_MASK_OTHEROCCUPIED', 2);  // 他人已抢先预约
define('_PREORDER_MASK_SELFPREORDERED', 4);  // 自己已预约

define('_PREORDERPRICE', 100);
define('_PREORDERTIMELIFE', 600);

$lvSteps = array(100, 200, 500, 1000, 2000, 5000, 10000);
define('_LVSTEPS', json_encode($lvSteps));
?>
