<?php if (!defined('THINK_PATH')) exit();?><form class="form-horizontal" action="<?php echo U('business/advance');?>" method="post">	
	<table class="table">
		<input type='hidden' name='business_id' value="<?php echo ($business_id); ?>"/>
		<tr>
			<th colspan="2"><?php echo L('ADVANCE_INFORMATION');?></th>
		</tr>
		<tr>
			<td width="20%" class="tdleft"><?php echo L('MOVE_ON_TO_PHASE');?></td>
			<td>
				<select name="status_id">
					<?php if(is_array($statusList)): $i = 0; $__LIST__ = $statusList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["status_id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tdleft"><?php echo L('THE_NEXT_TIME_CONTACT');?></td>
			<td><input placeholder="<?php echo L('TIME');?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'})" class="span2"  type="text" id="nextstep_time" name="nextstep_time"/> &nbsp; <input type="text" id="nextstep" name="nextstep" placeholder="<?php echo L('CONTENT');?>"/></td>
		</tr>
		<tr>
			<td class="tdleft"><?php echo L('STAGE_OF_DESCRIPTION');?></td>
			<td><textarea rows="6" class="span4" name="description"></textarea></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input class="btn btn-primary" type="submit" name="submit" value="<?php echo L('BOOST');?>"/> &nbsp; <input class="btn" onclick="javascript:$('#dialog-advance').dialog('close');" type="button" value="<?php echo L('CANCEL');?>"/></td>
		</tr>
	</table>
</form>