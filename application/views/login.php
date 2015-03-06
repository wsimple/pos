<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<base href="<?=base_url()?>" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" rev="stylesheet" href="css/login.css" />
	<title>Point Of Sale <?=$this->lang->line('login_login')?></title>
	<script src="js/jquery.switch.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/jquery-1.10.2.min.js" type="text/javascript" charset="UTF-8"></script>
	<script>window.$$=window.jQueryNew=jQuerySwitch('jQueryNew',jQuery);</script>
	<!-- jquery new -->
	<script src="js/jquery.local.js" type="text/javascript" charset="UTF-8"></script>
	<!-- end jquery new -->
	<script src="js/jquery-1.2.6.min.js" type="text/javascript" charset="UTF-8"></script>
	<script>window.jQueryOld=jQuerySwitch('jQueryOld',jQuery);</script>
	<!-- jquery old -->
	<script src="http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js"></script>
</head>
<body>

<?php
	$dbs = $this->Location->get_select_option_list(false, true);
	$dbs['default'] = 'Principal';
?>

<div>
	<img src="images/<?=file_exists('images/'.$this->Appconfig->get('logo'))?$this->Appconfig->get('logo'):'logo.png'?>" border="0" />
</div>

<div class="box-login">
	<?=form_open('login',array('id'=>'form_login'))?>
	<div  class="box-title clearfix">
		<?=(trim(validation_errors())!='')?validation_errors():$this->lang->line('login_welcome_message')?>
	</div>

	<div class="clearfix">
		<table class="box-table" border="0" cellpadding="0" cellspacing="0" width="340px">
			<tr>
				<td colspan="2" class="box-bkg-label-location"><?=form_label($this->lang->line('login_location_label'),'locationbd')?></td>
			</tr>
			<tr>
				<td colspan="2" class="box-bkg-drop-location">
					<?=form_dropdown('locationbd',$dbs,$this->input->post('locationbd'))?>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="1"></td>
			</tr>
			<tr>
				<td class="icon-user"></td>
				<td class="bkg-input-login"><?=form_input(array(
					'name'=>'username',
					'size'=>'20',
					'value'=>$fastUser,
					'placeholder'=>$this->lang->line('login_username'),
				))?></td>
			</tr>
			<tr>
				<td colspan="2" height="1"></td>
			</tr>
			<tr>
				<td class="icon-lock"></td>
				<td class="bkg-input-login"><?=form_password(array(
					'name'=>'password',
					'size'=>'20',
					'placeholder'=>$this->lang->line('login_password'),
				))?>
			</td>
			</tr>
			<tr>
				<td colspan="2" height="1"></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><?=form_submit('loginButton',$this->lang->line('login_submit_labelb'),'class="form-button"')?></td>
			</tr>
		</table>
	</div>
</div>
<?=form_close()?>
<script type="text/javascript">
(function($){
	var select=$('select[name="locationbd"]')[0];
	select.value=$.local('last_db');
	if(select.value=='') select.selectedIndex=0;
	$('#form_login').submit(function(){
		$.local('last_db',select.value);
	}).find("input:first").focus();
})(jQueryNew);
</script>
</body>
</html>
