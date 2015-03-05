<div id="receipt_header" style="text-align:center;">
	<div id="page_title" style="margin-bottom:8px;text-align:center;"><?php echo $title; ?></div>
	<div class="page_subtitle" style="margin-bottom:8px;"> <?php echo "$subtitle ($location)"; ?> </div>
</div>
<div id="chart_wrapper">
	<div id="chart<?=$location?>"></div>
</div>
<script type="text/javascript">
swfobject.embedSWF(
	"<?=base_url()?>open-flash-chart.swf","chart<?=$location?>",
	"100%","100%","9.0.0","expressInstall.swf",
	{"data-file":"<?=$data_file?>"}
);
</script>
<div id="report_summary">
<?php foreach($summary_data as $name=>$value){ ?>
	<div class="summary_row"><?=$this->lang->line('reports_'.$name).': '.to_currency($value)?></div>
<?php } ?>
</div>
<div id="location_id" style="margin:0 auto;text-align:center;">Location:<?=$location?></div>
<?=isset($last)?'':'<br/><hr/><br/>'?>