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
<script type="text/javascript" src="__PUBLIC__/js/zh_CN.js"></script>
<link rel="stylesheet" href="__PUBLIC__/css/kindeditor.css" type="text/css" />
<script type="text/javascript">
	var editor;
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="content"]', {
			uploadJson:'<?php echo U("file/editor");?>',
			allowFileManager : true,
			loadStyleMode : false,
			fileManagerJson: "<?php echo U('file/manager');?>"
		});
	});
</script>
<div class="container">
	<!-- Docs nav ================================================== -->
	<div class="page-header">
		<h3><?php echo L('WRITE_LOG');?></h3>
	</div>	
	<div class="row">
		<div class="span12">
			<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
			<form action="<?php echo U('log/mylog_add');?>" method="post">
				<input type="hidden" name="creator_id" value="<?php echo (session('user_id')); ?>"/>
				<table class="table" width="95%" border="0" cellspacing="1" cellpadding="0">
					<thead>
						<tr>
							<td>&nbsp;</td>
							<td><input name="submit" class="btn btn-primary" type="submit" value="<?php echo L('SAVE');?>"/>&nbsp; <input class="btn btn-primary" name="submit" type="submit" value="<?php echo L('SAVE AND NEW');?>"/> &nbsp;<input class="btn btn-primary" onclick="javascript:history.go(-1)" type="reset" value="<?php echo L('CANCEL');?>"/></td>
						</tr>
					</thead>
					<tfoot>
						<tr> 
							<td>&nbsp;</td>
							<td><input name="submit" class="btn btn-primary" type="submit" value="<?php echo L('SAVE');?>"/> &nbsp;<input name="submit" class="btn btn-primary" type="submit" value="<?php echo L('SAVE AND NEW');?>"/>&nbsp; <input class="btn btn-primary" type="reset" onclick="javascript:history.go(-1)" value="<?php echo L('CANCEL');?>"/></td>					
						</tr>
					</tfoot>
					<tbody width="100%">
						<tr>
							<th colspan="2"><?php echo L('BASIC_INFO');?></th>
						</tr>
						<tr>
							<td class="tdleft" width="15%"><?php echo L('SUBJECT');?></td>
							<td><input type="text" class="span5" name="subject" maxlength="20" value="<?php echo (date('Y-m-d',$current_time)); ?> <?php echo L('WORKING_LOG');?>"></td>						
						</tr>
						<tr>
							<td class="tdleft"><?php echo L('LOG_CATEGORY');?></td>
							<td><?php if(!C('ismobile')): ?><input type="radio" name="category_id" value="1" checked>  <?php echo L('COMMUNICATION_LOG');?>&nbsp;  &nbsp;<?php endif; ?><input type="radio" name="category_id" value="4" checked> <?php echo L('DAILY_REPROT');?> &nbsp;  &nbsp; <input type="radio" name="category_id" value="3"> <?php echo L('WEEKLY_REPROT');?> &nbsp;  &nbsp; <input type="radio" name="category_id" value="2"> <?php echo L('MONTHLY_REPROT');?></td>
						</tr>
						<?php if(!C('ismobile')): ?><tr>
								<td class="tdleft" ><?php echo L('RELATED_BUSINESS');?></td>
								<td >
									<input type="hidden" id="business_id" name="business_id" id="select_content"/>
									<input type="text" name="business_name" id="business_name"/> 
								</td>							
							</tr>
							<tr>
								<td class="tdleft" ><?php echo L('RELATED_TASK');?></td>
								<td>
									<input type="hidden" id="task_id" name="task_id" id="select_content"/>
									<input type="text" name="task_name" id="task_name"/> 
								</td>							
							</tr>
							<tr>
								<td class="tdleft" ><?php echo L('MARKETING_PRODUCTS');?></td>
								<td>
									<input type="hidden" id="product_id" name="product_id" id="select_content"/>
									<input type="text" name="product_name" id="product_name"/> 
								</td>							
							</tr>
							<tr>
								<td class="tdleft" ><?php echo L('CUSTOMER');?></td>
								<td>
									<input type="hidden" id="customer_id" name="customer_id" id="select_content"/>
									<input type="text" name="customer_name" id="customer_name"/> 
								</td>							
							</tr><?php endif; ?>
						<tr>
							<td class="tdleft" ><?php echo L('DETAILS_DESCRIPTION_ABOUT_JOB');?></td>
							<td>
								<textarea rows="15" <?php if(C('ismobile') == 1): ?>class="span6"<?php else: ?>class="span8"<?php endif; ?> name="content" style="height: 350px;"></textarea> 
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>
<div class="hide" id="dialog-message1" title="<?php echo L('DIALOG_SELECT_BUSINESS');?>">loading...</div>
<div class="hide" id="dialog-message2" title="<?php echo L('DIALOG_SELECT_TASK');?>">loading...</div>
<div class="hide" id="dialog-message3" title="<?php echo L('DIALOG_SELECT_PRODUCT');?>">loading...</div>
<div class="hide" id="dialog-message4" title="<?php echo L('DIALOG_SELECT_CUSTOMER');?>">loading...</div>
<script type="text/javascript">
<?php if(C('ismobile') == 1): ?>width=$('.container').width() * 0.9;<?php else: ?>width=800;<?php endif; ?>
$("#dialog-message1").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight: 400,
    close: function () {
        $(this).html(""); 
    },
    buttons: { 
        "Ok": function () {
			var item = $('input:radio[name="business"]:checked').val();
			var name = $('input:radio[name="business"]:checked').parent().next().html();
			if(item){
				$('#business_id').val(item);
				$('#business_name').val(name);
			}
			
            $(this).dialog("close"); 
        },
		"Cancel": function () {
            $(this).dialog("close");
        }
    },
	position:["center",100]
});
$("#dialog-message2").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight: 400,
    close: function () {
        $(this).html(""); 
    },
    buttons: { 
        "Ok": function () {
			var item = $('input:radio[name="task_id"]:checked').val();
			var name = $('input:radio[name="task_id"]:checked').parent().next().html();
			if(item){
				$('#task_id').val(item);
				$('#task_name').val(name);
			}
			
            $(this).dialog("close"); 
        },
		"Cancel": function () {
            $(this).dialog("close");
        }
    },
	position:["center",100]
});
$("#dialog-message3").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight: 400,
    close: function () {
        $(this).html(""); 
    },
    buttons: { 
        "Ok": function () {
			var item = $('input:radio[name="product_id"]:checked').val();
			var name = $('input:radio[name="product_id"]:checked').parent().next().html();
			if(item){
				$('#product_id').val(item);
				$('#product_name').val(name);
			}
            $(this).dialog("close"); 
        },
		"Cancel": function () {
            $(this).dialog("close");
        }
    },
	position:["center",100]
});
$("#dialog-message4").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight: 400,
    close: function () {
        $(this).html(""); 
    },
    buttons: { 
        "Ok": function () {
			var item = $('input:radio[name="customer"]:checked').val();
			var name = $('input:radio[name="customer"]:checked').parent().next().html();
			if(item){
				$('#customer_id').val(item);
				$('#customer_name').val(name);
			}
			
            $(this).dialog("close"); 
        },
		"Cancel": function () {
            $(this).dialog("close");
        }
    },
	position:["center",100]
});
$(function(){
	$('#business_name').click(
		function(){
			$('#dialog-message1').dialog('open');
			$('#dialog-message1').load('<?php echo U("business/listdialog");?>');
		}
	);
	$('#task_name').click(
		function(){
			$('#dialog-message2').dialog('open');
			$('#dialog-message2').load('<?php echo U("task/listdialog");?>');
		}
	);
	$("#product_name").click(function(){
		$('#dialog-message3').dialog('open');
		$('#dialog-message3').load('<?php echo U("product/allproductdialog");?>');
	});
	$('#customer_name').click(
		function(){
			$('#dialog-message4').dialog('open');
			$('#dialog-message4').load('<?php echo U("customer/listdialog");?>');
		}
	);
});

</script>

</body>
</html>