<?php $this->load->view("partial/header"); ?>
<div id="title_bar">
	<div id="title" class="float_left"><?php echo $this->lang->line('common_list_of').' '.$this->lang->line('module_'.$controller_name); ?></div>
	<div id="new_button">
		<?php 
		if ($controller_name =='employees') {
				if ($this->Employee->isAdmin()){ ?>
			 		<div class='big_button' style='margin: 0px 5px;float:left;'>
			 			<span><a href="<?php  echo site_url($controller_name."/profile_employee"); ?>" style="text-decoration: none;color: hsl(208, 45%, 41%);" >
			 			<?php echo $this->lang->line("employees_profile_per");?></a></span>
		 			</div>
		<?php 	}
		
			if($this->Employee->has_privilege('add', $controller_name)){
				echo anchor("$controller_name/view/-1/width:$form_width",
				"<div class='big_button' style='float: left;'><span>".$this->lang->line($controller_name.'_new')."</span></div>",
				array('class'=>'thickbox none','title'=>$this->lang->line($controller_name.'_new')));
			}
		}elseif ($controller_name =='customers') {
			if($this->Employee->has_privilege('add', $controller_name)){
				echo anchor("$controller_name/excel_import/width:450/height:165",
				"<div class='big_button' style='float: left; margin:0 5px 0'><span>".$this->lang->line('customers_excel')."</span></div>",
				array('class'=>'thickbox none','title'=>$this->lang->line('customers_excel_title')));
				echo anchor("$controller_name/view/-1/width:600/height:420",
				"<div class='big_button' style='float: left;'><span>".$this->lang->line($controller_name.'_new')."</span></div>",
				array('class'=>'thickbox none','title'=>$this->lang->line($controller_name.'_new')));
			}
		} ?>
	</div>
</div>
<?php echo $this->pagination->create_links();?>
<div id="table_action_header">
	<ul>
		<?php if($this->Employee->has_privilege('delete', $controller_name)):  ?>
		<li class="float_left"><span><?php echo anchor("$controller_name/delete",$this->lang->line("common_delete"),array('id'=>'delete')); ?></span></li>
		<?php endif; ?>
		<li class="float_left"><span><a href="javascript:void(0)" id="email"><?php echo $this->lang->line("common_email");?></a></span></li>
		<li class="float_right">
		<img src='<?php echo base_url()?>images/spinner_small.gif' alt='spinner' id='spinner' />
		<?php echo form_open("$controller_name/search",array('id'=>'search_form')); ?>
		<input type="text" name ='search' id='search' placeholder="Search ..."  style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; border: 1px solid #CCC " />
		</form>
		</li>
	</ul>
</div>
<div id="table_holder">
<?php echo $manage_table; ?>
</div>
<div id="feedback_bar"></div>
<script type="text/javascript">
$(document).ready(function() 
{ 
    init_table_sorting();
    enable_select_all();
    enable_row_selection();
    enable_search('<?php echo site_url("$controller_name/suggest")?>','<?php echo $this->lang->line("common_confirm_search")?>');
    enable_email('<?php echo site_url("$controller_name/mailto")?>');
    enable_delete('<?php echo $this->lang->line($controller_name."_confirm_delete")?>','<?php echo $this->lang->line($controller_name."_none_selected")?>');
}); 

function init_table_sorting()
{
	//Only init if there is more than one row
	if($('.tablesorter tbody tr').length >1)
	{
		$("#sortable_table").tablesorter(
		{ 
			sortList: [[1,0]], 
			headers: 
			{ 
				0: { sorter: false}, 
				5: { sorter: false} 
			} 

		}); 
	}
}

function post_person_form_submit(response)
{	console.log(response);
	if(!response.success)
	{
		set_feedback(response.message,'error_message',true);
	}
	else
	{	window.location.reload();
		//This is an update, just update one row
		//if(jQuery.inArray(response.person_id,get_visible_checkbox_ids()) != -1)
		//{	
		//	update_row(response.person_id,'<?php echo site_url("$controller_name/get_row")?>');
		//	set_feedback(response.message,'success_message',false);	
		//	
		//}
		//else //refresh entire table	
		//{   
			//var attr=$('#menu_changeuser ul li a').attr('href').split('cajas/index/');
			//$('#menu_changeuser ul').append('<li ><a href="'+attr[0]+'cajas/index/'+response.person_id+'" rel="#logout_overlay">'+response.name_employe+'</a></li>');
			//window.location.reload();  
			//do_search(true,function()
		//	{
				//highlight new row
		//		hightlight_row(response.person_id);
		//		set_feedback(response.message,'success_message',false);		
		//	});
		//}
	}
}
</script>
<?php $this->load->view("partial/footer"); ?>