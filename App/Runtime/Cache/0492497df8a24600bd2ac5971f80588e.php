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
<link type="text/css" href="__PUBLIC__/css/dynamic.css" rel="stylesheet"/>
<div class="container">
	<!-- Docs nav ================================================== -->
	<div class="page-header">
		<h4><?php echo L('USER_VIEW');?></h4>
	</div>
	<div class="row">
		<div class="span12">
			<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab1" data-toggle="tab"><?php echo L('BASIC_INFO');?></a></li>
				<li><a href="#tab2" data-toggle="tab"><?php echo L('USER_INFO_NOTE');?>(<?php echo ($user['log_count']); ?>)</a></li>
				<li><a href="#tab3" data-toggle="tab"><?php echo L('RELATED_ATTACHMENT');?>(<?php echo ($user['file_count']); ?>)</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab1">
					<table class="table" width="95%" border="0" cellspacing="1" cellpadding="0">
						<thead>
							<tr> 
								<td <?php if(C('ismobile') == 1): ?>colspan="2"<?php else: ?>colspan="4"<?php endif; ?>>
									<p style="font-size: 14px;">
										<a href="javascript:void(0);" class="add_log"><?php echo L('ADD_USER_INFO_NOTE');?></a> | 
										<a href="javascript:void(0);" class="add_file"><?php echo L('ADD_FILE');?></a> |
										<a href="<?php echo U('user/edit','id=' . $user['user_id']);?>"><?php echo L('EDIT');?></a> | 
										<a href="javascript:void(0)" onclick="javascript:history.go(-1)"><?php echo L('RETURN');?></a>
									</p>
								</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th <?php if(C('ismobile') == 1): ?>colspan="2"<?php else: ?>colspan="4"<?php endif; ?>><?php echo L('BASIC_INFO');?></th>
							</tr>
							<tr>
								<td class="tdleft"><?php echo L('USER_LOGO');?></td>
								<td colspan="3"><?php if($user['img'] != ''): ?><img src="<?php echo ($user["img"]); ?>" class="thumbnail img"/><?php else: ?>
									<img src="__PUBLIC__/img/avatar_default.png" class="thumbnail img"/><?php endif; ?>
								</td>
							</tr>
							<tr>
								<td class="tdleft" width="15%"><?php echo L('USER_NAME');?>:</td><td width="35%"><?php echo ($user["user_name"]); ?>(<?php if(is_array($categoryList)): $i = 0; $__LIST__ = $categoryList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$temp): $mod = ($i % 2 );++$i; if($temp["category_id"] == $user['category_id']): echo ($temp["name"]); endif; endforeach; endif; else: echo "" ;endif; ?>)</b></td>
								<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
								<td  class="tdleft" width="15%"><?php echo L('CURRENT_POSITION');?></td><td width="35%"><?php echo ($user['department_name']); ?>-<?php echo ($user['role_name']); ?></td>
							</tr>
							<tr>
								<td class="tdleft"><?php echo L('SEX');?>:</td>
								<td><?php if($user['sex'] == 1): echo L('MALE'); elseif($user['sex'] == 2): echo L('FEMALE'); elseif($user['sex'] == 0): echo L('UNKNOW'); endif; ?></td>
								<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
								<td class="tdleft"><?php echo L('EMAIL');?>:</td>
								<td><a href="mailto:<?php echo ($user["email"]); ?>"><?php echo ($user["email"]); ?></a></td>
							</tr>
							<tr>
								<td class="tdleft"><?php echo L('TELPHONE');?>:</td><td><a href="tel:<?php echo ($user["telephone"]); ?>"><?php echo ($user["telephone"]); ?></a></td>
								<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
								<td class="tdleft"><?php echo L('AUDIT_STATUS');?>:</td>
								<td><?php if($user['status'] == 0): echo L('UNAUDITED'); endif; if($user['status'] == 1): echo L('PASS'); endif; ?>
								</td>
							</tr>
							<tr>
								<td class="tdleft"><?php echo L('ADDRESS');?> :</td>
								<td <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?>><?php echo ($user["address"]); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="tab-pane" id="tab2">
					<table class="table">
						<?php if($user["log"] == null): ?><tr>
								<td><?php echo L('EMPTY_DATA');?> </td>
							</tr>
						<?php else: ?> 
							<tr>
								<td>&nbsp;</td>
								<td><?php echo L('TITLE');?></td>
								<td><?php echo L('CONTENT');?></td>
								<td><?php echo L('CREATOR_ROLE');?></td>
								<td><?php echo L('CREATE_TIME');?></td>
							</tr>
							<?php if(is_array($user["log"])): $i = 0; $__LIST__ = $user["log"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
									<td><a href="<?php echo U('log/log_delete','id=' . $vo['log_id'] . '&r=RLogProduct');?>"><?php echo L('DELETE');?></a> &nbsp;<a href="<?php echo U('log/view','id=' . $vo['log_id']);?>" class="edit_log"><?php echo L('VIEW');?></a></td>
									<td>
										<?php echo ($vo["subject"]); ?>
									</td>
									<td>
										<?php if($vo["content"] != null): echo (substr($vo["content"],0,60)); ?>……<?php endif; ?>
									</td>
									<td>
										<?php if(!empty($vo["owner"]["user_name"])): echo ($vo["owner"]["user_name"]); ?> [<?php echo ($vo["owner"]["department_name"]); ?>-<?php echo ($vo["owner"]["role_name"]); ?>]<?php endif; ?>
									</td>
									<td>
										<?php if(!empty($vo["create_date"])): echo (date("Y-m-d  g:i:s a",$vo["create_date"])); endif; ?>
									</td>
								</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
						<tr>
							<td colspan="5">
								<a href="javascript:void(0);" class="add_log"><?php echo L('ADD');?></a>
							</td>
						</tr>
					</table>
				</div>
				<div class="tab-pane" id="tab3">
					<table class="table">
						<?php if($user["file"] == null): ?><tr>
								<td><?php echo L('EMPTY_DATA');?> </td>
							</tr>
						<?php else: ?> 
							<tr>
								<td>&nbsp;</td>
								<td><?php echo L('FILE_NAME');?></td>
								<td><?php echo L('SIZE');?></td>
								<td><?php echo L('ADD_TIME');?></td>
								<td><?php echo L('ADDED_BY');?></td>
							</tr>
							<?php if(is_array($user["file"])): $i = 0; $__LIST__ = $user["file"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
									<td><a href="<?php echo U('file/delete','id=' . $vo['file_id']);?>"><?php echo L('DELETE');?></a></td>
									<td>
										<a target="_blank" href="<?php echo ($vo["file_path"]); ?>"><?php echo ($vo["name"]); ?></a>
									</td>
									<td>
										<?php echo ($vo["size"]); echo L('BYTE');?>
									</td>
									<td>
										<?php if(!empty($vo["create_date"])): echo (date("Y-m-d g:i:s a",$vo["create_date"])); endif; ?>
									</td>
									<td>
										<?php if(!empty($vo["owner"]["user_name"])): echo ($vo["owner"]["user_name"]); ?> [<?php echo ($vo["owner"]["department_name"]); ?>-<?php echo ($vo["owner"]["role_name"]); ?>]<?php endif; ?>
									</td>
								</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
						<tr>
							<td colspan="5">
								<a href="javascript:void(0);" class="add_file"><?php echo L('ADD');?></a>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="hide" id="dialog-file" title="<?php echo L('DIALOG_ADD_ATTACHMENTS');?>">loading...</div>
<div class="hide" id="dialog-log" title="<?php echo L('DIALOG_ADD_LOG');?>">loading...</div>
<script type="text/javascript">
<?php if(C('ismobile') == 1): ?>width=$('.container').width() * 0.9;<?php else: ?>width=800;<?php endif; ?>
$("#dialog-file").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeigh: 400,
	position: ["center",100]
});
$("#dialog-log").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeigh: 400,
	position: ["center",100]
});
$(".add_file").click(function(){
	$('#dialog-file').dialog('open');
	$('#dialog-file').load('<?php echo U("file/add","r=RFileUser&module=user&id=" . $user["user_id"]);?>');
});
$(".add_log").click(function(){
	$('#dialog-log').dialog('open');
	$('#dialog-log').load('<?php echo U("log/add","r=RLogUser&module=user&id=" . $user["user_id"]);?>');
});
</script>

</body>
</html>