<?php
define('_ENC_SALT', '38DB5B65-AFBA-439D-998D-7E08852EBEC8');

define('_USERNAME_LEN_MIN', 3);
define('_USERNAME_LEN_MAX', 10);
define('_PASSWD_LEN_MIN', 6);
define('_PASSWD_LEN_MAX', 16);

define('_LOGINTYPE_USERNAMEPASSWD', 1);
define('_LOGINTYPE_TOKEN', 2);

define('_CM_POSTPARAM_ERROR', 1);
define('_CM_DEFAULT_ERROR', 2);
define('_CM_USERIDENTIFY_ERROR', 4);  // 用户状态错误
define('_CM_USERIDENTIFY_INVALID', 8);  // 用户名或密码错误
define('_CM_USERIDENTIFY_BANNED', 16);
define('_CM_USERIDENTIFY_NORECOGNISED', 32);
define('_CM_USERIDENTIFY_TOKENERROR', 64);
define('_CM_USERIDENTIFY_TIMEOUT', 128);

define('_REG_MASK_EXIST', 1);
define('_REG_MASK_ERROR', 2);

define('_PREORDER_MASK_COINNOTENOUGH', 1);
define('_PREORDER_MASK_OTHEROCCUPIED', 2);  // 他人已抢先预约
define('_PREORDER_MASK_SELFPREORDERED', 4);  // 自己已预约
define('_PREORDER_MASK_ERROR', 8);

define('_PREORDERPRICE', 100);
define('_PREORDERTIMELIFE', 600);

define('_PAGEFROM_REGOK', 1);

$lvSteps = array(100, 200, 500, 1000, 2000, 5000, 10000);
define('_LVSTEPS', json_encode($lvSteps));
?>
