<?php if (!defined('THINK_PATH')) exit();?><form action="<?php echo U('Message/send');?>" method="post">

	<ul class="nav nav-tabs">
		<?php if(is_array($departments_list)): $k = 0; $__LIST__ = $departments_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li <?php if($vo['department_id'] == session('department_id')): ?>class="active"<?php endif; ?>>
				<a class="ta" rel="<?php echo ($k); ?>" data-toggle="tab"><?php echo ($vo['name']); ?></a>
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
	<?php if(is_array($departments_list)): $k = 0; $__LIST__ = $departments_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="tab-pane <?php if($vo['department_id'] != session('department_id')): ?>hide<?php endif; ?>" id="ta<?php echo ($k); ?>">
			<input type="checkbox" class="check_all" rel="<?php echo ($k); ?>"/><?php echo L('SELECT_ALL');?><br/>
			<?php if(is_array($vo['user'])): $i = 0; $__LIST__ = $vo['user'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$temp): $mod = ($i % 2 );++$i;?><span style="width:25%; float:left;"><input type="checkbox" class="muti_role_id muti_role_id<?php echo ($k); ?>" name="to_role_id[]" rel="<?php echo ($temp['user_name']); ?>" value="<?php echo ($temp["role_id"]); ?>" <?php if(strpos($_GET['check_ids'],','.$temp['role_id'].',') !== false): ?>checked<?php elseif(strpos($_GET['check_roleids'],','.$temp['role_id'].',') !== false): ?>checked<?php endif; ?> /><?php echo ($temp['user_name']); ?>【<?php echo ($temp["role_name"]); ?>】&nbsp; </span><?php endforeach; endif; else: echo "" ;endif; ?>
		</div><?php endforeach; endif; else: echo "" ;endif; ?>

</form>
<script type="text/javascript">
	
$(function(){
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
			$("input.muti_role_id"+k).prop('checked', $(this).prop("checked"));
		}
	);
});
</script>