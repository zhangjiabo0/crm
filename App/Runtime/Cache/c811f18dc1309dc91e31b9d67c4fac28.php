<?php if (!defined('THINK_PATH')) exit();?><div>
	<ul class="nav pull-left">
		<li class="pull-left">
			&nbsp;&nbsp;
			<select id="field" style="width:auto" onchange="changeCondition()" name="field">
				<option class="word" value="name"><?php echo L('PRODUCT_NAME');?></option>
			</select>&nbsp;&nbsp;
		</li>
		<li id="conditionContent" class="pull-left">
			<select id="condition" style="width:auto" name="condition" onchange="changeSearch()">	
				<option value="contains"><?php echo L('INCLUDE');?></option>
				<option value="is"><?php echo L('YES');?></option>
				<option value="start_with"><?php echo L('BEGINNING_CHARACTER');?></option>
				<option value="end_with"><?php echo L('TERMINATION_CHARACTER');?></option>
				<option value="is_empty"><?php echo L('MANDATORY');?></option>
			</select>&nbsp;&nbsp;
		</li>
		<li id="searchContent" class="pull-left">
			<input id="search" type="text" class="input-medium search-query" name="search"/>&nbsp;&nbsp;
		</li>
		<li class="pull-left">
			<input type="hidden" name="m" value="product"/>
			<?php if($_GET['by']!= null): ?><input type="hidden" name="by" value="<?php echo ($_GET['by']); ?>"/><?php endif; ?>
			<button type="submit" onclick="d_changeCondition(0)" class="btn"><?php echo L('SEARCH');?></button>
		</li>
	</ul>
</div>
<p>&nbsp;</p>
<?php if(empty($list)): ?><div class="alert"><?php echo L('THERE_IS_NO_DATA');?></div>
<?php else: ?>
<table class="table table-hover">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th><?php echo L('PRODUCT_NAME');?></th>
			<th><?php echo L('PRODUCT_CATEGORY');?></th>
	</thead>
	<tfoot id="footer">
		<tr>
			<?php if(C('ismobile') != 1): ?><td colspan="6"><?php else: ?><td colspan="4"><?php endif; ?>
				<div class="row pagination">
					<div class="span2"><span id="count"><?php echo ($count_num); ?></span> <?php echo L('RECORDS');?> <span id="p">1</span>/<span id="total_page"><?php echo ($total); ?></span> <?php echo L('PAGE');?></div>
					<div class="span4">
						<div><ul id="changepage">
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
	<tbody id="load" class="hide">
		<tr><td class="tdleft" <?php if(C('ismobile') != 1): ?>colspan="6"<?php else: ?>colspan="4"<?php endif; ?> style=" height:300px;text-align:center"><img src="./Public/img/load.gif"></td></tr>
	</tbody>
	<tbody id="data">
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
				<td>
					<input type="radio" name="product_id" value="<?php echo ($vo["product_id"]); ?>"/>
					<input type="hidden" value="<?php echo ($vo["standard"]); ?>"/>
					<input type="hidden" value="<?php echo ($vo["suggested_price"]); ?>"/>
				</td>
				<td><?php echo ($vo["name"]); ?></td>
				<td><?php echo ($vo["category_name"]); ?></td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	</tbody>
</table>
<script type="text/javascript">
	$('.page').click(function(){
		var a = $(this).attr('rel');
		d_changeCondition(a);
	});
	function d_changeCondition(p){
		$('#data').addClass('hide');
		$('#load').removeClass('hide');
		
		var field = $('#field').val();
		var condition = $('#condition').val();
		var search = encodeURI($("#search").val());
		$.ajax({
			type:'get',
			url:'index.php?m=product&a=changecontent&field='+field+'&search='+search+'&condition='+condition+'&p='+p,
			async:false,
			success:function(data){
				var temp = '';
				if(data.data.list != null){
					$.each(data.data.list, function(k, v){
						temp += '<tr><td><input type="radio" name="product_id" value="'+v.product_id+'"/></td><td>'+v.name+'</td><td>'+v.category_name+'</td></tr>';
					});
					changepage = "";
					if(data.data.p == 1){
						changepage = "<li><span class='current'><?php echo L('FIRST_PAGE');?></span></li><li><span>« <?php echo L('THE_PREVIOUS_PAGE');?> </span></li>";
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
					$('#p').html(data.data.p);
					$('#changepage').html(changepage);
					$('#count').html(data.data.count);
					$('#total_page').html(data.data.total);
					$('#data').html(temp);
					$('.page').click(function(){
						var a = $(this).attr('rel');
						d_changeCondition(a);
					});
				}else{
					$('#data').html('<tr><td colspan="4"><?php echo L('DIDNOT_FIND_THE_RESULTS_YOU_WANT');?></tr>');
					$('#footer').addClass('hide');
				}
				$('#load').addClass('hide');
				$('#data').removeClass('hide');
			},
			dataType:'json'
		});		
	}
</script><?php endif; ?>