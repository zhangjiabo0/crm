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
<script src="__PUBLIC__/js/chart/highcharts.js"></script>
<script src="__PUBLIC__/js/chart/modules/exporting.js"></script>

<div class="container">
	<div class="page-header" style="border:none; font-size:14px;">
		<ul class="nav nav-tabs">
		  <li><a  href="<?php echo U('finance/index','t=receivables');?>"><img src="__PUBLIC__/img/yingshoukuan.png"/>&nbsp; <?php echo L('RECEIVABLES');?></a></li>
		  <li><a href="<?php echo U('finance/index','t=payables');?>"><img src="__PUBLIC__/img/yingfukuan.png"/> &nbsp; <?php echo L('PAYABLES');?></a></li>
		  <li><a href="<?php echo U('finance/index','t=receivingorder');?>"><img src="__PUBLIC__/img/shoukuandan.png"/> &nbsp; <?php echo L('RECEIVINGORDER');?></a></li>
		  <li><a href="<?php echo U('finance/index','t=paymentorder');?>"><img src="__PUBLIC__/img/fukuandan.png"/> &nbsp; <?php echo L('PAYMENTORDER');?></a></li>
		  <li class="active"><a href="<?php echo U('finance/analytics');?>"><img src="__PUBLIC__/img/tongji.png"/> &nbsp; <?php echo L('STATISTICS');?></a></li>
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
					<form class="form-inline" id="searchForm" onsubmit="return checkSearchForm();" action="" method="get">
						<ul class="nav pull-left">
							<li class="pull-left">
								<?php echo L('SELECT DEPARTMENT');?>&nbsp; <select style="width:auto" name="department" id="department" onchange="changeRole()">
									<option class="all" value="all"><?php echo L('ALL');?></option>
									<?php if(is_array($departmentList)): $i = 0; $__LIST__ = $departmentList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["department_id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</select>&nbsp;&nbsp;
							</li>
							<li class="pull-left">
								<?php echo L('SELECT USER');?>&nbsp; <select style="width:auto" name="role" id="role" onchange="changeCondition()">
									<option class="all" value="all"><?php echo L('ALL');?></option>
									<?php if(is_array($roleList)): $i = 0; $__LIST__ = $roleList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["role_id"]); ?>"><?php echo ($vo["role_name"]); ?>-<?php echo ($vo["user_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</select>&nbsp;&nbsp;
							</li>
							<li class="pull-left">
								<?php echo L('SELECT DATE');?>&nbsp; <?php echo L('FROM');?><input type="text" id="start_time" name="start_time" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})" class="Wdate" value="<?php echo ($_GET['start_time']); ?>"/><?php echo L('TO');?><input type="text" id="end_time" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})" name="end_time" class="Wdate" value="<?php echo ($_GET['end_time']); ?>" />&nbsp;&nbsp;
							</li>
							<li class="pull-left"><input type="hidden" name="m" value="finance"/><input type="hidden" name="a" value="analytics"/>
							<?php if($_GET['by']!= null): ?><input type="hidden" name="by" value="<?php echo ($_GET['by']); ?>"/><?php endif; ?>
							<button type="submit" class="btn"><?php echo L('SEARCH');?></button></li>
						</ul>
					</form>
				</li>				
			</ul>
		</div>
		<div class="span2 knowledgecate">
			<ul class="nav nav-list">
				<li class="active">
					<a href="javascript:void(0);"><?php echo L('CHOOSE STATISTICAL CONTENT');?></a>
				</li>
				<li id="report"><a id="show_report" class="active" href="javascript:void(0)"><i class="icon-white icon-chevron-right"></i><?php echo L('FINANCIAL STATISTICS REPORT');?></a></li>
				<li id="moon"><a id="show_moon" href="javascript:void(0)"><i class="icon-white icon-chevron-right"></i><?php echo L('MONTHLY STATISTICS');?></a></li>
				<li id="shoukuan"><a id="show_shoukuan" href="javascript:void(0)"><i class="icon-white icon-chevron-right"></i><?php echo L('RECEIVINGORDER YOY');?></a></li>
				<li id="fukuan"><a id="show_fukuan" href="javascript:void(0)"><i class="icon-white icon-chevron-right"></i><?php echo L('PAYABLES YOY');?></a></li>
			</ul> 
		</div>
		<div class="span10">
			<div id="report_content">
				<table class="table table-hover">
					<thead>
						<tr>
							<th colspan="7" style="text-align:center;background-color:#F5F5F5;border-right:2px solid #E0DCDC;">收款项</th>
							<th colspan="6" style="text-align:center;background-color:#F5F5F5;">付款项</th>
						</td>
						<tr>
							<th><?php echo L('USER');?></th>
							<th><?php echo L('RECEIVABLES NUMBER');?></th>
							<th><?php echo L('NO_RECEIVING');?></th>
							<th><?php echo L('PARTIALLY_RECEIVED');?></th>
							<th><?php echo L('AMOUNTS RECEIVABLE');?></th>
							<th><?php echo L('THE ACTUAL AMOUNT OF RECEIVABLES');?></th>
							<th style="border-right:2px solid #E0DCDC;"><?php echo L('RECEIVINGORDER');?></th>
							<th><?php echo L('PAYABLES NUMBER');?></th>
							<th><?php echo L('UNPAID');?></th>
							<th><?php echo L('PARTIALLY PAID');?></th>
							<th><?php echo L('AMOUNT PAYABLE');?></th>
							<th><?php echo L('THE ACTUAL PAYMENT AMOUNT');?></th>
							<th><?php echo L('PAYMENTORDER');?></th>
						</tr>
					</thead>
					<tfoot>
						<tr style="background: #029BE2;color: #fff;font-size: 13px;">
							<td><?php echo L('TOTAL');?></td>
							<td><?php echo ($total_report["shoukuan_count"]); ?></td>
							<td><?php echo ($total_report["weishou_count"]); ?></td>
							<td><?php echo ($total_report["bufenshoukuan_count"]); ?></td>
							<td><?php echo ($total_report["shoukuan_money"]); ?></td>
							<td><?php echo ($total_report["yishou_money"]); ?></td>
							<td><?php echo ($total_report["shoukuandan_count"]); ?></td>
							<td><?php echo ($total_report["fukuan_count"]); ?></td>
							<td><?php echo ($total_report["weifu_count"]); ?></td>
							<td><?php echo ($total_report["bufenfukuan_count"]); ?></td>
							<td><?php echo ($total_report["fukuan_money"]); ?></td>
							<td><?php echo ($total_report["yifu_money"]); ?></td>
							<td><?php echo ($total_report["fukuandan_count"]); ?></td>
						</tr>
					</tfoot>
					<tbody>
						<?php if(is_array($reportList)): $i = 0; $__LIST__ = $reportList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
							<td class="tdleft"><a class="role_info" rel="<?php echo ($vo["user"]["role_id"]); ?>" href="javascript:void(0)"><?php echo ($vo["user"]["user_name"]); ?></a></td>
							<td>
								<a href="<?php echo U('finance/index','t=receivables&field=owner_role_id&search='.$vo['user']['role_id']);?>" target="_blank">
									<?php echo ($vo["shoukuan_count"]); ?>
								</a>
							</td> 
							<td>
								<a href="<?php echo U('finance/index','t=receivables&field=owner_role_id&search='.$vo['user']['role_id'].'&by=none');?>" target="_blank">
									<?php echo ($vo["weishou_count"]); ?>
								</a>
							</td>
							<td>
								<a href="<?php echo U('finance/index','t=receivables&field=owner_role_id&search='.$vo['user']['role_id'].'&by=part');?>" target="_blank">
									<?php echo ($vo["bufenshoukuan_count"]); ?>
								</a>
							</td>
							<td>
								<a href="<?php echo U('finance/index','t=receivables&field=owner_role_id&search='.$vo['user']['role_id']);?>" target="_blank">
									<?php echo ($vo["shoukuan_money"]); ?>
								</a>
							</td>
							<td>
								<a href="<?php echo U('finance/index','t=receivingorder&field=owner_role_id&search='.$vo['user']['role_id'].'&by=part');?>" target="_blank">
									<?php echo ($vo["yishou_money"]); ?>
								</a>
							</td>
							<td style="border-right:2px solid #E0DCDC;">
								<a href="<?php echo U('finance/index','t=receivingorder&field=owner_role_id&search='.$vo['user']['role_id']);?>" target="_blank">
									<?php echo ($vo["shoukuandan_count"]); ?>
								</a>
							</td>
							<td>
								<a href="<?php echo U('finance/index','t=payables&field=owner_role_id&search='.$vo['user']['role_id']);?>" target="_blank">
									<?php echo ($vo["fukuan_count"]); ?>
								</a>
							</td>
							<td>
								<a href="<?php echo U('finance/index','t=payables&field=owner_role_id&search='.$vo['user']['role_id'].'&by=none');?>" target="_blank">
									<?php echo ($vo["weifu_count"]); ?>
								</a>
							</td>
							<td>
								<a href="<?php echo U('finance/index','t=payables&field=owner_role_id&search='.$vo['user']['role_id'].'&by=part');?>" target="_blank">
									<?php echo ($vo["bufenfukuan_count"]); ?>
								</a>
							</td> 
							<td>
								<a href="<?php echo U('finance/index','t=payables&field=owner_role_id&search='.$vo['user']['role_id']);?>" target="_blank">
									<?php echo ($vo["fukuan_money"]); ?>
								</a>
							</td>
							<td>
								<a href="<?php echo U('finance/index','t=paymentorder&field=owner_role_id&search='.$vo['user']['role_id'].'&by=part');?>" target="_blank">
									<?php echo ($vo["yifu_money"]); ?>
								</a>
							</td>
							<td>
								<a href="<?php echo U('finance/index','t=paymentorder&field=owner_role_id&search='.$vo['user']['role_id']);?>" target="_blank">
									<?php echo ($vo["fukuandan_count"]); ?>
								</a>
							</td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					</tbody>
				</table>
			</div>
			
			<div id="moon_content" class="hidden">
				<div id="an_chart">
					<div id="canvas_moon" style="min-width: 800px; height: 500px; margin: 0 auto"></div>
				</div>
			</div>
			<div id="shoukuan_year_content" class="hidden">
				<div id="an_chart">
					<div id="canvas_shoukuan" style="min-width: 800px; height: 500px; margin: 0 auto"></div>
				</div>
			</div>
			<div id="fukuan_year_content" class="hidden">
				<div id="an_chart">
					<div id="canvas_fukuan" style="min-width: 800px; height: 500px; margin: 0 auto"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="hide" id="dialog-role-info" title="<?php echo L('DIALOG_USER_INFO');?>">loading...</div>
<script type="text/javascript">
	<?php if(C('ismobile') == 1): ?>width=$('.container').width() * 0.9;<?php else: ?>width=800;<?php endif; ?>
	$("#dialog-role-info").dialog({
		autoOpen: false,
		modal: true,
		width: width,
		maxHeight: 400,
		position: ["center",100]
	});
	
	$(".role_info").click(function(){
		$role_id = $(this).attr('rel');
		$('#dialog-role-info').dialog('open');
		$('#dialog-role-info').load('<?php echo U("user/dialoginfo","id=");?>'+$role_id);
	});
	
	$(function () {
		$('#canvas_moon').highcharts({
			chart: {
				type: 'column'
			},
			title: {
				text: '<?php echo L('FINANCIAL INFORMATION MONTHLY STATISTICS');?>'
			},
			xAxis: {
				categories: [
					'Jan',
					'Feb',
					'Mar',
					'Apr',
					'May',
					'Jun',
					'Jul',
					'Aug',
					'Sep',
					'Oct',
					'Nov',
					'Dec'
				]
			},
			yAxis: {
				min: 0,
				title: {
					text: '<?php echo L('FINANCIAL INFORMATION MONTHLY STATISTICS');?>'
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					'<td style="padding:0"><b>{point.y:.1f} <?php echo L('YUAN',array(''));?></b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				}
			},
			series: [{
				name: '<?php echo L('RECEIVABLES');?>',
				data: <?php echo ($moon_count['shoukuan']); ?>
	
			}, {
				name: '<?php echo L('THE ACTUAL RECEIVABLES');?>',
				data: <?php echo ($moon_count['shijishoukuan']); ?>
	
			}, {
				name: '<?php echo L('PAYABLES');?>',
				data: <?php echo ($moon_count['fukuan']); ?>
	
			}, {
				name: '<?php echo L('THE ACTUAL PAYABLES');?>',
				data: <?php echo ($moon_count['shijifukuan']); ?>
	
			}]
		});
		
		$('#canvas_shoukuan').highcharts({
			chart: {
				type: 'column'
			},
			title: {
				text: '<?php echo L('RECEIVINGORDER YOY');?>'
			},
			xAxis: {
				categories: [
					'Jan',
					'Feb',
					'Mar',
					'Apr',
					'May',
					'Jun',
					'Jul',
					'Aug',
					'Sep',
					'Oct',
					'Nov',
					'Dec'
				]
			},
			yAxis: {
				min: 0,
				title: {
					text: '<?php echo L('RECEIVINGORDER YOY');?>'
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					'<td style="padding:0"><b>{point.y:.1f} <?php echo L('YUAN',array(''));?></b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				}
			},
			series: [{
				name: '<?php echo L('LAST YEAR');?>',
				data: <?php echo ($year_count['shoukuan_previousyear']); ?>
	
			}, {
				name: '<?php echo L('THIS YEAR');?>',
				data: <?php echo ($year_count['shoukuan_thisyear']); ?>
	
			}]
		});
		
		$('#canvas_fukuan').highcharts({
			chart: {
				type: 'column'
			},
			title: {
				text: '<?php echo L('PAYABLES YOY');?>'
			},
			xAxis: {
				categories: [
					'Jan',
					'Feb',
					'Mar',
					'Apr',
					'May',
					'Jun',
					'Jul',
					'Aug',
					'Sep',
					'Oct',
					'Nov',
					'Dec'
				]
			},
			yAxis: {
				min: 0,
				title: {
					text: '<?php echo L('PAYABLES YOY');?>'
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					'<td style="padding:0"><b>{point.y:.1f} <?php echo L('YUAN',array(''));?></b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				}
			},
			series: [{
				name: '<?php echo L('LAST YEAR');?>',
				data: <?php echo ($year_count['fukuan_previousyear']); ?>
	
			}, {
				name: '<?php echo L('THIS YEAR');?>',
				data: <?php echo ($year_count['fukuan_thisyear']); ?>
	
			}]
		});
	});
	
	
	
	function changeRole(){
		department_id = $("#department option:selected").val();
		$.ajax({
			type:'get',
			url:'index.php?m=user&a=getrolebydepartment&department_id='+department_id,
			async:true,
			success:function(data){
				options = '<option value="all"><?php echo L('ALL');?></option>';
				if(data.data != null){
					$.each(data.data, function(k, v){
						options += '<option value="'+v.role_id+'">'+v.role_name+"-"+v.user_name+'</option>';
					});
				}
				$("#role").html(options);
				<?php if($_GET['role']): ?>$("#role option[value='<?php echo ($_GET['role']); ?>']").prop("selected", true);<?php endif; ?>
			},
			dataType:'json'});
	}
	
	<?php if($_GET['department'] and $_GET['department'] != 'all'): ?>$("#department option[value='<?php echo ($_GET['department']); ?>']").prop("selected", true); 
	changeRole();<?php endif; ?>
	<?php if($_GET['department'] == 'all'): ?>$("#role option[value='<?php echo ($_GET['role']); ?>']").prop("selected", true);<?php endif; ?>
	
	$(function(){
		$("#show_report").click(function(){
			$(this).addClass('active');
			$("#show_moon").removeClass('active');
			$("#show_shoukuan").removeClass('active');
			$("#show_fukuan").removeClass('active');
			
			$("#report_content").removeClass('hidden');
			$("#moon_content").addClass('hidden');
			$("#shoukuan_year_content").addClass('hidden');
			$("#fukuan_year_content").addClass('hidden');
		});
		$("#show_moon").click(function(){
			$(this).addClass('active');
			$("#show_report").removeClass('active');
			$("#show_shoukuan").removeClass('active');
			$("#show_fukuan").removeClass('active');
			
			$("#report_content").addClass('hidden');
			$("#moon_content").removeClass('hidden');
			$("#shoukuan_year_content").addClass('hidden');
			$("#fukuan_year_content").addClass('hidden');
		});
		$("#show_shoukuan").click(function(){
			$(this).addClass('active');
			$("#show_report").removeClass('active');
			$("#show_moon").removeClass('active');
			$("#show_fukuan").removeClass('active');
			
			$("#report_content").addClass('hidden');
			$("#moon_content").addClass('hidden');
			$("#shoukuan_year_content").removeClass('hidden');
			$("#fukuan_year_content").addClass('hidden');
		});
		$("#show_fukuan").click(function(){
			$(this).addClass('active');
			$("#show_report").removeClass('active');
			$("#show_moon").removeClass('active');
			$("#show_shoukuan").removeClass('active');
			
			$("#report_content").addClass('hidden');
			$("#moon_content").addClass('hidden');
			$("#shoukuan_year_content").addClass('hidden');
			$("#fukuan_year_content").removeClass('hidden');
		});
	});
</script>

</body>
</html>