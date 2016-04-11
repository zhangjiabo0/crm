<?php if (!defined('THINK_PATH')) exit();?><form id="role_add" class="form-horizontal" action="<?php echo U('user/role_add');?>" method="Post">
	<div class="control-group">
		<label class="control-label" for="name"><?php echo L('POSITION_NAME');?></label>  
		<div class="controls">
			<input type="text" class="text-input large-input" name="name"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="name"><?php echo L('WHICH_DEPARTMENT_BELONGS_TO');?></label>  
		<div class="controls">
			<select name="department_id">
				<?php if(is_array($departmentList)): $i = 0; $__LIST__ = $departmentList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$temp): $mod = ($i % 2 );++$i;?><option value="<?php echo ($temp["department_id"]); ?>"><?php echo ($temp["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="name"><?php echo L('SUPERIORS_POSITION');?></label>  
		<div class="controls">
			<select name="parent_id">
				<?php if(is_array($positionList)): $i = 0; $__LIST__ = $positionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$temp): $mod = ($i % 2 );++$i;?><option value="<?php echo ($temp["position_id"]); ?>"><?php echo ($temp["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="name"><?php echo L('POSITION_DESCRIPTION');?></label>  
		<div class="controls">
			<textarea name="description"></textarea>
		</div>
	</div>
</form>