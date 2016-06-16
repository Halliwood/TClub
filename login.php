<?php
require_once 'def.php';
// check auto login
$username = '';
$autoLogin = FALSE;
$identifier = '';
$token = '';
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
		require_once 'db/TClubDBOperator.php';
		$dbo = new TClubDBOperator();
		$userTokenSql = $dbo->getUserToken($mysql['identifier']);
		$userTokenSql->setFetchMode(PDO::FETCH_ASSOC);
		$userTokenArr = $userTokenSql->fetchAll();
		if(count($userTokenArr))
		{
			$userTokenDB = $userTokenArr[0];
			if($clean['token'] != $userTokenDB['token'])
			{
				// token error
			}
			elseif($now > $userTokenDB['timeout'])
			{
				// timeout
			}
			else
			{
				$username = $userTokenDB['name'];
				$autoLogin = TRUE;
			}
		}
	}
}
?>
<!DOCTYPE html>
<html class="ui-page-login">
	<head>
		<meta content="text/html" charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title></title>
		<link href="css/mui.min.css" rel="stylesheet" />
		<style>
			.area {
				margin: 20px auto 0px auto;
			}
			
			.mui-input-group {
				margin-top: 10px;
			}
			
			.mui-input-group:first-child {
				margin-top: 20px;
			}
			
			.mui-input-group label {
				width: 22%;
			}
			
			.mui-input-row label~input,
			.mui-input-row label~select,
			.mui-input-row label~textarea {
				width: 78%;
			}
			
			.mui-checkbox input[type=checkbox],
			.mui-radio input[type=radio] {
				top: 6px;
			}
			
			.mui-content-padded {
				margin-top: 25px;
			}
			
			.mui-btn {
				padding: 10px;
			}
			
			.link-area {
				display: block;
				margin-top: 25px;
				text-align: center;
			}
			
			.spliter {
				color: #bbb;
				padding: 0px 8px;
			}
			
			.oauth-area {
				position: absolute;
				bottom: 20px;
				left: 0px;
				text-align: center;
				width: 100%;
				padding: 0px;
				margin: 0px;
			}
			
			.oauth-area .oauth-btn {
				display: inline-block;
				width: 50px;
				height: 50px;
				background-size: 30px 30px;
				background-position: center center;
				background-repeat: no-repeat;
				margin: 0px 20px;
				/*-webkit-filter: grayscale(100%); */
				border: solid 1px #ddd;
				border-radius: 25px;
			}
			
			.oauth-area .oauth-btn:active {
				border: solid 1px #aaa;
			}
			
			.oauth-area .oauth-btn.disabled {
				background-color: #ddd;
			}
		</style>

	</head>

	<body>
		<header class="mui-bar mui-bar-nav">
			<h1 class="mui-title">登录 TClub</h1>
		</header>
		<div class="mui-content">
			<form id='login-form' class="mui-input-group">
				<div class="mui-input-row">
					<label>账号</label>
					<input id='account' type="text" class="mui-input-clear mui-input" placeholder="请输入账号" value="<?=$username?>">
				</div>
				<div class="mui-input-row">
					<label>密码</label>
					<input id='password' type="password" class="mui-input-clear mui-input" placeholder="请输入密码">
				</div>
			</form>
			<form class="mui-input-group">
				<ul class="mui-table-view mui-table-view-chevron">
					<li class="mui-table-view-cell">
						自动登录
						<div id="autoLogin" class="mui-switch">
							<div class="mui-switch-handle"></div>
						</div>
					</li>
				</ul>
			</form>
			<div class="mui-content-padded">
				<button id='login' class="mui-btn mui-btn-block mui-btn-primary">登录</button>
				<div class="link-area"><a id='reg'>注册账号</a> <span class="spliter">|</span> <a id='forgetPassword'>忘记密码</a>
				</div>
			</div>
			<div class="mui-content-padded oauth-area">

			</div>
		</div>
		<script src="js/mui.min.js"></script>
		<script src="js/mui.enterfocus.js"></script>
		<script src="js/app.js"></script>
		<script>
		<?php
			require_once('func.php');
			echo 'var autoLogin = '.($autoLogin ? 'true' : 'false').";\n";
		?>
			(function($, doc) {
				$.init({
					statusBarBackground: '#f7f7f7'
				});
				$.ready(function(){
					var settings = app.getSettings();
					var state = app.getState();
					//检查 "登录状态/锁屏状态" 结束
					var loginButton = doc.getElementById('login');
					var accountBox = doc.getElementById('account');
					var passwordBox = doc.getElementById('password');
					var autoLoginButton = doc.getElementById("autoLogin");
					var regButton = doc.getElementById('reg');
					var forgetButton = doc.getElementById('forgetPassword');
					loginButton.addEventListener('tap', function(event) {
						var loginInfo = {
							account: accountBox.value,
							password: passwordBox.value
						};
						if(0 != checkUsernamePasswd(loginInfo.account, loginInfo.password))
						{
							return;
						}
						mui.ajax('loginJudge.php', {
							type: 'POST',
							async: true,
							data: {username: loginInfo.account, password: loginInfo.password, isAuto: 0}, 
							dataType: 'data',
							success: function(data, textStatus){
								loginSuccess(data, textStatus);
							},
							error: function(xhr, type, errorThrown)
							{
								loginError(xhr, type, errorThrown);
							}
						});	
					});
					$.enterfocus('#login-form input', function() {
						$.trigger(loginButton, 'tap');
					});
					autoLoginButton.classList[settings.autoLogin ? 'add' : 'remove']('mui-active')
					autoLoginButton.addEventListener('toggle', function(event) {
						setTimeout(function() {
							var isActive = event.detail.isActive;
							settings.autoLogin = isActive;
							app.setSettings(settings);
						}, 50);
					}, false);
					regButton.addEventListener('tap', function(event) {
						$.openWindow({
							url: 'reg.php',
							id: 'reg',
							preload: true,
							show: {
								aniShow: 'pop-in'
							},
							styles: {
								popGesture: 'hide'
							},
							waiting: {
								autoShow: false
							}
						});
					}, false);
					forgetButton.addEventListener('tap', function(event) {
						$.openWindow({
							url: 'forget_password.html',
							id: 'forget_password',
							preload: true,
							show: {
								aniShow: 'pop-in'
							},
							styles: {
								popGesture: 'hide'
							},
							waiting: {
								autoShow: false
							}
						});
					}, false);
					
					if(autoLogin)
					{
						setTimeout(function()
						{
							loginButton.textContent = '自动登录中...';
							loginButton.disabled = true;
							
							mui.ajax('loginJudge.php', {
								type: 'POST',
								async: true,
								data: {identifier: <?='\''.(is_null($identifier) ? '--' : $identifier).'\''?>, token: <?='\''.(is_null($token) ? '--' : $token).'\''?>, isAuto: 1}, 
								dataType: 'json',
								success: function(data, textStatus)
								{
									loginSuccess(data, textStatus);
								},
								error: function(xhr, type, errorThrown)
								{
									loginError(xhr, type, errorThrown);
								}
							});
						}, 500);
					}
				});
			}(mui, document));
			
			function loginSuccess(data, textStatus)
			{
				if(<?=_CM_USERIDENTIFY_ERROR?> & data.code)
				{
					mui.toast('登录失败，请稍侯再试。');
				}
				else if(<?=_CM_USERIDENTIFY_INVALID?> & data.code)
				{
					mui.toast('用户名或密码错误。');
				}
				else if(<?=_CM_USERIDENTIFY_BANNED?> & data.code)
				{
					mui.toast('本账号已被禁止登录。');
				}
				else if(<?=_CM_USERIDENTIFY_NORECOGNISED?> & data.code)
				{
					mui.toast('本账号尚未激活。');
				}
				else
				{
					mui.openWindow({
						url: 'index.php',
						id: 'main',
						preload: true,
						show: {
							aniShow: 'pop-in'
						},
						styles: {
							popGesture: 'hide'
						},
						waiting: {
							autoShow: false
						}
					});
				}
			}
			
			function loginError(xhr, type, errorThrown)
			{
				mui.toast('ERROR: ' + xhr.status + ', ' + xhr.readyState + ', ' + type + ', ' + errorThrown);
			}
		</script>
	</body>

</html>