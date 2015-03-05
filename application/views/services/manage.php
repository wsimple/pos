<?=$this->load->view("partial/header")?>
<div id="title_bar">
	<div id="title" class="float_left"><?=$this->lang->line('common_list_of').' '.$this->lang->line('module_'.$controller_name)?></div>
	<div id="new_button">
		<?php
			echo anchor("$controller_name/view/-1/width:850/height:465",
			"<span>".$this->lang->line($controller_name.'_new')."</span>",
			array('class'=>'big_button thickbox','title'=>$this->lang->line($controller_name.'_new')));
		?>
	</div>
</div>

<div id="titleTextImg" class="middle-gray-bar">
	<div style="float:left;"><?php echo $this->lang->line('search_options') ?> :</div>
		<div id="search_filter_section" style="text-align: right; font-weight: bold;  font-size: 12px; ">
		<?php 	 echo form_open("$controller_name/index",array('id'=>'items_filter_form')); 
				 echo form_label($this->lang->line('services_all').' '.': ', 'filter_all');
				 echo form_radio(array('name'=>'filters','id'=>'filter_all','value'=>0,'checked'=>isset($filter_all)?  ( ($filter_all)? 1 : 0) : 1)).' | ';
				 echo form_label($this->lang->line('services_today').' '.': ', 'filter_today');
				 echo form_radio(array('name'=>'filters','id'=>'filter_today','value'=>1,'checked'=>isset($filter_today)?  ( ($filter_today)? 1 : 0) : 0)).' | ';				 
				 echo form_label($this->lang->line('services_yesterday').' '.': ', 'filter_yesterday');
				 echo form_radio(array('name'=>'filters','id'=>'filter_yesterday','value'=>2,'checked'=>isset($filter_yesterday)?  ( ($filter_yesterday)? 1 : 0) : 0)).' | ';
				 echo form_label($this->lang->line('services_lastweek').' '.': ', 'filter_lastweek');
				 echo form_radio(array('name'=>'filters','id'=>'filter_lastweek','value'=>3,'checked'=>isset($filter_lastweek)?  ( ($filter_lastweek)? 1 : 0) : 0)).' | ';
				 $options = array('-1'=>'',
				 				  "1"=>$this->lang->line('services_status_1'),
								  "2"=>$this->lang->line('services_status_2'),
								  "3"=>$this->lang->line('services_status_3'),
								  "4"=>$this->lang->line('services_status_4'),
								  "100"=>$this->lang->line('services_status_100'));

				 echo form_label($this->lang->line('services_status').' '.': ', 'filter_status');
				 echo form_dropdown('filter_status', $options, isset($filter_status)?$filter_status:'',"id='filter_status'");
			     echo form_close(); 
		?>
	</div>
</div>

<div style="padding:3px;margin:3px 0;"> <?=$this->pagination->create_links()?> </div>
<div id="table_action_header" style="background-image:none;">
	<ul>
		<li class="float_left">
		<?php if($this->Employee->has_privilege('delete', $controller_name)&&false):  ?>
		<li class="float_left">
			<?php echo anchor('#', $this->lang->line("common_delete"), 'id="delete" class="small_button"'); ?>
		</li>
		<?php endif ?>
		</li>
		<li class="float_right">
		<?=form_open("$controller_name/search",array('id'=>'search_form'))?>
			<input type="text" name='search' id='search' style="width:400px;"/>
			<input type="hidden" name='term' id='term' value=""/>
		</form>
		</li>
	</ul>
</div>
<div id="table_holder">
<?php // echo form_open("$controller_name/delete", array('id'=>'delete-form')); ?>
<?=$manage_table?>
<?php //echo form_close(); ?>
</div>
<div id="feedback_bar"></div>
<?php $this->load->view("partial/footer"); ?>
<script type="text/javascript">
(function($){
	var count=0;
	$('.tablesorter').off('.tslocked').on('change.tslocked','input:checkbox.locked',function(){
		if(this.checked) count++; else count--;
		$('#delete').attr('title',count>0?"<?=$this->lang->line('services_is_locked_alert')?>":null).prop('disabled',count>0);
	});

	$("#filter_all,#filter_today,#filter_yesterday,#filter_lastweek").click(function(){
			$('#items_filter_form').submit();
	});
	$("#filter_status").change(function(){
			$('#items_filter_form').submit();
	});

	$('#search').select2({
		placeholder:'Owner, Phone Number, Brand, Model',
		minimumInputLength:1,
		openOnEnter:false,
		ajax:{
			url:'index.php/services/suggest2',
			data:function(term,page){ return { term: term }; },
			results:function(data,page){ return { results: data };}
		}
	}).change(function(val, added, removed){
		console.log(val);
		if (val.added) {
			$('#term').val(val.added.term);
			console.log($('#term').val());
			$('#search_form').submit();
			//tb_show('<?="<span>".$this->lang->line($controller_name.'_update')."</span>"?>','index.php/<?=$controller_name?>/view/'+val.val+'/width:660/height:465');
		}
	});

<?php if (isset($_POST['search'])&&$_POST['search']!='') { ?>
	$( "#sortable_table tbody tr" ).first().find("td").css( "background-color", "#e1ffdd" );
<?php } ?>

})(jQueryNew);

$(function(){
	init_table_sorting();
	enable_select_all();
	enable_search('<?=site_url("$controller_name/suggest")?>','<?=$this->lang->line("common_confirm_search")?>')
	$('#delete').click(function(e) {
		e.preventDefault();
		$('#delete-form').submit();
		return false;
	});
});

function init_table_sorting(){
	//Only init if there is more than one row
	if($('.tablesorter tbody tr').length >1)
	{
		$("#sortable_table").tablesorter({
			//sortList:[[1,0]],
			headers:{
				
				9:{sorter:false}
			}

		});
	}
}
</script>