<?php echo form_open('items/save_inventory/'.$item_info->item_id,array('id'=>'item_form')); ?>
<div>
	<h3><?php echo $this->lang->line("items_basic_information"); ?></h3><hr>
	<div class="field_row clearfix">
		<table align="center" border="0">
			<tr>
				<td><?php echo form_label($this->lang->line('items_item_number').':', 'items_number',array('class'=>'lable-form')); ?></td>
				<td>
					<?php
					echo form_input(array (
						'name'=>'item_number',
						'id'=>'item_number',
						'value'=>$item_info->item_number,
						'style'       => 'border:none',
						'readonly' => 'readonly',
						'class'=>'text_box'
					)); ?>
				</td>
				<td rowspan="4" width="200">
					<?php  $tuId=md5($this->session->userdata('dblocation').'-'.$item_info->item_id);
						for ($i=0; $i < 5; $i++) {
							if (file_exists('./images/items/'.$tuId.'/'.$tuId.'_'.$i.'.jpg')){
								echo '<div class="photo_add" style="border:1px transparent solid;">
											<div style="background-image:url(\'./images/items/'.$tuId.'/'.$tuId.'_'.$i.'.jpg\')">
											</div>
									  </div>';
							  	break;
							}
						}
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo form_label($this->lang->line('items_name').':', 'items_name',array('class'=>'lable-form')); ?></td>
				<td>
					<?php
					echo form_input(array (
						'name'=>'name',
						'id'=>'name',
						'value'=>$item_info->name,
						'style'       => 'border:none',
						'readonly' => 'readonly',
						'class'=>'text_box'
					)); ?>
				</td>
			</tr>
			<tr>
				<td><?php echo form_label($this->lang->line('items_category').':', 'items_category',array('class'=>'lable-form')); ?></td>
				<td>
					<?php
					echo form_input(array (
						'name'=>'category',
						'id'=>'category',
						'value'=>$item_info->category,
						'style'       => 'border:none',
						'readonly' => 'readonly',
						'class'=>'text_box'
					));	?>
				</td>
			</tr>
			<tr>
				<td><?php echo form_label($this->lang->line('items_current_quantity').':', 'items_quantity',array('class'=>'lable-form')); ?></td>
				<td>
					<?php
					echo form_input(array (
						'name'=>'quantity',
						'id'=>'quantity',
						'value'=>($item_info->is_service?'unlimited':number_format($item_info->quantity)),
						'style'       => 'border:none',
						'readonly' => 'readonly',
						'class'=>'text_box'
					));
					?>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php echo form_close(); ?>
<div id="details_items_po">
	<h3><?php echo $this->lang->line('items_inventory_tracking'); ?></h3><hr>
	<table border="0" align="center" width="100%" >
		<tr align="center" style="font-weight:bold">
			<th width="15%" class="noBorderTop noBorderLeft"><?php echo $this->lang->line('reports_date'); ?></th>
			<th width="25%" class="noBorderTop"><?php echo $this->lang->line('employees_employee'); ?></th>
			<th width="15%" class="noBorderTop"><?php echo $this->lang->line('items_q_i'); ?></th>
			<th width="45%" class="noBorderTop noBorderRigth"><?php echo $this->lang->line('items_observ'); ?></th>
		</tr>
		<?php
			$vector=$this->Inventory->get_inventory_data_for_item($item_info->item_id)->result_array();
			$limit=count($vector);$i=0;
			foreach($vector as $row) {
			$class=($i++==($limit-1)?' noBorderBottom':'');
		?>
		<tr align="center">
			<td class="noBorderLeft<?php echo $class;?>"><?php echo $row['trans_date'];?></td>
			<td class="<?php echo $class;?>"><?php
				$person_id = $row['trans_user'];
				$employee = $this->Employee->get_info($person_id);
				echo $employee->first_name." ".$employee->last_name;
				?>
			</td>
			<td class="<?php echo $class;?>"><?php echo $row['trans_inventory']; ?></td>
			<td class="noBorderRigth<?php echo $class;?>"><?php echo $row['trans_comment'];?></td>
		</tr>
		<?php } ?>
	</table>
</div>
