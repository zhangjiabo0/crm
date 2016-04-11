<?php if (!defined('THINK_PATH')) exit();?><div class="clearfix">
	<ul class="nav pull-left">			
		<li class="pull-left" ><?php echo L('TO_FIND_THE_CONDITIONS');?>
			<select style="width:auto" id="fields" onchange="changeCondition()" name="field">
            <option class="" value="">--<?php echo L('SELECT_THE_FILTER');?>--</option>
				<?php if(is_array($field_array)): $i = 0; $__LIST__ = $field_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if($i < 4): ?><option class="<?php echo ($v['form_type']); ?>" value="<?php echo ($v[field]); ?>"><?php echo ($v[name]); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
				<option class="role" value="owner_role_id"><?php echo L('PRINCIPAL');?></option>
				<option class="date" value="create_time"><?php echo L('CREATION_TIME');?></option>
				<option class="date" value="update_time"><?php echo L('MODIFICATION_TIME');?></option>
			</select>&nbsp;&nbsp;
		</li>
		<li id="conditionContent" class="pull-left">
			<select id="conditions" style="width:auto" name="condition" onchange="changeSearch()">
				<option value="contains"><?php echo L('INCLUDE');?></option>
				<option value="not_contain"><?php echo L('EXCLUSIVE');?></option>
				<option value="is"><?php echo L('YES');?></option>
				<option value="isnot"><?php echo L('ISNOT');?></option>						
				<option value="start_with"><?php echo L('BEGINNING_CHARACTER');?></option>
				<option value="end_with"><?php echo L('TERMINATION_CHARACTER');?></option>
				<option value="is_empty"><?php echo L('MANDATORY');?></option>
				<option value="is_not_empty"><?php echo L('ISNOTEMPTY');?></option>
			</select>&nbsp;&nbsp;
		</li>
		<li id="searchContent" class="pull-left">
			<input id="searchs" type="text" class="input-medium search-query" name="search"/>&nbsp;&nbsp;
		</li>
		<li class="pull-left">
			<button class="btn" onclick="d_changeCondition(0)"><?php echo L('SEARCH');?></button>
		</li>
		&nbsp;<a target="_blank" href="<?php echo U('customer/add');?>"><?php echo L('CREATE_NEW_CUSTOMER');?></a>
	</ul>
</div>
<?php if(empty($customerList)): ?><div class="alert"><?php echo L('TEMPORARILY_NO_DATA');?></div>
<?php else: ?>
<table class="table table-hover">
	<thead>
		<tr>
			<th>&nbsp;</th>
            <?php if(is_array($field_array)): $i = 0; $__LIST__ = $field_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if($i < 4): ?><th><?php echo (($v[name])?($v[name]):'&nbsp;'); ?></th><?php endif; endforeach; endif; else: echo "" ;endif; ?>
		</tr>
	</thead>
	<tfoot id="footers">
		<tr>
			<td colspan="<?php echo ($field_num); ?>">
				<div class="row pagination">
					<div class="span2"><span id="counts"><?php echo ($count_num); ?></span> <?php echo L('RECORDS');?> <span id="ps">1</span>/<span id="total_pages"><?php echo ($total); ?></span> <?php echo L('PAGE');?></div>
					<div class="span4">
						<div><ul id="changepages">
							<li><span class='current'><?php echo L('FIRST_PAGE');?></span></li><li><span>« <?php echo L('THE_PREVIOUS_PAGE');?></span></li>
							<?php if(1 < $total): ?><li><a class="page" href="javascript:void(0)" rel="2"><?php echo L('THE_NEXT_PAGE');?> »</a></li>
							<?php else: ?>
								<li><span><?php echo L('THE_NEXT_PAGE');?> »</span></li><?php endif; ?>
						</ul></div>
					</div>
				</div>
			</td>
		</tr>
	</tfoot>
	<tbody id="loads" class="hide">
		<tr><td class="tdleft" <?php if(C('ismobile') != 1): ?>colspan="6"<?php else: ?>colspan="4"<?php endif; ?> style=" height:300px;text-align:center"><img src="./Public/img/load.gif"></td></tr>
	</tbody>
	<tbody id="datas">
		<?php if(is_array($customerList)): $i = 0; $__LIST__ = $customerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
				<td>
					<input type="radio" name="customer" value="<?php echo ($vo["customer_id"]); ?>" rel="<?php echo ($vo["contacts_id"]); ?>" />
					<input type="hidden" name="contacts_name" value="<?php echo ($vo["contacts_name"]); ?>" />
				</td>
                <?php if(is_array($field_array)): $i = 0; $__LIST__ = $field_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if($i < 4): ?><td><?php echo ($vo[$v['field']]); ?></td><?php endif; endforeach; endif; else: echo "" ;endif; ?>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	</tbody>
</table><?php endif; ?>
<script type="text/javascript">
	$('.page').click(function(){
		var a = $(this).attr('rel');
		d_changeCondition(a);
	});
	function d_changeCondition(p){
		$('#datas').addClass('hide');
		$('#loads').removeClass('hide');
		
		var field = $('#fields').val();
		var condition = $('#conditions').val();
		var asearch = encodeURI($("#searchs").val());
		$.ajax({
			type:'get',
			url:'index.php?m=customer&a=changecontent&field='+field+'&search='+asearch+'&condition='+condition+'&p='+p<?php if($customer_id): ?>+"&customer_id=<?php echo ($customer_id); ?>"<?php endif; ?>,
			async:false,
			success:function(data){			
				var temp = '';
				if(data.data.list != null){
					$.each(data.data.list, function(k, v){
						temp += "<tr><td><input type='radio' name='customer' value='"+v.customer_id+"'/><input type='hidden' name='contacts_name' value='"+v.contacts_name+"'/></td>";
                        <?php if(is_array($field_array)): $i = 0; $__LIST__ = $field_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if($i < 4): ?>temp +=  "<td>" + v.<?php echo ($v[field]); ?> + "</td>";<?php endif; endforeach; endif; else: echo "" ;endif; ?>
                        temp +=  "</tr>";
					});
					var changepage = "";
					if(data.data.p == 1){
						changepage = "<li><span class='current'><?php echo L('FIRST_PAGE');?></span></li><li><span>« <?php echo L('THE_PREVIOUS_PAGE');?></span></li>";
						if(data.data.p < data.data.total){
							changepage += "<li><a class='page' href='javascript:void(0)' rel='"+(data.data.p+1)+"'><?php echo L('THE_NEXT_PAGE');?> »</a></li>";
						}else{
							changepage += "<li><span><?php echo L('THE_NEXT_PAGE');?> »</span></li>";
						}
					}else if(data.data.p == data.data.total){
						changepage = "<li><a class='page' href='javascript:void(0)' rel='1'><?php echo L('FIRST_PAGE');?></a></li><li><a class='page' href='javascript:void(0)' rel='"+(data.data.p-1)+"'>« <?php echo L('THE_PREVIOUS_PAGE');?></a></li><li><span><?php echo L('THE_NEXT_PAGE');?> »</span></li>";
					}else{
						changepage = "<li><a class='page' href='javascript:void(0)' rel='1'><?php echo L('FIRST_PAGE');?></a></li><li><a class='page' href='javascript:void(0)' rel='"+(data.data.p-1)+"'>« <?php echo L('THE_PREVIOUS_PAGE');?></a></li><li><a class='page' href='javascript:void(0)' rel='"+(data.data.p+1)+"'><?php echo L('THE_NEXT_PAGE');?> »</a></li>";
					}				
					$('#ps').html(data.data.p);
					$('#changepages').html(changepage);
					$('#counts').html(data.data.count);
					$('#total_pages').html(data.data.total);				
					$('#datas').html(temp);
					$('.page').click(function(){
						var a = $(this).attr('rel');
						d_changeCondition(a);
					});
				}else{
					$('#datas').html('<tr><td colspan="4"><?php echo L('DO_NOT_FIND_THE_RESULTS_YOU_WANT');?></tr>');
					$('#footers').addClass('hide');
				}
				$('#loads').addClass('hide');
				$('#datas').removeClass('hide');
			},
			dataType:'json'
		});		
	}
</script>