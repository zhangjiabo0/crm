<?php if (!defined('THINK_PATH')) exit();?><form action="<?php echo U(product/excelImport);?>" method="post" enctype="multipart/form-data">
	<table class="table table-hover">
		<tr>
			<td class="tdleft" width="20%"><?php echo L('FILE_SPECIFICATION');?></td> 
			<td><?php echo L('IMPORT_EXCEL_FILE_PAY_ATTENTION_TO_THE_CHOICE_OF_THE_DATA_CONTENT');?>
			<p><?php echo L('ALLOW_TYPE_XLS_NO_MORE_THAN_20MB_FILE_TOTAL_SIZE');?></p></td>
		</tr>
		<tr>
			<td class="tdleft" width="20%"><?php echo L('ERROR_HANDLING');?></td> 
			<td>
				<input id="ownership" type="radio" checked="checked" value="0" name="error_handing"><?php echo L('TERMINATION');?>
				<input id="ownership1" type="radio" value="1" name="error_handing"><?php echo L('SKIP');?>
			</td>
		</tr>
		<tr>
			<td class="tdleft"><?php echo L('SELECT_IMPORT_FILE');?></td>
			<td><p id="attachment1"><input type="file" name="excel"/></p><p style="color:red"><?php echo L('IMPORT_FILE_PLEASE_BE_SURE_TO_USE_PROPRIETARY_DATA_WHEN_THE_IMPORT_TEMPLATE');?>&nbsp;<a href="__PUBLIC__/excelmodel/crm_product_model.xls"><?php echo L('NOKIA_MONITOR_TEST');?></a></p></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input class="btn btn-primary" type="submit" name="submit" value="<?php echo L('TO_LEAD');?>"/> &nbsp; <input class="btn" onclick="javascript:$('#dialog-import').dialog('close');" type="button" value="<?php echo L('CANCEL');?>"/></td>
		</tr>
	</table>
</form>