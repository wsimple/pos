<?php 
if (!isset($sendto)) $sendto=''; else $sendto='-1/'.$sendto; 
echo form_open_multipart('items/save/'.$item_info->item_id.$sendto,array('id'=>'item_form')); 
?>
<!-- <div id="item_basic_info"> -->
<div>
	<h3><?php echo $this->lang->line("items_basic_information"); ?></h3><hr>
	<div class="field_row clearfix">
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_item_number').':', 'name',array('class'=>'lable-form')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'item_number',
					'id'=>'item_number',
					'value'=>$item_info->item_number,
					'class'=>'text_box' //,'disabled'=>'disabled'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_name').':', 'items_name',array('class'=>'lable-form-required')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'name',
					'id'=>'name',
					'value'=>$item_info->name,
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_category').':', 'category',array('class'=>'lable-form-required')); ?>
				<div >
				<?php echo form_input(array(
					'name'=>'category',
					'id'=>'category',
					'value'=>$item_info->category,
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix">
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?=form_label($this->lang->line('items_supplier').':', 'supplier',array('class'=>'lable-form'))?>
				<div>
				<?=form_dropdown('supplier_id', $suppliers, $selected_supplier,'style="width:200px;"')?>
				</div>
			</div>
		</div>
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_cost_price').':', 'cost_price',array('class'=>'lable-form-required')); ?>
				<div style="width: 85%;">
				<?php echo form_input(array(
					'name'=>'cost_price',
					'size'=>'8',
					'id'=>'cost_price',
					'value'=>$item_info->cost_price,
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_unit_price').':', 'unit_price',array('class'=>'lable-form-required')); ?>
				<div style="width: 85%;">
				<?php echo form_input(array(
					'name'=>'unit_price',
					'size'=>'8',
					'id'=>'unit_price',
					'value'=>$item_info->unit_price,
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix">
		<div class="field_row clearfix">
			<?php echo form_label($this->lang->line('items_pictures').':', 'photo_label',array('class'=>'lable-form','style'=>'float:none;','style'=>'float:none;')); ?>
		</div>
		<div id="previewPhotos">
			<center>
				<?php $tuId=md5($this->session->userdata('dblocation').'-'.$item_info->item_id);
				if ($item_info->item_id && is_dir('./images/items/'.md5($item_info->item_id).'/')) $band=true; 
				for ($i=0; $i < 5; $i++) {  $bgImage="";$span="";
					if (file_exists('./images/items/'.$tuId.'/'.$tuId.'_'.$i.'.jpg')){
						$bgImage='style="background-image: url(\'./images/items/'.$tuId.'/'.$tuId.'_'.$i.'.jpg\')"';
						$span='<span></span>';
					}
					echo '<div class="photo_add">
								'.$span.'
								<div attr="'.$i.'" '.$bgImage.'>
									<input type="file" name="photo[]" id="photo_'.$i.'" class="invisible" multiple disabled="disabled">
									<input type="hidden" name="photo_hi_'.$i.'" id="photo_hi_'.$i.'" value="'.$i.'" disabled="disabled">
								</div>
						  </div>';
				}
				?>
			</center>
			<div class="upload_label"><?php echo $this->lang->line("common_logo_dimensiones"); ?></div>
		</div>
	</div>
	<div class="field_row clearfix">
		<!-- <div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php //echo form_label($this->lang->line('items_quantity').':', 'quantity',array('class'=>'lable-form-required')); ?>
				<div>
				<?php //echo form_input(array('name'=>'quantity','id'=>'quantity','value'=>$item_info->quantity,'class'=>'text_box'));?>
				</div>
			</div>
		</div> -->
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_reorder_level').':', 'reorder_level_label',array('class'=>'lable-form')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'reorder_level',
					'id'=>'reorder_level',
					'value'=>($item_info->reorder_level?$item_info->reorder_level:0),
					'class'=>'text_box'
				));?>
				</div>
				<div class="upload_label"><?php echo $this->lang->line("items_reorder_level_text"); ?></div>
			</div>
		</div>
		<!-- <div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_location').':', 'location',array('class'=>'lable-form')); ?>	
				<div >
				<?php echo form_input(array(
					'name'=>'location',
					'id'=>'location',
					'value'=>$item_info->location,
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div> -->
		<!-- <div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_allow_alt_desciption').':', 'allow_alt_description',array('class'=>'lable-form')); ?>
				<div >
				<?php echo form_checkbox(array(
					'name'=>'allow_alt_description',
					'id'=>'allow_alt_description',
					'value'=>1,
					'checked'=>($item_info->allow_alt_description)? 1 :0,
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_is_serialized').':', 'is_serialized',array('class'=>'lable-form')); ?>
				<div >
				<?php echo form_checkbox(array(
					'name'=>'is_serialized',
					'id'=>'is_serialized',
					'value'=>1,
					'checked'=>($item_info->is_serialized)? 1 : 0,
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div> -->
	</div>
	<div class="field_row clearfix">
		<div style="width: 100%;">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_tax').':', 'tax_percent',array('class'=>'lable-form','style'=>'float:none;display:block;')); ?>
				<div style="width: 45%;display:inline-block;">
					<?php echo form_label($this->lang->line('items_tax_1').':', 'tax_percent_1',array('class'=>'lable-form','style'=>'float:none;')).'&nbsp;&nbsp;';  
					echo form_input(array(
						'name'=>'tax_names[]',
						'id'=>'tax_name_1',
						'size'=>'8',
						'value'=> isset($item_tax_info[0]['name']) ? $item_tax_info[0]['name'] : $this->config->item('default_tax_1_name'),
						'class'=>'text_box'
					)); 
					echo form_input(array(
						'name'=>'tax_percents[]',
						'id'=>'tax_percent_name_1',
						'size'=>'3',
						'value'=> isset($item_tax_info[0]['percent']) ? $item_tax_info[0]['percent'] : $default_tax_1_rate,
						'class'=>'text_box'
					));?>&nbsp;%
				</div>
				<div style="width: 45%;display:inline-block;">
				<?php echo form_label($this->lang->line('items_tax_2').':', 'tax_percent_2',array('class'=>'lable-form','style'=>'float:none;')).'&nbsp;&nbsp;'; 
				 echo form_input(array(
					'name'=>'tax_names[]',
					'id'=>'tax_name_2',
					'size'=>'8',
					'value'=> isset($item_tax_info[1]['name']) ? $item_tax_info[1]['name'] : $this->config->item('default_tax_2_name'),
					'class'=>'text_box'
				));
				echo form_input(array(
					'name'=>'tax_percents[]',
					'id'=>'tax_percent_name_2',
					'size'=>'3',
					'value'=> isset($item_tax_info[1]['percent']) ? $item_tax_info[1]['percent'] : $default_tax_2_rate,
					'class'=>'text_box'
				));?>&nbsp;%
				</div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix">
		<div style="width: 100%;">
			<?=form_label($this->lang->line('items_tipe').':', 'tipe',array('class'=>'lable-form','style'=>'float:none;display:block;'))?>
			<div style="display:inline-block;width:30%;">
			<?php echo form_checkbox(array(
				'id'=>'is_service',
				'name'=>'is_service',
				'class'=>'wide',
				'value'=>'is_service',
				'checked'=>$item_info->is_service
			)); ?>&nbsp;
			<?=form_label($this->lang->line('items_is_service').':', 'service',array('class'=>'lable-form','style'=>'float:none'))?>
			</div>
			<div style="display:inline-block;width:30%;">
				<?php echo form_checkbox(array(
					'id'=>'is_locked',
					'name'=>'is_locked',
					'class'=>'wide',
					'value'=>'is_locked',
					'checked'=>$item_info->is_locked
				)); ?>&nbsp;
				<?=form_label($this->lang->line('items_is_locked').':', 'locked',array('class'=>'lable-form','style'=>'float:none'))?>
			</div>
		</div>
	</div>
	<div class="field_row clearfix">
		<div style="float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('items_description').':', 'description',array('class'=>'lable-form')); ?>
				<div>
				<?php echo form_textarea(array(
					'name'=>'description',
					'id'=>'description',
					'value'=>$item_info->description,
					'rows'=>'5',
					'cols'=>'60')
				);?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="field_row clearfix requested">
	<?=$this->lang->line('common_fields_required_message')?>
</div>
<ul id="error_message_box"></ul>
<?php echo form_submit(array(
	'name'=>'enviar',
	'id'=>'enviar',
	'value'=>$this->lang->line('common_submit'),
	'class'=>'small_button float_right'
)); ?>
<?php echo form_close(); ?>
<script type='text/javascript'>
//validation and submit handling
$(function(){
	var pass=true,extensiones_permitidas = new Array(".gif", ".jpg", ".png");
	$("#category")
		.autocomplete("<?php echo site_url('items/suggest_category');?>",{max:100,minChars:0,delay:10})
		.result(function(event,data,formatted){})
		.search();
	$('span.width_label_photo,span.height_label_photo').html(250);
	$('.photo_add>div').click(function() {
		$('input:file',this).removeAttr('disabled').click();
	});
	$('.photo_add span').click(function(e) {
		e.preventDefault();
		if (confirm('<?php echo $this->lang->line("items_confirm_delete_picture"); ?>')){
			var url=$('#item_form').attr('action').split('save');
			var div=$(this).next('div');
			$$.ajax({
				url: url[0]+'delete_picture'+url[1]+'/'+div.attr('attr'),
				dataType: 'json',
				success: function(data){
					if(data.success){
						div.removeAttr('style').prev('span').remove();
					}else{
						alert('Excuse me, but there was an error please try again');
						location.reload();
					}
				}
			});
		}
	});
	$('.photo_add input:file').change(function(){
		if($(this).val()){
			if (validaImagen($(this).val())){
				$(this).next('input').removeAttr('disabled');
				$(this).parents('div[attr]').addClass('photo_add_active');	
			}else{ 
				alert('<?php echo $this->lang->line("common_image_faild"); ?>'); 
				$(this).removeAttr('value');
				$(this).attr('disabled','disabled').next('input').attr('disabled','disabled');
				$(this).parents('div[attr]').removeClass('photo_add_active');	
			}
		}else{
			$(this).attr('disabled','disabled').next('input').attr('disabled','disabled');
			$(this).parents('div[attr]').removeClass('photo_add_active');	
		}
	});
	$('#item_form').validate({
		submitHandler:function(form)
		{
			$('#item_number').val($('#scan_item_number').val());
			$(form).ajaxSubmit({
				success:function(response){ 
					console.log(response);
					if (response.action){
						switch (response.action){
							case 'receiving': case 'sales': case 'shipping':
								$('#add_item_form #item').val(response.item_id).parents('#add_item_form').submit();
							break;
						}
					}
					tb_remove(false);
					post_item_form_submit(response);
				},
				dataType:'json'
			});
			return false;
		},
		errorLabelContainer:"#error_message_box",
 		wrapper:"li",
		rules:
		{
			name:"required",
			category:"required",
			cost_price:{
				required:true,
				number:true
			},
			unit_price:{
				required:true,
				number:true
			},
			tax_percent:{
				required:true,
				number:true
			},
			quantity:{
				required:true,
				number:true
			},
			reorder_level:{
				// required:true,
				number:true
			}
			// ,supplier_id:{ required:true }
   		},
		messages:
		{
			name:"<?php echo $this->lang->line('items_name_required'); ?>",
			category:"<?php echo $this->lang->line('items_category_required'); ?>",
			cost_price:{
				required:"<?php echo $this->lang->line('items_cost_price_required'); ?>",
				number:"<?php echo $this->lang->line('items_cost_price_number'); ?>"
			},
			unit_price:{
				required:"<?php echo $this->lang->line('items_unit_price_required'); ?>",
				number:"<?php echo $this->lang->line('items_unit_price_number'); ?>"
			},
			tax_percent:{
				required:"<?php echo $this->lang->line('items_tax_percent_required'); ?>",
				number:"<?php echo $this->lang->line('items_tax_percent_number'); ?>"
			},
			quantity:{
				required:"<?php echo $this->lang->line('items_quantity_required'); ?>",
				number:"<?php echo $this->lang->line('items_quantity_number'); ?>"
			},
			reorder_level:{
//				required:"<?php echo $this->lang->line('items_reorder_level_required'); ?>",
				number:"<?php echo $this->lang->line('items_reorder_level_number'); ?>"
			},
			supplier_id:{
				required:"<?php echo $this->lang->line('items_supplier_id_required'); ?>"
			}

		}
	});
	function validaImagen(imagen){
		var ext=imagen.split('.').pop().toLowerCase();
		return ['jpg','jpeg','png','gif'].indexOf(ext)>=0;
	}
});
//new jQuery
(function($){
	var msg='<?=$this->lang->line("items_is_service_true")?>';reor=0;
	$('#is_service').change(function(){
		var checked=$(this).is(':checked');
		$(this).parents('#item_form').find('#quantity,#reorder_level').prop('disabled',checked);
			// .parent().parent()[checked?'hide':'show']();
			if (checked.length > 0){
				reor=$('#item_form #reorder_level').val();
				$('#item_form #reorder_level').val(msg);
			}//else $('#item_form #reorder_level').val(reor);
		// $(this).parents('#item_form').find('[name="supplier_id"]')[checked?'removeAttr':'attr']('required','');
	}).change();
})(jQueryNew);
</script>
