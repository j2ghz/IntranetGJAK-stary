<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 4, $fullname, $login, $chyba))
{
  NoCACHE();
  
  $pole_tlacitek = array("Nová<br>akce", "Editace<br>akcí", "Standardní<br>akce");
  $pole_vyberu = array("vyber=1", "vyber=2", "vyber=3");
  if(!($vyber)) $vyber=1;
  Hlavicka("Editace kalendáøe akcí", $fullname, $kod, "plan_kal_editace.php", $pole_vyberu, $pole_tlacitek, $vyber);
    /* definice indexu poli - d-den, m-mesic, r-rok*/
  define(d,"2");
  define(m,"1");
  define(r,"0");


  switch($vyber)
  {
    case 1:
      /*Podnadpis("Vlo¾ení nové akce");*/
      //Tlacitka($kod, "plan_kal_editace.php", $pole_vyberu, $pole_tlacitek,1);

      echo "<center>".Hlaska($chyba, "Akci se nepodaøilo vlo¾it do kalendáøe", "Akce byla úspì¹nì vlo¾ena do kalendáøe")."</center>";
      if($chyba<>"ok")
      {

        echo "<form action=\"./plan_kal_send.php?kod=$kod&vyber=$vyber\" method=post enctype=\"multipart/form-data\">";

        echo "<p><table border=0>";
        echo "<tr><td colspan=2><b><".c_font.">Akce se týká:</b></td></tr>";
        echo "<tr><td width=\"10\"><input type=\"checkbox\" name=\"stud[]\" value=\"g\"></td><td>gymnázia</td></tr>";
        echo "<tr><td width=\"10\"><input type=\"checkbox\" name=\"stud[]\" value=\"j\"></td><td>jazykové ¹koly</td></tr>";
        echo "<tr><td width=\"10\"><input type=\"checkbox\" name=\"stud[]\" value=\"u\"></td><td>pouze uèitelù</td></tr>";
        echo "</table>";

        echo "<p><table border=0><tr><td><b><".c_font.">Datum konání akce:</b>";
        echo "<br><font color=gray><small>- datum pi¹te ve formátu <i>den.mìsíc.rok</i>, rok na 4 èíslice<br>";
        echo "- jedná-li se o jednodenní akci, vyplòte pouze jedno datum (libovolné)<br>";
        echo "- vyplníte-li pouze jedno datum, jsou políèka \"Od\" a \"Do\" rovnocenná</small></font>";
        echo "<br>Od: <input type=\"text\" name=\"platnost_od\" value=\"$platnost_od\">";
        echo "<br>Do: <input type=\"text\" name=\"platnost_do\" value=\"$platnost_do\"></td></tr>";
        echo "</table>";

        echo "<p><table border=0><tr><td><b><".c_font.">Èas konání akce:</b>";
        echo "<br><font color=gray><small>- èasový údaj je vhodný zejména u jednodenních akcí<br></small></font>";
        echo "<br>Od: <input type=\"text\" name=\"cas_od\" value=\"$cas_od\">";
        echo "<br>Do: <input type=\"text\" name=\"cas_do\" value=\"$cas_do\"></td></tr>";
        echo "</table>";

        echo "<p><table border=0><tr><td><b><".c_font.">Standardní akce:</b>";
        $SQL = "select * from akce where id>1 order by zkratka";
        if(DB_select($SQL, $vystup, $pocet))
        {
          echo "<tr><td><select name=\"id_akce\">";
          echo "<option value=\"1\">(není)";
          while($zaz=mysql_fetch_array($vystup))
          {
            echo "<option value=\"".$zaz["id"]."\">".$zaz["zkratka"]." - ".$zaz["nazev"];
          }
          echo "</select></td></tr></table>";
        }

        echo "<p><table border=0><tr><td><b><".c_font.">Popis akce:</b>";
        echo "<tr><td><textarea name=\"popis\" value=\"$popis\" rows=3 cols=20></textarea></td></tr></table>";

        echo "<p><table border=0><tr><td><b><".c_font.">Pøilo¾ený soubor:</td></tr>";
        echo "<tr><td><input type=\"file\" name=\"soubor\" value=\"$soubor\"></td></tr></table>";

        echo "<p><table border=0><tr><td><b><".c_font.">Nový název souboru:</b>";
        echo "<br><font color=gray><small>- nový název souboru nesmí být prázdný a mù¾e obsahovat pouze tyto znaky:<br>a-z, A-Z, 0-9, ., _, -,
             <br>tj. nesmíte pou¾ívat diakritiku a mezery (napø. namísto \"vzorové pøíklady\" pi¹te \"vzorove_priklady\")</small></td></tr>";
        echo "<tr><td><input type=\"textbox\" name=\"nazev\" value=\"$nazev\"></td></tr></table>";

        echo "<p><table border=0><tr><td><P>&nbsp;</P><input type = \"submit\" value=\"odeslat\" name=\"odeslano\"></td></tr>";
        echo "</table></form>";
      }
      break;
/**********************************************************************************************************/
    case 2:
      /*Podnadpis("Editace akcí");*/
      //Tlacitka($kod, "plan_kal_editace.php", $pole_vyberu, $pole_tlacitek,2);
      if(count($vymaz)<>0)
      {
        $zakazano = 0;
	for($i=0;$i<count($vymaz);$i++)
        {
          $SQL = "select s.nazev, k.id_soub from kalendar k, soubory s where s.id = k.id_soub and k.id = '".$vymaz[$i]."'";
          if(DB_select($SQL, $vystup, $pocet))
          {
            if($zaznam=mysql_fetch_array($vystup))
            {
              if(!(unlink(c_files."files_kalendar/".$zaznam["nazev"])))
              {
                $zakazano = 1;
                echo "soubor ".$zaznam["nazev"]." se nepodaøilo odstranit";
              }
              else
              {
                $SQL = "delete from soubory where id = '".$zaznam["id_soub"]."'";
                DB_exec($SQL);
	      }
            }
          }
          if(zakazano<>1)
          {
            $SQL = "delete from kalendar where id = '".$vymaz[$i]."'";
            DB_exec($SQL);
            $SQL = "delete from kalendar_prubeh where id_akce = '".$vymaz[$i]."'";
            DB_exec($SQL);
          }
        }
      }

      if($odeslano_editace<>"")
      {
        if($platnost_od=="" or $platnost_od=="0000-00-00") $chyba .= "<li>chybí datum konání akce</li>";
        else
        {
          $SQL = "delete from kalendar_prubeh where id_akce = '$id_akce'";
          DB_exec($SQL);
          if(($platnost_od=="")and($platnost_do<>""))
          {
            $platnost_od = $platnost_do;
            $platnost_do = "";
            echo "<br> vymenuju datumy";
          }

          if(($cas_od=="")and($cas_do<>""))
          {
            $cas_od = $cas_do;
            $cas_do = "";
          }
          $platnost_od = Datum_datab($platnost_od);
          $platnost_do = Datum_datab($platnost_do);
          $cas_od = Cas_datab($cas_od);
          $cas_do = Cas_datab($cas_do);

          if($platnost_do<>"" and $platnost_do<>"0000-00-00" and $platnost_do<>$platnost_od)
          {
            $platnost_do = day_add($platnost_do);
            while($platnost_od<>$platnost_do)
              {

                $SQL = "insert into kalendar_prubeh (id_akce, datum_akce, cas_od, cas_do) values
                        ('$id_akce', '$platnost_od', '$cas_od', '$cas_do')";
                DB_insert($SQL, $id);
                $pomocna = day_add($platnost_od);
                $platnost_od = $pomocna;
              }
          }
          else
          {
            $SQL = "insert into kalendar_prubeh (id_akce, datum_akce, cas_od, cas_do) values
                        ('$id_akce', '$platnost_od', '$cas_od', '$cas_do')";
            DB_insert($SQL, $id);
          }
          $g = "0";
          $j = "0";
          $u = "0";
          for($i=0;$i<4;$i++)
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
          $SQL = "update kalendar set popis = '$popis', id_akce = '$id_stand_akce',
                                      g = '$g', j = '$j', u = '$u', datum = Now()
                  where id = '$id_akce'";
          DB_exec($SQL);
          $chyba = "ok";
        }
      }
      else if($odeslano_soubor<>"")
      {
        if($soubor<>"")
        {
          $SQL = "select s.nazev, k.id_soub from kalendar k, soubory s where s.id = k.id_soub and k.id = '$id_akce'";
          if(DB_select($SQL, $vystup, $pocet))
          {
            if($zaznam=mysql_fetch_array($vystup))
            {
              if($pocet<>0 and $zaznam["id_soub"]<>0)
              {
                if(!(unlink(c_files."files_kalendar/".$zaznam["nazev"])))
                     echo "soubor ".$zaznam["nazev"]." se nepodaøilo odstranit";
                $SQL = "delete from soubory where id = '".$zaznam["id_soub"]."'";
                DB_exec($SQL);
                $SQL = "update kalendar set id_soub = '0' where id = '$id_akce'";
                DB_exec($SQL);
              }
            }
            if($soubor_size<>0)
            {
              if(StrSpn(StrToLower($nazev),
                 "abcdefghijklmnopqrstuvwxyz0123456789-_.")<>StrLen($nazev) or
                 $nazev=="")
                 $chyba .= "<li>nový název pøilo¾eného souboru nesmí být prázdný a mù¾e obsahovat pouze tyto znaky: <br> a-z, A-Z, 0-9, ., _, - </li>";
              else
              {
                $pom = explode(".", $nazev);
                if($pom[1]=="")
                {
                  $pom2 = explode(".", $soubor_name);
                  $nazev .= ".".$pom2[count($pom2)-1];
                }
                $novy = c_files."files_kalendar/$nazev";
                if(file_exists($novy)) $chyba .= "<li>soubor se stejným názvem jste u¾ v minulosti ulo¾il(a)</li>";
                else
                {
                  Copy($soubor, $novy);
                  $velikost = $soubor_size;
                  $platnost_do = Datum_datab($platnost_do);
                  $SQL = "insert into soubory (login_uc, nazev, popis, velikost, trida,
                          predmet, datum, typ)
                          values ('$login', '$nazev', '$popis', '$velikost', '$trida', '$predmet', Now(), 'akce')";
                  DB_insert($SQL, $id_soub);
                }
                $SQL = "update kalendar set id_soub='$id_soub' where id = '$id_akce'";
                DB_exec($SQL);
              }
            }
          }
        }
      }
      else if($odstranit_soubor<>"")
      {
        $SQL = "select s.nazev, k.id_soub from kalendar k, soubory s where s.id = k.id_soub and k.id = '$id_akce'";
        if(DB_select($SQL, $vystup, $pocet))
        {
          if($zaznam=mysql_fetch_array($vystup))
          {
            if(!(unlink(c_files."files_kalendar/".$zaznam["nazev"])))
                 echo "soubor ".$zaznam["nazev"]." se nepodaøilo odstranit";
            $SQL = "delete from soubory where id = '".$zaznam["id_soub"]."'";
            DB_exec($SQL);
            $SQL = "update kalendar set id_soub = '0' where id = '$id_akce'";
            DB_exec($SQL);
          }
        }
      }

      if($id_akce<>"")
      {
        echo "<p><table border=\"1\"><tr><td bgcolor=\"efefef\">
               Formuláø je rozdìlen na dva bloky - editace akce a editace pøilo¾eného informaèního souboru
               (logicky oddìlené tlaèítky).
               <br>Ka¾dou èást musíte mìnit samostatnì, tj. napø.
               zmìníte-li datum zahájení akce, musíte pøed výbìrem opraveného souboru stisknout tlaèítko
               <u>zmìnit údaje</u>. Zapomenete-li tlaèítko stisknout, vyberete soubor a ode¹lete jej, zmìna datumu se neprojeví.
               Ka¾dé tlaèítko má svou funkci a pøíslu¹í pouze jedné èásti formuláøe.</td></tr></table>";
        $SQL = "select k.*, a.id id_stand_akce, a.*,
                       max(kp.datum_akce) akce_platnost_do, min(kp.datum_akce) akce_platnost_od, kp.*,
                       s.nazev nazev_soub
                from kalendar k, kalendar_prubeh kp, akce a left join soubory s on s.id = k.id_soub
                where kp.id_akce = k.id and
                      k.id = '$id_akce' and   
                      a.id = k.id_akce
                group by kp.id_akce";
        if(DB_select($SQL, $vystup, $pocet))
        {
          if($zaznam = mysql_fetch_array($vystup))
          {
            $checked[0]="";$checked[1]="";$checked[2]="";
            if($zaznam["g"]==1) $checked[0] = "checked";
            if($zaznam["j"]==1) $checked[1] = "checked";
            if($zaznam["u"]==1) $checked[2] = "checked";

            $platnost_od = Datum_bez_mezer($zaznam["akce_platnost_od"],0);
            if($zaznam["akce_platnost_od"]<>$zaznam["akce_platnost_do"]) $platnost_do = Datum_bez_mezer($zaznam["akce_platnost_do"],0);
            /*if($zaznam["cas_od"]<>"00:00:00") $cas_od = Cas($zaznam["cas_od"]);
            if($zaznam["cas_do"]<>"00:00:00") $cas_do = Cas($zaznam["cas_do"]);*/
            $cas_od = Cas($zaznam["cas_od"]);
            $cas_do = Cas($zaznam["cas_do"]);
            $stand_akce = $zaznam["id_stand_akce"];
            $chybi_soub = 0;
            if($zaznam["nazev_soub"]=="") $chybi_soub = 1;
            $soubor_nazev = Text_alter($zaznam["nazev_soub"], "soubor nebyl pøilo¾en");
            $popis = $zaznam["popis"];
          }

          echo "<center>".Hlaska($chyba, "Akci se nepodaøilo v databázi zmìnit", "Akce byla úspì¹nì zmìnìna")."</center>";
          if($chyba<>"ok")
          {

            echo "<form action=\"./plan_kal_editace.php?kod=$kod&vyber=$vyber&id_akce=$id_akce\" method=post enctype=\"multipart/form-data\">";

            echo "<p><table border=0>";
            echo "<tr><td colspan=2><b><".c_font.">Akce se týká:</b></td></tr>";
            echo "<tr><td width=\"10\"><input type=\"checkbox\" name=\"stud[]\" value=\"g\"></td><td>gymnázia</td></tr>";
            echo "<tr><td width=\"10\"><input type=\"checkbox\" name=\"stud[]\" value=\"j\"></td><td>jazykové ¹koly</td></tr>";
            echo "<tr><td width=\"10\"><input type=\"checkbox\" name=\"stud[]\" value=\"u\"></td><td>pouze uèitelù</td></tr>";
            echo "</table>";

            echo "<p><table border=0><tr><td><b><".c_font.">Datum konání akce:</b>";
            echo "<br><font color=gray><small>- datum pi¹te ve formátu <i>den.mìsíc.rok</i>, rok na 4 èíslice<br>";
            echo "- jedná-li se o jednodenní akci, vyplòte pouze jedno datum (libovolné)<br>";
            echo "- vyplníte-li pouze jedno datum, jsou políèka \"Od\" a \"Do\" rovnocenná</small></font>";
            echo "<br>Od: <input type=\"text\" name=\"platnost_od\" value=\"$platnost_od\">";
            echo "<br>Do: <input type=\"text\" name=\"platnost_do\" value=\"$platnost_do\"></td></tr>";
            echo "</table>";

            echo "<p><table border=0><tr><td><b><".c_font.">Èas konání akce:</b>";
            echo "<br><font color=gray><small>- èasový údaj je vhodný zejména u jednodenních akcí<br></small></font>";
            echo "<br>Od: <input type=\"text\" name=\"cas_od\" value=\"$cas_od\">";
            echo "<br>Do: <input type=\"text\" name=\"cas_do\" value=\"$cas_do\"></td></tr>";
            echo "</table>";

            echo "<p><table border=0><tr><td><b><".c_font.">Standardní akce:</b>";
            $SQL = "select * from akce where id>1 order by zkratka";
            if(DB_select($SQL, $vystup, $pocet))
            {
              $select="";
              echo "<tr><td><select name=\"id_stand_akce\">";
              if($stand_akce==1) $select = "selected";
              echo "<option value=\"1\" ".$select.">(není)";
              while($zaz=mysql_fetch_array($vystup))
              {
                $select = "";
                if($stand_akce==$zaz["id"]) $select = "selected";
                echo "<option value=\"".$zaz["id"]."\" ".$select.">".$zaz["zkratka"]." - ".$zaz["nazev"];
              }
              echo "</select></td></tr></table>";
            }

            echo "<p><table border=0><tr><td><b><".c_font.">Popis akce:</b>";
            echo "<tr><td><textarea name=\"popis\" rows=3 cols=20>$popis</textarea></td></tr></table>";

            echo "<p><table border=0><tr><td><input type = \"submit\" value=\"zmìnit údaje\" name=\"odeslano_editace\"></td></tr>";
            echo "</table></form>";



            echo "<p>&nbsp;</p>";
            echo "<form action=\"./plan_kal_editace.php?kod=$kod&vyber=$vyber&id_akce=$id_akce\" method=post enctype=\"multipart/form-data\">";
            echo "<p><table border=0><tr><td><b><".c_font.">Pøilo¾ený soubor: </b><big>";
            if($chybi_soub==0)
            {
              echo "<a href=\"sendfile.php?kod=$kod&p_prava=5&p_nazev=$soubor_nazev&p_adresar=files_kalendar/".$soubor_nazev."\">".$soubor_nazev."</a>";
              $nazev = $soubor_nazev;
            }
            else
            {
              echo $soubor_nazev;
            }
            echo "</big></td></tr></table>";

            echo "<p><table border=0><tr><td><b><".c_font.">Novì pøilo¾ený soubor:</td></tr>";
            echo "<tr><td><input type=\"file\" name=\"soubor\" value=\"$soubor\"></td></tr></table>";

            echo "<p><table border=0><tr><td><b><".c_font.">Nový název souboru:</b>";
            echo "<br><font color=gray><small>- nový název souboru nesmí být prázdný a mù¾e obsahovat pouze tyto znaky:<br>a-z, A-Z, 0-9, ., _, -,
                 <br>tj. nesmíte pou¾ívat diakritiku a mezery (napø. namísto \"vzorové pøíklady\" pi¹te \"vzorove_priklady\")</small></td></tr>";
            echo "<tr><td><input type=\"textbox\" name=\"nazev\" value = \"$nazev\"></td></tr></table>";

            echo "<p><table border=0><tr><td><input type = \"submit\" value=\"ulo¾it soubor\" name=\"odeslano_soubor\"></td></tr>";
            echo "</table></form>";
            echo "<form action=\"./plan_kal_editace.php?kod=$kod&vyber=$vyber&id_akce=$id_akce\" method=post enctype=\"multipart/form-data\">";
            echo "<p>&nbsp;</p><p><b>Stisknete-li následující tlaèítko, odstraníte pøilo¾ený soubor:</b>";
            echo "<p><input type = \"submit\" value=\"odstranit soubor\" name=\"odstranit_soubor\">";
            echo "</form>";
          }
        }
      }
      else
      {
        class CskolniRok
        {
          var $rok1, $rok2;

          function CskolniRok($rok1, $rok2)
          {
          $this -> rok1 = $rok1;
          $this -> rok2 = $rok2;
          }

        }
        if($rok=="")
        {
          $dnes_mesic = Date("m");
          $dnes_rok = Date("Y");
          if($dnes_mesic>="9" and $dnes_mesic<="12") $rok = $dnes_rok;
          if($dnes_mesic>="1" and $dnes_mesic<="8") $rok = $dnes_rok-1;
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
          echo "<form action=\"plan_kal_editace.php?kod=$kod&vyber=$vyber\" method=\"post\">";

  /*** vyber roku **********************************************************************************/
          echo "©kolní rok <select name=\"rok\">";
          for($i=0;$i<count($skolni_rok);$i++)
          {
            $select = "";
            if($rok==$skolni_rok[$i]->rok1) $select="selected";
            echo "<option value=\"".$skolni_rok[$i]->rok1."\" $select>".$skolni_rok[$i]->rok1."/".$skolni_rok[$i]->rok2;
          }
          echo "</select>";

  /*** vyber studia ******************************************************************************/

          if($vybrano)
          {
            $podminka = "0=1";
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
                case "i":    $podminka .= " or i = '1'";
                             $checked[3] = "checked";
                             break;
              }
            }
          }
          else
          {
            $podminka = "g = '1' or j = '1' or u = '1'";
            for($i=0;$i<4;$i++) $checked[$i] = "checked";
          }

            echo "<p>Zobrazit pouze akce:";
            echo "<br><input type=\"checkbox\" name=\"stud[]\" value=\"g\" $checked[0]> gymnázia";
            echo "<br><input type=\"checkbox\" name=\"stud[]\" value=\"j\" $checked[1]> jazykové ¹koly";
            echo "<br><input type=\"checkbox\" name=\"stud[]\" value=\"u\" $checked[2]> uèitelù";
            if($prava<=2) echo "<br><input type=\"checkbox\" name=\"stud[]\" value=\"i\" $checked[3]> interní";
            echo "<p><input type=\"submit\" name=\"vybrano\" value=\"Vypsat akce\">";
            echo "</form>";
            $rok_dalsi=$rok_min+1;
          }

          $rok_2 = $rok + 1;
          $SQL1 = "select k.*, k.popis as popis1, a.*, min(kp.datum_akce) akce_platnost_od, max(kp.datum_akce) akce_platnost_do, k.id_akce id_stand_akce, k.id id_kal, s.*, kp.*, month(min(kp.datum_akce)) datum_akce_month
                  from kalendar k left join soubory s on s.id=k.id_soub, akce a, kalendar_prubeh kp
                  where a.id = k.id_akce and
                        ( (year(kp.datum_akce)='$rok_2' and month(kp.datum_akce)<='8') or
                        (year(kp.datum_akce)='$rok' and month(kp.datum_akce)>='9') ) and
                        kp.id_akce = k.id and
                        kp.datum_akce>=Now() and
                        ($podminka)
                  group by kp.id_akce
                  order by akce_platnost_od, kp.cas_od, kp.cas_do";
          if(DB_select($SQL1, $vystup1, $pocet1))
          {
            $min_mes="";
            $min_den="";
            echo "<form \"./plan_kal_editace.php?kod=$kod&vyber=$vyber\" method=post>";
            echo "<table border = \"0\" cellpadding=3 cellspacing = 0 width = \"100%\">";
           /* echo "<tr><td>termín</td><td>zkratka</td><td>akce</td><td>studium</td><td>podrobnosti</td></tr>";
            echo "<tr><td colspan=\"5\"><hr></td></tr>";*/
            while($zaz1 = mysql_fetch_array($vystup1))
            {
              $i=0;
              $nasel=0;
              if($zaz1["datum_akce_month"]<>$min_mes)
              {
                echo "<tr><td colspan=\"11\">&nbsp;</td></tr>";
                echo "<tr><td colspan=\"11\"><b><font size = \"5\">".Mesic($zaz1["datum_akce_month"])."</font></b></td></tr>";
              /*  echo "<tr bgcolor=\"#d5d5d5\"><td>termín</td><td>zkratka</td><td>akce</td><td>studium</td></tr>";*/
                $radek = "0";
              }

              $radek++;
              if(($radek%2)==1) $pozadi = "#e0e0e0";
              else $pozadi = "#efefef";
              echo "<tr bgcolor = $pozadi>";
              $cas_od = Cas($zaz1["cas_od"]);
              $cas_do = Cas($zaz1["cas_do"]);

              echo "<td width=\"1\"><a href=\"./plan_kal_editace.php?kod=$kod&vyber=2&odeslano_akce=1&id_akce=".$zaz1["id_kal"]."\"><img src=\"./images/edit.gif\" border=none></a></td>";
              echo "<td width=\"1\"><input type=\"checkbox\" name=\"vymaz[]\" value=\"".$zaz1["id_kal"]."\"></td>";
              echo "<td><font color=\"$barva_text\">";
              echo Datum($zaz1["akce_platnost_od"],0);
              if($zaz1["akce_platnost_od"]<>$zaz1["akce_platnost_do"] and $zaz1["akce_platnost_do"]<>"") echo " - ".Datum($zaz1["akce_platnost_do"],0);
              if($cas_od<>"")
              {
                echo "&nbsp;&nbsp;&nbsp;$cas_od";
                if($cas_do<>"") echo " - $cas_do";
              }
              echo "</font></td>";

              echo "<td><font color=\"$barva_text\">";
              if($zaz1["id_soub"]<>0) echo "<a href=\"sendfile.php?kod=$kod&p_prava=5&p_nazev=".$zaz1["nazev"]."&p_adresar=files_kalendar/".$zaz1["nazev"]."\"><img src=\"./images/doc3.gif\" border=\"0\"></a>";
              echo "</font></td>";

              echo "<td>";
              if($zaz1["zkratky"]<>0) echo $zaz1["zkratky"];
              echo "</font></td>";

              if($zaz1["id_stand_akce"]=="1") echo "<td>&nbsp;</td><td><font color=\"$barva_text\">".$zaz1["popis"]."</td>";
              else
              {
                echo "<td><font color=\"$barva_text\"><b>".$zaz1["zkratka"]."</b></td>";
                echo "<td>";
                if($zaz1["popis1"]<>"") echo  "<font color=\"$barva_text\">".$zaz1["popis1"]."</font>";
                echo "</td>";
              }

              echo "<td><font color=\"$barva_text\">";
              if($zaz1["g"]=="1") echo "G";
              echo "</font></td>";
              echo "<td><font color=\"$barva_text\">";
              if($zaz1["j"]=="1") echo "J©";
              echo "</font></td>";
              echo "<td><font color=\"$barva_text\">";
              if($zaz1["u"]=="1") echo "U";
              echo "</font></td>";
              echo "<td><font color=\"$barva_text\">$studium</font></td>";

              echo "</tr>";
              $min_mes = $zaz1["datum_akce_month"];
            }
            echo "<tr><td colspan=\"11\">&nbsp;</td></tr>";
            echo "<tr><td colspan=\"11\"><input type=\"submit\" name=\"odeslano_vymaz\" value=\"vymazat vybrané akce\"></td></tr>";
            echo "</table>";
            echo "</form>";
          }
        }
      break;
/**********************************************************************************************************/
    case 3:
      /*Podnadpis("Editace standardních akcí");*/
      //Tlacitka($kod, "plan_kal_editace.php", $pole_vyberu, $pole_tlacitek,3);
      if($odeslano_vymaz)
      {
        for($i=0;$i<count($vymaz);$i++)
        {
          $SQL = "delete from akce where id='".$vymaz[$i]."'";
          DB_exec($SQL);
        }
      }
      else
      {
        if($odeslano_new)
        {
          $zkratka = strtoupper($zkratka);
          $SQL = "select * from akce where zkratka = '$zkratka'";
          if(DB_select($SQL, $vystup, $pocet))
          {
            if($pocet<>0)
            {
              echo Hlaska("<li>Akce s touto zkratkou u¾ v databázi existuje.<br>Zvolte jinou zkratku, nebo nejdøíve pùvodní akci odstraòte.</li>","Akce nebyla ulo¾ena do databáze","");
            }
            else
            {
              $SQL = "insert into akce (zkratka, nazev) values ('$zkratka', '$nazev')";
              DB_exec($SQL);
            }
          }
        }
      }
      $SQL = "select * from akce where id>'1' order by zkratka";
      if(DB_select($SQL, $vystup, $pocet))
      {
        $SQL2 = "select a.id from akce a, kalendar k where a.id>'1' and k.id_akce=a.id order by a.zkratka";
        if(DB_select($SQL2, $vystup2, $pocet2))
        {
          while($zaznam=mysql_fetch_array($vystup2)) $pouzite_akce[] = $zaznam["id"];
        }
        echo "<form action=\"plan_kal_editace.php?kod=$kod&vyber=$vyber\" method=\"post\">";
        echo "<table border=0 cellspacing=2 cellpadding=5><tr>";
        echo "<td colspan=\"5\">Zkratka: <input type=\"text\" value=\"$zkratka\" name=\"zkratka\">";
        echo "&nbsp;&nbsp;&nbsp;Název: <input type=\"text\" value=\"$nazev\" name=\"nazev\"></td></tr>";
        echo "<tr><td colspan=\"4\"><input type=\"submit\" name=\"odeslano_new\" value=\"pøidat akci\"></td></tr>";
        echo "</form>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<form action=\"plan_kal_editace.php?kod=$kod&vyber=$vyber\" method=\"post\">
              <tr bgcolor=\"#dddddd\" align=\"center\">
              <td><b><center>mazání</center></b></td>
              <td><center><b>zkratka</b></center></td>
              <td><center><b>název</b></center></td></tr>";
        while($zaz=mysql_fetch_array($vystup))
        {
          $i = 0; $nasel = 0;
          while($i<count($pouzite_akce) and $nasel<>1)
          {
            if($pouzite_akce[$i]==$zaz["id"]) $nasel = 1;
            $i++;
          }
          if($nasel==0) echo "<tr valign=\"top\"><td><input type=\"checkbox\" name=\"vymaz[]\" value=\"".$zaz["id"]."\"></td>";
          else echo "<td>".Text_alter("", "nelze")."</td>";
          echo "<td>".$zaz["zkratka"]."</td>";
          echo "<td>".$zaz["nazev"]."</td></tr>";
        }
        echo "<tr><td colspan=\"4\"><input type=\"submit\" name=\"odeslano_vymaz\" value=\"odstranit vybrané akce\"></td></tr></table></form>";

      }
      break;
  }
  Konec();
}

/* funkce pro praci s datem a casem*/
/* pracuje s datumem ve formatu dd.mm.rrrr */
function day_add($datum)
{
 /* define(d,"2");
  define(m,"1");
  define(r,"0");   */
  $pom = explode("-", $datum);
  $datum_vysl= mktime(1, 0, 0, $pom[m], $pom[d], $pom[r]) + 86400;
  return Date("Y-m-d", $datum_vysl);
}

?>
