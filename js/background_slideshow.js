/*-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-  
-=-=- Author: Rez -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-=-=- Friday the 13th: 2016-05-13 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-=   If you didn't notice, this file has 420 lines -> IRIE MON: puf-puf-pas  =-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-*/
if(window.top === window.self){
    
    var backgroundImages_metadata = new Array();
    var backgroundImages = new Array();
    var currentBackgroundImageIndex = 0;
    var slideDelay = 10000; //-=-=- measured in miliseconds
    var autoPlaySlides = true;
    var autoPlayTimeout;
    var opacityStep = -0.03;
    var processingFade = false;

    document.open();
    document.write('<div class="canvas_wrapper">');
    document.write('<canvas id="canvas1" class="site_bg_canvas"></canvas>');
    document.write('<canvas id="canvas2" class="site_bg_canvas"></canvas>');
    document.write('<canvas id="canvas3" class="site_bg_canvas"></canvas>');
    document.write('</div>');
    document.close();
    
    renderBG();
}

function renderBG(){

    if(screen.width <= 640){
        
        daData = {ajax: 'bgList_sm'};
    }
    else{
        
        daData = {ajax: 'bgList_lg'};
    }

    var request = $.ajax({
        
        method: "GET",
        data: daData,
        cache: true
    });

    request.done(function(msg){
        
        var formData = JSON.parse(msg);
        
        for(var i = 0; i < formData.length; i++){

            var obj = formData[i];

            backgroundImages_metadata.push(obj);
        }
        
        slideShow();
    });

    request.fail(function(jqXHR, textStatus){
        
        alert("Request failed: " + textStatus);
    });
}


function slideShow(){

    if(backgroundImages_metadata.length > 0){

        for(var daCounter = 0; daCounter <= (backgroundImages_metadata.length-1); daCounter++){

            backgroundImages.push(null);
        }
        
        var img = new Image();
        img.className = "site_bg";
        img.onload = function(){
            
            backgroundImages[currentBackgroundImageIndex] = img;
            
            changeImage(document.getElementById("canvas1"), backgroundImages[currentBackgroundImageIndex], true);
        };
        img.src = "?ajax=image&id="+backgroundImages_metadata[currentBackgroundImageIndex].id;
        
        if(backgroundImages_metadata.length > 1){
            
            var leftChevy = document.createElement("div");
            leftChevy.id = "chevronLeft";
            leftChevy.className = "chevron chevronLeft";
            leftChevy.setAttribute("onclick", "javascript: getAnotherSlide('back');");
            document.body.appendChild(leftChevy);

            var rightChevy = document.createElement("div");
            rightChevy.id = "chevronRight";
            rightChevy.className = "chevron chevronRight";
            rightChevy.setAttribute("onclick", "javascript: getAnotherSlide('fwd');");
            document.body.appendChild(rightChevy);
            
            var img2 = new Image();
            img2.className = "site_bg";
            img2.onload = function(){

                backgroundImages[currentBackgroundImageIndex+1] = img2;

                changeImage(document.getElementById("canvas3"), backgroundImages[currentBackgroundImageIndex+1], false);
            };
            img2.src = "?ajax=image&id="+backgroundImages_metadata[currentBackgroundImageIndex+1].id;
            
            if(backgroundImages_metadata.length > 2){
                
                var img3 = new Image();
                
                img3.className = "site_bg";
                
                img3.onload = function(){
                    
                    backgroundImages[backgroundImages.length-1] = img3;
                };
                
                img3.src = "?ajax=image&id="+backgroundImages_metadata[backgroundImages_metadata.length-1].id;
            }
        }
    }
}

function changeImage(canvas, img, recurse){
    
    var ctx = canvas.getContext("2d");
    
    var canvas_dimensions = {
        
        max_height : 1200,
        max_width  : 1920,
        width  : 800, // this will change
        height : 600, // this will change
        largest_property : function () {
            return this.height > this.width ? "height" : "width";
        },
        read_dimensions : function (img){
            
            this.width = img.width;
            this.height = img.height;
            return this;
        },
        scaling_factor : function (original, computed) {
            
            return computed / original;
        },
        scale_to_fit : function () {
            
            var x_factor = this.scaling_factor(this.width,  this.max_width),
                y_factor = this.scaling_factor(this.height, this.max_height),

                largest_factor = Math.min(x_factor, y_factor);

            this.width  *= largest_factor;
            this.height *= largest_factor;
        }
    };
    
    canvas_dimensions.read_dimensions(img).scale_to_fit();
    
    canvas.width  = canvas_dimensions.width;
    canvas.height = canvas_dimensions.height;
    
    ctx.clearRect(0,0, canvas_dimensions.width, canvas_dimensions.height);
    ctx.drawImage(img, 0, 0, canvas_dimensions.width, canvas_dimensions.height);
    
    if(recurse){ //-=-=- canvas 3 should not come here...
        
        if(autoPlaySlides){ //-=-=- stop animation sequence if manual control in invoked
            
            autoPlayTimeout = setTimeout(fadeOut, slideDelay);

            var alpha = 1.0;

            function fadeOut(){
                
                processingFade = true;
                
                if (alpha <= 0) {
                    
                    processingFade = false;
                    
                    nextSlide();
                    
                    return;
                }         

                requestAnimationFrame(fadeOut); //-=-=-=- reHash your mind... puf puf pass... -=-=-=-=-

                ctx.clearRect(0,0, canvas.width, canvas.height);

                ctx.globalAlpha = alpha;

                ctx.drawImage(img, 0, 0, canvas_dimensions.width, canvas_dimensions.height);

                alpha += opacityStep;
            }
        }
    }
}

function getAnotherSlide(trajectory){
    
    clearTimeout(autoPlayTimeout);
    
    autoPlaySlides = false;

    if(processingFade){
        
        //-=-=- do nothing
        return;
    }
    
    var origialIndex = currentBackgroundImageIndex;
    
    if(trajectory === "fwd"){
        
        if(currentBackgroundImageIndex+1 === backgroundImages_metadata.length){

            currentBackgroundImageIndex = 0;
        }
        else{

            currentBackgroundImageIndex++;
        }
    }
    else{
        
        if(currentBackgroundImageIndex === 0){

            currentBackgroundImageIndex = backgroundImages_metadata.length-1;
        }
        else{
            
            currentBackgroundImageIndex--;
        }
    }
    
    if(backgroundImages[currentBackgroundImageIndex] === null){
        
        currentBackgroundImageIndex = origialIndex;
        
        return;
    }
    
    changeImage(document.getElementById("canvas3"), backgroundImages[currentBackgroundImageIndex], false);
    
    var daCanvas1, daCanvas2;
    
    if(currentBackgroundImageIndex%2){

        daCanvas1 = document.getElementById("canvas1");
        daCanvas2 = document.getElementById("canvas2");
    }
    else{

        daCanvas1 = document.getElementById("canvas2");
        daCanvas2 = document.getElementById("canvas1");
    }
    
    var ctx = daCanvas1.getContext("2d");
    
    var fadingImage = backgroundImages[origialIndex];
    
    var canvas_dimensions = {
        
        max_height : 1200,
        max_width  : 1920,
        width  : 800, // this will change
        height : 600, // this will change
        largest_property : function () {
            return this.height > this.width ? "height" : "width";
        },
        read_dimensions : function (fadingImage){
            
            this.width = fadingImage.width;
            this.height = fadingImage.height;
            return this;
        },
        scaling_factor : function (original, computed) {
            
            return computed / original;
        },
        scale_to_fit : function () {
            
            var x_factor = this.scaling_factor(this.width,  this.max_width),
                y_factor = this.scaling_factor(this.height, this.max_height),

                largest_factor = Math.min(x_factor, y_factor);

            this.width  *= largest_factor;
            this.height *= largest_factor;
        }
    };
    
    canvas_dimensions.read_dimensions(fadingImage).scale_to_fit();
    
    daCanvas1.width  = canvas_dimensions.width;
    daCanvas1.height = canvas_dimensions.height;
    
    var alpha = 1.0;
    
    function fadeOut(){
        
        processingFade = true;
        
        if (alpha <= 0) {
            
            if(trajectory === "fwd"){
                
                if(((currentBackgroundImageIndex+1) !== backgroundImages_metadata.length)){

                    if(backgroundImages[currentBackgroundImageIndex+1] === null){

                        var img = new Image();
                        
                        img.className = "site_bg";
                        
                        img.onload = function(){

                            backgroundImages[currentBackgroundImageIndex+1] = img;
                        };
                        
                        img.src = "?ajax=image&id="+backgroundImages_metadata[currentBackgroundImageIndex+1].id;
                    }
                }
            }
            else{
                
                changeImage(daCanvas2, backgroundImages[currentBackgroundImageIndex], true);
                
                if((currentBackgroundImageIndex-1) !== 0){
                    
                    if(backgroundImages[currentBackgroundImageIndex-1] === null){

                        var img = new Image();
                        
                        img.className = "site_bg";
                        
                        img.onload = function(){

                            backgroundImages[currentBackgroundImageIndex-1] = img;
                        };
                        
                        img.src = "?ajax=image&id="+backgroundImages_metadata[currentBackgroundImageIndex-1].id;
                    }
                }
            }
            
            processingFade = false;
            
            return;
        }         

        requestAnimationFrame(fadeOut); //-=-=-=- reHash your mind... puf puf pass... -=-=-=-=-

        ctx.clearRect(0,0, daCanvas1.width, daCanvas1.height);

        ctx.globalAlpha = alpha;

        ctx.drawImage(fadingImage, 0, 0, daCanvas1.width, daCanvas1.height);

        alpha += opacityStep;
    }
    
    fadeOut();
}

function nextSlide(){
    
    if(currentBackgroundImageIndex+1 === backgroundImages_metadata.length){
        
        currentBackgroundImageIndex = 0;
        
        changeImage(document.getElementById("canvas1"), backgroundImages[currentBackgroundImageIndex], true);
    }
    else{

        currentBackgroundImageIndex++;

        if(currentBackgroundImageIndex%2){
            
            changeImage(document.getElementById("canvas2"), backgroundImages[currentBackgroundImageIndex], true);
        }
        else{
            
            changeImage(document.getElementById("canvas1"), backgroundImages[currentBackgroundImageIndex], true);
        }
    }
    
    if(((currentBackgroundImageIndex+1) !== backgroundImages_metadata.length)){
        
        if(backgroundImages[currentBackgroundImageIndex+1] === null){
            
            var img = new Image();
            
            img.className = "site_bg";
            
            img.onload = function(){
                
                backgroundImages[currentBackgroundImageIndex+1] = img;
                
                changeImage(document.getElementById("canvas3"), backgroundImages[currentBackgroundImageIndex+1], false);
            };
            
            img.src = "?ajax=image&id="+backgroundImages_metadata[currentBackgroundImageIndex+1].id;
        }
        else{
            
            changeImage(document.getElementById("canvas3"), backgroundImages[currentBackgroundImageIndex+1], false);
        }
    }
    else{
        
        changeImage(document.getElementById("canvas3"), backgroundImages[0], false);
    }
}