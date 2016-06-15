<?php
// this is a work by teppei@fygame.com
session_start();
date_default_timezone_set('prc');
// session preserve for 2 hours
$lifeTime = 2 * 3600;
session_set_cookie_params($lifeTime);
$username = '';
$userid = 0;
$coin = 0;
if(isset($_SESSION['username']) && isset($_SESSION['userid']) && isset($_SESSION['coin']))
{
	$username = $_SESSION['username'];
	$userid = $_SESSION['userid'];
	$coin = $_SESSION['coin'];
}
else
{
	// no login, redirect to login page
	$headTo = 'login.php';
	if('' != $_SERVER['QUERY_STRING'])
	{
		$headTo .= '?'.$_SERVER['QUERY_STRING'];
	}
	header('Location: '.$headTo);
}
session_commit();  // close session to prevent block
require_once 'def.php';
$lvStepsArr = json_decode(_LVSTEPS);
$lv = 0;
foreach($lvStepsArr as $lvStepValue)
{
	if($coin < $lvStepValue)
	{
		break;
	}
	$lv++;
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>All in TClub</title>
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">

		<link rel="stylesheet" href="css/mui.min.css">
		<link href="css/mui.picker.css" rel="stylesheet" />
		<link href="css/mui.poppicker.css" rel="stylesheet" />
		<!--<link rel="stylesheet" type="text/css" href="../css/mui.picker.min.css" />-->
		<style>
			.mui-btn {
				font-size: 16px;
				padding: 8px;
				margin: 3px;
			}
			h5.mui-content-padded {
				margin-left: 3px;
				margin-top: 20px !important;
			}
			h5.mui-content-padded:first-child {
				margin-top: 12px !important;
			}
			.ui-alert {
				text-align: center;
				padding: 20px 10px;
				font-size: 16px;
			}
		</style>
	</head>
		<style>
			html,
			body {
				background-color: #efeff4;
			}
			p {
				text-indent: 22px;
			}
			p.prebook {
				line-height: 23px;
				text-align: center;
				font-size: 24px;
				color: #007AFF;
				font-weight: 700;
				padding: 8px;
				margin: 4px auto 4px auto;
			}
			span.mui-icon {
				font-size: 14px;
				color: #007aff;
				margin-left: -15px;
				padding-right: 10px;
			}
			.mui-off-canvas-left {
				color: #fff;
			}
			.title {
				margin: 35px 15px 10px;
			}
			.title+.content {
				margin: 10px 15px 35px;
				color: #bbb;
				text-indent: 1em;
				font-size: 14px;
				line-height: 24px;
			}
			input {
				color: #000;
			}
		</style>
	</head>

	<body>
		<div id="offCanvasWrapper" class="mui-off-canvas-wrap mui-draggable">
			<!--侧滑菜单部分-->
			<aside id="offCanvasSide" class="mui-off-canvas-left">
				<div id="offCanvasSideScroll" class="mui-scroll-wrapper">
					<div class="mui-scroll">
						<div class="title">Hi, <?=$username?></div>
						<div class="content">
							等级：Lv<?=$lv + 1?>，金币：<?=$coin?>。
							<p style="margin: 10px 15px;">
								<button id="logoutBtn" type="button" class="mui-btn mui-btn-danger mui-btn-block" style="padding: 5px 20px;">退出登录</button>
							</p>

						</div>
						<div class="title" style="margin-bottom: 25px;">免费功能</div>
						<ul class="mui-table-view mui-table-view-chevron mui-table-view-inverted">
							<li class="mui-table-view-cell">
								<a class="mui-navigate-right">
									预约构建机
								</a>
							</li>
							<li class="mui-table-view-cell">
								<a class="mui-navigate-right">
									赚金币
								</a>
							</li>
							<li class="mui-table-view-cell">
								<a class="mui-navigate-right">
									敬请期待
								</a>
							</li>
							<li class="mui-table-view-cell">
								<a class="mui-navigate-right">
									敬请期待
								</a>
							</li>
							<li class="mui-table-view-cell">
								<a class="mui-navigate-right">
									敬请期待
								</a>
							</li>
							<li class="mui-table-view-cell">
								<a class="mui-navigate-right">
									敬请期待
								</a>
							</li>
						</ul>
					</div>
				</div>
			</aside>
			<!--主界面部分-->
			<div class="mui-inner-wrap">
				<header class="mui-bar mui-bar-nav">
					<a href="#offCanvasSide" class="mui-icon mui-action-menu mui-icon-bars mui-pull-left"></a>
					<a class="mui-action-back mui-btn mui-btn-link mui-pull-right">关闭</a>
					<h1 class="mui-title">预约构建机</h1>
				</header>
				<div id="offCanvasContentScroll" class="mui-content mui-scroll-wrapper">
					<div class="mui-scroll">
						<div class="mui-content-padded">
							<p>提前预约构建机可以免除你排队等候的烦恼，每次预约最长保留10分钟。</p>
							<button id='showCityPicker' class="mui-btn mui-btn-block" type='button'>选择要预约的构建机</button>
							<p class="prebook" id="prebookInfo" style="display: none;">你已预约构建机：97，09:40后将失效</p>
							<p style="padding: 5px 20px;margin-bottom: 5px;">
								<button id="prebookBtn" type="button" class="mui-btn mui-btn-primary mui-btn-block" style="padding: 10px;">
									约起来
								</button>
							</p>
							<ol>
								<li>同一时间内只能预约一个构建项目</li>
								<li>预约仅对指定的机器有效</li>
								<li>预约成功后，需要在构建选项中填写正确的Token才能构建</li>
							</ol>

						</div>

					</div>
				</div>
				<!-- off-canvas backdrop -->
				<div class="mui-off-canvas-backdrop"></div>
			</div>
		</div>
		<script src="js/mui.min.js"></script>
		<script src="js/mui.picker.js"></script>
		<script src="js/mui.poppicker.js"></script>
		<script src="js/app/preorder_builders.js" type="text/javascript" charset="utf-8"></script>
		<script>	
		<?php
			echo "const _CM_POSTPARAM_ERROR = "._CM_POSTPARAM_ERROR.";";
			echo "const _CM_USERIDENTIFY_ERROR = "._CM_USERIDENTIFY_ERROR.";";
			echo "const _PREORDER_MASK_COINNOTENOUGH = "._PREORDER_MASK_COINNOTENOUGH.";";
			echo "const _PREORDER_MASK_OTHEROCCUPIED = "._PREORDER_MASK_OTHEROCCUPIED.";";
			echo "const _PREORDER_MASK_SELFPREORDERED = "._PREORDER_MASK_SELFPREORDERED.";";
			echo "const _PREORDERPRICE = "._PREORDERPRICE.";";
			echo "const _PREORDERTIMELIFE = "._PREORDERTIMELIFE.";";
		?>		
			mui.init();
			 //侧滑容器父节点
			var offCanvasWrapper = mui('#offCanvasWrapper');
			 //主界面容器
			var offCanvasInner = offCanvasWrapper[0].querySelector('.mui-inner-wrap');
			 //菜单容器
			var offCanvasSide = document.getElementById("offCanvasSide");
			 //移动效果是否为整体移动
			var moveTogether = false;
			 //侧滑容器的class列表，增加.mui-slide-in即可实现菜单移动、主界面不动的效果；
			var classList = offCanvasWrapper[0].classList;
			 //变换侧滑动画移动效果；
			offCanvasSide.classList.remove('mui-transitioning');
			offCanvasSide.setAttribute('style', '');
			classList.remove('mui-slide-in');
			classList.remove('mui-scalable');
			moveTogether = true;
			//整体滑动时，侧滑菜单在inner-wrap内
			offCanvasInner.insertBefore(offCanvasSide, offCanvasInner.firstElementChild);
			offCanvasWrapper.offCanvas().refresh();
			 //主界面‘我要预约’按钮的点击事件
			document.getElementById('prebookBtn').addEventListener('tap', function() {
				// 执行预约
				if(0 == app.preorder_builders.current.value)
				{
					mui.toast('请选择要预约的构建机');
				}
				else
				{
					mui.ajax('app/preorder.php', {
						type: 'POST',
						async: true,
						data: {userid: <?=$userid?>, builder: app.preorder_builders.current.value}, 
						dataType: 'json',
						success: function(data, textStatus)
						{
							if(0 == data.code)
							{
								if(0 == data.actionCode)
								{
									mui.toast('预约成功。');
								}
								else if(_PREORDER_MASK_COINNOTENOUGH & data.actionCode)
								{
									mui.toast('金币不足。');
								}
								else if(_PREORDER_MASK_OTHEROCCUPIED & data.actionCode)
								{
									mui.toast('已被抢先预约。');
								}
								else if(_PREORDER_MASK_SELFPREORDERED & data.actionCode)
								{
									mui.toast('请勿重复预约。');
								}
								else
								{
									mui.toast('预约失败。');
								}
							}
							else
							{
								if(_CM_POSTPARAM_ERROR & data.code)
								{
									mui.toast('参数错误。');
								}
								else if(_CM_USERIDENTIFY_ERROR & data.code)
								{
									mui.toast('用户状态错误。')
								}
								else
								{
									mui.toast('预约失败。');
								}								
							}
						},
						error: function(xhr, type, errorThrown)
						{
							mui.toast('ERROR: ' + xhr.status + ', ' + xhr.readyState + ', ' + type + ', ' + errorThrown);
						}
					});	
					// 预约成功，显示倒计时和取消预约
					var showCityPickerButton = document.getElementById('showCityPicker');
					showCityPickerButton.style.display = "none";
					var prebookInfo = document.getElementById("prebookInfo");
					prebookInfo.style.display = 'block';
					
					var prebookButton = document.getElementById('prebookBtn');
					prebookButton.textContent = '不约了';
					prebookButton.classList.remove('mui-btn-primary');
					prebookButton.classList.add('mui-btn-danger');
				}
				
			});
			 //菜单界面，‘退出登录’按钮的点击事件
			document.getElementById('logoutBtn').addEventListener('tap', function() {
				location.href = 'logout.php';
			});
			 //主界面和侧滑菜单界面均支持区域滚动；
			mui('#offCanvasSideScroll').scroll();
			mui('#offCanvasContentScroll').scroll();
			 //实现ios平台原生侧滑关闭页面；
			if (mui.os.plus && mui.os.ios) {
				mui.plusReady(function() { //5+ iOS暂时无法屏蔽popGesture时传递touch事件，故该demo直接屏蔽popGesture功能
					plus.webview.currentWebview().setStyle({
						'popGesture': 'none'
					});
				});
			}
			
			(function($, doc) {
				$.init();
				$.ready(function() {
					var cityPicker = new $.PopPicker();
					cityPicker.setData(builderData);
					var showCityPickerButton = doc.getElementById('showCityPicker');
					showCityPickerButton.addEventListener('tap', function(event) {
						cityPicker.show(function(items) {
							app.preorder_builders.current.value = items[0].value;
							showCityPickerButton.textContent = "你预约的构建机是:" + items[0].text;
							//返回 false 可以阻止选择框的关闭
							//return false;
						});
					}, false);
				});
			})(mui, document);
		</script>
	</body>
</html>