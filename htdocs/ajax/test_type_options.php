<?php
#
# Returns HTML check boxes containing compatible test types
# Called via Ajax from new_specimen.php
#

include("../includes/db_lib.php");

LangUtil::setPageId("new_specimen");

$specimen_type_id = $_REQUEST['stype'];
$lab_config_id = $_SESSION['lab_config_id'];
$test_type_list = get_compatible_test_types($lab_config_id, $specimen_type_id);
$external_test_names = explode(",",$_REQUEST['ext']);

if(count($test_type_list) == 0)
{
	# No compatible tests exist in the configuration
	?>
	<span class='clean-error uniform_width'>
		<?php echo LangUtil::$pageTerms['MSG_NOTESTMATCH']; ?>
	</span>
	<?php
	return;
}
?>
<select data-placeholder="Select Tests" class="chosen span11" id='t_type_list' name='t_type_list[]' multiple="multiple">
<?php
$count = 0;

foreach($test_type_list as $test_type)
{
 
	$test_name = $test_type->getName();
?>
	<option value='<?php echo $test_type->testTypeId; ?>'
			 <?php
			 foreach($external_test_names as $external_test_name){
				if ($external_test_name == $test_name) echo "selected";
			 }
			# If only one option, select it
// 			 if(count($test_type_list) == 1)
// 				echo " selected ";
			 ?>
	><?php echo $test_type->getName();?></option>
	<?php 
	
}


?>
</select>

<?php 
echo "<table class='table'>";
echo "<theader>";
echo "<th>Panel Tests";
echo "</th>";
echo "<th>";
echo "</th>";
echo "<theader>";
echo "<tbody>";
#panel tests
foreach($test_type_list as $test_type)
{
if($test_type->isPanel && ($test_type->panel_child_test !="")){
$child_tests =$test_type->panel_child_test;   
$tests = explode(',', $child_tests);  
$testBits = array(); 
foreach ($tests as $test) { 
          $test = trim($test); 
          if (!empty($test)) { 
          
                $testBits[] = "<input type='checkbox' onClick='javascript:addTest($test)' value='$test' id='$test'>".$test_type->getNameById($test)."</input>"; 
                //$test_type=$testBits;
          } 
}
echo "<tr>";
echo "<td>";
echo $test_type->getName();
echo "</td>";
echo "<td>";
echo implode(' <br> ', $testBits);
echo "</td>";
echo "</tr>";
}

}
echo "</tbody>";
echo "</table>";


?>
<script>
function addTest(value){
var optvalue= value;
var clickedbox=$('input[onClick="javascript:addTest('+optvalue+')"]');
var optext=$('#t_type_list').find('option[value="'+optvalue+'"]').text();
if (optext !=""){
if(clickedbox.is(":checked")){
$('#t_type_list').find('option[value="'+optvalue+'"]').attr("selected",1).trigger("liszt:updated");
//$('#t_type_list').append('<option value="'+optvalue+'" selected>'+optext+'</option>').trigger("liszt:updated");
}
else{
$('#t_type_list').find('option[value="'+optvalue+'"]').removeAttr("selected").trigger("liszt:updated");
}
}
else{
clickedbox.attr("disabled","disabled");
clickedbox.removeAttr("checked");
alert('Test not compatible for this specimen');
}
}
$(".chosen").chosen();


</script>