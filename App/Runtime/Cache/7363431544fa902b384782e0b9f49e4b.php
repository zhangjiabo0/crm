<?php if (!defined('THINK_PATH')) exit();?><div class="clearfix">
	<ul class="nav pull-left">
		<li class="pull-left">
			<select style="width:auto" id="field" name="field" onchange="changeCondition()">
				<option class="word" value="name"><?php echo L('NAME');?></option>
				<option class="word" value="telephone"><?php echo L('PHONE');?></option>
				<option class="word" value="email"><?php echo L('EMAIL');?></option>
				<option class="word" value="post"><?php echo L('POSITION');?></option>
			</select>&nbsp;&nbsp;
		</li>
		<li id="conditionContent" class="pull-left">
			<select id="d_condition" style="width:auto" name="condition" onchange="changeSearch()">
				<option value="contains"><?php echo L('CONTAINS');?></option>
				<option value="not_contain"><?php echo L('NOT_CONTAIN');?></option>
				<option value="is"><?php echo L('IS');?></option>
				<option value="isnot"><?php echo L('ISNOT');?></option>						
				<option value="start_with"><?php echo L('START_WITH');?></option>
				<option value="end_with"><?php echo L('END_WITH');?></option>
				<option value="is_empty"><?php echo L('IS_EMPTY');?></option>
				<option value="is_not_empty"><?php echo L('IS_NOT_EMPTY');?></option>
			</select>&nbsp;&nbsp;
		</li>
		<li id="searchContent" class="pull-left">
			<input id="dsearch" type="text" class="input-medium search-query" name="search"/>&nbsp;&nbsp;
		</li>
		<li class="pull-left">
			<input type="button" onclick="d_changeContent(0)" class="btn" value="<?php echo L('SEARCH');?>"/>
		</li>
		&nbsp;<a target="_blank" href="<?php echo U('contacts/add','redirect=customer&redirect_id='.$customer_id);?>"><?php echo L('NEW_LINK');?></a>
	</ul>
</div>
<?php if(empty($contactsList)): ?><div class="alert"><?php echo L('EMPTY_TPL_DATA');?></div>
<?php else: ?>
<table class="table table-hover">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th width="15%"><?php echo L('NAME');?></th>
			<th width="30%"><?php echo L('BELONGS TO THE CUSTOMER');?></th>
			<?php if(C('ismobile') != 1): ?><th width="15%"><?php echo L('PHONE');?></th>
			<th width="15%"><?php echo L('EMAIL');?></th><?php endif; ?>	
			<th width="25%"><?php echo L('POSITION');?></th>
		</tr>
	</thead>
	<tfoot id="footer">
		<tr>
			<td colspan="6">
				<div class="row pagination">
					<div class="span2"><?php echo L('PAGE_COUNT',array($count_num,$total));?></div>
					<div class="span4">
						<div><ul id="changepage">
							<li><span class='current'><?php echo L('HOME_PAGE');?></span></li><li><span><?php echo L('PRE_PAGE');?></span></li>
							<?php if($total > 1): ?><li><a class="page" href="javascript:void(0)" rel="2"><?php echo L('NEXT_PAGE');?></a></li>
							<?php else: ?>
								<li><span><?php echo L('NEXT_PAGE');?></span></li><?php endif; ?>
						</ul></div>
					</div>
				</div>
			</td>
		</tr>
	</tfoot>
	<tbody id="load" class="hide">
		<tr><td class="tdleft" <?php if(C('ismobile') != 1): ?>colspan="6"<?php else: ?>colspan="4"<?php endif; ?> style=" height:300px;text-align:center"><img src="./Public/img/load.gif"></td></tr>
	</tbody>
	<tbody id="data2">
		<?php if(is_array($contactsList)): $i = 0; $__LIST__ = $contactsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
				<td><input type="radio" name="contacts" value="<?php echo ($vo["contacts_id"]); ?>"/></td>
				<td><?php echo ($vo["name"]); ?></td>
				<td><a target="_blank" href="<?php echo U('customer/view','id='.$vo['customer']['customer_id']);?>"><?php echo ($vo["customer"]["name"]); ?></a></td>
				<?php if(C('ismobile') != 1): ?><td><?php echo ($vo["telephone"]); ?></td>
				<td><?php echo ($vo["email"]); ?></td><?php endif; ?>			
				<td><?php echo ($vo["post"]); ?></td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	</tbody>
</table>
<script type="text/javascript">
	$('.page').click(function(){
		var a = $(this).attr('rel');
		d_changeContent(a);
	});
	function d_changeContent(p){
		$('#data2').addClass('hide');
		$('#load').removeClass('hide');
		var search = encodeURI($("#dsearch").val());
        if(search != ''){
            var field = $('#field').val();
        }else{
            var field = '';
        }
		
		var condition = $('#condition').val();
		<?php if($customer_id != null): ?>var customer_id = <?php echo ($customer_id); ?>;<?php else: ?>var customer_id=0;<?php endif; ?>
		
		link = "<?php echo U('customer/view');?>";
		$.ajax({
			type:'get',
			url:'index.php?m=contacts&a=changedialog&field='+field+'&search='+search+'&condition='+condition+'&p='+p+'&customer_id='+customer_id,
			async:false,
			success:function(data){
				var temp = '';
				if(data.data.list != null){
					$('#footer').removeClass('hide');
					$.each(data.data.list, function(k, v){
						
						temp += "<tr><td><input type='radio' name='contacts' value='"+v.contacts_id+"'/></td><td>"+v.name+"</td>";
						if(v.customer != null) {
							temp += "<td><a target='_blank' href='"+link+"&id="+v.customer.customer_id+"'>"+v.customer.name+"</a></td>";
						}else{
							temp += "<td>&nbsp;</td>";
						}
						
						<?php if(C('ismobile') != 1): ?>temp += "<td>"+v.telephone+"</td><td>"+v.email+"</td>";<?php endif; ?>
						temp += "<td>"+v.post+"</td></tr>";
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
					$('#data2').html(temp);
					$('.page').click(function(){
						var a = $(this).attr('rel');
						d_changeContent(a);
					});
				}else{
					$('#data2').html('<tr><td colspan="4"><?php echo L('DID_NOT_FIND_THE_RESULTS_YOU_WANT');?></tr>');
					$('#footer').addClass('hide');
				}
				$('#load').addClass('hide');
				$('#data2').removeClass('hide');
			},
			dataType:'json'
		});
	}
</script><?php endif; ?>