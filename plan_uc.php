<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 5, $fullname, $login, $chyba))
{
  NoCACHE();
  
  $pole_tlacitek = array("Oznámení<br>nepøítomnosti", "Rezervace<br>uèeben" /*,"Pøesuny<br>uèeben"*/, "Odstranìní<br>zprávy");
  $pole_vyberu = array("vyber=1", "vyber=2", "vyber=3");
  if(!($vyber)) $vyber=1;
Hlavicka("Zprávy pro vedení", $fullname, $kod, "plan_uc.php", $pole_vyberu, $pole_tlacitek, $vyber);
  $SQL = "select prava from prihl_uziv where kod='$kod'";
  if(DB_select($SQL, $vyst, $pocet))
  {
    if($zaznam=mysql_fetch_array($vyst)) $prava = $zaznam["prava"];
  }

  $SQL = "select zkratka from ucitele where login='$login'";
  if(DB_select($SQL, $vyst, $pocet))
  {
    if($zaznam=mysql_fetch_array($vyst)) $zkratka = $zaznam["zkratka"];
  }


  
/*  echo "<ul><li><a href=\"./plan_uc.php?kod=$kod&vyber=1\">Oznámení nepøítomnosti </a> (formuláø analogický papírku <i>\"®ádost o pracovní volno\"</i>)</li>";
  echo "<li><a href=\"./plan_uc.php?kod=$kod&vyber=2\">Pøesuny, rezervace uèeben </a> </li></ul><hr><p>";*/
  switch($vyber)
  {

/***  oznameni nepritomnosti ****************************************************************************/
/***************************************************************************************************/
    case 1:
      //Tlacitka($kod, "plan_uc.php", $pole_vyberu, $pole_tlacitek, 1);
      echo "<ul>";
      echo "<li>Akce hlaste minimálnì dva dny dopøedu, tj. je-li 5.&nbsp;1. a akce se koná 6.&nbsp;1., u¾ se Vám ji nepodaøí
        oznámit pøes oasu, ale pouze osobní domluvou.</li>";
      echo "</ul>";

      echo "<center>".Hlaska($chyba, "Akci se nepodaøilo vlo¾it do kalendáøe", "Akce byla úspì¹nì vlo¾ena do kalendáøe")."</center>";
      if($chyba<>"ok")
      {
        if($platnost_od<>"")
        {
          $platnost_od = Datum_bez_mezer($platnost_od,0);
          $platnost_do = Datum_bez_mezer($platnost_do,0);
          if($platnost_od==$platnost_do) $platnost_do="";
        }
      echo "<form action=\"./plan_uc_send.php?kod=$kod&vyber=$vyber\" method=post enctype=\"multipart/form-data\">";

      echo "<p><table border=0><tr><td><b><".c_font.">Datum:</b>";
      echo "<br><font color=gray><small>- datum pi¹te ve formátu <i>den.mìsíc.rok</i>, rok na 4 èíslice<br>";
      echo "- jedná-li se o jednodenní akci, vyplòte pouze jedno datum (libovolné)<br>";
      echo "- vyplníte-li pouze jedno datum, jsou políèka \"Od\" a \"Do\" rovnocenná</small></font>";
      echo "<br>Od: <input type=\"text\" name=\"platnost_od\" value=\"$platnost_od\">";
      echo "<br>Do: <input type=\"text\" name=\"platnost_do\" value=\"$platnost_do\"></td></tr>";
      echo "</table>";

      echo "<p><table border=0><tr><td><".c_font."><b>Vyuèovací hodiny:</b></font>";
      echo "<br><font color=gray><small>- pokud se informace týká pouze jedné hodiny, vyplòte jen <b>Od:</b></small>";
      echo "<br><small>- uvedení vyuèovacích hodin je vhodné zejména u jednodenních akcí</small></font>";
      echo "<br>Od: <input type=\"text\" name=\"hod_od\" value=\"$hod_od\">";
      echo "<br>Do: <input type=\"text\" name=\"hod_do\" value=\"$hod_do\"></td></tr>";
      echo "</table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Dùvod nepøítomnosti:</b>";
      echo "<tr><td><textarea name=\"popis\" value=\"$popis\" rows=5 cols=25></textarea></td></tr></table>";

      if($prava<=2)
      {
        echo "<p><table border=0><tr><td><b><".c_font.">Zkratky vyuèujících:</b>";
        echo "<tr><td><input type=\"text\" name=\"zkratky\" value=\"$zkratky\"></td></tr></table>";
      }

      echo "<table border=0><tr><td><input type = \"submit\" value=\"odeslat\" name=\"odeslano\"></td></tr>";
      echo "</table></form>";
    }
    if($prava<=2)
    {
      $SQL = "select k.*, max(kp.datum_akce) platnost_do, min(kp.datum_akce) platnost_od, k.cas_od cas_od, k.cas_do cas_do, k.hod_od hod_od, k.hod_do hod_do
              from kalendar k, kalendar_prubeh kp
              where k.i = '1' and
                    kp.id_akce = k.id and
                    kp.datum_akce>=Now()-5 and
                    k.popis like '<b>n%'
              group by kp.id_akce
              order by k.datum";
      if(DB_select($SQL, $vyst, $poc))
      {
        $SQL = "select id from kalendar where DATE_ADD(datum, INTERVAL 2 DAY)>=Now() and i='1'";
        if(DB_select($SQL, $vystup, $pocet)) while($zaznam = mysql_fetch_array($vystup)) $id[] = $zaznam["id"];
        echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
        echo "<tr bgcolor=\"#ddddee\"><td><center>datum akce</center></td><td><center>zkratky</center></td><td><center>informace</center></td><td><center>datum ¾ádosti</center></td></tr>";
        echo "<tr><td colspan=\"4\" heigh=\"0\"></td></tr>";
        $barva = "bgcolor=\"#e0e0e0\"";
        $b = 0;
        while($zaz=mysql_fetch_array($vyst))
        {
          $platnost_od = $zaz["platnost_od"];
          $platnost_do = $zaz["platnost_do"];
          $hod_od = $zaz["hod_od"];
          $hod_do = $zaz["hod_do"];
          $cas_od = Cas($zaz["cas_od"]);
          $cas_do = Cas($zaz["cas_do"]);
	  $barva_text="black";
          $i=0;
          $nasel=0;
          while($i<count($id) and $nasel==0)
          {
            if($id[$i] == $zaz["id_kal"])
            {
              $barva_text = "red";
              $nasel = 1;
            }
            $i++;
          }
          echo "<tr $barva>";
          echo "<td><font color=\"$barva_text\">";
          echo Datum($platnost_od,0);
          if($platnost_od<>$platnost_do and Datum($platnost_do,0)<>"") echo " - ".Datum($platnost_do,0);

	  if($cas_od<>"")
          {
            echo "&nbsp;&nbsp;&nbsp;$cas_od";
            if($cas_do<>"") echo " - $cas_do";
          }
	  if($hod_od<>"")
          {
            echo "&nbsp;&nbsp;&nbsp;$hod_od.";
            if($hod_do<>"") echo " - $hod_do.";
          }
          echo "</font></td>";

          echo "<td><font color=\"$barva_text\">".$zaz["zkratky"]."</font></td>";
          echo "<td><font color=\"$barva_text\">".$zaz["popis"]."</font></td>";
          echo "<td><font color=\"$barva_text\">".Datum($zaz["datum"])."</font></td>";
          echo "</tr>";
          if($b==0)
          {
            $barva = "bgcolor=\"#efefef\"";
            $b = 1;
          }
          else
          {
            $barva = "bgcolor=\"#e0e0e0\"";
            $b = 0;
          }
        }
        echo "</table>";
        $platnost_od = "";
        $platnost_do="";
        $hod_od="";
        $hod_do="";
        $popis="";
        $datum="";
        $zkratky="";
      }
    }
    else
    {

      $SQL = "select k.*, max(kp.datum_akce) platnost_do, min(kp.datum_akce) platnost_od, k.hod_od hod_od, k.hod_do hod_do, k.cas_od cas_od, k.cas_do cas_do
              from kalendar k, kalendar_prubeh kp
              where k.i = '1' and
                    kp.id_akce = k.id and
                    kp.datum_akce>=Now()-5 and
                    k.zkratky like '%$zkratka%' and
                    k.login_uc = '$login' and
                    k.popis like '<b>n%'
              group by kp.id_akce
              order by k.datum";
      if(DB_select($SQL, $vyst, $poc))
      {
        $SQL = "select id from kalendar where DATE_ADD(datum, INTERVAL 2 DAY)>=Now() and i='1'";
        if(DB_select($SQL, $vystup, $pocet)) while($zaznam = mysql_fetch_array($vystup)) $id[] = $zaznam["id"];
        echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
        echo "<tr bgcolor=\"#ddddee\"><td><center>datum akce</center></td><td><center>informace</center></td><td><center>datum ¾ádosti</center></td></tr>";
        echo "<tr><td colspan=\"4\" heigh=\"0\"></td></tr>";
        $barva = "bgcolor=\"#e0e0e0\"";
        $b = 0;
        while($zaz=mysql_fetch_array($vyst))
        {
          $platnost_od = $zaz["platnost_od"];
          $platnost_do = $zaz["platnost_do"];
          $cas_od = Cas($zaz["cas_od"]);
          $cas_do = Cas($zaz["cas_do"]);
          $hod_od = $zaz["hod_od"];
          $hod_do = $zaz["hod_do"];
          $barva_text="black";
          $i=0;
          $nasel=0;
          while($i<count($id) and $nasel==0)
          {
            if($id[$i] == $zaz["id_kal"])
            {
              $barva_text = "red";
              $nasel = 1;
            }
            $i++;
          }
          echo "<tr $barva>";
          echo "<td><font color=\"$barva_text\">";
          echo Datum($platnost_od,0);
          if($platnost_od<>$platnost_do and Datum($platnost_do,0)<>"") echo " - ".Datum($platnost_do,0);
          if($cas_od<>"")
            {
              echo "&nbsp;&nbsp;&nbsp;$cas_od";
              if($cas_do<>"") echo " - $cas_do";
            }
            if($hod_od<>"")
            {
              echo "&nbsp;&nbsp;&nbsp;$hod_od.";
              if($hod_do<>"") echo " - $hod_do.";
            }
            echo "</font></td>";

          echo "<td><font color=\"$barva_text\">".$zaz["popis"]."</font></td>";
          echo "<td><font color=\"$barva_text\">".Datum($zaz["datum"])."</font></td>";
          echo "</tr>";
          if($b==0)
          {
            $barva = "bgcolor=\"#efefef\"";
            $b = 1;
          }
          else
          {
            $barva = "bgcolor=\"#e0e0e0\"";
            $b = 0;
          }
        }
        echo "</table>";
        $platnost_od = "";
        $platnost_do="";
        $cas_od="";
        $cas_do="";
        $popis="";
        $datum="";
      }
    }
    break;

   /***  rezervace uceben ****************************************************************************/
   /***************************************************************************************************/

    case 2:
      //Tlacitka($kod, "plan_uc.php", $pole_vyberu, $pole_tlacitek, 2);
      echo "<ul>";
      echo "<li>Akce hlaste minimálnì dva dny dopøedu, tj. je-li 5.&nbsp;1. a akce se koná 6.&nbsp;1., u¾ se Vám ji nepodaøí
        oznámit pøes oasu, ale pouze osobní domluvou.</li>";
      echo "<li>Rezervaci té¾e uèebny lze provést na nìkolik dní najednou pro zjednodu¹ení rezervace na zkou¹kové období.</li>";
      echo "</ul>";

      echo "<center>".Hlaska($chyba, "Akci se nepodaøilo vlo¾it do kalendáøe", "Akce byla úspì¹nì vlo¾ena do kalendáøe")."</center>";
      if($chyba<>"ok")
      {
        if($datum_rezervace[0]<>"")
        {
          $datum_rezervace[0] = Datum_bez_mezer($datum_rezervace[0],0);
          $datum_rezervace[1] = Datum_bez_mezer($datum_rezervace[1],0);
          $datum_rezervace[2] = Datum_bez_mezer($datum_rezervace[2],0);
          $datum_rezervace[3] = Datum_bez_mezer($datum_rezervace[3],0);
          $datum_rezervace[4] = Datum_bez_mezer($datum_rezervace[4],0);
        }
      }
      echo "<form action=\"./plan_uc_send.php?kod=$kod&vyber=$vyber\" method=post enctype=\"multipart/form-data\">";
      echo "<p><table border=0>";

      echo "<tr><td colspan=\"2\"><b><".c_font.">Tøída/studijní skupina:</b></td></tr>";
      echo "<tr><td colspan=\"2\"><input type=\"text\" name=\"trida\" value=\"$trida\"></td>";
      echo "<tr><td>&nbsp;</td></tr>";
      echo "<tr><td colspan=\"2\"><b><".c_font.">Pøedmìt:</b></td></tr>";
      echo "<tr><td colspan=\"2\"><input type=\"text\" name=\"predmet\" value=\"$predmet\"></td>";
      echo "<tr><td>&nbsp;</td></tr>";

      echo "<tr><td colspan=\"6\"><b><".c_font.">Datum(y) rezervace:</b>";
      echo "<br><font color=gray><small>- datum pi¹te ve formátu <i>den.mìsíc.rok</i>, rok na 4 èíslice<br></small></font>";
      echo "<br>Datum 1: <input type=\"text\" name=\"datum_rezervace0\" value=\"".$datum_rezervace[0]."\">";
      echo "<br>Datum 2: <input type=\"text\" name=\"datum_rezervace1\" value=\"".$datum_rezervace[1]."\">";
      echo "<br>Datum 3: <input type=\"text\" name=\"datum_rezervace2\" value=\"".$datum_rezervace[2]."\">";
      echo "<br>Datum 4: <input type=\"text\" name=\"datum_rezervace3\" value=\"".$datum_rezervace[3]."\">";
      echo "<br>Datum 5: <input type=\"text\" name=\"datum_rezervace4\" value=\"".$datum_rezervace[4]."\"></td></tr>";

      echo "</table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Vyuèovací hodiny:</b>";
      echo "<br><font color=gray><small>- pi¹te èíslem (bez teèky); v pøípadì, ¾e rezervujete uèebnu na souvislý blok vyuèovacích hodin, vyplòte i Do, </font><font color=\"red\">jestli¾e ale uèíte jen jednu hodinu, vyplòte pouze <b>Od</b></font></small></font>";
      echo "<br>Od: <input type=\"text\" name=\"hod_od\" value=\"$hod_od\">";
      echo "<br>Do: <input type=\"text\" name=\"hod_do\" value=\"$hod_do\"></td></tr>";
      echo "</table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Uèebna:</b>";
      echo "<tr><td><input type=\"text\" name=\"uceb\" value=\"$uceb\"></td></tr></table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Dùvod rezervace </b>(pro pøípadné uvedení v poznámce na rozvrhu):";
      echo "<tr><td><textarea name=\"popis\" value=\"$popis\" rows=5 cols=25></textarea></td></tr></table>";

      if($prava<=2)
      {
        echo "<p><table border=0><tr><td><b><".c_font.">Zkratky vyuèujících</b>";
        echo "<tr><td><input type=\"text\" name=\"zkratky\" value=\"$zkratky\"></td></tr></table>";
      }

      echo "<table border=0><tr><td><input type = \"submit\" value=\"odeslat\" name=\"odeslano\"></td></tr>";
      echo "</table></form>";

    if($prava<=2)
    {
      $SQL = "select k.*, max(kp.datum_akce) platnost_do, min(kp.datum_akce) platnost_od, k.hod_od hod_od, k.hod_do hod_do
              from kalendar k, kalendar_prubeh kp
              where k.i = '1' and
                    kp.id_akce = k.id and
                    kp.datum_akce>=Now()-5 and
                    k.popis like '<b>r%'
              group by kp.id_akce
              order by k.datum";
      if(DB_select($SQL, $vyst, $poc))
      {
        $SQL = "select id from kalendar where DATE_ADD(datum, INTERVAL 2 DAY)>=Now() and i='1'";
        if(DB_select($SQL, $vystup, $pocet)) while($zaznam = mysql_fetch_array($vystup)) $id[] = $zaznam["id"];
        echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
        echo "<tr bgcolor=\"#ddddee\"><td><center>datum</center></td><td><center>zkratky</center></td><td><center>informace</center></td><td><center>datum ¾ádosti</center></td></tr>";
        echo "<tr><td colspan=\"4\" heigh=\"0\"></td></tr>";
        $barva = "bgcolor=\"#e0e0e0\"";
        $b = 0;
        while($zaz=mysql_fetch_array($vyst))
        {
          $platnost_od = $zaz["platnost_od"];
          $platnost_do = $zaz["platnost_do"];
          $cas_od = Cas($zaz["cas_od"]);
          $cas_do = Cas($zaz["cas_do"]);
          $hod_od = $zaz["hod_od"];
          $hod_do = $zaz["hod_do"];
          $barva_text="black";
          $i=0;
          $nasel=0;
          while($i<count($id) and $nasel==0)
          {
            if($id[$i] == $zaz["id_kal"])
            {
              $barva_text = "red";
              $nasel = 1;
            }
            $i++;
          }
          echo "<tr $barva>";
          echo "<td><font color=\"$barva_text\">";
              echo Datum($platnost_od,0);
              if($platnost_od<>$platnost_do and Datum($platnost_do,0)<>"") echo " - ".Datum($platnost_do,0);
              if($cas_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$cas_od";
                if($cas_do<>"") echo " - $cas_do";
              }
              if($hod_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$hod_od.";
                if($hod_do<>"") echo " - $hod_do.";
                echo " h.";
              }
              echo "</font></td>";

          echo "<td><font color=\"$barva_text\">".$zaz["zkratky"]."</font></td>";
          echo "<td><font color=\"$barva_text\">".$zaz["popis"]."</font></td>";
          echo "<td><font color=\"$barva_text\">".Datum($zaz["datum"])."</font></td>";
          echo "</tr>";
          if($b==0)
          {
            $barva = "bgcolor=\"#efefef\"";
            $b = 1;
          }
          else
          {
            $barva = "bgcolor=\"#e0e0e0\"";
            $b = 0;
          }
        }
        echo "</table>";
        $platnost_od = "";
        $platnost_do="";
        $cas_od="";
        $cas_do="";
        $popis="";
        $datum="";
        $zkratky="";
      }
    }
    else
    {
      $SQL = "select k.*, max(kp.datum_akce) platnost_do, min(kp.datum_akce) platnost_od, k.cas_od cas_od, k.cas_do cas_do
              from kalendar k, kalendar_prubeh kp
              where k.i = '1' and
                    kp.id_akce = k.id and
                    kp.datum_akce>=Now()-5 and
                    k.zkratky like '%$zkratka%' and
                    k.login_uc = '$login' and
                    k.popis like '<b>r%'
              group by kp.id_akce
              order by k.datum";
      if(DB_select($SQL, $vyst, $poc))
      {
        $SQL = "select id from kalendar where DATE_ADD(datum, INTERVAL 2 DAY)>=Now() and i='1'";
        if(DB_select($SQL, $vystup, $pocet)) while($zaznam = mysql_fetch_array($vystup)) $id[] = $zaznam["id"];
        echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
        echo "<tr bgcolor=\"#ddddee\"><td><center>datum akce</center></td><td><center>informace</center></td><td><center>datum ¾ádosti</center></td></tr>";
        echo "<tr><td colspan=\"4\" heigh=\"0\"></td></tr>";
        $barva = "bgcolor=\"#e0e0e0\"";
        $b = 0;
        while($zaz=mysql_fetch_array($vyst))
        {
          $platnost_od = $zaz["platnost_od"];
          $platnost_do = $zaz["platnost_do"];
          $cas_od = Cas($zaz["cas_od"]);
          $cas_do = Cas($zaz["cas_do"]);
          $hod_od = $zaz["hod_od"].".";
          $hod_do = $zaz["hod_do"].".";
          $barva_text="black";
          $i=0;
          $nasel=0;
          while($i<count($id) and $nasel==0)
          {
            if($id[$i] == $zaz["id_kal"])
            {
              $barva_text = "red";
              $nasel = 1;
            }
            $i++;
          }
          echo "<tr $barva>";
          echo "<td><font color=\"$barva_text\">";
              echo Datum($platnost_od,0);
              if($platnost_od<>$platnost_do and Datum($platnost_do,0)<>"") echo " - ".Datum($platnost_do,0);
              if($cas_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$cas_od";
                if($cas_do<>"") echo " - $cas_do";
              }
              if($hod_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$hod_od.";
                if($hod_do<>"") echo " - $hod_do.";
                echo " h.";
              }
              echo "</font></td>";

          echo "<td><font color=\"$barva_text\">".$zaz["popis"]."</font></td>";
          echo "<td><font color=\"$barva_text\">".Datum($zaz["datum"])."</font></td>";
          echo "</tr>";
          if($b==0)
          {
            $barva = "bgcolor=\"#efefef\"";
            $b = 1;
          }
          else
          {
            $barva = "bgcolor=\"#e0e0e0\"";
            $b = 0;
          }
        }
        echo "</table>";
        $platnost_od = "";
        $platnost_do="";
        $cas_od="";
        $cas_do="";
        $popis="";
        $datum="";
      }
    }

    break;

/***  presuny uceben ****************************************************************************/
/***************************************************************************************************/
/*
    case 3:
      //Tlacitka($kod, "plan_uc.php", $pole_vyberu, $pole_tlacitek, 3);
      echo "<P><ul>";
      echo "<li>Akce hlaste minimálnì dva dny dopøedu, tj. je-li 5.&nbsp;1. a akce se koná 6.&nbsp;1., u¾ se Vám ji nepodaøí
        oznámit pøes oasu, ale pouze osobní domluvou.</li>";
      echo "<li>Datum pi¹te jako obvykle ve formátu <i>den.mìsíc.rok</i>, rok na 4 èíslice.</li>";
      echo "<li>Vyuèovací hodiny pi¹te bez teèky, </font><font color=\"red\">jestli¾e uèíte jen jednu hodinu, vyplòte pouze <b>Od</b></font>.</li>";
      echo "<li>Chcete-li nìkterý údaj zachovat nezmìnìný, nemusíte jej ve druhé kartièce vyplòovat.</li>";
      echo "</ul>";


      echo "<center>".Hlaska($chyba, "Akci se nepodaøilo vlo¾it do kalendáøe", "Akce byla úspì¹nì vlo¾ena do kalendáøe")."</center>";
      if($chyba<>"ok")
      {
        if($datum_odkud<>"") $datum_odkud = Datum_bez_mezer($datum_odkud,0);
        if($datum_kam<>"") $datum_kam = Datum_bez_mezer($datum_kam,0);
      }
      echo "<form action=\"./plan_uc_send.php?kod=$kod&vyber=$vyber\" method=post enctype=\"multipart/form-data\">";

      echo "<p><table border=\"0\" cellpadding=\"5\">";
      echo "<tr>";
      echo "<td><table border=\"1\" bgcolor=\"#e3e3e3\"><tr><td><font color=\"#ee4444\">Pùvodní výuka</font></td></tr>";
      echo "<tr><td><table border=\"0\">";
      echo "<tr><td colspan=\"3\"><b><".c_font.">Datum:</b></td></tr>";
      echo "<tr><td colspan=\"2\"><input type=\"text\" name=\"datum_odkud\" value=\"$datum_odkud\"></td>";
      echo "<tr><td>&nbsp;</td></tr>";
      echo "<tr><td colspan=\"2\"><b><".c_font.">Vyuèovací hodiny:</b></td></tr>";
      echo "<tr><td>Od: </td><td><input type=\"text\" name=\"hod_od_odkud\" value=\"$hod_od_odkud\"></td>";
      echo "<tr><td>Do: </td><td><input type=\"text\" name=\"hod_do_odkud\" value=\"$hod_do_odkud\"></td>";
      echo "<tr><td>&nbsp;</td></tr>";
      echo "<tr><td colspan=\"2\"><b><".c_font.">Uèebna:</b></td></tr>";
      echo "<tr><td colspan=\"2\"><input type=\"text\" name=\"uceb_odkud\" value=\"$uceb_odkud\"></td>";

      echo "</table></td></tr></table></td>";

      echo "<td><td valign=\"middle\" rowspan=\"2\"><img src=\"./images/sipka_red.gif\"></td>";

      echo "<td><table border=\"1\" bgcolor=\"#e3e3e3\"><tr><td><font color=\"#ee4444\">Po pøesunu</font></td></tr>";
      echo "<tr><td><table border=\"0\">";
      echo "<tr><td colspan=\"3\"><b><".c_font.">Datum:</b></td></tr>";
      echo "<tr><td colspan=\"2\"><input type=\"text\" name=\"datum_kam\" value=\"$datum_kam\"></td>";
      echo "<tr><td>&nbsp;</td></tr>";
      echo "<tr><td colspan=\"2\"><b><".c_font.">Vyuèovací hodiny:</b></td></tr>";
      echo "<tr><td>Od: </td><td><input type=\"text\" name=\"hod_od_kam\" value=\"$hod_od_kam\"></td>";
      echo "<tr><td>Do: </td><td><input type=\"text\" name=\"hod_do_kam\" value=\"$hod_do_kam\"></td>";
      echo "<tr><td>&nbsp;</td></tr>";
      echo "<tr><td colspan=\"2\"><b><".c_font.">Uèebna:</b></td></tr>";
      echo "<tr><td colspan=\"2\"><input type=\"text\" name=\"uceb_kam\" value=\"$uceb_kam\"></td>";
      echo "</table></td></tr></table></td></tr>";
      echo "</table>";


      echo "<p><table border=0>";
      echo "<tr><td colspan=\"2\"><b><".c_font.">Tøída/studijní skupina:</b></td></tr>";
      echo "<tr><td colspan=\"2\"><input type=\"text\" name=\"trida\" value=\"$trida\"></td>";
      echo "<tr><td>&nbsp;</td></tr>";
      echo "<tr><td colspan=\"2\"><b><".c_font.">Pøedmìt:</b></td></tr>";
      echo "<tr><td colspan=\"2\"><input type=\"text\" name=\"predmet\" value=\"$predmet\"></td>";
      echo "<tr><td>&nbsp;</td></tr>";
      echo "<tr><td><b><".c_font.">Dùvod pøesunu </b>(pro pøípadné uvedení v poznámce na rozvrhu):";
      echo "<tr><td><textarea name=\"popis\" value=\"$popis\" rows=5 cols=25></textarea></td></tr></table>";

      if($prava<=2)
      {
        echo "<p><table border=0><tr><td><b><".c_font.">Zkratky vyuèujících</b>";
        echo "<tr><td><input type=\"text\" name=\"zkratky\" value=\"$zkratky\"></td></tr></table>";
      }

      echo "<table border=0><tr><td><input type = \"submit\" value=\"odeslat\" name=\"odeslano\"></td></tr>";
      echo "</table></form>";

    if($prava<=2)
    {
      $SQL = "select k.*, max(kp.datum_akce) platnost_do, min(kp.datum_akce) platnost_od, k.cas_od cas_od, k.cas_do cas_do
              from kalendar k, kalendar_prubeh kp
              where k.i = '1' and
                    kp.id_akce = k.id and
                    kp.datum_akce>=Now()-5 and
                    k.popis like '<b>p%'
              group by kp.id_akce
              order by k.datum";
      if(DB_select($SQL, $vyst, $poc))
      {
        $SQL = "select id from kalendar where DATE_ADD(datum, INTERVAL 2 DAY)>=Now() and i='1'";
        if(DB_select($SQL, $vystup, $pocet)) while($zaznam = mysql_fetch_array($vystup)) $id[] = $zaznam["id"];
        echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
        echo "<tr bgcolor=\"#ddddee\"><td><center>datum</center></td><td><center>zkratky</center></td><td><center>informace</center></td><td><center>datum ¾ádosti</center></td></tr>";
        echo "<tr><td colspan=\"4\" heigh=\"0\"></td></tr>";
        $barva = "bgcolor=\"#e0e0e0\"";
        $b = 0;
        while($zaz=mysql_fetch_array($vyst))
        {
          $platnost_od = $zaz["platnost_od"];
          $platnost_do = $zaz["platnost_do"];
          $cas_od = Cas($zaz["cas_od"]);
          $cas_do = Cas($zaz["cas_do"]);
          $hod_od = $zaz["hod_od"];
          $hod_do = $zaz["hod_do"];
          $barva_text="black";
          $i=0;
          $nasel=0;
          while($i<count($id) and $nasel==0)
          {
            if($id[$i] == $zaz["id_kal"])
            {
              $barva_text = "red";
              $nasel = 1;
            }
            $i++;
          }
          echo "<tr $barva>";
          echo "<td><font color=\"$barva_text\">";
              echo Datum($platnost_od,0);
              if($platnost_od<>$platnost_do and Datum($platnost_do,0)<>"") echo " - ".Datum($platnost_do,0);
              if($cas_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$cas_od";
                if($cas_do<>"") echo " - $cas_do";
              }
              if($hod_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$hod_od.";
                if($hod_do<>"") echo " - $hod_do.";
		echo " h.";
              }
              echo "</font></td>";

          echo "<td><font color=\"$barva_text\">".$zaz["zkratky"]."</font></td>";
          echo "<td><font color=\"$barva_text\">".$zaz["popis"]."</font></td>";
          echo "<td><font color=\"$barva_text\">".Datum($zaz["datum"])."</font></td>";
          echo "</tr>";
          if($b==0)
          {
            $barva = "bgcolor=\"#efefef\"";
            $b = 1;
          }
          else
          {
            $barva = "bgcolor=\"#e0e0e0\"";
            $b = 0;
          }
        }
        echo "</table>";
        $platnost_od = "";
        $platnost_do="";
        $cas_od="";
        $cas_do="";
        $popis="";
        $datum="";
        $zkratky="";
      }
    }
    else
    {
      $SQL = "select k.*, max(kp.datum_akce) platnost_do, min(kp.datum_akce) platnost_od, k.cas_od cas_od, k.cas_do cas_do
              from kalendar k, kalendar_prubeh kp
              where k.i = '1' and
                    kp.id_akce = k.id and
                    kp.datum_akce>=Now()-5 and
                    k.zkratky like '%$zkratka%' and
                    k.popis like '<b>p%' and
                    k.login_uc = '$login'
              group by kp.id_akce
              order by k.datum";
      if(DB_select($SQL, $vyst, $poc))
      {
        $SQL = "select id from kalendar where DATE_ADD(datum, INTERVAL 2 DAY)>=Now() and i='1'";
        if(DB_select($SQL, $vystup, $pocet)) while($zaznam = mysql_fetch_array($vystup)) $id[] = $zaznam["id"];
        echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
        echo "<tr bgcolor=\"#ddddee\"><td><center>datum akce</center></td><td><center>informace</center></td><td><center>datum ¾ádosti</center></td></tr>";
        echo "<tr><td colspan=\"4\" heigh=\"0\"></td></tr>";
        $barva = "bgcolor=\"#e0e0e0\"";
        $b = 0;
        while($zaz=mysql_fetch_array($vyst))
        {
          $platnost_od = $zaz["platnost_od"];
          $platnost_do = $zaz["platnost_do"];
          $cas_od = Cas($zaz["cas_od"]);
          $cas_do = Cas($zaz["cas_do"]);
          $hod_od = $zaz["hod_od"];
          $hod_do = $zaz["hod_do"];
          $barva_text="black";
          $i=0;
          $nasel=0;
          while($i<count($id) and $nasel==0)
          {
            if($id[$i] == $zaz["id_kal"])
            {
              $barva_text = "red";
              $nasel = 1;
            }
            $i++;
          }
          echo "<tr $barva>";
          echo "<td><font color=\"$barva_text\">";
              echo Datum($platnost_od,0);
              if($platnost_od<>$platnost_do and Datum($platnost_do,0)<>"") echo " - ".Datum($platnost_do,0);
              if($cas_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$cas_od";
                if($cas_do<>"") echo " - $cas_do";
              }
              if($hod_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$hod_od.";
                if($hod_do<>"") echo " - $hod_do.";
                echo " h.";
              }
              echo "</font></td>";

          echo "<td><font color=\"$barva_text\">".$zaz["popis"]."</font></td>";
          echo "<td><font color=\"$barva_text\">".Datum($zaz["datum"])."</font></td>";
          echo "</tr>";
          if($b==0)
          {
            $barva = "bgcolor=\"#efefef\"";
            $b = 1;
          }
          else
          {
            $barva = "bgcolor=\"#e0e0e0\"";
            $b = 0;
          }
        }
        echo "</table>";
        $platnost_od = "";
        $platnost_do="";
        $cas_od="";
        $cas_do="";
        $popis="";
        $datum="";
      }
    }
  break;*/


  /***  odstraneni zpravy ****************************************************************************/
/***************************************************************************************************/

  case 3:
    //Tlacitka($kod, "plan_uc.php", $pole_vyberu, $pole_tlacitek, 3);
     echo "<p><ul><li><b>Pøesuny uèeben ze dne na den</b> se ukládají jako dvì polo¾ky - akce na oba dny, proto je nutno je odstranit také obì.
         Pøi souèasném øe¹ení oasy je technicky nemo¾né zjistit, ¾e jde o jeden pøesun.</li>";
    echo "<li>Zprávy, které se týkají dne¹ního a zítøej¹ího dne, mohou mazat <b>pouze zástupci</b>. V seznamu nejsou vùbec uvedeny.</li>";
    echo "</ul>";

    echo "<p>";
    if($prava<=2)
    {
      $SQL = "select k.*, max(kp.datum_akce) platnost_do, min(kp.datum_akce) platnost_od, k.cas_od cas_od, k.cas_do cas_do
              from kalendar k, kalendar_prubeh kp
              where k.i = '1' and
                    kp.id_akce = k.id and
                    kp.datum_akce>=Now()
              group by kp.id_akce
              order by k.datum";
      if(DB_select($SQL, $vyst, $poc))
      {
        $SQL = "select id from kalendar where DATE_ADD(datum, INTERVAL 2 DAY)>=Now() and i='1'";
        if(DB_select($SQL, $vystup, $pocet)) while($zaznam = mysql_fetch_array($vystup)) $id[] = $zaznam["id"];
        echo "<form action=\"./plan_uc_send.php?kod=$kod&vyber=$vyber\" method=post enctype=\"multipart/form-data\">";
        echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
        echo "<tr bgcolor=\"#ddddee\"><td></td><td><center>datum akce</center></td><td><center>zkratky</center></td><td><center>informace</center></td><td><center>datum ¾ádosti</center></td></tr>";
        echo "<tr><td colspan=\"4\" heigh=\"0\"></td></tr>";
        $barva = "bgcolor=\"#e0e0e0\"";
        $b = 0;
        while($zaz=mysql_fetch_array($vyst))
        {
          $platnost_od = $zaz["platnost_od"];
          $platnost_do = $zaz["platnost_do"];
          $cas_od = Cas($zaz["cas_od"]);
          $cas_do = Cas($zaz["cas_do"]);
          $hod_od = $zaz["hod_od"];
          $hod_do = $zaz["hod_do"];
          $barva_text="black";
          $i=0;
          $nasel=0;
          while($i<count($id) and $nasel==0)
          {
            if($id[$i] == $zaz["id_kal"])
            {
              $barva_text = "red";
              $nasel = 1;
            }
            $i++;
          }
          echo "<tr $barva>";
          echo "<td><input type=\"checkbox\" value=\"".$zaz["id"]."\" name=\"vymaz[]\"></td>";
          echo "<td><font color=\"$barva_text\">";
              echo Datum($platnost_od,0);
              if($platnost_od<>$platnost_do and Datum($platnost_do,0)<>"") echo " - ".Datum($platnost_do,0);
              if($cas_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$cas_od";
                if($cas_do<>"") echo " - $cas_do";
              }
              if($hod_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$hod_od.";
                if($hod_do<>"") echo " - $hod_do.";
                echo " h.";
              }
              echo "</font></td>";

          echo "<td><font color=\"$barva_text\">".$zaz["zkratky"]."</font></td>";
          echo "<td><font color=\"$barva_text\">".$zaz["popis"]."</font></td>";
          echo "<td><font color=\"$barva_text\">".Datum($zaz["datum"])."</font></td>";
          echo "</tr>";
          if($b==0)
          {
            $barva = "bgcolor=\"#efefef\"";
            $b = 1;
          }
          else
          {
            $barva = "bgcolor=\"#e0e0e0\"";
            $b = 0;
          }
        }
        echo "<tr><td colspan=\"5\"><input type=\"submit\" name=\"odesli\" value=\"vymazat vybrané zprávy\"></td></tr>";
        echo "</table>";
        echo "</form>";
        $platnost_od = "";
        $platnost_do="";
        $cas_od="";
        $cas_do="";
        $popis="";
        $datum="";
        $zkratky="";
      }
    }
    else
    {

      $SQL = "select k.*, max(kp.datum_akce) platnost_do, min(kp.datum_akce) platnost_od, k.cas_od cas_od, k.cas_do cas_do
              from kalendar k, kalendar_prubeh kp
              where k.i = '1' and
                    kp.id_akce = k.id and
                    kp.datum_akce>=DATE_ADD(Now(), INTERVAL 2 DAY) and
                    k.zkratky like '%$zkratka%' and
                    k.login_uc = '$login'
              group by kp.id_akce
              order by k.datum";
      if(DB_select($SQL, $vyst, $poc))
      {
        $SQL = "select id from kalendar where DATE_ADD(datum, INTERVAL 2 DAY)>=Now() and i='1'";
        if(DB_select($SQL, $vystup, $pocet)) while($zaznam = mysql_fetch_array($vystup)) $id[] = $zaznam["id"];
        echo "<form action=\"./plan_uc_send.php?kod=$kod&vyber=$vyber\" method=post enctype=\"multipart/form-data\">";
        echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
        echo "<tr bgcolor=\"#ddddee\"><td></td><td><center>datum akce</center></td><td><center>informace</center></td><td><center>datum ¾ádosti</center></td></tr>";
        echo "<tr><td colspan=\"4\" heigh=\"0\"></td></tr>";
        $barva = "bgcolor=\"#e0e0e0\"";
        $b = 0;
        while($zaz=mysql_fetch_array($vyst))
        {
          $platnost_od = $zaz["platnost_od"];
          $platnost_do = $zaz["platnost_do"];
          $cas_od = Cas($zaz["cas_od"]);
          $cas_do = Cas($zaz["cas_do"]);
          $hod_od = $zaz["hod_od"];
          $hod_do = $zaz["hod_do"];
          $barva_text="black";
          $i=0;
          $nasel=0;
          while($i<count($id) and $nasel==0)
          {
            if($id[$i] == $zaz["id_kal"])
            {
              $barva_text = "red";
              $nasel = 1;
            }
            $i++;
          }

          echo "<tr $barva>";
          echo "<td><input type=\"checkbox\" value=\"".$zaz["id"]."\" name=\"vymaz[]\"></td>";
          echo "<td><font color=\"$barva_text\">";
              echo Datum($platnost_od,0);
              if($platnost_od<>$platnost_do and Datum($platnost_do,0)<>"") echo " - ".Datum($platnost_do,0);
              if($cas_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$cas_od";
                if($cas_do<>"") echo " - $cas_do";
              }
              if($hod_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$hod_od.";
                if($hod_do<>"") echo " - $hod_do.";
                echo " h.";
              }
              echo "</font></td>";

          echo "<td><font color=\"$barva_text\">".$zaz["popis"]."</font></td>";
          echo "<td><font color=\"$barva_text\">".Datum($zaz["datum"])."</font></td>";
          echo "</tr>";
          if($b==0)
          {
            $barva = "bgcolor=\"#efefef\"";
            $b = 1;
          }
          else
          {
            $barva = "bgcolor=\"#e0e0e0\"";
            $b = 0;
          }
        }
        echo "<tr><td colspan=\"5\"><input type=\"submit\" name=\"odesli\" value=\"vymazat vybrané zprávy\"></td></tr>";
        echo "</table>";
        echo "</form>";
        $platnost_od = "";
        $platnost_do="";
        $cas_od="";
        $cas_do="";
        $popis="";
        $datum="";
      }
    }

  break;
  }






  Konec();
}
?>
