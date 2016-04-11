<?php if (!defined('THINK_PATH')) exit();?><table class="table">
	<thead>
		<tr>
			<th colspan="4"><?php echo L('BASIC_INFO');?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="15%" class="tdleft"><?php echo L('USERNAME');?></td>
			<td width="35%">
				<?php echo ($user["name"]); if(is_array($categoryList)): $i = 0; $__LIST__ = $categoryList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$temp): $mod = ($i % 2 );++$i; if($temp["category_id"] == $user['category_id']): ?>（<?php echo ($temp["name"]); ?>）<?php endif; endforeach; endif; else: echo "" ;endif; ?>
			</td>
			<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
			<td width="15%" class="tdleft"><?php echo L('SEX');?></td>
			<td width="35%"><?php if($user['sex'] == 1): echo L('MALE'); elseif($user['sex'] == 2): echo L('FEMALE'); else: echo L('UNKNOW'); endif; ?></td>
		</tr>
		<tr>
			<td class="tdleft"><?php echo L('DEPARTMENT');?></td>
			<td><?php echo ($user["role"]["department_name"]); ?></td>
			<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
			<td class="tdleft"><?php echo L('POSITION');?></td>
			<td><?php echo ($user["role"]["role_name"]); ?></td>
		</tr>
		<tr>
			<td class="tdleft"><?php echo L('TELPHONE');?></td>
			<td><?php if(C('ismobile') == 1): ?><a href="tel:<?php echo ($user["telephone"]); ?>"><?php echo ($user["telephone"]); ?></a><?php else: echo ($user["telephone"]); endif; ?></td>
			<?php if(C('ismobile') == 1): ?></tr><tr><?php endif; ?>
			<td class="tdleft"><?php echo L('EMAIL');?></td>
			<td><?php echo ($user["email"]); ?></td>
		</tr>
		<tr>
			<th colspan="4"><?php echo L('MESSAGE');?></th>
		</tr>
		<tr>
			<td class="tdleft"><?php echo L('CONTENT');?></td>
			<td colspan="3">
				<textarea rows="5" class="span4" id="content" name="content"></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="3">
				<input class="btn btn-primary" id="send" name="send" type="button" value="<?php echo L('SEND');?>"/>&nbsp; &nbsp;<input class="btn btn-primary" id="close" name="close" type="button" value="<?php echo L('CANCEL');?>"/>&nbsp; &nbsp;<span id="result"></span>
			</td>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
	$('#close').click(
		function(){
			$('#dialog-role-info').dialog('close');
		}
	);
	$('#send').click(
		function(){			
			to_role_id = <?php echo ($user["role"]["role_id"]); ?>;
			content = $('#content').val();
			if(content!=''){
				$("#result").html('<span style="color:red"><?php echo L("SENDING_MESSAGE");?></span>');
				$("#send").attr('disabled', "disabled");
				$.post('<?php echo U("message/ajaxsend");?>',
					{
						to_role_id:to_role_id,
						content:content
					},
					function(data){
						if(data.status == 1){
							$("#result").html('<span style="color:green"><?php echo L("SEND_SUCCESS");?></span>');
							$("#send").attr('disabled', false);
							$("#content").val("");
						} else if(data.status == 0) {
							$("#result").html('<span style="color:red"><?php echo L("SEND_FAILED");?></span>');
						}
					},
				'json');
			} else {
				$("#result").html('<span style="color:red"><?php echo L("NEED_CONTENT");?></span>');
			}
		}
	);
</script>