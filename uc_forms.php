<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 5, $fullname, $login, $chyba))
{
  NoCACHE();


  if($typ<>"vse")
  {
    $SQL = "select popis from typ where nazev='$typ'";
    if(DB_select($SQL, $vystup, $poc))
    {
      if($zaznam=mysql_fetch_array($vystup)) Hlavicka("Pro uèitele - ".PrvniPismeno($zaznam["popis"]), $fullname, $kod);
      $SQL = "select * from soubory where typ='$typ' order by datum desc";
      if(DB_select($SQL, $vystup, $pocet))
      {
        if($pocet>0)
        {
          echo "<table border=0 cellspacing=2 cellpadding=5>";
          echo "<tr bgcolor=\"#dddddd\"><td><b><center>soubor</center></b></td><td><b><center>datum ulo¾ení</center></b></td></tr>";
          while($zaz=mysql_fetch_array($vystup))
          {
            echo "<tr class=\"tabulka\"><td><a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=5&p_nazev=".$zaz["nazev"]."&p_adresar=files_ucitelum/".$zaz["nazev"]."\">".$zaz["popis"]."</a></td>";
            echo "<td><span class=\"seznam_text\">".Datum($zaz["datum"])."</span></td></tr>";
          }
          echo "</table>";
        }
        else echo Text_alter("", "®ádný soubor tohoto typu nebyl na server ulo¾en.");
      }
    }
  }
  else
  {
    Hlavicka("V¹echny soubory pro uèitele", $fullname, $kod);
    $SQL = "select s.*, t.popis popis_typ, t.id id_typ from soubory s, typ t where t.nazev = s.typ and t.skola='u' order by t.id, s.datum desc";
    if(DB_select($SQL, $vystup, $pocet))
    {
      $typ_min = "";
      if($pocet>0)
      {
        echo "<table border=0 cellspacing=2 cellpadding=5>";
        echo "<tr bgcolor=\"#dddddd\"><td><b><center>soubor</center></b></td><td><b><center>datum ulo¾ení</center></b></td></tr>";
        while($zaz=mysql_fetch_array($vystup))
        {
          if($zaz["typ"]<>$typ_min)
          {
            echo "<tr><td colspan=2><p>&nbsp;</p></td></tr>";
	    echo "<tr bgcolor=\"#eeeeee\"><td colspan=2>".$zaz["popis_typ"]."</td></tr>";

          }
          echo "<tr class=\"tabulka\"><td><a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=5&p_nazev=".$zaz["nazev"]."&p_adresar=files_ucitelum/".$zaz["nazev"]."\">".$zaz["popis"]."</a></td>";
          echo "<td><span class=\"seznam_text\">".Datum($zaz["datum"])."</span></td></tr>";
          $typ_min = $zaz["typ"];
        }
        echo "</table>";
      }
      else echo Text_alter("", "®ádný soubor tohoto typu nebyl na server ulo¾en.");
    }
  }

  Konec();

}

function PrvniPismeno($text)
{
  return StrToUpper(SubStr($text,0,1)).SubStr($text, 1, StrLen($text)-1);
}
?>
