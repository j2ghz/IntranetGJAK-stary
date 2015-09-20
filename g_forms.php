<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 6, $fullname, $login, $chyba))
{
  NoCACHE();
  $SQL = "select popis from typ where nazev='$typ'";
  if(DB_select($SQL, $vystup, $poc))
  {
    if($zaznam=mysql_fetch_array($vystup)) Hlavicka("Pro studenty - ".PrvniPismeno($zaznam["popis"]), $fullname, $kod);
    $SQL = "select * from soubory where typ='$typ' order by datum desc";
    if(DB_select($SQL, $vystup, $pocet))
    {
      if($pocet>0)
      {
        echo "<table border=0 cellspacing=2 cellpadding=5>";
        echo "<tr bgcolor=\"#dddddd\"><td><b><center>soubor</center></b></td><td><b><center>datum ulo¾ení</center></b></td></tr>";
        while($zaz=mysql_fetch_array($vystup))
        {
          echo "<tr class=\"tabulka\"><td><a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=".$zaz["nazev"]."&p_adresar=files_gym/".$zaz["nazev"]."\">".$zaz["popis"]."</a></td>";
          echo "<td><span class=\"seznam_text\">".Datum($zaz["datum"])."</span></td></tr>";
        }
        echo "</table>";
      }
      else echo Text_alter("", "®ádný soubor tohoto typu nebyl na server ulo¾en.");
    }

    Konec();
  }
}

function PrvniPismeno($text)
{
  return StrToUpper(SubStr($text,0,1)).SubStr($text, 1, StrLen($text)-1);
}
?>
