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
<div class="container no-mar-top no-bg" >
	<div class="row ">
		<div class="span2 bs-docs-sidebar mar-left3" >
			<ul class="nav nav-list bs-docs-sidenav span2 widths" id="left_list" style="height:440px;">
				<li class="first-li"><span class="spans1" ><img src="__PUBLIC__/img/house.png"/>&nbsp;任务详情</span></li>
				<li class="active"><a href="#tab1"><?php echo L('BASIC_INFO');?></a></li>
				<li><a href="#tab2"><?php echo L('PROGRESS_LOG');?>&nbsp;&nbsp;<span class="badge badge-success"><?php if($task['log_count']): echo ($task['log_count']); endif; ?></span></a></li>
			</ul>
		</div>
		<div class="tab-content span8 mar-lefts" >
			<div class="tab-pane fade in active" id="tab1">
				<div class="container2 top-pad" >
					<spanclass="basic_information" name="tab"><?php echo L('TASK_VIEW');?></span>
					<div class="pull-right"style="margin:-3px 10px 0 0;">
						<a href="<?php echo U('task/edit','id='.$task['task_id']);?>" class="btn btn-primary"/><?php echo L('EDIT');?></a>
						<a href="<?php echo U('task/delete','redirect=1&id='.$task['task_id']);?>" class="btn btn-primary del_confirm"/><?php echo L('DELETE');?></a>
						<a href="javascript:void(0)" onclick="javascript:history.go(-1)" class="btn btn-primary"><?php echo L('RETURN');?></a>
					</div>
				</div>
				<div class="back_box" style="margin-top:10px;">
					<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
					<table class="table">
						<tbody>
							<tr>
								<td class="tdleft" width="15%"><?php echo L('THEME');?>：</td>
								<td width="35%"><?php echo ($task["subject"]); ?></td>
								<td <?php if(C('ismobile') != 1): ?>width="15%"<?php endif; ?> class="tdleft" ><?php echo L('EXPIRATION_DATE');?>：</td>
								<td <?php if(C('ismobile') != 1): ?>width="35%"<?php endif; ?>><?php if($task['due_date'] > 0): echo (date('Y-m-d H:i',$task["due_date"])); endif; ?></td>
							</tr>
							<tr>
								<td class="tdleft"><font style="font-weight:700;">负责人：</span></td>
								<td colspan="7">
									<?php if(is_array($task['owner'])): $i = 0; $__LIST__ = $task['owner'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(!empty($vo["user_name"])): ?><a class="role_info" rel="<?php echo ($vo["role_id"]); ?>" href="javascript:void(0)"><?php echo ($vo["user_name"]); ?></a>&nbsp;,<?php endif; endforeach; endif; else: echo "" ;endif; ?>
								</td>
							</tr>
							<tr>
								<td class="tdleft"><font style="font-weight:700;">任务相关人列表：</font></td>
								<td colspan="7">
									<?php if(is_array($task['about_roles'])): $i = 0; $__LIST__ = $task['about_roles'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(!empty($vo["user_name"])): ?><a class="role_info" rel="<?php echo ($vo["role_id"]); ?>" href="javascript:void(0)"><?php echo ($vo["user_name"]); ?></a>&nbsp;,<?php endif; endforeach; endif; else: echo "" ;endif; ?>
								</td>
							</tr>
							<tr>
								<td class="tdleft" ><?php echo L('STATUS');?>：</td>
								<td><?php echo ($task["status"]); ?></td>
								<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
								<td class="tdleft" ><?php echo L('PRECEDENCE');?>：</td>
								<td ><?php echo ($task["priority"]); ?></td>								
							</tr>
							<tr>
								<td class="tdleft" ><?php echo L('CREATE_TIME');?>：</td>
								<td><?php if($task['create_date'] > 0): echo (date('Y-m-d H:i:s',$task["create_date"])); endif; ?></td>
								<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
								<td class="tdleft"><?php echo L('CREATOR_ROLE');?>：</td>
								<td><?php if(!empty($task["creator"]["user_name"])): ?><a class="role_info" rel="<?php echo ($task["creator"]["role_id"]); ?>" href="javascript:void(0)"><?php echo ($task["creator"]["user_name"]); ?></a><?php endif; ?></td>							
							</tr>
							<tr>
							<?php if(!empty($task["module"]["module_name"])): ?><td class="tdleft"><?php echo L('RELATED_THINGS', array($task['module']['module_name']));?></td>
								<td ><a  href="<?php echo U($task['module']['module'].'/view','id='.$task['module']['module_id']);?>"><?php echo ($task["module"]["name"]); ?></a></td>
								<?php if(C('ismobile') == 1): ?><td colspan="2">&nbsp;</td><?php endif; ?>
							</tr><?php endif; ?>
							<tr>
								<td class="tdleft" >
									<?php echo L('DESCRIPTION');?>：
								</td><td <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?> >
									<?php if($task["description"] != null): ?><pre style="min-height: 200px;"><?php echo ($task["description"]); ?></pre><?php endif; ?>
								</td>
							</tr>
							<tr>
								<td class="tdleft"><?php echo L('COMMENT');?>:</td>
								<td <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?>>
									<table width="100%">
										<?php if(empty($comment_list)): ?><tr>
												<td colspan="2"><?php echo L('NO_SUPERIORS_COMMENT');?></td>
											</tr>
										<?php else: ?>
											<?php if(is_array($comment_list)): $i = 0; $__LIST__ = $comment_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
													<td colspan="2"><?php echo L('COMMENTATOR');?>:<a class="role_info" rel="<?php echo ($vo["role_id"]); ?>" href="javascript:void(0)"><?php echo ($vo["user_name"]); ?></a>（<?php echo (date("Y-m-d H:i",$vo["create_time"])); ?>）<?php if($vo['role_id'] == session('role_id')): ?><a rel="<?php echo ($vo['comment_id']); ?>" class="edit_comment_btn" href="javascript:void(0);"><?php echo L('EDIT');?></a><?php endif; ?></td>
												</tr>
												<tr>
													<td colspan="2"><?php if($vo["content"] != ''): ?><pre><?php echo ($vo["content"]); ?></pre><?php endif; ?></td>
												</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="tab-pane fade back_box" id="tab2">
				<div class="header1">
					<div class="pull-left two-title" ><?php echo L('PROGRESS_LOG');?></div>
					<div class="pull-right">
						<?php if($task['isclose'] == 0): ?><p class="pull-right">
							<a href="javascript:void(0);" class="add_log btn btn-primary">+ 添加进度日志</a></p><?php endif; ?>
					</div>
					<div style="clear:both;"></div>
				</div>
				<a name="tab2">&nbsp;</a>
				<?php if(is_array($task['log'])): $i = 0; $__LIST__ = $task['log'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><table class="table table-bordered info">
						<tr class="<?php echo ($vo['style']); ?>">
							<td class="tdleft" width="12.5%"><font style="font-weight:700;">姓名</font></td>
							<td width="37.5%"><?php echo ($vo["owner"]["user_name"]); ?></td>
							<td width="12.5%" class="tdleft"><font style="font-weight:700;">时间</font></td>
							<td width="37.5%"><?php echo (date("Y-m-d  H:i",$vo["create_date"])); ?></td>
						</tr>
						<tr class="<?php echo ($vo['style']); ?>">
							<td class="tdleft"><font style="font-weight:700;">相关附件</font></td>
							<td colspan="3">
								<?php if(is_array($vo['files'])): $i = 0; $__LIST__ = $vo['files'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a href="<?php echo ($v['file_path']); ?>" title="<?php echo ($v["name"]); ?>"><?php echo ($v["subName"]); ?></a><br><?php endforeach; endif; else: echo "" ;endif; ?>
							</td>
						</tr>
						<tr class="<?php echo ($vo['style']); ?>">
							<td class="tdleft"><font style="font-weight:700;">新增相关人</font></td>
							<td colspan="3"><?php echo ($vo["about_roles_name"]); ?></td>
						</tr>
						<tr class="<?php echo ($vo['style']); ?>">
							<td class="tdleft"><font style="font-weight:700;">内容描述</font></td>
							<td colspan="3"><pre><?php echo ($vo["content"]); ?></pre></td>
						</tr>
						
					</table><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
		</div>
		<div class="span2 bs-docs-sidebar mar-lefts2" id="right_list" >
			<ul class="nav nav-list bs-docs-sidenav  span2 widths" >
				<li class="active first-li"><span class="spans1">编辑详情</span></li>
				<?php if(session('role_id') == $task['creator_role_id']): ?><li><a id="comment_btn" href="javascript:void(0);"><img src="__PUBLIC__/img/youce.png"/>&nbsp;&nbsp;&nbsp;<?php echo L('COMMENT');?></a> </li><?php endif; ?>
				<li><a href="javascript:void(0);" class="add_log"><img src="__PUBLIC__/img/youce.png"/>&nbsp;&nbsp;&nbsp;<?php echo L('ADD_PROGRESS_LOG');?></a></li>
			</ul>
		</div>
	</div>
</div>
<div class="hide" id="dialog-file" title="<?php echo L('DIALOG_ADD_ATTACHMENTS');?>">loading...</div>
<div class="hide" id="dialog-log" title="<?php echo L('DIALOG_ADD_LOG');?>">loading...</div>
<div class="hide" id="dialog-role-info" title="<?php echo L('DIALOG_USER_INFO');?>">loading...</div>
<div class="hide" id="dialog-log-edit" title="<?php echo L('DIALOG_EDIT_LOG');?>">loading...</div>
<?php if(session('role_id') == $task['creator_role_id']): ?><div class="hide" id="dialog-comment" title="<?php echo L('COMMENT');?>">loading...</div>
<div class="hide" id="dialog-editcomment" title="<?php echo L('EDIT_COMMENT');?>">loading...</div><?php endif; ?>
<script type="text/javascript">
$('#left_list a').click(function (e) {
        e.preventDefault();
        $('#right_list').hide();
        $('#left_list').parent().next().removeClass('span8').addClass('span10');
        $(this).tab('show');
    })
    $('#left_list a:first').on('click', function (e) {
        $('#left_list').parent().next().removeClass('span10').addClass('span8');
        $('#right_list').show();
    })
<?php if(C('ismobile') == 1): ?>width=$('.container').width() * 0.9;<?php else: ?>width=800;<?php endif; ?>

$("#dialog-role-info").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight: 400,
	position: ["center",100]
});
$("#dialog-file").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight: 400,
	position: ["center",100]
});
$("#dialog-log").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight: 500,
	position: ["center",100]
});
$("#dialog-log-edit").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight: 400,
	position: ["center",100]
});
$("#dialog-comment").dialog({
    autoOpen: false,
    modal: true,
	width: 600,
	maxHeight: 400,
	position: ["center",100],
	buttons: { 
		"<?php echo L('OK');?>": function () {
			$('#comment').submit();
			$(this).dialog("close"); 
		},
		"<?php echo L('CANCEL');?>": function () {
			$(this).dialog("close");
		}
	}
});
$("#dialog-editcomment").dialog({
    autoOpen: false,
    modal: true,
	width: 600,
	maxHeight: 400,
	position: ["center",100],
	buttons: { 
		"<?php echo L('OK');?>": function () {
			$('#editcomment').submit();
			$(this).dialog("close"); 
		},
		"<?php echo L('CANCEL');?>": function () {
			$(this).dialog("close");
		}
	}
});
$(function(){
	$(".add_file").click(function(){
		$('#dialog-file').dialog('open');
		$('#dialog-file').load('<?php echo U("file/add","r=RFileTask&module=task&id=".$task["task_id"]);?>');
	});
	<?php if($task['isclose'] == 0): ?>$(".add_log").click(function(){
		$('#dialog-log').dialog('open');
		$('#dialog-log').load('<?php echo U("log/tasklog","r=RLogTask&module=task&id=".$task["task_id"]);?>');
	});<?php endif; ?>
	$(".role_info").click(function(){
		$role_id = $(this).attr('rel');
		$('#dialog-role-info').dialog('open');
		$('#dialog-role-info').load('<?php echo U("user/dialoginfo","id=");?>'+$role_id);
	});
	$(".edit_log").click(function(){
		$log_id = $(this).attr('rel');
		$('#dialog-log-edit').dialog('open');
		$('#dialog-log-edit').load('<?php echo U("log/edit","id=");?>'+$log_id);
	});
	
	<?php if(session('role_id') == $task['creator_role_id']): ?>$("#comment_btn").click(function(){
		$('#dialog-comment').dialog('open');
		$('#dialog-comment').load('<?php echo U("comment/add","to_role_id=".$task["owner_role_id"]."&module=task&module_id=".$task["task_id"]);?>');
	});
	$(".edit_comment_btn").click(function(){
		comment_id = $(this).attr('rel');
		$('#dialog-comment').dialog('open');
		$('#dialog-comment').load('<?php echo U("comment/edit","to_role_id=".$task["owner_role_id"]."&id=");?>'+comment_id);
	});<?php endif; ?>
	$(".more").click(function(){
		log_id = $(this).attr('rel');
		$('#llog_'+log_id).attr('class','');
		$('#slog_'+log_id).attr('class','hide');
	});
});
</script>

</body>
</html>