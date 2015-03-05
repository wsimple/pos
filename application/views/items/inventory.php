<?php echo form_open('items/save_inventory/'.$item_info->item_id,array('id'=>'item_form')); ?>
<div>
	<h3><?php echo $this->lang->line("items_basic_information"); ?></h3><hr>
	<table align="center" border="0">
		<tr>
			<th><?php echo form_label($this->lang->line('items_item_number').':', 'name',array('class'=>'lable-form')); ?></th>
			<td>
				<?php 
				echo form_input(array (
					'name'=>'item_number',
					'id'=>'item_number',
					'value'=>$item_info->item_number,
					'style'       => 'border:none',
					'readonly' => 'readonly'
				)); ?>
			</td>
		</tr>
		<tr>
			<th><?php echo form_label($this->lang->line('items_name').':', 'name',array('class'=>'lable-form')); ?></th>
			<td>
				<?php 
				echo form_input(array (
					'name'=>'name',
					'id'=>'name',
					'value'=>$item_info->name,
					'style'       => 'border:none',
					'readonly' => 'readonly'
				));	?>
			</td>
		</tr>
		<tr>
			<th><?php echo form_label($this->lang->line('items_category').':', 'category',array('class'=>'lable-form')); ?></th>
			<td>
				<?php 
				echo form_input(array (
					'name'=>'category',
					'id'=>'category',
					'value'=>$item_info->category,
					'style'       => 'border:none',
					'readonly' => 'readonly'
				)); ?>
			</td>
		</tr>
		<tr>
			<th><?php echo form_label($this->lang->line('items_current_quantity').':', 'quantity',array('class'=>'lable-form')); ?></th>
			<td>
				<?php 
				echo form_input(array (
					'name'=>'quantity',
					'id'=>'quantity',
					'value'=>$item_info->quantity,
					'style'       => 'border:none',
					'readonly' => 'readonly'
				));
				?>
			</td>
		</tr>
	</table>
</div>
<div>
	<h3><?=$this->lang->line('items_inventory_manipulation_title')?></h3><hr>
	<div>
		<div class="field_row clearfix">
			<strong><?php echo form_label($this->lang->line('items_add_minus').':', 'quantity',array('class'=>'lable-form-required','style'=>'float: none;')); ?></strong>
			<div>
			<?php echo form_input(array(
				'name'=>'newquantity',
				'id'=>'newquantity',
				'class'=>'text_box'
			));?>
			</div>
		</div>
	</div>
	<!-- <div class="field_row clearfix"> -->
	<?php
	// $options = 'id="dbselected"';
	// echo form_label('To:', 'newquantityTo');
	?>
		<!-- <div class='form_field'>
		<?php //echo form_dropdown('dbselected', $dbs, '...', $options); ?>
		</div> -->
		<!-- <h6 clas="wire">(This option to send to another location and are deducted in this store!)</h6> -->
	<!-- </div> -->

	<div >
		<div class="field_row clearfix">
			<strong><?php echo form_label($this->lang->line('items_inventory_comments').':', 'description',array('class'=>'lable-form','style'=>'float: none;')); ?></strong>
			<div>
				<?php echo form_textarea(array(
					'name'=>'trans_comment',
					'id'=>'trans_comment',
					'rows'=>'3',
					'cols'=>'40',
					'class'=>'text_box'
				));?>
			</div>
		</div>
	</div>
</div>
<!-- <div id="required_fields_message"><?php //echo $this->lang->line('common_fields_required_message'); ?></div> -->
<div class="field_row clearfix requested">
	<?=$this->lang->line('common_fields_required_message')?><br>
	<?=$this->lang->line('items_inventory_manipulation_note')?>
</div>
<ul id="error_message_box"></ul>
<?php
echo form_submit(array(
	'name'=>'submit',
	'id'=>'submit',
	'value'=>$this->lang->line('common_submit'),
	'class'=>'small_button float_right'
));
echo form_close();
?>
<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	$('#item_form').validate({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_item_form_submit(response);
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules:
		{
			newquantity:
			{
				required:true,
				number:true
			}
   		},
		messages:
		{
			newquantity:
			{
				required:"<?php echo $this->lang->line('items_quantity_required'); ?>",
				number:"<?php echo $this->lang->line('items_quantity_number'); ?>"
			}
		}
	});
});
</script>
