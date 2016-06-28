<!DOCTYPE html>
<html>
    <head>
        <title>test</title>
    </head>
<body>
    <!--
<canvas width="800" height="600" style="position: absolute; top: 0; left: 0;" id="myFirstCanvas"></canvas>
<script>
    var c=document.getElementById("myFirstCanvas");
    var ctx=c.getContext("2d");
    
    var img = new Image();
    img.className = "site_bg";
    img.onload = function(){
        ctx.drawImage(img,0,0, img.width, img.height, 0, 0, 800, 600);
        console.log("r2d2: "+img.width);
    };
    img.src = "../?ajax=image&id=2";

    
    alert(screen.width);
    alert(screen.height);
    //window.screen.availWidth
    
</script>
<input type="date">

            <div style="width: 35%; text-align: right; float: left;">
                <label for='firstname1'>Firstname</label>
            </div>
            <div style="width: 65%; text-align: left; float: left;">
                <input type='text' name='firstname1' id='firstname1'>
            </div>
            <div style="width: 35%; text-align: right; float: left;">
                <label for='lastname1'>Lastname</label>
            </div>
            <div style="width: 65%; text-align: left; float: left;">
                <input type='text' name='lastname1' id='lastname1'>
            </div>
            <div style="width: 35%; text-align: right; float: left;">
                <label for='email1'>E-mail</label>
            </div>
            <div style="width: 65%; text-align: left; float: left;">
                <input type='text' name='email1' id='email1'>
            </div>
            <div style="width: 35%; text-align: right; float: left;">
                <label for='phone1'>Phone</label>
            </div>
            <div style="width: 65%; text-align: left; float: left;">
                <input type='text' name='phone1' id='phone1'>
            </div>
            <div id="plus1" style="width: 100%; text-align: right;">
                <a href="javascript: beEzafeYek();">+ one</a>
            </div>
            <div style="width: 100%; text-align: center;">
                <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
            </div>

-->
<div id="reg-diag" title="Registration form">
    <form method="post" id='reg-form' name='reg-form'>
        <fieldset>
            <div style="display: none;">
                <input type="text" name="id" id="id" disabled>
                <input type="text" name="event_type" id="event_type" disabled>
                <input type="text" name="event_date" id="event_date" disabled>
            </div>
            <label for='firstname1' style="display: inline-block; width: 30%; text-align: right;">Firstname</label>
            <input type='text' name='firstname1' id='firstname1' style="display: inline-block; width: 60%; text-align: left;">
            <br>
            <label for='lastname1' style="display: inline-block; width: 30%; text-align: right;">Lastname</label>
            <input type='text' name='lastname1' id='lastname1' style="display: inline-block; width: 60%; text-align: left;">
            <br>
            <label for='email1' style="display: inline-block; width: 30%; text-align: right;">E-mail</label>
            <input type='text' name='email1' id='email1' style="display: inline-block; width: 60%; text-align: left;">
            <br>
            <label for='phone1' style="display: inline-block; width: 30%; text-align: right;">Phone</label>
            <input type='text' name='phone1' id='phone1' style="display: inline-block; width: 60%; text-align: left;">

            <div id="plus1">
                <a href="javascript: beEzafeYek();">+ one</a>
            </div>
            
            <!-- Allow form submission with keyboard without duplicating the dialog button -->
            <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
        </fieldset>
    </form>
</div>

</body>
</html>

<!--

            <table>
                <tr id='node_id' style="display: none;">
                    <td align='right'><label for="id">id</label></td>
                    <td><input type="text" name="id" id="id" disabled>
                        <input type="text" name="event_type" id="event_type" disabled>
                        <input type="text" name="event_date" id="event_date" disabled></td>
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
                        <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
                    </td>
                </tr>
            </table>
-->