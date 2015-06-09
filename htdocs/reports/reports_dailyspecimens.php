<?php
#
# Main page for printing daily specimen records
#
include("redirect.php");
include("includes/db_lib.php");
include("includes/script_elems.php");
include("includes/page_elems.php");
LangUtil::setPageId("reports");

# Utility function
function get_records_to_print($lab_config, $test_type_id, $date_from, $date_to)
{
	$saved_db = DbUtil::switchToLabConfig($lab_config->id);
	$retval = array();
	
	if(isset($_REQUEST['p']) && $_REQUEST['p'] ==1)
	{
	
		$query_string =
		"SELECT * FROM test WHERE test_type_id=$test_type_id ".
		"AND result LIKE '' ".
		"AND specimen_id IN ( ".
			"SELECT specimen_id FROM specimen ".
			"WHERE (date_collected BETWEEN '$date_from' AND '$date_to') ".
		")";
	}
	else if(isset($_REQUEST['ip']) && $_REQUEST['ip'] == 0)
	{
		$query_string =
		"SELECT * FROM test WHERE test_type_id=$test_type_id ".
		//"AND result <> '' ".
		"AND specimen_id IN ( ".
			"SELECT specimen_id FROM specimen ".
			"WHERE (date_collected BETWEEN '$date_from' AND '$date_to') ".
		")";
	}
	else
	{
		$query_string =
		"SELECT * FROM test WHERE test_type_id=$test_type_id ".
		//"AND result <> '' ".
		"AND specimen_id IN ( ".
			"SELECT specimen_id FROM specimen ".
			"WHERE (date_collected BETWEEN '$date_from' AND '$date_to') ".
		")";
	}
	
	$resultset = query_associative_all($query_string, $row_count);
	
	foreach($resultset as $record)
	{
		$test = Test::getObject($record);
		$specimen = Specimen::getById($test->specimenId);
		$patient = Patient::getById($specimen->patientId);
		$retval[] = array($test, $specimen, $patient);
	}
	
	
	DbUtil::switchRestore($saved_db);
	return $retval;
}
?>
<html>
<head>
<?php
$page_elems = new PageElems();
$script_elems = new ScriptElems();
$script_elems->enableJQuery();
//$script_elems->enableTableSorter();
$script_elems->enableDragTable();
$script_elems->enableLatencyRecord();
$script_elems->enableEditInPlace();

$date_from = $_REQUEST['yf']."-".$_REQUEST['mf']."-".$_REQUEST['df'];
$date_to = $_REQUEST['yt']."-".$_REQUEST['mt']."-".$_REQUEST['dt'];
$lab_config_id = $_REQUEST['l'];
$cat_code = $_REQUEST['c'];
$ttype = $_REQUEST['t'];

$uiinfo = "from=".$date_from."&to=".$date_to."&ct=".$cat_code."&tt=".$ttype;
putUILog('daily_log_specimens', $uiinfo, basename($_SERVER['REQUEST_URI'], ".php"), 'X', 'X', 'X');

$lab_config = get_lab_config_by_id($lab_config_id);
$test_types = get_lab_config_test_types($lab_config_id);

$report_id = $REPORT_ID_ARRAY['reports_dailyspecimens.php'];
$report_config = $lab_config->getReportConfig($report_id);

$margin_list = $report_config->margins;
for($i = 0; $i < count($margin_list); $i++)
{
	$margin_list[$i] = ($SCREEN_WIDTH * $margin_list[$i] / 100);
}

if($ttype != 0)
{
	# Single test type selected
	$test_types = array();
	$test_types[] = $ttype;
}
else if($cat_code != 0)
{
	# Fetch all tests belonging to this category (aka lab section)
	$cat_test_types = TestType::getByCategory($cat_code);
	$cat_test_ids = array();
	foreach($cat_test_types as $test_type)
		$cat_test_ids[] = $test_type->testTypeId;
	$matched_test_ids = array_intersect($cat_test_ids, $test_types);
	$test_types = array_values($matched_test_ids);
}
?>
<script type="text/javascript" src="js/table2CSV.js"></script>
<script type='text/javascript'>
var curr_orientation = 0;
function export_as_word(div_id)
{
	var content = $('#'+div_id).html();
	$('#word_data').attr("value", content);
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

function export_as_csv(table_id)
{
	var content = $('#'+table_id).table2CSV({delivery:'value'});
	$("#csv_data").val(content);
	$('#csv_format_form').submit();
}

function report_fetch()
{ 	var yt= <?php echo $_REQUEST['yt'];?>;
	var yf=<?php echo $_REQUEST['yf'];?>;
	var mt=<?php echo $_REQUEST['mt'];?>;
	var mf=<?php echo $_REQUEST['mf'];?>;
	var dt=<?php echo $_REQUEST['dt'];?>;
	var df=<?php echo $_REQUEST['df'];?>;
	var l=<?php echo $_REQUEST['l'];?>;
	var cat_code=<?php echo $_REQUEST['c'];?>;
	var ttype=<?php echo $_REQUEST['t'];?>;
	var ip = 0;
	var p=0;
	if($('#ip').is(":checked"))
		ip = 1;
	if($('#p').is(":checked"))
		p = 1;
	var url = "reports_dailyspecimens.php?yt="+yt+"&mt="+mt+"&dt="+dt+"&yf="+yf+"&mf="+mf+"&df="+df+"&l="+l+"&c="+cat_code+"&t="+ttype+"&ip="+ip+"&p="+p;
	window.open(url);
	}

function print_content(div_id)
{
	var DocumentContainer = document.getElementById(div_id);
	var WindowObject = window.open("", "PrintWindow", "toolbars=no,scrollbars=yes,status=no,resizable=yes");
	var html_code = DocumentContainer.innerHTML;
	var do_landscape = $("input[name='do_landscape']:checked").attr("value");
	if(do_landscape == "Y")
		html_code += "<style type='text/css'> #report_config_content {-moz-transform: rotate(-90deg) translate(-300px); } </style>";WindowObject.document.writeln(html_code);
	WindowObject.document.close();
	WindowObject.focus();
	WindowObject.print();
	WindowObject.close();
	//javascript:window.print();
}

$(document).ready(function(){
	$('#report_content_table4').tablesorter();
	$('.editable').editInPlace({
		callback: function(unused, enteredText) {
			return enteredText; 
		},
		show_buttons: false,
		bg_over: "FFCC66"			
	});
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

$(document).ready(function(){

  change_orientation();

  // Reset Font Size
  var originalFontSize = $('#report_content').css('font-size');
   $(".resetFont").click(function(){
  $('#report_content').css('font-size', originalFontSize);
  $('#report_content table').css('font-size', originalFontSize);
  $('#report_content table th').css('font-size', originalFontSize);
  });
  // Increase Font Size
  $(".increaseFont").click(function(){
  	var currentFontSize = $('#report_content').css('font-size');
 	var currentFontSizeNum = parseFloat(currentFontSize, 10);
    var newFontSize = currentFontSizeNum*1.2;
		$('#report_content').css('font-size', newFontSize);
	$('#report_content table').css('font-size', newFontSize);
	$('#report_content table th').css('font-size', newFontSize);
	return false;
  });
  // Decrease Font Size
  $(".decreaseFont").click(function(){
  	var currentFontSize = $('#report_content').css('font-size');
 	var currentFontSizeNum = parseFloat(currentFontSize, 10);
    var newFontSize = currentFontSizeNum*0.8;
	$('#report_content').css('font-size', newFontSize);
	$('#report_content table').css('font-size', newFontSize);
	$('#report_content table th').css('font-size', newFontSize);
	return false;
  });
  
   $(".bold").click(function(){
  	 var selObj = window.getSelection();
		alert(selObj);
		selObj.style.fontWeight='bold';
	return false;
  });
});
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



.landscape_content {-moz-transform: rotate(90deg) translate(300px); }



.portrait_content {-moz-transform: translate(1px); rotate(-90deg) }

</style>

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
		<input type='radio' name='do_landscape' value='N'<?php
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
<input type='button' onclick="javascript:export_as_csv('report_content_table4');" value='<?php echo LangUtil::$generalTerms['CMD_EXPORTCSV']; ?>'></input>
&nbsp;&nbsp;
<?php if($_REQUEST['ip']==1){?><input type='radio' name='ip' id='ip' checked ></input> <?php echo LangUtil::$generalTerms['ALL_TESTS']; ?>
<?php } else{?><input type='radio' name='ip' id='ip'></input> <?php echo LangUtil::$generalTerms['ALL_TESTS']; }?>
&nbsp;&nbsp;&nbsp;&nbsp;
<?php if($_REQUEST['p']==1){?><input type='radio' name='ip' id='p' checked ></input> <?php echo LangUtil::$generalTerms['ONLY_PENDING']; ?>
<?php } else{?><input type='radio' name='ip' id='p'></input> <?php echo LangUtil::$generalTerms['ONLY_PENDING']; }?>
&nbsp;&nbsp;&nbsp;&nbsp;
<input type='button' onclick="javascript:report_fetch();" value='<?php echo LangUtil::$generalTerms['CMD_VIEW']; ?>'></input>
&nbsp;&nbsp;&nbsp;&nbsp;
<!-- <input type='button' onclick="javascript:window.close();" value='<?php echo LangUtil::$generalTerms['CMD_CLOSEPAGE']; ?>'></input> -->
&nbsp;&nbsp;&nbsp;&nbsp;
<?php $page_elems->getTableSortTip(); echo "<small>" . LangUtil::$generalTerms['DGAR_TABLE_COLUMNS'] . "</small>";?>
<hr>

<div id='export_content'>
<link rel='stylesheet' type='text/css' href='css/table_print.css' />
<style type='text/css'>
	<?php $page_elems->getReportConfigCss($margin_list, false); ?>
</style>
<div id='report_config_content' style='display:block;'>
<h3><?php echo LangUtil::$pageTerms[$report_config->headerText]; ?></h3>
<h3><?php echo $report_config->titleText; ?></h3>
<?php
 if($date_from == $date_to)
 {
	echo LangUtil::$generalTerms['DATE'].": ".DateLib::mysqlToString($date_from);
 }
 else
 {
	echo LangUtil::$generalTerms['FROM_DATE'].": ".DateLib::mysqlToString($date_from);
	echo " | ";
	echo LangUtil::$generalTerms['TO_DATE'].": ".DateLib::mysqlToString($date_to);
 }
$record_list = array();
foreach($test_types as $test_type_id)
{
	$record_list[] = get_records_to_print($lab_config, $test_type_id, $date_from, $date_to);
}
$total_tests = 0;
foreach($record_list as $record)
{
	$total_tests += count($record);
}
?>	
<br>
 <?php if($cat_code != 0){ echo LangUtil::$generalTerms['LAB_SECTION']; ?>: <?php }
	if($cat_code == 0)
	{
		//echo LangUtil::$generalTerms['ALL'];
	}
	else
	{
		$cat_name = get_test_category_name_by_id($cat_code);
		echo $cat_name;
	}
	 if($ttype != 0 && $cat_code != 0)
	 {
 ?>
 |<?php  
 }
 if($ttype != 0) { echo LangUtil::$generalTerms['TEST_TYPE']; ?>: <?php }
	if($ttype == 0)
	{
		//echo LangUtil::$generalTerms['ALL'];
	}
	else
	{
		$test_name = get_test_name_by_id($ttype);
		echo $test_name;
	
	}



if(count($test_types) == 0)
{
	?>
	<br><br>
	<b><?php echo $cat_name; ?></b> <?php echo LangUtil::$pageTerms['TIPS_RECNOTFOUND']; ?>
	<?php # Line for Signature ?>
	<br><br>
	.............................
	<h4><?php echo $report_config->footerText; ?></h4>
	<?php
	return;
}
$record_list = array();
foreach($test_types as $test_type_id)
{
	$record_list[] = get_records_to_print($lab_config, $test_type_id, $date_from, $date_to);
}
$total_tests = 0;
foreach($record_list as $record)
{
	$total_tests += count($record);
}
?>

<?php
$no_match = true;
foreach($record_list as $record)
{
	if($record == null)
		continue;
	if(count($record) == 0)
		continue;
	if(count($record[0]) != 0)
	{
		$no_match = false;
		break;
	}
}
if($no_match === true)
{
	?>
	<?php echo LangUtil::$pageTerms['TIPS_RECNOTFOUND']; ?>
	<?php # Line for Signature ?>
	<br><br>
	.............................
	<h4><?php echo $report_config->footerText; ?></h4>
	<?php
	return;
}
?>
<table class='print_entry_border' id='report_content_table4'>
<thead>
		<tr valign='top'>
			<?php
			if($report_config->useDailyNum == 1)
			{
				echo "<th>" . LangUtil::$generalTerms['VISIT_NUMBER'] . "</th>";
			}
			if($report_config->useSpecimenAddlId != 0)
			{
				echo "<th>".LangUtil::$generalTerms['SPECIMEN_ID']."</th>";
			}
			if($report_config->usePatientId == 1)
			{
			?>
				<!--<th><?php echo LangUtil::$generalTerms['PATIENT_ID']; ?></th>-->
			<?php
			}
			if($report_config->usePatientAddlId == 1)
			{
			?>
				<th><?php echo LangUtil::$generalTerms['ADDL_ID']; ?></th>
			<?php
			}
			if($report_config->usePatientName == 1)
			{
			?>
				<th><?php echo LangUtil::$generalTerms['NAME']; ?></th>
			<?php
			}
			if($report_config->useAge == 1)
			{
			?>
				<th><?php echo LangUtil::$generalTerms['AGE']; ?></th>
			<?php
			}
			if($report_config->useGender == 1)
			{
			?>			
				<th><?php echo LangUtil::$generalTerms['GENDER']; ?></th>
			<?php
			}
			if($report_config->useDob == 1)
			{
			?>
				<!--<th><?php echo LangUtil::$generalTerms['DOB']; ?></th>-->
			<?php 
			}
			# Patient Custom fields here
			$custom_field_list = $lab_config->getPatientCustomFields();
			foreach($custom_field_list as $custom_field)
			{
				if(in_array($custom_field->id, $report_config->patientCustomFields))
				{	
					$field_name = $custom_field->fieldName;				
					echo "<th>";
					echo $field_name;
					echo "</th>";
				}
			}
			if($report_config->useSpecimenName == 1)
			{
				echo "<th>Specimen Type</th>";
			}
			if($report_config->useDateRecvd == 1)
			{
				echo "<th>".LangUtil::$generalTerms['R_DATE']."</th>";
			}
			# Specimen Custom fields headers here
			$custom_field_list = $lab_config->getSpecimenCustomFields();
			foreach($custom_field_list as $custom_field)
			{
				$field_name = $custom_field->fieldName;
				$field_id = $custom_field->id;
				if(in_array($field_id, $report_config->specimenCustomFields))
				{
					echo "<th>".$field_name."</th>";
				}
			}
			if($report_config->useTestName == 1)
			{
				echo "<th>".LangUtil::$generalTerms['TEST_TYPE']."</th>";
			}
			if($report_config->useComments == 1)
			{
				echo "<th>".LangUtil::$generalTerms['INTERPRETATION']."</th>";
			}
			if($report_config->useReferredTo == 1)
			{
// 				echo "<th>".LangUtil::$generalTerms['REF_TO']."</th>";
			}
			if($report_config->useDoctor == 1)
			{
				echo "<th>".LangUtil::$generalTerms['DOCTOR']."</th>";
			}
			if($report_config->useResults == 1)
			{
				echo "<th>".LangUtil::$generalTerms['RESULTS']."</th>";
			}
			if($report_config->useRange == 1)
			{
				echo "<th style='width:120px;'>".LangUtil::$generalTerms['RANGE']."</th>";
			}
			if($report_config->useEntryDate == 1)
			{
				echo "<th>".LangUtil::$generalTerms['E_DATE']."</th>";
			}
			if($report_config->useRemarks == 1)
			{
// 				echo "<th>".LangUtil::$generalTerms['RESULT_COMMENTS']."</th>";
			}
			if($report_config->useEnteredBy == 1)
			{
				echo "<th>".LangUtil::$generalTerms['ENTERED_BY']."</th>";
			}
			if($report_config->useVerifiedBy == 1)
			{
				echo "<th>".LangUtil::$generalTerms['VERIFIED_BY']."</th>";
			}
			if($report_config->useStatus == 1)
			{
				echo "<th>".LangUtil::$generalTerms['SP_STATUS']."</th>";
			}
			?>
		</tr>
	</thead>
	<tbody>
	
	<?php
	$count = 1;
	# Loop here
	//ho "rl".count($record_list);
	foreach($record_list as $record_set_array)
	{ //ho "eeel".count($record_set_array);
		foreach($record_set_array as $record_set)
		{
		if(count($record_set) == 0)
			continue;
		$value = $record_set;
		$test = $value[0];
		$specimen = $value[1];
		$patient = $value[2];
		?>
		<tr valign='top'>
			<?php
			if($report_config->useDailyNum == 1)
			{
				echo "<td>".$patient->getDailyNum(); //$specimen->getDailyNum()."</td>";
			}
			if($report_config->useSpecimenAddlId == 1)
			{
				echo "<td>"./*$specimen->specimenId;*/$test->getLabSectionByTest();
				//$specimen->getAuxId();
				echo "</td>";
			}
			if($report_config->usePatientId == 1)
			{
			?>
				<!--<td><?php echo $patient->getPatientId(); //$patient->getSurrogateId(); ?></td>-->
			<?php
			}
			if($report_config->usePatientAddlId == 1)
			{
			?>
				<td><?php echo $patient->getAddlId(); ?></td>
			<?php
			}
			if($report_config->usePatientName == 1)
			{
			?>
				<td><?php echo $patient->name; ?></td>
			<?php
			}
			if($report_config->useAge == 1)
			{
			?>
				<td><?php echo $patient->getAge(); ?></td>
			<?php
			}
			if($report_config->useGender == 1)
			{
			?>			
				<td><?php echo $patient->sex; ?></td>
			<?php
			}
			if($report_config->useDob == 1)
			{
			?>
				<!--<td><?php echo $patient->getDob(); ?></td>-->
			<?php 
			}
			# Patient Custom fields here
			$custom_field_list = $lab_config->getPatientCustomFields();
			foreach($custom_field_list as $custom_field)
			{
				if(in_array($custom_field->id, $report_config->patientCustomFields))
				{	
					$field_name = $custom_field->fieldName;				
					$custom_data = get_custom_data_patient_bytype($patient->patientId, $custom_field->id);
					echo "<td>";
					if($custom_data == null)
					{
						echo "-";
					}
					else
					{
						$field_value = $custom_data->getFieldValueString($lab_config->id, 2);
						if(trim($field_value) == "")
							$field_value = "-";
						echo $field_value;
					}
					echo "</td>";					
				}
			}
			if($report_config->useSpecimenName == 1)
			{
				echo "<td>".get_specimen_name_by_id($specimen->specimenTypeId)."</td>";
			}
			if($report_config->useDateRecvd == 1)
			{
				echo "<td>".DateLib::mysqlToString($specimen->dateRecvd)."</td>";
			}
			# Specimen Custom fields here
			$custom_field_list = $lab_config->getSpecimenCustomFields();
			foreach($custom_field_list as $custom_field)
			{
				if(in_array($custom_field->id, $report_config->specimenCustomFields))
				{
					echo "<td>";
					$custom_data = get_custom_data_specimen_bytype($specimen->specimenId, $custom_field->id);
					if($custom_data == null)
					{
						echo "-";
					}
					else
					{
						$field_value = $custom_data->getFieldValueString($lab_config->id, 1);
						if($field_value == "" or $field_value == null) 
							$field_value = "-";
						echo $field_value; 
					}
					echo "</td>";
				}
			}
			if($report_config->useTestName == 1)
			{
				echo "<td>".get_test_name_by_id($test->testTypeId)."</td>";
			}
			if($report_config->useComments == 1)
			{
				echo "<td>";
				echo $specimen->getComments();
				echo "</td>";
			}
			if($report_config->useReferredTo == 1)
			{
// 				echo "<td>".$specimen->getReferredToName()."</td>";
			}
			if($report_config->useDoctor == 1)
			{
				echo "<td>".$specimen->getDoctor()."</td>";
			}
			if($report_config->useResults == 1)
			{
				echo "<td>";
				if(trim($test->result) == "")
				{
					echo LangUtil::$generalTerms['PENDING_RESULTS'];
				}
				else
				{
				
					echo $test->decodeResult();
				}
				echo "</td>";
			}
			if($report_config->useRange == 1)
			{
				echo "<td>";
				$test_type = TestType::getById($test->testTypeId);
				$measure_list = $test_type->getMeasures();
				foreach($measure_list as $measure)
						{
						$type=$measure->getRangeType();
						if($type==Measure::$RANGE_NUMERIC)
						{
						$range_list_array=$measure->getRangeString($patient);
					$lower=$range_list_array[0];
					$upper=$range_list_array[1];
						?>(
		<?php
			$unit=$measure->unit;
			if(stripos($unit,",")!=false)
		{	
			$units=explode(",",$unit);
			$lower_parts=explode(".",$lower);
			$upper_parts=explode(".",$upper);
			if($lower_parts[0]!=0)
			{
			echo $lower_parts[0];
			echo $units[0];
			}
			if($lower_parts[1]!=0)
			{
			echo $lower_parts[1];
			echo $units[1];
			}
			echo " - ";
			if($upper_parts[0]!=0)
			{
			echo $upper_parts[0];
			echo $units[0];
			}
			if($upper_parts[1]!=0)
			{
			echo $upper_parts[1];
			echo $units[1];
			}

		}
		else if(stripos($unit,":")!=false)
		{
		$units=explode(":",$unit);
			
		echo $lower;
		?><sup><?php echo $units[0]; ?></sup> - 
		<?php echo $upper;?> <sup> <?php echo $units[0]; ?> </sup>
		<?php
		echo " ".$units[1];
		}
		
		else
		{			
			echo $lower; ?>-<?php echo $upper; 
			echo " ".$measure->unit;
		}
		?>)&nbsp;&nbsp;	
			
			<?php
		//echo " ".$measure->unit;
	//	echo "<br>";
		
		
		
							
						}
						//echo $measure->getRangeString($patient);
						else
							echo "&nbsp;&nbsp;&nbsp;".$measure->unit;
							echo "<br>";
						}
				echo "</td>";
			}
			if($report_config->useEntryDate == 1)
			{
				echo "<td>";
				if(trim($test->result) == "")
					echo "-";
				else
				{
					$ts_parts = explode(" ", $test->timestamp);
					echo DateLib::mysqlToString($ts_parts[0]);
				}
				echo "</td>";
			}
			if($report_config->useRemarks == 1)
			{
// 				echo "<td>".$test->getComments()."</td>";
			}
			if($report_config->useEnteredBy == 1)
			{
				echo "<td>".$test->getEnteredBy()."</td>";
			}
			if($report_config->useVerifiedBy == 1)
			{
				echo "<td>".$test->getVerifiedBy()."</td>";
			}
			if($report_config->useStatus == 1)
			{
				echo "<td>".$test->getStatus()."</td>";
			}
			?>
		</tr>
		<?php
		$count++;
		}
	}
	?>
	</tbody>
	</table>
		<?php echo LangUtil::$pageTerms['TOTAL_TESTS']; ?>: <?php echo $total_tests; ?> 


	<br>
	<?php

?>

<?php # Line for Signature ?>
.............................
<h4><?php echo $report_config->footerText; ?></h4>
</div>
</div>
</div>
</body>
</html>

