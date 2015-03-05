<div style="text-align:center;">
	<div id="receipt_header">
		<div id="page_title" style="margin-bottom:6px;text-align:center;"><?php echo $this->lang->line('items_details_count'); ?></div>
		<div class="page_subtitle" style="margin-bottom:6px;"><?="Location: $location"?></div>
	</div>
</div>
<div>
	<div id="table_holder">
		<table class="tablesorter report">
			<thead>
				<tr>
					<th align="center" style="padding: 5px;border-top-left-radius:5px;-webkit-border-top-left-radius:5px;">
					<?php echo $this->lang->line('items_basic_information'); ?></th>
					<th colspan="2" style="padding: 5px;width:240px;">
					<?php echo $this->lang->line('giftcards_barcodes'); ?></th>
					<th colspan="2" style="padding: 5px;border-top-right-radius:5px;-webkit-border-top-right-radius:5px;width:150px;">
					<?php echo $this->lang->line('items_pictures'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php $num=count($items_info); $url=str_replace('/index.php', "",site_url());
					for ($i=0; $i < $num; $i++) {  
						$imagen='<img src="'.$url.'/images/no_image2.png" width="120"/>'; 
						$tuId=md5($location.'-'.$items_info[$i]['item_id']); 
						for ($j=0; $j < 5; $j++) { 
							if (file_exists('./images/items/'.$tuId.'/'.$tuId.'_'.$j.'.jpg')){
										$imagen= '<img src="'.$url.'/images/items/'.$tuId.'/'.$tuId.'_'.$j.'.jpg" width="120"/>';
										// $imagen= '<div class="photo_add" style="border:1px transparent solid;">
										// 			<div style="background-image:url(\'./images/items/'.$tuId.'/'.$tuId.'_'.$j.'.jpg\')">
										// 			</div>
										// 	  </div>';
									  	break;
							}
						} 
						echo '<tr>
						<td>
							<table width="420">
								<tbody>
									<tr>
										<th width="20%">'.$this->lang->line('items_item_number').':</td>
										<td width="30%">'.$items_info[$i]['item_number'].'</td>
										<th width="20%">'.$this->lang->line('items_cost_price').':</td>
										<td width="30%">'.$items_info[$i]['cost_price'].'</td>
									</tr>
									<tr>
										<th width="20%">'.$this->lang->line('items_name').':</td>
										<td width="30%">'.$items_info[$i]['name'].'</td>
										<th width="20%">'.$this->lang->line('items_unit_price').':</td>
										<td width="30%">'.$items_info[$i]['unit_price'].'</td>
									</tr>
									<tr>
										<th width="20%">'.$this->lang->line('items_category').':</td>
										<td width="30%">'.$items_info[$i]['category'].'</td>
										<th width="20%">'.$this->lang->line('items_current_quantity').':</td>
										<td width="30%">'.($items_info[$i]['is_service']?'unlimited':number_format($items_info[$i]['quantity'])).'</td>
									</tr>
									<tr>
										<th width="20%">'.$this->lang->line('items_reorder_level').':</td>
										<td width="30%">'.$items_info[$i]['reorder_level'].'</td>
										<th width="20%">'.$this->lang->line('items_description').':</td>
										<td width="30%">'.($items_info[$i]['description']?$items_info[$i]['description']:'"not provided"').'</td>
									</tr>
								</tbody>
							</table>							
						</td>
						<td colspan="3" style="width:240px;"><img style="margin-top:10px;" src="'.site_url().'/barcode?barcode='.$items_info[$i]['item_id'].'&text='.$items_info[$i]['name'].'&width=200" /></td>
						<td colspan="2" style="width:150px;">'.$imagen.'</td>
					</tr>';
					}
				?>

			</tbody>
		</table>
	</div>	
</div>