<?php
#
# Main page for adding new quality control
#
include("redirect.php");
include("includes/header.php");
LangUtil::setPageId("quality");
$script_elems->enableFormBuilder();
?>
<script type='text/javascript'>
function check_input()
{
	// Validate
	var category_name = $('#category_name').attr("value");
	if(category_name == "")
	{
		alert("<?php echo LangUtil::$pageTerms['TIPS_MISSING_CATNAME']; ?>");
		return;
	}
	// All OK
	$('#new_test_category_form').submit();
}

</script>
<br>
<b><?php echo LangUtil::$generalTerms['NEW_QC'] ?></b>
| <a href='catalog.php?show_tc=1'><?php echo LangUtil::$generalTerms['CMD_CANCEL']; ?></a>
<br><br>
<div class='pretty_box'>
<form name='new_test_category_form' id='new_test_category_form' action='test_category_add.php' method='post'>
<table width="100%" border="0">
  <tr>
    <th scope="col"><?php echo LangUtil::$generalTerms['NEW_QC_DEF_1'] ?> <?php $page_elems->getAsterisk(); ?> <?php echo LangUtil::$generalTerms['NEW_QC_DEF_2'] ?></th>
  </tr>
  <tr>
    <td><p><a href="form_builder/example-html.php"><?php echo LangUtil::$generalTerms['VIEW_SAMPLE_HTML'] ?></a>.</p>
		<div id="my_form_builder"></div></td>
  </tr>
</table>
<br><br>
<input type='button' onclick='check_input();' value='<?php echo LangUtil::$generalTerms['CMD_SUBMIT']; ?>' />
&nbsp;&nbsp;&nbsp;&nbsp;
<a href='catalog.php?show_tc=1'> <?php echo LangUtil::$generalTerms['CMD_CANCEL']; ?></a>
</form>
</div>
<div id='test_category_help' style='display:none'>
<small>
<?php echo LangUtil::$generalTerms['USE_KEY_TO_SELECT'] ?>
</small>
</div>
<?php include("includes/footer.php"); ?>