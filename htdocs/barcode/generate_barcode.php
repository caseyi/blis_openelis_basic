<?php
include("redirect.php");
include("includes/header.php");
include("barcode_lib.php");

$barcodeSettings = get_lab_config_settings_barcode();
//  print_r($barcodeSettings);
$code_type = $barcodeSettings['type']; //"code39";
$bar_width = $barcodeSettings['width']; //2;
$bar_height = $barcodeSettings['height']; //40;
$font_size = $barcodeSettings['textsize']; //11;
putUILog('generate_barcode', '-', basename($_SERVER['REQUEST_URI'], ".php"), 'X', 'X', 'X');
?>
<style>
    .remove {
        position: relative;
    float: left;
}

.barcodes {
    
}

</style>
<script type="text/javascript">
    $(document).ready(function(){
	});
function getBarcode()
{
    var code = $('#code').val();
    if(code == '')
        {
             alert('<?php echo LangUtil::$generalTerms['CANNOT_BE_EMPTY']; ?>');
                return;
        }
    var count = parseInt($('#count').html()); 
    count = count + 1;
    $('#count').html(count);  
    var div = "bar"+count;
        generateRemoveLink(div);

    generateBarcode(div, code);
}

function generateBarcode(div, code)
{
    var content = "<div class='barcodes' id='"+div+"'></div>";
    $('#barcodeList').append(content);
        $("#"+div).barcode(code, '<?php echo $code_type; ?>',{barWidth:<?php echo $bar_width; ?>, barHeight:<?php echo $bar_height; ?>, fontSize:<?php echo $font_size; ?>, output:'css'});

}

function generateRemoveLink(div)
{
    var divR = div+"Remove";
    var oc = "hide_div('"+div+"')";
    //var content = "<div float class='remove' id ='"+divR+"' >"+"<img src='includes/img/knob_cancel.png' alt='Remove' onclick=\""+oc+"\";' />"+"</div>";
    var content = "<br><br><div float class='remove' id ='"+divR+"' >"+"<a onclick=\""+oc+"\"' ><?php echo LangUtil::$generalTerms['CMD_REMOVE']; ?></a>"+"</div>";
    $('#barcodeList').append(content);
}


function removeAllLinks(classname)
{
    $("."+classname).hide();
}


function showAllLinks(classname)
{
    $("."+classname).show();
}

function export_as_word(div_id)
{
	var content = $('#'+div_id).html();
	$('#word_data').attr("value", content);
	$('#word_format_form').submit();
}

function export_as_pdf(div_id)
{
            removeAllLinks('remove');
        div_id = 'bar1';
	var content = $('#'+div_id).html();
            content=content.replace(/\"/g,'\'');

	$('#pdf_data').attr("value", content);
	$('#pdf_format_form').submit();
}
function export_as_pdf2(div_id)
{
            removeAllLinks('remove');
            var content = $('#'+div_id).html();
            content=content.replace(/\"/g,'\'');

	$('#pdf_data').val(content);
	$('#pdf_format_form').submit();
        showAllLinks('remove');
}
    function PrintElem(elem)
    {
        removeAllLinks('remove');
        Popup($(elem).html());
    }

    function Popup(data) 
    {
        var mywindow = window.open('', 'my div', 'height=400,width=600');
        mywindow.document.write('<html><head><title><?php echo LangUtil::$generalTerms['BARCODES']; ?></title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.print();
        mywindow.close();
        //mywindow.document.show
        showAllLinks('remove');
        return true;
    }

function get_next(url, sno, cap)
{
    var count = parseInt($('#count').html()); 
    count = count + 1;
    $('#rem').html(rem);
    var displayed = tot - rem;
    if(displayed > tot)
        displayed = tot;
    $('#result_counts').html(displayed + '/' + tot + ' <?php echo LangUtil::$generalTerms['RESULTS']; ?>');
    $('.more_link').hide();
    url = url + '&rem=' + rem;
    var div_name = 'resultset'+sno;
    var html_content = "<div id='"+div_name+"'</div>";
    $('#data_table').append(html_content);
    $('#'+div_name).load(url);
}   

function hide_div(div)
{
    $('#'+div).remove();
     $('#'+div+'Remove').remove();
}
function export_to_pdf(div_id)
{
    var content = $('#'+div_id).html();
    content=content.replace(/\"/g,'\'');

    alert(content);
}
</script>

<?php

?>
<p style="text-align: right;"><a rel='facebox' href='#generate_barcode_help'><?php echo LangUtil::$generalTerms['PAGE_HELP']; ?></a></p>

<div id="count" style="display: none;">0</div>

Code: <input type="text" id="code" name="code"></input>
<input type="button" onclick='getBarcode();' value="<?php echo LangUtil::$generalTerms['CMD_GENERATE']; ?>">  
<input type="button" value="<?php echo LangUtil::$generalTerms['CMD_PRINT']; ?>" onclick="PrintElem('#barcodeList')" />
<!--
<!-- <input type='button' onclick="javascript:export_as_word('barcodeList');" value='<?php echo LangUtil::$generalTerms['CMD_EXPORTWORD']; ?>'></input> -->
<input type='button' onclick="javascript:export_as_pdf2('barcodeList');" value='<?php echo LangUtil::$generalTerms['CMD_EXPORTPDF']; ?>'></input>
-->
<form name='word_format_form' id='word_format_form' action='export_word.php' method='post' target='_blank'>
	<input type='hidden' name='data' value='' id='word_data' />
</form>
<!--
<form name='pdf_format_form' id='pdf_format_form' action='barcode/export_to_pdf_barcodes.php' method='post' target='_blank'>
	<input type='hidden' name='content_for_pdf' value='' id='pdf_data' />
</form>
-->
<div id="barcode_textarea" style="display:none;">
<form name='pdf_format_form' id='pdf_format_form' action='barcode/export_to_pdf_barcodes.php' method='post' target='_blank'>
    <textarea  name='content_for_pdf' value='' id='pdf_data' ></textarea>
</form>
</div>
<hr width="50%" align="left">

<div id="barcodeList">

</div>

<div id='generate_barcode_help' class='right_pane' style='display:none;margin-left:10px;'>
<ul>	
        <?php
                echo "<li>";
                echo LangUtil::$generalTerms['TIPS_GENERATE_BC_1'];
                echo "</li>";
                echo "<li>";
                echo LangUtil::$generalTerms['TIPS_GENERATE_BC_2'];
                echo "</li>";
                echo "<li>";
                echo LangUtil::$generalTerms['TIPS_GENERATE_BC_3'];
                echo "</li>";
                echo "<li>";
                echo LangUtil::$generalTerms['TIPS_GENERATE_BC_4'];
                echo "</li>";
                echo "<li>";
                echo LangUtil::$generalTerms['TIPS_GENERATE_BC_5'];
                echo "</li>";
        ?>
</ul>
</div>
<?php include("includes/footer.php"); ?>