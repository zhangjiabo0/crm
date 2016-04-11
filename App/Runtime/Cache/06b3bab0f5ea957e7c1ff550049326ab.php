<?php if (!defined('THINK_PATH')) exit();?><div class="clearfix">
	<ul class="nav pull-left">
		<li class="pull-left">
			<select id="field" style="width:auto" onchange="changeCondition()" name="field">
				<option class="all" value="all"><?php echo L('ANY_FIELD');?></option>
				<option class="word" value="number"><?php echo L('CONTRACT_NO');?></option>
				<option class="role" value="owner_role_id"><?php echo L('OWNER_ROLE');?></option>
				<option class="customer" value="customer_id"><?php echo L('CUSTOMER');?></option>
				<option class="date" value="create_time"><?php echo L('CREATION_DATE');?></option>
				<option class="date" value="update_time"><?php echo L('MODIFICATION_DATE');?></option>
				<option class="date" value="due_time"><?php echo L('MODIFICATION_DATE');?></option>
				<option class="number" value="price"><?php echo L('QUOTATION');?></option>
				<option class="word" value="description"><?php echo L('REMARK');?></option>
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
			<button type="submit" onclick="d_changeCondition(0)" class="btn"><?php echo L('SEARCH');?></button>
		</li>
		&nbsp;<a target="_blank" href="<?php echo U('contract/add');?>"><?php echo L('THE_NEW_CONTRACT');?></a>
	</ul>
</div>
<?php if(empty($contractList)): ?><div class="alert"><?php echo L('EMPTY_TPL_DATA');?></div>
<?php else: ?>
	<table class="table table-hover">
		<?php if($contractList == null): ?><tr><td><?php echo L('EMPTY_TPL_DATA');?></td></tr>
		<?php else: ?>
		<thead>
			<tr> 
				<th></th>
				<th><?php echo L('CONTRACT_NO');?></th>
				<th class="hide"></th>
				<th><?php echo L('CUSTOMER');?></th>
				<th><?php echo L('QUOTATION');?></th>
			</tr>
		</thead> 
		<tfoot id="footer">
			<tr>
				<td colspan="4">
					<div class="row pagination">
						<div class="span2"><?php echo L('PAGE_COUNT',array($count_num,$total));?></div>
						<div class="span4">
							<div><ul id="changepage">
								<li><span class='current'><?php echo L('HOME_PAGE');?></span></li><li><span><?php echo L('PRE_PAGE');?></span></li>
								<?php if(1 < $total): ?><li><a class="page" href="javascript:void(0)" rel="2"><?php echo L('NEXT_PAGE');?></a></li>
								<?php else: ?>
									<li><span><?php echo L('NEXT_PAGE');?></span></li><?php endif; ?>
							</ul></div>
						</div>
					</div>
				</td>
			</tr>
		</tfoot>
		<tbody id="load" class="hide">
			<tr><td class="tdleft" <?php if(C('ismobile') != 1): ?>colspan="6"<?php else: ?>colspan="4"<?php endif; ?> style=" height:300px;text-align:center"><img src="__PUBLIC__/img/load.gif"></td></tr>
		</tbody>
		<tbody id="data">
			<?php if(is_array($contractList)): $i = 0; $__LIST__ = $contractList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
					<td><input type="radio" name="contract" value="<?php echo ($vo["contract_id"]); ?>"/></td>
					<td><?php echo ($vo["number"]); ?></td>
					<td class="hide"><?php echo ($vo["customer_id"]); ?></td>
					<td><?php echo ($vo["customer_name"]); ?></td>
					<td><?php echo ($vo["price"]); ?></td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody><?php endif; ?>
	</table>
	
<script type="text/javascript">
	$('.page').click(function(){
		a = $(this).attr('rel');
		d_changeCondition(a);
	});
	function d_changeCondition(p){
		$('#data').addClass('hide');
		$('#load').removeClass('hide');
		
		field = $('#field').val();
		condition = $('#condition').val();
		asearch = $("#search").val();
		$.ajax({
			type:'get',
			url:'index.php?m=contract&a=changecontent&field='+field+'&search='+asearch+'&condition='+condition+'&p='+p,
			async:false,
			success:function(data){
				temp = '';
				if(data.data.list != null){
					$.each(data.data.list, function(k, vo){
						temp += '<tr><td><input type="radio" name="contract" value="'+vo.contract_id+'"/></td><td>'+vo.number+'</td><td class="hide">'+vo.customer_id+'</td><td>'+vo.customer_name+'</td><td>'+vo.price+'</td></tr>';
					});
					changepage = "";
					if(data.data.p == 1){
						changepage = "<li><span class='current'><?php echo L('HOME_PAGE');?></span></li><li><span><?php echo L('PRE_PAGE');?> </span></li>";
						if(data.data.p < data.data.total){
							changepage += "<li><a class='page' href='javascript:void(0)' rel='"+(data.data.p+1)+"'><?php echo L('NEXT_PAGE');?></a></li>";
						}else{
							changepage += "<li><span><?php echo L('NEXT_PAGE');?></span></li>";
						}
					}else if(data.data.p == data.data.total){
						changepage = "<li><a class='page' href='javascript:void(0)' rel='1'><?php echo L('HOME_PAGE');?></a></li><li><a class='page' href='javascript:void(0)' rel='"+(data.data.p-1)+"'><?php echo L('PRE_PAGE');?></a></li><li><span><?php echo L('NEXT_PAGE');?></span></li>";
					}else{
						changepage = "<li><a class='page' href='javascript:void(0)' rel='1'><?php echo L('HOME_PAGE');?></a></li><li><a class='page' href='javascript:void(0)' rel='"+(data.data.p-1)+"'><?php echo L('PRE_PAGE');?></a></li><li><a class='page' href='javascript:void(0)' rel='"+(data.data.p+1)+"'><?php echo L('NEXT_PAGE');?></a></li>";
					}
					$('#p').html(data.data.p);
					$('#changepage').html(changepage);
					$('#count').html(data.data.count);
					$('#total_page').html(data.data.total);
					$('#data').html(temp);
					$('.page').click(function(){
						a = $(this).attr('rel');
						d_changeCondition(a);
					});
				}else{
					$('#data').html('<tr><td colspan="4"><?php echo L('DID_NOT_FIND_THE_RESULTS_YOU_WANT');?></tr>');
					$('#footer').addClass('hide');
				}
				$('#load').addClass('hide');
				$('#data').removeClass('hide');
			},
			dataType:'json'
		});		
	}
</script><?php endif; ?>