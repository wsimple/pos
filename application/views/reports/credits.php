<?php $this->load->view("partial/header"); ?>
<div id="page_title" style="margin-bottom:8px; text-align: center"><?php echo $this->lang->line('reports_reports').' '.$this->lang->line('customers_credit_info'); ?></div>
<?php 
	if(isset($error))
		echo "<div class='error_message' style=' margin: 0 0 10px 0' >".$error."</div>";
?>
<div class="box-form-view">
	<div>
		<div style="float: left; width: 835px; margin-bottom: 10px;">
			<ul>
				<li>
					<h3><?php echo $this->lang->line('customers_credit_info'); ?></h3>
					<ul>
						<li><a href="<?php echo site_url('reports/credit_customers/'.$customers_name.'/'.$customers_id);?>"><?php echo $this->lang->line('customers_general'); ?></a></li>
						<li><a href="<?php echo site_url('reports/customers_credit_overdue_bills/'.$customers_name.'/'.$customers_id);?>"><?php echo $this->lang->line('customers_overdue_bills'); ?></a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>
<?php $this->load->view("partial/footer"); ?>