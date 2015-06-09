<?php
#
# Shows confirmation for quality control category updation
#
include("redirect.php");
include("includes/header.php"); 
LangUtil::setPageId("quality");
?>
<br>
<b><?php echo LangUtil::$generalTerms['QC_CATEGORY_UPDATED']; ?></b>
 | <a href='quality.php?show_qcc=1'>&laquo; <?php echo LangUtil::$generalTerms['BACK_QC']; ?></a>
<br><br>
<?php 
$qcc = get_test_category_by_id($_REQUEST['qccid']);
$page_elems->getQualityControlCategoryInfo($qcc->name); 
include("includes/footer.php");
?>