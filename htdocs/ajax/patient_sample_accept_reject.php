<?php
#
# Returns list of patients matched with list of samples awaiting acceptance/rejection
# Called via Ajax form result_entry.php
#
include("../includes/db_lib.php");
include("../includes/user_lib.php");
LangUtil::setPageId("results_entry");

$attrib_value = $_REQUEST['a'];
$attrib_type = $_REQUEST['t'];
$dynamic = 1;
$search_settings = get_lab_config_settings_search();
$rcap = $search_settings['results_per_page'];
$lab_config = LabConfig::getById($_SESSION['lab_config_id']);

//echo "Bungoma District Hospital";
if(!isset($_REQUEST['result_cap']))
    $result_cap = $rcap;
else
    $result_cap = $_REQUEST['result_cap'];

if(!isset($_REQUEST['result_counter']))
    $result_counter = 1;
else
    $result_counter = $_REQUEST['result_counter'];

$query_string_not_collected = "";

            # Get all specimens with pending status
            $query_string_not_collected = 
                    "SELECT s.specimen_id FROM specimen s, test t, patient p ".
                    "WHERE p.patient_id=s.patient_id ".
                    "AND (s.status_code_id=".Specimen::$STATUS_NOT_COLLECTED.") ".
                    "AND s.specimen_id=t.specimen_id ".
                    "AND t.result = '' order by s.date_recvd DESC, s.time_collected DESC limit 200";

$query_string_rejected = "";

            # Get all specimens with pending status
            $query_string_rejected = 
                    "SELECT s.specimen_id FROM specimen s, test t, patient p ".
                    "WHERE p.patient_id=s.patient_id ".
                    "AND (s.status_code_id=".Specimen::$STATUS_REJECTED.") ".
                    "AND s.specimen_id=t.specimen_id ".
                    "AND t.result = '' order by s.date_recvd DESC, s.time_collected DESC limit 200";


$resultset_not_collected = query_associative_all($query_string_not_collected, $row_count);
$resultset_rejected = query_associative_all($query_string_rejected, $row_count);

$specimen_id_list_nc = array();
foreach($resultset_not_collected as $record_nc)
{
	$specimen_id_list_nc[] = $record_nc['specimen_id'];
}
# Remove duplicates that might come due to multiple pending tests
$specimen_id_list_nc = array_values(array_unique($specimen_id_list_nc));

$specimen_id_list_rj = array();
foreach($resultset_rejected as $record_rj)
{
    $specimen_id_list_rj[] = $record_rj['specimen_id'];
}
# Remove duplicates that might come due to multiple pending tests
$specimen_id_list_rj = array_values(array_unique($specimen_id_list_rj));

?>
<div class="tabbable tabbable-custom">                                          
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_1_1" data-toggle="tab"><?php echo LangUtil::$generalTerms['PENDING_SAMPLES']; ?></a></li>
            <li class=""><a href="#tab_1_2" data-toggle="tab"><?php echo LangUtil::$generalTerms['REJECTED_SAMPLES']; ?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1_1">
                <?php
                    if(count($resultset_not_collected) == 0 || $resultset_not_collected == null)
                            {
                                ?>
                                <div class='sidetip_nopos'>
                                <?php 
                                if($attrib_type == 0)
                                    echo " ".LangUtil::$generalTerms['PATIENT_ID']." ";
                                else if($attrib_type == 1)
                                    echo " ".LangUtil::$generalTerms['PATIENT_NAME']." ";
                                else if($attrib_type == 3)
                                    echo " ".LangUtil::$generalTerms['PATIENT_DAILYNUM']." ";
                                    if($attrib_type == 9)
                                    {
                                        echo LangUtil::$pageTerms['MSG_PENDINGNOTFOUND'];
                                        echo '<br>'.LangUtil::$generalTerms['TRY_SEARCH_BY_PAT_NAME'];
                                    }
                                    else
                                    {
                                echo "<b>".$attrib_value."</b>";
                                echo " - ".LangUtil::$pageTerms['MSG_PENDINGNOTFOUND'];
                                    }
                                ?>
                                </div>
                                <?php
                            }
                                else { 
                    ?>
                <table class="tablesorter table tale-striped table-condensed" id="<?php echo $attrib_type; ?>">
                <thead>
                    <tr>
                    	<?php 
                    	if($_SESSION['sid'] != 0)
                        {
                        ?>
                            <th style='width:75px;'><?php echo LangUtil::$generalTerms['SPECIMEN_ID']; ?></th>
                        <?php
                        }?>
                         	<th><?php echo LangUtil::$generalTerms['TIME_REGISTERED']; ?></th>
                        <?php
                        if($_SESSION['pid'] != 0)
                        {
                        ?>
                            <th style='width:75px;'><?php LangUtil::$generalTerms['PATIENT_ID']; ?></th>
                        <?php
                        }
                        if(false) //Not displaying Lab no
                        {
                        ?>
                            <th style='width:100px;'><?php LangUtil::$generalTerms['VISIT_NO']; ?></th>
                        <?php
                        }
                        if($_SESSION['p_addl'] != 0)
                        {
                        ?>
                            <th style='width:75px;'><?php echo LangUtil::$generalTerms['ADDL_ID']; ?></th>
                        <?php
                        }
                        if($_SESSION['s_addl'] != 0)
                        {
                        ?>
                            <th style='width:75px;'><?php echo LangUtil::$generalTerms['SPECIMEN_ID']; ?></th>
                        <?php
                        }
						//Removing Blocking showing patient Name based on user level
                        //if($_SESSION['user_level'] == $LIS_TECH_SHOWPNAME)
                        ?>
                        <th style='width:200px;'><?php echo LangUtil::$generalTerms['PATIENT_NAME']; ?></th>
                        <th style='width:100px;'><?php echo LangUtil::$generalTerms['SPECIMEN_TYPE']; ?></th>
                        <th style='width:100px;'><?php echo LangUtil::$generalTerms['TESTS']; ?></th>
                        <th style='width:130px;'><?php echo LangUtil::$generalTerms['STATUS']; ?></th>
                        <th style='width:130px;'><?php echo LangUtil::$generalTerms['ACCEPT'] . "/" . LangUtil::$generalTerms['REJECT']; ?></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
            <?php
                $count = 1;
                foreach($specimen_id_list_nc as $specimen_id)
                    {
                    $specimen = get_specimen_by_id($specimen_id);
                    $patient = get_patient_by_id($specimen->patientId);
                    ?>
                    <tr <?php
                    if($attrib_type == 3 && $count != 1)
                    {
                        # Fetching by patient daily number. Hide all records except the latest one
                        echo " class='old_pnum_records' style='display:none' ";
                    }
                    ?> id="<?php echo $specimen->specimenId; ?>">
                   	 	<?php if($_SESSION['dnum'] != 0)
                        {
                        ?>
                            <td style='width:100px;'><?php echo $specimen->getLabSection();  ?></td>
                        <?php
                        }
                        ?>
                         <td style='width:100px;'> <?php echo $specimen->dateRecvd." ".$specimen->timeCollected;?></td>
                        <?php
                        if($_SESSION['pid'] != 0)
                        {
                        ?>
                            <td style='width:75px;'><?php echo $patient->getSurrogateId(); ?></td>
                        <?php
                        }
                        if($_SESSION['p_addl'] != 0)
                        {
                        ?>
                            <td style='width:75px;'><?php echo $patient->getAddlId(); ?></td>
                        <?php
                        }
                        //if($_SESSION['sid'] != 0)
                        // "Specimen ID" now refers to aux_id
                        if(false)
                        {
                        ?>
                            <td style='width:75px;'><?php echo $specimen->specimenId; ?></td>
                        <?php
                        }
                        if($_SESSION['s_addl'] != 0)
                        {
                        ?>
                            <td style='width:75px;'><?php echo $specimen->getAuxId(); ?></td>
                        <?php
                        }
                        //if($lab_config->hidePatientName == 0)
                        //if($_SESSION['user_level'] == $LIS_TECH_SHOWPNAME)
                        //{
                        ?>
                        <td style='width:200px;'><?php echo $patient->getName()." (".$patient->sex." ".$patient->getAgeNumber().") "; ?></td>
                        <td style='width:100px;'><?php echo get_specimen_name_by_id($specimen->specimenTypeId); ?></td>
                        <td style='width:100px;'>
                        <?php
                        $test_list = get_tests_by_specimen_id($specimen->specimenId);
                        $i = 0;
                        foreach($test_list as $test)
                        {
                           	$test_type = get_test_type_by_id($test->testTypeId);
                           	$parent_tt_id = $test_type->parent_test_type_id;
                           	// $test->
                           	if ($parent_tt_id==0){
	                        	echo get_test_name_by_id($test->testTypeId);
	                            $i++;
	                            if($i != count($test_list))
	                            {
	                                echo "<br>";
	                            }
	                        }
                        }
                        ?>
                        </td>
                        <td>
                        <span class="label"> <?php echo LangUtil::$generalTerms['NOT_ACCEPTED']; ?></span>
                        </td>
                        
                        <?php if($attrib_type == 10)
                        {?>
                        <td style='width:130px;'><a href="specimen_acceptance.php?sid=<?php echo $specimen->specimenId; ?>&pid=<?php echo $patient->patientId; ?>" class="btn mini green"><i class="icon-thumbs-up"></i> <?php echo LangUtil::$generalTerms['ACCEPT']; ?></a>
                        <a href="javascript:load_specimen_rejection(<?php echo $specimen->specimenId; ?>)" class="btn mini yellow"><i class="icon-thumbs-down"></i> <?php echo LangUtil::$generalTerms['REJECT']; ?></a>
                        </td>
                        <td style="width:250px;"><a href="javascript:specimen_info(<?php echo $specimen->specimenId; ?>);" title="<?php echo LangUtil::$generalTerms['VIEW_TEST_DETAILS']; ?>" class="btn blue mini">
							<i class="icon-search"></i> <?php echo LangUtil::$generalTerms['TEST_DETAILS']; ?></a>
						</td>
                        <?php }?>
                    </tr>
                    
                    <div class='result_form_pane' id='result_form_pane_<?php echo $specimen->specimenId; ?>'>
                    </div>
            
                    <?php
                    $count++;
                }
                ?>
                </tbody>
            </table>
            <?php } ?>
            </div>
            
            
            <div class="tab-pane" id="tab_1_2">
                 <?php
                    if(count($resultset_rejected) == 0 || $resultset_rejected == null)
                            {
                                ?>
                                <div class='sidetip_nopos'>
                                <?php 
                                if($attrib_type == 0)
                                    echo " ".LangUtil::$generalTerms['PATIENT_ID']." ";
                                else if($attrib_type == 1)
                                    echo " ".LangUtil::$generalTerms['PATIENT_NAME']." ";
                                else if($attrib_type == 3)
                                    echo " ".LangUtil::$generalTerms['PATIENT_DAILYNUM']." ";
                                    if($attrib_type == 9)
                                    {
                                        echo LangUtil::$pageTerms['MSG_PENDINGNOTFOUND'];
                                        echo '<br>'.LangUtil::$generalTerms['TRY_SEARCH_BY_PAT_NAME'];
                                    }
                                    else
                                    {
                                echo "<b>".$attrib_value."</b>";
                                echo " - ".LangUtil::$pageTerms['MSG_PENDINGNOTFOUND'];
                                    }
                                ?>
                                </div>
                                <?php
                            }
                                else { 
                    ?>
                <table class="table tablesorter table-striped table-bordered table-condensed" id="rejct_samples">
                <thead>
                    <tr>
                        <?php
                        if($_SESSION['pid'] != 0)
                        {
                        ?>
                            <th style='width:75px;'><?php echo LangUtil::$generalTerms['PATIENT_ID']; ?></th>
                        <?php
                        }
                        if(false) //Not displaying Lab no
                        {
                        ?>
                            <th style='width:100px;'><?php echo "Lab. No"; ?></th>
                        <?php
                        }
                        if($_SESSION['p_addl'] != 0)
                        {
                        ?>
                            <th style='width:75px;'><?php echo LangUtil::$generalTerms['ADDL_ID']; ?></th>
                        <?php
                        }
                        if($_SESSION['sid'] != 0)
                        {
                        ?>
                            <th style='width:75px;'><?php echo LangUtil::$generalTerms['SPECIMEN_ID']; ?></th>
                        <?php
                        }
                        if($_SESSION['s_addl'] != 0)
                        {
                        ?>
                            <th style='width:75px;'><?php echo LangUtil::$generalTerms['SPECIMEN_ID']; ?></th>
                        <?php
                        }
                        //if($lab_config->hidePatientName == 0)
                        if($_SESSION['user_level'] == $LIS_TECH_SHOWPNAME)
                        {
                        ?>
                            <th style='width:200px;'><?php echo LangUtil::$generalTerms['PATIENT_NAME']; ?></th>
                        <?php
                        }
                        else
                        {
                        ?>
                        <th style='width:100px;'><?php echo LangUtil::$generalTerms['GENDER']."/".LangUtil::$generalTerms['AGE']; ?></th>
                        <?php
                        }
                        ?>
                        <th style='width:100px;'><?php echo LangUtil::$generalTerms['SPECIMEN_TYPE']; ?></th>
                        <th style='width:100px;'><?php echo LangUtil::$generalTerms['TESTS']; ?></th>
                        <th style='width:100px;'><?php echo LangUtil::$generalTerms['REJECTION_REPORT']; ?></th>
                    </tr>
                </thead>
                <tbody>
            <?php
                $count = 1;
                foreach($specimen_id_list_rj as $specimen_id)
                    {
                    $specimen = get_specimen_by_id($specimen_id);
                    $patient = get_patient_by_id($specimen->patientId);
                    ?>
                    <tr <?php
                    if($attrib_type == 3 && $count != 1)
                    {
                        # Fetching by patient daily number. Hide all records except the latest one
                        echo " class='old_pnum_records' style='display:none' ";
                    }
                    ?> id="<?php echo $specimen->specimenId; ?>">
                        <?php
                        if($_SESSION['pid'] != 0)
                        {
                        ?>
                            <td style='width:75px;'><?php echo $patient->getPatientID(); ?></td>
                        <?php
                        }
                        //if($_SESSION['dnum'] != 0)
                        if(false)
                        {
                        ?>
                            <td style='width:100px;'><?php echo $specimen->getDailyNumFull(); ?></td>
                        <?php
            
                        }
                        if($_SESSION['p_addl'] != 0)
                        {
                        ?>
                            <td style='width:75px;'><?php echo $patient->getAddlId(); ?></td>
                        <?php
                        }
                        if($_SESSION['sid'] != 0)
                        {
                        ?>
                            <td style='width:75px;'><?php echo $specimen->getLabSection(); ?></td>
                        <?php
                        }
                        if($_SESSION['s_addl'] != 0)
                        {
                        ?>
                            <td style='width:75px;'><?php echo $specimen->getAuxId(); ?></td>
                        <?php
                        }
                        //if($lab_config->hidePatientName == 0)
                        if($_SESSION['user_level'] == $LIS_TECH_SHOWPNAME)
                        {
                        ?>
                            <td style='width:200px;'><?php echo $patient->getName()." (".$patient->sex." ".$patient->getAgeNumber().") "; ?></td>
                        <?php
                        }
                        else
                        {
                        ?>
                            <td style='width:100px;'><?php echo $patient->sex."/".$patient->getAgeNumber(); ?></td>
                        <?php
                        }
                        ?>
                        <td style='width:100px;'><?php echo get_specimen_name_by_id($specimen->specimenTypeId); ?></td>
                        <td style='width:100px;'>
                        <?php
                        $test_list = get_tests_by_specimen_id($specimen->specimenId);
                        $i = 0;
                        foreach($test_list as $test)
                        {
                            echo get_test_name_by_id($test->testTypeId);
                            $i++;
                            if($i != count($test_list))
                            {
                                echo "<br>";
                            }
                        }
                        ?>
                        </td>
                        <?php if($attrib_type == 10)
                        {?>
                        <td style='width:100px;'><a href="report_onetesthistory.php?ppid=<?php echo $patient->getPatientID() ?>&spid=<?php echo $specimen->specimenId ?>" target="_blank" class="btn mini green"><i class="icon-thumbs-up"></i> <?php echo LangUtil::$generalTerms['VIEW_REPORT']; ?></a>
                        </td>
                        <?php }?>
                    </tr>
                    
                    <div class='result_form_pane' id='result_form_pane_<?php echo $specimen->specimenId; ?>'>
                    </div>
            
                    <?php
                    $count++;
                }
                ?>
                </tbody>
            </table>
             <?php } ?>
            </div>
        </div>
</div>
<div id="barcodeData" style="display:none;">
<br><br>
<div id="specimenBarcodeDiv"></div>
</div>
