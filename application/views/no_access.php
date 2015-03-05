<?php $this->load->view("partial/header"); ?>
<style>
	section.no-access{
		background-color:#fff;
		width: 900px;
		height: 500px;
		margin: auto;
		border-radius: 10px;
		padding: 10px;
	}
	section.no-access div.photo_add,section.no-access p{
		display: inline-block;
		margin: 50px 25px 0px;
	}
	section.no-access div.photo_add{
		border: 1px transparent solid;
		width: 330px; height: 300px;
	}
	section.no-access p{
		width: 300px;
		font-size: 20px;
		font-weight: bold;
		margin-bottom: 100px;
	}

	section.no-access > div > div{
		background-image: url(./images/no_access.png);
		-webkit-background-size: 332px 442px;
		background-size: 65%;
	}
</style>
	<section class="no-access">
		<div class="photo_add">
			<div></div>
		</div>
		<p><?php echo $this->lang->line('error_no_permission_module').' '.$module_name; ?></p>
	</section>
<?php $this->load->view("partial/footer"); ?> 