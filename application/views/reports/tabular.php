<?php 
	$numTh=count($headers); $i=0;
	$cs=$numTh>0?ceil(8/count($headers)):1; 
?>
<div style="text-align:center;">
	<div id="receipt_header">
		<div id="page_title" style="margin-bottom:6px;text-align:center;"><?=$title?></div>
		<div class="page_subtitle" style="margin-bottom:6px;"><?=$subtitle?></div>
		<div class="page_subtitle" style="margin-bottom:6px;"><?="Location: $location"?></div>
	</div>
</div>
<?php if($export_excel){ ?><br/><?php } ?>
<div id="table_holder">
	<table class="tablesorter report" <?php if($export_excel) echo 'border="1"'; ?>>
		<thead>
			<tr style="color:#FFFFFF;background-color:#396B98;">
				<?php foreach ($headers as $header) { 
						if ($i==0)	$border_ra="border-top-left-radius:5px;-webkit-border-top-left-radius:5px;";
						elseif (($i+1)==$numTh)	$border_ra="border-top-right-radius:5px;-webkit-border-top-right-radius:5px;";
						else $border_ra='';
				?>
				<th colspan="<?php echo $cs; ?>" style="padding: 5px;<?php echo $border_ra;?>"><?php echo $header; ?></th>
				<?php $i++; } ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($data as $row) { $i=0; ?>
			<tr>
				<?php foreach ($row as $cell) { 
					if (isset($right))	$class=in_array($i++,$right)?'class="this-right"':'';
					else $class='';
				?>
				<td colspan="<?php echo $cs; ?>" <?php echo $class; ?> style="<?php if($export_excel) echo 'text-align:center;'; ?>"><?php echo $cell; ?></td>
				<?php } ?>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php if($export_excel){ ?><br/><?php } ?>
<div style="text-align:center;">
	<div id="report_summary">
	<?php foreach($summary_data as $name=>$value) { ?>
		<div class="summary_row"><?php echo $this->lang->line('reports_'.$name). ': '.to_currency($value); ?></div>
	<?php }?>
	</div>
</div>
<?php
if(!isset($last)) echo '<br/><hr/><br/>';

if(!$export_excel&&isset($last)){
?>
<script type="text/javascript" language="javascript">
	$('.tablesorter').each(function(){
		if($(this).find('tr').length >1) $(this).tablesorter();
	});
</script>
<?php
}
?>
