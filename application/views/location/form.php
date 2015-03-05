<div class="loader">
	<img src="images/loading.gif" width="230" height="230">
	<br>
	<h3><?php echo $this->lang->line('location_loading'); ?></h3>
</div>
<?php echo form_open('locations/save/',array('id'=>'location_form')); ?>
<div>
	<div>
		<h3><?php echo $this->lang->line('location_general_info') ?></h3><hr>
		<?php echo form_hidden('id', $data['id']); ?>
		<ul>
			<li><?php echo form_label($this->lang->line('location_location_name').':', 'Location', array('class'=>'lable-form-required')).'<br>'.form_input('location', $data['name'], 'class="text_box"'); ?></li>
			<li><?php echo form_label($this->lang->line('location_host').':', 'hostname', array('class'=>'lable-form-required')).'<br>'.form_input('hostname', $data['hostname'], 'class="text_box"'); ?></li>
		</ul>
		<ul>
			<li><?php echo form_label($this->lang->line('location_user').':', 'username', array('class'=>'lable-form-required')).'<br>'.form_input('username', $data['username'], 'class="text_box"'); ?></li>
			<li><?php echo form_label($this->lang->line('location_password').':', 'password').'<br>'.form_password('password','', 'class="text_box"'); ?></li>
		</ul>
		<div>
		<?php
			if ($data['id'] <= 0) {
			 	echo form_label($this->lang->line('location_database').':', 'database', array('class'=>'lable-form-required')).'<br>'.form_input('database', $data['database'],'class="text_box"').'<br>';
			}else{
				echo $this->lang->line('location_database').':'.'<br>'.ucwords($data['database']);
			}
		?>
		</div>
	</div>
	<div>
		<h3><?php echo $this->lang->line('location_advanced_information') ?></h3><hr>
		<ul>
			<li><?php echo form_label($this->lang->line('location_driver').':', 'dbdriver').'<br>'.form_dropdown('dbdriver', $dbdrivers, $data['dbdriver']); ?></li>
			<li><?php echo form_label($this->lang->line('location_active').':', 'active').'<br>'.form_checkbox('active', 1, $data['active']); ?></li>
		</ul>
	</div>
	<div>
		<ul id="error_message_box"></ul>
	</div>
	<?php echo form_submit(array(
	'name'=>'save',
	'id'=>'save',
	'value'=>$this->lang->line('location_save'),
	'class'=>'small_button float_right'
	)); ?>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
$(document).ready(function() {
	$('div.loader').hide();

	$('#location_form').validate({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
				beforeSubmit:function(){
					//pone el loader
					$('#location_form > div').hide('slow');
					$('div.loader').show('fast');
				},
				success: function(data){
					// console.log(data);
					$('div.loader').remove();
					$('#location_form > div').show('fast');
					if (data.success) {
						post_item_form_submit(data);
					}else{
						notif({
							type: 'error',
							msg: data.message,
							width: 400,
							height: 100,
							position: "right"
						});
					}
				},dataType:'json'
			});
		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules: 
		{
			location: {
				required: true,
				// regex:/^\w[a-zA-Z]+$/,
				minlength: 4,
				maxlength: 50
		    },
		    hostname: {
			    required: true,
			    regex:/^[a-zA-Z1-9\.]+$/,
			    minlength: 5,
			    maxlength: 15
		    },
			username: {
			    required: true,
			    minlength: 3,
			    maxlength: 15
		    },
    		database:
			{
				required:true,
			    regex:/^[a-zA-Z_]+[0-9]*$/,
				minlength: 5,
			    maxlength: 20
			}
   		},
		messages: 
		{
			location: {
			      required: "<?php echo $this->lang->line('location_location_required'); ?>",
			      // regex:"<?php echo $this->lang->line('location_location_name').' '.$this->lang->line('location_location_name').' '.$this->lang->line('location_only_letters'); ?>",
			      minlength: jQuery.format("<?php echo $this->lang->line('common_at_least'); ?> {0} <?php echo $this->lang->line('common_at_characters'); ?>!")
    		},
    		hostname: {
			      required: "<?php echo $this->lang->line('location_host_required'); ?>",
			      regex:"<?php echo $this->lang->line('location_only_letters_numbers'); ?>",
			      minlength: jQuery.format("<?php echo $this->lang->line('common_at_least'); ?> {0} <?php echo $this->lang->line('common_at_characters'); ?>!")
    		},
     		username: "<?php echo $this->lang->line('location_username_required'); ?>",
     		database:{
     			required: "<?php echo $this->lang->line('location_db_required'); ?>",
		      	regex:"<?php echo $this->lang->line('location_database').' '.$this->lang->line('location_only_letters'); ?>",
     		}
		}
	});
			
});
</script>