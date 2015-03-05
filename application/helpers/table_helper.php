<?php
/*
Customers manage table 
*/
function get_customers_manage_table($people,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('common_last_name'),
	$CI->lang->line('common_first_name'),
	$CI->lang->line('common_email'),
	$CI->lang->line('common_phone_number'),
	$CI->lang->line('customers_lbl_td_type'),
	$CI->lang->line('customers_lbl_td_discount'),
	$CI->lang->line('customers_credit_limit'),
	$CI->lang->line('customers_credit_balance'),
	'&nbsp');

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_customer_manage_table_data_rows($people,$controller);
	$table.='</tbody></table>';
	return $table;
}

function get_customer_manage_table_data_rows($people,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($people->result() as $person)
	{
		$table_data_rows.=get_customer_data_row($person,$controller);
	}

	if($people->num_rows()==0)
	{
		$table_data_rows.='<tr><td colspan="6"><div class="warning_message" style="padding:7px;">'.$CI->lang->line('common_no_persons_to_display').'</div></tr></tr>';
	}

	return $table_data_rows;
}

function get_customer_data_row($person,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='4%'><input type='checkbox' id='person_$person->person_id' value='".$person->person_id."'/></td>";
	$table_data_row.='<td width="13%">'.character_limiter($person->last_name,13).'</td>';
	$table_data_row.='<td width="13%">'.character_limiter($person->first_name,13).'</td>';
	$table_data_row.='<td width="27%">'.mailto($person->email,character_limiter($person->email,22)).'</td>';
	$table_data_row.='<td width="18%">'.character_limiter($person->phone_number,13).'</td>';
	$table_data_row.='<td width="12%">'.character_limiter(($person->type==1?$CI->lang->line('customers_cbo_type_company'):$CI->lang->line('customers_cbo_type_regular')),13).'</td>';
	
	$table_data_row.='<td width="13%"><strong>'.(trim($person->discounts)!=''?character_limiter($person->discounts,13).'&nbsp;%':'---').'</strong></td>';

	if ($person->max_amount_credit!='') {
		$amount = explode('/', $person->max_amount_credit);
		switch ($amount[1]) {
			case '30_days':
				$days = '30 days';
				$final_ac = '$'.$amount[0].'<br>'.$days;
			break;
			case '60_days':
				$days = '60 days';
				$final_ac = '$'.$amount[0].'<br>'.$days;
			break;
			case '90_days':
				$days = '90 days';
				$final_ac = '$'.$amount[0].'<br>'.$days;
			break;
			default: $final_ac = '';
		}
	} else {
		$final_ac = $CI->lang->line('customer_none_limitcredit');
	}
	
	$balance = $CI->Customer->balance_total($person->person_id);
	$balance_total = (isset($balance->balance)) ? (($balance->balance!=0)?'$'.number_format($balance->balance):0): '0' ;
	$style = (isset($balance->balance)) ?  (($balance->balance!=0)?'style="color:#FF0000"':'style="color:#000"'): 'style="color:#000"' ;

	$table_data_row.='<td width="22%"> '.$final_ac.'</td>';
	$table_data_row.='<td width="22%" '.$style.' > '.$balance_total.'</td>';

	$lc = $CI->Customer->get_info($person->person_id)->max_amount_credit;
	if ($lc!='') {
		$btnCredit = anchor($controller_name."/account/$person->person_id/pay/width:650/height:450", 'Account',array('class'=>'thickbox small_button','title'=>'Account Balance'));
	} else {
		$btnCredit = '';
	}
	
	


	if($CI->Employee->has_privilege('add', $controller_name)){
		$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$person->person_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox small_button','title'=>$CI->lang->line($controller_name.'_update'))).$btnCredit.'</td>';
	}else{
		$table_data_row.='<td width="5%"></td>';
	}
	$table_data_row.='</tr>';

	return $table_data_row;
}

/* table customers credits  */
function get_customers_credits_manage_table($credi,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('&nbsp',
	$CI->lang->line('customers_payment_type'),
	$CI->lang->line('customers_payment_status'),
	$CI->lang->line('customers_payment_period'),
	$CI->lang->line('customers_pay_day'),
	$CI->lang->line('customers_payment_amonut')
	);

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_customer_credits_manage_table_data_rows($credi,$controller);
	$table.='</tbody></table>';

	$credits = $credi->row();

	if ($credi->num_rows()!=0) {
		// $pays = $CI->Customer->sum_all_pays($credits->person_custo_id);
		// $due = $CI->Customer->sum_all_credits($credits->person_custo_id);
		
		$balance = $CI->Customer->balance_total($credits->person_custo_id);
		$balanceT = $balance->balance;
	} else {
		$balanceT = $credi->num_rows();
	}

	$table.='<div style="width: 645px;font-weight: bold;margin: 10px 0px;font-size: 16px;">
						<div>Total Balance Credit: $'.$balanceT.'</div>
					  </div>';
	return $table;
}

function get_customer_credits_manage_table_data_rows($credi,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($credi->result() as $person)
	{
		$table_data_rows.=get_customer_credits_data_row($person,$controller);
	}

	if($credi->num_rows()==0)
	{
		$table_data_rows.='<tr><td colspan="6"><div class="warning_message" style="padding:7px;">'.$CI->lang->line('customers_no_credit_avaible').'</div></tr></tr>';
	}

	return $table_data_rows;
}

function get_customer_credits_data_row($credi,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width(); 

	$s = explode('/',$credi->status);

	//si viene con plazo de pago es credit sino es un pago
	if ($credi->payment_period=='---') {
		$p = '---';
	} else {
		$p = $credi->payment_period.' '.$CI->lang->line('customers_period_days');
	}
	
	$classamount = ($credi->type=='credit') ? (($s[0]!='paid')?'color:#FF0000':'color:#0CC54F') : 'color:#0CC54F' ;
	$table_data_row='<tr>';
	$table_data_row.="<td width='2%'></td>";
	$table_data_row.='<td width="15%">'.ucwords($credi->type).'</td>';
	$table_data_row.='<td width="15%">'.$s[0].'</td>';
	$table_data_row.='<td width="25%">'.$p.'</td>';
	$table_data_row.='<td width="20%">'.$credi->day_pay.'</td>';
	$table_data_row.='<td  width="25%" style="text-align: right;'.$classamount.'"><div class="set_curret">$'.number_format(str_replace('-','',$credi->payment_amount)).'</div></td>';
	
	return $table_data_row;
}


/*
Gets the html table to manage people.
*/
function get_people_manage_table($people,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('common_last_name'),
	$CI->lang->line('common_first_name'),
	$CI->lang->line('common_email'),
	$CI->lang->line('common_phone_number'),
	'&nbsp');

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_people_manage_table_data_rows($people,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the people.
*/
function get_people_manage_table_data_rows($people,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($people->result() as $person)
	{
		$table_data_rows.=get_person_data_row($person,$controller);
	}

	if($people->num_rows()==0)
	{
		$table_data_rows.='<tr><td colspan="6"><div class="warning_message" style="padding:7px;">'.$CI->lang->line('common_no_persons_to_display').'</div></tr></tr>';
	}

	return $table_data_rows;
}

function get_person_data_row($person,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='5%'><input type='checkbox' id='person_$person->person_id' value='".$person->person_id."'/></td>";
	$table_data_row.='<td width="20%">'.character_limiter($person->last_name,13).'</td>';
	$table_data_row.='<td width="20%">'.character_limiter($person->first_name,13).'</td>';
	$table_data_row.='<td width="30%">'.mailto($person->email,character_limiter($person->email,22)).'</td>';
	$table_data_row.='<td width="20%">'.character_limiter($person->phone_number,13).'</td>';
	if($CI->Employee->has_privilege('add', $controller_name)){
		$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$person->person_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox small_button','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	}else{
		$table_data_row.='<td width="5%"></td>';
	}
	$table_data_row.='</tr>';

	return $table_data_row;
}

/*
Gets the html table to manage suppliers.
*/
function get_supplier_manage_table($suppliers,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('suppliers_company_name'),
	$CI->lang->line('common_last_name'),
	$CI->lang->line('common_first_name'),
	$CI->lang->line('common_email'),
	$CI->lang->line('common_phone_number'),
	'Supplied',
	'Discount',
	'&nbsp');

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_supplier_manage_table_data_rows($suppliers,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the supplier.
*/
function get_supplier_manage_table_data_rows($suppliers,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($suppliers->result() as $supplier)
	{
		$table_data_rows.=get_supplier_data_row($supplier,$controller);
	}

	if($suppliers->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='7'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('common_no_persons_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_supplier_data_row($supplier,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='5%'><input type='checkbox' id='person_$supplier->person_id' value='".$supplier->person_id."'/></td>";
	$table_data_row.='<td width="17%">'.character_limiter($supplier->company_name,13).'</td>';
	$table_data_row.='<td width="17%">'.character_limiter($supplier->last_name,13).'</td>';
	$table_data_row.='<td width="17%">'.character_limiter($supplier->first_name,13).'</td>';
	$table_data_row.='<td width="22%">'.mailto($supplier->email,character_limiter($supplier->email,22)).'</td>';
	$table_data_row.='<td width="17%">'.character_limiter($supplier->phone_number,13).'</td>';
	$table_data_row.='<td width="17%"><strong>'.character_limiter($supplier->product_supplied,13).'</strong></td>';
	$table_data_row.='<td width="17%"><strong>'.(trim($supplier->discounts)!=''?character_limiter($supplier->discounts,13).'&nbsp;%':'---').'</strong></td>';

	if($CI->Employee->has_privilege('update', $controller_name)){
		$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$supplier->person_id/width:570/height:425", $CI->lang->line('common_edit'),array('class'=>'thickbox small_button','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	}else{
		$table_data_row .= '<td width="5%"></td>';
	}
	$table_data_row.='</tr>';

	return $table_data_row;
}

/*
Gets the html table to manage items.
*/
function get_items_manage_table($items,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('items_item_number'),
	$CI->lang->line('items_name'),
	$CI->lang->line('items_category'),
	$CI->lang->line('items_cost_price'),
	$CI->lang->line('items_unit_price'),
	$CI->lang->line('items_tax_percents'),
	$CI->lang->line('items_quantity'),
	'&nbsp;',
	$CI->lang->line('items_inventory'),
	'&nbsp;'
	);

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_items_manage_table_data_rows($items,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the items.
*/
function get_items_manage_table_data_rows($items,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($items->result() as $item)
	{
		$table_data_rows.=get_item_data_row($item,$controller);
	}

	if($items->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('items_no_items_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_item_data_row($item,$controller)
{
	$CI =& get_instance();
	$item_tax_info=$CI->Item_taxes->get_info($item->item_id);
	$tax_percents = '';
	foreach($item_tax_info as $tax_info)
	{
		$tax_percents.=$tax_info['percent']. '%, ';
	}
	$tax_percents=substr($tax_percents, 0, -2);
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%'>".($item->is_locked?'':"<input class='".($item->is_locked?'locked':'')."' type='checkbox' id='item_$item->item_id' value='$item->item_id'/>")."</td>";
	$table_data_row.='<td width="15%">'.$item->item_number.'</td>';
	$table_data_row.='<td width="20%">'.$item->name.'</td>';
	$table_data_row.='<td width="14%">'.$item->category.'</td>';
	$table_data_row.='<td width="14%">'.to_currency($item->cost_price).'</td>';
	$table_data_row.='<td width="14%">'.to_currency($item->unit_price).'</td>';
	$table_data_row.='<td width="14%">'.$tax_percents.'</td>';
	$table_data_row.='<td width="14%">'.($item->is_service?"&#x221E;":number_format($item->quantity)).'</td>';
	if($CI->Employee->has_privilege('update', $controller_name)){
		$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$item->item_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'small_button thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	}else{
		$table_data_row .= '<td width="5%"></td>';
	}
	$table_data_row.='<td width="10%">'.anchor($controller_name."/count_details/$item->item_id/width:$width", $CI->lang->line('common_det'),array('class'=>'small_button thickbox','title'=>$CI->lang->line($controller_name.'_details_count'))).'</td>';//inventory details
	//Ramel Inventory Tracking
	if($item->is_service){
		$table_data_row .= '<td width="10%"></td>';
	}else{
		if ($CI->Employee->isAdmin()){
			// $table_data_row.= '<td width="10%">'.anchor("backup/index/width:350/height:180",
			// "<div class='big_button' style='padding: 8px 25px;margin-right: 10px;'><span>".$CI->lang->line('config_backup')."</span></div>",
			// array('class'=>'thickbox small_button','title'=>$CI->lang->line('config_backup'))).'</td>';

			//$table_data_row.='<td width="10%">'.anchor($controller_name."/inventory/$item->item_id/width:$width", $CI->lang->line('common_inv'),array('class'=>'small_button thickbox','title'=>$CI->lang->line($controller_name.'_count')));
			$table_data_row.='<td width="10%">'.anchor('home/confirm_user/'.$controller_name."-inventory-$item->item_id/".$CI->lang->line($controller_name.'_count')."/660/465/".'width:350/height:180', $CI->lang->line('common_inv'),array('id'=>'confirm-user','class'=>'small_button thickbox','title'=>$CI->lang->line($controller_name.'_count')));
		}
		
	}

	$table_data_row.='</tr>';
	return $table_data_row;
}


/*
Gets the html data rows for the items.
*/
function get_locations_manage_table_data_rows($locations,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($locations->result() as $location)
	{
		$table_data_rows.=get_location_data_row($location,$controller);
	}

	if($locations->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='7'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('location_no_have_locations')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_location_data_row($location,$controller)
{
	$CI =& get_instance();
	$active = $CI->lang->line('location_no');
	if ($location->active) $active = $CI->lang->line('location_yes');

	$table_data_row ='<tr>';
	
	$table_data_row .= '<td>'.form_checkbox('locations[]', $location->id).'</td>';
	$table_data_row .= '<td>'.$location->name.'</td>';
	$table_data_row .= '<td>'.$location->hostname.'</td>';
	$table_data_row .= '<td>'.$location->database.'</td>';
	$table_data_row .= '<td>'.$location->dbdriver.'</td>';
	$table_data_row .= '<td>'.$active.'</td>';
	$table_data_row .= '<td>'.anchor('locations/view/'.$location->id.'/width:600/height:300', 'Edit', 'class="small_button thickbox", title="'.$CI->lang->line('location_edit').'"').'</td>';

	$table_data_row.='</tr>';
	return $table_data_row;
}
/*
Gets the html table to manage giftcards.
*/
function get_giftcards_manage_table( $giftcards, $controller )
{
	$CI =& get_instance();

	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('giftcards_giftcard_number'),
	$CI->lang->line('giftcards_card_value'),
	'&nbsp',
	);

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_giftcards_manage_table_data_rows( $giftcards, $controller );
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the giftcard.
*/
function get_giftcards_manage_table_data_rows( $giftcards, $controller )
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($giftcards->result() as $giftcard)
	{
		$table_data_rows.=get_giftcard_data_row( $giftcard, $controller );
	}

	if($giftcards->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('giftcards_no_giftcards_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_giftcard_data_row($giftcard,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();
	$height= $controller->get_form_height();

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%'><input type='checkbox' id='giftcard_$giftcard->giftcard_id' value='$giftcard->giftcard_id'/></td>";
	$table_data_row.='<td width="15%">'.$giftcard->giftcard_number.'</td>';
	$table_data_row.='<td width="20%">'.to_currency($giftcard->value).'</td>';
	$table_data_row .= '<td width="5%">';
	if($CI->Employee->has_privilege('update', $controller_name)){
		$table_data_row.=anchor("$controller_name/view/$giftcard->giftcard_id/width:$width/height:$height", $CI->lang->line('common_edit'),array('class'=>'small_button thickbox','title'=>$CI->lang->line($controller_name.'_update')));
	}
	$table_data_row .= '</td>';

	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage item kits.
*/
function get_item_kits_manage_table( $item_kits, $controller )
{
	$CI =& get_instance();

	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('item_kits_name'),
	$CI->lang->line('item_kits_description'),
	'&nbsp',
	);

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_item_kits_manage_table_data_rows( $item_kits, $controller );
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the item kits.
*/
function get_item_kits_manage_table_data_rows( $item_kits, $controller )
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($item_kits->result() as $item_kit)
	{
		$table_data_rows.=get_item_kit_data_row( $item_kit, $controller );
	}

	if($item_kits->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('item_kits_no_item_kits_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_item_kit_data_row($item_kit,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%'><input type='checkbox' id='item_kit_$item_kit->item_kit_id' value='$item_kit->item_kit_id'/></td>";
	$table_data_row.='<td width="15%">'.$item_kit->name.'</td>';
	$table_data_row.='<td width="20%">'.character_limiter($item_kit->description, 25).'</td>';
	if($CI->Employee->has_privilege('update', $controller_name)){
		$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$item_kit->item_kit_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox small_button','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	}else{
		$table_data_row .= '<td width="5%"></td>';
	}
	

	$table_data_row.='</tr>';
	return $table_data_row;
}

function get_services_manage_table($services,$controller){

	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array(
	$CI->lang->line('services_item_number'),
	$CI->lang->line('services_name_owner'),
	$CI->lang->line('common_phone_number'),
	$CI->lang->line('services_brand'),
	$CI->lang->line('services_model'),
	$CI->lang->line('services_received'),
	$CI->lang->line('services_delivered'),
	$CI->lang->line('sales_sub_total'),
	$CI->lang->line('services_status'),
	$CI->lang->line('services_actions')
	);

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_services_manage_table_data_rows($services,$controller);
	$table.='</tbody></table>';
	return $table;

}

function get_services_manage_table_data_rows($services,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($services as $service)
	{
		$table_data_rows.=get_service_data_row($service,$controller);
	}

	if(count($services)==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('services_no_services_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_service_data_row($service,$controller)
{
	$CI =& get_instance();

	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();
	$price=($service->price+($CI->config->item('default_service')?$CI->config->item('default_service'):0));
	$simbol=($CI->config->item('currency_symbol')?$CI->config->item('currency_symbol'):'$');
	$acum=0;
	if($service->add_pay!=''){
		$payclass = explode(',', $service->add_pay); 
		foreach ($payclass as $valor){
			$class = explode(' ', $valor);$mont=false; 
			if (isset($class[1])){
				switch ($class[0]) {
					case 'DebitCard': case 'CreditCard': case 'Cash': case 'Check': break;
					case 'GiftCard': $mont=true; break;
				}
				$acum=$acum+(!$mont?$class[1]:0);
			}
		}
	}
	//$table_data_row='<tr'.($service->is_locked?' title="'.$CI->lang->line('services_is_locked_title').'" class="locked"':'').'>';
	$table_data_row='<tr class="service status-'.$service->status.'">';
	//$table_data_row.="<td width='3%'><input class='".($service->is_locked?'locked':'')."' type='checkbox' id='service_$service->service_id' value='$service->service_id'/></td>";
	//$table_data_row.="<td width='3%'>".($service->status==100?'':form_checkbox('services[]', $service->service_id))."</td>";
	$table_data_row.='<td width="5%">'.$service->service_id.'</td>';
	$table_data_row.='<td width="17%">'.$service->first_name.' '.$service->last_name.'</small></td>';
	$table_data_row.='<td width="17%">'.$service->phone_number.'</td>';
	$table_data_row.='<td width="13%">'.$service->brand_name.'</td>';
	$table_data_row.='<td width="13%">'.$service->model_name.'</td>';
	$table_data_row.='<td width="13%">'.$service->date_received.'</td>';
	$date_delivered = ($service->date_delivered) ? $service->date_delivered : $CI->lang->line('services_undelivered');
	$table_data_row.='<td width="13%">'.$date_delivered.'</td>';
	$table_data_row.='<td width="13%" style="text-align: right;">ST:'.$simbol.$price.'<br/>P:'.$simbol.$acum.'<br/>D:'.$simbol.($price-$acum).'</td>';
	$table_data_row.='<td width="12%"><span class="status">'.$CI->lang->line('services_status_'.$service->status).'</span></td>';

	switch ($service->status){
		case 3:
			$hidden = array('item' => '3','customer_id' => $service->person_id, 'service' => $service->service_id);

			$table_data_row.='<td width="5%">'.form_open('sales/add/', '', $hidden).form_submit(array('value'=>$CI->lang->line('services_pay'),'class'=>'small_button')).form_close();
			$table_data_row.=anchor($controller_name."/view/$service->service_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'small_button thickbox','title'=>$CI->lang->line($controller_name.'_update')));
			$table_data_row.='</td>';
			break;
		case 100:
			$table_data_row.='<td width="5%">'.$CI->lang->line('services_no_actions').'</td>';
		break;
		default: 
			
			$b_note = anchor($controller_name."/notes_view/$service->service_id/width:$width", $CI->lang->line('services_btn_notes'),array('class'=>'small_button thickbox','title'=>$CI->lang->line($controller_name.'_list_notes')));
			$b_edit = anchor($controller_name."/view/$service->service_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'small_button thickbox','title'=>$CI->lang->line($controller_name.'_update'))); 

			$table_data_row.= '<td width="5%">'.$b_note.'&nbsp;'.$b_edit.'</td>';
			//$table_data_row.= '<td width="5%">'.anchor($controller_name."/view/$service->service_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'small_button thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';

		break;
	}
	$table_data_row.='</tr>';
	return $table_data_row;
}

?>
