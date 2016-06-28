<?php require_once 'header.php'; ?>

<script>document.body.className = "masterFrameBody";</script>

<?php
//$daView = new template('html/home.html');
//echo $daView->html;
new template('php/home.php');
?>

<div id='div_daBody'>
    <iframe id='iframe_daBody' name='iframe_daBody'></iframe>
</div>

<?php include 'footer.php'; ?>
