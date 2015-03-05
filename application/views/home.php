<?php $this->load->view("partial/header"); ?>
<br />
<h3><?php //echo $this->lang->line('common_welcome_message'); ?></h3>
<div id="home_module_list" class="clearfix">
	<?php
	foreach($allowed_modules->result() as $module){
	switch ($module->module_id) {
		case 'sales': $text='/index/sales'; break;
		case 'notification_alert': $band=true; $text=''; break;
		default: $text=''; break;
	}
	if (isset($band) && $band=true){ unset($band); continue; }
	?>
	<a href="<?php echo site_url("$module->module_id").$text;?>">
		<div class="module_item">	
			<img src="<?php echo base_url().'images/menubar/'.$module->module_id.'.png';?>" border="0" alt="Menubar Image" />
			<br/>
			<?php echo $this->lang->line("module_".$module->module_id) ?>
			<br/>
			<?php echo $this->lang->line('module_'.$module->module_id.'_desc');?>
		</div>
	</a>
	<?php
	}
	?>
</div>
<?php $this->load->view("partial/footer"); ?>
