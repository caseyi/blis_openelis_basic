<?php
#
# (c) C4G, Santosh Vempala, Ruban Monu and Amol Shintre Main page for 
# showing list of test/specimen types in catalog, with options to 
# add/modify
#
include("../users/accesslist.php"); if( 
!(isAdmin(get_user_by_id($_SESSION['user_id'])) && 
in_array(basename($_SERVER['PHP_SELF']), $adminPageList)) )
	header( 'Location: home.php' ); include("redirect.php"); 
include("includes/header.php"); require_once("includes/scripts.php"); 
require_once("includes/script_elems.php"); $script_elems = new 
ScriptElems(); $script_elems->enableTableSorter(); 
$script_elems->enableJQueryForm(); $script_elems->enableDatePicker(); 
$script_elems->enableValidation(); LangUtil::setPageId("catalog"); 
putUILog('catalog', 'X', basename($_SERVER['REQUEST_URI'], ".php"), 'X', 
'X', 'X'); $dialog_id = "dialog_deletecatalog"; ?> <script 
type="text/javascript" src="js/jquery.ui.js"></script> <script 
type="text/javascript" src="js/dialog/jquery.ui.core.js"></script> 
<script type="text/javascript" 
src="js/dialog/jquery.ui.dialog.js"></script> <?php $user = 
get_user_by_id($_SESSION['user_id']); if(is_super_admin($user) || 
is_country_dir($user)) {
	# Allow deletion of all catalog data
	?>
	<a href="javascript:load_right_pane('remove_data_div');" 
class='menu_option' id='remove_data_div_menu'>
		<?php echo LangUtil::$pageTerms['MENU_REMOVEDATA']; ?>
	</a>
	<br><br> <?php
}
?> <!-- BEGIN PAGE TITLE & BREADCRUMB-->
						<h3>
						</h3>
						<ul class="breadcrumb">
							<li><i 
class='icon-cogs'></i> <?php echo LangUtil::$pageTerms['TEST_CATALOG']; ?>
							</li>
						</ul>
						<!-- END PAGE TITLE & 
BREADCRUMB-->
					</div>
				</div>
				<!-- END PAGE HEADER--> <!-- BEGIN 
ROW-FLUID--> <div class="row-fluid"> <div class="span12 sortable">
	<div id='rm_msg' class='clean-orange' 
style='display:none;width:200px;'>
		<?php echo LangUtil::$generalTerms['MSG_DELETED']; 
?>&nbsp;&nbsp;<a href="javascript:toggle('rm_msg');"><?php echo 
LangUtil::$generalTerms['CMD_HIDE']; ?></a>
	</div>
	<div id='test_types_div' class='content_div'>
		<div class="portlet box green">
		<div class="portlet-title">
			<h4><i class="icon-reorder"></i><?php echo 
LangUtil::$generalTerms['TEST_TYPES']; ?></h4>
			<div class="tools">
				<a href="javascript:;" 
class="collapse"></a>
				
			</div>
		</div>
		<div class="portlet-body">
		<p style="text-align: right;"><a rel='facebox' 
href='#TestType_tc'><?php echo LangUtil::$generalTerms['PAGE_HELP']; ?></a></p>
		 <a href='test_type_new.php' class="btn blue-stripe" 
title='Click to Add a New Test Type'><i class='icon-plus'></i> <?php 
echo LangUtil::$generalTerms['ADDNEW']; ?></a>
		<br><br>
		<div id='tdel_msg' class='clean-orange' 
style='display:none;'>
			<?php echo LangUtil::$generalTerms['MSG_DELETED']; ?>&nbsp;&nbsp;<a 
href="javascript:toggle('tdel_msg');"><?php echo 
LangUtil::$generalTerms['CMD_HIDE']; ?></a>
		</div>
		<?php 
$page_elems->getTestTypeTable($_SESSION['lab_config_id']); ?>
		</div>
		</div>
	</div>
	
	<div id='specimen_types_div' class='content_div'>
	<div class="portlet box green">
		<div class="portlet-title">
			<h4><i class="icon-reorder"></i><?php echo 
LangUtil::$generalTerms['SPECIMEN_TYPES']; ?></h4>
			<div class="tools">
				<a href="javascript:;" 
class="collapse"></a>
				
			</div>
		</div>
		<div class="portlet-body">
		<p style="text-align: right;"><a rel='facebox' 
href='#SpecimenType_tc'><?php echo LangUtil::$generalTerms['PAGE_HELP']; ?></a></p>
		<a href='specimen_type_new.php' class="btn blue-stripe" 
title='Click to Add a New Specimen Type'><i class='icon-plus'></i> <?php 
echo LangUtil::$generalTerms['ADDNEW']; ?></a>
		<br><br>
		<div id='sdel_msg' class='clean-orange' 
style='display:none;'>
			<?php echo 
LangUtil::$generalTerms['MSG_DELETED']; ?>&nbsp;&nbsp;<a 
href="javascript:toggle('sdel_msg');"><?php echo 
LangUtil::$generalTerms['CMD_HIDE']; ?></a>
		</div>
		<?php 
$page_elems->getSpecimenTypeTable($_SESSION['lab_config_id']); ?>
		</div>
	</div>
	</div>
    
    <div id='test_categories_div' class='content_div'>
    <div class="portlet box green">
		<div class="portlet-title">
			<h4><i class="icon-reorder"></i><?php echo LangUtil::$pageTerms['LAB_SECTIONS']; ?></h4>
			<div class="tools">
				<a href="javascript:;" 
class="collapse"></a>
				
			</div>
		</div>
		<div class="portlet-body">
		<p style="text-align: right;"><a rel='facebox' 
href='#TestCategory_tc'><?php echo LangUtil::$generalTerms['PAGE_HELP']; ?></a></p>
		<a href='javascript:add_section();' class="btn 
blue-stripe" title='Click to Add a New Test Category'><i 
class='icon-plus'></i> <?php echo LangUtil::$generalTerms['ADDNEW']; 
?></a>
		<br><br>
		<div id='sdel_msg' class='clean-orange' 
style='display:none;'>
			<?php echo 
LangUtil::$generalTerms['MSG_DELETED']; ?>&nbsp;&nbsp;<a 
href="javascript:toggle('tcdel_msg');"><?php echo 
LangUtil::$generalTerms['CMD_HIDE']; ?></a>
		</div>
		<?php 
$page_elems->getTestCategoryTable($_SESSION['lab_config_id']); 
//$page_elems->getTestCategorySelect($_SESSION['lab_config_id']); ?>
		</div>
	</div>
	</div>
    
        
    <!-------------------------------------------------BEGIN SPECIMEN 
REJECTION---------------------------------------------------------->
	<div id="specimen_rejection_div" class='content_div'>
    		<div class="portlet box green">
							<div 
class="portlet-title">
								<h4><i 
class="icon-reorder"></i><?php echo LangUtil::$pageTerms['SPECIMEN_REJECTION_PHASE']; ?></h4>
								<div 
class="tools">
									<a 
href="javascript:;" class="collapse"></a>
									<!--a 
href="#portlet-config" data-toggle="modal" class="config"></a-->
								</div>
							</div>
							<div 
class="portlet-body">
								<div 
class="row-fluid">
									<div 
class="span12">
										<!--BEGIN TABS-->
										<div class="tabbable tabbable-custom">
											<ul class="nav nav-tabs">
												<li class="active"><a href="#tab_phases" data-toggle="tab"><h4><?php echo LangUtil::$pageTerms['SPECIMEN_REJECTION_PHASES']; ?></h4></a></li>
												<li><a href="#tab_reasons" data-toggle="tab"><h4><?php echo LangUtil::$pageTerms['SPECIMEN_REJECTION_REASONS']; ?></h4></a></li>
											</ul>
											<div class="tab-content">
												<div class="tab-pane active" id="tab_phases">
		<a href='catalog/rejection_phase_new.php' rel='facebox' class="btn 
blue-stripe" title='<?php echo LangUtil::$pageTerms['CLICK_TO_ADD_SPECIMEN_REJECTION_PHASE']; ?>'><i 
class='icon-plus'></i> <?php echo LangUtil::$generalTerms['ADDNEW']; 
?></a>
		<br><br>
		<div id='sdel_msg' class='clean-orange' 
style='display:none;'>
			<?php echo 
LangUtil::$generalTerms['MSG_DELETED']; ?>&nbsp;&nbsp;<a 
href="javascript:toggle('tcdel_msg');"><?php echo 
LangUtil::$generalTerms['CMD_HIDE']; ?></a>
		</div>
		<?php 
$page_elems->getRejectionPhaseTable($_SESSION['lab_config_id']); ?>
												</div>
												<div class="tab-pane" id="tab_reasons">
													<a href='catalog/rejection_reason_new.php' rel='facebox' class="btn blue-stripe" title='<?php echo LangUtil::$pageTerms['CLICK_TO_ADD_SPECIMEN_REJECTION_REASON']; ?>'><i class='icon-plus'></i> <?php echo LangUtil::$generalTerms['ADDNEW']; ?></a>
		<br><br>
		<div id='sdel_msg' class='clean-orange' 
style='display:none;'>
			<?php echo 
LangUtil::$generalTerms['MSG_DELETED']; ?>&nbsp;&nbsp;<a 
href="javascript:toggle('tcdel_msg');"><?php echo 
LangUtil::$generalTerms['CMD_HIDE']; ?></a>
		</div>
		<?php 
$page_elems->getRejectionReasonTable($_SESSION['lab_config_id']); ?>
												</div>
                                                </div>
                                                </div>
											</div>
										</div>
										<!--END TABS-->
									</div>
								</div>
                              </div>
    <!--------------------------------------------------END SPECIMEN 
REJECTION----------------------------------------------------------->
	
	<div id='TestType_tc' class='right_pane' 
style='display:none;margin-left:10px;'>
		<ul>
			<li><?php echo 
LangUtil::$pageTerms['TIPS_TC_TESTTYPE_1']; ?></li>
			<li><?php echo 
LangUtil::$pageTerms['TIPS_TC_TESTTYPE_2']; ?></li>
			<li><?php echo 
LangUtil::$pageTerms['TIPS_TC_TESTTYPE_3']; ?></li>
		</ul>
	</div>
		
	<div id='SpecimenType_tc' class='right_pane' 
style='display:none;margin-left:10px;'>
		<ul>
			<li><?php echo 
LangUtil::$pageTerms['TIPS_TC_SPECIMENTYPE_1']; ?></li>
			<li><?php echo 
LangUtil::$pageTerms['TIPS_TC_SPECIMENTYPE_2']; ?></li>
			<li><?php echo 
LangUtil::$pageTerms['TIPS_TC_SPECIMENTYPE_3']; ?></li>
		</ul>
	</div>
    
    <div id='TestCategory_tc' class='right_pane' 
style='display:none;margin-left:10px;'>
		<ul>
			<li><?php echo 
LangUtil::$pageTerms['TIPS_TC_TESTCATEGORY_1']; ?></li>
			<li><?php echo 
LangUtil::$pageTerms['TIPS_TC_TESTCATEGORY_2']; ?></li>
			<li><?php echo 
LangUtil::$pageTerms['TIPS_TC_TESTCATEGORY_3']; ?></li>
		</ul>
	</div>
	
	<?php
	if(is_super_admin($user) || is_country_dir($user))
	{
	?>
		<div id='remove_data_div' class='content_div'>
			<b><?php echo 
LangUtil::$pageTerms['MENU_REMOVEDATA']; ?></b> |
			<a href='javascript:hide_right_pane()'><?php 
echo LangUtil::$generalTerms['CMD_CANCEL']; ?></a>
			<br><br>
			<?php
			$message = 
LangUtil::$pageTerms['TIPS_REMOVEDATA'];
			$ok_function = "delete_catalog_data();";
			$cancel_function = "hide_right_pane();";
			$page_elems->getConfirmDialog($dialog_id, 
$message, $ok_function, $cancel_function);
			?>
			<span id='remove_data_progress' 
style='display:none;'>
				<br>
				&nbsp;<?php 
$page_elems->getProgressSpinner(" 
".LangUtil::$generalTerms['CMD_SUBMITTING']); ?>
			</span>
		</div>
	<?php
	}
	?> </div> </div> <div class='modal container hide fade' 
id='form' role="dialog" data-backdrop="static">
	
</div> <!-- END ROW-FLUID--> <?php /*include("includes/scripts.php"); 
require_once("includes/script_elems.php"); $script_elems = new 
ScriptElems(); $script_elems->enableJQueryForm(); 
$script_elems->enableDatePicker(); $script_elems->enableValidation();*/ 
?> <script type='text/javascript'> function load_right_pane(div_id) {
	$('#rm_msg').hide();
	$('div.content_div').hide();
	$('#'+div_id).show();
	$('.menu_option').removeClass('current_menu_option');
	$('#'+div_id+'_menu').addClass('current_menu_option');
}
function hide_right_pane() {
	$('div.content_div').hide();
	$('.menu_option').removeClass('current_menu_option');
}
function delete_catalog_data() {
	$('#remove_data_progress').show();
	var url_string = "ajax/catalog_deletedata.php";
	$.ajax({
		url: url_string,
		success: function () {
			$('#remove_data_progress').hide();
			window.location='catalog.php?rm';
		}
	});
}
function add_section(){
	var el = jQuery('.portlet .tools a.reload').parents(".portlet");
	App.blockUI(el);
	
	var url = 'catalog/test_category_new.php';
	$('#form').html("");
	var target_div = "form";
	$("#"+ target_div).load(url,
		{lab_config: "" },
		function()
		{
			$('#'+target_div).modal('show');
			App.unblockUI(el);
		}
	);
	
}
function add_phase(){
	var el = jQuery('.portlet .tools a.reload').parents(".portlet");
	App.blockUI(el);
	
	var url = 'catalog/rejection_phase_new.php';
	$('#form').html("");
	var target_div = "form";
	$("#"+ target_div).load(url,
		{lab_config: "" },
		function()
		{
			$('#'+target_div).modal('show');
			App.unblockUI(el);
		}
	);
	
}
function add_reason(){
	var el = jQuery('.portlet .tools a.reload').parents(".portlet");
	App.blockUI(el);
	
	var url = 'catalog/rejection_reason_new.php';
	$('#form').html("");
	var target_div = "form";
	$("#"+ target_div).load(url,
		{lab_config: "" },
		function()
		{
			$('#'+target_div).modal('show');
			App.unblockUI(el);
		}
	);
	
}
$(document).ready(function(){
	$('div.content_div').hide();
	$('#test_types_div').hide();
	$('#specimen_types_div').hide();
	$('#test_categories_div').hide();
	$('#specimen_rejection_div').hide();
	$('#<?php echo $dialog_id; ?>').show();
	<?php
	if(isset($_REQUEST['show_t']))
	{
		?>
		load_right_pane('test_types_div');
		<?php
	}
	else if(isset($_REQUEST['show_s']))
	{
		?>
		load_right_pane('specimen_types_div');
		<?php
	}
	else if(isset($_REQUEST['show_tc']))
	{
		?>
		load_right_pane('test_categories_div');
		<?php
	}
	else if(isset($_REQUEST['show_sr']))
	{
		?>
		load_right_pane('specimen_rejection_div');
		<?php
	}
	else if(isset($_REQUEST['tdel']))
	{
		?>
		$('#tdel_msg').show();
		load_right_pane('test_types_div');
		<?php
	}
	else if(isset($_REQUEST['sdel']))
	{
		?>
		$('#sdel_msg').show();
		load_right_pane('specimen_types_div');
		<?php
	}
	else if(isset($_REQUEST['tcdel']))
	{
		?>
		$('#sdel_msg').show();
		load_right_pane('test_categories_div');
		<?php
	}
	else if (isset($_REQUEST['rm']))
	{
		?>
		$('#rm_msg').show();
		<?php
	} else
	{
	?>
	load_right_pane('test_categories_div');
	<?php
	}
	?>
});
/*$(document).ready(function(){
});*/
</script>
<?php include("includes/footer.php"); ?>
