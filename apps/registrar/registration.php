<?php include '../../header_nested.php'; ?>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $sql = '';
    
    if(isset($_POST["action"]) && (strcasecmp($_POST["action"], "remove_node") == 0 )){

        $sql = 'update user set `status`=\'deleted\' where id='.$_POST["node"];
    }
    elseif(isset($_POST["action"]) && (strcasecmp($_POST["action"], "edit_node") == 0 )){

        $sql = 'update user set subscribed=\''.$_POST['subscribed'].'\', username=\''.
                $_POST['username'].'\', password=\''.
                $_POST['password'].'\', firstname=\''.
                $_POST['firstname'].'\', `lastname`=\''.
                $_POST['lastname'].'\', `email`=\''.
                $_POST['email'].'\', phone=\''.
                $_POST['phone'].'\', `address`=\''.
                $_POST['address'].'\', `city`=\''.
                $_POST['city'].'\', `zip`=\''.
                $_POST['zip'].'\', `role`=\''.
                $_POST['role'].'\', `status`=\''.
                $_POST['status'].'\' where id='.$_POST['id'];
    }

    if(strlen($sql) > 0){
        //error_log($sql);
        $mySQL = new db_mysql();

        mysqli_query($mySQL->connection, $sql);

        $mySQL->disconnect();
    }
}
?>
<link rel="stylesheet" href="/css/jstree/style.min.css" />
<link rel="stylesheet" href="/css/contextMenu/jquery.contextMenu.css" />
<link rel="stylesheet" href="/css/jquery_ui/jquery-ui.min.css" />

<script src="/js/jstree.js"></script>
<script src="/js/jquery.contextMenu.js"></script>
<script src="/js/jquery-ui-1.11.4/jquery-ui.min.js"></script>

<span class='registration_list'><b>Event registrar</b></span>
<div id="div_jstree"></div>
<script>
<?php
$mySQL = new db_mysql();

$sql = "select id, '#' parent,
        concat(date_format(created_on,'%m/%d/%Y'), ' - ', name, ': ', coalesce(date_format(event_date,'%m/%d/%Y'), '?'), ' -> ', firstname, ' ', lastname) `text`,
        '{\"class\":\"registration_list\"}' a_attr from(
            select id, created_on, event_date, name, firstname, lastname from (
                select r.id, r.created_on, r.event_date, et.name, u.firstname, u.lastname from registration r
                inner join event_type et on et.id=r.event_type
                inner join user u on u.id=r.user
                union
                select r.id, r.created_on, r.event_date, e.name, u.firstname, u.lastname from registration r
                inner join event e on e.id=r.event
                inner join user u on u.id=r.user
            ) x
            order by x.created_on desc, x.event_date desc, x.name, x.lastname, x.firstname
        ) y";

$resultset = mysqli_query($mySQL->connection, $sql);

if($resultset){

    $resultArray = array();
    while ($row = $resultset->fetch_assoc()) {

        $resultArray[] = $row;
        
        $sql_inner = "select concat(ru.id, '_', u.id) `id`, '".$row["id"]."' `parent`,
            concat(u.firstname, ' ', u.lastname, ': ', u.email) `text`,
            '{\"class\":\"registration_list rez\"}' a_attr
            from user u
            inner join reg_user ru on ru.user=u.id
            where ru.registration=".$row["id"];
        
        $resultset2 = mysqli_query($mySQL->connection, $sql_inner);

        if($resultset2){

            while ($row2 = $resultset2->fetch_assoc()) {

                $resultArray[] = $row2;
            }
            
            mysqli_free_result($resultset2);
        }
    }
    mysqli_free_result($resultset);
    
    $darth_message = str_replace('"{', '{', json_encode($resultArray));
    $darth_message = str_replace('}"', '}', $darth_message);
    $darth_message = str_replace('\\', '', $darth_message);
    //echo "console.log(".$darth_message.");";
    echo "$('#div_jstree').jstree({ 'core' : { 'data' : ".$darth_message." } });";
}

$mySQL->disconnect();
?>

    $('#div_jstree').bind('ready.jstree', function(e, data){
        // invoked after jstree has loaded
        resizeIframe();
    });
    $('#div_jstree').on("after_open.jstree", function(e, data){
        // invoked after jstree node is expanded
        resizeIframe();
    });
    //-=-=-=-=-=-=-=-=-=-=-=-=- CONTEXT MENU BLOCK -=-=-=-=-=-=-=-=-=-=-=-=-
    $.contextMenu({
        selector: '.rez',
        trigger: 'left',
        build: function($trigger, e){
            $('#div_jstree').jstree(true).deselect_all();
            var daMenu = {};
            var daFirstChild = $trigger.children(":first")[0];
            if(daFirstChild.innerHTML === "Patron registrar"){
                daMenu = {
                    callback: function(key, options){
                        registrationForm();
                        //contextMenuActionSelected($trigger, key, options);
                    },
                    items: {
                        "create_parent": {name: "Add", icon: "add"}
                    }
                };
            }
            else{
                $('#div_jstree').jstree(true).select_node($trigger);
                daMenu = {
                    callback: function(key, options){

                        contextMenuActionSelected($trigger, key, options);
                    },
                    items: {
                        //"create_child": {name: "Add", icon: "add"},
                        "edit_node": {name: "Edit", icon: "edit"},
                        "sep1": "---------",
                        "remove_node": {name: "Remove", icon: "delete"}
                    }
                };
            }

            return daMenu;
        }
    });
    //-=-=-=-=-=-=-=-=-=-=-=-=- Context Menu Action Block -=-=-=-=-=-=-=-=-=-=-=-=-
    function contextMenuActionSelected(daThing, key, options){

        top.grayOut(true);
        
        var dialog, form, daData, daButtons;
        var daForm = document.getElementById("registrationForm");
        var daNodeId = $(daThing).attr("id")?$(daThing).attr("id").split("_")[1]:'';
        
        if(key === "remove_node"){ //-=-=-=-=-=-=-=- Delete Navigation Node (set record status = 'deleted')

            daData = {action: key, node: daNodeId};
            var request = $.ajax({
                method: "POST",
                data: daData
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
        }
        else{

            $('input').attr('size', '42');
            $('input').removeAttr('disabled');
            $('input').val('');
            
            if(key === "edit_node"){

                daButtons = {
                    Update: function(){

                        $('input').removeAttr('disabled');
                        createMenuNode();
                    },
                    Cancel: function(){

                        dialog.dialog("close");
                    }
                };
                //-=-=-=- get form data with ajax
                var sql = 'select * from user where id=' + daNodeId;
                
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
                            
                            document.getElementById(attrName).value = attrValue;
                            //console.log(attrName);
                            //if(typeof document.getElementById(attrName).type !== null)
                            if(document.getElementById(attrName).type.indexOf('select') === 0){

                                for(var aOption in document.getElementById(attrName).options){

                                    if(document.getElementById(attrName).options[aOption].value === attrValue)
                                        document.getElementById(attrName).options[aOption].selected = 'true';
                                }
                            }
                            else{

                                document.getElementById(attrName).value = attrValue;
                            }
                        }
                    }
                    
                    resizeIframe();
                    top.grayOut(false);
                });
                request.fail(function(jqXHR, textStatus){
                    top.grayOut(false);
                    alert("Request failed: " + textStatus);
                });

                $('#node_id')[0].style = "display: show;";
            }

            $('#id').attr('disabled', true);
            
            function createMenuNode(){
                
                top.grayOut(true);
                
                var daData = new FormData(daForm);
                daData.append("action", key);
                
                var request = $.ajax({
                    method: "POST",
                    data: daData,
                    cache: false,
                    contentType: false,
                    processData: false
                });
                request.done(function(msg){
                    top.grayOut(false);
                    //console.log(msg);
                    //top.location.reload();
                });
                request.fail(function(jqXHR, textStatus){
                    top.grayOut(false);
                    alert("Request failed: " + textStatus);
                });
                dialog.dialog("close");
            }

            dialog = $("#dialog-register").dialog({
                autoOpen: false,
                width: '540',
                modal: true,
                buttons: daButtons,
                close: function(){
                    //form[ 0 ].reset();
                    //allFields.removeClass("ui-state-error");
                }
            });
            form = dialog.find("#registrationForm").on("submit", function(event){
                event.preventDefault();
                $('input').removeAttr('disabled');
                createMenuNode();
            });
            dialog.dialog("open");
            resizeIframe();
        }
    }
</script>

<div id="dialog-register" title="Registration form" style="display: none;">
    <form enctype="multipart/form-data" method="post" id='registrationForm' name='registrationForm'>
        <fieldset>
            <table>
                <tr id='node_id' style="display: none;">
                    <td align='right'><label for="id">id</label></td>
                    <td><input type="text" name="id" id="id" disabled></td>
                </tr>
                <tr>
                    <td align='right'><label for="subscribed">subscribed</label></td>
                    <td><input type="text" name="subscribed" id="subscribed"></td>
                </tr>
                <tr>
                    <td align='right'><label for="username">username</label></td>
                    <td><input type="text" name="username" id="username"></td>
                </tr>
                <tr>
                    <td align='right'><label for="password">password</label></td>
                    <td><input type="text" name="password" id="password"></td>
                </tr>
                <tr>
                    <td align='right'><label for="firstname">firstname</label></td>
                    <td><input type="text" name="firstname" id="firstname"></td>
                </tr>
                <tr>
                    <td align='right'><label for="lastname">lastname</label></td>
                    <td><input type="text" name="lastname" id="lastname"></td>
                </tr>
                <tr>
                    <td align='right'><label for="email">email</label></td>
                    <td><input type="text" name="email" id="email"></td>
                </tr>
                <tr>
                    <td align='right'><label for="phone">phone</label></td>
                    <td><input type="text" name="phone" id="phone"></td>
                </tr>
                <tr>
                    <td align='right'><label for="address">address</label></td>
                    <td><input type="text" name="address" id="address"></td>
                </tr>
                <tr>
                    <td align='right'><label for="city">city</label></td>
                    <td><input type="text" name="city" id="city"></td>
                </tr>
                <tr>
                    <td align='right'><label for="zip">zip</label></td>
                    <td><input type="text" name="zip" id="zip"></td>
                </tr>
                <tr>
                    <td align='right'><label for="role">role</label></td>
                    <td><input type="text" name="role" id="role"></td>
                </tr>
                <tr>
                    <td align='right'><label for="status">status</label></td>
                    <td><input type="text" name="status" id="status"></td>
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
function registrationForm(){
    
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
    resizeIframe();
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
    
}
</script>

<?php include '../../footer_nested.php'; ?>