<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba))
{
  NoCACHE();
  $pole_tlacitek = array("Pøehledný<br>kalendáø", "Podrobný<br>kalendáø", "Zkratky akcí");
  $pole_vyberu = array("vyber=1", "vyber=2", "vyber=3");
  if(!($vyber)) $vyber=1;
  Hlavicka("Kalendáø akcí ¹koly", $fullname, $kod, "plan_kal.php", $pole_vyberu, $pole_tlacitek, $vyber);

  $SQL = "select prava from prihl_uziv where kod='$kod'";
  if(DB_select($SQL, $vyst, $pocet))
  {
    if($zaznam=mysql_fetch_array($vyst)) $prava = $zaznam["prava"];
  }
  class CskolniRok
      {
        var $rok1, $rok2;

        function CskolniRok($rok1, $rok2)
        {
        $this -> rok1 = $rok1;
        $this -> rok2 = $rok2;
        }
      }


  switch($vyber)
  {

/***  prehledny kalendar ****************************************************************************/
/***************************************************************************************************/
    case 1:
      /*Podnadpis("Pøehledný kalendáø");*/
      //Tlacitka($kod, "plan_kal.php", $pole_vyberu, $pole_tlacitek, 1);

    /*** zjisteni zarazeni studenta/pracovnika ************************************************************************************/
      $SQL = "select skola from skupiny where id='$skupina'";
      if(DB_select($SQL, $vystup, $pocet)) if($zaznam=mysql_fetch_array($vystup)) $skola = $zaznam["skola"];



      if($rok=="")
      {
        $dnes_mesic = Date("m");
        $dnes_rok = Date("Y");
        if($dnes_mesic>="9" and $dnes_mesic<="12") $rok = $dnes_rok;
        if($dnes_mesic>="1" and $dnes_mesic<="8") $rok = $dnes_rok-1;
      }

      if($mesic=="")
      {
        $mesic = 13;
      }

      $SQL = "select year(min(datum_akce)) min_year, month(min(datum_akce)) min_month, year(max(datum_akce)) max_year, month(max(datum_akce)) max_month from kalendar_prubeh where year(datum_akce)>=year(Now())";
      $rok_min="";
      if(DB_select($SQL, $vystup, $pocet))
      {
        if($zaznam=mysql_fetch_array($vystup))
        {
          $min_year = $zaznam["min_year"];
          $year = $min_year;
          $min_month = $zaznam["min_month"];
          $max_year = $zaznam["max_year"];
          $max_month = $zaznam["max_month"];
          $predchozi_pol = 0;
          $posledni_pol = 0;
          if($min_month>=1 and $min_month<=8)
          {
            $pom_rok = $min_year-1;
            $skolni_rok[] = new CSkolniRok($pom_rok, $min_year);
          }
          if($max_month>=1 and $max_month<=8) $zahrn_1_pol = 1;

          while($year<$max_year)
          {
            $pom_rok = $year+1;
            $skolni_rok[] = new CSkolniRok($year, $pom_rok);
            $year++;
          }
          if($max_month>=9 and $max_month<=12)
          {
            $pom_rok = $max_year+1;
            $skolni_rok[] = new CSkolniRok($max_year, $pom_rok);

          }
        }
        echo "<form action=\"plan_kal.php?kod=$kod&vyber=$vyber\" method=\"post\">";

    /*** vyber roku **********************************************************************************/
        echo "©kolní rok <select name=\"rok\">";
        for($i=0;$i<count($skolni_rok);$i++)
        {
          $select = "";
          if($rok==$skolni_rok[$i]->rok1) $select="selected";
          echo "<option value=\"".$skolni_rok[$i]->rok1."\" $select>".$skolni_rok[$i]->rok1."/".$skolni_rok[$i]->rok2;
        }
        echo "</select>";

    /*** vyber mesice *******************************************************************************/
        echo "&nbsp;&nbsp;&nbsp;Mìsíc <select name=\"mesic\">";

	echo "<option value=\"13\">(v¹echny)";
        for($i=1;$i<=12;$i++)
        {
          $select = "";
          if($mesic==$i) $select="selected";
          echo "<option value=\"".$i."\" $select>".Mesic($i);
        }
        echo "</select>";
        if($mesic<>"13") $podminka_mesic = " and month(kp.datum_akce)='$mesic'";

    /*** vyber studia ******************************************************************************/

        if($vybrano)
        {
          switch($skola)
          {
            case "u":
                 $podminka = "0=1";
                 for($i=0;$i<4;$i++) $checked_i = "";
                 for($i=0;$i<count($stud);$i++)
                 {
                   switch($stud[$i])
                   {
                     case "g":    $podminka .= " or g = '1'";
                                  $checked[0] = "checked";
                                  break;
                     case "j":    $podminka .= " or j = '1'";
                                  $checked[1] = "checked";
                                  break;
                     case "u":    $podminka .= " or u = '1'";
                                  $checked[2] = "checked";
                                  break;
                  }
                 }
                 break;
            case "g":
                 $podminka = "g = '1'";
                 break;
            case "j":
                 $podminka = "j = '1'";
                 break;
          }
        }
        else
        {
          switch($skola)
          {
            case "u":
                 $podminka = "g = '1' or j = '1' or u='1'";
                 for($i=0;$i<3;$i++) $checked[$i] = "checked";
                 break;
            case "g":
                 $podminka = "g = '1'";
                 break;
            case "j":
                 $podminka = "j = '1'";
                 break;
          }
        }

        if($skola=="u")
        {
          echo "<p>Zobrazit pouze akce:";
          echo "<br><input type=\"checkbox\" name=\"stud[]\" $checked[0] value=\"g\"> gymnázia";
          echo "<br><input type=\"checkbox\" name=\"stud[]\" $checked[1] value=\"j\"> jazykové ¹koly";
          echo "<br><input type=\"checkbox\" name=\"stud[]\" $checked[2] value=\"u\"> uèitelù";
         }
        /*** vyber zobrazeni (vsechno nebo jenom aktualni) ******************************************************************************/
        if($zobrazit_vse=="vse") $checked="checked";
        echo "<p><input type=\"checkbox\" name=\"zobrazit_vse\" value=\"vse\" $checked>zobrazit i pøedchozí (ji¾ uskuteènìné) akce";
        echo "<p><input type=\"submit\" name=\"vybrano\" value=\"Vypsat akce\">";
        echo "</form>";
        $rok_dalsi=$rok_min+1;

        if($zobrazit_vse<>"vse") $podminka_zobrazeni = " and datum_akce>=Now() ";
        else $podminka_zobrazeni = " and 1=1";

        $SQL = "select id from kalendar where DATE_ADD(datum, INTERVAL 2 DAY)>=Now()";
        if(DB_select($SQL, $vyst, $pocet)) while($zaznam = mysql_fetch_array($vyst)) $id[] = $zaznam["id"];

        $rok_2 = $rok + 1;
        $SQL = "select k.*, k.popis popis1, a.*, min(kp.datum_akce) akce_platnost_od, max(kp.datum_akce) akce_platnost_do,
		k.id_akce id_stand_akce, k.id id_kal, k.cas_od cas_od, k.cas_do cas_do, s.*, kp.*, month(min(kp.datum_akce)) datum_akce_month
                from kalendar k left join soubory s on s.id=k.id_soub, akce a, kalendar_prubeh kp
                where a.id = k.id_akce and
                      ( (year(kp.datum_akce)='$rok_2' and month(kp.datum_akce)<='8') or
                      (year(kp.datum_akce)='$rok' and month(kp.datum_akce)>='9') ) and
                      kp.id_akce = k.id and
                      ($podminka)
                      $podminka_mesic
                      $podminka_zobrazeni
                group by kp.id_akce
                order by akce_platnost_od, k.cas_od, k.cas_do";
        if(DB_select($SQL, $vystup, $pocet))
        {
          $min_mes="";
          $min_den="";
          echo "<table border = \"0\" cellpadding=3 cellspacing = 0 width = \"100%\">";
         /* echo "<tr><td>termín</td><td>zkratka</td><td>akce</td><td>studium</td><td>podrobnosti</td></tr>";
          echo "<tr><td colspan=\"5\"><hr></td></tr>";*/
          while($zaz = mysql_fetch_array($vystup))
          {
            /* pokud byl zvolen konkretni mesic, zjisti se, zda akce trvala uz driv nebo bude trvat pozdeji */
            /*$sipka_L = "";
            $sipka_P = "";
            if($mesic<>13)
            {
              $SQL = "select count(kp.id_akce) pocet_mensich
                      from kalendar_prubeh kp
                      where id_akce='".$zaz["id_kal"]."' and
                            ( (year(kp.datum_akce)='$rok_2' and month(kp.datum_akce)<='8') or
                              (year(kp.datum_akce)='$rok' and month(kp.datum_akce)>='9') ) and
                            ( ($mesic>8 and month(kp.datum_akce)<$mesic) or
                              ($mesic<9 and (month(kp.datum_akce)<$mesic or (month(kp.datum_akce)<=12 and month(kp.datum_akce)>=9))))
                            ";
              echo "<p>SQL1 = $SQL";
              if(DB_select($SQL, $vystup1, $pocet)) $zaz_mensi = mysql_fetch_array($vystup1);
              if($zaz_mensi["pocet_mensich"]<>0) $sipka_L = "<img src = \"./images/sipka_L1.gif\">";
              $SQL = "select count(kp.id_akce) pocet_vetsich
                      from kalendar_prubeh kp
                      where id_akce='".$zaz["id_kal"]."' and
                            ( (year(kp.datum_akce)='$rok_2' and month(kp.datum_akce)<='8') or
                              (year(kp.datum_akce)='$rok' and month(kp.datum_akce)>='9') ) and
                            ( ($mesic<9 and month(kp.datum_akce)>$mesic) or
                              ($mesic>8 and (month(kp.datum_akce)>$mesic or (month(kp.datum_akce)<=8 and month(kp.datum_akce)>=1))))
                            ";
              echo "<p>SQL2 = $SQL";
              if(DB_select($SQL, $vystup2, $pocet)) $zaz_vetsi = mysql_fetch_array($vystup2);
              if($zaz_vetsi["pocet_vetsich"]<>0) $sipka_P = "<img src = \"./images/sipka_P1.gif\">";
            }    */
            /* pokracovani ve vypisu zjistenych hodnot */
            $platnost_od = $zaz["akce_platnost_od"];
            $platnost_do = $zaz["akce_platnost_do"];
            if($mesic<>13)
            {
              $SQL = "select min(kp.datum_akce) platnost_od
                      from kalendar_prubeh kp
                      where id_akce='".$zaz["id_kal"]."' and
                            ( (year(kp.datum_akce)='$rok_2' and month(kp.datum_akce)<='8') or
                              (year(kp.datum_akce)='$rok' and month(kp.datum_akce)>='9') )
                            ";
              if(DB_select($SQL, $vystup1, $pocet))
                if($zaz_od = mysql_fetch_array($vystup1))
                  $platnost_od = $zaz_od["platnost_od"];

	      $SQL = "select max(kp.datum_akce) platnost_do
                      from kalendar_prubeh kp
                      where id_akce='".$zaz["id_kal"]."' and
                            ( (year(kp.datum_akce)='$rok_2' and month(kp.datum_akce)<='8') or
                              (year(kp.datum_akce)='$rok' and month(kp.datum_akce)>='9') )
                            ";
              if(DB_select($SQL, $vystup2, $pocet))
                if($zaz_do = mysql_fetch_array($vystup2))
                  $platnost_do = $zaz_do["platnost_do"];
            }
            $barva_text = "black";
            $i=0;
            $nasel=0;
            if($zaz["i"]==1) $barva_text = "#228822";
            else
              while($i<count($id) and $nasel==0)
              {
                if($id[$i] == $zaz["id_kal"])
                {
                  $barva_text = "red";
                  $nasel = 1;
                }
                $i++;
              }

            if($zaz["datum_akce_month"]<>$min_mes)
            {
              echo "<tr><td colspan=\"11\">&nbsp;</td></tr>";
              Zahlavi_radek(array(Mesic($zaz["datum_akce_month"])), "left", 11);
            /*  echo "<tr bgcolor=\"#d5d5d5\"><td>termín</td><td>zkratka</td><td>akce</td><td>studium</td></tr>";*/
              $radek = "0";
            }

            $radek++;
            if(($radek % 2)==1) $pozadi = "#e0e0e0";
            else $pozadi = "#efefef";
            echo "<tr bgcolor = $pozadi>";
            $cas_od = Cas($zaz["cas_od"]);
            $cas_do = Cas($zaz["cas_do"]);
            $hod_od = $zaz["hod_od"];
            $hod_do = $zaz["hod_do"];

            echo "<td><font color=\"$barva_text\">";
            echo Datum($platnost_od,0);

            if($platnost_od<>$platnost_do and $platnost_do<>"") echo " - ".Datum($platnost_do,0);
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

            echo "<td><font color=\"$barva_text\">";
            if($zaz["id_soub"]<>0) echo "<a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=5&p_nazev=".$zaz["nazev"]."&p_adresar=files_kalendar/".$zaz["nazev"]."\"><img src=\"./images/doc3.gif\" border=\"0\"></a>";
            echo "</font></td>";

            if($zaz["id_stand_akce"]=="1") echo "<td>&nbsp;</td><td><font color=\"$barva_text\">".$zaz["popis1"]."</td>";
            else
            {
              echo "<td><font color=\"$barva_text\"><b>".$zaz["zkratka"]."</b></td>";
              echo "<td>";
              if($zaz["popis1"]<>"") echo  "<font color=\"$barva_text\">".$zaz["popis1"]."</font>";
              echo "</td>";
            }


            echo "<td><font color=\"$barva_text\">";
            if($zaz["g"]=="1") echo "G";
            echo "</font></td>";
            echo "<td><font color=\"$barva_text\">";
            if($zaz["j"]=="1") echo "J©";
            echo "</font></td>";
            echo "<td><font color=\"$barva_text\">";
            if($zaz["u"]=="1") echo "U";
            echo "</font></td>";

            echo "<td><font color=\"$barva_text\">$studium</font></td>";

            echo "</tr>";
            $min_mes = $zaz["datum_akce_month"];
          }
          echo "</table>";
        }

      }

      break;
     /***  podrobny kalendar ****************************************************************************/
/***************************************************************************************************/
    case 2:
      /*Podnadpis("Podrobný kalendáø");*/
      //Tlacitka($kod, "plan_kal_vedeni.php", $pole_vyberu, $pole_tlacitek, 2);
      $SQL = "select skola from skupiny where id='$skupina'";
      if(DB_select($SQL, $vystup, $pocet)) if($zaznam=mysql_fetch_array($vystup)) $skola = $zaznam["skola"];

      if($rok=="")
      {
        $dnes_mesic = Date("m");
        $dnes_rok = Date("Y");
        if($dnes_mesic>="9" and $dnes_mesic<="12") $rok = $dnes_rok;
        if($dnes_mesic>="1" and $dnes_mesic<="8") $rok = $dnes_rok-1;
      }

      if($mesic=="")
      {
        $mesic = Date("m");
      }

      $SQL = "select year(min(datum_akce)) min_year, month(min(datum_akce)) min_month, year(max(datum_akce)) max_year, month(max(datum_akce)) max_month from kalendar_prubeh";
      $rok_min="";
      if(DB_select($SQL, $vystup, $pocet))
      {
        if($zaznam=mysql_fetch_array($vystup))
        {
          $min_year = $zaznam["min_year"];
          $year = $min_year;
          $min_month = $zaznam["min_month"];
          $max_year = $zaznam["max_year"];
          $max_month = $zaznam["max_month"];
          $predchozi_pol = 0;
          $posledni_pol = 0;
          if($min_month>=1 and $min_month<=8)
          {
            $pom_rok = $min_year-1;
            $skolni_rok[] = new CSkolniRok($pom_rok, $min_year);
          }
          if($max_month>=1 and $max_month<=8) $zahrn_1_pol = 1;

          while($year<$max_year)
          {
            $pom_rok = $year+1;
            $skolni_rok[] = new CSkolniRok($year, $pom_rok);
            $year++;
          }
          if($max_month>=9 and $max_month<=12)
          {
            $pom_rok = $max_year+1;
            $skolni_rok[] = new CSkolniRok($max_year, $pom_rok);

          }
        }
        echo "<form method=\"post\" action=\"plan_kal_vedeni.php?kod=$kod&vyber=$vyber\">";

    /*** blok k vyberu skolniho roku ***************************************************************/

        echo "©kolní rok <select name=\"rok\">";
        for($i=0;$i<count($skolni_rok);$i++)
        {
          $select = "";
          if($rok==$skolni_rok[$i]->rok1) $select="selected";
          echo "<option value=\"".$skolni_rok[$i]->rok1."\" $select>".$skolni_rok[$i]->rok1."/".$skolni_rok[$i]->rok2;
        }
        echo "</select>";
        $rok_dalsi=$rok_min+1;

    /*** blok k vyberu mesice ***********************************************************************/

        echo "&nbsp;&nbsp;&nbsp;Mìsíc <select name=\"mesic\">";
        echo "<option value=\"13\">(v¹echny)";
        for($i=1;$i<=12;$i++)
        {
          $select = "";
          if($mesic==$i) $select="selected";
          echo "<option value=\"$i\" $select>".Mesic($i);
        }
        echo "</select>";
        if($mesic<>"13") $podminka_mesic = " and month(kp.datum_akce)='$mesic'";


    /*** blok k vyberu studia *************************************************************************/

      if($vybrano)
        {
          switch($skola)
          {
            case "u":
                 $podminka = "0=1";
                 for($i=0;$i<4;$i++) $checked[$i] = "";
                 for($i=0;$i<count($stud);$i++)
                 {
                   switch($stud[$i])
                   {
                     case "g":
                          $podminka .= " or g = '1'";
                          $checked[0] = "checked";
                          break;
                     case "j":
                          $podminka .= " or j = '1'";
                          $checked[1] = "checked";
                          break;
                     case "u":
                          $podminka .= " or u = '1'";
                          $checked[2] = "checked";
                          break;
                     case "i":
                          $podminka .= " or i = '1'";
                          $checked[3] = "checked";
                          break;
                    }
                 }
                 break;
            case "g":
                 $podminka = "k.g = '1'";
                 $checked[0] = "checked";
                 break;
            case "j":
                 $podminka = "k.j = '1'";
                 $checked[1] = "checked";
                 break;
            case "u":
                 $podminka = "k.u = '1'";
                 $checked[2] = "checked";
                 break;
          }
        }
        else
        {
          switch($skola)
          {
            case "u":
                 $podminka = "k.g = '1' or k.j = '1' or k.u='1'";
                 if($prava<=2) $podminka .= " or k.i = '1'";
                 for($i=0;$i<5;$i++) $checked[$i] = "checked";
                 break;
            case "g":
                 $podminka = "k.g = '1'";
                 $checked[0] = "checked";
                 break;
            case "j":
                 $podminka = "k.j = '1'";
                 $checked[1] = "checked";
                 break;
          }
        }

        if($skola=="u")
        {
          echo "<p>Zobrazit pouze akce:";
          echo "<br><input type=\"checkbox\" name=\"stud[]\" $checked[0] value=\"g\"> gymnázia";
          echo "<br><input type=\"checkbox\" name=\"stud[]\" $checked[1] value=\"j\"> jazykové ¹koly";
          echo "<br><input type=\"checkbox\" name=\"stud[]\" $checked[2] value=\"u\"> uèitelù";
        }

        /*** vyber zobrazeni (vsechno nebo jenom aktualni) ******************************************************************************/
        if($zobrazit_vse=="vse") $checked="checked";
        echo "<p><input type=\"checkbox\" name=\"zobrazit_vse\" value=\"vse\" $checked>zobrazit i pøedchozí (ji¾ uskuteènìné) akce";

        echo "<p><input type=\"submit\" name=\"vybrano\" value=\"Vypsat akce\">";
        echo "</form>";
      }


    /*** zpracovani pozadavku *****************************************************************************************************/

      $SQL = "select id from kalendar where DATE_ADD(datum, INTERVAL 2 DAY)>=Now()";

      if(DB_select($SQL, $vyst, $pocet)) while($zaznam = mysql_fetch_array($vyst)) $id[] = $zaznam["id"];

      if($zobrazit_vse<>"vse") $podminka_zobrazeni = " and kp.datum_akce>=Now() ";
      else $podminka_zobrazeni = " and 1=1";

      $rok_2 = $rok + 1;
      $SQL = "select k.*, k.popis popis1, a.*, k.id_akce id_stand_akce, k.id id_kal, k.cas_od cas_od, k.cas_do cas_do, s.*, kp.*,
                     month(kp.datum_akce) datum_akce_month, a.nazev nazev_akce
              from kalendar k left join soubory s on s.id=k.id_soub, akce a, kalendar_prubeh kp
              where a.id = k.id_akce and
                    ( (year(kp.datum_akce)='$rok_2' and month(kp.datum_akce)<='8') or
                    (year(kp.datum_akce)='$rok' and month(kp.datum_akce)>='9') ) and
                    kp.id_akce = k.id and
                    ($podminka)
                    $podminka_mesic
                    $podminka_zobrazeni
              order by kp.datum_akce, k.cas_od, k.cas_do";
      if(DB_select($SQL, $vystup, $pocet))
      {
        $min_mes="";
        $min_den="";
        echo "<table border = \"0\" cellpadding=3 cellspacing = 0 width = \"100%\">";
        while($zaz = mysql_fetch_array($vystup))
        {
          $preskocit = 0;
          $aktualni_datum = Datum_den($zaz["datum_akce"],$dentydne,0);
          if($dentydne<>0 and $dentydne<>6)
          {
            $barva_text = "black";
            $i=0;
            $nasel=0;
            if($zaz["i"]==1) $barva_text = "#228822";
            else
              while($i<count($id) and $nasel==0)
              {
                if($id[$i] == $zaz["id_kal"])
                {
                  $barva_text = "red";
                  $nasel = 1;
                }
                $i++;
              }
  
            if($zaz["datum_akce_month"]<>$min_mes)
            {
              echo "<tr><td>&nbsp;</td></tr>";
              echo "<tr><td colspan=\"4\"><b><font size = \"4\" face=\"arial\">".Mesic($zaz["datum_akce_month"])."</font></b></td></tr>";
            /*  echo "<tr bgcolor=\"#d5d5d5\"><td>termín</td><td>zkratka</td><td>akce</td><td>studium</td></tr>";*/
            $radek = "0";
            }
            if($zaz["datum_akce"]<>$min_den)
              {
                echo "<tr><td>&nbsp;</td></tr>";
                Zahlavi_radek(array($aktualni_datum), "left", 11);
                echo "<tr><td colspan=\"11\" height=\"1\" ></td></tr>";
                $radek=0;
              }
  
            $radek++;
            if(($radek % 2)==1) $pozadi = "#e0e0e0";
            else $pozadi = "#efefef";
            echo "<tr bgcolor = $pozadi>";
            $cas_od = Cas($zaz["cas_od"]);
            $cas_do = Cas($zaz["cas_do"]);
            $hod_od = $zaz["hod_od"];
            $hod_do = $zaz["hod_do"];
            echo "<td><font color=\"$barva_text\">";
            if($cas_od<>"")
            {
              echo "&nbsp;&nbsp;$cas_od";
              if($cas_do<>"") echo "&nbsp;-&nbsp;$cas_do";
            }
             if($hod_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$hod_od.";
                if($hod_do<>"") echo " - $hod_do.";
                echo " h.";
              }
            echo "</font></td>";
  
            echo "<td><font color=\"$barva_text\">";
            if($zaz["id_soub"]<>0) echo "<a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=5&p_nazev=".$zaz["nazev"]."&p_adresar=files_kalendar/".$zaz["nazev"]."\"><img src=\"./images/doc3.gif\" border=\"0\"></a>";
            echo "</font></td>";
  
            echo "<td><font color=\"$barva_text\">";
            if($zaz["zkratky"]<>0) echo $zaz["zkratky"];
            echo "</font></td>";
  
            if($zaz["id_stand_akce"]=="1") echo "<td>&nbsp;</td><td><font color=\"$barva_text\">".$zaz["popis1"]."</td>";
            else
            {
              echo "<td><font color=\"$barva_text\"><b>".$zaz["zkratka"]."</b></td>";
              echo "<td><font color=\"$barva_text\">";
              if($zaz["popis1"]<>"") echo $zaz["popis1"];
              echo "</font></td>";
            }
  
            echo "<td><font color=\"$barva_text\">";
              if($zaz["i"]=="1") echo $zaz["zkratky"];
              echo "</font></td>";
              echo "<td><font color=\"$barva_text\">";
              if($zaz["g"]=="1") echo "GUB";
              echo "</font></td>";
              echo "<td><font color=\"$barva_text\">";
              if($zaz["j"]=="1") echo "J©";
              echo "</font></td>";
              echo "<td><font color=\"$barva_text\">";
              if($zaz["u"]=="1") echo "U";
              echo "</font></td>";
            echo "<td><font color=\"$barva_text\">$studium</font></td>";
            echo "</tr>";
          }  
          $min_mes = $zaz["datum_akce_month"];
          $min_den = $zaz["datum_akce"];
        }
        echo "</table>";
      }
      break; 


      /**********************************************************************************/
      /************************************ standardni akce **********************************************/
    case 3:
      /*Podnadpis("Seznam zkratek standardních akcí");*/
      //Tlacitka($kod, "plan_kal.php", $pole_vyberu, $pole_tlacitek, 2);
      $SQL = "select zkratka, nazev from akce order by zkratka";
      if(DB_select($SQL, $vyst, $poc))
      {
        echo "<table border=\"0\" cellpadding=10 cellspacing=0>";
        $barva = "bgcolor=\"#e0e0e0\"";
        $b = 0;
        $zaz=mysql_fetch_array($vyst);
        while($zaz=mysql_fetch_array($vyst))
        {
          echo "<tr $barva><td><b>".$zaz["zkratka"]."</b></td><td align=\"center\"> - </td><td>".$zaz["nazev"]."</td></tr>";
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
      }
      break;


  }
  Konec();


}

function Odmaz_BR($retezec)
{
  $retezec = "x".$retezec;
  while(strpos($retezec, "<br>")==1) $retezec = substr($retezec, 4, strlen($retezec)-4);
  while(substr($retezec, strlen($retezec)-4, 4)=="<br>") $retezec = substr($retezec, 0, strlen($retezec)-4);
  $retezec = substr($retezec, 1, strlen($retezec)-1);
  return $retezec;
}

function Zjisti_den($mes, $rok)
{
  $datum_vysl= mktime(0, 0, 0, $mes, 1, $rok);
  return Date("w", $datum_vysl);
}
?>
