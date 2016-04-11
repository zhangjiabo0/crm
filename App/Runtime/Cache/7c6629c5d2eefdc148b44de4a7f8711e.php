<?php if (!defined('THINK_PATH')) exit();?><div class="clearfix">
	<ul class="nav pull-left">			
		<li class="pull-left" ><?php echo L('SEARCH_CONDITION');?>
			<select id="field" style="width:auto" onchange="changeCondition()" name="field">
				<option class="all" value="all"><?php echo L('ANY_FIELDS');?></option>
				<option class="word" value="subject"><?php echo L('TASK_SUBJECT');?></option>
				<option class="role" value="owner_role_id"><?php echo L('EXECUTOR');?></option>
				<option class="role" value="creator_role_id"><?php echo L('CREATOR_ROLE');?></option>
				<option class="task_status" value="status"><?php echo L('TASK_STATUS');?></option>
				<option class="task_priority" value="priority"><?php echo L('PRECEDENCE');?></option>
				<option class="word" value="description"><?php echo L('DESCRIPTION');?></option>
				<option class="date" value="due_date"><?php echo L('DEADLINE');?></option>
				<option class="date" value="create_date"><?php echo L('CREATE_DATE');?></option>
				<option class="date" value="update_date"><?php echo L('UPDATE_DATE');?></option>
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
			<button class="btn" onclick="d_changeCondition(0)"><?php echo L('SEARCH');?></button>
		</li>
		&nbsp;<a target="_blank" href="<?php echo U('task/add');?>"><?php echo L('CREATE_TASK');?></a>
	</ul>
</div>
<?php if(empty($task_list)): ?><div class="alert"><?php echo L('EMPTY_DATA');?></div>
<?php else: ?>
<table class="table table-hover">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th><?php echo L('THEME');?></th>
			<th><?php echo L('RELATED_INFO');?></th>
			<th><?php echo L('EXECUTOR');?></th>	
			<th><?php echo L('STATUS');?></th>
			<th><?php echo L('PRECEDENCE');?></th>
		</tr>
	</thead>
	<tfoot id="footer">
		<tr>
			<td colspan="6">
				<div class="row pagination">
					<div class="span2">
						<?php echo L('PAGE_COUNT', array($count_num ,$total));?>
					</div>
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
		<tr><td class="tdleft" colspan="6" style=" height:300px;text-align:center"><img src="./Public/img/load.gif"></td></tr>
	</tbody>
	<tbody id="data">
		<?php if(is_array($task_list)): $i = 0; $__LIST__ = $task_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
				<td>
					<input type="radio" name="task_id" value="<?php echo ($vo["task_id"]); ?>" rel="<?php echo ($vo["task_id"]); ?>" />
				</td>
				<td><?php echo ($vo["subject"]); ?></td>
				<td><?php echo ($vo["module"]["module_name"]); ?> <?php echo ($vo["module"]["name"]); ?></td>
				<td>
					<?php if(is_array($vo["owner"])): $i = 0; $__LIST__ = $vo["owner"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; echo ($v["user_name"]); ?>,<?php endforeach; endif; else: echo "" ;endif; ?>
				</td>
				<td><?php echo ($vo["status"]); ?></td>
				<td><?php echo ($vo["priority"]); ?></td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	</tbody>
</table><?php endif; ?>
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
		var asearch = encodeURI($("#search").val());
		$.ajax({
			type:'get',
			url:'index.php?m=task&a=changecontent&field='+field+'&search='+asearch+'&condition='+condition+'&p='+p,
			async:false,
			success:function(data){
				var temp = '';
				if(data.data.list != null){
					$.each(data.data.list, function(k, v){
						temp += "<tr><td><input type='radio' name='task_id' value='"+v.task_id+"'/></td>";
						temp +=  '<td>'+v.subject+'</td>';
						if(v.module){
							temp +=  '<td>'+v.module.module_name+v.module.name+'</td>';
						}else{
							temp +=  '<td>&nbsp;</td>';
						}
						var t = '';
						$.each(v.owner,function(key,val){
							t += val.user_name+',';
						});
						temp +=  '<td>'+t+'</td>';
						temp +=  '<td>'+v.status+'</td>';
						temp +=  '<td>'+v.priority+'</td>';
                        temp +=  "</tr>";
					});
					var changepage = "";
					if(data.data.p == 1){
						changepage = "<li><span class='current'><?php echo L('HOME_PAGE');?></span></li><li><span><?php echo L('PRE_PAGE');?> </span></li>";
						if(data.data.p < data.data.total){
							changepage += "<li><a class='page' href='javascript:void(0)' rel='"+(data.data.p+1)+"'> <?php echo L('NEXT_PAGE');?></a></li>";
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
						var a = $(this).attr('rel');
						d_changeCondition(a);
					});
				}else{
					$('#data').html('<tr><td colspan="4"><?php echo L("NOT_FIND_THE_RESULTS_YOU_WANT");?></tr>');
					$('#footer').addClass('hide');
				}
				$('#load').addClass('hide');
				$('#data').removeClass('hide');
			},
			dataType:'json'
		});		
	}
</script>