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
			<h4><?php echo L('SYSTEM_SETTINGS');?></h4>
		</div>
		<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
		<div class="tabbable">
			<ul class="nav nav-tabs">
				<li><a href="<?php echo U('setting/defaultInfo');?>"><?php echo L('SYSTEM_BASIC_SETTINGS');?></a></li>
				<li><a href="<?php echo U('setting/smtp');?>"><?php echo L('SMTP_SETTINGS');?></a></li>
				<li class="active"><a href="<?php echo U('setting/fields');?>"><?php echo L('CUSTOM_FIELDS');?></a></li>		
				<li><a href="<?php echo U('navigation/setting');?>"><?php echo L('SYSTEM_MENU_SETTINGS');?></a></li>
				
			</ul>
		</div>
		<div class="row">	
			<div class="span2 knowledgecate">
				<ul class="nav nav-list">
					<li class="active">
						<a href="javascript:void(0);" onclick="color_box()"><?php echo L('CUSTOM_FIELDS');?></a>
					</li>
					<li><a href="<?php echo U('setting/fields', 'model=customer');?>"><i class="icon-chevron-right"></i><?php echo L('CUSTOMER_FIELDS_SETTINGS');?></a></li>
					<li><a href="<?php echo U('setting/fields', 'model=business');?>"><i class="icon-chevron-right"></i><?php echo L('BUSINESS_FIELDS_SETTINGS');?></a></li>
					<li><a href="<?php echo U('setting/fields', 'model=product');?>"><i class="icon-chevron-right"></i><?php echo L('PRODUCT_FIELDS_SETTINGS');?></a></li>
					<li><a href="<?php echo U('setting/fields', 'model=leads');?>"><i class="icon-chevron-right"></i><?php echo L('CLUES_FIELDS_SETTINGS');?></a></li>
					<li><a href="<?php echo U('knowledge/category');?>" class="active"><i class="icon-chevron-right"></i><?php echo L('KNOWLEDGE_CATEGORY');?></a></li>
				</ul>
			</div>
			<form action="<?php echo U('knowledge/categoryDelete');?>" method="post">
				<div class="span10">
					<p><div class="bulk-actions align-left">
						<button type="submit" class="btn" ><i class="icon-remove"></i>&nbsp;<?php echo L('DELETE');?></button>
						<div class="pull-right">
							<a class="btn btn-primary" id="add_category" href="javascript:void(0);"><?php echo L('ADD_KNOWLEDGE_CATEGORY');?></a>
						</div>
					</div></p>
				</div>
				<div class="span10">
					<table class="table table-hover table-striped table_thead_fixed" width="95%" border="0" cellspacing="1" cellpadding="0">
						<thead>
							<tr>
								<th width="15%"><input class="check_all" name="check_all" id="check_all" type="checkbox" /> &nbsp;</th>
								<th width="15%"><?php echo L('CATEGORY_NAME');?></th>
								<th width="10%"><?php echo L('ARTICLE_NUM');?></th>
								<th width="35%"><?php echo L('CATEGORY_DESCRIPTION');?></th>
								<th width="15%"><?php echo L('OPERATING');?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="5">
									<?php echo ($page); ?>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<?php if(is_array($category_list)): $i = 0; $__LIST__ = $category_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
									<td>
									<input class="list" type="checkbox" id="category" name="category_list[]" value="<?php echo ($vo["category_id"]); ?>"/> &nbsp;
									</td>
									<td><?php echo ($vo["name"]); ?></td>
									<td><?php echo ($vo["count"]); ?></td>
									<td>
										<?php echo ($vo["description"]); ?>
									</td>
									<td><a class="edit_category" rel="<?php echo ($vo["category_id"]); ?>" href="javascript:void(0)"><?php echo L('EDIT');?></a> &nbsp;
									<a href="<?php echo U('knowledge/index','by=all&category_id='.$vo['category_id']);?>"><?php echo L('VIEW_THIS_CATEGORY');?></a></td>
								</tr><?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody>
					</table>
				</div> <!-- End #main-content -->
			</form>
		</div>		
	</div>
	<div id="dialog-message1" class="hide" title="<?php echo L('ADD_ARTICLE_CATEGORY');?>">loading...</div>
	<div id="dialog-message2" class="hide" title="<?php echo L('EDIT_CATEGORY_INFO');?>">loading...</div>
<script type="text/javascript">
	function changeContent(){
		a = $("#select1  option:selected").val();
		if(a=='1'){
			window.location.href="<?php echo U('knowledge/index');?>";
		}else if(a=='2'){
			window.location.href="<?php echo U('knowledge/category');?>";
		}else if(a=='3'){
			window.location.href="<?php echo U('knowledge/count');?>";
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

	$(function(){
		$("#check_all").click(function(){
			$("input[class='list']").prop('checked', $(this).prop("checked"));
		});
		$("#add_category").click(function(){
			$('#dialog-message1').dialog('open');
			$('#dialog-message1').load('<?php echo U('knowledge/categoryAdd');?>');
		});
		$(".edit_category").click(function(){
			$('#dialog-message2').dialog('open');
			$id = $(this).attr('rel');
			$('#dialog-message2').load("<?php echo U('knowledge/categoryEdit','id=');?>"+$id);
		});
	});
</script>

</body>
</html>