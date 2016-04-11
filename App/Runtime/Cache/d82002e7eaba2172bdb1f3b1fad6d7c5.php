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
<script type="text/javascript" src="<?php echo ($APP_PATH); ?>/resources/scripts/region.js"></script>
<script type="text/javascript" src="<?php echo ($APP_PATH); ?>/resources/scripts/jq_area.js"></script>
<div class="container">
	<div class="page-header">
		<h4><?php echo L('INFORM THE DETAILS');?></h4>
	</div>
	<div class="row">
		<div class="span12">
			<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
			<input type="hidden" name="announcement_id" value="<?php echo ($announcement["announcement_id"]); ?>"/>
				<table class="table" width="95%" border="0" cellspacing="1" cellpadding="0">
					<thead>
						<tr>
							<td colspan="2"><a class="btn btn-primary" href="<?php echo U('announcement/edit','id='.$announcement['announcement_id']);?>"><?php echo L('EDIT');?></a>&nbsp; <a class="btn btn-primary" href="<?php echo U('announcement/delete','id='.$announcement['announcement_id']);?>"><?php echo L('DELETE');?></a>&nbsp; <a class="btn" href="javascript:history.go(-1)"><?php echo L('RETURN');?></a></td>
						</tr>
					</thead>
					<tbody width="100%">
						<tr colspan="2"><td colspan="2"><span style="font-size:18px;"><?php echo ($announcement["title"]); ?>&nbsp; <span style="font-size:12px;"><?php if($announcement['status'] == 1): ?>(<span style="color:green;"><?php echo L('RELEASE');?></span>)<?php else: ?>(<span style="color:green;"><?php echo L('HAS BEEN DISCONTINUED');?></span>)<?php endif; ?></span></span> &nbsp; &nbsp;  <?php if($pre_href): ?><a href="<?php echo ($pre_href); ?>"><?php echo L('IN THE PREVIOUS');?></a><?php endif; ?> &nbsp;  &nbsp;  &nbsp;<?php if($next_href): ?><a href="<?php echo ($next_href); ?>"><?php echo L('THE NEXT ARTICLE');?></a><?php endif; ?></td></tr>
						<tr>
							<td width="7%" class="tdleft"><?php echo L('AUTHOR INFORMATION');?></td>						
							<td><?php if(!empty($announcement["owner"]["user_name"])): ?><a class="role_info" rel="<?php echo ($announcement["owner"]["role_id"]); ?>" href="javascript:void(0)"><?php echo ($announcement["owner"]["user_name"]); ?></a><?php endif; ?>
							&nbsp;&nbsp;&nbsp;&nbsp;<if condition="$announcement['update_time'] gt 0"><?php echo L('TIME'); echo (date("Y-m-d",$announcement["update_time"])); ?></notempty>
							</td>
						</tr>
						<tr>
							<td class="tdleft">
								<?php echo L('INFORM THE DEPARTMENT');?>:
							</td>
							<td>
								<?php if(is_array($department_list)): $i = 0; $__LIST__ = $department_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['checked'] == 'checked'): echo ($vo['name']); ?>、&nbsp;  &nbsp;<?php endif; endforeach; endif; else: echo "" ;endif; ?>
							</td>
						</tr>
						<tr>
							<td class="tdleft"><?php echo L('CONTENT');?></td>
							<td><?php echo ($announcement["content"]); ?></td>
						</tr>
					</tbody>
				</table>
		</div>
	</div>
</div>
<div class="hide" id="dialog-role-info" title="<?php echo L('DIALOG_USER_INFO');?>">loading...</div>
<script type="text/javascript">
$("#dialog-transform").dialog({
    autoOpen: false,
    modal: true,
	width: 600,
	position: ["center",100],
	maxHeight: 400,
	buttons: {
		"Ok": function () {
			$("#transform").submit();
			$(this).dialog("close");
		},
		"Cancel": function () {
			$(this).dialog("close");
		}
	}
});
$("#dialog-role-info").dialog({
    autoOpen: false,
    modal: true,
	width: 600,
	maxHeight: 400,
	position: ["center",100]
});
$(function(){
	$(".role_info").click(function(){
		$role_id = $(this).attr('rel');
		$('#dialog-role-info').dialog('open');
		$('#dialog-role-info').load('<?php echo U("user/dialoginfo","id=");?>'+$role_id);
	});
});
</script>

</body>
</html>