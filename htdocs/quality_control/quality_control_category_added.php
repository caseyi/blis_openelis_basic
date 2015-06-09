<?php
#
# Shows confirmation for new test type addition
#
include("redirect.php");
include("includes/header.php"); 
LangUtil::setPageId("quality");
?>
<br>
<b><?php echo LangUtil::$generalTerms['QC_CATEGORY_ADDED']; ?></b>
 | <a href='quality.php?show_qcc=1'>&laquo; <?php echo LangUtil::$generalTerms['BACK_QC']; ?></a>
<br><br>
<?php $page_elems->getQualityControlCategoryInfo($_REQUEST['qcc'], true); ?>
<?php include("includes/footer.php"); ?>