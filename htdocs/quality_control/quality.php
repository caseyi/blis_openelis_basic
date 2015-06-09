<?php 
include("redirect.php");
include("includes/header.php");
LangUtil::setPageId("quality");

putUILog('quality', 'X', basename($_SERVER['REQUEST_URI'], ".php"), 'X', 'X', 'X');


$dialog_id = "dialog_deletequality";
$script_elems->enableFormBuilder();
$script_elems->enableFacebox();
$script_elems->enableBootstrap();
?>
<script type='text/javascript'>
$(document).ready(function(){
	//$('div.content_div').hide();
	//$('#quality_controls_div').hide();
	//$('#quality_control_categories_div').hide();
	//$('#quality_control_field_groups_div').hide();
	$('#<?php echo $dialog_id; ?>').show();
	<?php
	if(isset($_REQUEST['show_qc']))
	{
		?>
		load_right_pane('quality_controls_div');
		<?php
	}
	else if(isset($_REQUEST['show_qcc']))
	{
		?>
		load_right_pane('quality_control_categories_div');
		<?php
	}
	/*else if(isset($_REQUEST['show_qcfg']))
	{
		?>
		load_right_pane('quality_control_field_groups_div');
		<?php
	}*/
	else if(isset($_REQUEST['qcdel']))
	{
		?>
		$('#tdel_msg').show();
		load_right_pane('quality_controls_div');
		<?php
	}
	else if(isset($_REQUEST['qccdel']))
	{
		?>
		$('#sdel_msg').show();
		load_right_pane('quality_control_categories_div');
		<?php
	}
	else if(isset($_REQUEST['qcfgdel']))
	{
		?>
		$('#sdel_msg').show();
		load_right_pane('quality_control_field_groups_div');
		<?php
	}
	else if (isset($_REQUEST['rm']))
	{
		?>
		$('#rm_msg').show();
		<?php
	}
	?>
});

function load_right_pane(div_id)
{
	$('#rm_msg').hide();
	$('div.content_div').hide();
	$('#'+div_id).show();
	$('.menu_option').removeClass('current_menu_option');
	$('#'+div_id+'_menu').addClass('current_menu_option');
}

function hide_right_pane()
{
	$('div.content_div').hide();
	$('.menu_option').removeClass('current_menu_option');
}

function delete_quality_data()
{
	$('#remove_data_progress').show();
	var url_string = "ajax/quality_deletedata.php";
	$.ajax({
		url: url_string, 
		success: function () {
			$('#remove_data_progress').hide();
			window.location='quality.php?rm';
		}
	});
}
</script>
<br>
<table cellpadding='10px' width="100%">
<tr valign='top'>
<td id=''>
	<div id='rm_msg' class='clean-orange' style='display:none;width:200px;'>
		<?php echo LangUtil::$generalTerms['MSG_DELETED']; ?>&nbsp;&nbsp;<a href="javascript:toggle('rm_msg');"><?php echo LangUtil::$generalTerms['CMD_HIDE']; ?></a>
	</div>
    <div class="row-fluid ">
					<div class="span12">				
					<div class="tab-content">
						<div class="tab-pane active" id="tabs-basic">
							<div class="tabbable">
								<ul class="nav nav-tabs">
									<li class="active"><a href="#tabs1-pane1" data-toggle="tab"><?php echo LangUtil::$generalTerms['QUALITY_CONTROLS']; ?></a></li>
									<li><a href="#tabs1-pane2" data-toggle="tab"><?php echo LangUtil::$generalTerms['QUALITY_CONTROL_CATEGORIES']; ?></a></li>
									<!--<li><a href="#tabs1-pane3" data-toggle="tab"><?php echo LangUtil::$generalTerms['QUALITY_CONTROL_FIELD_GROUPS']; ?></a></li>-->
								</ul>
								<div class="tab-content">
									<div id="quality_controls_div" class="tab-pane active" id="tabs1-pane1">
										
		<h5><?php echo LangUtil::$generalTerms['QUALITY_CONTROLS']; ?>
		| <a href='quality_control_new.php' title='<?php echo LangUtil::$generalTerms['CLICK_TO_ADD_NEW_QC']; ?>'><?php echo LangUtil::$generalTerms['ADDNEW']; ?></h5></a>
		<div id='tdel_msg' class='clean-orange' style='display:none;'>
			<?php echo LangUtil::$generalTerms['MSG_DELETED']; ?>&nbsp;&nbsp;<a href="javascript:toggle('qcdel_msg');"><?php echo LangUtil::$generalTerms['CMD_HIDE']; ?></a>
		</div>
		<?php $page_elems->getQualityControlsTable($_SESSION['lab_config_id']); ?>

										
									</div>
									<div class="tab-pane" id="tabs1-pane2">
										
		<div id='tdel_msg' class='clean-orange' style='display:none;'>
			<?php echo LangUtil::$generalTerms['MSG_DELETED']; ?>&nbsp;&nbsp;<a href="javascript:toggle('qccdel_msg');"><?php echo LangUtil::$generalTerms['CMD_HIDE']; ?></a>
		</div>

		<?php //$page_elems->getQualityControlCategoriesTable($_SESSION['lab_config_id']); ?>
									</div>
									
									</div><!-- /.tab-content -->
							</div><!-- /.tabbable -->
						</div><!-- .tabs-basic -->
						</div>
						<!-- END TAB PORTLET-->

    <div id='quality_control_categories_div' class='content_div'>
		<p style="text-align: right;"><a rel='facebox' href='#QualityControlCategories_tc'><?php echo LangUtil::$generalTerms['PAGE_HELP']; ?></a></p>
		<b><?php echo LangUtil::$generalTerms['QUALITY_CONTROL_CATEGORIES']; ?></b>
		| <a href='quality_control_category_new.php' title='<?php echo LangUtil::$generalTerms['CLICK_ADD_NEW_QUALITY_CONTROL_CATEGORY']; ?>'><?php echo LangUtil::$generalTerms['ADDNEW']; ?></a>
		<br><br>
		<div id='tdel_msg' class='clean-orange' style='display:none;'>
			<?php echo LangUtil::$generalTerms['MSG_DELETED']; ?>&nbsp;&nbsp;<a href="javascript:toggle('qccdel_msg');"><?php echo LangUtil::$generalTerms['CMD_HIDE']; ?></a>
		</div>
		<?php $page_elems->getQualityControlCategoriesTable($_SESSION['lab_config_id']); ?>
	</div>
    
	
    
    <div id='QualityControlCategories_tc' class='right_pane' style='display:none;margin-left:10px;'>
		 <?php
        
            echo "<li>";
            echo LangUtil::$generalTerms['QUALITY_TIP_1'];
            echo "</li>";
            echo "<li>";
            echo LangUtil::$generalTerms['QUALITY_TIP_2'];
            echo "</li>"           
            
        ?>
	</div>
	</div>
	
	<?php
	if(is_super_admin($user) || is_country_dir($user))
	{
	?>
		<div id='remove_data_div' class='content_div'>
			<b><?php echo LangUtil::$pageTerms['MENU_REMOVEDATA']; ?></b> |
			<a href='javascript:hide_right_pane()'><?php echo LangUtil::$generalTerms['CMD_CANCEL']; ?></a>
			<br><br>
			<?php
			$message = LangUtil::$pageTerms['TIPS_REMOVEDATA'];
			$ok_function = "delete_quality_data();";
			$cancel_function = "hide_right_pane();";
			$page_elems->getConfirmDialog($dialog_id, $message, $ok_function, $cancel_function);
			?>
			<span id='remove_data_progress' style='display:none;'>
				<br>
				&nbsp;<?php $page_elems->getProgressSpinner(" ".LangUtil::$generalTerms['CMD_SUBMITTING']); ?>
			</span>
		</div>
	<?php
	}
	?>
</td>
</tr>
</table>
<br>
<script type="text/javascript">
	function check_input()
{
	// Validate
	var category_name = $('#category_name').val();
	if(category_name == "")
	{
		alert(category_name);
		return;
	}
	// All OK
	$('#new_quality_control_category_form').submit();
}

</script>
<?php include("includes/footer.php"); ?>
