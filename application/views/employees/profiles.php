<?php $this->load->view("partial/header"); ?>
<div id="title_bar">
	<div id="title" class="float_left"><?php echo $this->lang->line('common_list_of').' '.$this->lang->line('employees_profile_per'); ?></div>
	<div id="new_button">
		<div id="to-back" class="big_button" style="margin: 0px 5px;float:left;">
			<span><a href="javascript:void();" style="text-decoration: none;color: hsl(208, 45%, 41%);" >
			Back</a></span>
		</div>
		<?php
			echo anchor("$controller_name/form_profile_employee/-1/width:680",
			"<div class='big_button' style='float: left;'><span>".$this->lang->line('employees_profile_per_new')."</span></div>",
			array('class'=>'thickbox none','title'=>$this->lang->line('employees_profile_per_new')));
		?>
	</div>
</div>
<div id="table_holder" class="box-form-view">
	<table class="tablesorter" id="sortable_table">
		<thead>	<tr>
			<th style="padding: 5px;"><?php echo $this->lang->line('employees_profile_per_name'); ?></th><th style="padding: 5px;"><?php echo $this->lang->line('employees_profile_m'); ?></th><th style="padding: 5px;"></th>
		</tr></thead>
		<tbody>
		<?php 	
		if (is_array($profiles)){
			foreach ($profiles as $row) { ?>
			<tr>
				<td width="30%" ><?=$row['name']?></td>
				<td width="65%" ><?php echo $row['module']; ?></td>
				<td width="5%"><?=anchor($controller_name."/form_profile_employee/".$row['name']."/width:680", $this->lang->line('common_edit'),array('class'=>'thickbox small_button','title'=>'Edit Profile'))?></td>
			</tr>
		<?php } }else{ echo '<tr><td colspan="3">'.$profiles.'</td></tr>'; } ?>
		</tbody>
	</table>
</div>
<div id="feedback_bar"></div>
<script type="text/javascript">
$(document).ready(function() { $('#to-back').click(function() { history.back(1); }); }); 
</script>
<?php $this->load->view("partial/footer"); ?>
