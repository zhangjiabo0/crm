<?php if (!defined('THINK_PATH')) exit();?><form id="department_edit" class="form-horizontal" action="<?php echo U('User/department_edit');?>" method="Post">
	<input type="hidden" name="department_id" value="<?php echo ($vo["department_id"]); ?>"/>
	<div class="control-group">
		<label class="control-label" for="name"><?php echo L('DEPARTMENT_NAME');?></label>  
		<div class="controls">
			<input class="text-input large-input" type="text" name="name" value="<?php echo ($vo["name"]); ?>"/>
		</div>
	</div>	
	<div class="control-group">
		<label class="control-label" for="name"><?php echo L('SUPERIORS_DEPARTMENT');?></label>  
		<div class="controls">
			<select name="parent_id">
				<option value="0"><?php echo L('THE_TOP_DEPARTMENT');?></option>
				<?php if(is_array($departmentList)): $i = 0; $__LIST__ = $departmentList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$temp): $mod = ($i % 2 );++$i;?><option <?php if($vo['parent_id'] == $temp['department_id']): ?>selected<?php endif; ?> value="<?php echo ($temp["department_id"]); ?>"><?php echo ($temp["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
	</div>	
	<div class="control-group">
		<label class="control-label" for=""><?php echo L('DEPARTMENT_DESCRIPTION');?></label>  
		<div class="controls">
			<textarea name="description"><?php echo ($vo["description"]); ?></textarea>
		</div>
	</div>
</form>