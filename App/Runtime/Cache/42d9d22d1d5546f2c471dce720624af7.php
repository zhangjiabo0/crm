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
<script type="text/javascript" src="__PUBLIC__/js/kindeditor-all-min.js"></script>
<link rel="stylesheet" href="__PUBLIC__/css/kindeditor.css" type="text/css" />
<script type="text/javascript">
	<?php if(C('ismobile') != 1): ?>var editor;
		KindEditor.ready(function(K) {
			editor = K.create('textarea[name="content"]', {
				uploadJson:'<?php echo U("file/editor");?>',
				allowFileManager : true,
				loadStyleMode : false,
				fileManagerJson: "<?php echo U('file/manager');?>",
				urlType:"domain"
			});
		});<?php endif; ?>
</script>
<div class="container">
	<div class="page-header" style="border:none; font-size:14px; ">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('setting/sendsms');?>"><?php echo L('SEND_SMS');?></a></li>
			<li ><a href="<?php echo U('setting/smsrecord');?>"><?php echo L('SMS_RECORD');?></a></li>
			<li class="active"><a href="<?php echo U('setting/sendemail');?>"><?php echo L('SEND_EMAIL');?></a></li>
		</ul>
	</div>
	<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>	
	<?php if(!empty($contacts)): $first = 0; ?>
			<?php if(is_array($contacts)): $i = 0; $__LIST__ = $contacts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(!is_email($vo['email'])): if($first==0){ $first = 1; }else{ $first = 2; } ?>
					<?php if($first == 1): ?><div class="alert alert-warning"><?php echo L('INVALIDATE_EMAIL_HAVE_BEEN_FILTER');?><br/><?php echo L('DETAILS_SEE_BLOW');?>:<?php endif; ?>
					<?php echo (trim($vo['email'])); ?> &nbsp; <?php echo ($vo['name']); ?>[<?php echo L('CUSTOMER');?>:<?php echo ($vo['customer_name']); ?>]、<?php endif; endforeach; endif; else: echo "" ;endif; ?>
			<?php if($first != 0): ?></div><?php endif; endif; ?>
	<div class="row">
		<div class="span12">
		</div>
		<div>
			<div class="span2 warning pull-left" style="background-color:#f5f5f5;">
				<pre><h4><?php echo L('OPERATING_TIPS');?></h4><?php echo L('EMAIL_TIPS_ONE');?><span style="color:red"><br/>123@xyb2c.com<br/>321@xyb2c.com</span><br/><?php echo L('EMAIL_TIPS_TWO');?><span style="color:red">{</span><span style="color:red">name}</span><?php echo L('INSTEAD');?><br><span style="color:red"><?php echo L('EMAIL_TIPS_TWO_NOTIC');?><br/>123@xyb2c.com,<?php echo L('ZHANGSAN');?><br/>321@xyb2c.com,<?php echo L('LISI');?></span><br/><?php echo L('EMAIL_TIPS_TWO_NOTICS');?><br/>3、<span style="color:red"><?php echo L('EMAIL_TIPS_THREE');?></span>
				</pre>
			</div>
			<form  action="<?php echo U('setting/sendemail');?>" method="post">
			<div class="pull-left">
				<div class="pull-left" style="margin-left:30px;">
					<div class="alert-info alert" style="margin:0px;"><?php echo L('EMAIL_TIPS_THREE_EXTRA');?>
					<br><?php echo L('EMAIL_TIPS_THREE_EXTRAS');?></div>
					<div><textarea id="emails" name="emails" style="min-height: 375px;width:200px;"><?php if(!empty($contacts)): if(is_array($contacts)): $i = 0; $__LIST__ = $contacts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(is_email($vo['email'])): echo (trim($vo['email'])); ?>,<?php echo ($vo['name']); ?>,<?php echo ($vo['customer_name']); echo chr(10); endif; endforeach; endif; else: echo "" ;endif; endif; ?></textarea></div>
				</div>
				<div class="pull-left" style="margin-left:30px;">
					<p>
						<?php echo L('CHANCE_SEND_BOX');?><select name="smtp" id="smtp" style="width:auto;font-size:12px;">
							<?php if(is_array($smtpList)): $i = 0; $__LIST__ = $smtpList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v['smtp_id']); ?>"><?php echo ($v['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
						<a href="<?php echo U('email/smtp');?>" style="color:red;"><?php echo L('SETTING');?></a> &nbsp;  &nbsp;  &nbsp;  &nbsp; 
						
						<?php echo L('CHANCE_EMAIL_TEMPLATE');?><select name="template" id="template" style="width:auto;font-size:12px;" onchange="changeContent()">
							<option><?php echo L('SELECT_EMAIL_TPA');?></option>
							<?php if(is_array($templateList)): $i = 0; $__LIST__ = $templateList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v['template_id']); ?>" rel="<?php echo ($v['content']); ?>" id="<?php echo ($v['title']); ?>"><?php echo ($v['subject']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
						<a href="<?php echo U('email/index');?>" style="color:red;"><?php echo L('SETTING');?></a>
					</p>
					<div><?php echo L('EMAIL_TITLE');?><br><input id="title" name="title" style="width:690px;"></input></div>
					<div><?php echo L('EMAIL_CONENT');?><br>
					<textarea id="contented" name="content" placeholder="<?php echo L('PLEASE_READ_OPERATING_TIPS');?>" style="height: 300px;width:700px;"></textarea><br>
					<input type="submit" class="btn btn-primary" value="<?php echo L('SEND');?>"/> &nbsp; 
					</div>
				</div>
			</div>
			</form>
		</div>
	</div> <!-- End #main-content -->
</div>
<script type="text/javascript">
/*function sub(){
	var img = new Array();
	var file = new Array();
	img = $("iframe").contents().find("img").each(function() {
				img.push($(this).attr("data-ke-src"));
			});
	file = $("iframe").contents().find("a").each(function() {
				file.push($(this).attr("href"));
			});
	if(img){
		$.post('<?php echo U("setting/sendemail");?>',
		{img:img,file:file},
		function(data){
			alert(data.info);
		},
		'json');
	}
}*/
/*$(function(){
	$('#submit').click(
		function(){
			var img=[];
			var file=[];
			$("iframe").contents().find("img").each(function(){
				img.push($(this).attr("src"));
			});
			$("iframe").contents().find("a").each(function(){
				file.push($(this).attr("data-ke-src"));
			});
			 
			if(img){
				$.post('<?php echo U("setting/sendemail");?>',
				{img:img,file:file,title:title,emails:emails,content:content},
				function(data){
					alert(data.info);
				},
				'json');
			}

		}
	);
});*/
function changeContent(){
	var a = $('#template option:selected').attr('rel');
	var b = $('#template option:selected').attr('id');
	if(a){
		$('#title').val(b);
		$("iframe").contents().find("body").html(a);
	}else{
		$('#title').val('');
		$("iframe").contents().find("body").html('');
	}
	
}
</script>

</body>
</html>