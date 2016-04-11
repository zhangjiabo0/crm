<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<title><?php echo C('defaultinfo.name');?> - Powered By <?php echo L('AUTHOR');?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta name="description" content=""/>
	<meta name="author" content="<?php echo L('AUTHOR');?>"/>
	<link type="text/css" href="__PUBLIC__/css/bootstrap.min.css?t=20140830" rel="stylesheet" />
	<link type="text/css" href="__PUBLIC__/css/bootstrap-responsive.min.css?t=20140830" rel="stylesheet">
	<link type="text/css" href="__PUBLIC__/css/jquery-ui-1.10.0.custom.css?t=20140830" rel="stylesheet" />
	<link type="text/css" href="__PUBLIC__/css/font-awesome.min.css?t=20140830" rel="stylesheet" />
	<link class="docs" href="__PUBLIC__/css/docs.css?t=20140830" rel="stylesheet"/>
	<link rel="shortcut icon" href="__PUBLIC__/ico/favicon.png"/>
    <script type="text/javascript">
        var browserInfo = {browser:"", version: ""};
        var ua = navigator.userAgent.toLowerCase();
        if (window.ActiveXObject) {
            browserInfo.browser = "IE";
            browserInfo.version = ua.match(/msie ([\d.]+)/)[1];
            if(browserInfo.version <= 7){
                if(confirm("您的ie浏览器版本过低，建议使用chorme浏览器")){}
            }
        }
    </script>
	<!--[if lt IE 9]>
	<script src="__PUBLIC__/js/respond.min.js" type="text/javascript"></script>
	<![endif]-->
	<script src="__PUBLIC__/js/jquery-1.9.0.min.js?t=20140830" type="text/javascript"></script>
	<script src="__PUBLIC__/js/bootstrap.min.js?t=20140830" type="text/javascript"></script>
	<script src="__PUBLIC__/js/jquery-ui-1.10.0.custom.min.js?t=20140830" type="text/javascript"></script>
	<script src="__PUBLIC__/js/WdatePicker.js?t=20140830" type="text/javascript"></script>
	<script src="__PUBLIC__/js/gototop.js?t=20140830" type="text/javascript"></script>
	<script src="__PUBLIC__/js/crm_zh-cn.js?t=20140830" type="text/javascript"></script>
	<script src="__PUBLIC__/js/crm.js?t=20140830" type="text/javascript"></script>
	<!--[if lte IE 6]>
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/bootstrap-ie6.css">
	<![endif]-->
	<!--[if lte IE 7]>
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/ie.css">
	<![endif]-->
	<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/font-awesome-ie7.min.css" />
	<![endif]-->
	<!--[if lt IE 9]>
	<link type="text/css" href="__PUBLIC__/css/jquery.ui.1.10.0.ie.css" rel="stylesheet"/>
	<script src="__PUBLIC__/js/ie8-eventlistener.js" type="text/javascript"></script>
	<![endif]-->	
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
</head>

<body data-spy="scroll" data-target=".bs-docs-sidebar" data-twttr-rendered="true">
<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<div style="line-height: 40px;padding-right: 5px;padding-top: 6px;" class="pull-left"><img src="__PUBLIC__/img/logomini.png"/></div>
			<a class="brand" href="<?php echo (__APP__); ?>" alt="<?php echo C('defaultinfo.description');?>"><?php echo C('defaultinfo.name');?></a>
			<?php echo W("Navigation");?>
		</div> 
	</div>
</div>
<div class="container">
	<div class="page-header">
		<h4><?php echo L('SYSTEM_SETTING');?></h4>
	</div>
	<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>	
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('setting/defaultInfo');?>"><?php echo L('BASIC_SYSTEM_SETTING');?></a></li>
			<li class="active"><a href="<?php echo U('setting/smtp');?>"><?php echo L('SMTP_SETTING');?></a></li>
			<li><a href="<?php echo U('setting/fields');?>"><?php echo L('CUSTOMIZING_FIELDS_SETTING');?></a></li>		
			<li><a href="<?php echo U('navigation/setting');?>"><?php echo L('SYSTEM_NAVIGATION_SETTING');?></a></li>
			<li><a href="<?php echo U('setting/appsetting');?>">APP接口配置</a></li>
		</ul>
		<div class="row">
		<form class="form-horizontal" action="<?php echo U('setting/smtp');?>" method="post">
			<table class="span6 table">
				<tbody>
					<tr>
						<th colspan="2"><?php echo L('SMTP_BASIC_SETTING');?></th>
					</tr>
					<tr>
						<td class="tdleft"><?php echo L('EMAIL_ADDRESS');?></td>  
						<td>
							<input name="address" id="address" type="text" value="<?php echo ($smtp['MAIL_ADDRESS']); ?>"/> <span style="color:red;"><?php echo L('MUST_FILL_IN');?></span>
						</td>
					</tr>
					<tr>
						<td class="tdleft"><?php echo L('SMTP_SERVER_ADDRESS');?></td>  
						<td>
							<input value="<?php echo ($smtp['MAIL_SMTP']); ?>" id="smtp" name="smtp" type="text"> <span style="color:red;"><?php echo L('MUST_FILL_IN');?></span>&nbsp;&nbsp;&nbsp;&nbsp;<input value="ssl" id="secure" name="secure" type="checkbox" <?php if($smtp['MAIL_SECURE'] == 'ssl'): ?>checked="checked"<?php endif; ?>> SSL
						</td>
					</tr>
                    <tr>
						<td class="tdleft"><?php echo L('SMTP_SERVER_PORT');?></td>  
						<td>
							<input value="<?php echo (($smtp['MAIL_PORT'])?($smtp['MAIL_PORT']):25); ?>" id="port" name="port" type="text"> <span style="color:red;"><?php echo L('MUST_FILL_IN');?></span>
						</td>
					</tr>
					<tr>
						<td class="tdleft"><?php echo L('LOGIN_NAME');?></td>  
						<td>
							<input value="<?php echo ($smtp['MAIL_LOGINNAME']); ?>" id="loginName" name="loginName" type="text"/> <span style="color:red;"><?php echo L('MUST_FILL_IN');?></span>
						</td>
					</tr>
					<tr>
						<td class="tdleft"><?php echo L('PASSWORD');?></td>  
						<td>
							<input value="<?php echo ($smtp['MAIL_PASSWORD']); ?>" id="password" name="password" type="password"> <span style="color:red;"><?php echo L('MUST_FILL_IN');?></span>
						</td>
					</tr>
					<tr>
						<td class="tdleft"><?php echo L('TEST_EMAIL');?>:</td>  
						<td>
							<input name="test_email" id="test_email" type="text"/> &nbsp; <input class="btn btn-mini" id="test" name="submit" type="button" value="<?php echo L('TEST');?>">
						</td>
					</tr>
					<tr>
						<th colspan="2"><?php echo L('SMS_CONFIGURATION_INFORMATION');?></th>
					</tr>
					<tr>
						<td class="tdleft"><?php echo L('THE_USER_NAME_TEXT_INTERFACE');?></td>  
						<td>
							<input name="uid" id="uid" type="text" value="<?php echo ($sms['uid']); ?>"/>
						</td>
					</tr>
					<tr>
						<td class="tdleft"><?php echo L('SMS_INTERFACE_PASSWORD');?></td>  
						<td>
							<input value="<?php echo ($sms['passwd']); ?>" id="passwd" name="passwd" type="password">
						</td>
					</tr>
					<tr>
						<td class="tdleft"><?php echo L('CUSTOMER_MESSAGE_SIGNATURE');?></td>  
						<td>
							<input value="<?php echo ($sms['sign_name']); ?>" maxlength="8" id="sign_name" name="sign_name" type="text">
						</td>
					</tr>
					<tr>
						<td class="tdleft"><?php echo L('INTERNAL_NOTIFICATION_MESSAGE_SIGNATURE');?></td>  
						<td>
							<input value="<?php echo ($sms['sign_sysname']); ?>" maxlength="8" id="sign_sysname" name="sign_sysname" type="text">
						</td>
					</tr>
					<tr>
						<td class="tdleft"><?php echo L('TEST_PHONE');?></td>  
						<td>
							<input name="test_sms_phone" id="test_sms_phone" type="text"/> &nbsp; <input class="btn btn-mini" id="test_sms_btn" type="button" value="<?php echo L('SEND_TEST_SMS');?>"/>
						</td>
					</tr>
					
					<tr>
						<td>&nbsp;</td>
						<td>
							<input name="submit" class="btn btn-primary" type="submit" value="<?php echo L('SAVE');?>"/>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
		</div>
	</div> <!-- End #main-content -->
</div>
<script type="text/javascript">	
	$('#test').click(
		function(){
			address = $('#address').val();
			smtp = $('#smtp').val();
			port = $('#port').val();
			secure = $('#secure:checked').val();
			name = $('#loginName').val();
			pw = $('#password').val();
			email = $('#test_email').val();
			if(address !='' && smtp !='' && port !='' && name!='' && pw!='' && email!=''){
				$.post('<?php echo U("setting/smtp");?>',
				{   address:address,
					smtp:smtp,
					port:port,
					secure:secure,
					loginName:name,
					password:pw,
					test_email:email},
				function(data){
					alert(data.info);
				},
				'json');
			} else {
				alert('<?php echo L("PLEASE_FILL_IN_COMPLETE_INFORMATION");?>');
			}
		}
	);
	$('#test_sms_btn').click(
		function(){
			uid = $('#uid').val();
			passwd = $('#passwd').val();
			phone = $('#test_sms_phone').val();
			if(uid !='' && passwd !='' && phone !=''){
				$.post('<?php echo U("setting/smtp");?>',
				{   uid:uid,
					passwd:passwd,
					phone:phone},
				function(data){
					alert(data.info);
				},
				'json');
			} else {
				alert('<?php echo L("PLEASE_FILL_IN_COMPLETE_INFORMATION");?>');
			}
		}
	);
</script>

</body>
</html>