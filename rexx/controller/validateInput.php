<?php
/**
* Controller für die Dateneingabe
* @benötigt cDATA.inc.php
*/
include('../libs/cDATA.inc.php');

$sSafariFrom = $_POST['start'];
$sSafariTo   = $_POST['end'];
// Browserabfrage, da Safari das Date Input field anders handelt
$sUserAgent = $_SERVER['HTTP_USER_AGENT'];
if(strpos($sUserAgent, 'Safari'))
{
  // Überprüfen, ob die Eingabe valide ist.
  if(preg_match('/^[0-9, . ]{10}+$/i', $sSafariFrom) && preg_match('/^[0-9, . ]{10}+$/i', $sSafariTo))
  {
    // MySQL-Query-fähigen String bauen

    $sSafariFrom = explode(".", $sSafariFrom);
    $sFrom = $sSafariFrom[2].'-'.$sSafariFrom[1].'-'.$sSafariFrom[0];

    $sSafariTo = explode(".", $sSafariTo);
    $sTo = $sSafariTo[2].'-'.$sSafariTo[1].'-'.$sSafariTo[0];

    DATA::buildDataArrayCSV($sFrom, $sTo);
    DATA::gererateCsvFile($sFrom, $sTo);
  }
  else
  {
    echo "<h2>Ihre Eingabe war nicht korrekt, bitte geben Sie das Datum im folgenden Format ein: 'tt.mm.yyyy'!</h2>";
    die();
  }
}
else
{
  $sFrom = $_POST['start'];
  $sTo   = $_POST['end'];
  DATA::gererateCsvFile($sFrom, $sTo);
  DATA::buildDataArrayCSV($sFrom, $sTo);
}
?>
