<?php $this->load->view("partial/header"); ?>
<div id="title_bar">
	<div id="title" class="float_left"><?=$title?></div>
	<?php if ($this->Employee->has_privilege('add', $controller_name)): ?>
		<div id="new_button">
		<?php if ($this->session->userdata('dblocation')=='default') { ?>
			<a href="index.php/<?php echo $controller_name ?>/view/0/width:600/height:300" class="thickbox none" title="<?php echo $this->lang->line('location_new_location') ?>"><div class="big_button" style="float: left;"><span><?php echo $this->lang->line('location_new_location') ?></span></div></a>
		<?php }else{

			echo $this->lang->line('location_wrong_base_location');

			} ?>
		</div>
	<?php endif ?>
</div>
<div id="table_action_header">
	<ul>
	<?php if ($this->Employee->has_privilege('disable', $controller_name)): ?>
		<li class="float_left"><span><a href="index.php/<?php echo $controller_name ?>/enable" id="enable"><?php echo $this->lang->line('location_enable') ?></a></span></li>
		<li class="float_left"><span><a href="index.php/<?php echo $controller_name ?>/disable" id="disable"><?php echo $this->lang->line('location_disable') ?></a></span></li>
	<?php endif ?>
		<li class="float_right">
			<img src="images/spinner_small.gif" alt="spinner" id="spinner">
			<form action="index.php/<?php echo $controller_name ?>/search" method="post" accept-charset="utf-8" id="search_form">
				<input type="text" name="search" id="search" placeholder="<?php echo $this->lang->line('location_search') ?>" style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; border: 1px solid #CCC " autocomplete="off" class="ac_input">
			</form>
		</li>
	</ul>
</div>
<div id="table_holder">
	<form action="index.php/<?php echo $controller_name ?>/enable_disable" method="POST" id="form_delete_location">
	<table class="tablesorter report" id="sortable_table">
		<thead>
			<tr style="color:#FFFFFF;background-color:#396B98;">
				<th colspan="1"><input type="checkbox" id="select_all"></th>
				<th colspan="1"><?php echo $this->lang->line('location_group_connection') ?></th>
				<th colspan="1"><?php echo $this->lang->line('location_host') ?></th>
				<th colspan="1"><?php echo $this->lang->line('location_database') ?></th>
				<th colspan="1"><?php echo $this->lang->line('location_driver') ?></th>
				<th colspan="1"><?php echo $this->lang->line('location_active') ?></th>
				<?php if ($this->Employee->has_privilege('update', $controller_name)): ?>
				<th colspan="1"></th>
				<?php endif ?>
			</tr>
		</thead>
		<tbody>
		<?php if (count($locations) > 0): ?>
			<?php foreach ($locations as $value): ?>
				<tr>
					<td><?php echo form_checkbox('locations[]', $value['id']); ?></td>
					<td><?php echo $value['name'] ?></td>
					<td><?php echo $value['hostname'] ?></td>
					<td><?php echo $value['database'] ?></td>
					<td><?php echo $value['dbdriver'] ?></td>
					<td><?php echo ($value['active']) ? $this->lang->line('location_yes') : $this->lang->line('location_no') ; ?></td>
					<?php if ($this->Employee->has_privilege('update', $controller_name)): ?>
					<td>
						<div class='warning_message' style='padding:7px;'>
						<?php echo anchor('locations/view/'.$value['id'].'/width:600/height:300', 'Edit', 'class="small_button thickbox", title="'.$this->lang->line('location_edit').'"'); ?>
						</div>
					</td>
					<?php endif ?>
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<tr>
				<td colspan="7"><?php echo $this->lang->line('location_no_have_locations') ?></td>
			</tr>
		<?php endif ?>
		</tbody>
	</table>
	</form>
</div>
<div id="feedback_bar"></div>
<script type="text/javascript">
	(function($){
		init_table_sorting();
		enable_select_all();
		enable_search('<?=site_url("locations/suggest")?>','<?=$this->lang->line("location_search")?>');

		$('#enable, #disable').on('click',function(event) {
			var url = $(this).attr('href');
			// console.log($('input:checked').length);
			if($('#sortable_table input:checked').length > 0) {
				$('#form_delete_location').attr('action', url);
				$('#form_delete_location').submit();
			}else{
				notif({
				    type: "error",
				    msg: '<?=$this->lang->line("location_least_one")?>',
				    width: "all",
				    height: 100,
				    position: "center"
				});
			}
			return false;
		});
	})(jQueryNew);

	function init_table_sorting()
	{
		//Only init if there is more than one row
		if($('.tablesorter tbody tr').length >1)
		{
			$("#sortable_table").tablesorter({
				sortList:[[1,0]],
				headers:{
					0:{sorter:false},
					6:{sorter:false}
				}

			});
		}
	}

	function post_item_form_submit(response)
	{
		tb_remove(true);
		set_feedback(response.message,'success_message',false);
	}
</script>
<?php $this->load->view("partial/footer"); ?>
