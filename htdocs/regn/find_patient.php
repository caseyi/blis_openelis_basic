<?php
#
# (c) C4G, Santosh Vempala, Ruban Monu and Amol Shintre
# Main page for starting patient lookup
# 1st step of specimen registration
#
include("redirect.php");
include("includes/header.php");
LangUtil::setPageId("find_patient");
putUILog('find_patient', 'X', basename($_SERVER['REQUEST_URI'], ".php"), 'X', 'X', 'X');
$lab_config = get_lab_config_by_id($_SESSION['lab_config_id']);
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
				
<!-- BEGIN PATIENT SAMPLE REJECTION -->
<div id="sample_collection" class='reg_subdiv' style='display:none;'>
	<div class="portlet box blue">
		<div class="portlet-title">
			<h4><i class="icon-reorder"></i><?php echo LangUtil::$generalTerms['SAMPLE_COLLECTION']; ?></h4>
			<div class="tools">
			<a href="javascript:;" class="collapse"></a>
			<a href="javascript:fetch_patient_specimens_accept_reject();" class="reload"></a>
			</div>
		</div>
		<div class="portlet-body form">
		  <p style="text-align: right;"><a rel='facebox' href='#Rejection'><?php echo LangUtil::$generalTerms['PAGE_HELP']; ?></a></p>
		   <div class="alert alert-info" style="display: none">
                            <button class="close" data-dismiss="alert"></button>
                            <strong>You have successfully rejected the specimen.</strong>
                     </div>
			<div id='sample_collection_body' class="portlet" style='position:relative;left:10px; height: 500px'> 			       
		</div>
<div id='Rejection' class='right_pane' style='display:none;margin-left:10px;'>
                    <ul>
                        <li>This page facilitates acceptance or rejection of a collected specimen.</li>
                    </ul>
                </div>	 				
		</div>
	</div>
</div>
<!-- END PATIENT SAMPLE REJECTION -->


<!-- BEGIN LAB REQUESTS -->
<div id="lab_requests" class='reg_subdiv' style='display:none;'>
<div class="portlet box blue">
    <div class="portlet-title">
    <h4><i class="icon-reorder"></i><?php echo LangUtil::$generalTerms['SEARCH_LAB_REQUEST']; ?></h4>
            <div class="tools">
            <a href="javascript:;" class="collapse"></a>
            <!--a href="#portlet-config" data-toggle="modal" class="config"></a-->
            <a href="javascript:load_all_external_requests();" class="reload"></a>
            
            </div>
    </div>

    <div class="portlet-body form" >
            	<p style="text-align: right;"><a rel='facebox' href='#Registration'><?php echo LangUtil::$generalTerms['PAGE_HELP']; ?></a></p>
                
                <div id='add_anyway_div' >
                    <a class ="btn" id='add_anyway_link' href='javascript:load_patient_reg()'><i class='icon-plus'></i> <?php echo LangUtil::$pageTerms['ADD_NEW_PATIENT']; ?> &raquo;</a>
                	<a href='javascript:load_all_external_requests();' id="refresh" class="btn blue icn-only">
                	<?php echo LangUtil::$generalTerms['CMD_REFRESH']; ?> <i class="icon-refresh m-icon-white"></i>
					</a>
                </div>
               
                <div id='Registration' class='right_pane' style='display:none;margin-left:10px;'>
                    <ul>
                        <?php
                        if(LangUtil::$pageTerms['TIPS_REGISTRATION_1']!="-") {
                            echo "<li>";
                            echo LangUtil::$pageTerms['TIPS_REGISTRATION_1'];
                            echo "</li>";
                        }   
                        if(LangUtil::$pageTerms['TIPS_REGISTRATION_2']!="-") {
                            echo "<li>"; 
                            echo LangUtil::$pageTerms['TIPS_REGISTRATION_2'];
                            echo "</li>";
                        }
                        if(LangUtil::$pageTerms['TIPS_PATIENT_LOOKUP']!="-")    {
                            echo "<li>"; 
                            echo LangUtil::$pageTerms['TIPS_PATIENT_LOOKUP'];
                            echo "</li>"; 
                        }
                        ?>
                    </ul>
                </div>
           		
           		<div id="external_labreq" style="height: 400px">
                   
               </div> 
       </div> 
    </div>
        
</div>
<!-- END LAB REQUEST -->

<!-- BEGIN PATIENT REGISTRATION -->
<div id="patient_registration" class='reg_subdiv' style='display:none;'>
	<div class="portlet box blue">
		<div class="portlet-title">
			<h4><i class="icon-reorder"></i><?php echo LangUtil::$generalTerms['PATIENT_REGISTRATION']; ?></h4>
			<div class="tools">
				<a href="javascript:;" class="collapse"></a>
				<a href="javascript:load_patient_reg();" class="reload"></a>
				<a href="javascript:;" class="remove"></a>
			</div>
		</div>
		<div class="portlet-body form">
		<p style="text-align: right;"><a rel='facebox' href='#Registration'><?php echo LangUtil::$generalTerms['PAGE_HELP']; ?></a></p>
		<div class="span4" style="position: absolute;top: 180px;right: 30px;">
						<!-- BEGIN Portlet PORTLET-->
						<div class="">
											<div class="well text-info">
											<?php
											echo "<b>" . LangUtil::$generalTerms['TIPS'] . "</b>";
											echo "<li>";
											echo LangUtil::$generalTerms['TIPS_FIND_PATIENS_1'];
											echo "</li>";
										
											echo "<li>"; 
											echo LangUtil::$generalTerms['TIPS_FIND_PATIENS_2'];
											echo "</li>";
										
											echo "<li>"; 
											echo LangUtil::$generalTerms['TIPS_FIND_PATIENS_3'];
											echo "</li>";
											?>
												
											</div>
										</div>
					</div>
			<div id='patients_registration_body' style='position:relative;left:10px; '> </div>
					
		</div>
	</div>
</div>
<!-- END PATIENT REGISTRATION -->

<!-- BEGIN SPECIMEN REGISTRATION -->
<div id="specimen_reg" class='reg_subdiv' style='display:none;'>
	<div class="portlet box blue">
		<div class="portlet-title">
			<h4><i class="icon-reorder"></i><?php echo LangUtil::$generalTerms['REGISTER_LAB_REQUESTS']; ?></h4>

			<div class="tools">
				<a href="javascript:;" class="collapse"></a>
				<a href="javascript:load_specimen_reg(patient_id,is_external_patient);" class="reload"></a>
			</div>
		</div>
		<div class="portlet-body form">
			<div id='specimen_reg_body' style='position:relative;left:10px;'> </div>					
		</div>
	</div>
</div>
<!-- END SPECIMEN REGISTRATION -->

<!-- END LAB REQUESTS -->
</div>
<!-- END SPAN 12 -->
</div>

<!-- BEGIN SPECIMEN REJECTION-->	
<div id="specimen_rejection" class='reg_subdiv' style='display:none;'>
	<div class="portlet box yellow">
		<div class="portlet-title">

			<h4><i class="icon-reorder"></i><?php echo LangUtil::$generalTerms['MENU_SPECIMEN_REJECTION']; ?></h4>
			<div class="tools">
				<a href="javascript:;" class="collapse"></a>
				<a href="javascript:;" class="reload"></a>
				<a href="javascript:;" class="remove"></a>
			</div>
		</div>
		<div class="portlet-body form">
			<div id='specimen_rejection_body' style='position:relative;left:10px;'> </div>					
		</div>
	</div>
</div>
<!-- END SPECIMEN REJECTION--> 
  
<!-- BEGIN SPECIMEN ACCEPTANCE-->	
<div id="specimen_acceptance" class='reg_subdiv' style='display:none;'>
	<div class="portlet box green">
		<div class="portlet-title">
			<h4><i class="icon-reorder"></i><?php echo LangUtil::$generalTerms['SPECIMEN_ACCEPTENCE']; ?></h4>

			<div class="tools">
				<a href="javascript:;" class="collapse"></a>
				<a href="javascript:;" class="reload"></a>
				<a href="javascript:;" class="remove"></a>
			</div>
		</div>
		<div class="portlet-body form">
			<div id='specimen_acceptance_body' style='position:relative;left:10px;'> </div>					
		</div>
	</div>
</div>
<!-- END SPECIMEN ACCEPTANCE--> 
  
<div id="specimen_info" class="modal hide fade" tabindex="-1" data-backdrop="static" data-keyboard="true" style="width:900px;">
	  <div class="modal-body">
	   
	  </div>
	  <div class="modal-footer">
	    <button type="button" data-dismiss="modal" class="btn" onclick='javascript:cancel_hide()'><?php echo LangUtil::$generalTerms['NO']; ?></button>
	  
	  </div>
</div>

<?php
include("includes/scripts.php");
$script_elems->enableDatePicker();
$script_elems->enableTableSorter();
?>
<script type='text/javascript'>
$(document).ready(function() {
	$('#psearch_progress_spinner').hide();
	$('#add_anyway_link').attr("href", "javascript:load_patient_reg()");
	$('#pq').focus();
	$('#p_attrib').change(function() {
		$('#pq').focus();
	});
	load_all_external_requests();
	<?php
    if(isset($_REQUEST['show_sc']))
    {
        //Load sample collection table
        if($_REQUEST['show_sc'] == 1)   
        {
            ?>
            right_load("sample_collection");
            $(".alert.alert-info").show();
                setTimeout(function() { $(".alert.alert-info").hide(); }, 4000);
            <?php
        }
        else
        {
            ?>
            right_load("sample_collection");
            <?php    
        }
               
    }
    else if ($_REQUEST['div']=="reception"){
        ?>
        right_load("lab_requests");
        <?php 
    }
    else if ($_REQUEST['div']=="sample_collection"){
    	?>
            right_load("sample_collection");
            <?php 
        }
    ?>
});

function restrictCharacters(e) {
	
	var alphabets = /[A-Za-z]/g;
	var numbers = /[0-9]/g;
	if(!e) var e = window.event;
	if( e.keyCode ) code = e.keyCode;
	else if ( e.which) code = e.which;
	var character = String.fromCharCode(code);
	
	if( !e.ctrlKey && code!=9 && code!=8 && code!=27 && code!=36 && code!=37 && code!=38  && code!=40 &&code!=13 &&code!=32 ) {
		if ( !character.match(alphabets) && !character.match(numbers) && !character.match("/"))
			return false;
		else
			return true;
	}
	else
		return true;
}


// Function to load all pending lab requests from external lab request table

function load_all_external_requests(){
        
    var el = jQuery('.portlet .tools a.reload').parents(".portlet");
	App.blockUI(el);
    
    var url = 'ajax/search_p.php';
    $("#external_labreq").load(url, 
        {search_all_external: 1},
         function(response, status) 
        {
            App.unblockUI(el);
            handlePaginateDataTable('patientListTable');
            $("#external_labreq").removeAttr('style');
        }
     );
}

function specimen_info(specimen_id)
{
	var el = jQuery('.portlet .tools a.reload').parents(".portlet");
	App.blockUI(el);
	var url = 'search/specimen_info.php';
	var target_div = "specimen_info";
	$("#"+target_div).load(url, 
		{sid: specimen_id, modal:1}, 
		function() 
		{
			$('#'+target_div).modal('show');
			App.unblockUI(el);
		}
	);
}


function fetch_patients()
{
	$('#psearch_progress_spinner').show();
	var patient_id = $.trim($('#pq').val());
	patient_id = patient_id.replace(/[^a-z0-9 ]/gi,'');
	var search_attrib = $('#p_attrib').val();
	var check_url = "ajax/patient_check_name.php?n="+patient_id;
	$.ajax({ url: check_url, success: function(response){
			if(response == "false" && search_attrib == 1)
			{
				$('#psearch_progress_spinner').hide();
				//window.location="new_patient.php?n="+patient_id+"&jmp=1";
				$('#add_anyway_link').attr("href", "javascript:right_load('new_patient')");
			}
			else
			{
				continue_fetch_patients();
			}
		}
	});
}

function continue_fetch_patients()
{   
	var patient_id = $.trim($('#pq').val());
	patient_id = patient_id.replace(/[^a-z0-9 /]/gi,'');
	var search_attrib = $('#p_attrib').val();
	$('#psearch_progress_spinner').show();
	if(patient_id == "")
	{
		$('#psearch_progress_spinner').hide();
		$('#add_anyway_div').show();
		return;
	}
	var url = 'ajax/search_p.php';
	$("#patients_found").load(url, 
		{q: patient_id, a: search_attrib}, 
		function(response)
		{
			if(search_attrib == 1)
			{
				$('#add_anyway_link').html(" If not this name register '<b>"+patient_id+"</b>' <?php echo LangUtil::$pageTerms['ADD_NEW_PATIENT']; ?>&raquo;");
				$('#add_anyway_link').attr("href", "javascript:load_patient_reg()");
			}
			else
			{
				$('#add_anyway_link').html("If not this name register <?php echo LangUtil::$pageTerms['ADD_NEW_PATIENT']; ?> &raquo;");
				$('#add_anyway_link').attr("href", "javascript:load_patient_reg()");
			}
			$('#add_anyway_div').show();
			$('#psearch_progress_spinner').hide();
		}
		
	);
}

function right_load(destn_div)
{	
	$('.reg_subdiv').hide();
	$('.results_subdiv').hide();
	$("#"+destn_div).show();
	$('#specimen_id').focus();
	$('.menu_option').removeClass('current_menu_option');
	$('#'+destn_div+'_menu').addClass('current_menu_option');
	$('#'+destn_div+'_subdiv_help').show();
	
	if(destn_div == "lab_requests")
	{
		$('#sample_collection').hide();
	}
	else if(destn_div == "sample_collection"){
		$('#lab_requests').hide();
		fetch_patient_specimens_accept_reject();
		
	}
	
}
/**
 * FETCH PATIENTS AND SPECIMENS
 */
function fetch_patient_specimens_accept_reject()
{	
	var el = jQuery('.portlet .tools a.reload').parents(".portlet");
	App.blockUI(el);
	var url = 'ajax/patient_sample_accept_reject.php';
	$("#sample_collection_body").load(url, 
		{a: '', t: 10}, 
		function(response, status) 
		{
		    App.unblockUI(el);
		    handlePaginateDataTable(10);
		    handlePaginateDataTable('rejct_samples');
			$("#sample_collection_body").css({'height':''});
		}
	);	
}
function load_patient_reg()
{
	$('.reg_subdiv').hide();
	var patient_id = $.trim($('#pq').val());
	patient_id = patient_id.replace(/[^a-z0-9 ]/gi,'');
	var url = 'regn/new_patient2.php';
	$('#patients_registration_body').load(url, {n: patient_id});		
	$('#patient_registration').show();
}

function load_specimen_reg(patient_id, is_external_patient)
{
	$('.reg_subdiv').hide();
	//Load new_specimen2.php via ajax
	var url = 'regn/new_specimen2.php';
	$('#specimen_reg_body').load(url, {pid: patient_id, ex: is_external_patient});		
	$('#specimen_reg').show();
}

function load_specimen_rejection(specimen_id)
{
	$('.reg_subdiv').hide();
	var specimen_id = specimen_id;
	var url = 'regn/specimen_rejection.php';
	$('#specimen_rejection_body').load(url, {sid: specimen_id});		
	$('#specimen_rejection').show();
}
function load_specimen_acceptance(specimen_id)
{
	$('.reg_subdiv').hide();
	var specimen_id = specimen_id;
	var url = 'regn/specimen_acceptance.php';
	$('#specimen_acceptance_body').load(url, {sid: specimen_id});		
	$('#specimen_acceptance').show();
};

function CalcAge(DateElement, AgeElement){

var YearVal = DateElement.value.slice(0, 4);
var MonthVal = DateElement.value.slice(5, 7);
var DayVal = DateElement.value.slice(-2);
var CurrDate = new Date();

if (((MonthVal.length==0) && (DayVal.length==0)) || 
   ((MonthVal.length>0) && (DayVal.length>0) && (MonthVal-1==CurrDate.getMonth()) &&
   (DayVal==CurrDate.getDate()))){
   AgeElement.value=CurrDate.getFullYear()-YearVal;
} else {
   //var MSecPerDay = 1000*60*60*24;
   var BYear = YearVal;
   var BMonth = MonthVal>0 ? MonthVal : CurrDate.getMonth();
   var BDay = DayVal>0 ? DayVal : CurrDate.getDate();
   var RefDate = new Date(BYear, BMonth, BDay);
   var NumDays = DaysBetween(RefDate, CurrDate)-1;
   NumLeapYears = 0;
   ThisYear = CurrDate.getFullYear();
   for (var CurrYear=YearVal.value; CurrYear<=ThisYear; CurrYear++){
      var TargetDate = new Date(CurrYear, 2, 1);
      TargetDate.setHours(TargetDate.getHours()-3);
      var NumDaysFeb = TargetDate.getDate();
      NumLeapYears += (NumDaysFeb==29 ? 1 : 0);
   }
   alert(AgeElement.value);
   //AgeElement.value=parseInt((NumDays-NumLeapYears)/365);
}

}

</script>
<?php $script_elems->bindEnterToClick('#pq', '#psearch_button'); ?>
<?php include("includes/footer.php");?>

