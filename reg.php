<?php
require_once 'def.php';
?>
<!DOCTYPE html>
<html class="ui-page-login">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title></title>
		<link href="css/mui.min.css" rel="stylesheet" />
		<style>
			.area {
				margin: 20px auto 0px auto;
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
			
		</style>
	</head>

	<body>
		<header class="mui-bar mui-bar-nav">
			<a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
			<h1 class="mui-title">注册</h1>
		</header>
		<div class="mui-content">
			<form class="mui-input-group">
				<div class="mui-input-row">
					<label>账号</label>
					<input id='account' type="text" class="mui-input-clear mui-input" placeholder="请输入账号">
				</div>
				<div class="mui-input-row">
					<label>密码</label>
					<input id='password' type="password" class="mui-input-clear mui-input" placeholder="请输入密码">
				</div>
				<div class="mui-input-row">
					<label>确认</label>
					<input id='password_confirm' type="password" class="mui-input-clear mui-input" placeholder="请确认密码">
				</div>
			</form>
			<div class="mui-content-padded">
				<button id='reg' class="mui-btn mui-btn-block mui-btn-primary">注册</button>
			</div>
			<div class="mui-content-padded">
				<p>注册成为会员将获得500金币。</p>
			</div>
		</div>
		<script src="js/mui.min.js"></script>
		<script src="js/app.js"></script>
		<script>
		<?php
			require_once('func.php');
		?>
			(function($, doc) {
				$.init();
				$.ready(function() {
					var toLogin = function() {
						$.openWindow({
							url: 'login.php',
							id: 'login',
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
					var settings = app.getSettings();
					var regButton = doc.getElementById('reg');
					var accountBox = doc.getElementById('account');
					var passwordBox = doc.getElementById('password');
					var passwordConfirmBox = doc.getElementById('password_confirm');
					regButton.addEventListener('tap', function(event) {
						var accountValue = accountBox.value;
						var passwdValue = passwordBox.value;
						if(0 != checkUsernamePasswd(accountValue, passwdValue))
						{
							return;
						}
						var passwordConfirm = passwordConfirmBox.value;
						if (passwordConfirm != passwdValue) {
							$.toast('密码两次输入不一致。');
							return;
						}
						mui.ajax('regIssue.php', {
								type: 'POST',
								async: true,
								data: {username: accountValue, password: passwdValue}, 
								dataType: 'json',
								success: function(data, textStatus)
								{
									console.log('data = %s', JSON.stringify(data));
									if(0 == data.code)
									{
										mui.alert('点击确定登录。', '注册成功', function() {
											toLogin();
										});
									}
									else if(<?=_REG_MASK_EXIST?> & data.actionCode)
									{
										$.toast('用户名已存在。');
									}
									else
									{
										$.toast('注册失败。');
									}
								},
								error: function(xhr, type, errorThrown)
								{
									$.toast('ERROR: ' + xhr.status + ', ' + xhr.readyState + ', ' + type + ', ' + errorThrown);
								}
							});	
					});
				});
			}(mui, document));
		</script>
	</body>

</html>