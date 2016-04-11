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
			<h4><?php echo L('ANNOUNCEMENT OF THE LIST');?></h4>
		</div>
		<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
		<div class="row">
			<div class="span12">
				<div class="bulk-actions align-left">
					<div class="pull-left"><a id="delete"  class="btn" style="margin-right: 8px;"><i class="icon-remove"></i>&nbsp;<?php echo L('DELETE');?></a></div>
					<div class="pull-left"><a id="sort_btn" class="btn" style="margin-right: 8px;">&nbsp;<?php echo L('SAVE ORDER');?></a></div>
					<ul class="nav pull-left">
						<form class="form-inline" id="searchForm" onsubmit="return checkSearchForm();" action="" method="get">
							<li class="pull-left">
								<select style="width:auto" id="field" name="field" id="selectCondition" onchange="changeCondition()">
									<option class="all" value="all"><?php echo L('ANY FIELD');?></option>
									<option class="word" value="title"><?php echo L('HEADLINE');?></option>
									<option class="word" value="content"><?php echo L('CONTENT');?></option>
									<option class="role" value="role_id"><?php echo L('AUTHORS');?></option>
									<option class="date" value="create_time"><?php echo L('CREATE_TIME');?></option>
									<option class="date" value="update_time"><?php echo L('UPDATE_TIME');?></option>
								</select>&nbsp;&nbsp;
							</li>
							<li id="conditionContent" class="pull-left">
							<select id="condition" style="width:auto" name="condition" onchange="changeSearch()">		
								<option value="contains"><?php echo L('CONTAINS');?></option>
								<option value="is"><?php echo L('IS');?></option>
								<option value="start_with"><?php echo L('START_WITH');?></option>
								<option value="end_with"><?php echo L('END_WITH');?></option>
								<option value="is_empty"><?php echo L('IS_EMPTY');?></option>
							</select>&nbsp;&nbsp;
							</li>
							<li id="searchContent" class="pull-left">
								<input id="search" type="text" class="input-medium search-query" name="search"/>&nbsp;&nbsp;
							</li>
							<li class="pull-left">
								<input type="hidden" name="m" value="announcement"/>
								<?php if($_GET['by']!= null): ?><input type="hidden" name="category_id" value="<?php echo ($_GET['category_id']); ?>"/><?php endif; ?>
								<button type="submit" class="btn"> <img src="__PUBLIC__/img/search.png"/>  <?php echo L('SEARCH');?></button>&nbsp;
							</li>
						</form>
					</ul>
				</div>
				<div class="pull-right">
						<a class="btn btn-primary" href="<?php echo U('announcement/add');?>"><i class="icon-plus"></i>&nbsp; <?php echo L('ADD THE ANNOUNCEMENT');?></a>
					</div>
			</div>
			<div class="span12">
				<form id="form1"  method="Post">
					<table class="table table-hover table-striped table_thead_fixed">
						<?php if($list == null): ?><tr><td><?php echo L('EMPTY_TPL_DATA');?></td></tr>
						<?php else: ?>
						<thead>
							<tr>
							   <th><input class="check_all" name="check_all" id="check_all" type="checkbox" /> &nbsp;</th>
							   <th><?php echo L('HEADLINE');?></th>
							   <th><?php echo L('CREATOR_ROLE');?></th>
							   <?php if(C('ismobile') != 1): ?><th><?php echo L('UPDATE TIME');?></th>
							   <th><?php echo L('THE CURRENT STATE');?></th>
							   <th><?php echo L('LOGIN INTERFACE STATE');?></th><?php endif; ?>
							   <th><?php echo L('OPERATING');?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="7">
									<?php echo ($page); ?>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
									<td>
										<input class="list" type="checkbox" name="announcement_id[]" value="<?php echo ($vo["announcement_id"]); ?>"/>
									</td>
									<td><a href="<?php echo U('announcement/view','id='.$vo['announcement_id']);?>"><?php echo ($vo["title"]); ?></a></td>
									<td><?php if(!empty($vo["owner"]["user_name"])): ?><a class="role_info" rel="<?php echo ($vo["owner"]["role_id"]); ?>" href="javascript:void(0)"><?php echo ($vo["owner"]["user_name"]); ?></a><?php endif; ?></td>
									<?php if(C('ismobile') != 1): ?><td><notempty name="vo.update_time"><?php echo (date("Y-m-d",$vo["update_time"])); ?><notempty></td>
									<td><?php if($vo['status'] == 1): ?><span style="color:green;"><i class="icon-ok-circle"></i><?php echo L('RELEASE');?></span><?php else: ?><span style="color:red;"><i class="icon-remove-circle"></i><?php echo L('HAS BEEN DISCONTINUED');?></span><?php endif; ?></td>
									<td><?php if($vo['isshow'] == 1): if($vo['status'] != 1): ?><span style="color:red;"><i class="icon-remove-circle"></i><?php echo L('NOT SHOW');?></span><?php else: ?><span style="color:green;"><i class="icon-ok-circle"></i><?php echo L('SHOW');?></span><?php endif; else: ?><span style="color:red;"><i class="icon-remove-circle"></i><?php echo L('NOT SHOW');?></span><?php endif; ?></td><?php endif; ?>
									<td><a href="<?php echo U('announcement/changestatus','id='.$vo['announcement_id']);?>"><?php if($vo['status'] == 1): echo L('DISABLE'); else: echo L('PUBLISH'); endif; ?></a> &nbsp;<a href="<?php echo U('announcement/view','id='.$vo['announcement_id']);?>"><?php echo L('SEE ABOUT');?></a> &nbsp;<a href="<?php echo U('announcement/edit','id='.$vo['announcement_id']);?>"><?php echo L('EDIT');?></a></td>
								</tr><?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody><?php endif; ?>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="hide" id="dialog-import" title="<?php echo L('IMPORT DATA');?>">loading...</div>
<div class="hide" id="dialog-role-info" title="<?php echo L('DIALOG_USER_INFO');?>">loading...</div>
<script type="text/javascript">
$("#dialog-import").dialog({
	autoOpen: false,
	modal: true,
	width: 600,
	maxHeight: 400,
	position: ["center",100]
});
$("#dialog-role-info").dialog({
    autoOpen: false,
    modal: true,
	width: 600,
	maxHeight: 400,
	position: ["center",100]
});

	function deleteConfirm(id,name){
		if(confirm("<?php echo L('WHETHER TO DELETE THE ARTICLE');?>"+name)){
			window.location="<?php echo U('announcement/delete','id=');?>"+id;
		}
	}
	function searchByCategory(){
		var objCategory=document.getElementById("categoryList");
		var id=objCategory.options[objCategory.selectedIndex].value;
		window.location="<?php echo U('announcement/index','by=all&category_id=');?>"+id;
	}
$("table tbody").sortable({connectWith: "table tbody"});

$(function(){
<?php if($_GET['field']!= null): ?>$("#field option[value='<?php echo ($_GET['field']); ?>']").prop("selected", true);changeCondition();
	$("#condition option[value='<?php echo ($_GET['condition']); ?>']").prop("selected", true);changeSearch();
	$("#search").prop('value', '<?php echo ($_GET['search']); ?>');<?php endif; ?>

	$("#check_all").click(function(){
		$("input[class='list']").prop('checked', $(this).prop("checked"));
	});
	$("#add_category").click(function(){
		$('#dialog-message1').dialog('open');
		$('#dialog-message1').load("<?php echo U('announcement/categoryAdd');?>");
	});
	$('#delete').click(function(){
		if(confirm('确认要删除吗？')){
			$("#form1").attr('action', '<?php echo U("announcement/delete");?>');
			$("#form1").submit();
		}else{
			return false;
		}
	});
	$(".role_info").click(function(){
		$role_id = $(this).attr('rel');
		$('#dialog-role-info').dialog('open');
		$('#dialog-role-info').load('<?php echo U("user/dialoginfo","id=");?>'+$role_id);
	});
	$("#sort_btn").click(
		function() {
			position = [];
			$.each($(".list"), function(i, item){position.push(item.value)});
			$.get('<?php echo U("announcement/announcementOrder");?>',{postion:position.join(',')}, function(data){
				if (data.status == 1) {
					$(".page-header").after('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>' + data.info + '</div>');
				} else {
					$(".page-header").after('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>' + data.info + '</div>');
				}
			}, 'json');
		}	
	);
});
</script>

</body>
</html>