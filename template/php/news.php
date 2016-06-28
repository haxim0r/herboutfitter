<div id="hotspot_left">
<?php
$mySQL = new db_mysql();

$sql = "select title, content from news where status='active' order by created_on desc";

$resultset = mysqli_query($mySQL->connection, $sql);
$daOutput = '<ul>';
if($resultset){

    $resultArray = array();
    while ($row = $resultset->fetch_assoc()) {
        
        $daOutput.="<li>";
        $daOutput.=$row["title"];
        $daOutput.=" - ";
        $daOutput.=$row["content"];
        $daOutput.="</li>";
    }
    mysqli_free_result($resultset);
}
$daOutput.="</ul>";
echo $daOutput;
?>
</div>