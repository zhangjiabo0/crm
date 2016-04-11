<?php if (!defined('THINK_PATH')) exit();?><form action="<?php echo U('file/add');?>" enctype="multipart/form-data" method="post">	
	<input type="hidden" name="module" value="<?php echo ($module); ?>"/>
	<input type="hidden" name="r" value="<?php echo ($r); ?>"/>
	<input type="hidden" name="id" value="<?php echo ($id); ?>"/>
	<input type="hidden" name="role_id" value="<?php echo (session('role_id')); ?>">
	<table class="table table-hover">
		<tr>
			<td class="tdleft"><?php echo L('SELECT_FILES');?></td> 
			<td>(<?php echo L('FILE_NO_MORE_THAN_20MB');?>)</td>
		</tr>
		<tr>
			<td class="tdleft"><?php echo L('Attachment');?></td>
			<td><p><?php echo L('ALLOWED_FILE_TYPES'); echo ($allowExts); ?></p>
			<p id="attachment1"><input type="file" name="file[]"/></p> <a id="add_more" href="javascript:add_file(2);"><?php echo L('ADD_AN_ATTACHMENT');?></a></td>
		</tr>		
		<tr> 
			<td>&nbsp;</td>
			<td><input class="btn btn-primary" type="submit" name="submit" value="<?php echo L('ADD');?>"/> &nbsp; <input class="btn" onclick="javascript:$('#dialog-file').dialog('close');" type="button" value="<?php echo L('CANCEL');?>"/></td>
		</tr>
	</table>
</form>			
<script type="text/javascript">
	function add_file(id) {
		$("#add_more").before('<p id="attachment' + id + '"><input type="file" name="file[]"/> <a href=\'javascript:void(0)\'  onclick=\'javascript:$(\"#attachment' + id + '\").remove();\'><?php echo L('DELETE');?></a></p>');
		$("#add_more").attr('href', 'javascript:add_file(' + (id+1) + ');');
	}
	$('input[type="submit"]').click(function(){
		var files = $('input[type="file"]').val();
		if(files.length < 1){
			alert('请选择你要上传的文件！');
			return false;
		}
	});
</script>