<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Soubory ke sta¾ení", $fullname, $kod);
  for($i=0;$i<=4;$i++) $selected[$i] = "";
  if($odeslano)
  {
    switch($vyber)
    {
      case 0:
           $razeni = "v.datum desc";
           $selected[0] = "selected";
           break;
      case 1:
           $razeni = "u.prijmeni, u.jmeno";
           $selected[1] = "selected";
           break;
    }
  }
  else
  {
    $razeni = "v.datum desc";
    $selected[0] = "selected";
  }

  if($skupina>=20) $podminka = "vs.id_skup = '$skupina' or vs.id_skup = '-1' or vs.id_skup = '-2'";
  else $podminka = "vs.id_skup = '$skupina' or vs.id_skup = '-2'";
  if($skupina<c_ucitel) $podminka .= "or vs.id_skup = '".c_ucitel."'";
  $SQL = "      select distinct v.id, v.popis, v.datum,
                       v.nazev, v.velikost,
                       v.login_uc,
                       u.jmeno, u.prijmeni, v.trida, v.predmet, v.typ
                from soubory_skupiny vs, soubory v, ucitele u
                where u.login=v.login_uc and
                      v.id = vs.id_soub and
                      v.typ is null and
                      (v.platnost_do>=Now() or v.platnost_do='0000-00-00' or v.platnost_do is null) and
                      ($podminka)
                order by $razeni ";
  if(DB_select($SQL, $vystup, $pocet))
    if($pocet>0)
    {
      echo "<form method=post>Øadit soubory podle ";
      echo "<select name=\"vyber\">";
      echo "<option value=\"0\" ".$selected[0]."> data ulo¾ení souboru";
      echo "<option value=\"1\" ".$selected[1]."> jména odesílatele";
      echo "</select>";
      echo "&nbsp;<input type=submit value=\"zobraz soubory\" name=\"odeslano\">";
      echo "</form>";
      echo "<P>&nbsp;</P><P><b>Poèet záznamù:</b> $pocet";
      echo "<p><table border=0 cellspacing=0 cellpadding=5>";
      Zahlavi_radek(array("Soubor", "Tøída", "Pøedmìt", "Odesílatel", "Velikost", "Datum"), "left");
      while($zaz=MySQL_fetch_array($vystup))
      {
        echo "<tr class=\"tabulka\">";
        echo "<td valign=\"top\"><dl><dt>";
        $p_adresar="files/".StrToLower($zaz["login_uc"])."/".$zaz["nazev"];
        $p_nazev=$zaz["nazev"];
	echo "<a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=$p_nazev&p_adresar=$p_adresar\">".$zaz["nazev"]."</a></dt>";
        echo "<dd><a class=\"vzkaz_small\" href=\"sendfile.php?kod=$kod&p_prava=6&p_adresar=$p_adresar&p_nazev=$p_nazev\"><i>".$zaz["popis"]."</i></a></dd></dl></td>";
        echo "<td valign=\"top\"><a class=\"vzkaz_small\" target=\"_new\" href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=$p_nazev&p_adresar=$p_adresar\">".$zaz["trida"]."</a></td>";
        echo "<td valign=\"top\"><a class=\"vzkaz_small\" target=\"_new\" href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=$p_nazev&p_adresar=$p_adresar\">".$zaz["predmet"]."</a></td>";
        echo "<td valign=\"top\"><a class=\"vzkaz_small\" target=\"_new\" href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=$p_nazev&p_adresar=$p_adresar\">".$zaz["jmeno"]." ".$zaz["prijmeni"]."</a></td>";
        echo "<td valign=\"top\"><a class=\"vzkaz_small\" target=\"_new\" href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=$p_nazev&p_adresar=$p_adresar\">".Prevod($zaz["velikost"])."</a></td>";
        echo "<td valign=\"top\"><a class=\"vzkaz_small\" target=\"_new\" href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=$p_nazev&p_adresar=$p_adresar\">".Datum($zaz["datum"], 0)."</a></td>";
        echo "</tr>";
      }
      echo "</table>";
    }
    else echo Text_alter("","Nejsou vám urèeny ¾ádné soubory.");
  Konec();
}
?>

