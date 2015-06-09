<?php
#
# Main page for showing disease report and options to export
# Called via POST from reports.php
#
include("redirect.php");
include("includes/db_lib.php");
include("includes/stats_lib.php");
include("includes/script_elems.php");
LangUtil::setPageId("reports");

include("../users/accesslist.php");
 if(!(isLoggedIn(get_user_by_id($_SESSION['user_id']))))
	header( 'Location: home.php' );
 
$script_elems = new ScriptElems();
$script_elems->enableJQuery();
$script_elems->enableTableSorter();

?>
<html>
<head>
<script type="text/javascript" src="js/table2CSV.js"></script>
<script type='text/javascript'>
var curr_orientation = 0;
function export_as_word(div_id)
{
	var html_data = $('#'+div_id).html();
	$('#word_data').attr("value", html_data);
	//$('#export_word_form').submit();
	$('#word_format_form').submit();
}

function export_as_pdf(div_id)
{
	var content = $('#'+div_id).html();
	$('#pdf_data').attr("value", content);
	$('#pdf_format_form').submit();
}

function export_as_txt(div_id)
{
	var content = $('#'+div_id).html();
	$('#txt_data').attr("value", content);
	$('#txt_format_form').submit();
}

function export_as_csv(table_id, table_id2)
{
	var content = $('#'+table_id).table2CSV({delivery:'value'}) + '\n\n' + $('#'+table_id2).table2CSV({delivery:'value'});
	$("#csv_data").val(content);
	$('#csv_format_form').submit();
}

function print_content(div_id)
{
	var DocumentContainer = document.getElementById(div_id);
	var WindowObject = window.open("", "PrintWindow", "toolbars=no,scrollbars=yes,status=no,resizable=yes");
	WindowObject.document.writeln(DocumentContainer.innerHTML);
	WindowObject.document.close();
	WindowObject.focus();
	WindowObject.print();
	WindowObject.close();
	//javascript:window.print();
}

$(document).ready(function(){
	$("input[name='do_landscape']").click( function() {
		change_orientation();
	});
});

function change_orientation()
{
	var do_landscape = $("input[name='do_landscape']:checked").attr("value");
	if(do_landscape == "Y" && curr_orientation == 0)
	{
		$('#report_config_content').removeClass("portrait_content");
		$('#report_config_content').addClass("landscape_content");
		curr_orientation = 1;
	}
	if(do_landscape == "N" && curr_orientation == 1)
	{
		$('#report_config_content').removeClass("landscape_content");
		$('#report_config_content').addClass("portrait_content");
		curr_orientation = 0;
	}
}

</script>
</head>
<body>
<div id='report_content'>

<link rel='stylesheet' type='text/css' href='css/table_print.css' />

<style type='text/css'>

div.editable {

	/*padding: 2px 2px 2px 2px;*/

	margin-top: 2px;

	width:900px;

	height:20px;

}



div.editable input {

	width:700px;

}

div#printhead {

position: fixed; top: 0; left: 0; width: 100%; height: 100%;

padding-bottom: 5em;

margin-bottom: 100px;

display:none;

}



@media all

{

  .page-break { display:none; }

}

@media print

{

	#options_header { display:none; }

	/* div#printhead {	display: block;

  } */

  div#docbody {

  margin-top: 5em;

  }

}



.landscape_content {-moz-transform: rotate(90deg) translate(600px); }



.portrait_content {-moz-transform: translate(1px); rotate(-90deg) }

</style>

<!--form name='word_format_form' id='word_format_form' action='export_word.php' method='post' target='_blank'>
	<input type='hidden' name='data' value='' id='word_data' />
	<input type='hidden' name='lab_id' value='<?php echo $lab_config_id; ?>' id='lab_id'>
	<input type='button' onclick="javascript:print_content('report_content');" value='<?php echo LangUtil::$generalTerms['CMD_PRINT']; ?>'></input>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<!-- <input type='button' onclick="javascript:export_as_word();" value='<?php echo LangUtil::$generalTerms['CMD_EXPORTWORD']; ?>'></input> -->
	&nbsp;&nbsp;&nbsp;&nbsp;
	<!-- <input type='button' onclick="javascript:window.close();" value='<?php echo LangUtil::$generalTerms['CMD_CLOSEPAGE']; ?>'></input> -->
</form-->

<form name='word_format_form' id='word_format_form' action='export_word.php' method='post' target='_blank'>
	<input type='hidden' name='data' value='' id='word_data' />
</form>
<form name='pdf_format_form' id='pdf_format_form' action='export_pdf.php' method='post' target='_blank'>
	<input type='hidden' name='data' value='' id='pdf_data' />
</form>
<form name='txt_format_form' id='txt_format_form' action='export_txt.php' method='post' target='_blank'>
	<input type='hidden' name='data' value='' id='txt_data' />
</form>
<form name='csv_format_form' id='csv_format_form' action='export_csv.php' method='post' target='_blank'> 
	<input type='hidden' name='csv_data' id='csv_data'>
</form>
<input type='radio' name='do_landscape' value='N' <?php
	//if($report_config->landscape == false) echo " checked ";
	echo " checked ";
?>>Portrait</input>
&nbsp;&nbsp;
<input type='radio' name='do_landscape' value='Y' <?php
	//if($report_config->landscape == true) echo " checked ";
?>><?php echo LangUtil::$generalTerms['LANDSCAPE_TYPE']; ?></input>&nbsp;&nbsp;

<input type='button' onclick="javascript:print_content('export_content');" value='<?php echo LangUtil::$generalTerms['CMD_PRINT']; ?>'></input>
&nbsp;&nbsp;
<!-- <input type='button' onclick="javascript:export_as_word('export_content');" value='<?php echo LangUtil::$generalTerms['CMD_EXPORTWORD']; ?>'></input> -->
&nbsp;&nbsp;
<input type='button' onclick="javascript:export_as_pdf('export_content');" value='<?php echo LangUtil::$generalTerms['CMD_EXPORTPDF']; ?>'></input>
&nbsp;&nbsp;
<!--input type='button' onclick="javascript:export_as_txt('export_content');" value='<?php echo LangUtil::$generalTerms['CMD_EXPORTTXT']; ?>'></input>
&nbsp;&nbsp;-->
<input type='button' onclick="javascript:export_as_csv('report_content_header', 'report_content_table1');" value='<?php echo LangUtil::$generalTerms['CMD_EXPORTCSV']; ?>'></input>
&nbsp;&nbsp;
<!-- <input type='button' onclick="javascript:window.close();" value='<?php echo LangUtil::$generalTerms['CMD_CLOSEPAGE']; ?>'></input> -->
&nbsp;&nbsp;

<hr>

<div id='export_content'>
<link rel='stylesheet' type='text/css' href='css/table_print.css' />
<div id='report_config_content'>
<b><?php echo LangUtil::$pageTerms['SPECIMEN_COUNT_REPORT']; ?></b>
<br><br>

<?php
$lab_config_id = $_REQUEST['location'];
$lab_config = LabConfig::getById($lab_config_id);
if($lab_config == null)
{
	echo LangUtil::$generalTerms['MSG_NOTFOUND'];
	return;
}
$date_from = $_REQUEST['from-report-date'];
$date_to = $_REQUEST['to-report-date'];
$uiinfo = "from=".$date_from."&to=".$date_to;
putUILog('reports_specimen_count_ungrouped', $uiinfo, basename($_SERVER['REQUEST_URI'], ".php"), 'X', 'X', 'X');
# Fetch site-wide settings
//$site_settings = DiseaseReport::getByKeys($lab_config->id, 0, 0);

/*
$byAge = 1;
$bySection = 1;
$byGender = 0;
*/
 //$age_group_list = $site_settings->getAgeGroupAsList();
 
$query = 'SELECT a.name AS `Specimen Type`, COUNT(*) AS `Total Specimens`
	FROM specimen_type a INNER JOIN specimen b ON a.specimen_type_id=b.specimen_type_id	
	WHERE date_collected BETWEEN \''.$date_from.'\' AND \''.$date_to.'\'
	GROUP BY a.specimen_type_id ORDER BY a.name';
global $con;
$result = mysql_query($query, $con);
 
?>
<table id='report_content_header' class="print_entry_border draggable">
	<tbody>
		<tr>
			<td><?php echo LangUtil::$generalTerms['FACILITY']; ?>:</td>
			<td><?php echo $lab_config->getSiteName(); ?></td>
		</tr>
		<tr>
			<td><?php echo LangUtil::$pageTerms['REPORT_PERIOD']; ?>:</td>
			<td>
			<?php
			if($date_from == $date_to)
			{
				echo DateLib::mysqlToString($date_from);
			}
			else
			{	
				echo DateLib::mysqlToString($date_from)." to ".DateLib::mysqlToString($date_to);
			}
			?>
			</td>
		</tr>
		
		
	</tbody>
</table>
<?php

$table_css = "style='padding: .3em; border: 1px black solid; font-size:14px;'";
?>
<br>
<table id='report_content_table1' class="print_entry_border draggable">
<?php
	if ($result){
		echo '<thead><tr>';
// 		for ($counter=0; $counter<mysql_num_fields($result); $counter++){
// 			echo '<th>'.mysql_field_name($result, $counter).'</th>';
// 		}
		echo '<th>'.LangUtil::$generalTerms['SPECIMEN_TYPE'].'</th>';
		echo '<th>'.LangUtil::$pageTerms['TOTAL_SPECIMENS'].'</th>';
		echo '</tr></thead><tbody>';
		while($row = mysql_fetch_assoc($result)){
			echo '<tr><td>'.implode($row, '</td><td>').'</tr>';
		}
		echo '</tbody>';
	}
?>
</table>
<br><br><br>
............................................
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#report_content_table1').tablesorter();
});
</script>
</div>
</div>
</body>
</html>