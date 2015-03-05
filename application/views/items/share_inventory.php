<?php $this->load->view("partial/header"); ?>
<?php print_r($session).' Cantidad de elementos:'.count($session); ?>
<div id="page_title" style="margin-bottom:8px;"><?php echo $title ?></div>
<div class="page_subtitle" style="margin-bottom:8px;"><?php echo $subtitle ?></div>
<?php echo form_open(site_url("$controller_name/search"),array('id'=>'search_form', 'method'=>'GET')); ?>
	<label for="search"><?php echo $this->lang->line('items_share_search'); ?></label>
	<input type="text" name ='search' id='search' placeholder="<?php echo $this->lang->line('items_share_search_name'); ?>"/>
<?php echo form_close() ?>
<?=form_open(site_url("$controller_name/save_dispatch"), array('id'=>'frmShareIvn', 'method'=>'GET'))?>
	<?php
	include('application/config/database.php'); //Incluyo donde estaran todas las config de las databses
	$dbs = array('...'=>'...');
	foreach ($db as $key => $value){
		if ( $key != $_SESSION['dblocation'] ) {
			$dbs[$key] = ucwords($key);
		}
	}
	$options = 'id="dbselected"';
	?>
	<div class="location-option">
	<?php echo form_label('Receiving Location:', 'dbselected'); ?>
	<?php echo form_dropdown('dbselected', $dbs, '...', $options); ?>
	<h6 class="wire" style="display:inline-block"><?php echo $this->lang->line('items_share_sendto'); ?></h6>
	</div>
	<div class="products-to-send">
		<table class="tablesorter report share-inventorie-report" id="sortable_table">
	        <thead>
	            <tr>
	                <?php foreach ($headers as $header) { ?>
	                <th><?php echo $header; ?></th>
	                <?php } ?>
	            </tr>
	        </thead>
	        <tbody>
	            <tr>
	            	<td colspan="5" class="td-info"><?php echo $this->lang->line('items_share_tex'); ?></td>
	            </tr>
	        </tbody>
	    </table>
	    <div class="delete-options">
			<!-- <a href="#" id="deselectall">Delete All Items Selected</a> -->
			<div class="big_button" style="float: left;" id="deselectall"><span><?php echo $this->lang->line('items_share_delete'); ?></span></div>
		</div>
	    <a class="linkPrint" href="#" id="btnSubmit">
	        <div class="big_button" style="float: left;"><span><?php echo $this->lang->line('items_share_send'); ?></span></div>
	    </a>
	</div>
<?=form_close()?>
<?php
$this->load->view("partial/footer");
?>
<script>
	var default_table_row = '<tr><td colspan="5" class="td-info">'.$this->lang->line('items_share_tex');.'</td></tr>';

	enable_search_form('<?php echo site_url("$controller_name/suggest")?>','<?php echo $this->lang->line("common_confirm_search")?>', false);
	
	$$('.cb').each(function(index, el) {
		var id = this.id;
		if ($$(this).is(':not:checked')) {
			$$('#amount'+id).attr('disabled', 'disabled');
		}else{
			$$('#amount'+id).attr('disabled', 'false');
		}

	});

	$$('#btnSubmit').click(function(){
		var dbselected = document.getElementById('dbselected').selectedIndex;
		// console.log('Base de datos seleccionada'+dbselected);
		if (dbselected > 0) {
			var items = $$('tbody tr input').length;
			if (items > 0) {
				// $$('#frmShareIvn').submit();	
				var form = $$('#frmShareIvn');

				$$.ajax({
					url: form.attr('action'),
					type: form.attr('method'),
					data: form.serialize(),
					dataType: 'json',
					success: function(data){
						if (data.id) {
							notif({
							    type: "success",
							    msg: data.msg,
							    width: "all",
							    height: 100,
							    position: "center"
							});
							window.location = 'index.php/share_inventories/dispatch_details/'+data.id;
						};
					}
				});
			}else{
				notif({
				    type: "warning",
				    msg: "<?php echo $this->lang->line('items_share_tex_error') ?>",
				    width: "all",
				    height: 100,
				    position: "center"
				});
			}
		}else{
			// alert('You must select a database');
			notif({
			  type: "error",
			  msg: "<?php echo $this->lang->line('items_share_tex_error_data'); ?>",
			  width: "all",
			  height: 100,
			  position: "center"
			});
		}

		return false;
	});
	$$('#sortable_table tbody').on('click','input.cb',function(){
		if (!$$(this).is(':checked')) {
			var id = $$(this).parents('tr').find('td:first-child').html();
			$$(this).parents('tr').remove();
			$$.get('index.php/<?php echo $controller_name ?>/delete_suggest', {'id': id});
		}
		
		var items = $$('#sortable_table tbody tr').length;
		if(items<=0){
			$$('#sortable_table tbody').html(default_table_row);
		}
	});
	$$('#deselectall').click(function(){
		$$('#sortable_table tbody tr').remove();
		$$('#sortable_table tbody').html(default_table_row);
		$.get('index.php/<?php echo $controller_name ?>/delete_suggest');
		return false
	});
</script>