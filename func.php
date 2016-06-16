<?php
require_once 'def.php';
?>
function checkUsernamePasswd(accountValue, passwdValue)
{
	if(accountValue.length < <?=_USERNAME_LEN_MIN?> || accountValue.length > <?=_USERNAME_LEN_MAX?>)
	{
		mui.toast('账号长度为<?=_USERNAME_LEN_MIN?>-<?=_USERNAME_LEN_MAX?>个字符。');
		return 1;
	}
	if(passwdValue.length < <?=_PASSWD_LEN_MIN?> || passwdValue.length > <?=_PASSWD_LEN_MAX?>)
	{
		mui.toast('密码长度为<?=_PASSWD_LEN_MIN?>-<?=_PASSWD_LEN_MAX?>个字符。');
		return 1;
	}
	return 0;
}

function getQueryString(name)
{
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
} 