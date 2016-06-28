<?php include '../../header_nested.php'; ?>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $sql = '';
    
    $image_data = '';
    if (isset($_FILES['poster']) && $_FILES['poster']['size'] > 0) {
        
        $file_ary = reArrayFiles($_FILES['poster']);
        
        foreach($file_ary as $file) {

            $image = $file['tmp_name'];
            //$image_type = miniMIME($image);
            if(!empty($image)) $image_data = base64_encode(file_get_contents($image));
        }
    }
    
    if(isset($_POST["action"]) && ((strcasecmp($_POST["action"], "create_parent") == 0) || (strcasecmp($_POST["action"], "create_child") == 0 ))){
        
        $sql = 'insert into event_type(name, title, description, `start`, `end`, price, `limit`, poster, created_by, `status`, sort) values (\''.
                $_POST['name'].'\', \''.
                $_POST['title'].'\', \''.
                $_POST['description'].'\', \''.
                $_POST['start'].'\', \''.
                $_POST['end'].'\', \''.
                $_POST['price'].'\', \''.
                $_POST['limit'].'\', \''.
                $image_data.'\', \''.
                $_POST['created_by'].'\', \''.
                $_POST['status'].'\', \''.
                $_POST['sort'].'\')';
    }
    elseif(isset($_POST["action"]) && (strcasecmp($_POST["action"], "remove_node") == 0 )){

        $sql = 'update event_type set `status`=\'deleted\' where id='.$_POST["node"];
    }
    elseif(isset($_POST["action"]) && (strcasecmp($_POST["action"], "edit_node") == 0 )){

        $sql = 'update event_type set name=\''.$_POST['name'].'\', title=\''.
                $_POST['title'].'\', description=\''.
                $_POST['description'].'\', `start`=\''.
                $_POST['start'].'\', `end`=\''.
                $_POST['end'].'\', price=\''.
                $_POST['price'].'\', `limit`=\''.
                $_POST['limit'].'\''.
                ((strlen($image_data) === 0)?"":", poster='".$image_data."'").
                ', created_by=\''.
                $_POST['created_by'].'\', `status`=\''.
                $_POST['status'].'\', `sort`=\''.
                $_POST['sort'].'\' where id='.$_POST['id'];
    }

    if(strlen($sql) > 0){
        //error_log($sql);
        $mySQL = new db_mysql();

        mysqli_query($mySQL->connection, $sql);

        $mySQL->disconnect();
    }
}
?>

<link rel="stylesheet" type="text/css" href="/css/jstree/style.min.css" />
<link rel="stylesheet" type="text/css" href="/css/contextMenu/jquery.contextMenu.css" />
<link rel="stylesheet" type="text/css" href="/css/jquery_ui/jquery-ui.min.css" />
<link rel="stylesheet" type="text/css" href="/css/datetimepicker/jquery.datetimepicker.css">

<script src="/js/jstree.js"></script>
<script src="/js/jquery.contextMenu.js"></script>
<script src="/js/jquery-ui-1.11.4/jquery-ui.min.js"></script>
<script src="/js/jquery.datetimepicker.full.js"></script>

<span class='event_list' title="Click to add new even type to list."><b>Event type console</b></span>
<div id="div_jstree"></div>
<div id="dialog-form" title="Event type console" style="display: none;">
    <form enctype="multipart/form-data" method="post" id='eventTypeForm' name='eventTypeForm'>
        <fieldset>
            <table>
                <tr id='node_id' style="display: none;">
                    <td align='right'><label for="id">id</label></td>
                    <td><input type="text" name="id" id="id" disabled></td>
                </tr>
                <tr>
                    <td align='right'><label for="name">name</label></td>
                    <td><input type="text" name="name" id="name" disabled></td>
                </tr>
                <tr>
                    <td align='right'><label for="title">title</label></td>
                    <td><input type="text" name="title" id="title"></td>
                </tr>
                <tr>
                    <td align='right'><label for="description">description</label></td>
                    <td><textarea name="description" id="description" cols="42" rows="7"></textarea>
                        <!--
                        <script src="/js/tinymce/tinymce.min.js"></script>
                        <script>tinymce.init({ selector:'#description' });</script>
                        -->
                    </td>
                </tr>
                <tr>
                    <td align='right'><label for="start">start</label></td>
                    <td><input type="text" name="start" id="start"><script>$('#start').datetimepicker();</script></td>
                </tr>
                <tr>
                    <td align='right'><label for="end">end</label></td>
                    <td><input type="text" name="end" id="end"><script>$('#end').datetimepicker();</script></td>
                </tr>
                <tr>
                    <td align='right'><label for="price">price</label></td>
                    <td><input type="text" name="price" id="price"></td>
                </tr>
                <tr>
                    <td align='right'><label for="limit">limit</label></td>
                    <td><input type="text" name="limit" id="limit"></td>
                </tr>
                <tr>
                    <td align='right' valign="top"><label for="poster[]">poster</label></td>
                    <td>
                        <!-- MAX_FILE_SIZE must precede the file input field -->
                        <!--<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />-->
                        <input type="file" name="poster[]" id="poster"/>
                    </td>
                </tr>
                <tr>
                    <td align='right'><label for="created_by">created by</label></td>
                    <td><input type="text" name="created_by" id="created_by"></td>
                </tr>
                <tr>
                    <td align='right'><label for="sort">sort</label></td>
                    <td><input type="text" name="sort" id="sort"></td>
                </tr>
                <tr>
                    <td align='right'><label for="status">status</label></td>
                    <td><select name="status" id="status"><option value="new">new</option><option value="pending">pending</option><option value="approved">approved</option><option value="completed">completed</option><option value="deleted">deleted</option></select></td>
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
<?php
$mySQL = new db_mysql();

$sql = "select id, '#' parent, name `text`, '{\"class\":\"event_list\"}' a_attr from event_type order by sort";

$resultset = mysqli_query($mySQL->connection, $sql);

if($resultset){

    //$resultArray = pg_fetch_all($resultset);
    //pg_free_result($resultset);
    
    $resultArray = array();
    while ($row = $resultset->fetch_assoc()) {

        $resultArray[] = $row;
    }
    mysqli_free_result($resultset);
    
    $darth_message = str_replace('"{', '{', json_encode($resultArray));
    $darth_message = str_replace('}"', '}', $darth_message);
    $darth_message = str_replace('\\', '', $darth_message);

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
        selector: '.event_list',
        trigger: 'left',
        build: function($trigger, e){
            $('#div_jstree').jstree(true).deselect_all();
            var daMenu = {};
            var daFirstChild = $trigger.children(":first")[0];
            if(daFirstChild.innerHTML === "Event type console"){
                daMenu = {
                    callback: function(key, options){

                        contextMenuActionSelected($trigger, key, options);
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
                        "create_child": {name: "Add", icon: "add"},
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
        var daForm = document.getElementById("eventTypeForm");
        var daNodeId = $(daThing).attr("id")?$(daThing).attr("id").split("_")[0]:'';
        if(key === "remove_node"){ //-=-=-=-=-=-=-=- Delete Navigation Node (set record status = 'deleted')

            daData = {action: key, node: daNodeId};
            var request = $.ajax({
                method: "POST",
                data: daData
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
        }
        else{

            $('input').attr('size', '42');
            $('input').removeAttr('disabled');
            $('input').val('');
            $("#evet-type-image").remove();
            
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
                var sql = 'select * from event_type where id=' + daNodeId;
                
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
                            //console.log(attrName);
                            //if(typeof document.getElementById(attrName).type !== null)
                            if(document.getElementById(attrName).type.indexOf('select') === 0){

                                for(var aOption in document.getElementById(attrName).options){

                                    if(document.getElementById(attrName).options[aOption].value === attrValue)
                                        document.getElementById(attrName).options[aOption].selected = 'true';
                                }
                            }
                            else if(document.getElementById(attrName).type.indexOf('file') === 0){
                                
                                var campaignImage = new Image();
                                campaignImage.id = "evet-type-image";
                                campaignImage.width = 370;
                                campaignImage.height = 310;
                                campaignImage.src = "data:image;base64,"+attrValue;
                                
                                document.getElementById(attrName).parentNode.appendChild(campaignImage);
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
            else{ //-=-=-=-=- Node Creation Block

                daButtons = {
                    Create: function(){

                        $('input').removeAttr('disabled');
                        createMenuNode();
                    },
                    Cancel: function(){

                        dialog.dialog("close");
                    }
                };

                $('#node_id')[0].style = "display: none;";
                //$("#parent")[0].value = $(daThing).attr("id")?$(daThing).attr("id").split("_")[0]:'#';
                top.grayOut(false);
            }

            //$('#parent').attr('disabled', true);
            $('#id').attr('disabled', true);
            //$('#status').attr('disabled', true);
            
            function createMenuNode(){
                
                top.grayOut(true);
                
                //daData = {action: key, form: $('#eventTypeForm').serialize()};
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

            dialog = $("#dialog-form").dialog({
                autoOpen: false,
                width: '540',
                modal: true,
                buttons: daButtons,
                close: function(){
                    //form[ 0 ].reset();
                    //allFields.removeClass("ui-state-error");
                }
            });
            form = dialog.find("#eventTypeForm").on("submit", function(event){
                event.preventDefault();
                $('input').removeAttr('disabled');
                createMenuNode();
            });
            dialog.dialog("open");
            resizeIframe();
        }
    }
</script>

<?php include '../../footer_nested.php'; ?>