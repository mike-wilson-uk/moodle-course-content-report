<?php 

//Written by Richard Havinga @ Southampton City College originally based on work by Mike Wilson

require_once(dirname(__FILE__) . '/../../config.php');
require_once (dirname(__FILE__) . '/../../lib/adminlib.php');

global $DB, $CFG;

 $con = mysql_connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass);
	if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
admin_externalpage_setup('report_gsb');
echo $OUTPUT->header().
     $OUTPUT->heading(get_string('gsbdepartment', 'report_gsb'));
	 
	 
  //connect to db
  mysql_select_db($CFG->dbname, $con);
$prefix = $CFG->prefix;
 
	$categoryid = $_POST['dept'];
	//get course category name
	$get_dept_name = mysql_query("SELECT name FROM " . $prefix . "course_categories where id = $categoryid");
	$dept_name = mysql_fetch_assoc($get_dept_name);
	$dept_name_text = $dept_name['name'];
	
	$get_dept_codes1 = mysql_query("Select id from " . $prefix . "course where category = $categoryid");	
	//loop through and create " . $prefix . "gsb_content lines
	while($row1 = mysql_fetch_array($get_dept_codes1))
		{
		$courseid1 = $row1['id'];
		$insert_gsb_row = mysql_query("INSERT INTO $dbname." . $prefix . "gsb_content (`id`, `linksnum`, `booknum`, `labelnum`, `assignmentnum`, `turnitinnum`, `questnum`, `quiznum`, `compass_resources`, `chatnum`, `forumnum`, `wikinum`, `interactnum`, `embednum`, `visible`, `gsb`, `oldgsb`, `gsboverride`, `enrolnum`) VALUES ($courseid1, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0','', '0', 'no', '0')");
		}
					
	//get course codes from current department for gsb row insert
	$get_dept_codes1 = mysql_query("Select id from " . $prefix . "course where category = $categoryid");
	echo "<h1><font face='Arial'>$dept_name_text</font></h1>";
	
	echo "<font face='Arial'>The table below contains the courses from the $dept_name_text category.<br /><br />The 'Current GSB Score' reflects what is currently recorded for each course in the database. This was updated last time the GSB medals were processed. The courses have been automatically weighed and the 'Auto Calculated GSB' column shows the new medals. Please use the course links below to moderate the courses and if you don't agree with the auto calculated gsb score use the 'Override GSB' column to override the medal.<br/><br/>The 'Auto Calculated GSB' column will be used to update each course medal unless you choose to override the medal.<br/><br/>Press the 'Process GSB Medals' button below to update the medals.</font>";
		
	echo "<br /><br />";
	
	echo "<form method='post' action='$CFG->wwwroot/report/gsb/index.php' name='gsb_process_form'><br><br><input type='submit' name='submit2' value='Process GSB Medals'></font></p><br>";
	
	echo"
		<table border='1' cellspacing='0' cellpadding='2' width='100%'>
		<tr>
			<td bgcolor='#C0C0C0'><b><font face='Arial' size='3'>ID</font></b></td>
			<td bgcolor='#C0C0C0'><b><font face='Arial' size='3'>Shortname</font></b></td>
			<td bgcolor='#C0C0C0'><b><font face='Arial' size='3'>Course name (click name to visit course)</font></b></td>
			<td bgcolor='#C0C0C0'><b><font face='Arial' size='3'>Current GSB Score</font></b></td>
			<td bgcolor='#C0C0C0'><b><font face='Arial' size='3'>Auto Calculated GSB</b></font></b></td>
			<td bgcolor='#C0C0C0' width='120'><b><font face='Arial' size='3'>Override GSB</b></font></b></td>
			<td bgcolor='#C0C0C0' width='120'><b><font face='Arial' size='3'>Manual Medal</b></font></b></td>
		</tr>";
	
	
	$get_dept_codes = mysql_query("SELECT " . $prefix . "course.id, " . $prefix . "course.shortname, " . $prefix . "course.fullname, " . $prefix . "gsb_content.gsb, " . $prefix . "gsb_content.enrolnum, " . $prefix . "gsb_content.gsboverride
									FROM " . $prefix . "course INNER JOIN " . $prefix . "gsb_content ON " . $prefix . "course.id = " . $prefix . "gsb_content.id
										WHERE (((" . $prefix . "course.category)=$categoryid))
									ORDER BY " . $prefix . "course.id;");
		//loop through and process gsb stats for courses 
	while($row = mysql_fetch_array($get_dept_codes))
		{
 			$courseid = $row['id'];
			$courseshortname = $row['shortname'];
			$coursefullname = $row['fullname'];
			$old_gsb_score = $row['gsb'];
			$gsboverride = $row['gsboverride'];
			$dbname = $CFG->dbname;
			if ($old_gsb_score == "") $old_gsb_score = "";
			else $old_gsb_score = $old_gsb_score;
			
			
			//selecting the context id for enrolments. This then can be used to search the number of course enrolments. 
			$level = '50';
			$context = mysql_query("select " . $prefix . "context.id from " . $prefix . "context WHERE " . $prefix . "context.contextlevel = '50' AND " . $prefix . "context.instanceid = '$courseid'");
			$context_array = mysql_fetch_assoc( $context );
			$contextid = $context_array['id'];
			//echo $context_array['id'];
			//echo "<br/>//Calculate number of enrolments";

			//sql statement to search the number of course enrolments based upon context id. 								
			$enrolnum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.enrolnum = (SELECT count(*) FROM " . $prefix . "role_assignments where contextid=$contextid and roleid = 5) where " . $prefix . "gsb_content.id=$courseid");
			//$exclusion = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.gsb = exclude where " . $prefix . "course.shortname REGEXP '$shortNameReg'");
			
			//bronze stats
			$standardslinknum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.linksnum = (SELECT count(*) FROM " . $prefix . "resource where course=$courseid) where " . $prefix . "gsb_content.id=$courseid");
			$standardsbooknum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.booknum = (SELECT count(*) FROM " . $prefix . "book where course=$courseid) where " . $prefix . "gsb_content.id=$courseid");
			$standardslabelnum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.labelnum = (SELECT count(*) FROM " . $prefix . "label where course=$courseid) where " . $prefix . "gsb_content.id=$courseid");			
			
			//silver stats
			$standardsassnum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.assignmentnum = (SELECT count(*) FROM " . $prefix . "assignment where course=$courseid) where " . $prefix . "gsb_content.id=$courseid");
			$standardsttinum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.turnitinnum = (SELECT count(*) FROM " . $prefix . "turnitintool where course=$courseid) where " . $prefix . "gsb_content.id=$courseid");
			$standardsfeedbacknum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.feedbacknum = (SELECT count(*) FROM " . $prefix . "feedback where course=$courseid) where " . $prefix . "gsb_content.id=$courseid");
			$standardscompassnum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.interactnum = (SELECT count(*) FROM " . $prefix . "equella where course=$courseid) where " . $prefix . "gsb_content.id=$courseid");
			$standardsquestnum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.questnum = (SELECT count(*) FROM " . $prefix . "questionnaire where course=$courseid) where " . $prefix . "gsb_content.id=$courseid");
			$standardsquiznum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.quiznum = (SELECT count(*) FROM " . $prefix . "quiz where course=$courseid) where " . $prefix . "gsb_content.id=$courseid");	
			$standardsembednum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.embednum = (select count(id) as embed_vid_count from " . $prefix . "label where intro like '%embed%' and course = $courseid) where " . $prefix . "gsb_content.id=$courseid");	
				
			//gold stats
			$standardschatnum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.chatnum = (SELECT count(*) FROM " . $prefix . "chat where course=$courseid) where " . $prefix . "gsb_content.id=$courseid");
			$standardsforumbacknum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.forumnum = (SELECT count(*) FROM " . $prefix . "forum where course=$courseid and type <> 'news') where " . $prefix . "gsb_content.id=$courseid");
			$standardswikinum = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.wikinum = (SELECT count(*) FROM " . $prefix . "wiki where course=$courseid) where " . $prefix . "gsb_content.id=$courseid");	
		
			//unit visible or hidden
			$unit_visible = mysql_query("update " . $prefix . "gsb_content set " . $prefix . "gsb_content.visible = (SELECT visible FROM " . $prefix . "course where id=$courseid) where " . $prefix . "gsb_content.id=$courseid");
		
			//Gold, Silver, Bronze logic
			$bronze_score = mysql_query("select " . $prefix . "gsb_content.linksnum from " . $prefix . "gsb_content where " . $prefix . "gsb_content.id = $courseid");

			$bs = mysql_fetch_assoc( $bronze_score );

			$bss = $bs['linksnum'];
			
			if ($bss < 25) $gsb_score = "";

			else 
			
				{ 

				$gsb_bronze = 1;

				$silver_fetch = mysql_query("select " . $prefix . "gsb_content.assignmentnum, " . $prefix . "gsb_content.interactnum, " . $prefix . "gsb_content.turnitinnum, " . $prefix . "gsb_content.questnum, " . $prefix . "gsb_content.quiznum, " . $prefix . "gsb_content.embednum from " . $prefix . "gsb_content where id = $courseid");

				$silver_array = mysql_fetch_assoc( $silver_fetch );

				$silver_counter = 0;

				if ($silver_array['assignmentnum'] > 0) $silver_counter ++;
				if ($silver_array['interactnum'] > 0) $silver_counter ++;
				if ($silver_array['feedbacknum'] > 0) $silver_counter ++;
				if ($silver_array['turnitinnum'] > 0) $silver_counter ++;
				if ($silver_array['quiznum'] > 0) $silver_counter ++;
				if ($silver_array['embednum'] > 3) $silver_counter ++;
				if ($silver_counter > 1) $gsb_silver = 10;
				else $gsb_silver = 0;

				$gold_fetch = mysql_query("select " . $prefix . "gsb_content.forumnum, " . $prefix . "gsb_content.chatnum, " . $prefix . "gsb_content.wikinum, " . $prefix . "gsb_content.imailnum from " . $prefix . "gsb_content where id = $courseid");

				$gold_array = mysql_fetch_assoc( $gold_fetch );

				$gold_counter = 0;

				if ($gold_array['forumnum'] > 0) $gold_counter ++;
				if ($gold_array['chatnum'] > 0) $gold_counter ++;
				if ($gold_array['wikinum'] > 0) $gold_counter ++;

				if ($gold_counter > 0) $gsb_gold = 100;
				else $gsb_gold = 0;

				$gsb = $gsb_bronze + $gsb_silver + $gsb_gold;

				if ($gsb == 111) $gsb_score = "Gold";
				else if ($gsb == 11) $gsb_score = "Silver";
				else if ($gsb == 1) $gsb_score = "Bronze";
				else if ($gsb == 101) $gsb_score = "Bronze";
				else $gsb_score = "&nbsp;";
				
												
				}
				
		
				
				echo   "<tr>
					
				
						<td><font face='Arial' size='2'>$courseid</font></td>
						<td><font face='Arial' size='2'>$courseshortname</font></td>
						<td><font face='Arial' size='2'><a target='_blank' title='Click to enter this course' href='$CFG->wwwroot/course/view.php?id=$courseid'>$coursefullname</a></font></td>
						<td><font face='Arial' size='2'>$old_gsb_score</font></td>
						<td><font face='Arial' size='2'>$gsb_score</font></td>
						<td width='120'>
					
						<select size='1' name='gsb[$courseid][override]' style='font-family: Arial; font-size: 10pt; width: 120'>
						<option></option>
						<option value='Gold'>Gold</option>
						<option value='Silver'>Silver</option>
						<option value='Bronze'>Bronze</option>
						<option value='In Dev'>In Dev</option>
						<option value='exclude'>Exclude</option>
						<option value='remove'>Remove Over Ride</option>
						</select></td>
						<td><font face='Arial' size='2'>$gsboverride</font></td></tr>

						<input type='hidden' name='gsb[$courseid][prev]' value=$old_gsb_score>
						<input type='hidden' name='gsb[$courseid][current]' value='$gsb_score'>
						<input type='hidden' name='courseid' value=$courseid>
						<input type='hidden' name='categoryid' value=$categoryid>";

										
		}
		
	echo "<input type='hidden' name='course' value='$dept_name_text'>
			</form></table>";
	
	echo $OUTPUT->footer();
?>