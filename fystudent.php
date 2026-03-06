<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$BR = $_GET['BR'];
	$GR = $_GET['GR'];
} else {
	exit("Error: No information\n");
}

include("fullarray.php");
include("semid.php");
$today = date("Y-m-d-H:i:s");
$dtstamp = date("Ymd\\THis");
$ndays = $dstart->diff($dend)->days;
$semid = generate_sem_code_text();
$semcode = generate_sem_code();
header('Content-Type: text/calendar');
header("Content-disposition: attachment; filename=$semcode-FY-$BR-$GR.ics");

$file_path = "ugfytt";
$fp = fopen($file_path, 'r');
if($fp) {
$i=0;
while (($line = fgets($fp)) !== false) {
		$events[$i] = explode(" ", $line);
		$events[$i][4] = substr($events[$i][4], 0, -1);
		$i++;
	}
}
else exit("Could not open ugfytt.\n");
$Nl = $i;

if(!($lines = file_get_contents("ugfygrps")))
	exit("Could not open ugfygrps.\n");
$linesarray = explode(PHP_EOL, $lines); $nbr = count($linesarray)-1;
for($i=0; $i<$nbr; $i++) {
	$brarr = explode(" ", $linesarray[$i]);
	$BrGroups[$brarr[0]] = explode(",", $brarr[1]);
}

if(!($lines = file_get_contents("ugfycrss")))
	exit("Could not open ugfycrss.\n");
$linesarray = explode(PHP_EOL, $lines); $nbr = count($linesarray)-1;
for($i=0; $i<$nbr; $i++) {
	$brarr = explode(" ", $linesarray[$i]);
	$BrCourses[$brarr[0]] = explode(",", $brarr[1]);
}

for($i=0; $i<$Nl; $i++) {
	$events[$i]['groups'] = explode(",", $events[$i][3]);
}
# Course-wise

$CourseEvents = array();
for($i=0; $i<$Nl; $i++) {
	$courseid = $events[$i][2];
	if (!array_key_exists($courseid, $CourseEvents)) {
		$CourseEvents["$courseid"] = array();
		$j=0;
	}
	else 
		$j=count($CourseEvents["$courseid"]);	
	$CourseEvents["$courseid"][$j]["dow"] = $events[$i][0];
	list($from, $to) = explode("-", $events[$i][1]);
	$CourseEvents["$courseid"][$j]["start"] = $from;
	$CourseEvents["$courseid"][$j]["stop"] = $to;
	$CourseEvents["$courseid"][$j]["groups"] = $events[$i]["groups"];
	$CourseEvents["$courseid"][$j]["venue"] = $events[$i][4];
}

# Group-wise

$GroupEvents = array();
for($i=0; $i<$Nl; $i++) {
	$courseid = $events[$i][2];
	$groups = $events[$i]["groups"];
	foreach ($groups as $group) {
		if (!isset($GroupEvents[$group]))
			$GroupEvents[$group] = array();
		if (!array_key_exists($courseid, $GroupEvents[$group])) {
			$GroupEvents[$group]["$courseid"] = array();
			$j=0;
		}
		else
			$j=count($GroupEvents[$group]["$courseid"]);
		list($from, $to) = explode("-", $events[$i][1]);
		$GroupEvents[$group]["$courseid"][$j]["dow"] = $events[$i][0];
		$GroupEvents[$group]["$courseid"][$j]["start"] = $from;
		$GroupEvents[$group]["$courseid"][$j]["stop"] = $to;
		$GroupEvents[$group][$courseid][$j]["venue"] = $events[$i][4];
	}
}

$GpEvents = $GroupEvents[$GR];
foreach($GpEvents as $key => $value) {
	$keystripped = substr($key, 0, -1);
	if(in_array($keystripped, $BrCourses[$BR]))
		$BrGpEvents[$key] = $value;
}

# Now we create calendar for each of the BrGpEvents
echo "BEGIN:VCALENDAR\r\n";
echo "CALSCALE:GREGORIAN\r\n";
echo "PRODID:-/IIT Delhi/php calendar generator 1.1/EN\r\n";
echo "VERSION:2.0\r\n";
echo "X-WR-CALNAME:IITD-$semcode-FY-$BR-$GR-$today\r\n";
echo "X-WR-CALDESC:IITD $semid 1st yr $BR gp $GR $today\r\n";
echo "BEGIN:VTIMEZONE\r\n";
echo "TZID:Asia/Kolkata\r\n";
echo "BEGIN:STANDARD\r\n";
echo "TZOFFSETFROM:+0530\r\n";
echo "TZOFFSETTO:+0530\r\n";
echo "DTSTART:19451015T000000\r\n";
echo "TZNAME:IST\r\n";
echo "END:STANDARD\r\n";
echo "END:VTIMEZONE\r\n";
foreach($BrGpEvents as $courseid => $events) {
	$days_of_interest = array_column($events, "dow");
	$nclasses = 0;
	for($k=0; $k<=$ndays; $k++) {
		$dstr = "P" . $k . "D";
		$currdate = $dstart->add(new DateInterval($dstr));
		$currstr = $currdate->format('Y-m-d');
		$dow = $semcal[$currstr]["status"];
		if(in_array($dow, $days_of_interest))
		{
			$nclasses++;
			$len = count($events);
			for($i=0; $i<$len; $i++) {
				if($events[$i]["dow"] == $dow) {
					$starttime = $events[$i]["start"];
					$stoptime = $events[$i]["stop"];
					$v = $events[$i]["venue"];
					$v = str_replace(",", "\,", $v);
				}
			}
			$cstr = $currdate->format('Ymd');
			$start = $cstr . "T" . $starttime . "00";
			$stop  = $cstr . "T" . $stoptime  . "00";
			$ucid = str_replace(",", "a", $courseid);
			$ucid = str_replace("-", "t", $ucid);
			$ucid = str_replace(":", "", $ucid);
			$uid = "IITDauto" . $semcode . $ucid . "n" . $nclasses;
			echo "BEGIN:VEVENT\r\n";
			echo "DTSTART;TZID=Asia/Kolkata:$start\r\n";
			echo "DTEND;TZID=Asia/Kolkata:$stop\r\n";
			echo "LOCATION:$v\r\n";
			echo "SUMMARY:$courseid class #$nclasses\r\n";
			echo "UID:$uid\r\n";
			echo "DTSTAMP:$dtstamp\r\n";
			echo "BEGIN:VALARM\r\n";
			echo "TRIGGER:-PT5M\r\n";
			echo "ACTION:AUDIO\r\n";
			echo "END:VALARM\r\n";
			echo "END:VEVENT\r\n";
		}
	}
}
echo "END:VCALENDAR\r\n";
?>
