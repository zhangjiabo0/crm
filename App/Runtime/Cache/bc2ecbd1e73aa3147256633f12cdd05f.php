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
	<div class="page-header" style="border:none; font-size:14px; ">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('setting/sendsms');?>"><?php echo L('SEND_SMS');?></a></li>
			<li class="active"><a href="<?php echo U('setting/smsrecord');?>"><?php echo L('SMS_RECORD');?></a></li>
			<li><a href="<?php echo U('setting/sendemail');?>"><?php echo L('SEND_EMAIL');?></a></li>
		</ul>
	</div>
	<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>	
	<div class="row">	
		<div class="span12">
			<ul class="nav pull-left">
				<li class="pull-left">
					<form class="form-inline" id="searchForm" action="" method="get">
						<ul class="nav pull-left">
                        <li class="pull-left"><a id="delete"  class="btn" style="margin-right: 8px;"><i class="icon-remove"></i><?php echo L('DELETE');?></a></li>						
						<li class="pull-left" >
							<select style="width:auto" id="field" onchange="changeCondition()" name="field">
								<option class="word" value="">--<?php echo L('PLEASE_CHOOSE');?>--</option>
								<option class="word" value="telephone"><?php echo L('RECEIVE_TELEPHONE');?></option>
								<option class="word" value="content"><?php echo L('SEND_CONTENT');?></option>
								<option class="date" value="sendtime"><?php echo L('SEND_SENDTIME');?></option>
							</select>&nbsp;&nbsp;
						</li>
						<li id="conditionContent" class="pull-left">
							<select id="condition" style="width:auto" name="condition" onchange="changeSearch()">
								<option value="contains"><?php echo L('INCLUDE');?></option>
								<option value="not_contain"><?php echo L('EXCLUSIVE');?></option>
								<option value="is"><?php echo L('YES');?></option>
								<option value="isnot"><?php echo L('ISNOT');?></option>				
								<option value="start_with"><?php echo L('BEGINNING_CHARACTER');?></option>
								<option value="end_with"><?php echo L('TERMINATION_CHARACTER');?></option>
								<option value="is_empty"><?php echo L('Mandatory');?></option>
								<option value="is_not_empty"><?php echo L('ISNOTEMPTY');?></option>
							</select>&nbsp;&nbsp;
						</li>
						<li id="searchContent" class="pull-left">
							<input id="search" type="text" class="input-medium search-query" name="search"/>&nbsp;&nbsp;
						</li>
						<li class="pull-left"> 
							<input type="hidden" name="act" id="act" value="search"/>
							<input type="hidden" name="m" value="setting"/>
							<input type="hidden" name="a" value="smsrecord"/>
							<?php if($_GET['by']!= null): ?><input type="hidden" name="by" value="<?php echo ($_GET['by']); ?>"/><?php endif; ?>
							<button type="button" id="dosearch" class="btn"> <img src="__PUBLIC__/img/search.png"/>  <?php echo L('SEARCH');?></button>
							&nbsp;
						</li>
						</ul>
					</form>
				</li>
			</ul>
			
		</div>
		<div class="span12">
			<form id="form1" action="" method="post">
				<table class="table table-hover  table-striped">
					<thead>
						<tr>
						    <th>
								<input class="check_all" id="check_all" type="checkbox" /> &nbsp;
							</th>
							<th> <?php echo L('SEND_ROW_NAME');?></th>
							<th> <?php echo L('RECEIVE_TELEPHONE');?></th>
							<th> <?php echo L('SEND_CONTENT');?></th>
							<th> <?php echo L('SEND_SENDTIME');?></th>
						</tr>
					</thead>
					<tfoot>
						<tr  >
							<td colspan="5" style="text-align:center"><?php echo ($page); ?></td>
						</tr>
					</tfoot>
					<tbody>
					<?php if(!empty($data)): if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
								<td><input type="checkbox" class="check_list" name="record_id[]" value="<?php echo ($vo["sms_record_id"]); ?>"/> &nbsp;
								</td>
								<td>
								   <?php echo ($vo["send_user"]); ?>
								</td>
                                <td title="<?php echo ($vo["telephone"]); ?>">
								   <?php echo ($vo["subtelephone"]); ?>
								</td>
								<td>
								    <div title="<?php echo ($vo["content"]); ?>"><?php echo ($vo["subcontent"]); ?></div>
								</td>
								<td>
								    <?php echo (date('Y-m-d H:i:s',$vo["sendtime"])); ?>
								</td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php else: ?>
						<tr><td colspan="5">----<?php echo L('EMPTY_TPL_DATA');?>----</td></tr><?php endif; ?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		//提交搜索
		$("#dosearch").click(function(){
			result = checkSearchForm();
			if(result){
				$("#act").val('search');
				$("#searchForm").submit();
			}
		});
	});
	$("#check_all").click(function(){
		$("input[class='check_list']").prop('checked', $(this).prop("checked"));
	});
	$('#delete').click(function(){
		$("#form1").attr('action', '<?php echo U("Setting/delete");?>');
		$("#form1").submit();
	});
</script>

</body>
</html>