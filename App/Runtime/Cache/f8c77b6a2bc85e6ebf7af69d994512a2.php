<?php if (!defined('THINK_PATH')) exit();?><form action="<?php echo U('Message/send');?>" method="post">
	<table type="hidden" class="table table-hover">
		<tfoot>
			<tr>
				<td>&nbsp;</td>
				<td ><input class="btn btn-primary" type="submit" name="submit" value="<?php echo L('SEND');?>"/> &nbsp; <input class="btn" onclick="javascript:$('#dialog-message-send').dialog('close');" type="button" value="<?php echo L('CANCEL');?>"/></td>
			</tr>
		</tfoot> 
		<tbody>
			<?php if($user_info['role_id'] > 0): ?><input type="hidden" name="to_role_id" value="<?php echo ($user_info["role_id"]); ?>" id="to_role_id"/>
			<?php else: ?>
				<tr>
					<td class="tdleft" width="15%">*<?php echo L('SELECT_THE_RECIPIENT');?></td>
					<td>
						<ul class="nav nav-tabs">
							<?php if(is_array($departments_list)): $k = 0; $__LIST__ = $departments_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li <?php if($vo['department_id'] == session('department_id')): ?>class="active"<?php endif; ?>>
									<a class="ta" rel="<?php echo ($k); ?>" data-toggle="tab"><?php echo ($vo['name']); ?></a>
								</li><?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
						<?php if(is_array($departments_list)): $k = 0; $__LIST__ = $departments_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="tab-pane <?php if($vo['department_id'] != session('department_id')): ?>hide<?php endif; ?>" id="ta<?php echo ($k); ?>">
								<input type="checkbox" class="check_all" rel="<?php echo ($k); ?>"/><?php echo L('SELECT_ALL');?><br/>
								<?php if(is_array($vo['user'])): $i = 0; $__LIST__ = $vo['user'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$temp): $mod = ($i % 2 );++$i;?><span style="width:33%; float:left;"><input class="muti_role_id<?php echo ($k); ?>" type="checkbox" name="to_role_id[]" value="<?php echo ($temp["role_id"]); ?>"/><?php echo ($temp['user_name']); ?>【<?php echo ($temp["role_name"]); ?>】&nbsp; </span><?php endforeach; endif; else: echo "" ;endif; ?>
							</div><?php endforeach; endif; else: echo "" ;endif; ?>
					</td>
				</tr><?php endif; ?>
			
			<tr>
				<td class="tdleft"><?php echo L('CONTENTS');?></td>
				<td>
					<textarea rows="5" class="span5" name="content"></textarea>
				</td>
			</tr>
		</tbody>
	</table>
	</form>
<div id="dialog-to-role-list" class="hide" title="<?php echo L('SELECT_THE_RECIPIENT');?>">loading...</div>
<script type="text/javascript">
$("#dialog-to-role-list").dialog({
	autoOpen: false,
	modal: true,
	width: 600,
	maxHeight: 400,
	buttons: { 
		"Ok": function () {
			var item = $('input:radio[name="owner"]:checked').val();
			var name = $('input:radio[name="owner"]:checked').parent().next().html();
			$('#to_role_name').val(name);
			$('#to_role_id').val(item);
			$(this).dialog("close"); 
		},
		"Cancel": function () {
			$(this).dialog("close");
		}
	},
	position: ["center", 100]
});
	
$(function(){
	$('#to_role_name').click(
		function(){
			$('#dialog-to-role-list').dialog('open');
			$('#dialog-to-role-list').load("<?php echo U('user/listDialog','by=all');?>");
		}
	);
	$('.ta').click(
		function(){
			var num = $(this).attr('rel');
			var list = new Array();
			<?php if(is_array($departments_list)): $k = 0; $__LIST__ = $departments_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?>list.push(<?php echo ($k); ?>);<?php endforeach; endif; else: echo "" ;endif; ?>
			for (var i=0;i<list.length;i++){
				if(num == list[i]){
					$('#ta'+(i+1)).show();
				}else{
					$('#ta'+(i+1)).hide();
				}
			}
		}
	);
	$('.check_all').click(
		function(){
			var k = $(this).attr('rel');
			$("input[class='muti_role_id"+k+"']").prop('checked', $(this).prop("checked"));
		}
	);
});
</script>