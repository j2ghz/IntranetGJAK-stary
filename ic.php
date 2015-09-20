<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 6, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Informaèní centrum", $fullname, $kod);
  $pole_tlacitek = array("Obecné<br>informace", "Katalog<br>knih",  "Vyhledávání<br>v katalogu");
  $pole_vyberu = array("vyber=1", "vyber=2", "vyber=3");


  if($vyber=="") $vyber=1;

  /*echo "<TABLE border=\"1\"><TR>";
  echo $bunka_p."<A href=\"ic.php?kod=$kod&vyber=1\">Obecné informace".$bunka_k;
  echo $bunka_p."<A href=\"ic.php?kod=$kod&vyber=2\">Katalog knih".$bunka_k;
  echo $bunka_p."<A href=\"ic.php?kod=$kod&vyber=3\">Vyhledávání <br>v katalogu".$bunka_k;
  echo "</TR></TABLE><p>";
  echo "<hr>";*/

  switch($vyber)
  {
    case 1:
    /**** Obecné informace ****/
      Tlacitka($kod, "ic.php", $pole_vyberu, $pole_tlacitek,1);
   /*   echo "<table bgcolor=\"#dddddd\" width=\"100%\" border=\"0\"><tr><td>";
      echo "<A href=\"ic.php?kod=$kod&vyber=1\"><img src=\"./buttons/ic_obecne_a.gif\" border=\"0\"></a> ";
      echo "<A href=\"ic.php?kod=$kod&vyber=2\"><img src=\"./buttons/ic_katalog.gif\" border=\"0\"></a> ";
      echo "<A href=\"ic.php?kod=$kod&vyber=3\"><img src=\"./buttons/ic_vyhledavani.gif\" border=\"0\"></a> ";
      echo "</td></tr></table>";
      echo "<P>";*/


      $SQL = "select hodnota from pomocna where klic='ic_pravidla'";
      if(DB_select($SQL, $vystup, $pocet))
      {
        if($zaz=mysql_fetch_array($vystup)) $pravidla = $zaz["hodnota"];
      }
      $SQL = "select hodnota from pomocna where klic='ic_ucebny'";
      if(DB_select($SQL, $vystup, $pocet))
      {
        if($zaz=mysql_fetch_array($vystup)) $ucebny = $zaz["hodnota"];
      }

      echo "<table valign=\"middle\">";
      echo "<tr><td width=\"20\"><img src=\"./images/sipka_red.gif\"></td><td><A HREF=$ucebny target=\"_new\">Volné uèebny pro samotatnou práci</A></td></tr>";
      echo "<tr><td width=\"20\"><img src=\"./images/sipka_red.gif\"></td><td><A HREF=$pravidla target=\"_new\">Pravidla provozu IC</A></td></tr>";
      echo "</table><p>";


      ?>
      <TABLE>
        <TR>
          <TD><B>Tel.:</B></TD><TD>57 721 07 59, kl. 18</TD>
        </TR>
        <TR>
          <TD rowspan="2"><B>E-mail:</B></TD><TD>S.Batousek@OAZlin.cz</TD>
        </TR>
        <TR>
          <TD>P.Beran@OAZlin.cz</TD>
        </TR>
        <TR>
          <TD valign="top"><B>Otevírací doba:</B></TD>
        <TD><TABLE cellpadding="10" cellspacing="0" border="1">
        <TR bgcolor="#efefef">
          <TD>&nbsp;</TD><TD align="center">Knihovna</TD><TD align="center">Poèítaèové uèebny</TD>
        </TR>
          <TR>
          <TD bgcolor="#efefef">Po</TD><TD align="center">7:00 - 18:00</TD><TD align="center">7:00 - 20:00</TD>
        </TR>
        <TR>
          <TD bgcolor="#efefef">Út</TD><TD align="center">7:00 - 18:00</TD><TD align="center">7:00 - 20:00</TD>
        </TR>
        <TR>
          <TD bgcolor="#efefef">St</TD><TD align="center">7:00 - 18:00</TD><TD align="center">7:00 - 20:00</TD>
        </TR>
        <TR>
          <TD bgcolor="#efefef">Èt</TD><TD align="center">7:00 - 18:00</TD><TD align="center">7:00 - 20:00</TD>
        </TR>
        <TR>
          <TD bgcolor="#efefef">Pá</TD><TD align="center">7:00 - 15:45</TD><TD align="center">7:00 - 16:00</TD>
        </TR>
         </TABLE></TD>
      </TR>
      </TABLE>

      <p>
      
      <b>Odkazy:</b>
      <ul>

      <!-- <li><p><A HREF="ic_katalog.php?kod=$kod">Katalog knih v IC</A></li> -->


      <li><p><A HREF="http://www.knihovna.utb.cz" target="_new">Knihovna UTB</A></li>
      <li><p><A HREF="http://tinweb.utb.cz" target="_new">Knihovna UTB - katalog knih</A></li>
      <!-- <li><p><A HREF="files/pravidla_ic.doc">Pravidla provozu IC</A><br></li> -->
      </ul>
    <?
    break;

    case 2:
    /**** Katalog knih ****/
      Tlacitka($kod, "ic.php", $pole_vyberu, $pole_tlacitek,2);
      /*echo "<table bgcolor=\"#dddddd\" width=\"100%\" border=\"0\"><tr><td>";
      echo "<A href=\"ic.php?kod=$kod&vyber=1\"><img src=\"./buttons/ic_obecne.gif\" border=\"0\"></a> ";
      echo "<A href=\"ic.php?kod=$kod&vyber=2\"><img src=\"./buttons/ic_katalog_a.gif\" border=\"0\"></a> ";
      echo "<A href=\"ic.php?kod=$kod&vyber=3\"><img src=\"./buttons/ic_vyhledavani.gif\" border=\"0\"></a> ";
      echo "</td></tr></table>";
      echo "<P>";*/

      for($i=0;$i<=3;$i++) $selected[$i] = "";
      if($odeslano_kat)
      {
        switch($razeni_kat)
        {
          case 0:
               $razeni = "k.autor";
               $selected[0] = "selected";
               break;
          case 1:
               $razeni = "k.nazev";
               $selected[1] = "selected";
               break;
          case 2:
               $razeni = "k.zkr_doby";
               $selected[2] = "selected";
               break;
          case 3:
               $razeni = "k.id";
               $selected[3] = "selected";
               break;
        }
      }
      else
      {
        $razeni = "k.autor";
        $selected[0] = "selected";
      }

      echo "<FORM action=\"ic.php?kod=$kod&vyber=2\" method=\"post\">";
      echo "Øadit knihy podle ";
      echo "<select name=\"razeni_kat\">";
      echo "<option value=\"0\" ".$selected[0]."> autora";
      echo "<option value=\"1\" ".$selected[1]."> názvu";
      echo "<option value=\"2\" ".$selected[2]."> výpùjèní doby";
      echo "<option value=\"3\" ".$selected[3]."> identifikaèního èísla";
      echo "</select>";
      echo " <input type=submit value=\"zobraz knihy\" name=\"odeslano_kat\">";
      echo "</FORM>";
      $SQL = "select * from ic_knihy k, ic_doba d where k.zkr_doby=d.zkratka order by $razeni";
      if(DB_select($SQL, $vystup, $pocet))
      {
        echo "<TABLE border=\"1\" bgcolor=\"eeeeee\" cellpadding = \"5\">";
        echo "<TR><td><b>Identifikaèní è.</b></td><TD><b>Autor</b></TD><TD><b>Název</b></TD><TD><b>Výpùjèní doba</b></TD><TD><b>Poèet kusù</b></TD></TR>";
        while($zaz = MySQL_fetch_array($vystup))
        {
          echo "<TR>";
          echo "<TD>".$zaz["id"]."</TD>";
          echo "<TD>".$zaz["autor"]."</TD>";
          echo "<TD>".$zaz["nazev"]."</TD>";
          echo "<TD>".$zaz["popis"]."</TD>";
          echo "<TD>".$zaz["pocet"]."</TD>";
          echo "</TR>";
        }
        echo "</TABLE>";
      }
    break;
    case 3:
    /**** vyhledavani v katalogu *****/
      Tlacitka($kod, "ic.php", $pole_vyberu, $pole_tlacitek,3);
     /* echo "<table bgcolor=\"#dddddd\" width=\"100%\" border=\"0\"><tr><td>";
      echo "<A href=\"ic.php?kod=$kod&vyber=1\"><img src=\"./buttons/ic_obecne.gif\" border=\"0\"></a> ";
      echo "<A href=\"ic.php?kod=$kod&vyber=2\"><img src=\"./buttons/ic_katalog.gif\" border=\"0\"></a> ";
      echo "<A href=\"ic.php?kod=$kod&vyber=3\"><img src=\"./buttons/ic_vyhledavani_a.gif\" border=\"0\"></a> ";
      echo "</td></tr></table>";
      echo "<P>";      */

      if($odeslano_hledani)
      {
        for($i=0;$i<3;$i++)
        {
	  $selected[$i];
          $selected1[$i];
        }
        switch($polozka)
        {
          case "id":
            $selected[0] = "selected";
            $poz_id = "#F7E4E5";
            $poz_naz = "#eeeeee";
            $poz_aut = "#eeeeee";
          break;
          case "nazev":
            $selected[1] = "selected";
            $poz_id = "#eeeeee";
            $poz_naz = "#F7E4E5";
            $poz_aut = "#eeeeee";
          break;
          case "autor":
            $selected[2] = "selected";
            $poz_id = "#eeeeee";
            $poz_naz = "#eeeeee";
            $poz_aut = "#F7E4E5";
          break;
        }
        switch($kde)
        {
          case "lib":
            $selected1[0] = "selected";
            $pom_ret = "%".$retezec."%";
          break;
          case "zac":
            $selected1[1] = "selected";
            $pom_ret = $retezec."%";
          break;
          case "kon":
            $selected1[2] = "selected";
            $pom_ret = "%".$retezec;
          break;
        }
      }
      else
      {
        $selected1[0] = "selected";
        $selected[1] = "selected";
      }

      echo "<form action=\"ic.php?kod=$kod&vyber=3\" method=\"post\">";
      echo "<table border=\"0\">";
      echo "<tr><td>Hledaný øetìzec:</td><td><input type=\"text\" name=\"retezec\" value=\"$retezec\"></td></tr>";
      echo "<tr><td>Hledat:</td>";
      echo "<td><select name=\"kde\">";
      echo   "<option value=\"lib\" ".$selected1[0]."> kdekoli";
      echo   "<option value=\"zac\" ".$selected1[1]."> na zaèátku";
      echo   "<option value=\"kon\" ".$selected1[2]."> na konci";
      echo "</select></td>";
      echo "<tr><td>Hledat v polo¾ce:</td>";
      echo "<td><select name=\"polozka\">";
      echo   "<option value=\"id\" ".$selected[0]."> identifikaèní èíslo";
      echo   "<option value=\"nazev\" ".$selected[1]."> název";
      echo   "<option value=\"autor\" ".$selected[2]."> autor";
      echo "</select></td>";
      echo "<tr><td colspan=\"2\"><input type = \"submit\" value=\"vyhledej knihy\" name=\"odeslano_hledani\"></td></tr>";
      echo "</table>";
      echo "</form>";

      if($odeslano_hledani)
      {
        $SQL = "select * from ic_knihy k, ic_doba d where $polozka like '$pom_ret' and k.zkr_doby=d.zkratka";
        if(DB_select($SQL, $vystup, $pocet))
        {
          if($pocet==0) echo Text_alter("", "Nebyly nalezeny ¾ádné polo¾ky.");
          else
          {
            echo "<TABLE border=\"1\" bgcolor=\"#eeeeee\" cellpadding = \"5\">";
            echo "<TR><td bgcolor=\"$poz_id\"><b>Identifikaèní è.</b></td><TD bgcolor=\"$poz_aut\"><b>Autor</b></TD><TD bgcolor=\"$poz_naz\"><b>Název</b></TD><TD><b>Výpùjèní doba</b></TD><TD><b>Poèet kusù</b></TD></TR>";
            while($zaz=mysql_fetch_array($vystup))
            {
              echo "<TR>";
              echo "<TD bgcolor=\"$poz_id\">".$zaz["id"]."</TD>";
              echo "<TD bgcolor=\"$poz_aut\">".$zaz["autor"]."</TD>";
              echo "<TD bgcolor=\"$poz_naz\">".$zaz["nazev"]."</TD>";
              echo "<TD>".$zaz["popis"]."</TD>";
              echo "<TD>".$zaz["pocet"]."</TD>";
              echo "</TR>";
            }
            echo "</TABLE>";
          }
        }
      }



    break;

  }
}
?>



