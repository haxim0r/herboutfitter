<?php require_once 'header_nested.php'; ?>

<script>document.body.className = "childFrameBody";</script>
<div id="div_iframeBody">
    
<link rel='stylesheet' href='/css/fullcalendar/fullcalendar.css'>
<link rel="stylesheet" href="/css/contextMenu/jquery.contextMenu.css" />
<link rel="stylesheet" href="/css/jquery_ui/jquery-ui.min.css" />

<script src='/js/moment.min.js'></script>
<script src='/js/fullcalendar.js'></script>
<script src="/js/jquery.contextMenu.js"></script>
<script src="/js/jquery-ui-1.11.4/jquery-ui.min.js"></script>

<p>Please choose a day from the calendar to schedule an outing.</p>

<div id="daCalendar"></div>

<div id="reg-diag" title="Registration form" style="display: none;">
    <form method="post" id='reg-form' name='reg-form'>
        <fieldset>
            <div style="display: none;">
                <!--<input type="text" name="id" id="id" disabled>-->
                <input type="text" name="event_type" id="event_type" disabled>
                <input type="text" name="event_date" id="event_date" disabled>
            </div>
            
            <label for='firstname1' style="display: inline-block; width: 30%; text-align: right;">Firstname</label>
            <input type='text' name='firstname1' id='firstname1' value='<?=getUserData('firstname');?>' style="display: inline-block; width: 60%; text-align: left;">
            
            <label for='lastname1' style="display: inline-block; width: 30%; text-align: right;">Lastname</label>
            <input type='text' name='lastname1' id='lastname1' value='<?=getUserData('lastname');?>' style="display: inline-block; width: 60%; text-align: left;">
            
            <label for='email1' style="display: inline-block; width: 30%; text-align: right;">E-mail</label>
            <input type='text' name='email1' id='email1' value='<?=getUserData('email');?>' style="display: inline-block; width: 60%; text-align: left;">
            
            <label for='phone1' style="display: inline-block; width: 30%; text-align: right;">Phone</label>
            <input type='text' name='phone1' id='phone1' value='<?=getUserData('phone');?>' style="display: inline-block; width: 60%; text-align: left;">

            <div id="plus1" style="width: 100%; text-align: right;">
                <a href="javascript: beEzafeYek();">+ one</a>
            </div>
            
            <!-- Allow form submission with keyboard without duplicating the dialog button -->
            <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
        </fieldset>
    </form>
</div>

<script>
grayOut(true);
//-=-=-=-=- reference: http://fullcalendar.io
$('#daCalendar').fullCalendar({
    height: "auto",
    loading: function(bool){
        if(bool){
            grayOut(true);
        }
        else{
            resizeIframe();
            grayOut(false);
        }
    },
    events: '?ajax=userEvents&id=<?=getUserData("id");?>',
    dayClick: function(date, jsEvent, view) {

        $(this)[0].id = date.format();
        buildEventOptions($(this)[0]);
    },
    eventClick: function(calEvent, jsEvent, view) {
        
        var userOptions = 
        {"cancel":
            {name: "Cancel registration", icon: "delete", callback: 
                function(itemKey, opt){

                    var sql = "delete from registration where id="+calEvent.id;

                    var daData = {ajax: 'SQL', statement: sql};

                    var request = $.ajax({
                        method: "POST",
                        data: daData
                    });
                    request.done(function(msg){ //-=-=-=- set form data
                        navigate('registrar.php');
                    });
                    request.fail(function(jqXHR, textStatus){
                        top.grayOut(false);
                        alert("Request failed: " + textStatus);
                    });
                }
            }
        };

        $.contextMenu({
            selector: 'div.'+jsEvent.toElement.className,
            trigger: 'left',
            build: function(){
                return {items: userOptions};
            }
        });
    }
});

<?php
$mySQL = new db_mysql();

$sql = "select id, title from event_type where end >= now() and status in('new', 'active') order by sort, title";
//error_log($sql);
$resultset = mysqli_query($mySQL->connection, $sql);

if($resultset){

    $tmpStr = "";
    
    while ($row = $resultset->fetch_assoc()) {
        
        $tmpStr .= ',"register-'.$row['id'].'": {name: "'.$row['title'].'",'
                . ' icon: "add", '
                . 'callback: '
                . 'function(itemKey, opt){'
                //. 'console.log("Clicked on " + itemKey + " on element " + opt.$trigger.attr("id"));'
                . 'var daTitle = "'.$row['title'].' registration for: " + this[0].id;'
                . 'document.getElementById("reg-diag").setAttribute("title", daTitle);'
                . '$("span.ui-dialog-title").text("'.$row['title'].' registration for: " + this[0].id);'
                //. 'console.log(itemKey.substring(itemKey.indexOf("-")+1));'
                . 'document.getElementById("event_type").value = itemKey.substring(itemKey.indexOf("-")+1);'
                . 'document.getElementById("event_date").value = this[0].id;'
                . 'displayForm();'
                . '}'
                . '}';
    }
    
    mysqli_free_result($resultset);
    
    echo "var eventTypes = {".substr($tmpStr, 1)."};";
}

$mySQL->disconnect();
?>

function buildEventOptions(tableColumn){

    var daSelector = "#"+tableColumn.id;

    $.contextMenu({
        selector: daSelector,
        trigger: 'left',
        build: function(){
            return {items: eventTypes};
        }
    });
}


function displayForm(){
    
    var dialog, daButtons, form;

    daButtons = {
        Enter: function(){

            $('input').removeAttr('disabled');
            submitRegistration();
        },
        Cancel: function(){

            dialog.dialog("close");
        }
    };

    dialog = $("#reg-diag").dialog({
        autoOpen: false,
        width: '580',
        modal: true,
        buttons: daButtons,
        close: function(){
            //form[ 0 ].reset();
            //allFields.removeClass("ui-state-error");
        }
    });

    form = dialog.find("#reg-form").on("submit", function(event){

        event.preventDefault();
        $('input').removeAttr('disabled');
        submitRegistration();
    });
    
    dialog.dialog("open");
    resizeIframe();

    function submitRegistration(){
        
        top.grayOut(true);

        var regData = new FormData(document.getElementById("reg-form"));
        
        regData.append("ajax", "new_reg");
        regData.append("registreeCount", registereeCount);

        var regReq = $.ajax({
            method: "POST",
            data: regData,
            cache: false,
            contentType: false,
            processData: false
        });

        regReq.done(function(msg){

            //console.log(msg);
            alert("Registration Accepted. Please check your email for registration and event information.");
            document.location.reload(); 
            //top.grayOut(false);
            //location.reload();
        });

        regReq.fail(function(jqXHR, textStatus){

            alert("Request failed: " + textStatus);
            top.grayOut(false);
        });

        dialog.dialog("close");
    }
}

var registereeCount = 1;

function beEzafeYek(){

    registereeCount++;

    var yekDooneh = "<hr>"+
        "<label for='firstname"+registereeCount+"' style='display: inline-block; width: 30%; text-align: right;'>Firstname</label>"+
        "<input type='text' name='firstname"+registereeCount+"' id='firstname"+registereeCount+"' style='display: inline-block; width: 60%; text-align: left;'>"+

        "<label for='lastname"+registereeCount+"' style='display: inline-block; width: 30%; text-align: right;'>Lastname</label>"+
        "<input type='text' name='lastname"+registereeCount+"' id='lastname"+registereeCount+"' style='display: inline-block; width: 60%; text-align: left;'>"+

        "<label for='email"+registereeCount+"' style='display: inline-block; width: 30%; text-align: right;'>E-mail</label>"+
        "<input type='text' name='email"+registereeCount+"' id='email"+registereeCount+"' style='display: inline-block; width: 60%; text-align: left;'>"+

        "<label for='phone"+registereeCount+"' style='display: inline-block; width: 30%; text-align: right;'>Phone</label>"+
        "<input type='text' name='phone"+registereeCount+"' id='phone"+registereeCount+"' style='display: inline-block; width: 60%; text-align: left;'>"+

        "<div id='plus"+registereeCount+"' style='width: 100%; text-align: right;'>"+
        "    <a href='javascript: beEzafeYek();'>+ one</a>"+
        "</div>";

    var rowToRemove = "#plus"+(registereeCount-1);
    $(rowToRemove).after(yekDooneh);
    $(rowToRemove).remove();
    resizeIframe();
}

</script>
</div>
<?php include 'footer_nested.php'; ?>
