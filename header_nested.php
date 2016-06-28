<?php require 'php/ajax.php'; ?>
<?php require 'php/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="goods and services for recreational outdoor activities in San Bernardino County USA since 2016">
        <meta name="keywords" content="hike,hiking,fishing,skiing,snowboarding,outfitter,camping,rental">
        <meta name="author" content="Reza Moini-Araghi">
        
        <!--<link rel="icon" type="ICO" href="/images/favicon.ico">-->
        <link href='https://fonts.googleapis.com/css?family=Dancing+Script' rel='stylesheet' type='text/css'>
        
        <link rel="stylesheet" media="(min-width: 790px)" href="/css/wgt790.css">
        <link rel="stylesheet" media="(max-width: 790px)" href="/css/wgt790.css">
        
        <script src="/js/jquery-2.2.3.min.js"></script>
        <script src="/js/global.js"></script>        

        <?php if(isset($_SESSION['auth'])){ ?>
            <script>
                var wait = 420;
                var sessionTimer1, sessionTimer2;

                sessionTimer1 = setTimeout("alertUser()", (60000 * (wait - 1)));
                sessionTimer2 = setTimeout("logout()", 60000 * wait);
            </script>
        <?php } ?>
            
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-77623106-1', 'auto');
            ga('send', 'pageview');
        </script>
        
        <title>herb'Noutfitter - admin</title>
    </head>
    <body>