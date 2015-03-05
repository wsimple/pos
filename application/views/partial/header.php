<?php if(ENVIRONMENT == 'development')$this->output->enable_profiler(true); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<base href="<?=base_url()?>" />
	<meta charset="UTF-8">
	<title><?=$this->config->item('company')?></title>
	<link rel="stylesheet" type="text/css" href="css/ospos.css" />
	<link rel="stylesheet" type="text/css" href="css/ospos_print.css" media="print"/>
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="css/notifIt.css" />
	<link rel="stylesheet" type="text/css" href="css/select2.css" media="screen" />
	<script>BASE_URL = '<?=site_url()?>';</script>
	<!--<script src="js/jquery.switch.js" type="text/javascript" charset="UTF-8"></script>-->
	<script src="js/jquery-1.10.2.min.js" type="text/javascript" charset="UTF-8"></script>
	<!--<script>window.$$=window.jQueryNew=jQuerySwitch('jQueryNew',jQuery);</script>-->
	<!-- jquery new -->
	<script src="js/jquery.local.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/jquery.form.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/notifIt.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/thickbox.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/jquery.validate.min.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/select2.min.js" type="text/javascript" charset="UTF-8"></script>
	<script>window.$$=window.jQueryNew=jQuery.noConflict();</script>
	<!-- end jquery new -->
	<script src="js/jquery-1.2.6.min.js" type="text/javascript" charset="UTF-8"></script>
	<!--<script>window.jQueryOld=jQuerySwitch('jQueryOld',jQuery);</script>-->
	<!-- jquery old -->
	<script src="js/jquery.formatCurrency.js" type="text/javascript"></script>
	<script src="js/jquery-ui.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/jquery.color.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/jquery.metadata.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/jquery.form.old.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/jquery.tablesorter.min.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/jquery.ajax_queue.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/jquery.bgiframe.min.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/jquery.autocomplete.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/jquery.validate.min.old.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/jquery.jkey-1.1.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/common.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/manage_tables.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/swfobject.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/date.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/datepicker.js" type="text/javascript" charset="UTF-8"></script>
	<!-- end jquery old -->

<style type="text/css">
html{
    overflow: auto;
}

.logoutdetails-content{
	text-align: center;
}

.logoutdetails-content .summary_col{
	font-size:16px;
	line-height:1.4em;
	padding-top:.5em;
}
.overlay{
	display: none;
}
</style>

</head>
<body>
<!-- inicio chat (agregar guion doble para descomentar) ->
<div id="chat"></div>
<link rel="stylesheet" type="text/css" href="css/chat.css" media="screen" />
<script src="js/chat.js" type="text/javascript" charset="UTF-8"></script>
<!-- fin chat -->
<nav class="main-menu">
	<ul>
		<?php
		foreach($allowed_modules->result() as $module){
			$sub_menu = @file_exists('application/views/sub_menus/'.$module->module_id.'.sb.php');
			switch ($module->module_id) {
				case 'sales': $text='/index/sales'; break;
				case 'notification_alert': $band=true; $text=''; break;
				default: $text=''; break;
			}
			if (isset($band) && $band=true){ unset($band); continue; }
		?>
		<li class="menu_item<?php echo ($sub_menu) ? ' alternative-menu-vertical' : '';  ?>" id="<?=$module->module_id?>">
			<ul url="<?=site_url("$module->module_id").$text?>">
				<li>
					<img src="images/menubar/<?=$module->module_id?>.png" border="0" alt="Menubar Image" />
				</li>
				<li>
					<span><?=$this->lang->line("module_".$module->module_id)?></span>
					<?php if(isset($module->shortcut)&&$module->shortcut!=''){ ?><a href="<?=site_url("$module->module_id").$module->shortcut?>" title="<?=$this->lang->line("module_".$module->module_id)?>" class='small_button thickbox shortcut_button'>+</a><?php } ?>
				</li>
			</ul>
			<?php
			//Submenus para main menu solo agregar archivo en: "application/views/sub_menus"
			if ($sub_menu) {
				$this->load->view('sub_menus/'.$module->module_id.'.sb.php', array('active_module'=>$module->module_id));
			}
			?>
		</li>
		<?php
		}
		?>
		<li class="menu_item" id="assistance">
			<ul>
				<li><img src="images/menubar/schedule.png" border="0" alt="Menubar Image" style="cursor: pointer" /></li>
				<li><span>Schedule</span></li>
			</ul>
		</li>
	</ul>
</nav>

<nav class="user">
	<div class="logo">
		<a href="index.php"><img src="images/<?=file_exists('images/'.$this->Appconfig->get('logo'))?$this->Appconfig->get('logo'):'logo.png'?>" border="0"/><?php //echo $this->config->item('company'); ?></a>
	</div>
	<div id="menubar_date">
		<?=date('F d, Y')?>
		<span></span>
	</div>
	<?php
		$dbs = $this->Location->get_select_option_list();
		$dbs['default']='Principal';
		$people = $this->Employee->get_all();
	?>
	<div class="container-menus">
	<?php if(isset($notifications)){ ?>
		<nav id="notifications" class="alternative-menu">
			<?php 
				$li='';$num=0;
				foreach ($notifications as $notification => $data){
					$noti_num = count($data['data']);
					$num=$num+$noti_num;//clearfix
					$li.='<li class="clearfix"><div class="notification-'.$notification.'" style="max-width: 26px;float:left;'.($noti_num>999?'font-size: 12px;':'').'">'.$noti_num.'</div>';
					if ( $noti_num > 0 ){ //Si tiene notificaciones se pone como enlace
						$li.=anchor($data['url'],$data['title'],'style="float:right;max-width:100px;"');
					}else{
						$li.='<label style="float:right;max-width:100px;">'.$data['title'].'</label>';
					}
					$li.='</li>';
				} 
			?>
			<div><?php echo $num; ?></div>
			<ul><?php echo $li; ?></ul>
		</nav>
		<?php } ?>
		<nav id="menu_changelocation">
			<span>User:</span><?=' '.$user_info->first_name.' '.$user_info->last_name; ?>
		</nav>
		<nav id="menu_location" class="<?php if ($this->Employee->isAdmin()){  ?>alternative-menu <?php } ?>">
			<form action="index.php/employees/set_location" method="POST" id="form-location">
				<input type="hidden" value="" name="location"/>
			</form>
			<span>
				<?php 
				$_location=$this->session->userdata('dblocation')=='default'?'principal':$this->session->userdata('dblocation');
				echo '<span>Location: </span>'.ucwords($_location);
				if ($this->Employee->isAdmin()){ ?>
				<ul>
					<?php 
					foreach($dbs as $key => $db){ ?>
					<li>
						<?=anchor('#',$db, 'data-url="'.str_replace('"', '\"', $key).'"')?>
					</li>
					<?php } ?>
				</ul>
				<?php } ?>
			</span> 
		</nav>
		<nav id="menu_changeuser" class="alternative-menu">
			<span><?=$this->lang->line('common_changeuser')?></span>
			<ul>
				<?php
					foreach($people->result() as $person)
					{
						?>
						<li><?=anchor("cajas/change/".$person->person_id."/width:550/height:380",$person->last_name.' '.$person->first_name,'class="thickbox" title="'.$this->lang->line('common_end_of_the_day').'"')?></li>
						<?php
					}
				?>
			</ul>
		</nav>
		<nav id="menu_logout">
			<a href="index.php/cajas/logout/width:550/height:380" class="thickbox" title="<?php echo $this->lang->line('common_end_of_the_day') ?>"><?=$this->lang->line("common_end_of_the_day")?></a>
		</nav>
	</div>
</nav>
<div id="overlay_cash">
	<div class="contentWrap"></div>
</div>
<div id="content_area_wrapper">
<div id="content_area">
<?php $hoy = date('Y-m-d'); ?>
<script>
(function($){
	$('#menu_location').on('click','ul li a',function(event) {
		var a = this;
		$('#form-location > input').attr('value', a.dataset.url);
		$('#form-location').submit();
		return false;
	});
})(jQueryNew);
(function($){
	var cont='<?=$this->uri->segment(1)?>',cont2='<?=$this->uri->segment(2)?>',cont3='<?=$this->uri->segment(3)?>';
	$('nav.main-menu ul>li [url]').click(function(event){
		//redirecciona si el click no se hizo sobre un link ni un thickbox
		if(event.target.href==''||!$(event.target).hasClass('thickbox'))
			location.href=$(this).attr('url');
	});

	$('#assistance').click(function(event){
		location.href='index.php/employees/assistance';
	});
	if (cont=='receivings' || cont=='orders' || cont3=='shipping'){
		$('#stock_control').addClass('nav-main-menu-active');
	}else if(cont2=='assistance'){
		$('#assistance').addClass('nav-main-menu-active');
	}else{
		$('nav.main-menu ul>li#'+cont).addClass('nav-main-menu-active');
	};

	//On dom ready
	$(function(){
		var $date=$("#menubar_date > span");
		function mostrarHora(){
			var hoy=new Date();
			var h=hoy.getHours();
			var m=hoy.getMinutes();
			var s=hoy.getSeconds();
			if(s <= 9) s = '0'+s;
			if(m <= 9) m = '0'+m;
			var hora = h+":"+m+":"+s
			$date.html(hora);
			setTimeout(function(){mostrarHora()},500);
		}
		mostrarHora();
	});
})(jQuery);
</script>
