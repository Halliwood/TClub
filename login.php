<?php
require_once 'def.php';
$username = '';
if(isset($_COOKIE['tclub_login']))
{
	$cookieArr = explode('|', $_COOKIE['tclub_login']);
	$username = $cookieArr[0];
}
?>
<!DOCTYPE html>
<html class="ui-page-login">
	<head>
		<meta content="text/html" charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title></title>
		<link href="css/mui.min.css" rel="stylesheet" />
		<link href="css/style.css" rel="stylesheet" />
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
			echo "const _CM_USERIDENTIFY_ERROR = "._CM_USERIDENTIFY_ERROR.";";
			echo "const _CM_USERIDENTIFY_INVALID = "._CM_USERIDENTIFY_INVALID.";";
			echo "const _CM_USERIDENTIFY_BANNED = "._CM_USERIDENTIFY_BANNED.";";
			echo "const _CM_USERIDENTIFY_ADMIN = "._CM_USERIDENTIFY_ADMIN.";";
		?>
			(function($, doc) {
				$.init({
					statusBarBackground: '#f7f7f7'
				});
				$.ready(function(){
					var settings = app.getSettings();
					var state = app.getState();
					var toMain = function() {
						$.openWindow({
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
					};
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
						app.checkLoginParam(loginInfo, function(err) {
							if (err) {
								$.toast(err);
								return;
							}
							mui.ajax('loginJudge.php', {
								type: 'POST',
								async: true,
								data: {username: loginInfo.account, password: loginInfo.password}, 
								dataType: 'json',
								success: function(data, textStatus)
								{
									if(_CM_USERIDENTIFY_ERROR & data.code)
									{
										$.toast('登录失败，请稍侯再试。');
									}
									else if(_CM_USERIDENTIFY_INVALID & data.code)
									{
										$.toast('用户名或密码错误。');
									}
									else if(_CM_USERIDENTIFY_BANNED & data.code)
									{
										$.toast('本账号已被禁止登录。');
									}
									else
									{
										toMain();
									}
								},
								error: function(xhr, type, errorThrown)
								{
									$.toast('ERROR: ' + xhr.status + ', ' + xhr.readyState + ', ' + type + ', ' + errorThrown);
								}
							});							
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
							url: 'reg.html',
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
					//
					window.addEventListener('resize', function() {
						oauthArea.style.display = document.body.clientHeight > 400 ? 'block' : 'none';
					}, false);
					//
				});
			}(mui, document));
		</script>
	</body>

</html>