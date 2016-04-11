<?php if (!defined('THINK_PATH')) exit();?><form id="roleedit" class="form-horizontal" action="<?php echo U('user/roleedit');?>" method="Post">
	<input type="hidden" name="position_id"  id="position_id"  name="position_id" value="<?php echo ($position["position_id"]); ?>"/>
	<div class="control-group">
		<label class="control-label" for="name"><?php echo L('POSITION_NAME');?></label>  
		<div class="controls">
			<input type="text" class="text-input large-input" name="name" id="name" value="<?php echo ($position["name"]); ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="name"><?php echo L('WHICH_DEPARTMENT_BELONGS_TO');?></label>  
		<div class="controls">
			<select name="department_id" id="department_id">
				<?php if(is_array($departmentList)): $i = 0; $__LIST__ = $departmentList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$temp): $mod = ($i % 2 );++$i;?><option <?php if($position['department_id'] == $temp['department_id']): ?>selected<?php endif; ?> value="<?php echo ($temp["department_id"]); ?>"><?php echo ($temp["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="name"><?php echo L('SUPERIORS_POSITION');?></label>  
		<div class="controls">
			<select name="parent_id" id="parent_id">
				<option></option>
				<?php if(is_array($positionList)): $i = 0; $__LIST__ = $positionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$temp): $mod = ($i % 2 );++$i;?><option <?php if($position['parent_id'] == $temp['position_id']): ?>selected<?php endif; ?> value="<?php echo ($temp["position_id"]); ?>"><?php echo ($temp["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="name"><?php echo L('POSITION_DESCRIPTION');?></label>  
		<div class="controls">
			<textarea name="description" id="description"><?php echo ($position["description"]); ?></textarea>
		</div>
	</div>
</form>