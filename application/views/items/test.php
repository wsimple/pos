<?php
$this->load->view("partial/header");
$total_general = 0;
?>
<style>
    .table-header tr td{
        width: 33.3%;
        text-align: center;
    }
    #sortable_table tr td:last-child{
        text-align: right;
    }
</style>
<link rel="stylesheet" href="css/compare_stock.css" media="print">
<table class="table-header">
    <tr>
        <td>
            <div id="dispatch_header">
                <div id="company_name"><?php echo $this->config->item('company'); ?></div>
                <div id="company_address"><?php echo nl2br($this->config->item('address')); ?></div>
                <div id="company_phone"><?php echo $this->config->item('phone'); ?></div>
            </div>
        </td>
        <td>
            <div id="page_title" style="margin-bottom:8px;"><?php echo $title ?></div>
            <div class="page_subtitle" style="margin-bottom:8px;">
                <?php echo $subtitle ?><br>
                <div id="employee"><?php echo $this->lang->line('employees_employee').": ".$employee ?></div>
            </div>
        </td>
        <td>
            <?php echo '<img src="'.site_url().'/barcode?barcode='.$barcode.'&text='.$barcodetext.'&width=256" />';  ?>
        </td>
    </tr>
</table>
<div class="product-shipped-report">
	<table class="tablesorter report compare-stock" id="sortable_table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Sub Total</th>
            </tr>
        </thead>
        <tbody>

        <?php for ($i=0;$i<count($items_info);$i++): ?>
            <?php $total_general += $items_info[$i]->cost_price*$items_info[$i]->quantity; ?>
        	<tr>
        		<td><?php echo $items_info[$i]->item_id; ?></td>
        		<td><?php echo $items_info[$i]->name; ?></td>
        		<td><?php echo $items_info[$i]->quantity; ?></td>
        		<td><?php echo to_currency($items_info[$i]->cost_price); ?></td>
        		<td><?php echo to_currency($items_info[$i]->cost_price*$items_info[$i]->quantity); ?></td>
        	</tr>
        <?php endfor; ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><strong>Total:</strong> <?php echo to_currency($total_general); ?></td>
            </tr>
        </tbody>
	</table>
</div>

<a class="linkPrint" href="#">
    <div class="big_button" style="float: left;"><span>Print</span></div>
</a>
<a class="linkBack" href="#">
    <div class="big_button" style="float: left;"><span>Back</span></div>
</a>
<?php
$this->load->view("partial/footer");
?>
<script>
	$('.linkPrint').click(function(){
        window.print();
        return false;
    });
</script>