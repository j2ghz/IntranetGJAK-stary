<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 6, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Vzkazy odeslané vedením ¹koly", $fullname, $kod);
  if($id_vzkazu=="")
  {
    if($skupina>=20) $podminka = "vs.id_skup = '$skupina' or vs.id_skup = '-1' or vs.id_skup = '-2'";
    else $podminka = "vs.id_skup = '$skupina' or vs.id_skup = '-2'";
    if($skupina<c_ucitel) $podminka .= "or vs.id_skup = '".c_ucitel."'";
    $SQL = "      select v.id id_vzkazu, v.text text, v.datum datum,
                         v.platnost_do platnost_do, v.trida trida, v.predmet predmet,
                         u.jmeno jmeno, u.prijmeni prijmeni, s.skupina skupina
                  from vzkazy_skupiny vs, vzkazy v, skupiny s, ucitele u
                  where (v.platnost_do>=Now() or v.platnost_do='0000-00-00' or v.platnost_do is null) and
                        u.login=v.login_uc and
                        s.id=v.id_skup_odesilatel and
                        v.id = vs.id_vzkaz and
                        (v.id_skup_odesilatel='2' or v.id_skup_odesilatel='3') and
                        ($podminka)
			order by v.datum desc";
    if(DB_select($SQL, $vystup, $pocet))
      if($pocet>0)
      {
        echo "<b>Poèet záznamù:</b> $pocet";
        echo "<p><table border=0 cellspacing=0 cellpadding=5>";
        Zahlavi_radek(array("&nbsp;", "Tøída", "Pøedmìt", "Odesílatel", "&nbsp;", "Datum"), "left");
        $i=0;
        while($zaz=MySQL_fetch_array($vystup))
        {
          echo "<tr class=\"tabulka\">";
          echo "<td><a class=\"vzkaz\" href=\"./vzkazy_vedeni.php?kod=$kod&id_vzkazu=".$zaz["id_vzkazu"]."\"><img src=\"./images/oko.gif\" border=\"none\"></a></td>";
          echo "<td><a class=\"vzkaz\" href=\"./vzkazy_vedeni.php?kod=$kod&id_vzkazu=".$zaz["id_vzkazu"]."\">".$zaz["trida"]."</a></td>";
          echo "<td><a class=\"vzkaz\" href=\"./vzkazy_vedeni.php?kod=$kod&id_vzkazu=".$zaz["id_vzkazu"]."\">".$zaz["predmet"]."</a></td>";
          echo "<td><a class=\"vzkaz\" href=\"./vzkazy_vedeni.php?kod=$kod&id_vzkazu=".$zaz["id_vzkazu"]."\">".$zaz["jmeno"]." ".$zaz["prijmeni"]."</a></td>";
          echo "<td><a class=\"vzkaz\" href=\"./vzkazy_vedeni.php?kod=$kod&id_vzkazu=".$zaz["id_vzkazu"]."\">(".$zaz["skupina"].")</a></td>";
          echo "<td><a class=\"vzkaz\" href=\"./vzkazy_vedeni.php?kod=$kod&id_vzkazu=".$zaz["id_vzkazu"]."\">".Datum($zaz["datum"])."</a></td>";
          echo "</tr>";
          $i++;
        }
        echo "</table>";
      }
      else echo Text_alter("","Nemáte ¾ádné zprávy.");
  }
  else
  {
    $SQL = "select * from vzkazy v, skupiny s, ucitele u 
            where v.id = '$id_vzkazu' and s.id = v.id_skup_odesilatel and u.login = v.login_uc";
    if(DB_select($SQL, $vystup, $pocet))
    {
      echo "<table>";
      echo "<tr><td><a href=\"./vzkazy_vedeni.php?kod=$kod\"><img src=\"./images/sipka.gif\" border=none></a></td></tr>";
      if($zaz = MySQL_fetch_array($vystup))
      {
        echo "<TR><td colspan=2>&nbsp;</td></tr>";
        echo "<tr><td width=\"150\"><b>Odesílatel:</b></td><td>".$zaz["jmeno"]." ".$zaz["prijmeni"].", ".$zaz["skupina"]."</td></tr>";
        echo "<tr><td width=\"150\"><b>Pro tøídy/skupiny:</b></td><td>".Text_alter($zaz["trida"], "neuvedeno")."</td></tr>";
        echo "<tr><td width=\"150\"><b>Pøedmìt:</b></td><td>".Text_alter($zaz["predmet"], "neuvedeno")."</td></tr>";
        echo "<tr><td colspan=2>&nbsp;</td></tr><tr><td colspan=2><i>".Text_alter($zaz["text"], "zpráva neobsahuje ¾ádný text")."</i></td></tr></table>";
        echo "<P>";
      }
    }
  }
  Konec();
}
?>
