<?php include '../../header_nested.php'; ?>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    if(isset($_POST['daAction'])){
        
        $mysqlConnnected = false;
        
        if(isset($_FILES['uploads_sm'])){
            
            $file_ary = reArrayFiles($_FILES['uploads_sm']);

            $mySQL = new db_mysql();
            
            foreach ($file_ary as $file) {

                $sql = '';
                $image = $file['tmp_name'];
                $image_type = miniMIME($image);
                $image_data = base64_encode(file_get_contents($image));

                if((strcasecmp($_POST["daAction"], "create_parent") == 0) || (strcasecmp($_POST["daAction"], "create_child") == 0 )){

                    $sql = "insert into site_bg(name, type, dimensions, image) values ('".$file['name']."', '".$image_type."', 'small', '".$image_data."')";
                }

                if(strlen($sql) > 0){

                    //error_log("c3p0: ".$sql);
                    
                    if(!mysqli_query($mySQL->connection, $sql)){
                        
                        error_log("Error description: " . mysqli_error($mySQL->connection));
                    }
                    
                    $mysqlConnnected = true;
                }
            }
            
            if($mysqlConnnected){

                $mySQL->disconnect();
            }
        }
        elseif(isset($_FILES['uploads_lg'])){
            
            $file_ary = reArrayFiles($_FILES['uploads_lg']);
            
            $mySQL = new db_mysql();
            
            foreach ($file_ary as $file) {

                $sql = '';
                $image = $file['tmp_name'];
                $image_type = miniMIME($image);
                $image_data = base64_encode(file_get_contents($image));

                if((strcasecmp($_POST["daAction"], "create_parent") == 0) || (strcasecmp($_POST["daAction"], "create_child") == 0 )){

                    $sql = "insert into site_bg(name, type, dimensions, image) values ('".$file['name']."', '".$image_type."', 'large', '".$image_data."')";
                }
                elseif(strcasecmp($_POST["daAction"], "remove_node") == 0 ){

                    $sql = 'delete from site_bg where id='.$_POST["node"];
                }

                if(strlen($sql) > 0){

                    //error_log("c3p0: ".$sql);
                    
                    if(!mysqli_query($mySQL->connection, $sql)){
                        
                        error_log("Error description: " . mysqli_error($mySQL->connection));
                    }
                    
                    $mysqlConnnected = true;
                }
            }
        }
        elseif(strcasecmp($_POST["daAction"], "remove_node") == 0 ){

            $sql = 'delete from site_bg where id='.$_POST["node"];
            
            $mySQL = new db_mysql();
            
            if(!mysqli_query($mySQL->connection, $sql)){

                error_log("Error description: " . mysqli_error($mySQL->connection));
            }

            $mysqlConnnected = true;
        }
        
        
        if($mysqlConnnected){

            $mySQL->disconnect();
        }
    }
}
?>
<link rel="stylesheet" href="/css/jstree/style.min.css" />
<link rel="stylesheet" href="/css/contextMenu/jquery.contextMenu.css" />
<link rel="stylesheet" href="/css/jquery_ui/jquery-ui.min.css" />

<script src="/js/jstree.js"></script>
<script src="/js/jquery.contextMenu.js"></script>
<script src="/js/jquery-ui-1.11.4/jquery-ui.min.js"></script>

<div style="position: relative; clear: none; float: left; margin: 18px;">
<span class='image_library_lg' title="Click to add new image to library."><b>Large background image library</b></span>
<div id="div_jstree_lg"></div>
<div id="dialog-form_lg" title="Large background image library" style="display: none;">
    <form enctype="multipart/form-data" method="post" id='newMenuItemForm_lg' name='newMenuItemForm_lg'>
        <fieldset>
            <table>
                <tr id='node_id' style="display: none;">
                    <td align='right'><label for="id">id</label></td>
                    <td><input type="text" name="id" id="id" disabled></td>
                </tr>
                <tr>
                    <td align='right' valign="top"><label for="uploads_lg[]">image</label></td>
                    <td>
                        <!-- MAX_FILE_SIZE must precede the file input field -->
                        <!--<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />-->
                        <input type="file" name="uploads_lg[]" id="uploads_lg[]" multiple="multiple"/>
                    </td>
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

$sql = "select id, '#' parent, concat(name, ' - ', type) text, '{\"class\":\"image_library_lg\"}' a_attr from site_bg where dimensions='large'";

$resultset = mysqli_query($mySQL->connection, $sql);

if($resultset){

    $resultArray = array();
    while ($row = $resultset->fetch_assoc()) {

        $resultArray[] = $row;
    }
    mysqli_free_result($resultset);
    
    $darth_message = str_replace('"{', '{', json_encode($resultArray));
    $darth_message = str_replace('}"', '}', $darth_message);
    $darth_message = str_replace('\\', '', $darth_message);
    
    echo "$('#div_jstree_lg').jstree({ 'core' : { 'data' : ".$darth_message." } });";
}

$mySQL->disconnect();
?>

    $('#div_jstree_lg').bind('ready.jstree', function(e, data){
        // invoked after jstree has loaded
        resizeIframe();
    });
    $('#div_jstree_lg').on("after_open.jstree", function(e, data){
        // invoked after jstree node is expanded
        resizeIframe();
    });
    //-=-=-=-=-=-=-=-=-=-=-=-=- CONTEXT MENU BLOCK -=-=-=-=-=-=-=-=-=-=-=-=-
    $.contextMenu({
        selector: '.image_library_lg',
        trigger: 'left',
        build: function($trigger, e){
            $('#div_jstree_lg').jstree(true).deselect_all();
            var daMenu = {};
            var daFirstChild = $trigger.children(":first")[0];
            if(daFirstChild.innerHTML === "Large background image library"){
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
                $('#div_jstree_lg').jstree(true).select_node($trigger);
                daMenu = {
                    callback: function(key, options){

                        contextMenuActionSelected($trigger, key, options);
                        //grayOut(false);
                    },
                    items: {
                        //"create_child": {name: "Add", icon: "add"},
                        //"edit_node": {name: "Edit", icon: "edit"},
                        //"entries": {name: "Entries", icon: "paste"},
                        //"sep1": "---------",
                        "remove_node": {name: "Remove", icon: "delete"}
                    }
                };
            }

            return daMenu;
        }
    });
    //-=-=-=-=-=-=-=-=-=-=-=-=- Context Menu Action Block -=-=-=-=-=-=-=-=-=-=-=-=-
    function contextMenuActionSelected(daThing, key, options){
        
        var dialog, daData, daButtons;
        var daForm = document.getElementById("newMenuItemForm_lg");
        var daNodeId = $(daThing).attr("id")?$(daThing).attr("id").split("_")[0]:'';
        
        if(key === "remove_node"){ //-=-=-=-=-=-=-=- Delete Navigation Node (set record status = 'deleted')

            daData = {daAction: key, node: daNodeId};
            var request = $.ajax({
                method: "POST",
                data: daData
            });
            request.done(function(msg){

                //console.log(msg);
                location.reload();
            });
            request.fail(function(jqXHR, textStatus){

                alert("Request failed: " + textStatus);
            });
        }
        else{
            
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
  
            $('#id').attr('disabled', true);

            dialog = $("#dialog-form_lg").dialog({
                autoOpen: false,
                dialogClass: 'rez_ui',
                width: '580',
                modal: true,
                buttons: daButtons,
                close: function(){
                    //daForm.reset();
                    //allFields.removeClass("ui-state-error");
                }
            });

            dialog.find("#newMenuItemForm_lg").on("submit", function(event){

                event.preventDefault();
                $('input').removeAttr('disabled');
                createMenuNode();
            });

            dialog.dialog("open");
            //daForm.reset();
            resizeIframe();
            
            
            function createMenuNode(){
                
                top.grayOut(true);
                
                //daData = {action: key, form: $('#newMenuItemForm_lg').serialize()};
                var daData = new FormData(daForm);
                daData.append("daAction", key);
                
                var request = $.ajax({
                    method: "POST",
                    data: daData,
                    cache: false,
                    contentType: false,
                    processData: false
                });
                
                request.done(function(msg){

                    //console.log(msg);
                    location.reload();
                });
                request.fail(function(jqXHR, textStatus){

                    alert("Request failed: " + textStatus);
                });
                dialog.dialog("close");
            }
        }
    }
</script>
</div>



<div style="position: relative; float: left; clear: none; margin: 18px;">
<span class='image_library_sm' title="Click to add new image to library."><b>Small background image library</b></span>
<div id="div_jstree_sm"></div>
<div id="dialog-form_sm" title="Small background image library" style="display: none;">
    <form enctype="multipart/form-data" method="post" id='newMenuItemForm_sm' name='newMenuItemForm_sm'>
        <fieldset>
            <table>
                <tr id='node_id' style="display: none;">
                    <td align='right'><label for="id">id</label></td>
                    <td><input type="text" name="id" id="id" disabled></td>
                </tr>
                <tr>
                    <td align='right' valign="top"><label for="uploads_sm[]">image</label></td>
                    <td>
                        <!-- MAX_FILE_SIZE must precede the file input field -->
                        <!--<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />-->
                        <input type="file" name="uploads_sm[]" id="uploads_sm[]" multiple="multiple"/>
                    </td>
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

$sql = "select id, '#' parent, concat(name, ' - ', type) text, '{\"class\":\"image_library_sm\"}' a_attr from site_bg where dimensions='small'";

$resultset = mysqli_query($mySQL->connection, $sql);

if($resultset){

    $resultArray = array();
    while ($row = $resultset->fetch_assoc()) {

        $resultArray[] = $row;
    }
    mysqli_free_result($resultset);
    
    $darth_message = str_replace('"{', '{', json_encode($resultArray));
    $darth_message = str_replace('}"', '}', $darth_message);
    $darth_message = str_replace('\\', '', $darth_message);
    
    echo "$('#div_jstree_sm').jstree({ 'core' : { 'data' : ".$darth_message." } });";
}

$mySQL->disconnect();
?>

    $('#div_jstree_sm').bind('ready.jstree', function(e, data){
        // invoked after jstree has loaded
        resizeIframe();
    });
    $('#div_jstree_sm').on("after_open.jstree", function(e, data){
        // invoked after jstree node is expanded
        resizeIframe();
    });
    //-=-=-=-=-=-=-=-=-=-=-=-=- CONTEXT MENU BLOCK -=-=-=-=-=-=-=-=-=-=-=-=-
    $.contextMenu({
        selector: '.image_library_sm',
        trigger: 'left',
        build: function($trigger, e){
            $('#div_jstree_sm').jstree(true).deselect_all();
            var daMenu = {};
            var daFirstChild = $trigger.children(":first")[0];
            if(daFirstChild.innerHTML === "Small background image library"){
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
                $('#div_jstree_sm').jstree(true).select_node($trigger);
                daMenu = {
                    callback: function(key, options){

                        contextMenuActionSelected($trigger, key, options);
                        //grayOut(false);
                    },
                    items: {
                        //"create_child": {name: "Add", icon: "add"},
                        //"edit_node": {name: "Edit", icon: "edit"},
                        //"entries": {name: "Entries", icon: "paste"},
                        //"sep1": "---------",
                        "remove_node": {name: "Remove", icon: "delete"}
                    }
                };
            }

            return daMenu;
        }
    });
    //-=-=-=-=-=-=-=-=-=-=-=-=- Context Menu Action Block -=-=-=-=-=-=-=-=-=-=-=-=-
    function contextMenuActionSelected(daThing, key, options){
        
        var dialog, daData, daButtons;
        var daForm = document.getElementById("newMenuItemForm_sm");
        var daNodeId = $(daThing).attr("id")?$(daThing).attr("id").split("_")[0]:'';
        
        if(key === "remove_node"){ //-=-=-=-=-=-=-=- Delete Navigation Node (set record status = 'deleted')

            daData = {daAction: key, node: daNodeId};
            var request = $.ajax({
                method: "POST",
                data: daData
            });
            request.done(function(msg){

                //console.log(msg);
                location.reload();
            });
            request.fail(function(jqXHR, textStatus){

                alert("Request failed: " + textStatus);
            });
        }
        else{
            
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
  
            $('#id').attr('disabled', true);

            dialog = $("#dialog-form_sm").dialog({
                autoOpen: false,
                dialogClass: 'rez_ui',
                width: '580',
                modal: true,
                buttons: daButtons,
                close: function(){
                    //daForm.reset();
                    //allFields.removeClass("ui-state-error");
                }
            });

            dialog.find("#newMenuItemForm_sm").on("submit", function(event){

                event.preventDefault();
                $('input').removeAttr('disabled');
                createMenuNode();
            });

            dialog.dialog("open");
            //daForm.reset();
            resizeIframe();
            
            
            function createMenuNode(){
                
                top.grayOut(true);
                
                //daData = {action: key, form: $('#newMenuItemForm_sm').serialize()};
                var daData = new FormData(daForm);
                daData.append("daAction", key);
                
                var request = $.ajax({
                    method: "POST",
                    data: daData,
                    cache: false,
                    contentType: false,
                    processData: false
                });
                
                request.done(function(msg){

                    //console.log(msg);
                    location.reload();
                });
                request.fail(function(jqXHR, textStatus){

                    alert("Request failed: " + textStatus);
                });
                dialog.dialog("close");
            }
        }
    }
</script>
</div>
<?php include '../../footer_nested.php'; ?>