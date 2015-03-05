<?php echo form_open_multipart('employees/save/'.($person_info->person_id?$person_info->person_id:'-1'),array('id'=>'employee_form')); ?>
<div>
	<h3 class="user-icon"><?=$this->lang->line('customers_h3_contact_info')?></h3>
	<?php $this->load->view("people/form_basic_info"); ?>
	
	<div class="clearfix my_height"></div>
	<h3 class="camera-icon"><?=$this->lang->line('employees_h3_photo')?></h3>

	<div class="field_row clearfix">
		<div class="field_row clearfix">
			<?php echo form_label($this->lang->line('employees_photo').':', 'photo_label',array('class'=>'lable-form','style'=>'float:none;','style'=>'float:none;')); ?>
		</div>
		<?php 
		$tuId=md5($this->session->userdata('dblocation').'+'.($person_info->person_id?$person_info->person_id:''));
		if ($person_info->person_id && file_exists('./images/employees/'.$tuId.'.jpg')){ ?>
		<div id="fileb" style="margin-bottom: 10px; background-repeat:no-repeat; width: 180px; height: 140px; background-image: url('./images/employees/<?php echo $tuId; ?>.jpg')">
		</div>
		<?php 
			$displayNonePhoto='style="display:none;"';
			echo form_button(
				array(
					'name'=>'change',
					'id'=>'change',
					'value'=>'change',
					'content' => $this->lang->line('employees_photo_change'),
					'class'=>'big_button',
					'style'=>'display: inline-block; margin: 0 20px;'
				)
			);
		}else $displayNonePhoto="style='display:block;'"; ?>
		<div id="filel" class="field_row clearfix" <?php echo $displayNonePhoto; ?>> 
			<input type="radio" name="photop" value="0" checked="checked">	
			<?php echo form_label($this->lang->line('config_recover_fe'), 'photo_label',array('class'=>'lable-form','style'=>'float:none;')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="radio" value="1" name="photop" >	
			<?php echo form_label($this->lang->line('employees_photo_g'), 'photo_label',array('class'=>'lable-form','style'=>'float:none;')); ?>
		</div>
		<div id="filee" class="field_row clearfix" <?php echo $displayNonePhoto; ?>>
			<div class='form_field' style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; ">
				<input type="file" name="photo_e" id="photo_e">
				<div class="upload_label"><?php echo $this->lang->line('common_logo_dimensiones');?></div>
			</div>	
		</div>
		<div id="filei" class="field_row clearfix" style="display: none;">
			<video id="video" width="180" height="140" autoplay></video>
			<?php
			echo form_button(
					array(
						'name'=>'snap',
						'id'=>'snap',
						'value'=>'snap',
						'content' => $this->lang->line('employees_photo_capture'),
						'class'=>'big_button',
						'style'=>'display: inline-block; margin: 0 20px;'
					)
				);
			?>
			<canvas id="canvas" width="180" height="140"></canvas>
			<input type="hidden" name="protocapture" id="protocapture"/>
		</div>
	</div>
</div>
<div>
	<div class="clearfix my_height"></div>
	<h3 class="lock-icon"><?php echo $this->lang->line("employees_login_info"); ?></h3>
	<div class="field_row clearfix">
		<div style="width: 180px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('employees_username').':', 'username',array('class'=>'lable-form-required')); ?>
				<div >
				<?php 
					if($person_info->username){ $disabled="disabled"; ?>
					<input type="hidden" name="nameuser" value="<?php echo $person_info->username; ?>">
				<?php
					}else $disabled="";
					echo form_input(array(
					'name'=>'username',
					'id'=>'username',
					'value'=>$person_info->username,$disabled=>$disabled,
					'class'=>'text_box'
				));
				?>
				</div>
			</div>
		</div>
		<div style="width: 180px; float: left">
			<div class="field_row clearfix">
				<?php $password_label_attributes = $person_info->person_id == "" ? array('class'=>'lable-form-required'):array('class'=>'lable-form');
				echo form_label($this->lang->line('employees_password').':', 'password',$password_label_attributes); ?>
				<div>
				<?php echo form_password(array(
					'name'=>'password',
					'id'=>'password',
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 180px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('employees_repeat_password').':', 'repeat_password',$password_label_attributes); ?>
				<div >
				<?php echo form_password(array(
					'name'=>'repeat_password',
					'id'=>'repeat_password',
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="employee_permission_info">
	<div class="clearfix my_height"></div>
	<h3 class="access-icon"><?php echo $this->lang->line("employees_permission_info"); ?></h3>
	<p><?php echo $this->lang->line("employees_permission_desc"); ?></p>
	<select id="employee_profile_type">
		<option value=""><?php echo $this->lang->line('employees_profi'); ?>...</option>
	</select>
	<input type="hidden" name="employee_profile_type" value="<?=$person_info->type_employees?>" />
	<div id="radio" style="margin-top: 10px;"><input type="checkbox"  style="margin-right: 10px;"><?php echo $this->lang->line('employees_see'); ?></div>
	<ul id="permission_list" style="display: none;">
		<?php foreach($all_modules->result() as $module) { 
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
						'checked'=>$this->Employee->has_permission($module->module_id,$person_info->person_id)
					);
					echo form_checkbox($attribs);
				?>
				<span><?php echo $this->lang->line('employees_profile_see'); ?></span>	
				</li>
				<?php 
				foreach ($subpermissions as $subpermission) {
					if ($subpermission != 'none' || $subpermission == '') {
						$attribs = array(
							'id'=>$module->module_id.'-'.str_replace(" ","_",$subpermission),
							'name'=>$module->module_id.'[]',
							'value'=>$subpermission,
							'class'=>$module->module_id.'-option',
							'checked'=>$this->Employee->has_privilege_permi($module->module_id,$person_info->person_id,$subpermission));
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
<?php $days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'); ?>
<table id="employee_schedule">
	<tr>
		<?php foreach($days as $day) echo '<th>'.$day.'</th>'; ?>
		<th></th>
	</tr>
	<tr>
		<?php foreach($days as $day){
		echo '<td class="checkDays">'.
		form_checkbox(strtolower($day), $day, $this->Schedule->workable_day($day, $person_info->person_id))
		.'</td>';
		} ?>
		<td class="checkDays">Days</td>
	</tr>
	<tr>
		<?php
			$horas = array();
			for ($i=0; $i < 23; $i++) { $horas[] = $i; }
			foreach($days as $day){
				$select=$this->Schedule->workable_day_hour($day, 'in', $person_info->person_id);
				$disabled=$select===false?'disabled="disabled"':'';
				echo '<td>'.form_dropdown('in'.$day, $horas,$select, 'id="in'.strtolower($day).'" '.$disabled).'</td>';
			} 
		?>
		<td>In</td>
	</tr>
	<tr>
		<?php
			unset($horas[0]);
			$horas[] = 23;
			foreach($days as $day){
				$select=$this->Schedule->workable_day_hour($day, 'out', $person_info->person_id);
				$disabled=$select===false?'disabled="disabled"':'';
				echo '<td>'.form_dropdown('out'.$day, $horas,$select, 'id="out'.strtolower($day).'" '.$disabled).'</td>';
			} 
		?>
		<td>Out</td>
	</tr>
</table>
<div class="clearfix my_height"></div>
<div class="field_row clearfix requested">
	<?=$this->lang->line('common_fields_required_message')?>
</div>
<ul id="error_message_box"></ul>
<?php
echo form_submit(array(
	'name'=>'sendto',
	'id'=>'sendto',	
	'value'=>$this->lang->line('common_submit'),
	'class'=>'small_button float_right')
);
echo form_close(); ?>
<?php 
	$retypes=''; 
	foreach ($module_profiles as $key) {
		$key['module']=str_replace(" ","_",$key['module']);
		$retypes.=implode("|",$key).']';
	}
?>
<script type='text/javascript'>
$(document).ready(function(){
	var canvas=document.getElementById('canvas'),video=document.getElementById('video'),localW=null;
		context = canvas.getContext("2d"),videoStado=false,
		videoObj = {video: true, audio: false},
		errBack = function(error) {
			console.log("Video capture error: ", error.code); 
		};
	var type=[],htype=[],select='<?=$person_info->type_employees?>',options='',retypes='<?=$retypes?>',urlImg="";
	var retypes2=retypes.split(']');
	for (var i=0;i<retypes2.length;i++){
		var types=retypes2[i].split('|');
		if (types[1]){ 
			options+='<option value="'+i+'" '+(select==types[0].toLowerCase()?'selected="selected"':'')+'>'+types[0]+'</option>';
			type[i]=types[1];
			htype[i]=types[0].toLowerCase();
		}
	}
	$('#employee_profile_type').append(options).change(function(event){
		if(!type[this.value]) return;
		$('ul#permission_list :checkbox').removeAttr('checked').filter(type[this.value]).attr('checked','checked');
		$('input[type="hidden"][name="employee_profile_type"]').val(htype[this.value]);
	});

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
	$('#radio input').click(function(event) {
		if($(this).is(':checked')) $('#permission_list').css('display','block');
		else $('#permission_list').css('display','none');
	});

	//Verifica  dia marcado para mostrar el selector de hora
	$('table#employee_schedule input[type=checkbox]').change(function(){
		var id = $(this).attr('name');

		if( $(this).is(':checked') ){
			$('select#in'+id).removeAttr('disabled');

			// $('select#out'+id).html('').append('<option value="1">1</option>');
			$('select#out'+id).removeAttr('disabled');
		}else{
			$('select#in'+id).attr('disabled', 'disabled').selectedIndex = 0;
			$('select#out'+id).attr('disabled', 'disabled').val('0');
		}
	});
	$('#change').click(function() {
		$('#filel,#filee').css('display','block');
		$('#fileb').css('display','none');
		$(this).css('display','none');
	});
	//Verifica la hora de entrada para liberar la hora de salida y valida
	$('select[id^="in"]').change(function(){
		var id = $(this).attr('id'),val = $(this).val();
		id = id.slice(2, id.length);
		val = parseInt(val);
		// console.log(val);
		val2 = parseInt( $('select#out'+id).val() );

		$(id).attr('checked', 'checked');

		//Elimina items de hora final y agrega nuevos de acuerdo a la hora de entrada seleccionada
		$('select#out'+id).html('');
		for (var i = val+1; i < 24; i++) {
			$('select#out'+id).append('<option value="'+i+'">'+i+'</option>');
		};

		if(val < val2) $('select#out'+id).val(val2); //Validacion hora de salida de acuerdo a la de entrada
		// $('select#out'+id).removeAttr('disabled');
	});
	
	$('input[type="radio"][name="photop"]').change(function(e) {
		if ($(this).val()!=0){
			if (conectar()){
				$('#filee').css('display', 'none');
				$('#filei').css('display', 'block');
				$("#snap").click(function() {
					context.drawImage(video, 0, 0, 180, 140);
					urlImg=canvas.toDataURL("image/png");
				});
			}
		}else{
			$('#filee').css('display', 'block');
			$('#filei').css('display', 'none');
			desconecta();
			$('#protocapture').val("");
		}
	});

	function hasGetUserMedia() {
        navigator.getUserMedia = navigator.getUserMedia ||
            navigator.mozGetUserMedia ||
            navigator.webkitGetUserMedia ||
            navigator.msGetUserMedia;
        if (navigator.getUserMedia) {return true;} else {return false;}
    }
    function hasURL() {
        window.URL = window.URL || window.webkitURL || window.mozURL || window.msURL;
        if (window.URL && window.URL.createObjectURL) {return true;} else {return false;}
    }
    function setStream(stream) {
        video.src = window.URL.createObjectURL(stream);
        video.play();
        videoStado=true;
        localW=stream;
    }
    function conectar() {
        if (!hasGetUserMedia() || !hasURL()) { 
        	alert("<?php echo $this->lang->line('employees_photo_capture_no_support')?>"); 
        	$('input[type="radio"][name="photop"][value="0"]').attr('checked','checked');
        	return false;
        }
        navigator.getUserMedia( videoObj, setStream, errBack );
        return true;
    }
    function desconecta(){
    	canvas.width = canvas.width;
		//context.clearRect(0, 0, canvas.width, canvas.height);
		urlImg="";
    	video.pause();	
    	// video.stop();
		video.mozSrcObject=null;
		localW.stop();
		video.src = "";
		videoStado=false;
    }
    $('#TB_overlay,#TB_closeAjaxWindow').click(function(event) {
    	if (videoStado) desconecta();
    });

	$('#employee_form').validate({
		submitHandler:function(form)
		{	var pass=false;
			if ($('input[type="radio"][name="photop"]:checked').val()!=0){
				if (urlImg!=""){
					pass=true;
					$('#protocapture').val(urlImg);
				}else pass=false;
			}else{
				$('#protocapture').val("");
				if ($('#photo_e').val()) {
					extensiones_permitidas = new Array(".gif", ".jpg", ".png"); 
					//recupero la extensión de este nombre de archivo 
				    extension = ($('#photo_e').val().substring($('#photo_e').val().lastIndexOf("."))).toLowerCase(); 
				    //compruebo si la extensión está entre las permitidas 
				    pass = false; 
				    for (var i = 0; i < extensiones_permitidas.length; i++) { 
				       if (extensiones_permitidas[i] == extension) { pass = true; break; } 
				    }
				}else pass = true;
			}
			if (pass){			
				$('#employee_form').ajaxSubmit({
					success:function(response)
					{   if (response.success) tb_remove();
						post_person_form_submit(response);
					},
					error:function(response){ console.log(response); },
					dataType:'json'
				});
			}else{ alert('<?php echo $this->lang->line("common_image_faild"); ?>');  }
			return false;
		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules:
		{
			first_name: {
			    required: true,
			    regex:/^[a-zA-Z\u00C0-\u00FF\s]+$/,
			    minlength: 3
		    },
		    last_name: {
			    required: true,
			     regex:/^[a-zA-Z\u00C0-\u00FF\s]+$/,
			    minlength: 3
		    },
			username:
			{
				required:true,
				minlength: 5
			},

			password:
			{
				<?php if($person_info->person_id == "") { ?>
				required:true,
				<?php } ?>
				minlength: 8
			},
			repeat_password:
			{
 				equalTo: "#password"
			},
    		email: {
			    required: true,
			    email: "email"
		    },
		    employee_profile_type:{ required:true },
    		phone_number:
			{
				required:true,
				number:true
			}
   		},
		messages:
		{
     		first_name: {
			      required: "<?php echo $this->lang->line('common_first_name_required'); ?>",
			      regex:"<?php echo  $this->lang->line('common_first_name_only_char');?>",
			      minlength: jQuery.format("<?php echo $this->lang->line('common_at_least'); ?> {0} <?php echo $this->lang->line('common_at_characters'); ?>!")
    		},
    		last_name: {
			      required: "<?php echo $this->lang->line('common_last_name_required'); ?>",
			      regex:"<?php echo  $this->lang->line('common_first_name_only_char');?>",
			      minlength: jQuery.format("<?php echo $this->lang->line('common_at_least'); ?> {0} <?php echo $this->lang->line('common_at_characters'); ?>!")
    		},
     		username:
     		{
     			required: "<?php echo $this->lang->line('employees_username_required'); ?>",
     			minlength: "<?php echo $this->lang->line('employees_username_minlength'); ?>"
     		},

			password:
			{
				<?php if($person_info->person_id == "") { ?>
				required:"<?php echo $this->lang->line('employees_password_required'); ?>",
				<?php } ?>
				minlength: "<?php echo $this->lang->line('employees_password_minlength'); ?>"
			},
			repeat_password:
			{
				equalTo: "<?php echo $this->lang->line('employees_password_must_match'); ?>"
     		},
     		email: "<?php echo $this->lang->line('common_email_invalid_format'); ?>",
     		employee_profile_type:"Profile type is required",
     		phone_number:"<?php echo $this->lang->line('common_phone_invalid_format');  ?>"
		}
	});
});
</script>
