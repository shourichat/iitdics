<?php
include("fullarray.php");
include("semid.php");
include("examsched.php");
$today = date("Y-m-d-H:i:s");
$dtstamp = date("Ymd\\THis");
$semid = generate_sem_code_text();
$semcode = generate_sem_code();
$midexamcount = 0;
$endexamcount = 0;
header('Content-Type: text/calendar');
header("Content-disposition: attachment; filename=$semcode-acad-cal.ics");
echo "BEGIN:VCALENDAR\r\n";
echo "CALSCALE:GREGORIAN\r\n";
echo "PRODID:-/IIT Delhi/php calendar generator 1.0/EN\r\n";
echo "VERSION:2.0\r\n";
echo "X-WR-CALNAME:IITD-$semcode-AcadCal-$today\r\n";
echo "X-WR-CALDESC:IITD academic $semid on $today\r\n";
echo "BEGIN:VTIMEZONE\r\n";
echo "TZID:Asia/Kolkata\r\n";
echo "BEGIN:STANDARD\r\n";
echo "TZOFFSETFROM:+0530\r\n";
echo "TZOFFSETTO:+0530\r\n";
echo "DTSTART:19451015T000000\r\n";
echo "TZNAME:IST\r\n";
echo "END:STANDARD\r\n";
echo "END:VTIMEZONE\r\n";
$ndays = $dstart->diff($dend)->days;
for($k=0; $k<=$ndays; $k++) {
	$dstr = "P" . $k . "D";
	$currdate = $dstart->add(new DateInterval($dstr));
	$currstr = $currdate->format('Y-m-d');
	$dow = $semcal[$currstr]["status"];
	$currdow = $currdate->format('l');
	if(($dow != $currdow) or ($semcal[$currstr]["note"] != "")) {
		$cstr = $currdate->format('Ymd');
		echo "BEGIN:VEVENT\r\n";
		echo "DTSTART;VALUE=DATE:$cstr\r\n";
		echo "DURATION:P1D\r\n";
		$eventstr = "";
		if($dow != $currdow)
			$eventstr = "$eventstr $dow; ";
		if($semcal[$currstr]["note"] != "")
			$eventstr = $eventstr .  $semcal[$currstr]["note"];
		if(preg_match('/Mid-term exams/', $semcal[$currstr]["note"])) {
			if(preg_match('/Buffer/', $semcal[$currstr]["note"])) {
				$slots = $exams["buf"];
			} else {
				$midexamcount++;
				$slots = $exams[$midexamcount];
			}
			$eventstr = $eventstr . " [";
			for($j=0; $j<count($slots); $j++) {
				$eventstr = $eventstr . $slots[$j] . " ";
			}
			$eventstr = $eventstr . "slots]";
		}
		if(preg_match('/End-semester exams/', $semcal[$currstr]["note"])) {
			if(preg_match('/Buffer/', $semcal[$currstr]["note"])) {
				$slots = $exams["buf"];
			} else {
				$endexamcount++;
				$slots = $exams[$endexamcount];
			}
			$eventstr = $eventstr . " [";
			for($j=0; $j<count($slots); $j++) {
				$eventstr = $eventstr . $slots[$j] . " ";
			}
			$eventstr = $eventstr . "slots]";
		}
		echo "SUMMARY:$eventstr\r\n";
		echo "UID:IITDacad$semcode" . "EN$k\r\n";
		echo "DTSTAMP:$dtstamp\r\n";
		echo "END:VEVENT\r\n";
	}
}
echo "END:VCALENDAR\r\n";
?>
