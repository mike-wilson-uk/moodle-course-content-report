<?php

//Written by Richard Havinga @ Southampton City College originally based on work by Mike Wilson
 
require_once(dirname(__FILE__) . '/../../config.php');
require_once (dirname(__FILE__) . '/../../lib/adminlib.php');
require_login();
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('report/gsb:viewmygsbreport', $context);
global $DB, $CFG;
 //connect to server server, mysql username, mysql password
 $con = mysql_connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass);
	if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
admin_externalpage_setup('report_gsb');
admin_externalpage_setup('report_gsb');
echo $OUTPUT->header().
     $OUTPUT->heading(get_string('gsbadmin', 'report_gsb'));

  //connect to db
  mysql_select_db($CFG->dbname, $con);
  
  
	  //these are all coming from gsb_by department
	$submitted = $_POST['submit2'];
	$course = $_POST['course'];
	
	$categoryid = $_POST['categoryid'];
	
	//uncomment next 3 lines for error checking
	//echo"Category = ";
	//echo $categoryid;
	//echo "<br />";
	$prefix = $CFG->prefix;
	$table = "gsb_content";
	$ctable = "course";
	$cattable = "course_categories";
	if (ISSET($submitted))
	{
	//"SELECT mdl_course.id, " . $prefix . "gsb_content.gsb, fullname FROM " . $prefix . "gsb_content, " . $prefix . "course where " . $prefix . "course.category = $categoryid order by id asc"
		//get course codes and override status from department and it also includes the regular expression for B codes and ignores nominal codes based upon the sumary field with $nominal
	//$get_dept_codes = mysql_query("SELECT mdl_course.id, gsboverride FROM mdl_gsb_content, mdl_course where (((mdl_course.category) = $categoryid)) order by id asc ");
	//("SELECT id, shortname, mdl_gsb_content.gsb, fullname, mdl_gsb_content.gsboverride, mdl_course.summary FROM mdl_gsb_content, mdl_course where (((mdl_course.category) = $categoryid) and (mdl_course.summary != '$nominal') and (mdl_course.shortname REGEXP '$shortNameReg')) order by id asc ");
	$get_dept_codes = mysql_query("SELECT " . $prefix . "course.id, gsboverride FROM " . $prefix . "gsb_content, " . $prefix . "course where " . $prefix . "course.category = $categoryid order by id asc");

	
	
	//loop through and update gsb status for courses
	while($row = mysql_fetch_array($get_dept_codes))
		{
		$courseid = $row['id'];
		//yes or no
		$gsboverride = $row['gsboverride'];
		//auto calculated
		$current = $_POST['gsb'][$courseid]['current'];
		//override value
		$override = $_POST['gsb'][$courseid]['override'];
		
		//backup of oldgsb
		//$oldgsb = mysql_query("update mdl_gsb_content set oldgsb = $current");
		//uncomment next 4 lines for error checking
		//echo "<br />";
		//echo $courseid;
		//echo $current;
		//echo $override;
		//echo $gsboverride;
		//echo $nominal;
		//echo $shortNameReg;
		//populate the override column for manual gsb overrides

		
	if ($gsboverride != "" && $override != $current){
	switch ($override) {
	case "":
	case "remove":
        $finalgsb = $current;
		//		$update_overrides = mysql_query("update mdl_gsb_content set gsb = '$finalgsb', gsboverride = 'no' where id = $courseid");
        $updgsb->id = $courseid;
		$updgsb->gsb = $finalgsb;
		$updgsb->gsboverride = 'no';
		if ($DB->record_exists('gsb_content', array('id' => $updgsb->id))) {
		$DB->update_record('gsb_content', $updgsb); 
} 
		break;
    case "In Dev":
        $finalgsb = ""; 
		//$update_overrides = mysql_query("update mdl_gsb_content set gsb = '$finalgsb', gsboverride = 'yes' where id = $courseid");
		$updgsb->id = $courseid;
		$updgsb->gsb = $finalgsb;
		$updgsb->gsboverride = 'yes';
		if ($DB->record_exists('gsb_content', array('id' => $updgsb->id))) {
		$DB->update_record('gsb_content', $updgsb); 
} 
        break;
	default: 
		$finalgsb = $override;
		/*$overridegsb = mysql_query("update mdl_gsb_content set gsboverride = 'yes' where id = $courseid");
		$update_overrides = mysql_query("update mdl_gsb_content set gsb = '$finalgsb' where id = $courseid");*/
		$updgsb->id = $courseid;
		$updgsb->gsb = $finalgsb;
		$updgsb->gsboverride = 'yes';
		if ($DB->record_exists('gsb_content', array('id' => $updgsb->id))) {
		$DB->update_record('gsb_content', $updgsb); 
		} 
		break;
}
		
		}
		/*if ($override == "In Dev"){ $finalgsb = ""; 
		$update_overrides = mysql_query("update mdl_gsb_content set gsb = '$finalgsb', gsboverride = 'yes' where courseid = $courseid");
		 
		}else if($override == "remove"){
		//$overridegsb = mysql_query("update mdl_gsb_content set gsboverride = 'no' where courseid = $courseid");
		$finalgsb = $current;
		$update_overrides = mysql_query("update mdl_gsb_content set gsb = '$finalgsb', gsboverride = 'no' where courseid = $courseid");
		//if it hasn't been over ridden this time
		}else if($override == ""){
		
		//if it has never been over ridden or has had its override reset
			if($gsboverride == "no"){
			$finalgsb = $current;
			}
		}else{ $finalgsb = $override;
		if ($override != "" && $override != "remove") $overridegsb = mysql_query("update mdl_gsb_content set gsboverride = 'yes' where courseid = $courseid");
		
			
		$update_overrides = mysql_query("update mdl_gsb_content set gsb = '$finalgsb' where courseid = $courseid");
		}*/
		//}
		}
	

		echo "<h3><font face='Arial' color='#FF0000'>Gsb medals have been processed for </font><font face='Arial' color='#0000FF'>$course</font></h3>";
		echo "<a href='javascript:location.reload(true)'>Refresh this page</a>";

	}
		
	//get course codes from department
	//$sql = mysql_query("SELECT id, name FROM mdl_course_categories where depth = 1 order by name asc");
	$sql = "SELECT id, name FROM " . $prefix . "course_categories";
	$params = array(1);
	$get_dept_codes = $DB->get_records_sql_menu($sql,$params);
	//print_r($get_dept_codes);
	//$get_dept_codes = $DB->get_records($cattable,array('depth'=>'1'),null,'id, name');
		echo "<form method='POST' action='$CFG->wwwroot/report/gsb/gsb_by_department.php'><p>";
		echo "<select size='1' name='dept'>";
		$i =1; 
		//loop through and list department names in drop down box
		foreach ($get_dept_codes as $record => $value) {
		
		//while($row = mysql_fetch_array($get_dept_codes))
			
			$catid = $record;
			$catname = $value;
			echo "<option name='category' value=$catid>$catname</option>";
			//echo $catid;
			//echo $catname;
			echo "<br />";
			}
	echo "</select></p><p><input type='submit' value='Submit' name='submit'></p></form>";
	
	$totalcourses = mysql_query("SELECT " . $prefix . "role_assignments.roleid, Count(" . $prefix . "role_assignments.roleid) AS studentsenrolled, " . $prefix . "course.id
								 FROM " . $prefix . "user INNER JOIN ((" . $prefix . "role_assignments INNER JOIN " . $prefix . "context ON " . $prefix . "role_assignments.contextid = " . $prefix . "context.id) INNER JOIN (" . $prefix . "course INNER JOIN " . $prefix . "course_categories ON " . $prefix . "course.category = " . $prefix . "course_categories.id) ON " . $prefix . "context.instanceid = " . $prefix . "course.id) ON " . $prefix . "user.id = " . $prefix . "role_assignments.userid
								 GROUP BY " . $prefix . "role_assignments.roleid, " . $prefix . "course.id
								 HAVING (((" . $prefix . "role_assignments.roleid)=5))
								 ORDER BY Count(" . $prefix . "role_assignments.roleid)");
								
    $total = mysql_fetch_assoc ( $totalcourses );								
	
	$total2 = count( $total['id'] );
	//gsb table summary
	
				$gold = mysql_query("SELECT COUNT(gsb) as gold_total from " . $prefix . "gsb_content WHERE gsb = 'Gold'");
				$gold_total = mysql_fetch_assoc( $gold );

				$silver = mysql_query("SELECT COUNT(gsb) as silver_total from " . $prefix . "gsb_content WHERE gsb = 'Silver'");
				$silver_total = mysql_fetch_assoc( $silver );

				$bronze = mysql_query("SELECT COUNT(gsb) as bronze_total from " . $prefix . "gsb_content WHERE gsb = 'Bronze'");
				$bronze_total = mysql_fetch_assoc( $bronze );
				
				$bronze = mysql_query("SELECT COUNT(gsb) as bronze_total from " . $prefix . "gsb_content WHERE gsb = 'Bronze'");
				$bronze_total = mysql_fetch_assoc( $bronze );
				
				$count_courses = mysql_query("SELECT Count(" . $prefix . "course.id) AS total_courses
												FROM " . $prefix . "course INNER JOIN " . $prefix . "course_categories ON " . $prefix . "course.category = " . $prefix . "course_categories.id
												;
											");
				$total_courses = mysql_fetch_assoc( $count_courses );

								
				$total = $gold_total['gold_total'] + $silver_total['silver_total']+ $bronze_total['bronze_total'];

				$gold_count = $gold_total['gold_total'];
				$silver_count = $silver_total['silver_total'];
				$bronze_count = $bronze_total['bronze_total'];


				$total = $gold_count + $silver_count + $bronze_count;

				$total_medals = $total_courses['total_courses'];
				
				
				$indev_count = $total_medals - $total;

				$gold_perc = $gold_count / $total_medals * 100;
				$gold_perc_form = sprintf ('%01.1f', $gold_perc);

				$silver_perc = $silver_count / $total_medals * 100;
				$silver_perc_form = sprintf ('%01.1f', $silver_perc);

				$bronze_perc = $bronze_count / $total_medals * 100;
				$bronze_perc_form = sprintf ('%01.0f', $bronze_perc);

				$indev_perc = $indev_count / $total_medals * 100;
				$indev_perc_form = sprintf ('%01.0f', $indev_perc);

		
				$message = "<p align='center'>Citybit course quality and content medals:</p>";
	$total = $gold_count + $silver_count + $bronze_count + $indev_count;
				$table = "
					<div align='center'>
						<table style='text-align: left; width: 20%;' border='0'
						 cellpadding='2' cellspacing='2'>
						  <tbody>
							<tr>
							  <td style='width: 100%;'>


						<div align='center'>
						  <table width='100%' border='0' cellspacing='0' cellpadding='0'>
							<tr>
							  <td width='100%' colspan='4'><font face='Arial'><h3 align='center'>GSB Summary for: $moodle_title</h3></font></td>
							</tr>
							<tr>
							  <td width='30%'><div align='right'><img src='$CFG->wwwroot/report/gsb/pics/gold_icon.png'></div></td>
							  <td width='35%'><font face='Arial' size='2'><div align='centre'>&nbsp;&nbsp;&nbsp;Gold</div></td>
							  <td width='25%'><font face='Arial' size='2'><div align='centre'>" . $gold_count . "</div></td>
							  <td width='10%'><div align='left'><font face='Arial' size='1'>" . $gold_perc_form . "%</div></td>
							</tr>
							<tr>
							  <td width='30%'><div align='right'><img src='$CFG->wwwroot/report/gsb/pics/silver_icon.png'></div></td>
							  <td width='35%'><font face='Arial' size='2'><div align='left'>&nbsp;&nbsp;&nbsp;Silver</div></td>
							  <td width='25%'><font face='Arial' size='2'><div align='left'>" . $silver_count . "</div></td>
							  <td width='10%'><div align='left'><font face='Arial' size='1'>" . $silver_perc_form . "%</div></td>
							</tr>
							<tr>
							  <td width='30%'><div align='right'><img src='$CFG->wwwroot/report/gsb/pics/bronze_icon.png'></div></td>
							  <td width='35%'><font face='Arial' size='2'><div align='left'>&nbsp;&nbsp;&nbsp;Bronze</div></td>
							  <td width='25%'><font face='Arial' size='2'><div align='left'>" . $bronze_count . "</div></td>
							  <td width='10%'><div align='left'><font face='Arial' size='1'>" . $bronze_perc_form . "%</div></td>
							</tr>
							<tr>
							  <td width='30%'>&nbsp;</td>
							  <td width='35%'><font face='Arial' size='2'><div align='left'>&nbsp;&nbsp;&nbsp;In Dev</div></td>
							  <td width='25%'><font face='Arial' size='2'><div align='left'>" . $indev_count . "</div></td>
							  <td width='10%'><div align='left'><font face='Arial' size='1'>" . $indev_perc_form . "%</div></td>
							</tr>
								  </table>
						  <hr>
						<table width='100%' border='0' cellpadding='0' cellspacing='0'>
							<tr>
							  <td width='30%'><div align='left'>&nbsp;</div></td>
							  <td width='35%'><font face='Arial' size='2'><div align='left'>Total Courses</div></td>
							  <td width='25%'><font face='Arial' size='2'><div align='left'>" . $total . "</div></td>
							  <td width='10%'><div align='left'>&nbsp;</div></td>
							</tr>
						  </table>
						</div>

						</td>
							  
							</tr>
						  </tbody>
						</table>
						</div>
						";

		//print_r('$count_dev');
		echo $count_dev;
		echo $total_dev;
		echo $table;
			
	$downloadgsb = "<p><font face='Arial'><a href='$CFG->wwwroot/report/gsb/download_gsb.php'>Download GSB Report to Excel</a></font></p>";
	echo $downloadgsb;
	echo "<p><font face='Arial' size='1'><br/>Note: you should process the GSB Medals for each department before attempting to download the GSB Report</a></font></p>";
	echo $OUTPUT->footer();
	?>