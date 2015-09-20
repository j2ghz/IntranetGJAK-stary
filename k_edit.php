<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 4, $fullname, $login, $chyba))
{
  NoCACHE();
  $pole_tlacitek = array("Ulo¾ení<br>souboru", "Odstranìní<br>souborù");
  $pole_vyberu = array("vyber=1", "vyber=2");
  if(!($vyber)) $vyber=1;
  Hlavicka("Aktualizace souborù kraje", $fullname, $kod, "k_edit.php", $pole_vyberu, $pole_tlacitek, $vyber);
  echo "<P>";

  if($odeslano_vymaz)
  {
    $podminka = " id = '".$vymaz[0]."' ";
    for($i=1;$i<count($vymaz);$i++)
    {
      $podminka .= " or id = '".$vymaz[$i]."' ";
    }
    $SQL = "select nazev from soubory where $podminka ";
      if(DB_select($SQL, $vystup, $pocet))
      {
        while($zaznam = MySQL_fetch_array($vystup))
        {
          if(!(unlink(c_files."files_kraj/".$zaznam["nazev"])))
            echo "soubor ".$zaznam["nazev"]." se nepodaøilo odstranit";
        }
      }
    $SQL = "delete from soubory where $podminka";
    DB_exec($SQL);
    $vyber=2;
  }
  switch($vyber)
  {
    case 1:
      //Tlacitka($kod, "k_edit.php", $pole_vyberu, $pole_tlacitek,1);
/*******************************************************/
      echo "<center>".Hlaska($chyba, "Soubor se nepodaøilo ulo¾it", "Soubor byl úspì¹nì ulo¾en")."</center>";
      if($chyba<>"ok")
      {
        echo "<form action=\"./k_send.php?kod=$kod\" method=post enctype=\"multipart/form-data\">";
        echo "<p><table border=0><tr><td><b><".c_font.">Soubor k odeslání:</td></tr>";
        echo "<tr><td><input type=\"file\" name=\"soubor\" value=\"$soubor\"></td></tr></table>";

        echo "<p><table border=0><tr><td><b><".c_font.">Nový název souboru:</b>";
        echo "<br><font color=gray><small>- nový název souboru nesmí být prázdný a mù¾e obsahovat pouze tyto znaky:<br>a-z, A-Z, 0-9, ., _, -,
             <br>tj. nesmíte pou¾ívat diakritiku a mezery (napø. namísto \"vzorové pøíklady\" pi¹te \"vzorove_priklady\")</small></td></tr>";
        echo "<tr><td><input type=\"textbox\" name=\"nazev\" value=\"$nazev\"></td></tr></table>";

        echo "<p><table border=0><tr><td><b><".c_font.">Struèný popis:</b>";
        echo "<br><font color=gray><small>- tento text se objeví místo názvu souboru (mù¾e být èesky)</small></font></td></tr>";
        echo "<tr><td><textarea name=\"popis\" value=\"$popis\" rows=3 cols=20></textarea></td></tr></table>";

        $SQL = "select * from typ where skola='k' order by id";

        if(DB_select($SQL, $vyst, $pocet))
        {
          echo "<p><table border=0><tr><td><b><".c_font.">Typ souboru:</td></tr>";
          echo "<tr><td><select name=\"typ\" value=\"$typ\">";
          while($zaz=mysql_fetch_array($vyst))
          {
            echo "<option value=\"".$zaz["nazev"]."\">".$zaz["popis"];
          }
          echo "</select></td></tr></table>";
        }
        echo "<p><table border=0><tr><td><input type = \"submit\" value=\"odeslat soubor\" name=\"odeslano\"></td></tr>";
        echo "</table></form>";
      }
      break;
    case 2:
      //Tlacitka($kod, "k_edit.php", $pole_vyberu, $pole_tlacitek,2);
/*******************************************************/
      if(!($typ)) $typ="k_zpravodaj";
      $SQL = "select * from typ where skola='k' order by id";
      if(DB_select($SQL, $vystup, $pocet))
      {
        echo "<form method=\"post\">";
        echo "Zobrazit soubory typu ";
        echo "<select name=\"typ\"> ";
        while($zaz=mysql_fetch_array($vystup))
        {
          $selected="";
          if($zaz["nazev"]==$typ) $selected = "selected";
          echo "<option value=\"".$zaz["nazev"]."\" $selected>".$zaz["popis"];
        }
        echo "</select>";
        echo " <input type=submit name=odeslano value=\"zobraz soubory\">";
        echo "</form>";
      }

      $SQL = "select * from soubory where typ='$typ' order by datum desc";
      if(DB_select($SQL, $vystup, $pocet))
      {
        if($pocet>0)
        {
          if($typ=="k_zpravodaj") echo "(Mazat lze pouze zpravodaje bez pøíloh.)";
          echo "<P>Poèet záznamù: $pocet";
          echo "<form method=\"post\">";
          echo "<p><table border=0 cellspacing=2 cellpadding=5>";
          echo "<tr bgcolor=\"#dddddd\"><td><b><center>mazání</center></b></td><td><center><b>soubor</b></center></td><td><center><b>datum</b></center></td></tr>";
          $i=0;
          while($zaz=MySQL_fetch_array($vystup))
          {
            $spril=0;
            if($typ=="k_zpravodaj")
            {
              $SQL2 = "select count(*) sprilohou from soubory where id_zprav='".$zaz["id"]."'";
              if(DB_select($SQL2, $vyst, $poc))
	        $zaznam=mysql_fetch_array($vyst);
                $spril=$zaznam["sprilohou"];
            }
            echo "<tr valign=\"top\">";
            echo "<td>";
	    if($spril<>0) echo Text_alter("","nelze");
            else echo "<input type=\"checkbox\" name=\"vymaz[]\" value=\"".$zaz["id"]."\">";
	    echo "</td>";
            echo "<td><a href=\"sendfile.php?kod=$kod&p_prava=5&p_nazev=".$zaz["nazev"]."&p_adresar=files_kraj/".$zaz["nazev"]."\">".$zaz["popis"]."</a></td>";
            echo "<td><small>".Datum($zaz["datum"])."</small></td>";
            echo "</tr>";
            $i++;
          }
          echo "<tr><td colspan=\"5\"><input type=\"submit\" name=\"odeslano_vymaz\" value=\"odstranit vybrané soubory\"></td></tr>";
          echo "</table>";
          echo "</form>";
        }
        else echo Text_alter("","®ádný soubor tohoto typu nebyl na server ulo¾en.");
      }
      break;
  }
}
Konec();


define(c_font, "font size=4");

?>
