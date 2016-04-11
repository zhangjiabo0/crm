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
<style>
table tbody tr{cursor:move;}
</style>
	<div class="container">
		<div class="page-header">
			<h4><?php echo L('SYSTEM_SETTINGS');?></h4>
		</div>
		<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('setting/defaultInfo');?>"><?php echo L('SYSTEM_BASIC_SETTINGS');?></a></li>
			<li><a href="<?php echo U('setting/smtp');?>"><?php echo L('SMTP_SETTINGS');?></a></li>
			<li><a href="<?php echo U('setting/fields');?>"><?php echo L('CUSTOMIZING_FIELDS_SETTING');?></a></li>		
			<li class="active"><a href="<?php echo U('navigation/setting');?>"><?php echo L('SYSTEM_MENU_SETTINGS');?></a></li>
			<li><a href="<?php echo U('setting/appsetting');?>">APP接口配置</a></li>
		</ul>
		<form action="<?php echo U('navigation/delete');?>" method="post">
		<div class="row">
			<div class="span12">
				<div class="nav pull-left">
					<button type="submit" class="btn"><i class="icon-remove"></i>&nbsp;<?php echo L('DELETE');?></button>&nbsp; <a id="sort_btn" class="btn"><i class=" icon-file"></i>&nbsp;<?php echo L('SAVE_THE_LOCATION');?></a>
				</div>
				<div class="pull-right">
					<a class="btn btn-primary" id="add_navigation"><i class="icon-plus"></i>&nbsp; <?php echo L('ADD_NAVIGATION_MENU');?></a>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span4" id="postion_top">
				<h4><?php echo L('NAVIGATION_LOCATION_AT_THE_TOP');?></h4>
				<table class="table table-hover">
					<thead>
						<tr>
						   <th width="25%"><input class="check_all" type="checkbox" /> &nbsp; <?php echo L('CHECK_ALL');?></th>
						   <th width="25%"><?php echo L('MENU');?></th>
						   <th><?php echo L('LINK');?></th>						   
						</tr>
					</thead>
					<tbody>
						<?php if(empty($postion["top"])): ?><tr><td colspan="3"><?php echo L('THIS_POSITION_IS_NOT_TO_ADD_THE_MENU');?></td></tr>
						<?php else: ?>
						<?php if(is_array($postion["top"])): $i = 0; $__LIST__ = $postion["top"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr><td><input type="checkbox" class="list" name="list[]" value="<?php echo ($vo["id"]); ?>"/> &nbsp; 
								<a class="edit" href="javascript:void(0);" rel="<?php echo ($vo["id"]); ?>"><?php echo L('EDITING');?></a>  &nbsp;
								</td>
								<td><?php echo ($vo["title"]); ?></td>
								<td><a href="<?php echo ($vo["url"]); ?>" target="_blank"><?php if(strlen($vo['url']) > 25): echo (substr($vo["url"],0,25)); ?>...<?php else: echo ($vo["url"]); endif; ?></a></td>
							</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
					</tbody>
				</table>
			</div>
			<div class="span4" id="postion_more">
				<h4><?php echo L('NAVIGATION_LOCATION_MORE');?></h4>
				<table class="table table-hover">
					<thead>
						<tr>
						   <th width="25%"><input class="check_all" type="checkbox" /> &nbsp; <?php echo L('CHECK_ALL');?></th>
						   <th width="25%"><?php echo L('MENU');?></th>
						   <th><?php echo L('LINK');?></th>						   
						</tr>
					</thead>
					<tbody>
						<?php if(empty($postion["more"])): ?><tr><td colspan="3"><?php echo L('THIS_POSITION_IS_NOT_TO_ADD_THE_MENU');?></td></tr>
						<?php else: ?>
						<?php if(is_array($postion["more"])): $i = 0; $__LIST__ = $postion["more"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr><td><input type="checkbox" class="list" name="list[]" value="<?php echo ($vo["id"]); ?>"/> &nbsp; 
								<a class="edit" href="javascript:void(0);" rel="<?php echo ($vo["id"]); ?>"><?php echo L('EDITING');?></a>  &nbsp;
								</td>
								<td><?php echo ($vo["title"]); ?></td>
								<td><a href="<?php echo ($vo["url"]); ?>" target="_blank"><?php if(strlen($vo['url']) > 25): echo (substr($vo["url"],0,25)); ?>...<?php else: echo ($vo["url"]); endif; ?></a></td>
							</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
					</tbody>
				</table>
			</div>
			<div class="span4" id="postion_user">
				<h4><?php echo L('NAVIGATION_LOCATION_PERSONAL_CENTER');?></h4>
				<table class="table table-hover">
					<thead>
						<tr>
						   <th width="25%"><input class="check_all" type="checkbox" /> &nbsp; <?php echo L('CHECK_ALL');?></th>
						   <th width="25%"><?php echo L('MENU');?></th>
						   <th><?php echo L('LINK');?></th>						   
						</tr>
					</thead>
					<tbody>
						<?php if(empty($postion["user"])): ?><tr><td colspan="3"><?php echo L('THIS_POSITION_IS_NOT_TO_ADD_THE_MENU');?></td></tr>
						<?php else: ?>
						<?php if(is_array($postion["user"])): $i = 0; $__LIST__ = $postion["user"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr><td><input type="checkbox" class="list" name="list[]" value="<?php echo ($vo["id"]); ?>"/> &nbsp; 
								<a class="edit" href="javascript:void(0);" rel="<?php echo ($vo["id"]); ?>"><?php echo L('EDITING');?></a>  &nbsp;
								</td>
								<td><?php echo ($vo["title"]); ?></td>
								<td><a href="<?php echo ($vo["url"]); ?>" target="_blank"><?php if(strlen($vo['url']) > 25): echo (substr($vo["url"],0,25)); ?>...<?php else: echo ($vo["url"]); endif; ?></a></td>
							</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
					</tbody>
				</table>
			</div>
			<div class="span12"><span style="color: rgb(243, 40, 12);"><?php echo L('PROMPT');?></span></div>
		</div>
	</div>
<div id="dialog-message1" class="hide" title="<?php echo L('ADD_MENU');?>">loading...</div>
<div id="dialog-message2" class="hide" title="<?php echo L('EDIT_MENU');?>">loading...</div>
<script type="text/javascript">
	$(function(){
		$(".check_all").click(function(){
			$(this).parents("table").find("input[class='list']").prop('checked', $(this).prop("checked"));
		});

		$("#add_navigation").click(function(){
			$('#dialog-message1').dialog('open');
			$('#dialog-message1').load('<?php echo U("navigation/add");?>');
		});
		$("table tbody").sortable({connectWith: "table tbody"});
		$(".edit").click(
			function(){
				$('#dialog-message2').dialog('open');
				tid = $(this).attr('rel');
				$('#dialog-message2').load('<?php echo U("navigation/edit","id=");?>' + tid);
			}
		);
		$("#sort_btn").click(
			function() {
				postion_top = [];
				$.each($("#postion_top .list"), function(i, item){postion_top.push(item.value)});
				postion_user = [];
				$.each($("#postion_user .list"), function(i, item){postion_user.push(item.value)});
				postion_more = [];
				$.each($("#postion_more .list"), function(i, item){postion_more.push(item.value)});
				$.get('<?php echo U("navigation/sort");?>',{postion_top:postion_top.join(','), postion_user:postion_user.join(','), postion_more:postion_more.join(',')}, function(data){
					if (data.status == 1) {
						$(".page-header").after('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>' + data.info + '</div>');
					} else {
						$(".page-header").after('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>' + data.info + '</div>');
					}
				}, 'json');
			}		
		);
	});
	function deleteDepartment(id,name){
		var v = confirm(<?php echo L('SURE_TO_DELETE_THE_MENU');?>);
		if(v == true){
			window.location="<?php echo U('navigation/delete','id=');?>"+id;
		}
	}

	$('#dialog-message1').dialog({
		autoOpen: false,
		modal: true,
		width: 600,
		maxHeight: 400,
		position :["center",100]
	});
	$('#dialog-message2').dialog({
		autoOpen: false,
		modal: true,
		width: 600,
		maxHeight: 400,
		position :["center",100]
	});
</script>

</body>
</html>