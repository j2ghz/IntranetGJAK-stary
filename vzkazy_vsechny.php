<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 1, $fullname, $login, $chyba)):
  NoCACHE();
  Hlavicka("Zobrazení v¹ech vzkazù", $fullname, $kod);
  for($i=0;$i<=4;$i++) $selected[$i] = "";
  if($id_vzkazu=="")
  {
    if($odeslano)
    {
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
             $razeni = "v.id_skup_odesilatel";
             $selected[4] = "selected";
             break;
        case 5:
             $razeni = "v.platnost_do";
             $selected[5] = "selected";
             break;
      }
    }
    else
    {
      $razeni = "v.datum desc";
      $selected[0] = "selected";
    }
    if($odeslano_promazat)
    {
      $SQL = "select id from vzkazy
              where (platnost_do<Now() and platnost_do<>'0000-00-00' and platnost_do is not null)";
      if(DB_select($SQL, $vystup, $pocet))
      {
        while($zaznam = MySQL_fetch_array($vystup))
        {
          $SQL_vs = "delete from vzkazy_skupiny where id_vzkaz='".$zaznam["id"]."'";
          DB_exec($SQL_vs);
	  $SQL_v = "delete from vzkazy where id='".$zaznam["id"]."'";
          DB_exec($SQL_v);
        }
      }
    }
    $SQL_neplatne = "select platnost_do from vzkazy where platnost_do<Now() and platnost_do<>'0000-00-00' and platnost_do is not null";
    if(DB_select($SQL_neplatne, $vystup, $pocet_neplatnych))
    {
      while($zaz = MySQL_fetch_array($vystup))
      {
        $neplatne[] = $zaz["platnost_do"];
      }
    }
    if($pocet_neplatnych<>0) $pocet_neplatnych = "<font color=red><b>$pocet_neplatnych</b></font>";
    $SQL = "      select distinct v.id id_vzkazu, v.text text, v.datum datum,
                         v.platnost_do platnost_do, v.trida trida, v.predmet predmet,
                         u.jmeno jmeno, u.prijmeni prijmeni, s.skupina skupina
                  from vzkazy_skupiny vs, vzkazy v, skupiny s, ucitele u
                  where u.login=v.login_uc and
                        s.id=v.id_skup_odesilatel and
                        v.id = vs.id_vzkaz 
		  order by $razeni ";

    if(DB_select($SQL, $vystup, $pocet))
      if($pocet>0)
      {
        echo "<form method=post>Øadit zprávy podle ";
        echo "<select name=\"vyber\">";
        echo "<option value=\"0\" ".$selected[0]."> data odeslání zprávy";
        echo "<option value=\"1\" ".$selected[1]."> tøíd";
        echo "<option value=\"2\" ".$selected[2]."> pøedmìtù";
        echo "<option value=\"3\" ".$selected[3]."> jména odesílatele";
        echo "<option value=\"4\" ".$selected[4]."> \"funkce odesílatele\"";
        echo "<option value=\"5\" ".$selected[5]."> data ukonèení platnosti";
        echo "</select>";
        echo "&nbsp;<input type=submit value=\"zobraz zprávy\" name=\"odeslano\">";
        echo "</form>";
        echo "<table border=\"0\"><tr><td><b>Poèet záznamù:</b> </td><td> $pocet</td></tr>";
        echo "<tr><td><b>Poèet neplatných záznamù:</b> </td><td> $pocet_neplatnych</td></tr>";
        echo "<tr><td>&nbsp;</td></tr></table>";
        echo "<table border=0 cellspacing=2 cellpadding=5>";
        Zahlavi_radek(array("Ètení", "Tøída", "Pøedmìt", "Odesílatel", "&nbsp;", "Datum", "Platnost do"), "left");
        $i=0;
        while($zaz=MySQL_fetch_array($vystup))
        {
          $j=0;
          while($zaz["platnost_do"]<>$neplatne[$j] and $j<count($neplatne)) $j++;
          if($zaz["platnost_do"]==$neplatne[$j]) $podklad = "bgcolor = \"#ffdddd\"";
          $platnost_do = Datum($zaz["platnost_do"], 0);
          echo "<tr valign=\"top\" $podklad>";
          echo "<td><a href=\"./vzkazy_vsechny.php?kod=$kod&id_vzkazu=".$zaz["id_vzkazu"]."\"><img src=\"./images/oko.gif\" border=none></a></td>";
          echo "<td>".$zaz["trida"]."</td>";
          echo "<td>".$zaz["predmet"]."</td>";
          echo "<td>".$zaz["jmeno"]." ".$zaz["prijmeni"]."</td>";
          echo "<td>(".$zaz["skupina"].")</td>";
          echo "<td>".Datum($zaz["datum"])."</td>";
          echo "<td>".Text_alter($platnost_do, "neuvedena")."</td>";
          echo "</tr>";
          $i++;
          $podklad = "";
        }
        echo "<tr><td colspan=\"4\">";
        echo "<form action=\"./vzkazy_vsechny.php?kod=$kod\" method=post>";
	echo "<input type=\"submit\" name=\"odeslano_promazat\" value = \"vymazat neplatné zprávy\"></td></tr>";
        echo "</form>";
        echo "</table>";
      }
      else echo "<p><i>V databázi nejsou ulo¾eny ¾ádné zprávy.</i>";
  }
  else
  {
    $SQL = "select v.*, s.*, vs.*, u.*, ss.skupina as skupina_odes
            from vzkazy v, skupiny s, skupiny ss, ucitele u, vzkazy_skupiny vs
            where v.id = '$id_vzkazu' and
                  u.login = v.login_uc and
		  s.id = vs.id_skup and
		  vs.id_vzkaz = v.id and
		  ss.id = v.id_skup_odesilatel";
    /*echo "SQL = $SQL";*/
    if(DB_select($SQL, $vystup, $pocet))
    {
      echo "<table>";
      echo "<tr><td><a href=\"./vzkazy_vsechny.php?kod=$kod\"><img src=\"./images/sipka.gif\" border=none></a></td></tr>";
      echo "<tr><td>&nbsp;</td></tr>";
      echo "<tr valign = \"top\"><td><b>Pøíjemci zprávy:</b></td><td>";
      $prvnipruchod = true;
      while($zaz = MySQL_fetch_array($vystup))
      {
        if($prvnipruchod)
        {
          echo $zaz["skupina"];
          $jmeno = $zaz["jmeno"]." ".$zaz["prijmeni"].", ".$zaz["skupina_odes"];
          $trida = $zaz["trida"];
          $predmet = $zaz["predmet"];
          $text = $zaz["text"];
          $prvnipruchod = false;
        }
        else echo "<br>".$zaz["skupina"];
      }
      echo "</td></tr>";
      echo "<tr><td width=\"150\"><b>Odesílatel:</b></td><td>$jmeno</td></tr>";
      echo "<tr><td width=\"150\"><b>Pro tøídy/skupiny:</b></td><td>".Text_alter($trida, "neuveden")."</td></tr>";
      echo "<tr><td width=\"150\"><b>Pøedmìt:</b></td><td>".Text_alter($predmet, "neuvedeo")."</td></tr>";
      echo "<tr><td colspan=2>&nbsp;</td></tr><tr><td colspan=2><i>".Text_alter($text, "zpráva neobsahuje ¾ádný text")."</i></td></tr></table>";
      echo "<P>";
    }
  }
  Konec();
endif;
?>
