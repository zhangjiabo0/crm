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
		<h4><?php echo L('MAIL_TEMPLATE_SETTINGS');?></h4>
	</div>
	<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
	<div class="row">
		<form action="<?php echo U('email/delete');?>" id="form1" method="post">
			<div class="span12">
				<p><div class="bulk-actions align-left">
					<a class="btn" href="javascript:void(0);" id="btn_delete"/><i class="icon-remove"></i> <?php echo L('DELETE');?></a> &nbsp; <a class="btn" id="sort_btn"><i class="icon-file"></i> <?php echo L('SAVE_THE_ORDER');?></a>
					<div class="pull-right">
						<a href="javascript:void(0);" id="add" class="btn btn-primary"><i class="icon-plus"></i> <?php echo L('ADD_THE_EMAIL_TEMPLATE');?></a>
					</div>
				</div></p>
			</div>
			<div class="span12">
				<table class="table table-hover table-striped table_thead_fixed" width="95%" border="0" cellspacing="1" cellpadding="0">
					<?php if(!empty($templateList)): ?><thead>
							<tr>
								<th width="10%"><input type="checkbox" name="check_all" id="check_all" class="check_all"/> &nbsp;</th>
								<th width="20%"><?php echo L('THE_NAME_OF_THE_TEMPLATE');?></th>
								<th width="20%"><?php echo L('EMAIL_SUBJECT');?></th>
								<th width="50%"><?php echo L('MAIL_SUBJECT_CONTENT');?></th>
								<th width="20%"><?php echo L('OPERATING');?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="6">
									<div class="span8"><span style="color: rgb(243, 40, 12);"><?php echo L('HINT');?></span></div>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<?php if(is_array($templateList)): $i = 0; $__LIST__ = $templateList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
									<td><input type="checkbox" class="list" name="template_id[]" value="<?php echo ($vo["template_id"]); ?>"/>
									</td>
									<td><?php echo ($vo["subject"]); ?></td>
									<td><?php echo ($vo["title"]); ?></td>
									<td><?php echo ($vo["content"]); ?></td>
									<td><a href="javascript:void(0);" rel="<?php echo ($vo['template_id']); ?>" class="edit"><?php echo L('EDITING');?></a></td>
								</tr><?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody>
					<?php else: ?>
						<tr>
							<td><?php echo L('EMPTY_TPL_DATA');?></td>
						</tr><?php endif; ?>
				</table>
			</div> 
		</form>
	</div>
<div class="hide" id="dialog-add" title="<?php echo L('ADD_STATE');?>">loading...</div>
<div class="hide" id="dialog-edit" title="<?php echo L('MODIFY_STATE');?>">loading...</div>
<div class="hide" id="dialog-delete" title="<?php echo L('DELETE_STATE');?>">loading...</div>
</div>
<script type="text/javascript">	
$("#dialog-add").dialog({
	autoOpen: false,
	modal: true,
	width: 600,
	maxHeight: 400,
	position: ["center",100]
});
$("#dialog-edit").dialog({
	autoOpen: false,
	modal: true,
	width: 600,
	maxHeight: 400,
	position: ["center",100]
});

$("table tbody").sortable({connectWith: "table tbody"});

$(function(){
	$("#btn_delete").click(function(){
		if(confirm("<?php echo L('COMFIRM_DELETE');?>")){
			$('#form1').submit();
		}
	});
	$("#check_all").click(function(){
		$("input[class='list']").prop('checked', $(this).prop("checked"));
	});
	$("#add").click(function(){
		$('#dialog-add').dialog('open');
		$('#dialog-add').load('<?php echo U("email/add");?>');
	});
	$(".edit").click(function(){
		var id = $(this).attr('rel');
		$('#dialog-edit').dialog('open');
		$('#dialog-edit').load('<?php echo U("email/edit","id");?>'+id);
	});
	$("#sort_btn").click(
		function() {
			position = [];
			$.each($(".list"), function(i, item){position.push(item.value)});
			$.get('<?php echo U("email/ordersort");?>',{postion:position.join(',')}, function(data){
				if (data.status == 1) {
					$(".page-header").after('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>' + data.info + '</div>');
				} else {
					$(".page-header").after('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>' + data.info + '</div>');
				}
			}, 'json');
		}	
	);
})
</script>

</body>
</html>