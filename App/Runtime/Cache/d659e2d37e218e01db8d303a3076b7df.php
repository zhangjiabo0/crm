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
				<li class="first-li"><span class="spans1"><img src="__PUBLIC__/img/house.png"/>&nbsp;合同详情</span></li>
				<li class="active"><a href="#tab1"><?php echo L('BASIC_INFO');?></a></li>
				<li><a href="#tab2"><?php echo L('PRODUCT');?>&nbsp;&nbsp;<span class="badge badge-success"><?php if($info['product_count']): echo ($info['product_count']); endif; ?></span></a></li>
				<li><a href="#tab3">应收款&nbsp;&nbsp;<span class="badge badge-success"><?php if($info['receivables_count']): echo ($info['receivables_count']); endif; ?></span></a></li>
				<li><a href="#tab5">应付款&nbsp;&nbsp;<span class="badge badge-success"><?php if($info['payables_count']): echo ($info['payables_count']); endif; ?></span></a></li>
				<li><a href="#tab4"><?php echo L('FILE');?>&nbsp;&nbsp;<span class="badge badge-success"><?php if($info['file_count']): echo ($info['file_count']); endif; ?></span></a></li>
			</ul>
		</div>
		<div class="tab-content span8 mar-lefts" >
			<div class="tab-pane fade in active" id="tab1">
				<div class="container2 top-pad" >
					<span class="basic_information" name="tab">基本信息</span>
					<div class="pull-right"style="margin:-3px 10px 0 0;">
						<a href="<?php echo U('contract/edit', 'id='.$info['contract_id']);?>" class="btn btn-primary"><?php echo L('EDITING');?></a>
						<a href="<?php echo U('contract/delete', 'contract_id='.$info['contract_id']);?>" class="btn btn-primary del_confirm"><?php echo L('DELETE');?></a>
						<a href="javascript:void(0)" onclick="javascript:history.go(-1)" class="btn btn-primary"><?php echo L('RETURN');?></a>
					</div>
				</div>
				<div class="back_box" style="margin-top:10px;">
					<?php if(is_array($alert)): foreach($alert as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><div class="alert alert-<?php echo ($k); ?>">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo ($vv); ?>
		</div><?php endforeach; endif; endforeach; endif; ?>
					<table class="table table-hover">
						<tbody>
							<tr><th <?php if(C('ismobile') == 1): ?>colspan="2"<?php else: ?>colspan="4"<?php endif; ?>><?php echo L('BASIC_INFO');?></th></tr>
							<tr>
								<td class="tdleft" width="15%"><?php echo L('CONTRACT_NO');?></td>
								<td><?php echo ($info["number"]); ?></td>
								<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
								<td class="tdleft" width="15%"><?php echo L('BUSINESS');?></td>
								<td><a href="<?php echo U('business/view','id='.$info['business_id']);?>"><?php echo ($info["business_name"]); ?></a></td>
							</tr>
							<tr>
								<td class="tdleft" width="15%"><?php echo L('CUSTOMER');?></td>
								<td><a href="<?php echo U('customer/view','id='.$info['customer_id']);?>"><?php echo ($info["customer_name"]); ?></a></td>
								<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
								<td class="tdleft"><?php echo L('CREATOR_ROLE');?></td>
								<td><a class="role_info" href="javascript:void(0)" rel="<?php echo ($info['creator_role_id']); ?>"><?php echo ($info["creator_name"]); ?></a></td>
							</tr>
							<tr>
								<td class="tdleft" width="15%"><?php echo L('CONTACTS');?></td>
								<td><?php echo ($info["contacts_name"]); ?></td>
								<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
								<td class="tdleft" width="15%"><?php echo L('OWNER_ROLE');?></td>
								<td><a class="role_info" rel="<?php echo ($info['owner_role_id']); ?>" href="javascript:void(0)"><?php echo ($info['owner_name']); ?></a></a></td>
							</tr>
							<tr>
								<td class="tdleft" width="15%"><?php echo L('THE_TIME_WHICH_THE_CONTRACT_COMES_INTO_FORCE');?></td>
								<td>
									<?php if($info['start_date']): echo (date("Y-m-d",$info['start_date'])); endif; ?>
								</td>
								<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
								<td class="tdleft" width="15%"><?php echo L('CONTRACT_TIME');?></td>
								<td>
									<?php if($info['end_date']): echo (date("Y-m-d",$info['end_date'])); endif; ?>
								</td>
							</tr>
							<tr>
								<td class="tdleft" width="15%"><?php echo L('SIGNING_TIME');?></td>
								<td><?php echo (date("Y-m-d",$info["due_time"])); ?></td>
								<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
								<td class="tdleft" width="15%"><?php echo L('UPDATE_TIME');?></td>
								<td><?php echo (date("Y-m-d",$info["update_time"])); ?></td>
							</tr>
							<tr>
								<td class="tdleft" width="15%"><?php echo L('QUOTATION');?></td>
								<td><?php echo ($info["price"]); ?></td>
								<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
								<td class="tdleft" width="15%"><?php echo L('STATUS');?></td>
								<td><?php echo ($info["status"]); ?></td>
							</tr>
							<tr>
								<td class="tdleft" width="15%"><?php echo L('THE_TERMS_OF_THE_CONTRACT');?></td>
								<td style="word-break:break-word;" <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?>><?php if($info["content"] != null): echo ($info["content"]); endif; ?></td>
							</tr><tr>
								<td class="tdleft" width="15%"><?php echo L('DESCRIBE');?></td>
								<td style="word-break:break-word;" <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?>><?php if($info["description"] != null): echo ($info["description"]); endif; ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="tab-pane fade back_box" id="tab2">
				<div class="header1">
					<div class="pull-left two-title" > <?php echo L('RELATED_PRODUCTS');?> </div>
					<?php if($info['is_deleted'] == 0): ?><div class="pull-right"> <a href="javascript:void(0);" class="btn btn-primary add_product"><?php echo L('ADD');?></a></div><?php endif; ?>
					<div style="clear:both;"></div>
				</div>
				<table class="table">
					<?php if($info["product"] == null): ?><tr>
							<td><?php echo L('THERE_IS_NO_DATA');?> </td>
						</tr>
					<?php else: ?> 
						<thead>
							<tr>
								<td>&nbsp;</td>
								<td><?php echo L('PRODUCT_NAME');?></td>
								<td><?php echo L('PRODUCT_CATEGORY');?></td>
								<?php if(C('ismobile') != 1): ?><td><?php echo L('QUOTATION_YUAN');?>)</td><?php endif; ?>
								<td><?php echo L('TRADING_YUAN');?></td>
								<?php if(C('ismobile') != 1): ?><td><?php echo L('THE_COST_PRICE_YUAN');?></td>
								<td width="30%"><?php echo L('REMARK');?></td><?php endif; ?>
							</tr>
						</thead>
						<tbody>
							<?php if(is_array($info["product"])): $i = 0; $__LIST__ = $info["product"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
									<td class="tdleft"><a href="<?php echo U('product/mdelete', 'r=rContractProduct&id='.$vo['id']);?>" class="del_confirm"><?php echo L('DELETE');?></a>&nbsp;<a class="edit_product" href="javascript:void(0)" rel="<?php echo ($vo["id"]); ?>"><?php echo L('EDITING');?></a></a></td>
									<td>
										<a href="<?php echo U('product/view', 'id='.$vo['product_id']);?>"><?php echo ($vo["info"]["name"]); ?></a>
									</td>
									<td>
										<?php echo ($vo["category_name"]); ?>
									</td>
									<?php if(C('ismobile') != 1): ?><td>
										<?php if($vo['estimate_price'] > 0): echo ($vo["estimate_price"]); endif; ?>
									</td><?php endif; ?>
									<td>
										<?php if($vo['sales_price'] > 0): echo ($vo["sales_price"]); endif; ?>
									</td>
									<?php if(C('ismobile') != 1): ?><td>
										<?php if($vo['info']['cost_price'] > 0): echo ($vo["info"]["cost_price"]); endif; ?>
									</td>
									<td style="word-break:break-word;">
										<?php echo ($vo["description"]); ?>
									</td><?php endif; ?>
								</tr><?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody><?php endif; ?>
				</table>
			</div>
			<div class="tab-pane fade back_box" id="tab3">
				<div class="header1">
					<div class="pull-left two-title" > <?php echo L('THE_ACCOUNTS_RECEIVABLE');?> </div>
						<?php if($info['is_deleted'] == 0): ?><div class="pull-right"> <a href="javascript:void(0);" class="btn btn-primary add_receivables"><?php echo L('ADD');?></a></div><?php endif; ?>
					<div style="clear:both;"></div>
				</div>
				<table class="table">
					<?php if($info["receivables"] == null): ?><tr>
							<td><?php echo L('THERE_IS_NO_DATA');?></td>
						</tr>
					<?php else: ?>
						<thead>
							<tr>
								<td width="10%">&nbsp;</td>
								<td><?php echo L('THE_ACCOUNTS_RECEIVABLE_NAME');?></td>
								<td><?php echo L('STATUS');?></td>
								<td><?php echo L('MONEY');?></td>
								<?php if(C('ismobile') != 1): ?><td><?php echo L('OWNER_ROLE');?></td><?php endif; ?>
							</tr>
						</thead>
						<tbody>
							<?php if(is_array($info["receivables"])): $i = 0; $__LIST__ = $info["receivables"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
									<td class="tdleft">
										<a href="<?php echo U('finance/delete', 't=receivables&id='.$vo['receivables_id'].'&refer=receivables');?>" class="del_confirm"><?php echo L('DELETE');?></a>&nbsp;
										<a class="edit_receivables" href="javascript:void(0);" rel="<?php echo ($vo['receivables_id']); ?>"><?php echo L('EDITING');?></a></a>
										</td>
									<td>
										<a href="<?php echo U('finance/view', 't=receivables&id='.$vo['receivables_id']);?>" ><?php echo ($vo["name"]); ?></a>
									</td>
									<td>
										<?php if($vo['status'] == 2): echo L('HAS_BEEN_RECEIVING'); elseif($vo['status'] == 1): echo L('PART_OF_THE_RECEIVED'); else: echo L('DID_NOT_RECEIVE_PAYMENT'); endif; ?>
									</td>
									<td>
										<?php echo ($vo['price']); ?>
									</td>
									<?php if(C('ismobile') != 1): ?><td>
										<a class="role_info" href="javascript:void(0)" rel="<?php echo ($vo['owner_role_id']); ?>"><?php echo ($vo['owner']['user_name']); ?></a>
									</td><?php endif; ?>
								</tr><?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody><?php endif; ?>
					
				</table>
			</div>
			<div class="tab-pane fade back_box" id="tab5">
				<div class="header1">
					<div class="pull-left two-title" > <?php echo L('THE_ACCOUNTS_PAYABLE');?> </div>
						<?php if($info['is_deleted'] == 0): ?><div class="pull-right"> <a href="javascript:void(0);" class="btn btn-primary add_payables"><?php echo L('ADD');?></a></div><?php endif; ?>
					<div style="clear:both;"></div>
				</div>
				<table class="table">
					<?php if($info["payables"] == null): ?><tr>
							<th colspan="6"><?php echo L('THERE_IS_NO_DATA');?></th>
						</tr>
					<?php else: ?>
						<thead>
							<tr>
								<th width="10%">&nbsp;</th>
								<th><?php echo L('THE_ACCOUNTS_PAYABLE_NAME');?></th>
								<th><?php echo L('STATUS');?></th>
								<th><?php echo L('MONEY');?></th>
								<?php if(C('ismobile') != 1): ?><th><?php echo L('OWNER_ROLE');?></th><?php endif; ?>
							</tr>
						</thead>
						<tbody>
							<?php if(is_array($info["payables"])): $i = 0; $__LIST__ = $info["payables"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
									<td class="tdleft">
										<a href="<?php echo U('finance/delete', 't=payables&id='.$vo['payables_id'].'&refer=payables');?>" class="del_confirm"><?php echo L('DELETE');?></a>&nbsp;
										<a class="edit_payables" href="javascript:void(0);" rel="<?php echo ($vo['payables_id']); ?>"><?php echo L('EDITING');?></a></a>
										</td>
									<td>
										<a href="<?php echo U('finance/view', 't=payables&id='.$vo['payables_id']);?>"><?php echo ($vo["name"]); ?></a>
									</td>
									<td>
										<?php if($vo['status'] == 2): echo L('PAYMENT_HAS_BEEN'); elseif($vo['status'] == 1): echo L('PART_OF_THE_PREPAID'); else: echo L('NOT_PAYING'); endif; ?>
									</td>
									<td>
										<?php echo ($vo['price']); ?>
									</td>
									<?php if(C('ismobile') != 1): ?><td>
										<a class="role_info" href="javascript:void(0)" rel="<?php echo ($vo['owner_role_id']); ?>"><?php echo ($vo['owner']['user_name']); ?></a>
									</td><?php endif; ?>
								</tr><?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody><?php endif; ?>
				</table>
			</div>
			<div class="tab-pane fade back_box" id="tab4">
				<div class="header1">
					<div class="pull-left two-title" > <?php echo L('RELATED_FILE');?> </div>
						<?php if($info['is_deleted'] == 0): ?><div class="pull-right"><a href="javascript:void(0);" class="add_file btn btn-primary"><?php echo L('ADD');?></a></div><?php endif; ?>
					<div style="clear:both;"></div>
				</div>
				<table class="table">
					<?php if($info["file"] == null): ?><tr>
							<td><?php echo L('THERE_IS_NO_DATA');?> </td>
						</tr>
					<?php else: ?> 
						<tr>
							<td>&nbsp;</td>
							<td><?php echo L('FILE_NAME');?></td>
							<td><?php echo L('SIZE');?></td>
							<td><?php echo L('ADDED_BY');?></td>
							<td><?php echo L('ADD_TIME');?></td>
						</tr>
						<?php if(is_array($info["file"])): $i = 0; $__LIST__ = $info["file"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
								<td class="tdleft"><a href="<?php echo U('file/delete','r=rContractFile&id='.$vo['file_id']);?>" class="del_confirm"><?php echo L('DELETE');?></a></td>
								<td>
									<a target="_blank" href="<?php echo ($vo["file_path"]); ?>"><?php echo ($vo["name"]); ?></a>
								</td>
								<td>
									<?php echo ($vo["size"]); echo L('BYTE');?>
								</td>
								<td>
									<?php if(!empty($vo["owner"]["user_name"])): echo ($vo["owner"]["user_name"]); ?> [<?php echo ($vo["owner"]["department_name"]); ?>-<?php echo ($vo["owner"]["role_name"]); ?>]<?php endif; ?>
								</td>
								<td>
									<?php if(!empty($vo["create_date"])): echo (date("Y-m-d g:i:s a",$vo["create_date"])); endif; ?>
								</td>
							</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
				</table>
			</div>
		</div>
		<div class="span2 bs-docs-sidebar mar-lefts2" id="right_list" >
			<ul class="nav nav-list bs-docs-sidenav  span2 widths" >
				<li class="active first-li"><span class="spans1">编辑详情</span></li>
				<li><a href="javascript:void(0);" class="add_product"><img src="__PUBLIC__/img/youce.png"/>&nbsp;&nbsp;&nbsp;<?php echo L('ADD_PRODUCT');?></a> </li>
				<li><a href="javascript:void(0);" class="add_file"><img src="__PUBLIC__/img/youce.png"/>&nbsp;&nbsp;&nbsp;<?php echo L('ADD_ACCESSORY');?></a></li>
			</ul>
		</div>
	</div>
</div>
<div class="hide" id="dialog-product" title="<?php echo L('ADD_PRODUCT');?>">loading...</div>
<div class="hide" id="dialog-edit" title="<?php echo L('AMENDING_THE_CONTRACT_PRODUCT_INFORMATION');?>">loading...</div>
<div class="hide" id="dialog-role-info" title="<?php echo L('DIALOG_USER_INFO');?>">loading...</div>
<div class="hide" id="dialog-receivables" title="<?php echo L('ADD_THE_ACCOUNTS_RECEIVABLE');?>">loading...</div>
<div class="hide" id="dialog-payables" title="添加应付款">loading...</div>
<div class="hide" id="dialog-receivables-edit" title="编辑应收款">loading...</div>
<div class="hide" id="dialog-payables-edit" title="编辑应付款">loading...</div>
<div class="hide" id="dialog-file" title="<?php echo L('ADD_ACCESSORY');?>">loading...</div>
<script>
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
$("#dialog-product").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight:400,
	position: ["center",100]
});
$("#dialog-edit").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight:400,
	position: ["center",100]
});
$("#dialog-role-info").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight:400,
	position: ["center",100]
});
$("#dialog-receivables").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight:400,
	position: ["center",100]
});
$("#dialog-receivables-edit").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight:400,
	position: ["center",100]
});
$("#dialog-payables").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight:450,
	position: ["center",100]
});
$("#dialog-payables-edit").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight:450,
	position: ["center",100]
});
$("#dialog-file").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight: 400,
	position: ["center",100]
});
$(function(){
	$(".edit_product").click(function(){
		id = $(this).attr('rel');
		$('#dialog-edit').dialog('open');
		$('#dialog-edit').load('<?php echo U("product/editdialog","r=RContractProduct&id");?>'+id);
	});
	$(".add_product").click(function(){
		$('#dialog-product').dialog('open');
		$('#dialog-product').load('<?php echo U("product/adddialog","r=RContractProduct&module=contract&id=".$info["contract_id"]);?>');
	});
	$(".role_info").click(function(){
		$role_id = $(this).attr('rel');
		$('#dialog-role-info').dialog('open');
		$('#dialog-role-info').load('<?php echo U("user/dialoginfo","id=");?>'+$role_id);
	});
	$(".add_receivables").click(function(){
		$('#dialog-receivables').dialog('open');
		$('#dialog-receivables').load('<?php echo U("finance/adddialog", "t=receivables&contract_id=".$info['contract_id']);?>');
	});
	$(".edit_receivables").click(function(){
		var receivables_id = $(this).attr('rel');
		$('#dialog-receivables-edit').dialog('open');
		$('#dialog-receivables-edit').load('<?php echo U("finance/editdialog", "t=receivables&id=");?>'+receivables_id);
	});
	$(".add_payables").click(function(){
		$('#dialog-payables').dialog('open');
		$('#dialog-payables').load('<?php echo U("finance/adddialog", "t=payables&contract_id=".$info['contract_id']);?>');
	});
	$(".edit_payables").click(function(){
		var payables_id = $(this).attr('rel');
		$('#dialog-payables-edit').dialog('open');
		$('#dialog-payables-edit').load('<?php echo U("finance/editdialog", "t=payables&id=");?>'+payables_id);
	});
	$(".add_file").click(function(){
		$('#dialog-file').dialog('open');
		$('#dialog-file').load('<?php echo U("file/add","r=RContractFile&module=contract&id=".$info["contract_id"]);?>');
	});
});
</script>

</body>
</html>