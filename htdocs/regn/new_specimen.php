<?php
#
# Main page for registering new specimen(s) in a single session/accession
#
/*
$load_time = microtime(); 
$load_time = explode(' ',$load_time); 
$load_time = $load_time[1] + $load_time[0]; 
$page_start = $load_time; 
*/

include("redirect.php");
include("includes/header.php");
include("includes/scripts.php");
require_once("includes/script_elems.php");
LangUtil::setPageId("new_specimen");

$script_elems->enableDatePicker();
$script_elems->enableLatencyRecord();
$script_elems->enableJQueryForm();
$script_elems->enableAutocomplete();
$pid = $_REQUEST['pid'];

if(isset($_REQUEST['dnum']))
	$dnum = (string)$_REQUEST['dnum'];
else
	$dnum = get_daily_number();

if(isset($_REQUEST['session_num']))
	$session_num = $_REQUEST['session_num'];
else
	$session_num = get_session_number();
	
/* check discrepancy between dnum and session number and correct 
if ( substr($session_num,strpos($session_num, "-")+1 ) )
	$session_num = substr($session_num,0,strpos($session_num, "-"))."-".$dnum;
*/
	
$doc_array= getDoctorList();
$php_array= addslashes(implode("%", $doc_array));
	
$uiinfo = "pid=".$_REQUEST['pid']."&dnum=".$_REQUEST['dnum'];
putUILog('new_specimen', $uiinfo, basename($_SERVER['REQUEST_URI'], ".php"), 'X', 'X', 'X');
?>
	<script type="text/javascript" src="js/check_date_format.js"></script>
	<script>
  $(document).ready(function(){
//var data = "Core Selectors Attributes Traversing Manipulation CSS Events Effects Ajax Utilities".split(" ");
var data_string="<?php echo $php_array;?>";
var data=data_string.split("%");
$("#doc_row_1_input").autocomplete(data);
  });
  </script>
<script>
// <!-- <![CDATA[
specimen_count = 1;
patient_exists = false;
$(document).ready(function(){
	$('#specimen_id').focus();
	$('a[rel*=facebox]').facebox()
	<?php
	if(isset($_REQUEST['pid']))
	{
		echo "; get_patient_info('".$pid."');";
		echo " patient_exists = true;";
	}
	?>
});

function get_patient_info()
{
	var patient_id = <?php echo $_REQUEST['pid']; ?>;//$("#card_num").attr("value");
	if(patient_id == "")
	{
		$('#specimen_patient').html("");
		return;
	}
	$('#specimen_patient').load(
		"ajax/patient_info.php", 
		{
			pid: patient_id
		}, 
		function(){
			var return_html = $('#specimen_patient').html();
			if(return_html.indexOf("<?php echo LangUtil::$generalTerms['PATIENT']." ".LangUtil::$generalTerms['MSG_NOTFOUND']; ?>") == -1)
				patient_exists = true;
			else
				patient_exists = false;
		}
	);
}

function check_specimen_id(specimen_div_id, err_div_id)
{
	var specimen_id = $('#'+specimen_div_id).attr("value");
	if(specimen_id == "")
	{	
		$('#'+err_div_id).html("");
		return;
	}
	if(isNaN(specimen_id))
	{
		var msg_string = "<small><font color='red'>"+"Invalid ID. Only numbers allowed.</font></small>";
		$('#'+err_div_id).html(msg_string);
		return;
	}
	$('#'+err_div_id).load(
		"ajax/specimen_check_id.php", 
		{ 
			sid: specimen_id
		}
	);
}

function contains(a, obj){
  for(var i = 0; i < a.length; i++) {
    if(a[i] === obj){
      return true;
    }
  }
  return false;
}

function set_compatible_tests()
{
	var specimen_type_id = $("#s_type").attr("value");
	if(specimen_type_id == "")
	{	
		$('#test_type_box').html("Select specimen type to view compatible tests");
		return;
	}
	$('#test_type_box').load(
		"ajax/test_type_options.php", 
		{
			stype: specimen_type_id
		}
	);
}

function add_specimens()
{
	for(var j = 1; j <= specimen_count; j++)
	{
		// Validate each form
		var form_id = 'specimenform_'+j;
		var form_elem = $('#'+form_id);
		if(	form_elem == undefined || 
			form_elem == null )
			continue;
		if(	$("#"+form_id+" [name='stype']").attr("value") == null || 
			$("#"+form_id+" [name='stype']").attr("value") == undefined )
			continue;
		var stype = $("#"+form_id+" [name='stype']").attr("value");
		if(stype.trim() == "")
		{
			alert("<?php echo LangUtil::$generalTerms['ERROR'].": ".LangUtil::$pageTerms['MSG_STYPE_MISSING']; ?>");
			return;
		}
		var ttype_list = $("#"+form_id+" [name='t_type_list[]']");
		var ttype_notselected = true;
		for(var i = 0; i < ttype_list.length; i++)
		{
			//if(ttype_list[i].selected){
			for (var k=0; k < ttype_list[i].length; k++){
				//if (ttype_list[i].options.length>0){
				if (ttype_list[i][k].selected){
					ttype_notselected = false;
					break;
					break;
				}
			}
		}
		if(ttype_notselected == true)
		{
			alert("<?php echo LangUtil::$generalTerms['ERROR'].": ".LangUtil::$pageTerms['MSG_NOTESTS_SELECTED']; ?>");
			return;
		}
		var sid = $("#"+form_id+" [name='specimen_id']").attr("value");
		if(sid.trim() == "")
		{
			alert("<?php echo LangUtil::$generalTerms['ERROR'].": ".LangUtil::$pageTerms['MSG_SID_MISSING']; ?>");
			return;
		}
		var doctor = $("#"+form_id+" [name='doctor']").attr("value");
		if(doctor.trim() == "")
		{
			alert("<?php echo 'Please Enter Clinician Name to proceed' ?>");
			return;
		}
		var checkedValue = $("#"+form_id+" input[type='radio']:checked").val();
		var facility = $("#"+form_id+" [name='MFL_Code']").attr("value");
		//var facility_sel = $( "#MFL_Code option:selected" ).text();
		if(checkedValue.trim() == "2")
		{
		if (facility.trim() == ""){
			alert("Select a facility");
			return;
			}
		}
		
		var spec_reg_date = $("#"+form_id+" [name='spec_date']").attr("value");
		var curr_date = new Date();
		if (spec_reg_date!=""){
		var pt_regdate = new Date(spec_reg_date.slice(0, 4), parseInt(spec_reg_date.slice(5, 7))-1, spec_reg_date.slice(-2));
		if (curr_date<pt_regdate){

			alert("<?php echo LangUtil::$generalTerms['ERR_REGISTRATION_CANNOT_BE_AFTER_TODAY']; ?>");
			return;
		}
	}
		
		var specimen_valid = $("#specimen_msg_"+j).html();
		if(specimen_valid != "")
		{
			alert("<?php echo LangUtil::$generalTerms['ERROR'].": ".LangUtil::$pageTerms['MSG_SID_INVALID']; ?>");
			return;
		}
           /*
            * Call a  function to validate the format of the keyed in format
            */
            var lab_receipt_date = $("#"+form_id+" [name='spec_date']").attr("value");//get lab receipt date
            if (dt_format_check(lab_receipt_date, "Lab Receipt Date") == false)
            {return;}
            /* execute if the date is ok echiteri*/
		var ry = $("#"+form_id+" [name='receipt_yyyy']").attr("value");
		if (ry!=undefined) ry = ry.replace(/[^0-9]/gi,'');
		var rm = $("#"+form_id+" [name='receipt_mm']").attr("value");
		if (rm!=undefined) rm = rm.replace(/[^0-9]/gi,'');
		var rd = $("#"+form_id+" [name='receipt_dd']").attr("value");
		if (rd!=undefined) rd = rd.replace(/[^0-9]/gi,'');
		var cy = $("#"+form_id+" [name='collect_yyyy']").attr("value");
		if (cy!=undefined) cy = cy.replace(/[^0-9]/gi,'');
		var cm = $("#"+form_id+" [name='collect_mm']").attr("value");
		if (cm!=undefined) cm = cm.replace(/[^0-9]/gi,'');
		var cd = $("#"+form_id+" [name='collect_dd']").attr("value");
		if (cd!=undefined) cd = cd.replace(/[^0-9]/gi,'');
		var ch = $("#"+form_id+" [name='ctime_hh']").attr("value");
		if (ch!=undefined) ch = ch.replace(/[^0-9]/gi,'');
		var cmm = $("#"+form_id+" [name='ctime_mm']").attr("value");
		if (cmm!=undefined) cmm = cmm.replace(/[^0-9]/gi,'');
		if ((ry!=undefined) && (rm!=undefined) && (rd!=undefined)){
			if(checkDate(ry, rm, rd) == false)
			{
				var answer = confirm("<?php echo LangUtil::$generalTerms['ERROR'].": ".LangUtil::$pageTerms['MSG_RDATE_INVALID']; ?> . Are you sure you want to continue?");
				if (answer == false)
					return;
			}
		}
		if((cy!=undefined) && (cy.trim()=="")  && (cm!=undefined) && (cm.trim()=="") && (cd!=undefined) && (cd.trim()==""))
		{
			//Collection date not entered (optional field)
			//Do nothing
		}
		else
		{
			if ((cy!=undefined) && (cm!=undefined) && (cd!=undefined)){
				//Collection date entered. Check date string
				if(checkDate(cy, cm, cd) == false)
				{
					alert("<?php echo LangUtil::$generalTerms['ERROR'].": ".LangUtil::$pageTerms['MSG_CDATE_INVALID']; ?>");
					return;
				}
			}
		}
		//All okay
	}
	$('#progress_spinner').show();
	
	for(var j = 1; j <= specimen_count; j++)
	{
		// Submit each form
		var form_id = 'specimenform_'+j;

		$('#'+form_id).ajaxSubmit({async: false});
		//$('#'+form_id).submit();
	}
	var dnum_val = $('#dnum').attr("value");
	<?php
	$today = date("Ymd");
	switch($_SESSION['dnum_reset'])
	{
		case LabConfig::$RESET_DAILY:
			$today = date("Ymd");
			break;
		case LabConfig::$RESET_WEEKLY:
			$today = date("Y_W");
			break;
		case LabConfig::$RESET_MONTHLY:
			$today = date("Ym");
			break;
		case LabConfig::$RESET_YEARLY:
			$today = date("Y");
			break;
	}
	?>
	/*
	var dnum_string= "<?php echo $today; ?>";
	var url_string = "ajax/daily_num_update.php?dnum="+dnum_string+"&dval="+dnum_val;
	$.ajax({ url: url_string, async: false, success: function() {}}); 
	
	var url_string = "ajax/session_num_update.php?snum=<?php echo date("Ymd"); ?>";
	$.ajax({ url: url_string, async: false, success: function() {
		$('#progress_spinner').hide();
		window.location="specimen_added.php?snum=<?php echo $session_num; ?>";
	}});
	*/
	window.location="specimen_added.php?snum=<?php echo $session_num; ?>";
}
$(document).ready(function() {
    $('.date-picker').live('click', function() {
    $(this).datepicker('destroy').datepicker({showOn:'focus'}).focus();
        });
});
$(document).ready(function() {
    $('.bootstrap-timepicker-component').live('click', function() {
   // $('.timepicker-24').timepicker();
  
  $('.timepicker-24').timepicker({
            showMeridian: false     
        });
  
  
        });
});

function add_specimenbox()
{
	specimen_count++;
	var doc = $('#doc_row_1_input').attr("value");
	var title= $('#doc_row_1_title').attr("value");
	var dnumInit = "<?php echo $dnum; ?>";
	dnum = dnumInit.toString();
	var url_string = "ajax/specimenbox_add.php?num="+specimen_count+"&pid=<?php echo $pid; ?>"+"&dnum="+dnum+"&doc="+doc+"&title="+title+"&session_num=<?php echo $session_num; ?>";
	$('#sbox_progress_spinner').show();
	$.ajax({ 
		url: url_string, 
		success: function(msg){
			var currentTime = new Date()
			var hours = currentTime.getHours()
			var minutes = currentTime.getMinutes()
			$('#specimenboxes').append(msg);
			$('#sbox_progress_spinner').hide();
			$('#specimenform_'+specimen_count+'_ctime').val(hours + ':' + minutes);
		}
	});
}

function get_testbox(testbox_id, stype_id)
{
	var stype_val = $('#'+stype_id).attr("value");
	if(stype_val == "")
	{
		$('#'+testbox_id).html("-<?php echo LangUtil::$pageTerms['MSG_SELECT_STYPE']; ?>-");
		return;
	}
	$('#'+testbox_id).html("<?php $page_elems->getProgressSpinner(LangUtil::$generalTerms['CMD_FETCHING']); ?>");
	$('#'+testbox_id).load(
		"ajax/test_type_options.php", 
		{
			stype: stype_val
		}
	);
}

function show_dialog_box(div_id)
{
	var dialog_id = div_id+"_dialog";
	$('#'+dialog_id).show();
}

function hide_dialog_box(div_id)
{
	var dialog_id = div_id+"_dialog";
	$('#'+dialog_id).hide();
}

function remove_specimenbox(box_id)
{
	hide_dialog_box(box_id);
	specimen_count--;
	$('#'+box_id).remove();
}

function askandback()
{
	var todo = confirm("<?php echo LangUtil::$pageTerms['TIPS_SURETOABORT']; ?>");
	if(todo == true)
		history.go(-1);
}

function checkandtoggle(select_elem, div_id)
{
	var input_id = div_id+"_input";
	var report_to_val = select_elem.value;
	if(report_to_val == 1)
	{
		$('#'+div_id).hide();
	}
	else if(report_to_val == 2)
	{
		$('#'+div_id).show();
	}
	
}

function checkandtoggle_ref(ref_check_id, ref_row_id)
{
	if($('#'+ref_check_id).attr("checked") == true)
	{
		$('#'+ref_row_id).show();
	}
	else
	{
		$('#'+ref_row_id).hide();
	}
}
// And here is the end.

// ]]> -->
</script>

<p style="text-align: right;"><a rel='facebox' href='#NEW_SPECIMEN'><?php echo LangUtil::$generalTerms['PAGE_HELP']; ?></a></p>
<span class='page_title'><?php echo LangUtil::getTitle(); ?></span>
 | <?php echo LangUtil::$generalTerms['VISIT_NUMBER']:"; ?> <?php echo $session_num; ?>
 | <a href='javascript:history.go(-1);'><?php echo LangUtil::$generalTerms['CMD_CANCEL']; ?></a>
<br>
<br>
<?php
# Check if Patient ID is valid
$patient = get_patient_by_id($pid);
if($patient == null)
{
	?>
	<div class='sidetip_nopos'>
	<?php
	echo LangUtil::$generalTerms['ERROR'].": ".LangUtil::$generalTerms['PATIENT_ID']." ".$pid." ".LangUtil::$generalTerms['MSG_NOTFOUND']; ?>.
	<br><br>
	<a href='find_patient.php'>&laquo; <?php echo LangUtil::$generalTerms['CMD_BACK']; ?></a>
	</div>
	<?php
	include("includes/footer.php");
	return;
}
?>
<table cellpadding='5px'>
	<tbody>
		<tr valign='top'>
			<td>
				<span id='specimenboxes'>
				<?php echo $page_elems->getNewSpecimenForm(1, $pid, $dnum, $session_num); ?>
				</span>
				<br>
				<a href='javascript:add_specimenbox();'><?php echo LangUtil::$pageTerms['ADD_ANOTHER_SPECIMEN']; ?> &raquo;</a>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<span id='sbox_progress_spinner' style='display:none;'>
					<?php $page_elems->getProgressSpinner(LangUtil::$generalTerms['CMD_FETCHING']); ?>
				</span>
			</td>
			<td>
				<div>
					<?php echo $page_elems->getPatientInfo($pid, 300); ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<br>
&nbsp;&nbsp;
<input type="button" name="add_sched" class="btn green" id="add_button" onclick="add_specimens();" value="<?php echo LangUtil::$generalTerms['CMD_SUBMIT']; ?>" size="20" />
&nbsp;&nbsp;&nbsp;&nbsp;
<small><a href='javascript:askandback();' class="btn red icn-only"><?php echo LangUtil::$generalTerms['CMD_CANCEL']; ?></a></small>
&nbsp;&nbsp;&nbsp;&nbsp;
<div id='NEW_SPECIMEN' class='right_pane' style='display:none;margin-left:10px;'>
	<ul>
		<?php
		if(LangUtil::$pageTerms['TIPS_REGISTRATION_SPECIMEN']!="-") {
			echo "<li>";
			echo LangUtil::$pageTerms['TIPS_REGISTRATION_SPECIMEN'];
			echo "</li>";
		}	
		if(LangUtil::$pageTerms['TIPS_REGISTRATION_SPECIMEN_1']!="-") {
			echo "<li>";
			echo LangUtil::$pageTerms['TIPS_REGISTRATION_SPECIMEN_1'];
			echo "</li>";
		}	
		?>
	</ul>
</div>
<span id='progress_spinner' style='display:none;'>
	<?php $page_elems->getProgressSpinner(LangUtil::$generalTerms['CMD_SUBMITTING']); ?>
</span>
<br>
<?php 
/*
$load_time = microtime(); 
$load_time = explode(' ',$load_time); 
$load_time = $load_time[1] + $load_time[0]; 
$page_end = $load_time; 
$final_time = ($page_end - $page_start); 
$page_load_time = number_format($final_time, 4, '.', ''); 
echo("Page generated in " . $page_load_time . " seconds"); 
*/
include("includes/footer.php"); 
?>
<script type="text/javascript">
$(document).ready(function(){
	if (document.getElementById("rdb2_1").checked="true"){
		$('#blk-1').show(); //$('.toHide').show('slow');
	} else {
		$('#blk-1').hide(); //$('.toHide').hide();
	}
    /*$("[name=toggler]").click(function(){
		var opt = $(this).val();
		if (opt==="1"){
            $('#blk-1').hide(); //$('.toHide').hide();
        } else {
            $('#blk-1').show(); //$('.toHide').show('fast');
        }
    });*/
 });

function ShowFacility(form){
var form_id = 'specimenform_'+form;
	var opt = $("#"+form_id+" input[type='radio']:checked").val();
	//var formid = Element.id.substr(Element.id.lastIndexOf('_')+1);
	if (opt==="1"){
	$("#"+form_id+" tr[class='toHide']").hide();
		//$('#blk-'+formid).hide(); //$('.toHide').hide();
		$("#"+form_id+" input[name='MFL_Code']").attr('value','');
		$("#"+form_id+" input[name='MFL']").attr('value','');
	//$('#empty_opt').attr('selected','true');
} else {
		$("#"+form_id+" tr[class='toHide']").show(); //$('.toHide').show('fast');
		//$("#"+form_id+" option[id='empty_opt']").attr('selected','false');
	}
	
}
 
</script>
<script type="text/javascript">
$(document).ready(function(){
    $(".tooltip-examples").tooltip({
        placement : 'right'
    });


var data = {}; 
$("#MFL option").each(function(i,el) {  
   data[$(el).data("value")] = $(el).val();
});
// `data` : object of `data-value` : `value`
console.log(data, $("#MFL option").val());


    $('#MFL_Codeid').bind('input',function()
    {
    
        var value = $('#MFL_Codeid').val();
        var val=$('#MFL [value="' + value + '"]').data('value');
        $('#MFL_Code').attr('value',val);
        
    });
});
</script>

