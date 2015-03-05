<?php echo form_open('employees/save_profile_employee/'.$profile,array('id'=>'employee_form')); ?>
<div>
	<h3><?php echo $this->lang->line("employees_profile_per"); ?></h3><hr>
	<div class="field_row clearfix" style="margin: 0 0 5px 0">	
		<?php echo form_label($this->lang->line('employees_profile_per_name').':', 'name',array('class'=>'lable-form-required'))?>
		<div>
		<?php 
			$disabled=($profile!='' && $profile=='administrator')?"disabled":"";
			echo form_input(array(
				'name'=>'name',
				'id'=>'name',
				'value'=>$profile,$disabled=>$disabled,
				'class'=>'text_box'
			));
		?>
		<input type="hidden" value="<?php echo $profile; ?>" name="pos-name">
		</div>
	</div>
</div>
<div id="employee_permission_info" class="clearfix">
	<h3><?php echo $this->lang->line("employees_permission_info"); ?></h3>
	<hr>
	<p>
		<?php echo $this->lang->line("employees_permission_desc"); ?>
		<a id="all-che" class="small_button"><?php echo $this->lang->line('employees_profile_SA');?></a>
		<a id="none-che" class="small_button"><?php echo $this->lang->line('employees_profile_DA');?></a>
	</p>
	<ul id="permission_list">
	<?php
	foreach($all_modules->result() as $module){
			switch ($module->module_id) {
				case 'stock_control': 
					$classSee=' no-see'; $styleli=''; break;
				case 'notification_alert':
					$classSee=' no-see'; $styleli='style="width:642px;height:65px;"'; break;					
				default: $classSee='';$styleli=''; break;
			}
	?>
		<li <?php echo $styleli; ?>>
			<span class="medium"><?php echo $this->lang->line('module_'.$module->module_id);?>:</span>
			<span class="small"><?php echo $this->lang->line('module_'.$module->module_id.'_desc');?></span>
			<ul class="module-options<?php echo $classSee; ?>">
				<li class="<?php echo $classSee; ?>">
					<?php
						$subpermissions = explode(',', $module->options);
						$attribs = array(
							'id'=>$module->module_id,
							'name'=>'permissions[]',
							'value'=>$module->module_id,
							'class'=>'permissions-option',
							'checked'=>$this->Employee->has_permission($module->module_id,$profile,true)
						);
						echo form_checkbox($attribs);
					?>
					<span><?php echo $this->lang->line('employees_profile_see'); ?></span>	
				</li>
				<?php 
				foreach ($subpermissions as $subpermission) {
					if ($subpermission != 'none' || $subpermission == '') {
						$attribs = array(
							'id'=>$module->module_id.'-'.$subpermission,
							'name'=>$module->module_id.'[]',
							'value'=>$subpermission,
							'class'=>$module->module_id.'-option',
							'checked'=>$this->Employee->has_privilege_permi($module->module_id,$profile,$subpermission,true));
						echo "<li>";
						echo form_checkbox($attribs);
						echo form_label(ucwords($subpermission));
						echo "</li>";
					}
				}
				?>
			</ul>
		</li>
	<?php } ?>
	</ul>
</div>
<div class="field_row clearfix" style="color: #FF0000; font-size: 11px">
	<?=$this->lang->line('common_fields_required_message')?>
</div>
<ul id="error_message_box"></ul>
<?php
echo form_submit(array(
	'name'=>'submit',
	'id'=>'submit',
	'value'=>$this->lang->line('common_submit'),
	'class'=>'small_button float_right')
);
echo form_close();
?>
<script type='text/javascript'>

//validation and submit handling
$(document).ready(function()
{
	//Funcionavilidad de los permisos
	$('.permissions-option').click(function(event) {
		var perm = this.value;
		if ($(this).is(':checked')){
			$('.'+perm+'-option').attr('checked','checked');
		}else{
			$('.'+perm+'-option').removeAttr('checked');
		}
	});
	$('#permission_list input[type="checkbox"]:not(.permissions-option)').click(function(event) {
		var perm = $(this).attr('class').split('-');
		if ($(this).is(':checked')){
			$('input[type="checkbox"][value="'+perm[0]+'"].permissions-option').attr('checked','checked');
		}else{
			var ul=$(this).parents('ul.module-options');
			if ($(ul).hasClass('no-see')){
				var check=$('input[type=checkbox]',ul),band=false,obj;
				$.each(check, function(index, val) {
					if ($(this).parent('li').hasClass('no-see')) obj=$(this);
					else if ($(this).is(':checked')) band=true;
				});
				if (!band) $(obj).removeAttr('checked');
			}
		}
	});
	$('#all-che').click(function(){
		$('input[type="checkbox"]').attr('checked','checked');
	});
	$('#none-che').click(function(){
		$('input[type="checkbox"]').removeAttr('checked');
	});
	function post_person_form_submit(response){	console.log(response);
		if(!response.success){ set_feedback(response.message,'error_message',true);	}
		else{ window.location.reload();}
	}
	$('#employee_form').validate({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
			success:function(response)
			{   
				if (response.success) tb_remove();
				post_person_form_submit(response);
			},
			dataType:'json'
		});
		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules:
		{
			name: {
			    required: true,
			    regex:/^[a-zA-Z\u00C0-\u00FF\s]+$/,
			    minlength: 3
		    }
   		},
		messages:
		{
     		name: {
			      required: "<?php echo $this->lang->line('employees_profile_per_name'); ?>",
			      regex:"<?php echo  $this->lang->line('common_first_name_only_char');?>",
			      minlength: jQuery.format("<?php echo $this->lang->line('common_at_least'); ?> {0} <?php echo $this->lang->line('common_at_characters'); ?>!")
    		}
		}
	});
});
</script>
