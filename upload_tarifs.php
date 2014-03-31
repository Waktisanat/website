<!doctype html>
<HTML>
<HEAD>
<META HTTP-EQUIV="Pragma"  CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<TITLE>UPLOAD</TITLE>
</HEAD>
<BODY>
<?PHP
  include_once( 'connect_base.php.ini' );

  $dte = date("d/m/Y H:i:s");
  $req = $bdd->prepare("UPDATE INFOS set Value='".$dte."' WHERE INFOS.Key='Last Price Import';");
  $req->execute();
                
if (is_uploaded_file($_FILES['fname']['tmp_name']))
{
  $fname = $_FILES['fname']['tmp_name'];
  
  $cleaned = False;
  $fd = fopen($fname, "r");
  $cnt = 0;        
  while (!feof($fd)) {
      $line = fgets($fd);
      $data = explode(",", $line);
                         
      if (count($data) == 4) {
          if (!$cleaned) {
            $cleaned = true;
            $req = $bdd->prepare('DELETE FROM TARIFS WHERE date='.$data[3].';');
            $req->execute();
          }
          $sql = 'insert into TARIFS (item, min, max, date) values ('
                .'(select id from ITEMS where name = \''.$data[0].'\' LIMIT 1),'
                .$data[1].','.$data[2].','.$data[3].');';    
          $req = $bdd->prepare($sql);
          $req->execute(); /* */    
          $cnt++;
      }       
  }         
  fclose($fd);
  print ("Inserted ".$cnt." records.") ;
}   
 
?>
</BODY>
</HTML>