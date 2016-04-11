<?php if (!defined('THINK_PATH')) exit();?><form action="<?php echo U('finance/add','t=receivables');?>" method="post">
<input name="customer_id" id="customer_id" type="hidden" value="<?php echo ($customer_id); ?>" />
<input name="contract_id" id="contract_id" type="hidden" value="<?php echo ($contract_id); ?>"/>
<input name="refer_url" type="hidden" value="<?php echo ($refer_url); ?>"/>
	<table class="table table-hover">
		<tfoot>
			<tr>
				<td>&nbsp;</td>
				<td><input name="submit" class="btn btn-primary" type="submit" value="<?php echo L('SAVE');?>"/> &nbsp; <input class="btn" type="button" value="<?php echo L('CANCEL');?>" onclick="javascript:$('#dialog-receivables').dialog('close');"></td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<th colspan="2"><?php echo L('BASIC_INFO');?></th>
			</tr>
			<tr>
				<td class="tdleft" width="20%" valign="middle"><?php echo L('RECEIVABLES NAME');?></td>
				<td valign="middle"><input name="name" id="name" class="text-input large-input" type="text" /></td>
			</tr>
			<tr>
				<td class="tdleft" width="20%" valign="middle"><?php echo L('OWNER_ROLE');?></td>
				<td valign="middle"><input name="owner_role_id" id="owner_role_id" type="hidden" /><input name="owner_name" id="owner_name" class="text-input large-input" type="text" /></td>
			</tr>
			<tr>
				<td class="tdleft" valign="middle"><?php echo L('AMOUNT OF RECEIVING');?></td>
				<td valign="middle"><input class="text-input large-input" id="price" name="price" type="text" /></td>
			</tr>
			<tr>
				<td class="tdleft" valign="middle"><?php echo L('RECEIVING TIME');?></td>
				<td valign="middle"><input onclick="WdatePicker()"  type="text" id="pay_time" name="pay_time"/></td>
			</tr>
			<tr>
				<td class="tdleft" valign="middle"><?php echo L('DESCRIPTION');?></td>
				<td valign="middle"><textarea cols="30" rows="3" name="description"></textarea></td>
			</tr>
		</tbody>
	</table>
</form>
<div id="dialog-message3" title="<?php echo L('SELECT THE LEADER');?>">loading...</div>
<script type="text/javascript">
	<?php if(C('ismobile') == 1): ?>width=$('.container').width() * 0.9;<?php else: ?>width=800;<?php endif; ?>
$("#dialog-message3").dialog({
	autoOpen: false,
	modal: true,
	width: width,
	maxHeight: 400,
	buttons: {
		"Ok": function () {
			var item = $('input:radio[name="owner"]:checked').val();
			var name = $('input:radio[name="owner"]:checked').parent().next().html();
			if(item){
				$('#owner_name').val(name);
				$('#owner_role_id').val(item);
			}
			$(this).dialog("close");
		},
		"Cancel": function () {
			$(this).dialog("close");
		}
	},
	position: ["center", 100]
});
$(function(){
	$("#owner_name").click(
		function(){
			$('#dialog-message3').dialog('open');
			$('#dialog-message3').load('<?php echo U("user/listDialog","by=all");?>');
		}
	);
});
</script>