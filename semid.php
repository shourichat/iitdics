<?php
function generate_sem_code()
{
	$thismonth = date("m"); $thisyear = date("y");
	if($thismonth >= 6) {
		$sem = $thisyear . "01";
	}
	if($thismonth <= 5) {
		$sem = ($thisyear-1) . "02";
	}
	if($thismonth == 12) {
		$sem = $thisyear . "02";
	}
	return $sem;
}

function generate_sem_code_text()
{
	$thismonth = date("m"); $thisyear = date("Y");
	if($thismonth >= 6) {
		$nextyear = $thisyear+1;
		$semtext = "Sem-1 $thisyear" . "-" . "$nextyear";
	} 
	if($thismonth <= 5) {
		$nextyear = $thisyear;
		$thisyear = $thisyear-1;
		$semtext = "Sem-2 $thisyear" . "-" . "$nextyear";
	}
	if($thismonth == 12) {
		$nextyear = $thisyear+1;
		$semtext = "Sem-2 $thisyear" . "-" . "$nextyear";
	}
	return $semtext;
}

?>
