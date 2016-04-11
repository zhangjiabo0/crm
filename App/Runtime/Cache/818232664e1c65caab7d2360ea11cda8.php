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
			editor = K.create('textarea[name="description"]', {
				uploadJson:'<?php echo U("file/editor");?>',
				allowFileManager : true,
				loadStyleMode : false,
				fileManagerJson: "<?php echo U('file/manager');?>"
			});
		});
</script>
<div class="container">
	<div class="page-header">
		<h4><?php echo L('ADD_TASK');?></h4>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
			<form action="<?php echo U('task/add');?>" method="post">
			<input type="hidden" name="creator_role_id" value="<?php echo (session('role_id')); ?>"/>
			<table class="table table-hover">
				<thead>
					<tr>
						<td>&nbsp;</td>						
						<td <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?>><input class="btn btn-primary" name="submit" type="submit" value="<?php echo L('SAVE');?>"/> &nbsp; <?php if(C('ismobile') != 1): ?><input name="submit" class="btn btn-primary" type="submit" value="<?php echo L('SAVE AND NEW');?>"/><?php endif; ?> &nbsp; <input class="btn" onclick="javascript:history.go(-1)" type="reset" value="<?php echo L('RETURN');?>"/></td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?>><input name="submit" class="btn btn-primary" type="submit" value="<?php echo L('SAVE');?>"/> &nbsp; <?php if(C('ismobile') != 1): ?><input name="submit" class="btn btn-primary" type="submit" value="<?php echo L('SAVE AND NEW');?>"/><?php endif; ?> &nbsp; <input onclick="javascript:history.go(-1)" class="btn" type="reset" value="<?php echo L('RETURN');?>"/></td>
					</tr>
				</tfoot> 
				<tbody>
					<tr><th colspan="4"><?php echo L('BASIC_INFO');?></th></tr>
					<tr>
						<td class="tdleft" <?php if(C('ismobile') != 1): ?>width="15%"<?php endif; ?>><?php echo L('THEME');?></td>
						<td <?php if(C('ismobile') != 1): ?>width="35%"<?php endif; ?>><input type="text" name="subject" id="subject" /></td>
						<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
						<td class="tdleft" <?php if(C('ismobile') != 1): ?>width="15%"<?php endif; ?>><?php echo L('NOTIFICATION_METHODS');?></td>
						<td <?php if(C('ismobile') != 1): ?>width="35%"<?php endif; ?>><input type="checkbox" name="message_alert" value="1" checked="checked"><?php echo L('MESSAGE');?> &nbsp; <input type="checkbox" name="email_alert" value="1"><?php echo L('EMAIL');?></td>
					</tr>
					<tr>
						<td class="tdleft">负责人</td>
						<td <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?>><input type="hidden" name="owner_role_id_str" id="owner_id"/><input class="span6" type="text" id="owner_name" name="owner_name" />&nbsp; <?php echo L('CLICK_TO_SELECT');?></td>
					</tr>
					<tr>
						<td class="tdleft">任务相关人</td>
						<td <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?>><input type="hidden" name="about_roles" id="about_roles"/><input class="span6" type="text" id="about_roles_name" name="about_roles_name" />&nbsp; <?php echo L('CLICK_TO_SELECT');?></td>
					</tr>
					<tr>
						<td class="tdleft"><?php echo L('SELECT_RELATED');?></td>
						<td>
							<select name="module" class="span2" onchange="changeContent()" id="select1">
								<option value=""></option>
								<option value="contacts"><?php echo L('CONTACTS');?></option>
								<!-- <option value="leads">线索</option> -->
								<option value="customer"><?php echo L('CUSTOMER');?></option>
								<option value="business"><?php echo L('BUSINESS');?></option>
								<option value="product"><?php echo L('PRODUCT');?></option>
							</select>
							<input type="hidden" id="module_id" name="module_id" id="select_content"/>
							<input type="text" name="module_name" id="module_name"/>
						</td>
						<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
						<td class="tdleft"><?php echo L('DEADLINE');?></td>
						<td><input type="text" id="due_date" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'})" name="due_date" class="Wdate"/></td>
					</tr>
					<tr>
						<td class="tdleft" ><?php echo L('STATUS');?></td>
						<td><select name="status">
							<option value="<?php echo L('NOT_START');?>"><?php echo L('NOT_START');?></option>
							<option value="<?php echo L('DELAY');?>"><?php echo L('DELAY');?></option>
							<option value="<?php echo L('ONGOING');?>"><?php echo L('ONGOING');?></option>
							<option value="<?php echo ('COMPLETE');?>"><?php echo L('COMPLETE');?></option>
						</select></td>
						<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
						<td class="tdleft" ><?php echo L('PRECEDENCE');?></td>
						<td><select name="priority">
							<option><?php echo L('HIGH');?></option>
							<option><?php echo L('GENERAL');?></option>
							<option><?php echo L('LOW');?></option>
						</select></td>
					</tr>
					<tr>
						<td class="tdleft"><?php echo L('DESCRIPTION');?></td>
						<td <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?>>
							<textarea rows="15" class="span10" name="description"></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			</form>
		</div>
	</div>
</div>
<div class="hide" id="dialog-message2" title="<?php echo L('SELECT_CONTACTS');?>">loading...</div>
<div class="hide" id="dialog-message3" title="<?php echo L('SELECT_LEADS');?>">loading...</div>
<div class="hide" id="dialog-message4" title="<?php echo L('SELECT_CUSTOMER');?>">loading...</div>
<div class="hide" id="dialog-message5" title="<?php echo L('SELECT_BUSINESS');?>">loading...</div>
<div class="hide" id="dialog-message6" title="<?php echo L('SELECT_PRODUCT');?>">loading...</div>
<div class="hide" id="dialog-message7" title="<?php echo L('SELECT_EXECUTOR');?>">loading...</div>
<div class="hide" id="dialog-message8" title="选择任务相关人">loading...</div>
<script type="text/javascript">
<?php if(C('ismobile') == 1): ?>width=$('.container').width() * 0.9;<?php else: ?>width=800;<?php endif; ?>

$("#dialog-message7").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	height:400,
    close: function () {
        $(this).html(""); 
    },
    buttons: { 
        "Ok": function () {
			checked_role_id = ',';
			checked_role_name = '';
			$(".muti_role_id:checked").each(function(){
				checked_role_id += ($(this).val()+',');
				checked_role_name += ($(this).attr('rel')+',');
			});
			$('#owner_id').val(checked_role_id);
			$('#owner_name').val(checked_role_name);
			$(this).html(""); 
            $(this).dialog("close"); 
        },
		"Cancel": function () {
            $(this).dialog("close");
        }
    },
	position:["center",100]
});
$("#dialog-message8").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	height:400,
    close: function () {
        $(this).html(""); 
    },
    buttons: { 
        "Ok": function () {
			checked_role_id = ',';
			checked_role_name = '';
			$(".muti_role_id:checked").each(function(){
				checked_role_id += ($(this).val()+',');
				checked_role_name += ($(this).attr('rel')+',');
			});
			$('#about_roles').val(checked_role_id);
			$('#about_roles_name').val(checked_role_name);
			$(this).html(""); 
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
			var item = $('input:radio[name="contacts"]:checked').val();
			var name = $('input:radio[name="contacts"]:checked').parent().next().html();
			$('#module_id').val(item);
			$('#module_name').val(name);
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
			var item = $('input:radio[name="leads"]:checked').val();
			var name = $('input:radio[name="leads"]:checked').parent().next().html();
			var company = $('input:radio[name="leads"]:checked').parent().next().next().html();
			$('#module_id').val(item);
			$('#module_name').val(name+" "+company);
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
			$('#module_id').val(item);
			$('#module_name').val(name);
            $(this).dialog("close"); 
        },
		"Cancel": function () {
            $(this).dialog("close");
        }
    },
	position:["center",100]
});
$("#dialog-message5").dialog({
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
				$('#module_id').val(item);
				$('#module_name').val(name);
			}
			
            $(this).dialog("close"); 
        },
		"Cancel": function () {
            $(this).dialog("close");
        }
    },
	position:["center",100]
});
$("#dialog-message6").dialog({
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
			$('#module_id').val(item);
			$('#module_name').val(name);
            $(this).dialog("close"); 
        },
		"Cancel": function () {
            $(this).dialog("close");
        }
    },
	position:["center",100]
});
function changeContent(){
	$('#module_id').val("");
	$('#module_name').val("");
}
$(function(){
	$("input[name='submit']").click(function(){			
		if($("#subject").val() == null || $("#subject").val() == ""){
			alert('<?php echo L('SUBJECT_CAN_NOT_EMPTY');?>');
			return false;
		}
	});
	$('#module_name').click(
		function(){
			a = $("#select1  option:selected").val();
			if (a == "contacts"){
				$('#dialog-message2').dialog('open');
				$('#dialog-message2').load('<?php echo U("contacts/radiolistdialog");?>');
			}else if(a == "leads"){
				$('#dialog-message3').dialog('open');
				$('#dialog-message3').load('<?php echo U("leads/listdialog");?>');
			}else if(a == "business"){
				$('#dialog-message5').dialog('open');
				$('#dialog-message5').load('<?php echo U("business/listdialog");?>');
			}else if(a == "customer"){
				$('#dialog-message4').dialog('open');
				$('#dialog-message4').load('<?php echo U("customer/listdialog");?>');
			}else if(a == "product"){
				$('#dialog-message6').dialog('open');
				$('#dialog-message6').load('<?php echo U("product/allproductdialog");?>');
			}
		}
	);
	$('#owner_name').click(
		function(){
			$('#dialog-message7').dialog('open');
			$('#dialog-message7').load('<?php echo U("user/mutiListDialog","by=task");?>');
		}
	);
	$('#about_roles_name').click(
		function(){
			$('#dialog-message8').dialog('open');
			$('#dialog-message8').load('<?php echo U("user/mutiListDialog","by=task");?>');
		}
	);
});
</script>

</body>
</html>