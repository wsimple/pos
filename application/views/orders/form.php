<div>
<h2><?php echo $this->lang->line('orders_quantity_required') ?></h2>
<div style="float:left;padding:10px;">
	<input type="number" id="new-qty" min="1" value="1">
	<p>Press enter or OK button to accept changes...</p>
	<input id="close_button" type="button" class="small_button" value="Ok">
</div>
</div>
<script>
	$(function() {
		var pressed = null;
		$('input#new-qty').keydown(function(e) {
			var that = this;
			var key = e.which;
			if (key>=96 && key<=105 || key>=48 && key<=57 || key>=37 && key<=40 || key==8 || key==46) {
				pressed = true;
			}else if(key==13){
				pressed = true;
				if ( $(that).val().length < 1 ) {$(that).val('1')};
				tb_remove(false);
			}else{
				e.preventDefault();
				pressed = false;
			}
		}).keyup(function(e) {
			var that = this;
			if (pressed) {
				$('tbody#cart_contents tr:first-child input[type=text]').val( $(that).val() );
			}
		});

		$('#close_button').click(function(event) {
			$('tbody#cart_contents tr:first-child input[type=text]').val( $('input#new-qty').val() );
			tb_remove();
		});
	});
</script>