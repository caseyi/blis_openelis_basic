<?php
#
# Main page for modifying an existing specimen type
#
include("redirect.php");
include("includes/header.php");
include("includes/ajax_lib.php");
LangUtil::setPageId("catalog");


$specimen_type = get_specimen_type_by_id($_REQUEST['sid']);
?>

<!-- BEGIN PAGE TITLE & BREADCRUMB-->       
                        <h3>
                        </h3>
                        <ul class="breadcrumb">
                            <li>
                                <i class="icon-download-alt"></i>
                                <a href="index.php"><?php echo LangUtil::$generalTerms['HOME']; ?></a> 
                            </li>
                        </ul>
                        <!-- END PAGE TITLE & BREADCRUMB-->
                    </div>
                </div>
                <!-- END PAGE HEADER-->
                <!-- BEGIN REGISTRATION PORTLETS-->   
                <div class="row-fluid">
                <div class="span12 sortable">
                
    <div class="portlet box blue">
        <div class="portlet-title">
            <h4><i class="icon-reorder"></i><?php echo LangUtil::$pageTerms['EDIT_SPECIMEN_TYPE']; ?></h4>
            <div class="tools">
            <a href="javascript:;" class="collapse"></a>
            <a href="javascript:;" class="reload"></a>
            </div>
        </div>
        <div class="portlet-body form">

            <a href="catalog.php?show_s=1"><?php echo LangUtil::$generalTerms['CMD_CANCEL']; ?></a>
            <br><br>
            <?php
            if($specimen_type == null)
            {
            ?>
            	<div class='sidetip_nopos'>
            	<?php echo LangUtil::$generalTerms['MSG_NOTFOUND']; ?>
            	</div>
            <?php
            	include("includes/footer.php");
            	return;
            }
            $page_elems->getSpecimenTypeInfo($specimen_type->name, true);
            ?>
            <br>
            <br>
            <div class='pretty_box'>
            <form name='edit_stype_form' id='edit_stype_form' action='ajax/specimen_type_update.php' method='post'>
            <input type='hidden' name='sid' id='sid' value='<?php echo $_REQUEST['sid']; ?>'></input>
            	<table cellspacing='4px'>
            		<tbody>
            			<tr valign='top'>
            				<td style='width:150px;'><?php echo LangUtil::$generalTerms['NAME']; ?><?php $page_elems->getAsterisk(); ?></td>
            				<td><input type='text' name='name' id='name' value='<?php echo $specimen_type->getName(); ?>' class='uniform_width'></input></td>
            			</tr>
            			<tr valign='top'>
            				<td><?php echo LangUtil::$generalTerms['DESCRIPTION']; ?></td>
            				<td><textarea type='text' name='description' id='description' class='uniform_width'><?php echo trim($specimen_type->description); ?></textarea></td>
            			</tr>
            			<tr valign='top'>
            				<td><?php echo LangUtil::$generalTerms['COMPATIBLE_TESTS']; ?><?php $page_elems->getAsterisk(); ?> [<a href='#test_help' rel='facebox'>?</a>] </td>
            				<td>
            					<?php $page_elems->getTestTypeCheckboxes(); ?>
            					<br>
            				</td>
            			</tr>
            			<tr>
            				<td></td>
            				<td>
            					<input type='button' class="btn green" value='<?php echo LangUtil::$generalTerms['CMD_SUBMIT']; ?>' onclick='javascript:update_stype();'></input>
            					&nbsp;&nbsp;&nbsp;
            					<a class="btn" href='catalog.php?show_s=1'><?php echo LangUtil::$generalTerms['CMD_CANCEL']; ?></a>
            					&nbsp;&nbsp;&nbsp;
            					<span id='update_stype_progress' style='display:none;'>
            						<?php $page_elems->getProgressSpinner(LangUtil::$generalTerms['CMD_SUBMITTING']); ?>
            					</span>
            				</td>
            			</tr>
            		</tbody>
            	</table>
            </form>
            </div>
            <div id='test_help' style='display:none'>
            <small>
            Use Ctrl+F to search easily through the list. Ctrl+F will prompt a box where you can enter the test name you are looking for.
            </small>
            </div>
            </div>
        </div>
    </div>
</div>
<?php
include("includes/scripts.php");
$script_elems->enableDatePicker();
$script_elems->enableJQuery();
$script_elems->enableFacebox();
$script_elems->enableJQueryForm();
$script_elems->enableTokenInput();
?>
<script type='text/javascript'>
$(document).ready(function(){
	<?php
	$test_list = get_compatible_tests($specimen_type->specimenTypeId);
	foreach($test_list as $test_type_id)
	{
		# Mark existing compatible tests as checked
		?>
		$('#t_type_<?php echo $test_type_id; ?>').attr("checked", "checked"); 
		<?php
	}
	?>
});

function update_stype()
{
	if($('#name').attr("value").trim() == "")
	{
		alert("<?php echo LangUtil::$pageTerms['TIPS_MISSING_SPECIMENNAME']; ?>");
		return;
	}
	var ttype_entries = $('.ttype_entry');
	var ttype_selected = false;
	for(var i = 0; i < ttype_entries.length; i++)
	{
		if(ttype_entries[i].checked)
		{
			ttype_selected = true;
			break;
		}
	}
	if(ttype_selected == false)
	{
		alert("<?php echo LangUtil::$pageTerms['TIPS_MISSING_SELECTEDTESTS']; ?>");
		return;
	}
	$('#update_stype_progress').show();
	$('#edit_stype_form').ajaxSubmit({
		success: function(msg) {
			$('#update_stype_progress').hide();
			window.location="specimen_type_updated.php?sid=<?php echo $_REQUEST['sid']; ?>";
		}
	});
}
</script>
<?php include("includes/footer.php"); ?>