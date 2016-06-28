$(document).ready(function(){
    
    top.grayOut(false);
});

function grayOut(vis, options) {
    // Pass true to gray out screen, false to ungray
    // options are optional.  This is a JSON object with the following (optional) properties
    // opacity:0-100         // Lower number = less grayout higher = more of a blackout
    // zindex: #             // HTML elements with a higher zindex appear on top of the gray out
    // bgcolor: (#xxxxxx)    // Standard RGB Hex color code
    // grayOut(true, {'zindex':'50', 'bgcolor':'#0000FF', 'opacity':'70'});
    // Because options is JSON opacity/zindex/bgcolor are all optional and can appear
    // in any order.  Pass only the properties you need to set.
    var options = options || {};
    var zindex = options.zindex || 99999;
    var opacity = options.opacity || 70;
    var opaque = (opacity / 100);
    var bgcolor = options.bgcolor || '#000000';
    var dark = document.getElementById('darkenScreenObject');
    if (!dark) {
        // The dark layer doesn't exist, it's never been created.  So we'll
        // create it here and apply some basic styles.
        // If you are getting errors in IE see: http://support.microsoft.com/default.aspx/kb/927917
        var tbody = document.getElementsByTagName("body")[0];
        var tnode = document.createElement('div');           // Create the layer.
        tnode.innerHTML = '<center><FONT SIZE="20" COLOR="ffffff">Processing Request...</FONT></center>';
        tnode.style.position = 'absolute';                 // Position absolutely
        tnode.style.top = '0px';                           // In the top
        tnode.style.left = '0px';                          // Left corner of the page
        tnode.style.overflow = 'hidden';                   // Try to avoid making scroll bars
        tnode.style.display = 'none';                      // Start out Hidden
        tnode.style.padding = '14% 0 0 0';
        tnode.id = 'darkenScreenObject';                   // Name it so we can find it later
        tbody.appendChild(tnode);                            // Add it to the web page
        dark = document.getElementById('darkenScreenObject');  // Get the object.
    }
    if (vis) {
        // Calculate the page width and height
        /*if (document.body && (document.body.scrollWidth || document.body.scrollHeight)) {
            var pageWidth = document.body.scrollWidth + 'px';
            var pageHeight = document.body.scrollHeight + 'px';
        } else if (document.body.offsetWidth) {
            var pageWidth = document.body.offsetWidth + 'px';
            var pageHeight = document.body.offsetHeight + 'px';
        } else {
            var pageWidth = '100%';
            var pageHeight = '100%';
        }*/
        //set the shader to cover the entire page and make it visible.
        dark.style.opacity = opaque;
        dark.style.MozOpacity = opaque;
        //dark.style.filter='alpha(opacity='+opacity+')';
        dark.style.zIndex = zindex;
        dark.style.backgroundColor = bgcolor;
        dark.style.width = "100%";
        dark.style.height = "100%";
        dark.style.display = 'block';
    } else {
        dark.style.display = 'none';
    }
}

var daToggler_siteBG = 1;

function toggleSiteViz(){
    
    if(++daToggler_siteBG%2){
        
        $("#homeDiv").fadeOut("slow");
    }
    else{
        
        $("#homeDiv").fadeIn("slow");
    }
}

var daToggler_userControls = 0;

function toggleUserControls(){
    
    daToggler_siteBG++;
    
    if(++daToggler_userControls%2){
        
        $("#user_man").fadeIn("slow");
    }
    else{
        
        $("#user_man").fadeOut("slow");
    }
}
/********************************** Session Management START here... *********************************/

function resetTimer() {

    if (top.document.getElementById('timeoutPopup')) {

        top.document.getElementById('timeoutPopup').style.display = 'none';

        top.clearTimeout(top.sessionTimer1);
        top.clearTimeout(top.sessionTimer2);

        // alert user one minute before
        top.sessionTimer1 = top.window.setTimeout("alertUser()", (60000 * (top.wait - 1)));

        // logout user
        top.sessionTimer2 = top.window.setTimeout("logout()", 60000 * top.wait);
    }
}

function alertUser() {

    top.document.getElementById('timeoutPopup').style.display = 'block';
}

function logout() {
    
    toggleUserControls();
    top.grayOut(true);
    top.location.href = '/?logout';
}

function setLoginFieldFocus() {

    var daField2Focus = document.getElementById("ejazat");

    if (daField2Focus != null) {

        var daUsernameField = document.getElementById("esmet");

        if (daUsernameField.value.length > 0) {

            daField2Focus.focus();
            daField2Focus.select();
        }
        else {

            daUsernameField.focus();
        }
    }
}
/********************************** Session Management END here... *********************************/

function navigate(daDestination){
    
    top.grayOut(true);
    
    if (self !== top) {
        
        top.document.getElementById("homeDiv").style.display = "none";
        
        var destinationIframe = top.document.getElementById("iframe_daBody");
    }
    else{
        
        document.getElementById("homeDiv").style.display = "none";
        
        var destinationIframe = document.getElementById("iframe_daBody");
    }
    
    destinationIframe.onload = function(){
        
        if (self !== top) { //-=-=-=- auto resize nested iframes
            
            top.document.getElementById("div_daBody").style.display = "block";
        }
        else{
            try{
                
                document.getElementById("div_daBody").style.display = "block";
                document.getElementById("div_daBody").style.visibility = "visible";
            }
            catch(e){
                console.log("Found a Phenomenon...(Anomaly, Outlier) "+e);
            }
        }
    };
    destinationIframe.src = daDestination;
}

function resizeIframe() {

    if (window.self !== window.top) { //-=-=-=- auto resize nested iframes

        var body = document.body, html = document.documentElement;

        var height = Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight, 240);

        parent.document.getElementById(window.name).style.height = height + "px";
        parent.document.getElementById(window.name).parentNode.style.height = height + "px";
        //console.log(parent.document.getElementById(window.name).style.height + " | " + parent.document.getElementById(window.name).parentNode.style.height);
    }
}
