<div id="conten-option" class="<?php echo $control; ?>">
	<ul>
		<?php
		if($this->Employee->has_privilege('Receivings','stock_control')){ ?>
		<li id="recvs-option"><a href="<?php echo base_url().'index.php/stock_control/goto_receiving'; ?>"><?php echo $this->lang->line('recvs_register'); ?></a></li>
		<?php }
			if($this->Employee->has_privilege('Shipping','stock_control')){  ?>
		<li id="shipp-option"><a href="<?php echo base_url().'index.php/stock_control/goto_shipping'; ?>"><?php echo $this->lang->line('sales_shipping'); ?></a></li>
		<?php }
			if($this->Employee->has_privilege('Orders','stock_control')){ ?>
		<li id="ordrs-option"><a href="<?php echo base_url().'index.php/stock_control/goto_orders'; ?>"><?php echo $this->lang->line('orders'); ?></a></li>
		<?php } ?>
		<?php if ($this->Employee->has_privilege('Orders','stock_control')): ?>
			<li id="reports-option"><a href="<?php echo base_url().'index.php/reports/control_stock'; ?>">Stock Control</a></li>
		<?php endif ?>
	</ul>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
	var tabNum = $('#conten-option ul li').length;
	$('#conten-option ul li').css('width',((100/tabNum)-1)+'%');
</script>