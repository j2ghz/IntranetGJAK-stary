<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 6, $fullname, $login, $chyba))
{
  NoCACHE();
  $SQL = "select popis from typ where nazev='$typ'";
  if(DB_select($SQL, $vystup, $poc))
  {
    if($zaznam=mysql_fetch_array($vystup)) Hlavicka("Kraj, regionální ¹kolství - ".PrvniPismeno($zaznam["popis"]), $fullname, $kod);
  }
  if($pril<>"")
  {
   /* echo "<a href=\"./k_forms.php?kod=$kod&typ=k_zpravodaj\"><img src=\"./images/sipka.gif\" border=none></a>";*/
    $SQL = "select * from soubory where id_zprav = '$pril' and typ='z_prilohy' order by datum desc";
    if(DB_select($SQL, $vystup, $pocet))
    {
      echo Podnadpis("<a href=\"./k_forms.php?kod=$kod&typ=k_zpravodaj\"><img src=\"./images/sipka.gif\" border=none></a> Pøílohy ke zpravodaji $zprav");
      if($pocet>0)
      {
        echo "<table border=0 cellspacing=2 cellpadding=5>";
        echo "<tr bgcolor=\"#dddddd\"><td><b><center>soubor</center></b></td><td><b><center>datum ulo¾ení</center></b></td></tr>";
        while($zaz=mysql_fetch_array($vystup))
        {
          echo "<tr class=\"tabulka\"><td><a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=5&p_nazev=".$zaz["nazev"]."&p_adresar=files_kraj/".$zaz["nazev"]."\">".$zaz["popis"]."</a></td>";
          echo "<td><span class=\"seznam_text\">".Datum($zaz["datum"])."</span></td></tr>";
        }
        echo "</table>";
      }
    }
  }
  else
  {

    $SQL = "select * from soubory where typ='$typ' order by datum desc";
    if($typ=="k_zpravodaj") $bunka = "<td><b><center>pøílohy</center></b></td>";
    else $bunka="";
    if(DB_select($SQL, $vystup, $pocet))
    {
      if($pocet>0)
      {
        echo "<table border=0 cellspacing=2 cellpadding=5>";
        echo "<tr bgcolor=\"#dddddd\"><td><b><center>soubor</center></b></td><td><b><center>datum ulo¾ení</center></b></td>$bunka</tr>";
        while($zaz=mysql_fetch_array($vystup))
        {
          echo "<tr class=\"tabulka\"><td><a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=5&p_nazev=".$zaz["nazev"]."&p_adresar=files_kraj/".$zaz["nazev"]."\">".$zaz["popis"]."</a></td>";
          echo "<td><span class=\"seznam_text\">".Datum($zaz["datum"])."</span></td>";
          if($zaz["typ"]==k_zpravodaj)
          {
            $SQL2 = "select * from soubory where id_zprav='".$zaz["id"]."'";
            if(DB_select($SQL2, $vystup2, $pocet2)) if($pocet2<>0)
              echo "<td><a class=\"seznam\" href=\"./k_forms.php?kod=$kod&typ=$typ&zprav=".$zaz["popis"]."&pril=".$zaz["id"]."\">zobrazit pøílohy</a></td>";
            else echo "<td></td>";
            echo "</tr>";
          }
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
