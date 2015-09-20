<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 5, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Editace ulo¾ených souborù", $fullname, $kod);
  for($i=0;$i<=4;$i++) $selected[$i] = "";
  if($odeslano_edit_del) $id_soub="";
  if($id_soub=="")
  {
    if($odeslano_vymaz or $odeslano_edit_del)
    {
      $velikost = 0;
      $podminka_vs = " id_soub = '".$vymaz[0]."' ";
      $podminka_v = " id = '".$vymaz[0]."' ";
      for($i=1;$i<count($vymaz);$i++)
      {
        $podminka_vs .= " or id_soub = '".$vymaz[$i]."' ";
        $podminka_v .= " or id = '".$vymaz[$i]."' ";
      }
      $SQL = "select nazev, velikost from soubory where $podminka_v ";
        if(DB_select($SQL, $vystup, $pocet))
        {
          while($zaznam = MySQL_fetch_array($vystup))
          {
            $velikost += $zaznam["velikost"];
            if(!(unlink(c_files."files/".$login."/".$zaznam["nazev"])))
              echo "soubor ".$zaznam["nazev"]." se nepodaøilo odstranit";
          }
        }
      $SQL = "delete from soubory_skupiny where $podminka_vs";
      DB_exec($SQL);
      $SQL = "delete from soubory where $podminka_v";
      DB_exec($SQL);
    }
    $SQL = "      select distinct v.id id_soub, v.datum,
                         v.trida trida, v.predmet predmet, v.login_uc,
                         v.nazev, v.popis, v.velikost, v.typ, v.platnost_do,
                         u.jmeno jmeno, u.prijmeni prijmeni
                  from soubory_skupiny vs, soubory v,  ucitele u
                  where u.login = v.login_uc and
                        v.id = vs.id_soub and
                        v.login_uc = '$login' and
                        v.typ is null and
                        (v.platnost_do>=Now() or v.platnost_do='0000-00-00' or v.platnost_do is null)
                  order by v.datum desc ";
    if(DB_select($SQL, $vystup, $pocet))
      if($pocet>0)
      {
        echo "<P>Poèet záznamù: $pocet";
        echo "<form method=\"post\">";
        echo "<p><table border=0 cellspacing=0 cellpadding=5>";
        Zahlavi_radek(array("Editace", "Mazání", "Soubor", "Tøída", "Pøedmìt", "Velikost", "Datum", "Platnost do"), "left");
         $i=0;
        while($zaz=MySQL_fetch_array($vystup))
        {
          echo "<tr valign=\"top\">";
          echo "<td><a href=\"./soubory_editace.php?kod=$kod&id_soub=".$zaz["id_soub"]."\"><img src=\"./images/edit.gif\" border=none></a></td>";
          echo "<td><input type=\"checkbox\" name=\"vymaz[]\" value=\"".$zaz["id_soub"]."\"></td>";
          $p_adresar="files/".StrToLower($zaz["login_uc"])."/".$zaz["nazev"];
          $p_nazev=$zaz["nazev"];
          echo "<td><dl><dt><a href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=$p_nazev&p_adresar=$p_adresar\">".$zaz["nazev"]."</a></dt><dd><i><small>".$zaz["popis"]."</i></small></dd></dl></td>";
          echo "<td><small>".$zaz["trida"]."</small></td>";
          echo "<td><small>".$zaz["predmet"]."</small></td>";
          echo "<td><small>".Prevod($zaz["velikost"])."</small></td>";
          echo "<td><small>".Datum($zaz["datum"])."</small></td>";
          echo "<td><small>".Text_alter(Datum($zaz["platnost_do"], 0), "neuvedena")."</small></td>";
          echo "</tr>";
          $i++;
        }
        echo "<tr><td colspan=\"5\"><input type=\"submit\" name=\"odeslano_vymaz\" value=\"odstranit vybrané soubory\"></td></tr>";
        echo "</table>";
        echo "</form>";
      }
      else echo Text_alter("","<p>Neulo¾il(a) jste ¾ádný soubor.");
  }
  else
  {
    if($odeslano_edit)
      {
        $platnost_do = Datum_datab($platnost_do);
        $SQL = "update soubory set platnost_do = '$platnost_do' where id = '$id_soub'";
        DB_exec($SQL);
        $SQL = "update soubory set popis = '$popis' where id = '$id_soub'";
        DB_exec($SQL);
        if(count($prijemce)<>0)
        {
	  $SQL = "delete from soubory_skupiny where id_soub = '$id_soub'";
          DB_exec($SQL);
	  for($i=0; $i<count($prijemce); $i++)
          {
            $SQL = "insert into soubory_skupiny (id_skup, id_soub) values
                   ('".$prijemce[$i]."', '$id_soub')";
            DB_exec($SQL);
          }
        }
        else
        {
          if(count($vsichni_studenti[0])<>0)
          {
            $SQL = "delete from soubory_skupiny where id_soub = '$id_soub'";
            DB_exec($SQL);
            $SQL = "insert into soubory_skupiny (id_skup, id_soub) values ('-1',
                    '$id_soub')";
            DB_exec($SQL);
          }
          else
          {
            if(count($vsichni[0])<>0)
            {
              $SQL = "delete from soubory_skupiny where id_soub = '$id_soub'";
              DB_exec($SQL);
              $SQL = "insert into soubory_skupiny (id_skup, id_soub) values
                     ('-2', '$id_soub')";
              DB_exec($SQL);
            }
          }
        }
      }
    $SQL = "select *
            from soubory v, skupiny s, soubory_skupiny vs
            where v.id = '$id_soub' and
                  vs.id_soub = v.id and
                  s.id = vs.id_skup and
                  v.login_uc='$login'";
    if(DB_select($SQL, $vystup, $pocet))
    {
      if($zaz = MySQL_fetch_array($vystup))
      {
        echo "<form method=\"post\">";
        echo "<table>";
        echo "<tr><td><a href=\"./soubory_editace.php?kod=$kod\"><img src=\"./images/sipka.gif\" border=none><P></P></a></td></tr>";
        echo "<tr><td colspan=3><b><".c_font.">Skupiny u¾ivatelù, kterým je soubor urèen (roèník):</font></b>";
      echo "<br><font color=gray><small>- zvolíte-li <i>uèitel</i>, soubor se zobrazí i øediteli, zástupcùm a administrátorùm";
      echo "<br>- jednotlivé skupiny mají pøednost pøed volbou <i>v¹ichni</i> i <i>v¹ichni studenti</i>";
      echo "<br>- podobnì <i>v¹ichni studenti</i> má pøednost pøed volbou <i>v¹ichni</i>";
      echo "<br>- nezvolíte-li ¾ádnou skupinu, bude soubor viditelný v¹em</li></small></font></td></tr>";
        $SQL = "select * from skupiny where id<100 order by id";
        DB_select($SQL, $vystup, $pocet);
        $i=0;
        while($zaznam = MySQL_fetch_array($vystup))
        {
          $skup[$i] = new Cskupina($zaznam["id"], $zaznam["skupina"]);
          $i++;
        }
        for($i=0;$i<count($skup);$i++) $checked[$i] = "";
	$SQL = "select id_skup from soubory_skupiny where id_soub = '$id_soub'";
        DB_select($SQL, $vystup, $pocet);
        while($zaznam = MySQL_fetch_array($vystup))
          for($i=0; $i<count($skup); $i++)
            if($skup[$i]->id==$zaznam["id_skup"]) $checked[$i] = " checked";
        echo "<tr><td><br><input type=\"checkbox\" name=\"vsichni[]\" value=\"".$skup[0]->id."\" ".$checked[0].">".$skup[0]->skupina;
        echo "<br><input type=\"checkbox\" name=\"vsichni_studenti[]\" value=\"".$skup[1]->id."\" ".$checked[1].">".$skup[1]->skupina."</td></tr>";
        echo "<tr><td colspan=3><hr></td></tr>";
        echo "<tr><td valign=\"top\">";
        for($i=2; $i<count($skup); $i++)
        {
          if((($i-2)%9)==0 and $i<>2)
          {
            echo "</td><td valign=\"top\">";
            echo "<input type=\"checkbox\" name=\"prijemce[]\" value=\"".$skup[$i]->id."\" ".$checked[$i].">".$skup[$i]->skupina;
          }
          else if($i==2) echo "<input type=\"checkbox\" name=\"prijemce[]\" value=\"".$skup[$i]->id."\" ".$checked[$i].">".$skup[$i]->skupina;
               else echo "<br><input type=\"checkbox\" name=\"prijemce[]\" value=\"".$skup[$i]->id."\" ".$checked[$i].">".$skup[$i]->skupina;
        }
        echo "</td></tr></table>";
        echo "<p><table border=\"0\"><tr><td><b><".c_font.">Struèný popis souboru:</b>";
        echo "<tr><td><textarea name=\"popis\" rows=3 cols=20>".$zaz["popis"]."</textarea></td></tr>";
        echo "</table>";
        $platnost_do = Datum_bez_mezer($zaz["platnost_do"], 0);
        echo "<table border=\"0\"><p><tr><td><b><".c_font.">Platnost souboru do:</b>";
      	echo "<br><font color=gray><small>- datum pi¹te ve formátu <i>den.mìsíc.rok</i> bez mezer, rok na 4 èíslice</small></font>";
      	echo "<br><input type=\"text\" name=\"platnost_do\" value=\"$platnost_do\"></td></tr>";
        echo "</table>";
	echo "<table>";

        echo "<TR><td colspan=2>&nbsp;</td></tr>";
/*        echo "<tr><td width=\"150\"><b>Pøíjemce zprávy:</b></td><td><input type=\"text\" name=\"skupiny\" value=\"$skupiny\">".$zaz["trida"]."</td></tr>";*/
        echo "<tr><td width=\"150\"><b>Pro tøídy/skupiny:</b></td><td>".Text_alter($zaz["trida"], "neuvedeno")."</td></tr>";
        echo "<tr><td width=\"150\"><b>Interní název souboru:</b></td><td>".Text_alter($zaz["nazev"], "neuveden")."</td></tr>";
        echo "<tr><td colspan=2>&nbsp;</td></tr>";
        echo "<input type=\"hidden\" name=\"vymaz[0]\" value=\"$id_soub\">";
        echo "<tr><td colspan=2><input type=\"submit\" name=\"odeslano_edit\" value=\"potvrdit zmìny\">&nbsp;<input type=\"submit\" name=\"odeslano_edit_del\" value=\"odstranit soubor\"></td></tr>";
	echo "</table>";
        echo "<P>";

      }
    }
  }
  Konec();
}
?>
