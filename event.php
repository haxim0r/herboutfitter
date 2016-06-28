<?php require_once 'header_nested.php'; ?>
<?php
$mySQL = new db_mysql();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    if(isset($_POST["daAction"]) && (strcasecmp($_POST["daAction"], "new_reg") == 0)){
        
        //$registreeCount = 1;
        
        $sql_inner = "select id from user where email='".$_POST["email1"]."'";
        
        $resultset = mysqli_query($mySQL->connection, $sql_inner);

        if(mysqli_num_rows($resultset) > 0){
            
            $sql = '';
            
            $user = mysqli_fetch_object($resultset);
            
            $sql = 'insert into registration(user, event) values ('.$user->id.', '.$_GET['id'].');';
            
            error_log($sql);

            mysqli_query($mySQL->connection, $sql);
        }
    }
}

$sql = "select id, title, description, start, end, price, poster from event where id=".$_GET['id'];

$resultset = mysqli_query($mySQL->connection, $sql);

if(mysqli_num_rows($resultset) > 0){
    
    $row = mysqli_fetch_object($resultset);
?>
<script>document.body.className = "childFrameBody";</script>
<div id="div_iframeBody">
    <a href="javascript: displayForm();">Register</a>
<img alt="event promotion" class="notResizing" src="data:image/png;base64,<?= $row->poster ?>">
<h1 style="clear: right"><?= $row->title ?></h1>
<h2 style="clear: right">Event date: <?= $row->start ?></h2>
<h2 style="clear: right"><?= $row->price ?></h2>
<div id="div_desc" style="clear: right; text-align: left; position: relative; left: 18px;"><?= htmlentities($row->description) ?></div>
</div>

<div id="reg-diag" title="Registration form" style="display: none;">
    <form method="post" id='reg-form' name='reg-form'>
        <fieldset>
            <table>
                <tr id='node_id' style="display: none;">
                    <td align='right'><label for="id">id</label></td>
                    <td><input type="text" name="id" id="id" disabled></td>
                </tr>
                <tr>
                    <td align='right'><label for='firstname1'>Firstname</label></td>
                    <td><input type='text' name='firstname1' id='firstname1'></td>
                </tr>
                <tr>
                    <td align='right'><label for='lastname1'>Lastname</label></td>
                    <td><input type='text' name='lastname1' id='lastname1'></td>
                </tr>
                <tr>
                    <td align='right'><label for='email1'>E-mail</label></td>
                    <td><input type='text' name='email1' id='email1'></td>
                </tr>
                <tr>
                    <td align='right'><label for='phone1'>Phone</label></td>
                    <td><input type='text' name='phone1' id='phone1'></td>
                </tr>
                <tr id="plus1">
                    <td colspan="2" align='right'><a href="javascript: beEzafeYek();">+ one</a></td>
                </tr>
                <tr>
                    <td colspan='2' align='center'>
                        <!-- Allow form submission with keyboard without duplicating the dialog button -->
                        <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>

<script>
    var registereeCount = 1;
    
    function beEzafeYek(){
        
        registereeCount++;
        
        var yekDooneh = "<tr><td colspan='2'><hr></td></tr>"+
                "<tr>"+
                "<td align='right'><label for='firstname"+registereeCount+"'>Firstname</label></td>"+
                "<td><input type='text' name='firstname"+registereeCount+"' id='firstname"+registereeCount+"'></td>"+
                "</tr>"+
                "<tr>"+
                "<td align='right'><label for='lastname"+registereeCount+"'>Lastname</label></td>"+
                "<td><input type='text' name='lastname"+registereeCount+"' id='lastname"+registereeCount+"'></td>"+
                "</tr>"+
                "<tr>"+
                "<td align='right'><label for='email"+registereeCount+"'>E-mail</label></td>"+
                "<td><input type='text' name='email"+registereeCount+"' id='email"+registereeCount+"'></td>"+
                "</tr>"+
                "<tr>"+
                "<td align='right'><label for='phone"+registereeCount+"'>Phone</label></td>"+
                "<td><input type='text' name='phone"+registereeCount+"' id='phone"+registereeCount+"'></td>"+
                "</tr>"+
                "<tr id='plus"+registereeCount+"'>"+
                "<td colspan='2' align='right'><a href='javascript: beEzafeYek();'>+ one</a></td>"+
                "</tr>";
        
        var rowToRemove = "#plus"+(registereeCount-1);
        $(rowToRemove).after(yekDooneh);
        $(rowToRemove).remove();
        resizeIframe();
    }
    
    function displayForm(){
        
        //$("#campaign-form").show();
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
            
            var regData = new FormData(document.getElementById("reg-form"));
            regData.append("daAction", "new_reg");
            
            var regReq = $.ajax({
                method: "POST",
                data: regData,
                cache: false,
                contentType: false,
                processData: false
            });
            
            grayOut(true);

            regReq.done(function(msg){

                //console.log(msg);
                alert("Registration Accepted. Please check your email for registration and event information.");
                top.grayOut(false);
                //location.reload();
            });
            
            regReq.fail(function(jqXHR, textStatus){

                alert("Request failed: " + textStatus);
                top.grayOut(false);
            });
            
            dialog.dialog("close");
        }
    }
</script>
<?php
}

mysqli_free_result($resultset);

$mySQL->disconnect();
?>

<?php include 'footer_nested.php'; ?>
