<div id="homeDiv">
<div id="carousel" class="carousel">
    <div>
        <h1>Providing goods and services for recreational outdoor activities within <a target="_blank" href="http://www.sbcounty.gov/main/default.aspx">San Bernardino County USA</a> since 2016</h1>
        <p>Enhance your relationships with friends, family and colleagues alike with our <a target="_blank" href="http://www.merriam-webster.com/dictionary/outfitter">outfitter</a> programs, whilst becoming attuned to your very planet!</p>
        <p>Earth is a beautiful miracle that we want everyone to recognize and be a part of. Our goal is to, <b>with safety as a priority</b>, provide adventures that will become lifetime memories, whether novice or expert!</p>
        <h2>Our activities include <i>hiking, fishing, skiing/snowboarding, canoeing/kayaking/rafting</i> and much more!</h2>
    </div>
<?php
    $mySQL = new db_mysql();
    $sql = "select id, title, description, start, end, price, poster from event where status='approved' order by sort, start, title";
    $resultset = mysqli_query($mySQL->connection, $sql);

    if(mysqli_num_rows($resultset) > 0){

        while ($row = mysqli_fetch_object($resultset)) {
            //echo '<div><a href="javascript: displayEvent("'.$row->id.'");"><img width="600" height="400" src="data:image;base64,'.$row->poster.'"></a></div>';  
            echo '<div>'
               . '<img alt="event promotion" class="notResizing" src="data:image/png;base64,'.$row->poster.'">'
               . '<h1 class="carousel_link" onclick="javascript: displayForm('.$row->id.', \''.$row->start.'\');" style="clear: right;"><u>'.$row->title.'</u></h1>'
               . '<h2 style="clear: right;">Event date: '.$row->start.'</h2>'
               . '<h2 style="clear: right;">'.$row->price.'</h2>'
               . '<div class="div_desc" style="clear: right; text-align: left; position: relative; left: 18px;">'.htmlentities($row->description).'</div></div>';

            //break;
        }
    }
    
    mysqli_free_result($resultset);
    
    $mySQL->disconnect();
?>
</div>

<script src="/js/jquery.dotdotdot.js"></script>
<script>
$(".div_desc").dotdotdot({
    ellipsis: "... more",
    height: 240
});
</script>

<script src="/js/slick.js"></script>
<script>
$('.carousel').slick({
    dots: true,
    infinite: true,
    speed: 1000,
    slidesToShow: 1,
    /*adaptiveHeight: true,*/
    autoplay: true,
    autoplaySpeed: 10000
});
</script>

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
function displayForm(eventType, eventDate){
    
    document.getElementById("event_type").value = eventType;
    document.getElementById("event_date").value = eventDate;
    
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
            //document.location.reload(); 
            navigate("registrar.php");
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

<?php new template("php/news.php"); ?>
<?php new template("php/calendar.php"); ?>
</div>