<?php if (!defined('THINK_PATH')) exit();?><form id="department_add" class="form-horizontal" action="<?php echo U('User/department_add');?>" method="Post">
	<div class="control-group">
		<label class="control-label" for="name"><?php echo L('DEPARTMENT_NAME');?></label>  
		<div class="controls">
			<input type="text" id="name" name="name"/>
		</div>
	</div>	
	<div class="control-group">
		<label class="control-label" for="name"><?php echo L('SUPERIORS_DEPARTMENT');?></label>  
		<div class="controls">
			<select name="parent_id">
				<option value="0"><?php echo L('THE_TOP_DEPARTMENT');?></option>
				<?php if(is_array($departmentList)): $i = 0; $__LIST__ = $departmentList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$temp): $mod = ($i % 2 );++$i;?><option value="<?php echo ($temp["department_id"]); ?>"><?php echo ($temp["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
	</div>	
	<div class="control-group">
		<label class="control-label" for=""><?php echo L('DEPARTMENT_DESCRIPTION');?></label>  
		<div class="controls">
			<textarea name="description"></textarea>
		</div>
	</div>
</form>