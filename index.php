<?php
include("header.php");
include("slotdefs.php");
include("semid.php");
?>
<div class="actionbox">
<h3>Usage:</h3>
This tool generates .ics files. These can be directly imported into
any calendar application: google-calendar, iCalendar, owncloud
calendar etc.
<p>
For Android: <ul><li>Use the download buttons below to download .ics
files <b>on your computer</b>. You will not be able to perform these
steps on your mobile phone. <b>Please use a computer.</b>
<li>Navigate to calendar.google.com 
<li>Click on "Settings" (the gear-wheel button on the top right). 
<li>Click on "Import and export". 
<li>Import the just downloaded calendars to a new
calendar, preferably.
</ul>
<p>
For iOS, simply hit the download buttons below to add the calendars to
your schedule.
<p>
It is strongly recommended to subscribe to the overall academic calendar
in addition to slot-wise calendars.
<p>
Please note that the subscribed calendar will be updated if there are
any changes. You need to subscribe only once.
</div>
<p>
<div class="actionbox">
<h3>Automatic slot-wise calendar:</h3>
<h4><?php $str = generate_sem_code_text(); echo $str; ?></h4>
<p>
<form id="gencal" action="gencal.php" method="GET">
<label for="slot">Slot: </label>
<select id="slot" name="slot">
<?php
$allslots = array_keys($timings);
$nslots = count($allslots);
for($i=0; $i<$nslots; $i++) {
	$thisslot = $allslots[$i];
	echo "<option value=\"$thisslot\">$thisslot</option>\n";
}
?>
</select>
<input type="submit" value="Download calendar for the slot">
</form>
</div>
<p>
<div class="actionbox">
<h3>Calendar for undergraduate first-year students (BTech/BS):</h3>
<h4><?php $str = generate_sem_code_text(); echo $str; ?></h4>
<p>
<?php
$items = [];
$file = fopen('ugfygrps', 'r');

if ($file) {
    while (($line = fgets($file)) !== false) {
        $line = trim($line);
        if (!empty($line)) {
            // Split by space to get item name and units
            $parts = explode(' ', $line, 2);
            if (count($parts) == 2) {
                $itemName = $parts[0];
                $units = explode(',', $parts[1]);
                $items[$itemName] = $units;
            }
        }
    }
    fclose($file);
}
?>
<form id="fystudent" method="GET" action="fystudent.php">
        <label for="BR">Program:</label>
        <select id="BR" name="BR" required onchange="updateUnits()">
            <option value="">Branch:</option>
            <?php foreach (array_keys($items) as $item): ?>
                <option value="<?php echo htmlspecialchars($item); ?>">
                    <?php echo htmlspecialchars($item); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="GR">Group number:</label>
        <select id="GR" name="GR" required disabled>
            <option value="">Select branch first</option>
        </select>
        <br><br>
        <input type="submit" value="Download calendar">
</form>
</div>
<p>
<div class="actionbox">
<h3>Calendar for UG first-year teachers (BTech/BS):</h3>
<h4><?php $str = generate_sem_code_text(); echo $str; ?></h4>
<p>
<form id="fyteacher" method="GET" action="fyteacher.php">
<label for="CR">Course: &nbsp;</label>
<select id="CR" name="CR">
<?php
	$courses = array();
	$lines = explode(PHP_EOL, file_get_contents("ugfycrss"));
	$nl = count($lines);
	for($i=0; $i<$nl; $i++) {
		$line = substr($lines[$i], 4);
		$ci = explode(',', $line);
		for($j=0; $j<count($ci); $j++) {
			if(!in_array($ci[$j], $courses))
				array_push($courses, $ci[$j]);
		}
	}
	sort($courses);
	for($i=1; $i<count($courses); $i++) {
		$cid = $courses[$i];
		echo "<option value=\"$cid\">$cid</option>\n";
	}
?>
</select>
<input type="submit" value="Download full course calendar">
</form>
</div>
<p>
<div class="actionbox">
<h3>Overall academic calendar:</h3>
You can subscribe to the calendar, or download the calendar.
Subscribing will allow you to keep abreast of changes made to the
calendar without downloading again.
<p>
URL is: 
<a href="https://web.iitd.ac.in/~adcur/ics/acadcal.ics">
https://web.iitd.ac.in/~adcur/ics/acadcal.ics</a>
<p>
To subscribe on google calendar (Android), <b>on a computer</b> navigate to
calendar.google.com; on the left sidebar bottom, click on the "+" next
to Other calendars; select From URL; paste the above URL; and Add
calendar.
<p>
To subscribe on iOS, <b>on your device (iphone/ipad/iCal)</b> click on
Calendars; click on Add Calendar; Add Subscription Calendar; paste the
above URL; and Subscribe.
<p>
Alternately, you can download the academic calendar directly.
<form id="genacadcal" action="genacadcal.php" method="post">
<input type="submit" value="Download overall academic calendar">
</form>
</div>
<?php
	include("tail.php");
?>
