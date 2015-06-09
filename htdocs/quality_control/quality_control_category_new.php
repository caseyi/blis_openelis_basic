<?php
#
# Main page for adding new test category
#
include("redirect.php");
include("includes/header.php");
LangUtil::setPageId("quality");
?>
<br>
<b><?php echo LangUtil::$generalTerms['NEW_CQ_CATEGORY'] ?></b>
| <a href='quality.php?show_qcc=1'><?php echo LangUtil::$generalTerms['CMD_CANCEL']; ?></a>
<br><br>
<div class='pretty_box'>
<form name='new_quality_control_category_form' id='new_quality_control_category_form' action='quality_control_category_add.php' method='post'>
<table class='smaller_font'>
<tr>
<td style='width:150px;'><?php echo LangUtil::$generalTerms['NAME']; ?><?php $page_elems->getAsterisk(); ?></td>
<td><input type='text' name='category_name' id='category_name' class='uniform_width' /></td>
</tr>
</table>
<br><br>
<input type='button' onclick='check_input();' value='<?php echo LangUtil::$generalTerms['CMD_SUBMIT']; ?>' />
&nbsp;&nbsp;&nbsp;&nbsp;
<a href='quality.php?show_qcc=1'> <?php echo LangUtil::$generalTerms['CMD_CANCEL']; ?></a>
</form>
</div>
<div id='quality_control_category_help' style='display:none'>
<small>
<?php echo LangUtil::$generalTerms['USE_KEY_TO_SELECT'] ?>
</small>
</div>
<?php include("includes/scripts.php");
$script_elems->enableLatencyRecord();
$script_elems->enableDatePicker();
$script_elems->enableJQueryForm();
?>
<script type='text/javascript'>
function check_input()
{
	// Validate
	var category_name = $('#category_name').val();
	if(category_name == "")
	{
		alert("<?php echo LangUtil::$generalTerms['ERR_MIS_QC_CATEGORY_NAME'] ?>");
		return;
	}
	// All OK
	$('#new_quality_control_category_form').submit();
}

</script>

<?php include("includes/footer.php"); ?>