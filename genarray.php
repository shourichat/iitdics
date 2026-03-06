<?php
# N = number of days between start of sem and beginning of vacation
# for k=1:N
# build an array
# date[k]["date"]
# date[k]["status"] = M/Tu/Wed/Th/Fr/Sat/Sun or Holiday
# date[k]["note"] = If any special note for the day; name of holiday;
# date[k]["count"] = p'th monday. There should be 14 class mondays.
# or special status of working day etc.
# print all of this out as php -> so that it can be read in straight.
$dstartstr = "2026-01-02";
$dendstr   = "2025-05-12";
$dstart = new DateTimeImmutable($dstartstr);
$dend   = new DateTimeImmutable($dendstr);
$ndays = $dstart->diff($dend)->days;
echo "<?php\n";
echo "\$dstart = new DateTimeImmutable(\"$dstartstr\");\n";
echo "\$dend   = new DateTimeImmutable(\"$dendstr\");\n";
for($k=0; $k<=$ndays; $k++) {
	$dstr = "P" . $k . "D";
	$currdate = $dstart->add(new DateInterval($dstr));
	$currstr = $currdate->format('Y-m-d');
	$dow = $currdate->format('l');
	echo "\$semcal[\"$currstr\"][\"status\"] = \"$dow\";\n";
	echo "\$semcal[\"$currstr\"][\"note\"] = \"\";\n";
}
echo "?>";
?>
