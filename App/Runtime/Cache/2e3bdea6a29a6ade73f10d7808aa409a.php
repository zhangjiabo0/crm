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
		<h4><?php echo L('SHORT_MESSAGE_VIEW');?></h4>
	</div>
	<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
	<div class="row">
		<div class="tabbable span12">
			<div class="tab-content">
					<table class="table table-hover">
						<thead>
							<tr> 
								<td>&nbsp;</td>
								<td <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?>><?php if(session('role_id')==$info['to_role_id'] || $info['to_role_id'] = 0): ?><a class="btn btn-primary" id="reply" href="javascript:void(0);"/><?php echo L('REPLY');?></a><?php endif; ?>&nbsp;
								<a class="btn" id="delete" href="<?php echo U('message/delete', 'id='.$info['message_id']);?>"/><?php echo L('DELETE');?></a> &nbsp;<a class="btn" href="<?php echo U('message/index');?>"><?php echo L('RETURN');?></a></td>
							</tr>
						</thead>
						<tbody>
							<tr><th colspan="4"><?php echo L('SHORT_MESSAGE');?></th></tr>
							<tr> 
								<td class="tdleft" width="15%"><?php echo L('THE_SENDERS');?></td>
								<td width="35%"><?php if(!empty($info["from_role_id"])): echo ($info["from_name"]); else: echo L('SYSTEM_ADMINISTRATOR'); endif; ?></td>
								<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
								<td class="tdleft" width="15%"><?php echo L('SEND_TIMES');?></td>
								<td width="35%"><?php echo (date('Y-m-d H:i:s',$info["send_time"])); ?></td>
							</tr>
							<tr>
								<td class="tdleft" width="15%"><?php echo L('CONTENTSS');?></td>
								<td <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?>><pre><?php echo ($info["content"]); ?></pre></td>
							</tr>
							<tr>
								<td <?php if(C('ismobile') == 1): ?>colspan="2"<?php else: ?>colspan="4"<?php endif; ?>></td>
							</tr>
						</tbody>
					</table>
			</div>
		</div>
	</div>
</div>
<div class="hide" id="dialog-send" title="<?php echo L('WRITE_LETTER');?>">loading...</div>

<script type="text/javascript">
<?php if(C('ismobile') == 1): ?>width=$('.container').width() * 0.9;<?php else: ?>width=800;<?php endif; ?> 
$("#dialog-send").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight: 400,
	position: ["center",100]
});
$(function(){
	$("#reply").click(function(){
		$('#dialog-send').dialog('open');
		$('#dialog-send').load('<?php echo U("message/send");?>&from_role_id='+<?php echo ($info["from_role_id"]); ?>);
	});
	$("#delete").click(function(){
	if(!confirm("<?php echo L('ARE_YOU_SRUE_DELETE');?>")){
		return false;
	}
})
});
</script>

</body>
</html>