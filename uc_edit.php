<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 4, $fullname, $login, $chyba))
{
  NoCACHE();
  $pole_tlacitek = array("Ulo�en�<br>souboru", "Odstran�n�<br>soubor�");
  $pole_vyberu = array("vyber=1", "vyber=2");
  if(!($vyber)) $vyber=1;
  Hlavicka("Aktualizace soubor� pro u�itele", $fullname, $kod, "uc_edit.php", $pole_vyberu, $pole_tlacitek, $vyber);
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
          if(!(unlink(c_files."files_ucitelum/".$zaznam["nazev"])))
            echo "soubor ".$zaznam["nazev"]." se nepoda�ilo odstranit";
        }
      }
    $SQL = "delete from soubory where $podminka";
    DB_exec($SQL);
    $vyber=2;
  }
  switch($vyber)
  {
    case 1:
      //Tlacitka($kod, "uc_edit.php", $pole_vyberu, $pole_tlacitek,1);
/*******************************************************/
      echo "<center>".Hlaska($chyba, "Soubor se nepoda�ilo ulo�it", "Soubor byl �sp�n� ulo�en")."</center>";
      if($chyba<>"ok")
      {
        echo "<form action=\"./uc_send.php?kod=$kod\" method=post enctype=\"multipart/form-data\">";
        echo "<p><table border=0><tr><td><b><".c_font.">Soubor k odesl�n�:</td></tr>";
        echo "<tr><td><input type=\"file\" name=\"soubor\" value=\"$soubor\"></td></tr></table>";

        echo "<p><table border=0><tr><td><b><".c_font.">Nov� n�zev souboru:</b>";
        echo "<br><font color=gray><small>- nov� n�zev souboru nesm� b�t pr�zdn� a m��e obsahovat pouze tyto znaky:<br>a-z, A-Z, 0-9, ., _, -,
             <br>tj. nesm�te pou��vat diakritiku a mezery (nap�. nam�sto \"vzorov� p��klady\" pi�te \"vzorove_priklady\")</small></td></tr>";
        echo "<tr><td><input type=\"textbox\" name=\"nazev\" value=\"$nazev\"></td></tr></table>";

        echo "<p><table border=0><tr><td><b><".c_font.">Stru�n� popis:</b>";
        echo "<br><font color=gray><small>- tento text se objev� m�sto n�zvu souboru (m��e b�t �esky)</small></font></td></tr>";
        echo "<tr><td><textarea name=\"popis\" value=\"$popis\" rows=3 cols=20></textarea></td></tr></table>";

        $SQL = "select * from typ where skola='u' order by id";

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
      //Tlacitka($kod, "uc_edit.php", $pole_vyberu, $pole_tlacitek,2);
/*******************************************************/
      if(!($typ)) $typ="sdeleni";
      $SQL = "select * from typ where skola='u' order by id";
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

      $SQL = "select * from soubory where typ='$typ' ";
      if(DB_select($SQL, $vystup, $pocet))
      {
        if($pocet>0)
        {
          echo "<P>Po�et z�znam�: $pocet";
          echo "<form method=\"post\">";
          echo "<p><table border=0 cellspacing=2 cellpadding=5>";
          echo "<tr bgcolor=\"#dddddd\"><td><b><center>maz�n�</center></b></td><td><center><b>soubor</b></center></td><td><center><b>datum</b></center></td></tr>";
          $i=0;
          while($zaz=MySQL_fetch_array($vystup))
          {
            echo "<tr valign=\"top\">";
            echo "<td><input type=\"checkbox\" name=\"vymaz[]\" value=\"".$zaz["id"]."\"></td>";
            echo "<td><a href=\"sendfile.php?kod=$kod&p_prava=5&p_nazev=".$zaz["nazev"]."&p_adresar=files_ucitelum/".$zaz["nazev"]."\">".$zaz["popis"]."</a></td>";
            echo "<td><small>".Datum($zaz["datum"])."</small></td>";
            echo "</tr>";
            $i++;
          }
          echo "<tr><td colspan=\"5\"><input type=\"submit\" name=\"odeslano_vymaz\" value=\"odstranit vybran� soubory\"></td></tr>";
          echo "</table>";
          echo "</form>";
        }
      }
      break;
  }
}
Konec();


define(c_font, "font size=4");

?>
