<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 2, $fullname, $login, $chyba))
{
  NoCACHE();
  $pole_tlacitek = array("V¹echny<br>soubory", "Podle<br>u¾ivatelù",  "Pøehled");
  $pole_vyberu = array("vyber=1", "vyber=2", "vyber=3");

  if(!($vyber)) $vyber=1;
  Hlavicka("Soubory podle uèitelù", $fullname, $kod, "soubory_vsechny.php", $pole_vyberu, $pole_tlacitek, $vyber);
  switch($vyber)
  {
/**** podobne jako v prehledu vsech zprav muzeme kontrolovat vsechny soubory a mazat neplatne */
    case 1:
      /*Podnadpis("Zobrazení v±ech souborù");*/
      //Tlacitka($kod, "soubory_vsechny.php", $pole_vyberu, $pole_tlacitek,1);
      for($i=0;$i<=4;$i++) $selected[$i] = "";
      switch($vyber)
      {
        case 0:
             $razeni = "v.datum desc";
             $selected[0] = "selected";
             break;
        case 1:
             $razeni = "v.trida";
             $selected[1] = "selected";
             break;
        case 2:
             $razeni = "v.predmet";
             $selected[2] = "selected";
             break;
        case 3:
             $razeni = "u.prijmeni, u.jmeno";
             $selected[3] = "selected";
             break;
        case 4:
             $razeni = "v.platnost_do";
             $selected[4] = "selected";
             break;
        default:
             $razeni = "v.datum desc";
             $selected[0] = "selected";
        break;
      }

    if($odeslano_promazat)
    {
      $SQL = "select id, nazev, login_uc from soubory
              where (platnost_do<Now() and platnost_do<>'0000-00-00' and platnost_do is not null)";
      if(DB_select($SQL, $vystup, $pocet))
      {
        while($zaznam = MySQL_fetch_array($vystup))
        {
          if(!(unlink(c_files."files/".$zaznam["login_uc"]."/".$zaznam["nazev"])))
             echo "soubor ".c_files."files/".$zaznam["login_uc"]."/".$zaznam["nazev"]." se nepodaøilo odstranit";
          else
          {
            $SQL_vs = "delete from soubory_skupiny where id_soub='".$zaznam["id"]."'";
            DB_exec($SQL_vs);
            $SQL_v = "delete from soubory where id='".$zaznam["id"]."'";
            DB_exec($SQL_v);
          }
        }
      }
    }
    $SQL_neplatne = "select platnost_do from soubory where platnost_do<Now() and platnost_do<>'0000-00-00' and platnost_do is not null and typ is null";
    if(DB_select($SQL_neplatne, $vystup, $pocet_neplatnych))
    {
      while($zaz = MySQL_fetch_array($vystup))
      {
        $neplatne[] = $zaz["platnost_do"];
      }
    }
    if($pocet_neplatnych<>0) $pocet_neplatnych = "<font color=red><b>$pocet_neplatnych</b></font>";

  /*  if(!($typ)) $typ=1;*/
    $SQL = " select distinct v.id id_soub, v.nazev, v.popis, v.datum,
                             v.platnost_do, v.trida, v.predmet, v.velikost,
                             u.jmeno, u.prijmeni
             from soubory_skupiny vs, soubory v, ucitele u
             where u.login=v.login_uc and
                   v.id = vs.id_soub and
                   v.typ is null
             order by $razeni ";
    if(DB_select($SQL, $vystup, $pocet))
      if($pocet>0)
      {
        echo "<form method=post>Øadit soubory podle ";
        echo "<select name=\"vyber\">";
        echo "<option value=\"0\" ".$selected[0]."> data ulo¾ení souboru";
        echo "<option value=\"1\" ".$selected[1]."> tøíd";
        echo "<option value=\"2\" ".$selected[2]."> pøedmìtù";
        echo "<option value=\"3\" ".$selected[3]."> jména odesílatele";
        echo "<option value=\"4\" ".$selected[4]."> data ukonèení platnosti";
        echo "</select>";

        echo "&nbsp;<input type=submit value=\"zobraz soubory\" name=\"odeslano\">";
        echo "</form>";
        echo "<table border=\"0\"><tr><td><b>Poèet záznamù:</b> </td><td> $pocet</td></tr>";
        echo "<tr><td><b>Poèet neplatných záznamù:</b> </td><td> $pocet_neplatnych</td></tr>";
        echo "<tr><td>&nbsp;</td></tr></table>";
        echo "<table border=0 cellspacing=0 cellpadding=5>";
        Zahlavi_radek(array("Název", "Popis", "Tøída", "Pøedmìt", "Odesílatel", "Velikost", "Datum", "Platnost do", "Pøíjemce"), "left");
        $i=0;
        while($zaz=MySQL_fetch_array($vystup))
        {
          $j=0;
          while($zaz["platnost_do"]<>$neplatne[$j] and $j<count($neplatne)) $j++;
          if($zaz["platnost_do"]==$neplatne[$j]) $podklad = "bgcolor = \"#ffdddd\"";
          $platnost_do = Datum($zaz["platnost_do"], 0);
          echo "<tr valign=\"top\" $podklad>";
          echo "<td>".$zaz["nazev"]."</td>";
          echo "<td>".$zaz["popis"]."</td>";
          echo "<td>".$zaz["trida"]."</td>";
          echo "<td>".$zaz["predmet"]."</td>";
          echo "<td>".$zaz["jmeno"]." ".$zaz["prijmeni"]."</td>";
          echo "<td>".Prevod($zaz["velikost"])."</td>";
          echo "<td>".Datum($zaz["datum"])."</td>";
          echo "<td>".Text_alter($platnost_do, "neuvedena")."</td>";
          $SQL2 = "select s.skupina
                   from skupiny s, soubory_skupiny vs
                   where vs.id_soub='".$zaz["id_soub"]."' and
                   vs.id_skup=s.id";
          if(DB_select($SQL2, $vystup2, $pocet2))
          {
            echo "<td>";
            if($zaznam2=mysql_fetch_array($vystup2)) echo $zaznam2["skupina"];
            while($zaznam2=mysql_fetch_array($vystup2)) echo " ,".$zaznam2["skupina"];

            echo "</td>";
          }
          echo "</tr>";
          $i++;
          $podklad = "";
        }
        echo "<tr><td colspan=\"4\">";
        echo "<form action=\"./soubory_vsechny.php?kod=$kod\" method=post>";
        echo "<input type=\"submit\" name=\"odeslano_promazat\" value = \"vymazat neplatné soubory\">";
        echo "</form>";
        echo "</td></tr></table>";
      }
      else echo "<p><i>V databázi nejsou ulo¾eny ¾ádné soubory.</i>";
      break;


/**** simulujeme prihlaseni pod jinou skupinou uzivatelu (pro kontrolu, co vsechno vidi) */
    case 2:
      /*Podnadpis("Zobrazení platných souborù pouze urèité skupiny pøíjemcù");*/
      //Tlacitka($kod, "soubory_vsechny.php", $pole_vyberu, $pole_tlacitek,2);
      for($i=0;$i<=4;$i++) $selected[$i] = "";
      if(!($vyber2)) $vyber2=1;
      if($vyber2)
      {
        $selected[$vyber2] = "selected";
        $podminka = "vs.id_skup='$vyber2'";
      }

      $SQL = "select * from skupiny order by id";
      if(DB_select($SQL, $vystup, $pocet))
      {
        echo "<form method=post>Zobrazit pouze soubory urèené skupinì ";
        echo "<select name=\"vyber2\">";
        while($zaz=mysql_fetch_array($vystup))
        {
          echo "<option value=\"".$zaz["id"]."\" ".$selected[$zaz["id"]]."> ".$zaz["skupina"];
        }
        echo "</select>";
        echo "&nbsp;<input type=submit value=\"zobraz soubory\" name=\"odeslano\">";
        echo "</form>";
      }
       $SQL = " select distinct v.id id_soub, v.nazev, v.popis, v.datum,
                             v.platnost_do, v.trida, v.predmet, v.velikost,
                             u.jmeno, u.prijmeni
             from soubory_skupiny vs, soubory v, ucitele u
             where u.login=v.login_uc and
                   v.id = vs.id_soub and
                   (v.platnost_do>=Now() or v.platnost_do='0000-00-00' or v.platnost_do is null) and
                   v.typ is null and
                   $podminka
             order by datum desc";
    if(DB_select($SQL, $vystup, $pocet))
      if($pocet>0)
      {
        echo "<table border=\"0\"><tr><td><b><P>Poèet záznamù:</b> </td><td> $pocet</td></tr>";
        echo "<tr><td>&nbsp;</td></tr></table>";
        echo "<table border=0 cellspacing=2 cellpadding=5>";
        echo "<tr><td bgcolor=\"#dddddd\"><center><b>název</b></center></td><td bgcolor=\"#dddddd\"><center><b>popis</b></center></td><td bgcolor=\"#dddddd\"><center><b>tøída</b></center></td><td bgcolor=\"#dddddd\"><center><b>pøedmìt</b></center></td><td bgcolor=\"#dddddd\"><center><b>odesílatel</b></center></td><td bgcolor=\"#dddddd\"><center><b>velikost</b></center></td><td bgcolor=\"#dddddd\"><center><b>datum</b></center></td><td bgcolor=\"#dddddd\"><center><b>platnost do</b></center></td><td bgcolor=\"#dddddd\"><center><b>pøíjemce</b></center></td></tr>";
        $i=0;
        while($zaz=MySQL_fetch_array($vystup))
        {
          $j=0;
          while($zaz["platnost_do"]<>$neplatne[$j] and $j<count($neplatne)) $j++;
          if($zaz["platnost_do"]==$neplatne[$j]) $podklad = "bgcolor = \"#ffdddd\"";
          $platnost_do = Datum($zaz["platnost_do"], 0);
          echo "<tr valign=\"top\" $podklad>";
          echo "<td>".$zaz["nazev"]."</td>";
          echo "<td>".$zaz["popis"]."</td>";
          echo "<td>".$zaz["trida"]."</td>";
          echo "<td>".$zaz["predmet"]."</td>";
          echo "<td>".$zaz["jmeno"]." ".$zaz["prijmeni"]."</td>";
          echo "<td>".Prevod($zaz["velikost"])."</td>";
          echo "<td>".Datum($zaz["datum"])."</td>";
          echo "<td>".Text_alter($platnost_do, "neuvedena")."</td>";
          $SQL2 = "select s.skupina
                   from skupiny s, soubory_skupiny vs
                   where vs.id_soub='".$zaz["id_soub"]."' and
                   vs.id_skup=s.id";
          if(DB_select($SQL2, $vystup2, $pocet2))
          {
            echo "<td>";
            if($zaznam2=mysql_fetch_array($vystup2)) echo $zaznam2["skupina"];
            while($zaznam2=mysql_fetch_array($vystup2)) echo " ,".$zaznam2["skupina"];

            echo "</td>";
          }
          echo "</tr>";
          $i++;
          $podklad = "";
        }
        echo "</table>";
      }
      else
      {
        echo "<P>".Text_alter("", "Této skupinì u¾ivatelù nejsou urèeny ¾ádné soubory.");
      }
      break;
/**** pouze prehled o tom, kdo kolik platnych souboru ma ulozeno a kolik zabiraji mista na disku */
    case 3:
      /*Podnadpis("Pøehled o mno¾ství ulo¾ených souborù");*/
      //Tlacitka($kod, "soubory_vsechny.php", $pole_vyberu, $pole_tlacitek,3);
/*      $SQL = "select u.*, s.id from ucitel u, soubory s where s.login_uc=u.login";*/
      $SQL = "select count(s.login_uc) pocet, sum(s.velikost) zabr, s.login_uc, u.*
              from soubory s, ucitele u
	      where u.login=s.login_uc and
	            (s.platnost_do>=Now() or s.platnost_do='0000-00-00' or s.platnost_do is null) and
                    s.typ is null
	      group by s.login_uc
	      order by zabr desc";
      if(DB_select($SQL, $vystup, $pocet))
      {
        echo "<p><table border=0 cellspacing=2 cellpadding=5>";
        echo "<tr bgcolor=\"#dddddd\"><td><center><b>zkratka</b></center></td><td><center><b>jméno</b></center></td><td><center><b>poèet souborù</b></center></td><td><center><b>zabráno</b></center></td></tr>";
        while($zaz=mysql_fetch_array($vystup))
        {
          echo "<tr><td><small>".$zaz["zkratka"]."</small></td><td><small>".$zaz["jmeno"]." ".$zaz["prijmeni"]."</small></td><td><small>".$zaz["pocet"]."</small></td><td><small>".Prevod($zaz["zabr"])."</small></td></tr>";
        }
        echo "</table>";
      }
      break;
  }

  Konec();
}
?>
