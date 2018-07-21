<?php
$factuur_name = $_GET['factuur'];

header("Content-type: application/pdf");
header("Content-Disposition: inline; filename='.$factuur_name.'.pdf");
@readfile('../factuur_files/'.$factuur_name.'.pdf');
