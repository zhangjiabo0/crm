<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
<?php if(C('ismobile') == 1): ?>width=$('.container').width() * 0.9;<?php else: ?>width=800;<?php endif; ?>
$("#dialog-select").dialog({
	autoOpen: false,
	modal: true,
	width: width,
	maxHeight:400,
	position: ["center",100],
	buttons: { 
        "Ok": function () {
			var item = $('input:radio[name="product_id"]:checked').val();
			var name = $('input:radio[name="product_id"]:checked').parent().next().html();
			var suggested_price = $('input:radio[name="product_id"]:checked').next().next().val();
			$('#product_id').val(item);
			$('#product_name').val(name);
			$('#estimate_price').val(suggested_price);
			$('#sales_price').val(suggested_price);
			$('#amount').val(1);
            $(this).dialog("close");  
        },
		"Cancel": function () {
            $(this).dialog("close");
        }
    }
});
$("#product_name").click(function(){
	$('#dialog-select').dialog('open');
	$('#dialog-select').load('<?php echo U("product/allproductdialog");?>');
});
</script>
<form action="<?php echo U('product/addDialog');?>" method="post">
	<input type="hidden" name="creator_role_id" value="<?php echo (session('role_id')); ?>"/>
	<input type="hidden" name="r" value="<?php echo ($r); ?>"/>
	<input type="hidden" name="module" value="<?php echo ($module); ?>"/>
	<input type="hidden" name="model_id" value="<?php echo ($model_id); ?>"/>
	<input type="hidden" name="dialog_add" value="dialog_add"/>
	<table class="table table-hover">
		<tfoot>
			<tr>
				<th>&nbsp;</th>
				<th colspan="3"><input class="btn btn-primary" type="submit" value="<?php echo L('SAVE');?>"/> &nbsp;<input class="btn" onclick="javascript:$('#dialog-product').dialog('close');" type="button" value="<?php echo L('CANCEL');?>"/></th>
			</tr>
		</tfoot>
		<tbody>
			<tr><th <?php if(C('ismobile') != 1): ?>colspan="4"<?php else: ?>colspan="2"<?php endif; ?>><?php echo L('BASIC_INFORMATION');?></th></tr>
			<tr>
				<td class="tdleft" valign="middle"><?php echo L('PRODUCT');?></td>
				<td valign="middle"><input type="hidden" name="product_id" id="product_id"/><input name="product_name" id="product_name" type="text" /></td>
				<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
				<td class="tdleft" valign="middle"><?php echo L('OFFER');?></td>
				<td valign="middle">
					<input name="estimate_price" id="estimate_price" type="text" />
				</td>
			</tr>
			<tr>
				<td class="tdleft" valign="middle"><?php echo L('SELLING_PRICE');?></td>
				<td valign="middle"><input name="sales_price" id="sales_price" type="text" /></td>
				<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
				<td class="tdleft" valign="middle"><?php echo L('QUANTITY');?></td>
				<td valign="middle"><input name="amount" id="amount" type="text" /></td>
			</tr>
			<tr>
				<td class="tdleft" valign="middle"><?php echo L('REMARK');?></td>
				<td <?php if(C('ismobile') != 1): ?>colspan="3"<?php endif; ?> valign="middle"><textarea rows="6" class="span5" name="description"></textarea></td>
			</tr>
		</tbody>
	</table>
</form>
<div class="hide" id="dialog-select" title="<?php echo L('ADD_PRODUCT');?>">loading...</div>