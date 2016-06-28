<div id="user_auth" onclick="daToggler_siteBG++;">
    <a href="javascript: toggleUserControls();" id="user-icon"></a>
    <a href="javascript: navigate('registrar.php');" id="event-request"></a>
    <div id="user_man">
        <a href="javascript: toggleUserControls();" id='close_btn'></a>
        <?php if(!isset($_SESSION['auth'])){ ?>
        <form method="post" id="loginForm" class="ui-widget" onsubmit="javascript: toggleUserControls();top.grayOut(true);">
            <table class="loginForm">
                <tr>
                    <td><label for="esmet">Email</label></td>
                    <td><input type="text" name="esmet" id="esmet" /></td>
                </tr>
                <tr>
                    <td><label for="ejazat">Password</label></td>
                    <td><input type="password" name="ejazat" id="ejazat" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="submit" value="Login" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td><a href="javascript: registrationForm();">Register free!</a></td>
                </tr>
                <tr>
                    <td></td>
                    <td><a href="javascript: recoverPass();">Forgot password!</a></td>
                </tr>
                <tr>
                    <td colspan="2"><hr></td>
                </tr>
                <tr>
                    <td colspan="2"><button onclick="javascript: return subsUsr();">Subscribe to newsletter?</button></td>
                </tr>
                <tr>
                    <td colspan="2"><label for="subscribe">Email</label><input type="text" name="subscribe" id="subscribe" /></td>
                </tr>
            </table>
        </form>
        <?php } else { ?>
        <button onclick="javascript: loadProfile(<?=getUserData('id');?>)">Profile</button><br/><br/>
        <button onclick="javascript: alert('not yet implemented')">History</button><br/><br/>
        <button onclick="javascript: logout()">Logout</button>
        <?php } ?>
    </div>
</div>
<div id="dialog-forgot" title="Password Recovery From" style="display: none;">
    <form enctype="multipart/form-data" method="post" id='passwordRecovery' name='passwordRecovery'>
        <fieldset>
            <p>Please provide the email address for the account being recovered.</p>
            
            <label for='pw_user' style="display: inline-block; width: 30%; text-align: right;">E-Mail</label>
            <input type='text' name='pw_user' id='pw_user' style="display: inline-block; width: 60%; text-align: left;">
            
            <!-- Allow form submission with keyboard without duplicating the dialog button -->
            <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
        </fieldset>
    </form>
</div>
<div id="dialog-register" title="Registration form" style="display: none;">
    <form enctype="multipart/form-data" method="post" id='registrationForm' name='registrationForm'>
        <fieldset>
            <table class="loginForm">
                <tr id='node_id' style="display: none;">
                    <td><label for="id">id</label></td>
                    <td><input type="text" name="id" id="id" disabled></td>
                </tr>
                <tr>
                    <td><label for="firstname">firstname</label></td>
                    <td><input type="text" name="firstname" id="firstname"></td>
                </tr>
                <tr>
                    <td><label for="lastname">lastname</label></td>
                    <td><input type="text" name="lastname" id="lastname"></td>
                </tr>
                <tr>
                    <td><label for="email">email</label></td>
                    <td><input type="text" name="email" id="email"></td>
                </tr>
                <tr>
                    <td><label for="password">password</label></td>
                    <td><input type="text" name="password" id="password"></td>
                </tr>
                <tr>
                    <td><label for="phone">phone</label></td>
                    <td><input type="text" name="phone" id="phone"></td>
                </tr>
                <tr>
                    <td><label for="address">address</label></td>
                    <td><input type="text" name="address" id="address"></td>
                </tr>
                <tr>
                    <td><label for="city">city</label></td>
                    <td><input type="text" name="city" id="city"></td>
                </tr>
                <tr>
                    <td><label for="zip">zip</label></td>
                    <td><input type="text" name="zip" id="zip"></td>
                </tr>
                <tr>
                    <td colspan='2'>
                        <!-- Allow form submission with keyboard without duplicating the dialog button -->
                        <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>
<script>
function subsUsr(){
    
    toggleUserControls();
    
    top.grayOut(true);
    
    daData = {ajax: 'subscribe', email: document.getElementById("subscribe").value};
    var request = $.ajax({
        method: "POST",
        data: daData
    });
    request.done(function(msg){ //-=-=-=- set form data
        //console.log(msg);
        eval(msg);
        top.grayOut(false);
    });
    request.fail(function(jqXHR, textStatus){
        top.grayOut(false);
        alert("Request failed: " + textStatus);
    });
    
    return false;
}
function loadProfile(id){
    
    toggleUserControls();
    
    top.grayOut(true);
    
    var daButtons = {
        Update: function(){

            $('input').removeAttr('disabled');

            var form = document.forms.namedItem("registrationForm");
            var daData = new FormData(form);
            daData.append("ajax", "update_user");

            var request = $.ajax({
                method: "POST",
                data: daData,
                cache: false,
                contentType: false,
                processData: false
            });
            request.done(function(msg){
                top.grayOut(false);
                console.log(msg);
                //top.location.reload();
            });
            request.fail(function(jqXHR, textStatus){
                top.grayOut(false);
                alert("Request failed: " + textStatus);
            });
            
            dialog.dialog("close");
        },
        Cancel: function(){

            dialog.dialog("close");
        }
    };
    
    var dialog = $("#dialog-register").dialog({
        autoOpen: false,
        width: '360',
        modal: true,
        buttons: daButtons,
        close: function(){
            //form[ 0 ].reset();
            //allFields.removeClass("ui-state-error");
        }
    });
    
    dialog.dialog("open");
    
    //-=-=-=- get form data with ajax
    var sql = 'select * from user where id=' + id;

    daData = {ajax: 'SQL', statement: sql};
    var request = $.ajax({
        method: "POST",
        data: daData
    });
    request.done(function(msg){ //-=-=-=- set form data
        //console.log(msg);
        var formData = JSON.parse(msg);
        for(var i = 0; i < formData.length; i++){

            var obj = formData[i];
            for(var key in obj){

                var attrName = key;
                var attrValue = obj[key];
                
                try{
                    document.getElementById(attrName).value = attrValue;
                }
                catch(e){
                    console.log('unable to set value for form field: '+e);
                }
                //document.getElementById(attrName).value = attrValue;
                
                /*if(document.getElementById(attrName).type.indexOf('select') === 0){

                    for(var aOption in document.getElementById(attrName).options){

                        if(document.getElementById(attrName).options[aOption].value === attrValue)
                            document.getElementById(attrName).options[aOption].selected = 'true';
                    }
                }
                else{

                    document.getElementById(attrName).value = attrValue;
                }*/
            }
        }
        top.grayOut(false);
    });
    request.fail(function(jqXHR, textStatus){
        top.grayOut(false);
        alert("Request failed: " + textStatus);
    });
}
function registrationForm(){
    
    toggleUserControls();
    
    //top.grayOut(true);
    
    var daButtons = {
        Create: function(){

            $('input').removeAttr('disabled');
            registerUser(dialog);
        },
        Cancel: function(){

            dialog.dialog("close");
        }
    };
    
    var dialog = $("#dialog-register").dialog({
        autoOpen: false,
        width: '360',
        modal: true,
        buttons: daButtons,
        close: function(){
            //form[ 0 ].reset();
            //allFields.removeClass("ui-state-error");
        }
    });
    
    var form = dialog.find("#registrationForm").on("submit", function(event){
        event.preventDefault();
        $('input').removeAttr('disabled');
        registerUser(dialog);
    });
    
    dialog.dialog("open");
}

function registerUser(dialog){
    
    top.grayOut(true);
    
    //daData = {action: key, form: $('#newMenuItemForm').serialize()};
    var form = document.forms.namedItem("registrationForm");
    var daData = new FormData(form);
    daData.append("ajax", "create_user");

    var request = $.ajax({
        method: "POST",
        data: daData,
        cache: false,
        contentType: false,
        processData: false
    });
    request.done(function(msg){
        top.grayOut(false);
        console.log(msg);
        //top.location.reload();
    });
    request.fail(function(jqXHR, textStatus){
        top.grayOut(false);
        alert("Request failed: " + textStatus);
    });
    dialog.dialog("close");
}

function recoverPass(){
    
    toggleUserControls();
    
    //top.grayOut(true);
    
    var daButtons = {
        Recover: function(){

            $('input').removeAttr('disabled');
            recover(dialog);
        },
        Cancel: function(){

            dialog.dialog("close");
        }
    };
    
    var dialog = $("#dialog-forgot").dialog({
        autoOpen: false,
        modal: true,
        buttons: daButtons,
        close: function(){
            //form[ 0 ].reset();
            //allFields.removeClass("ui-state-error");
        }
    });
    
    var form = dialog.find("#passwordRecovery").on("submit", function(event){
        event.preventDefault();
        $('input').removeAttr('disabled');
        recover(dialog);
    });
    
    dialog.dialog("open");
}

function recover(dialog){

    top.grayOut(true);

    //daData = {action: key, form: $('#newMenuItemForm').serialize()};
    //var form = $("#passwordRecovery")[0];
    var form = document.forms.namedItem("passwordRecovery");
    var daData = new FormData(form);
    daData.append("ajax", "recover");
    
    var request = $.ajax({
        method: "POST",
        data: daData,
        cache: false,
        contentType: false,
        processData: false
    });
    request.done(function(msg){
        top.grayOut(false);
        eval(msg);
        //top.location.reload();
    });
    request.fail(function(jqXHR, textStatus){
        top.grayOut(false);
        alert("Request failed: " + textStatus);
    });
    dialog.dialog("close");
}
</script>
