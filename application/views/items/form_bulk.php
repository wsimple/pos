<?php echo form_open('items/bulk_update/',array('id'=>'item_form')); ?>
<div>
	<h3><?php echo $this->lang->line("items_basic_information"); ?></h3><hr>
	<div class="field_row clearfix">
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('items_name').':', 'name',array('class'=>'lable-form','style'=>'float:none;','style'=>'float:none;')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'name',
					'id'=>'name',
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('items_category').':', 'category',array('class'=>'lable-form','style'=>'float:none;')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'category',
					'id'=>'category',
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('items_supplier').':', 'supplier',array('class'=>'lable-form','style'=>'float:none;')); ?>
				<div><?php echo form_dropdown('supplier_id', $suppliers, '');?></div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix">
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('items_cost_price').':', 'cost_price',array('class'=>'lable-form','style'=>'float:none;')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'cost_price',
					'size'=>'8',
					'id'=>'cost_price',
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('items_unit_price').':', 'unit_price',array('class'=>'lable-form','style'=>'float:none;')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'unit_price',
					'size'=>'8',
					'id'=>'unit_price',
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix">
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('items_reorder_level').':', 'reorder_level',array('class'=>'lable-form','style'=>'float:none;')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'reorder_level',
					'id'=>'reorder_level',
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
		<!-- <div style="width: 210px; float: left">
			<div class="field_row clearfix">	
				<?php /*echo form_label($this->lang->line('items_location').':', 'location',array('class'=>'lable-form','style'=>'float:none;'));*/ ?>
				<div>
				<?php /*echo form_input(array(
					'name'=>'location',
					'id'=>'location',
					'class'=>'text_box'
				));*/ ?>
				</div>
			</div>
		</div> -->
	</div>
	<div class="field_row clearfix">
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('items_tax_1').':', 'tax_percent_1',array('class'=>'lable-form','style'=>'float:none;')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'tax_names[]',
					'id'=>'tax_name_1',
					'size'=>'8',
					'value'=> isset($item_tax_info[0]['name']) ? $item_tax_info[0]['name'] : $this->lang->line('items_sales_tax'),
					'class'=>'text_box'
				));?>
				&nbsp;
				<?php echo form_input(array(
					'name'=>'tax_percents[]',
					'id'=>'tax_percent_name_1',
					'size'=>'3',
					'value'=> isset($item_tax_info[0]['percent']) ? $item_tax_info[0]['percent'] : '',
					'class'=>'text_box'
				));?>%
				</div>
			</div>
		</div>
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('items_tax_2').':', 'tax_percent_2',array('class'=>'lable-form','style'=>'float:none;')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'tax_names[]',
					'id'=>'tax_name_2',
					'size'=>'8',
					'value'=> isset($item_tax_info[1]['name']) ? $item_tax_info[1]['name'] : '',
					'class'=>'text_box'
				));?>
				&nbsp;
				<?php echo form_input(array(
					'name'=>'tax_percents[]',
					'id'=>'tax_percent_name_2',
					'size'=>'3',
					'value'=> isset($item_tax_info[1]['percent']) ? $item_tax_info[1]['percent'] : '',
					'class'=>'text_box'
				));?>%
				</div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix">
		<div style="float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('items_description').':', 'description',array('class'=>'lable-form','style'=>'float:none;')); ?>
				<div>
				<?php echo form_textarea(array(
					'name'=>'description',
					'id'=>'description',
					'rows'=>'5',
					'cols'=>'50',
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix">
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_allow_alt_desciption').':', 'allow_alt_description',array('class'=>'lable-form','style'=>'float:none;')); ?>
				<div><?php echo form_dropdown('allow_alt_description', $allow_alt_desciption_choices);?></div>
			</div>
		</div>
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_is_serialized').':', 'is_serialized',array('class'=>'lable-form','style'=>'float:none;')); ?>
				<div><?php echo form_dropdown('is_serialized', $serialization_choices);?></div>
			</div>
		</div>
	</div>
</div>
<?php
echo form_submit(array(
	'id'=>'submit',
	'value'=>$this->lang->line('common_submit'),
	'class'=>'small_button float_right'
));  
echo form_close(); ?>
<script type='text/javascript'>
//validation and submit handling
$(document).ready(function(){	
	$("#category").autocomplete("<?php echo site_url('items/suggest_category');?>",{max:100,minChars:0,delay:10});
    $("#category").result(function(event, data, formatted)
    {
    });
	$("#category").search();
	
	$('#item_form').validate({
		submitHandler:function(form)
		{
			if(confirm("<?php echo $this->lang->line('items_confirm_bulk_edit') ?>"))
			{
				//Get the selected ids and create hidden fields to send with ajax submit.
				var selected_item_ids=get_selected_values();
				for(k=0;k<selected_item_ids.length;k++)
				{
					$(form).append("<input type='hidden' name='item_ids[]' value='"+selected_item_ids[k]+"' />");
				}				
				$(form).ajaxSubmit({
				success:function(response)
				{
					tb_remove();
					post_bulk_form_submit(response);
				},
				dataType:'json'
				});
			}
		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules: 
		{
			unit_price:{ number:true },
			tax_percent:{ number:true },
			quantity: { number:true },
			reorder_level:{ number:true }
   		},
		messages: 
		{
			unit_price:{ number:"<?php echo $this->lang->line('items_unit_price_number'); ?>" },
			tax_percent:{ number:"<?php echo $this->lang->line('items_tax_percent_number'); ?>" },
			quantity: { number:"<?php echo $this->lang->line('items_quantity_number'); ?>" },
			reorder_level: { number:"<?php echo $this->lang->line('items_reorder_level_number'); ?>" }
		}
	});
});
</script>