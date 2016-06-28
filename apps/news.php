<?php include '../header_nested.php'; ?>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $sql = '';
    
    if(isset($_POST["action"]) && ((strcasecmp($_POST["action"], "create_parent") == 0) || (strcasecmp($_POST["action"], "create_child") == 0 ))){
        
        $sql = 'insert into news(title, content, created_by, `status`) values (\''.
                addslashes($_POST['title']).'\', \''.
                addslashes($_POST['content']).'\', \''.
                $_SESSION['auth']['id'].'\', \''.
                $_POST['status'].'\')';
    }
    elseif(isset($_POST["action"]) && (strcasecmp($_POST["action"], "remove_node") == 0 )){

        $sql = 'update news set `status`=\'deleted\' where id='.$_POST["node"];
    }
    elseif(isset($_POST["action"]) && (strcasecmp($_POST["action"], "edit_node") == 0 )){

        $sql = 'update news set title=\''.
                addslashes($_POST['title']).'\', content=\''.
                addslashes($_POST['content']).'\', `status`=\''.
                $_POST['status'].'\' where id='.$_POST['id'];
    }

    if(strlen($sql) > 0){
        error_log($sql);
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

<span class='news_list' title="Click to add new news to list."><b>News console</b></span>
<div id="div_jstree"></div>
<div id="dialog-form" title="News console" style="display: none;">
    <form method="post" id='newMenuItemForm' name='newMenuItemForm'>
        <fieldset>
            <table>
                <tr id='node_id' style="display: none;">
                    <td align='right'><label for="id">id</label></td>
                    <td><input type="text" name="id" id="id" disabled></td>
                </tr>
                <tr>
                    <td align='right'><label for="title">title</label></td>
                    <td><input type="text" name="title" id="title"></td>
                </tr>
                <tr>
                    <td align='right'><label for="content">content</label></td>
                    <td><textarea name="content" id="content" cols="42" rows="7"></textarea></td>
                </tr>
                <tr>
                    <td align='right'><label for="status">status</label></td>
                    <td><select name="status" id="status"><option value="active">active</option><option value="inactive">in-active</option><option value="deleted">deleted</option></select></td>
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

$sql = "select id, '#' parent, title `text`, '{\"class\":\"news_list\"}' a_attr from news order by created_on desc";

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
        selector: '.news_list',
        trigger: 'left',
        build: function($trigger, e){
            $('#div_jstree').jstree(true).deselect_all();
            var daMenu = {};
            var daFirstChild = $trigger.children(":first")[0];
            if(daFirstChild.innerHTML === "News console"){
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
        var daForm = document.getElementById("newMenuItemForm");
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
                var sql = 'select id, title, content, status from news where id=' + daNodeId;
                
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
                            else{

                                document.getElementById(attrName).value = attrValue;
                            }
                        }
                    }
                    
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
                
                //daData = {action: key, form: $('#newMenuItemForm').serialize()};
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
            form = dialog.find("#newMenuItemForm").on("submit", function(event){
                event.preventDefault();
                $('input').removeAttr('disabled');
                createMenuNode();
            });
            dialog.dialog("open");
            resizeIframe();
        }
    }
</script>

<?php include '../footer_nested.php'; ?>