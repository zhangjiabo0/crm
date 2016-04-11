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
<script type="text/javascript" src="__PUBLIC__/js/jscolor/jscolor.js"></script>
<link rel="stylesheet" href="__PUBLIC__/css/kindeditor.css" type="text/css" />
<script type="text/javascript">
	<?php if(C('ismobile') != 1): ?>var editor;
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="content"]', {
			uploadJson:'<?php echo U("file/editor");?>',
			allowFileManager : true,
			fileManagerJson: "<?php echo U('file/manager');?>"
		});		
	});<?php endif; ?>
</script>
<div class="container">
	<!-- Docs nav ================================================== -->
	<div class="page-header">
		<h4><?php echo L('ADD THE ANNOUNCEMENT');?></h4>
	</div>
	<div class="row">
		<div class="span12">
			<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
			<form action="" method="post">
				<table class="table" border="0" cellspacing="1" cellpadding="0">
					<thead>
						<tr>
							<td colspan="2"><input name="submit" class="btn btn-primary" type="submit" value="<?php echo L('SAVE');?>"/> &nbsp;<input class="btn" type="button" onclick="javascript:history.go(-1)" value="<?php echo L('RETURN');?>"/></td>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="2"><input class="btn btn-primary" name="submit" type="submit" value="<?php echo L('SAVE');?>"/> &nbsp; <input class="btn btn" type="button" onclick="javascript:history.go(-1)" value="<?php echo L('RETURN');?>"/></td>
						</tr>
					</tfoot>
					<tbody>
						<th colspan="2"><?php echo L('BASIC INFORMATION');?></th>
						<tr>
							<td class="tdleft"><?php echo L('HEADLINE');?></td>
							<td><input type="text" name="title" class="span4" value=""/><?php echo L('100 CHARACTERS');?></td>
						</tr>
						<tr>
							<td class="tdleft"><?php echo L('HEADLINE COLOR');?></td>
							<td><input class="color" value="#000" name="color"/></td>
						</tr>
						<tr>
							<td class="tdleft"><?php echo L('WHETHER ISSUED');?></td>
							<td><input type="radio" value="1" checked name="status"/> <?php echo L('IS');?> &nbsp;
								<input type="radio" value="2" name="status"/> <?php echo L('ISNOT');?></td>
						</tr>
						<tr>
							<td class="tdleft"><?php echo L('WHETHER IN THE LOGIN SCREEN DISPLAY');?></td>
							<td><input type="radio" value="1" checked name="isshow"/> <?php echo L('IS');?> &nbsp;
								<input type="radio" value="0" name="isshow"/> <?php echo L('ISNOT');?></td>
						</tr>
						<tr>
							<td class="tdleft"><?php echo L('INFORM THE DEPARTMENT');?></td>
							<td><input type="checkbox" id="check_all" checked="checked"> &nbsp; <?php echo L('CHECK ALL');?> &nbsp;  &nbsp; <?php if(is_array($department_list)): $i = 0; $__LIST__ = $department_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input name="announce_department[]" class="department" type="checkbox" value="<?php echo ($vo['department_id']); ?>" checked="checked"/> &nbsp; <?php echo ($vo['name']); ?> &nbsp;  &nbsp;<?php endforeach; endif; else: echo "" ;endif; ?></td>
						</tr>
						<tr>
							<td class="tdleft"><?php echo L('CONTENT');?></td>
							<td>
								<textarea name="content" id="content" style="width: 1000px; height: 350px;"></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	<?php if(C('ismobile') == 1): ?>width=$('.container').width() * 0.8;
		$("#content").css({
			width : width
		});<?php endif; ?>
	$("#check_all").click(function(){
		var check_all = $(this).prop('checked');
		$(".department").prop('checked', check_all);
	});
</script>

</body>
</html>