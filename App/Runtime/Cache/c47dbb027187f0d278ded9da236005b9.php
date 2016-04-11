<?php if (!defined('THINK_PATH')) exit();?>
<form class="form-horizontal" action="<?php echo U('email/smtpadd');?>" method="post">
	<table class="span5 table">
		<tbody>
			<tr>
				<th colspan="2"><?php echo L('SMTP_BASIC_SETTING');?></th>
			</tr>
			<tr>
				<td class="tdleft"><?php echo L('EMAIL_ADDRESS_NAME');?></td>  
				<td>
					<input name="name" id="name" type="text" value="<?php echo ($smtp['name']); ?>"/> <span style="color:red;"><?php echo L('MUST_FILL_IN');?></span>
				</td>
			</tr>
			<tr>
				<td class="tdleft"><?php echo L('EMAIL_ADDRESS');?></td>  
				<td>
					<input name="address" id="address" type="text" value="<?php echo ($smtp['setting']['MAIL_ADDRESS']); ?>"/> <span style="color:red;"><?php echo L('MUST_FILL_IN');?></span>
				</td>
			</tr>
			<tr>
				<td class="tdleft"><?php echo L('SMTP_SERVER_ADDRESS');?></td>  
				<td>
					<input value="<?php echo ($smtp['setting']['MAIL_SMTP']); ?>" id="smtp" name="smtp" type="text"> <span style="color:red;"><?php echo L('MUST_FILL_IN');?></span>&nbsp;&nbsp;&nbsp;&nbsp;<input value="ssl" id="secure" name="secure" type="checkbox" <?php if($smtp['setting']['MAIL_SECURE'] == 'ssl'): ?>checked="checked"<?php endif; ?>> SSL
				</td>
			</tr>
			<tr>
				<td class="tdleft"><?php echo L('SMTP_SERVER_PORT');?></td>  
				<td>
					<input value="<?php echo (($smtp['setting']['MAIL_PORT'])?($smtp['setting']['MAIL_PORT']):25); ?>" id="port" name="port" type="text"> <span style="color:red;"><?php echo L('MUST_FILL_IN');?></span>
				</td>
			</tr>
			<tr>
				<td class="tdleft"><?php echo L('LOGIN_NAME');?></td>  
				<td>
					<input value="<?php echo ($smtp['setting']['MAIL_LOGINNAME']); ?>" id="loginName" name="loginName" type="text"/> <span style="color:red;"><?php echo L('MUST_FILL_IN');?></span>
				</td>
			</tr>
			<tr>
				<td class="tdleft"><?php echo L('PASSWORD');?></td>  
				<td>
					<input value="<?php echo ($smtp['setting']['MAIL_PASSWORD']); ?>" id="password" name="password" type="password"> <span style="color:red;"><?php echo L('MUST_FILL_IN');?></span>
				</td>
			</tr>
			<tr>
				<td class="tdleft"><?php echo L('TEST_EMAIL');?>:</td>  
				<td>
					<input name="test_email" id="test_email" type="text"/> &nbsp; <input class="btn btn-mini" id="test" name="submit" type="button" value="<?php echo L('TEST');?>">
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input name="submit" class="btn btn-primary" type="submit" value="<?php echo L('SAVE');?>"/>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<script type="text/javascript">	
	$('#test').click(
		function(){
			address = $('#address').val();
			smtp = $('#smtp').val();
			port = $('#port').val();
			secure = $('#secure:checked').val();
			name = $('#loginName').val();
			pw = $('#password').val();
			email = $('#test_email').val();
			if(address !='' && smtp !='' && port !='' && name!='' && pw!='' && email!=''){
				$.post('<?php echo U("setting/smtp");?>',
				{   address:address,
					smtp:smtp,
					port:port,
					secure:secure,
					loginName:name,
					password:pw,
					test_email:email},
				function(data){
					alert(data.info);
				},
				'json');
			} else {
				alert('<?php echo L("PLEASE_FILL_IN_COMPLETE_INFORMATION");?>');
			}
		}
	);
</script>