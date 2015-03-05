<ul class="<?php echo $active_module ?>">
	<?php
	if($this->Employee->has_privilege('Receivings','stock_control')){ ?>
	<li><a href="<?php echo base_url().'index.php/stock_control/goto_receiving'; ?>"><?php echo $this->lang->line('recvs_register'); ?></a></li>
	<?php }
		if($this->Employee->has_privilege('Shipping','stock_control')){  ?>
	<li><a href="<?php echo base_url().'index.php/stock_control/goto_shipping'; ?>"><?php echo $this->lang->line('sales_shipping'); ?></a></li>
	<?php }
		if($this->Employee->has_privilege('Orders','stock_control')){ ?>
	<li><a href="<?php echo base_url().'index.php/stock_control/goto_orders'; ?>"><?php echo $this->lang->line('orders'); ?></a></li>
	<?php } ?>
</ul>