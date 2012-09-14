<?php

//Written by Mike Wilson @ Southampton City College 01/05/2010
require_once(dirname(__FILE__) . '/../../config.php');

  //connect to server server, mysql username, mysql password
 $con = mysql_connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass);
	if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

  //connect to db
  mysql_select_db($CFG->dbname, $con);

$select = "SELECT mdl_course.id, mdl_course.sortorder, mdl_course_categories.name as category, mdl_course.fullname as coursename, mdl_course.shortname, mdl_gsb_content.linksnum, mdl_gsb_content.booknum, mdl_gsb_content.labelnum, mdl_gsb_content.assignmentnum,  mdl_gsb_content.turnitinnum, mdl_gsb_content.questnum, mdl_gsb_content.quiznum, mdl_gsb_content.interactnum as compass_resources, mdl_gsb_content.embednum as embedded_videos, mdl_gsb_content.wikinum, mdl_gsb_content.chatnum, mdl_gsb_content.forumnum, mdl_gsb_content.visible as unit_visible, mdl_gsb_content.enrolnum as student_enrolments
FROM (mdl_gsb_content INNER JOIN mdl_course ON mdl_gsb_content.id = mdl_course.id) INNER JOIN mdl_course_categories ON mdl_course.category = mdl_course_categories.id
ORDER by sortorder ASC;"; 
 
$export = mysql_query ( $select ) or die ( "Sql error : " . mysql_error( ) ); 
 
$fields = mysql_num_fields ( $export ); 
 
for ( $i = 0; $i < $fields; $i++ ) 
{ 
    $header .= mysql_field_name( $export , $i ) . "\t"; 
} 
 
while( $row = mysql_fetch_row( $export ) ) 
{ 
    $line = ''; 
    foreach( $row as $value ) 
    {                                             
        if ( ( !isset( $value ) ) || ( $value == "" ) ) 
        { 
            $value = "\t"; 
        } 
        else 
        { 
            $value = str_replace( '"' , '""' , $value ); 
            $value = '"' . $value . '"' . "\t"; 
        } 
        $line .= $value; 
    } 
    $data .= trim( $line ) . "\n"; 
} 
$data = str_replace( "\r" , "" , $data ); 
 
if ( $data == "" ) 
{ 
    $data = "\n(0) Records Found!\n";                         
} 
 
header("Content-type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=gsb_report.xls"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 
print "$header\n$data";

	include '..\..\..\footer.php'; 

?>