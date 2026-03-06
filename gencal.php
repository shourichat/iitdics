<?php
include("fullarray.php");
include("slotdefs.php");
include("semid.php");
include("examsched.php");
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$slot = $_GET['slot'];
} else {
	exit("Error: No information\n");
}
$today = date("Y-m-d-H:i:s");
$dtstamp = date("Ymd\\THis");
$days_of_interest = array_column($timings[$slot], "dow");
$ndays = $dstart->diff($dend)->days;
$nclasses=0;
$semid = generate_sem_code_text();
$semcode = generate_sem_code();
$endexamcount = 0; $midexamcount=0;
header('Content-Type: text/calendar');
header("Content-disposition: attachment; filename=$semcode-$slot.ics");
echo "BEGIN:VCALENDAR\r\n";
echo "CALSCALE:GREGORIAN\r\n";
echo "PRODID:-/IIT Delhi/php calendar generator 1.0/EN\r\n";
echo "VERSION:2.0\r\n";
echo "X-WR-CALNAME:IITD-$semcode-$slot-$today\r\n";
echo "X-WR-CALDESC:IITD cal $semid $slot-slot on $today\r\n";
echo "BEGIN:VTIMEZONE\r\n";
echo "TZID:Asia/Kolkata\r\n";
echo "BEGIN:STANDARD\r\n";
echo "TZOFFSETFROM:+0530\r\n";
echo "TZOFFSETTO:+0530\r\n";
echo "DTSTART:19451015T000000\r\n";
echo "TZNAME:IST\r\n";
echo "END:STANDARD\r\n";
echo "END:VTIMEZONE\r\n";
for($k=0; $k<=$ndays; $k++) {
	$dstr = "P" . $k . "D";
	$currdate = $dstart->add(new DateInterval($dstr));
	$currstr = $currdate->format('Y-m-d');
	$dow = $semcal[$currstr]["status"];
	if(in_array($dow, $days_of_interest))
	{
		$nclasses++;
		$len = count($timings[$slot]);
		for($i=1; $i<=$len; $i++) {
			if($timings[$slot][$i]["dow"] == $dow) {
				$starttime = $timings[$slot][$i]["start"];
				$stoptime = $timings[$slot][$i]["stop"];
			}
		}
		$cstr = $currdate->format('Ymd');
		$start = $cstr . "T" . $starttime . "00"; 
		$stop  = $cstr . "T" . $stoptime  . "00"; 
		echo "BEGIN:VEVENT\r\n";
		echo "DTSTART;TZID=Asia/Kolkata:$start\r\n";
		echo "DTEND;TZID=Asia/Kolkata:$stop\r\n";
		echo "LOCATION:LHC, IIT Delhi\r\n";
		echo "SUMMARY:Class #$nclasses\r\n";
		echo "UID:uidIITDauto$semcode$slot$nclasses\r\n";
		echo "DTSTAMP:$dtstamp\r\n";
		echo "BEGIN:VALARM\r\n";
		echo "TRIGGER:-PT5M\r\n";
		echo "ACTION:AUDIO\r\n";
		echo "END:VALARM\r\n";
		echo "END:VEVENT\r\n";
	} else if(preg_match('/Mid-term exams/', $semcal[$currstr]["note"])) {
		$cstr = $currdate->format('Ymd');
		if(preg_match('/Buffer/', $semcal[$currstr]["note"])) {
			$slots = $exams["buf"];
		} else {
			$midexamcount++;
			$slots = $exams[$midexamcount];
		}
		if(in_array($slot, $slots)) {
		echo "BEGIN:VEVENT\r\n";
		echo "DTSTART;VALUE=DATE:$cstr\r\n";
		echo "DURATION:P1D\r\n";
		echo "SUMMARY:Likely mid-term exam for $slot slot\r\n";
		echo "UID:uidIITDmidsem$semcode$slot\r\n";
		echo "DTSTAMP:$dtstamp\r\n";
		echo "END:VEVENT\r\n";
		}
	} else if(preg_match('/End-semester exams/', $semcal[$currstr]["note"])) {
		$cstr = $currdate->format('Ymd');
		if(preg_match('/Buffer/', $semcal[$currstr]["note"])) {
			$slots = $exams["buf"];
		} else {
			$endexamcount++;
			$slots = $exams[$endexamcount];
		}
		if(in_array($slot, $slots)) {
		echo "BEGIN:VEVENT\r\n";
		echo "DTSTART;VALUE=DATE:$cstr\r\n";
		echo "DURATION:P1D\r\n";
		echo "SUMMARY:Likely end-semester exam for $slot slot\r\n";
		echo "UID:uidIITDendsem$semcode$slot\r\n";
		echo "DTSTAMP:$dtstamp\r\n";
		echo "END:VEVENT\r\n";
		}
	}
}
echo "END:VCALENDAR\r\n";
?>
