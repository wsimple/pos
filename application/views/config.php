<?php $this->load->view("partial/header"); ?>

<div id="page_title">Store Profile</div>

<?php echo form_open_multipart('config/save/',array('id'=>'config_form')); ?>

<div class="field_row clearfix" style="display:block;">
	<?php
		if ($this->Employee->has_privilege('save', 'config')){ 
			echo form_submit(
				array(
					'name'=>'submitC',
					'id'=>'submitC',
					'value'=>$this->lang->line('common_submit'),
					'class'=>'small_button float_right'
				)
			);
		}
	?>
</div>
<div class="box-form-view">
	<ul id="error_message_box"></ul>
	<div class="field_row clearfix" style="display:block;margin: 0 0 5px 0">
		<div style="width: 250px; float: left">
			<div class="field_row clearfix" style="display:block;">	
				<?php echo form_label($this->lang->line('config_company').':', 'company',array('class'=>'lable-form-required','style'=>'width: 160px;')); ?>
				<div>
					<?php 
						echo form_input(
							array(
								'name'=>'company',
								'id'=>'company',
								'value'=>$this->config->item('company'),
								'class'=>'text_box'
							)
						);
					?>
				</div>
				<small>(requested)</small>
			</div>
		</div>
		<div style="width: 250px; float: left">
			<div class="field_row clearfix" style="display:block;">	
				<?php echo form_label($this->lang->line('config_phone').':', 'phone',array('class'=>'lable-form-required','style'=>'width: 160px;')); ?>
				<div>
					<?php 
						echo form_input(
							array(
								'name'=>'phone',
								'id'=>'phone',
								'value'=>$this->config->item('phone'),
								'class'=>'text_box'
							)
						);
					?>
				</div>
			</div>
		</div>
		<div style="width: 250px; float: left">
			<div class="field_row clearfix" style="display:block;">	
				<?php echo form_label('Company Fax:', 'fax',array('class'=>'lable-form')); ?>
				<div>
					<?php 
						echo form_input(
							array(
								'name'=>'fax',
								'id'=>'fax',
								'value'=>$this->config->item('fax'),
								'class'=>'text_box'
							)
						);
					?>
				</div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix" style="display:block;margin: 0 0 5px 0">
		<div style="width: 250px; float: left">
			<div class="field_row clearfix" style="display:block;">
				<?php echo form_label('Company Email:', 'email',array('class'=>'lable-form-required')); ?>
				<div>
					<?php 
						echo form_input(
							array(
								'name'=>'email',
								'id'=>'email',
								'value'=>$this->config->item('email'),
								'class'=>'text_box'
							)
						);
					?>
				</div>
			</div>
		</div>
		<div style="width: 250px; float: left">
			<div class="field_row clearfix" style="display:block;">	
				<?php echo form_label('Company '.$this->lang->line('config_website').':', 'website',array('class'=>'lable-form', 'style'=>'width:160px')); ?>
				<div>
					<?php 
						echo form_input(
							array(
								'name'=>'website',
								'id'=>'website',
								'value'=>$this->config->item('website'),
								'class'=>'text_box',
								'style'=>'width:365px;'
							)
						);
					?>
				</div>
			</div>
		</div>
		<div style="width: 100%; float: left; margin-top: 5px">
			<div class="field_row clearfix" style="display:block;">
			<?php echo form_label('Company '.$this->lang->line('common_logo').':', 'logo',array('class'=>'lable-form')); ?>
			<div class='form_field' style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; ">
				<input type="file" name="logo" id="logo">
				<input type="hidden" name="logo_name" id="logo_name" value="<?=$this->config->item('logo')?>">
				<div class="upload_label">
					<?php echo $this->lang->line('common_logo_dimensiones');?>
				</div>
			</div>
			</div>
		</div>
		<div class="field_row clearfix" style="display:block;margin: 0 0 5px 0">
			<div class="field_row clearfix" style="display:block;">	
				<?php echo form_label($this->lang->line('config_address').':', 'addresslabel',array('class'=>'lable-form-required','style'=>'width:160px;')); ?>
				<div>
					<?php 
						echo form_textarea(
							array(
								'name'=>'address',
								'id'=>'address',
								'rows'=>'4',
								'cols'=>'17',
								'value'=>$this->config->item('address'),
								'class'=>'text_box',
								'style'=>'width:98%; height: 60px; margin: 0 0 0 8px'
							)
						);
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="field_row clearfix" style="margin: 0 0 5px 0;display:block;">
		<div class="sub-title-view"><?php echo $this->lang->line('config_sales_info'); ?></div>
	</div>
	<div class="field_row clearfix" style="margin: 0 0 5px 0;display:block;">
		<div>
			<div class="field_row clearfix" style="float: left;margin-right: 70px;">
				<div style="height: 27px;">
				<?php echo form_label($this->lang->line('config_service_price').':', 'servicelabel',array('class'=>'lable-form-required','style'=>'width:160px;')); ?>
				</div>
				<div>
					<?php 
						echo form_input(
							array(
								'name'=>'default_service',
								'id'=>'default_service',
								'size'=>'10',
								'value'=>$this->config->item('default_service')? $this->config->item('default_service') : '',
								'class'=>'text_box'
							)
						).' '.$this->config->item('currency_symbol');
					?>
				</div>
			</div>
			<div class="field_row clearfix" style="margin: 0 0 5px 0;float: left;margin-right: 80px;">
				<div style="height: 27px;">
				<?php echo form_label($this->lang->line('config_service_item_percentage').':', 'default_percentagelabel',array('class'=>'lable-form-required','style'=>'width:160px;')); ?>
				</div>
				<div>
					<?php 
						echo form_input(
							array(
								'name'=>'default_item_percentage',
								'id'=>'default_item_percentage',
								'size'=>'10',
								'value'=>$this->config->item('default_item_percentage')? $this->config->item('default_item_percentage') :'',
								'class'=>'text_box'
							)
						);
					?>&nbsp;%
				</div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix" style="margin: 0 0 5px 0;display:block;">
		<div>
			<div class="field_row clearfix" style="float: left;margin-right: 70px;">
				<div style="height: 27px;">
				<?php echo form_label($this->lang->line('config_default_tax_rate_1').':', 'default_tax_1_rate',array('class'=>'lable-form-required','style'=>'width:160px;')); ?>
				</div>
				<div>
					<?php 
						echo form_input(
							array(
								'name'=>'default_tax_1_name',
								'id'=>'default_tax_1_name',
								'size'=>'10',
								'value'=>$this->config->item('default_tax_1_name')!==FALSE ? $this->config->item('default_tax_1_name') : $this->lang->line('items_sales_tax_1'),
								'class'=>'text_box'
							)
						);

						echo form_input(
							array(
								'name'=>'default_tax_1_rate',
								'id'=>'default_tax_1_rate',
								'size'=>'4',
								'value'=>$this->config->item('default_tax_1_rate'),
								'class'=>'text_box'
							)
						);
					?>&nbsp;%
				</div>
			</div>
			<div class="field_row clearfix" style="margin: 0 0 5px 0;float: left;margin-right: 80px;">
				<div style="height: 27px;">
				<?php echo form_label($this->lang->line('config_default_tax_rate_2').':', 'default_tax_1_rate',array('class'=>'lable-form','style'=>'width:160px;')); ?>
				</div>
				<div>
					<?php 
						echo form_input(
							array(
								'name'=>'default_tax_2_name',
								'id'=>'default_tax_2_name',
								'size'=>'10',
								'value'=>$this->config->item('default_tax_2_name')!==FALSE ? $this->config->item('default_tax_2_name') : $this->lang->line('items_sales_tax_2'),
								'class'=>'text_box'
							)
						);
						
						echo form_input(
							array(
								'name'=>'default_tax_2_rate',
								'id'=>'default_tax_2_rate',
								'size'=>'4',
								'value'=>$this->config->item('default_tax_2_rate'),
								'class'=>'text_box'
							)
						);
					?>&nbsp;%
				</div>
			</div>
		</div>
		<div class="field_row clearfix" style="display:block;">
			<div style="height: 27px;">
			<?php echo form_label($this->lang->line('config_currency_symbol').':', 'currency_symbol',array('class'=>'lable-form','style'=>'width:160px;')); ?>
			</div>
			<div>
				<?php 
					echo form_input(
						array(
							'name'=>'currency_symbol',
							'id'=>'currency_symbol',
							'value'=>$this->config->item('currency_symbol'),
							'class'=>'text_box',
							'style'=>'width:20px;'
						)
					);
				?>
			</div>
		</div>
		<div class="field_row clearfix" style="display:block;">
			<div  style="float: left;">
				<div>
				<?php echo form_label($this->lang->line('config_print_after_sale').':', 'print_after_sale',array('class'=>'lable-form','style'=>'width:250px;')); ?>
				</div>
				<div style="height: 27px;">
					<?php 
						echo form_checkbox(
							array(
								'name'=>'print_after_sale',
								'id'=>'print_after_sale',
								'value'=>'print_after_sale',
								'checked'=>$this->config->item('print_after_sale')
							)
						);
					?>
				</div>
			</div>
			<div style="float: left;">
				<div>
				<?php echo form_label($this->lang->line('config_alert_after_sale').':', 'alert_after_sale',array('class'=>'lable-form','style'=>'width:270px;')); ?>
				</div>
				<div style="height: 27px;">
					<?php 
						echo form_checkbox(
							array(
								'name'=>'alert_after_sale',
								'id'=>'alert_after_sale',
								'value'=>'alert_after_sale',
								'checked'=>$this->config->item('alert_after_sale')
							)
						);
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="field_row clearfix" style="margin: 0 0 5px 0;display:block;">
		<div class="sub-title-view" style="color: #FF0000">
			<?php echo $this->lang->line('common_return_policy'); ?>
		</div>
	</div>

	<div class="field_row clearfix" style="margin: 0 0 5px 0;display:block;">
		<div class="field_row clearfix" style="display:block;">	
			<div>
				<?php 
					echo form_textarea(
						array(
							'name'=>'return_policy',
							'id'=>'return_policy',
							'rows'=>'4',
							'cols'=>'17',
							'value'=>$this->config->item('return_policy'),
							'class'=>'text_box',
							'style'=>'width:98%; height: 60px; margin: 0 0 0 8px'
						)
					);
				?>
			</div>
		</div>
	</div>

	<div class="field_row clearfix" style="margin: 0 0 5px 0;display:block;">
		<div class="sub-title-view">
			General Setting
		</div>
	</div>

	<div class="field_row clearfix" style="margin: 0 0 5px 0;display:block;">
		<div class="field_row clearfix" style="float: left">	
			<?php echo form_label($this->lang->line('config_language').':', 'language',array('class'=>'lable-form', 'style'=>'width:170px;')); ?>
			<div>
				<?php 
					echo form_dropdown('language', 
						array(
							'english'  => 'English',
							'spanish'   => 'Spanish'), 
							$this->config->item('language')
						);
				?>
			</div>
		</div>

		<div class="field_row clearfix" style="display:block;">	
		<?php echo form_label($this->lang->line('config_timezone').':', 'timezone',array('class'=>'lable-form', 'style'=>'width:300px;')); ?>
			<div>
			<?php echo form_dropdown('timezone', 
			 array(
				'Pacific/Midway'=>'(GMT-11:00) Midway Island, Samoa',
				'America/Adak'=>'(GMT-10:00) Hawaii-Aleutian',
				'Etc/GMT+10'=>'(GMT-10:00) Hawaii',
				'Pacific/Marquesas'=>'(GMT-09:30) Marquesas Islands',
				'Pacific/Gambier'=>'(GMT-09:00) Gambier Islands',
				'America/Anchorage'=>'(GMT-09:00) Alaska',
				'America/Ensenada'=>'(GMT-08:00) Tijuana, Baja California',
				'Etc/GMT+8'=>'(GMT-08:00) Pitcairn Islands',
				'America/Los_Angeles'=>'(GMT-08:00) Pacific Time (US & Canada)',
				'America/Denver'=>'(GMT-07:00) Mountain Time (US & Canada)',
				'America/Chihuahua'=>'(GMT-07:00) Chihuahua, La Paz, Mazatlan',
				'America/Dawson_Creek'=>'(GMT-07:00) Arizona',
				'America/Belize'=>'(GMT-06:00) Saskatchewan, Central America',
				'America/Cancun'=>'(GMT-06:00) Guadalajara, Mexico City, Monterrey',
				'Chile/EasterIsland'=>'(GMT-06:00) Easter Island',
				'America/Chicago'=>'(GMT-06:00) Central Time (US & Canada)',
				'America/New_York'=>'(GMT-05:00) Eastern Time (US & Canada)',
				'America/Havana'=>'(GMT-05:00) Cuba',
				'America/Bogota'=>'(GMT-05:00) Bogota, Lima, Quito, Rio Branco',
				'America/Caracas'=>'(GMT-04:30) Caracas',
				'America/Santiago'=>'(GMT-04:00) Santiago',
				'America/La_Paz'=>'(GMT-04:00) La Paz',
				'Atlantic/Stanley'=>'(GMT-04:00) Faukland Islands',
				'America/Campo_Grande'=>'(GMT-04:00) Brazil',
				'America/Goose_Bay'=>'(GMT-04:00) Atlantic Time (Goose Bay)',
				'America/Glace_Bay'=>'(GMT-04:00) Atlantic Time (Canada)',
				'America/St_Johns'=>'(GMT-03:30) Newfoundland',
				'America/Araguaina'=>'(GMT-03:00) UTC-3',
				'America/Montevideo'=>'(GMT-03:00) Montevideo',
				'America/Miquelon'=>'(GMT-03:00) Miquelon, St. Pierre',
				'America/Godthab'=>'(GMT-03:00) Greenland',
				'America/Argentina/Buenos_Aires'=>'(GMT-03:00) Buenos Aires',
				'America/Sao_Paulo'=>'(GMT-03:00) Brasilia',
				'America/Noronha'=>'(GMT-02:00) Mid-Atlantic',
				'Atlantic/Cape_Verde'=>'(GMT-01:00) Cape Verde Is.',
				'Atlantic/Azores'=>'(GMT-01:00) Azores',
				'Europe/Belfast'=>'(GMT) Greenwich Mean Time : Belfast',
				'Europe/Dublin'=>'(GMT) Greenwich Mean Time : Dublin',
				'Europe/Lisbon'=>'(GMT) Greenwich Mean Time : Lisbon',
				'Europe/London'=>'(GMT) Greenwich Mean Time : London',
				'Africa/Abidjan'=>'(GMT) Monrovia, Reykjavik',
				'Europe/Amsterdam'=>'(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna',
				'Europe/Belgrade'=>'(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague',
				'Europe/Brussels'=>'(GMT+01:00) Brussels, Copenhagen, Madrid, Paris',
				'Africa/Algiers'=>'(GMT+01:00) West Central Africa',
				'Africa/Windhoek'=>'(GMT+01:00) Windhoek',
				'Asia/Beirut'=>'(GMT+02:00) Beirut',
				'Africa/Cairo'=>'(GMT+02:00) Cairo',
				'Asia/Gaza'=>'(GMT+02:00) Gaza',
				'Africa/Blantyre'=>'(GMT+02:00) Harare, Pretoria',
				'Asia/Jerusalem'=>'(GMT+02:00) Jerusalem',
				'Europe/Minsk'=>'(GMT+02:00) Minsk',
				'Asia/Damascus'=>'(GMT+02:00) Syria',
				'Europe/Moscow'=>'(GMT+03:00) Moscow, St. Petersburg, Volgograd',
				'Africa/Addis_Ababa'=>'(GMT+03:00) Nairobi',
				'Asia/Tehran'=>'(GMT+03:30) Tehran',
				'Asia/Dubai'=>'(GMT+04:00) Abu Dhabi, Muscat',
				'Asia/Yerevan'=>'(GMT+04:00) Yerevan',
				'Asia/Kabul'=>'(GMT+04:30) Kabul',
				'Asia/Yekaterinburg'=>'(GMT+05:00) Ekaterinburg',
				'Asia/Tashkent'=>'(GMT+05:00) Tashkent',
				'Asia/Kolkata'=>'(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi',
				'Asia/Katmandu'=>'(GMT+05:45) Kathmandu',
				'Asia/Dhaka'=>'(GMT+06:00) Astana, Dhaka',
				'Asia/Novosibirsk'=>'(GMT+06:00) Novosibirsk',
				'Asia/Rangoon'=>'(GMT+06:30) Yangon (Rangoon)',
				'Asia/Bangkok'=>'(GMT+07:00) Bangkok, Hanoi, Jakarta',
				'Asia/Krasnoyarsk'=>'(GMT+07:00) Krasnoyarsk',
				'Asia/Hong_Kong'=>'(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi',
				'Asia/Irkutsk'=>'(GMT+08:00) Irkutsk, Ulaan Bataar',
				'Australia/Perth'=>'(GMT+08:00) Perth',
				'Australia/Eucla'=>'(GMT+08:45) Eucla',
				'Asia/Tokyo'=>'(GMT+09:00) Osaka, Sapporo, Tokyo',
				'Asia/Seoul'=>'(GMT+09:00) Seoul',
				'Asia/Yakutsk'=>'(GMT+09:00) Yakutsk',
				'Australia/Adelaide'=>'(GMT+09:30) Adelaide',
				'Australia/Darwin'=>'(GMT+09:30) Darwin',
				'Australia/Brisbane'=>'(GMT+10:00) Brisbane',
				'Australia/Hobart'=>'(GMT+10:00) Hobart',
				'Asia/Vladivostok'=>'(GMT+10:00) Vladivostok',
				'Australia/Lord_Howe'=>'(GMT+10:30) Lord Howe Island',
				'Etc/GMT-11'=>'(GMT+11:00) Solomon Is., New Caledonia',
				'Asia/Magadan'=>'(GMT+11:00) Magadan',
				'Pacific/Norfolk'=>'(GMT+11:30) Norfolk Island',
				'Asia/Anadyr'=>'(GMT+12:00) Anadyr, Kamchatka',
				'Pacific/Auckland'=>'(GMT+12:00) Auckland, Wellington',
				'Etc/GMT-12'=>'(GMT+12:00) Fiji, Kamchatka, Marshall Is.',
				'Pacific/Chatham'=>'(GMT+12:45) Chatham Islands',
				'Pacific/Tongatapu'=>'(GMT+13:00) Nuku\'alofa',
				'Pacific/Kiritimati'=>'(GMT+14:00) Kiritimati'
				), $this->config->item('timezone') ? $this->config->item('timezone') : date_default_timezone_get());
				?>
			</div>
		</div>
	</div>
	<div class="field_row clearfix" style="margin: 0 0 5px 0;display:block;">
		<div class="sub-title-view"><?php echo $this->lang->line('config_system_back'); ?></div>
	</div>
	<div class="field_row clearfix" style="margin: 0 0 5px 0;display:block;">
		<div>
			<div class="field_row clearfix;display:block;">	
				<?php  
					if ($this->Employee->isAdmin()){
						echo anchor("backup/index/width:350/height:180",
						"<div class='big_button' style='padding: 8px 25px;margin-right: 10px;'><span>".$this->lang->line('config_backup')."</span></div>",
						array('class'=>'thickbox none','title'=>$this->lang->line('config_backup'))); 				
						echo anchor("backup/recover/width:400/height:400",
						"<div class='big_button' style='padding: 8px 25px;'><span>".$this->lang->line('config_recover')."</span></div>",
						array('class'=>'thickbox none','title'=>$this->lang->line('config_recover'))); 
					}
				?>
			</div>
		</div>
	</div>
</div>

<div class="field_row clearfix" style="display:block;color: #FF0000; font-size: 11px">
	The <strong>red</strong> field are required
</div>

<div class="field_row clearfix" style="display:block;">
	<?php
		if ($this->Employee->has_privilege('save', 'config')){ 
			echo form_submit(
				array(
					'name'=>'submitC',
					'id'=>'submitC',
					'value'=>$this->lang->line('common_submit'),
					'class'=>'big_button float_left'
				)
			);
		}
	?>
</div>
<?php echo form_close(); ?>

<!-- here -->



<div id="feedback_bar"></div>
<script type='text/javascript'>
//bloqueo de inputs en caso de que no pueda editar
if ( $('#submitC').length < 1) {
	$('input, textarea, select').attr('disabled', 'disabled');
};
// $('#reestablecer').click(function(event){
// 	alert('aqui');
// 	// $.ajax({
// 	// 	type:"POST",
// 	// 	url:"controlllers/index.php/cofig/restablecer",
// 	// 	dataType:"json",
// 	// 	success:function(data){

// 	// 		// if(data==1)
// 	// 		// 	$.ajax({url:'views/tags/update.view.php?asyn&tag='+tag,success:function(){
// 	// 		// 		$("#previewTag").dialog("close");
// 	// 		// 		$('body #previewTag').remove();
// 	// 		// 		document.location.hash='update';
// 	// 		// 	}});
// 	// 		// else
// 	// 		// 	message("messages","Error",valores[1]);
// 	// 	}
// 	// });
// });

//validation and submit handling
$(document).ready(function()
{
	$('#config_form').validate({
		submitHandler:function(form)
		{ //console.log('file: '+$('#logo').val()+' hidden: '+$('#logo_name').val());
		var pass = '0';
			if ($('#logo').val()) {
				extensiones_permitidas = new Array(".gif", ".jpg", ".png"); 
				
				//recupero la extensión de este nombre de archivo 
			    extension = ($('#logo').val().substring($('#logo').val().lastIndexOf("."))).toLowerCase(); 
			    //compruebo si la extensión está entre las permitidas 
			    permitida = false; 
			    for (var i = 0; i < extensiones_permitidas.length; i++) { 
			       if (extensiones_permitidas[i] == extension) { 
			       permitida = true; 
			       break; 
			       } 
			    }
			    if (!permitida) { 
			    	console.log('no permitido');
			    	pass = '0';
			    }else{
			    	console.log('permitido');
			    	pass = '1';
			    }
			}else
				pass = '1';

			if (pass=='1'){
				$(form).ajaxSubmit({
					success:function(response)
					{ console.log(response);
						if(response.success)
						{
							set_feedback(response.message,'success_message',false);
							(response.Upstatus==1)?location.reload():'';
									
						}
						else
						{
							set_feedback(response.message,'error_message',true);		
						}
					},
					error:function(response){console.log(response);},
					dataType:'json'
				});
			}else{
				alert('<?=$this->lang->line('common_image_faild')?>'); 
			};

			

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules: 
		{
			company: "required",
			address: "required",
    		phone: "required",
    		default_tax_1_name: "required",
    		default_tax_1_rate:
    		{
    			required:true,
    			number:true
    		},
    		default_tax_2_rate:
    		{
    			number:true
    		},
    		email:"email",
    		return_policy: "required"    		
   		},
		messages: 
		{
     		company: "<?php echo $this->lang->line('config_company_required'); ?>",
     		address: "<?php echo $this->lang->line('config_address_required'); ?>",
     		phone: "<?php echo $this->lang->line('config_phone_required'); ?>",
     		default_tax_1_name: "<?php echo $this->lang->line('config_default_tax_rate_1'); ?> is a required field",
     		default_tax_rate:
    		{
    			required:"<?php echo $this->lang->line('config_default_tax_rate_required'); ?>",
    			number:"<?php echo $this->lang->line('config_default_tax_rate_number'); ?>"
    		},
     		email: "<?php echo $this->lang->line('common_email_invalid_format'); ?>",
     		return_policy:"<?php echo $this->lang->line('config_return_policy_required'); ?>"
	
		}
	});
});
</script>
<?php $this->load->view("partial/footer"); ?>
