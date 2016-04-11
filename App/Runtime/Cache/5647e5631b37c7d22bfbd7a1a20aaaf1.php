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
	<!-- Docs nav ================================================== -->
	<div class="page-header"><h4><?php echo L('OPERATE_LOG');?></h4></div>
	<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
	<p class="view"><b><?php echo L('VIEW_NAV');?></b>
	<img src=" __PUBLIC__/img/by_owner.png"/> <a href="<?php echo U('action_log/index');?>" <?php if($_GET['by']== null): ?>class="active"<?php endif; ?>><?php echo L('ALL');?></a> |
	<a href="<?php echo U('action_log/index','by=me');?>" <?php if($_GET['by']== 'me'): ?>class="active"<?php endif; ?>><?php echo L('MY_LOG');?></a> |
	<img src="__PUBLIC__/img/by_time.png"/> <a href="<?php echo U('action_log/index','by=today');?>" <?php if($_GET['by']== 'today'): ?>class="active"<?php endif; ?>><?php echo L('CREATE_TODAY');?></a> | 
	<a href="<?php echo U('action_log/index','by=week');?>" <?php if($_GET['by']== 'week'): ?>class="active"<?php endif; ?>><?php echo L('CREATE_THIS_WEEK');?></a> | 
	<a href="<?php echo U('action_log/index','by=month');?>" <?php if($_GET['by']== 'month'): ?>class="active"<?php endif; ?>><?php echo L('CREATE_THIS_MONTH');?></a>  &nbsp; 
	<a href="<?php echo U('action_log/index','by=add');?>" <?php if($_GET['by']== 'add'): ?>class="active"<?php endif; ?>><?php echo L('RECENTLY_CREATED');?></a>
	</p>
	<div class="row">
		<div class="span2 knowledgecate">
			<ul class="nav nav-list">
				<li class="active">
					<a href="javascript:void(0);"><?php echo L('VIEW_BY_LOG_CATEGORY');?></a>
				</li>
				<li><a href="<?php echo U('action_log/index','by='.$_GET['by']);?>" <?php if($_GET['module'] == null): ?>class="active"<?php endif; ?>><i class="icon-white icon-chevron-right"></i><?php echo L('ALL');?></a></li>
				<li><a href="<?php echo U('action_log/index','module=business&by='.$_GET['by'].'&act='.$_GET['act']);?>" <?php if($_GET['module'] == 'business'): ?>class="active"<?php endif; ?>><i class="icon-chevron-right"></i><?php echo L('BUSINESS');?></a></li>
				<li><a href="<?php echo U('action_log/index','module=product&by='.$_GET['by'].'&act='.$_GET['act']);?>" <?php if($_GET['module'] == 'product'): ?>class="active"<?php endif; ?>><i class="icon-chevron-right"></i><?php echo L('PRODUCT');?></a></li>
				<li><a href="<?php echo U('action_log/index','module=customer&by='.$_GET['by'].'&act='.$_GET['act']);?>" <?php if($_GET['module'] == 'customer'): ?>class="active"<?php endif; ?>><i class="icon-chevron-right"></i><?php echo L('CUSTOMER');?></a></li>
				<li><a href="<?php echo U('action_log/index','module=leads&by='.$_GET['by'].'&act='.$_GET['act']);?>" <?php if($_GET['module'] == 'leads'): ?>class="active"<?php endif; ?>><i class="icon-chevron-right"></i><?php echo L('LEADS');?></a></li>
				<li><a href="<?php echo U('action_log/index','module=finance&by='.$_GET['by'].'&act='.$_GET['act']);?>" <?php if($_GET['module'] == 'finance'): ?>class="active"<?php endif; ?>><i class="icon-chevron-right"></i><?php echo L('FINANCE');?></a></li>
			</ul>
		</div>
		<div class="span10">
			<p style="font-size:14px;">
				<b><?php echo L('FILTER');?></b>
				<a <?php if($_GET['act'] == null): ?>class="active"<?php endif; ?> href="<?php echo U('ActionLog/index','module='.$_GET['module'].'&by='.$_GET['by'].'&type=1');?>"><?php echo L('ALL_THE_OPERATION');?></a> &nbsp; | &nbsp; 
				<a <?php if($_GET['act'] == 'add'): ?>class="active"<?php endif; ?> href="<?php echo U('ActionLog/index','module='.$_GET['module'].'&by='.$_GET['by'].'&act=add');?>"><?php echo L('ADD');?></a> &nbsp; | &nbsp; 
				<a <?php if($_GET['act'] == 'edit'): ?>class="active"<?php endif; ?> href="<?php echo U('ActionLog/index','module='.$_GET['module'].'&by='.$_GET['by'].'&act=edit');?>"><?php echo L('EDIT');?></a> &nbsp; | &nbsp; 
				<a <?php if($_GET['act'] == 'delete'): ?>class="active"<?php endif; ?> href="<?php echo U('ActionLog/index','module='.$_GET['module'].'&by='.$_GET['by'].'&act=delete');?>"><?php echo L('DELETE');?></a> &nbsp; | &nbsp; 
				<a <?php if($_GET['act'] == 'completedelete'): ?>class="active"<?php endif; ?> href="<?php echo U('ActionLog/index','module='.$_GET['module'].'&by='.$_GET['by'].'&act=completedelete');?>"><?php echo L('RECYCLE_BIN');?></a> 
			</p>
			<ul class="nav pull-left">		
				<li class="pull-left"><a id="delete" class="btn" ><i class="icon-remove"></i>&nbsp;<?php echo L('DELETE');?></a> </li>
				<li class="pull-left">
					<form class="form-inline" id="searchForm" onsubmit="return checkSearchForm();" action="index.php" method="get">
						<ul class="nav pull-left">
							<li class="pull-left">
								&nbsp;
								<select id="field" style="width:auto" onchange="changeCondition()" name="field">
									<option class="word" value="content"><?php echo L('OPERATOR');?></option>
									<option class="date" value="create_time"><?php echo L('OPERATING_TIME');?></option>
								</select>&nbsp;&nbsp;
							</li>
							<li id="conditionContent" class="pull-left">
							<select id="condition" style="width:auto" name="condition" onchange="changeSearch()">
								<option value="contains"><?php echo L('CONTAINS');?></option>
								<option value="not_contain"><?php echo L('NOT_CONTAIN');?></option>
								<option value="is"><?php echo L('IS');?></option>
								<option value="isnot"><?php echo L('ISNOT');?></option>						
								<option value="start_with"><?php echo L('START_WITH');?></option>
								<option value="end_with"><?php echo L('END_WITH');?></option>
								<option value="is_empty"><?php echo L('IS_EMPTY');?></option>
								<option value="is_not_empty"><?php echo L('IS_NOT_EMPTY');?></option>
								</select>&nbsp;&nbsp;
							</li>
							<li id="searchContent" class="pull-left"><input id="search" type="text" class="input-medium search-query" name="search"/>&nbsp;&nbsp;</li>
							<li class="pull-left"><input type="hidden" name="m" value="action_log"/>
							<?php if($_GET['by']!= null): ?><input type="hidden" name="by" value="<?php echo ($_GET['by']); ?>"/><?php endif; ?>
							<?php if($_GET['act']!= null): ?><input type="hidden" name="act" value="<?php echo ($_GET['act']); ?>"/><?php endif; ?>
							<?php if($_GET['module']!= null): ?><input type="hidden" name="module" value="<?php echo ($_GET['module']); ?>"/><?php endif; ?>
							<button type="submit" class="btn"> <img src="__PUBLIC__/img/search.png"/>  <?php echo L('SEARCH');?></button></li>
						</ul>
					</form>
				</li>
			</ul>
		</div>
		<div class="span10">
			<form id="form1" method="post">
			<table class="table table-hover table-striped table_thead_fixed">
				<?php if($list == null): ?><tr><td><?php echo L('EMPTY_TPL_DATA');?></td></tr>
				<?php else: ?>
					<thead>
						<tr>
							<th><input id="check_all" class="check_all" type="checkbox" /></th>
							<th><?php echo L('OPERATOR');?></th>
							<th><?php echo L('MODULE');?></th>
							<th><?php echo L('CONTENT');?></th>
							<th>
								<?php if($_GET['asc_order'] == 'create_time'): ?><a href="<?php echo U('actionLog/index','desc_order=create_time&'.$parameter);?>">
										<?php echo L('TIME');?>&nbsp;<img src="__PUBLIC__/img/arrow_up.png">
									</a>
								<?php elseif($_GET['desc_order'] == 'create_time'): ?>
									<a href="<?php echo U('actionLog/index','asc_order=create_time&'.$parameter);?>">
										<?php echo L('TIME');?>&nbsp;<img src="__PUBLIC__/img/arrow_down.png">
									</a>
								<?php else: ?>
									<a href="<?php echo U('actionLog/index','desc_order=create_time&'.$parameter);?>"><?php echo L('TIME');?></a><?php endif; ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
							<td><input class="check_list" type="checkbox" name="log_id[]" value="<?php echo ($vo["log_id"]); ?>"/></td>
							<td><?php if(!empty($vo["creator"]["user_name"])): ?><a class="role_info" rel="<?php echo ($vo["creator"]["role_id"]); ?>" href="javascript:void(0)"><?php echo ($vo["creator"]["user_name"]); ?></a><?php endif; ?></td>
							<td><?php echo L($vo['module_name']);?></a></td>
							<td><?php echo ($vo["content"]); ?></a></td>
							<td><?php echo (date("Y-m-d H:i:s",$vo["create_time"])); ?></td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					</tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5"><?php echo ($page); ?></td>
                        </tr>
                    </tfoot><?php endif; ?>
			</table>
			
			</form>
		</div>
	</div>
</div>
<div class="hide" id="dialog-role-info" title="<?php echo L('DIALOG_USER_INFO');?>">loading...</div>
<script type="text/javascript">
$("#dialog-role-info").dialog({
    autoOpen: false,
    modal: true,
	width: 600,
	maxHeight: 400,
	position: ["center",100]
});
$(function(){
	<?php if($_GET['field']!= null): ?>$("#field option[value='<?php echo ($_GET['field']); ?>']").prop("selected", true);changeCondition();
		$("#condition option[value='<?php echo ($_GET['condition']); ?>']").prop("selected", true);changeSearch();
		$("#search").prop('value', '<?php echo ($_GET['search']); ?>');<?php endif; ?>
	$('#delete').click(function(){
		if(confirm('<?php echo L('CONFIRM_DELETE');?>')){
			$("#form1").attr('action', '<?php echo U("Action_log/delete");?>');
			$("#form1").submit();
		}
	});
	$("#check_all").click(function(){
		$("input[class='check_list']").prop('checked', $(this).prop("checked"));
	});
	$(".role_info").click(function(){
		$role_id = $(this).attr('rel');
		$('#dialog-role-info').dialog('open');
		$('#dialog-role-info').load('<?php echo U("user/dialoginfo","id=");?>'+$role_id);
	});
})
</script>

</body>
</html>