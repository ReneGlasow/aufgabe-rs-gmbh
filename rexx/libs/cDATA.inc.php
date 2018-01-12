<?php
/**
* cDATA.inc.php
* Klasse und Methoden zum Import der Daten aus
* 'customers','sales1' und 'sales2'
* @author René Glasow
*/
class DATA
{
  private static $sUser = 'root';
  private static $sPass = '';
  private static $sDb = 'mysql:host=localhost;dbname=rexx';

  /**
  * Getter für den Datensatz aus den obigen Tabellen
  * @access public
  * @static
  * @return {Array} $aDataset
  */
  public static function getDataset($sFrom, $sTo)
  {
    try
    {
      $oPDO = new PDO(self::$sDb, self::$sUser, self::$sPass);
    }
    catch (\Exception $e)
    {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
    }

    $aDataset = array();
    $sSQL = "
            SELECT
              a.customer_id
            FROM
              customer a
            LEFT JOIN
              sales1 b
            ON
              a.customer_id = b.customer_id
            LEFT JOIN
              sales1 c
            ON
              a.customer_id = c.customer_id
            WHERE
              (b.sale_date
            BETWEEN
              '{$sFrom}'
            AND
              '{$sTo}')
            OR
              (c.sale_date
            BETWEEN
              '{$sFrom}'
            AND
              '{$sTo}')
            ORDER BY
              a.customer_id
            ";
    $sStmt = $oPDO->prepare($sSQL);
    $sStmt->execute();
    // Alle IDs des entsprechenden Zeitraums fetchen
    $aCustomerID = $sStmt->fetchAll(PDO::FETCH_COLUMN);
    // Doppelte IDs entfernen
    $aUniqueID = array_unique($aCustomerID);

    $oPDO = null;
    return $aUniqueID;
  }

  public static function buildDataArrayCSV($sFrom, $sTo)
  {

    $aUniqueID = self::getDataset($sFrom, $sTo);
    $aCsvData = array();

    try
    {
      $oPDO = new PDO(self::$sDb, self::$sUser, self::$sPass);
    }
    catch (\Exception $e)
    {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
    }

    foreach($aUniqueID as $nValue)
    {
      $sSQL = "
              SELECT
                a.firstname,
                a.lastname,
                a.gender,
              (SELECT SUM(b.sale_amount)
              FROM
                sales1 b
              WHERE
                sale_date
              BETWEEN '{$sFrom}' AND '{$sTo}'
              AND
                a.customer_id = b.customer_id) as 'Amount1',
              (SELECT COUNT(b.sale_id)
              FROM
                sales1 b
              WHERE
                sale_date
              BETWEEN '{$sFrom}' AND '{$sTo}'
              AND
                a.customer_id = b.customer_id) as 'ID1',
              (SELECT SUM(c.sale_amount)
              FROM
                sales2 c
              WHERE
                sale_date
              BETWEEN '{$sFrom}' AND '{$sTo}'
              AND
                a.customer_id = c.customer_id) as 'Amount2',
              (SELECT COUNT(c.sale_id)
              FROM
                sales2 c
              WHERE
                sale_date
              BETWEEN '{$sFrom}' AND '{$sTo}'
              AND
                a.customer_id = c.customer_id) as 'ID2'
              FROM
                customer a
              WHERE
                a.customer_id = :customer_id
              ";

        $sStmt = $oPDO->prepare($sSQL);
        $sStmt->bindParam(':customer_id', $nValue);
        $sStmt->execute();
        //echo $sSQL;
        while($aRow = $sStmt->fetch(PDO::FETCH_ASSOC))
        {
          $aCsvData[] = array
          (
            'firstname'       => $aRow['firstname'],
            'lastname'        => $aRow['lastname'],
            'gender'          => $aRow['gender'],
            'amount'          => number_format($aRow['Amount1'] + $aRow['Amount2'], 2),
            'sales'           => $aRow['ID1'] + $aRow['ID2']
          );
        }
      }
      return $aCsvData;
    }

    public static function gererateCsvFile($sFrom, $sTo)
    {
      $aCsvData = self::buildDataArrayCSV($sFrom, $sTo);
      $aFinalData = array();
      $nArraySize = count($aCsvData);
      for($i = 0; $i < $nArraySize; $i++)
      {
        if($aCsvData[$i]['gender'] === 'male')
        {
          $aCsvData[$i]['title'] = "Herr";
        }
        else
        {
          $aCsvData[$i]['title'] = "Frau";
        }
        $aFinalData[$i]['name']      = $aCsvData[$i]['title']." ".$aCsvData[$i]['firstname']." ".$aCsvData[$i]['lastname'];
        $aFinalData[$i]['amount']    = $aCsvData[$i]['amount'];
        $aFinalData[$i]['sales']     = $aCsvData[$i]['sales'];
      }
        //Pfad, Directory und Datei mit unique ID erstellen
        $PATH = $_SERVER['DOCUMENT_ROOT'];
        if(!file_exists("../csv/"))
        {
          mkdir("$PATH/rexx/csv", 0777);
        }
        $nId = uniqid();
        $sCsvOutput = fopen("../csv/csvFile".$nId.".csv", 'w');
        // jedes Array in die CSV schreiben. Delimiter ";"
        foreach($aFinalData as $sValue)
        {
          fputcsv($sCsvOutput, $sValue, ";");
        }
        fclose($sCsvOutput);
        // Link zum (erstellten) Ordner mit den CSV Dateien bereitstellen
        if($sCsvOutput)
        {
          echo "CSV Datei erstellt: <a href='../csv'>Link</a>";
        }
        $oPDO = null;
    }
  }
