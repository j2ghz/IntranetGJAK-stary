<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 2, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Mazání libovolných souborù", $fullname, $kod);
  for($i=0;$i<=4;$i++) $selected[$i] = "";
  echo "<center>".Hlaska(" ","Zde máte mo¾nost odstranit jakýkoli soubor kteréhokoli u¾ivatele. <br>Dobøe
       rozva¾te, jestli ho dotyèný nebude potøebovat. <br>Odstranìní je nevratná operace.","")."</center>";
  
  if($id_soub=="")
  {
    if($odeslano_vymaz)
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
            if(!(@unlink(c_files."files/".$login_uc."/".$zaznam["nazev"])))
              echo "soubor ".$zaznam["nazev"]." se nepodaøilo odstranit";
            else
            {
              $SQL = "delete from soubory_skupiny where $podminka_vs";
              DB_exec($SQL);
              $SQL = "delete from soubory where $podminka_v";
              DB_exec($SQL);
            }
          }
        }
    }
    $SQL = "      select distinct v.id id_soub, v.datum,
                         v.trida trida, v.predmet, v.login_uc, u.zkratka,
                         v.nazev, v.popis, v.velikost, v.typ, v.platnost_do, u.login,
                         u.jmeno jmeno, u.prijmeni prijmeni
                  from soubory_skupiny vs, soubory v,  ucitele u
                  where u.login = v.login_uc and
                        v.id = vs.id_soub and
                        v.typ is null and
                        (v.platnost_do>=Now() or v.platnost_do='0000-00-00' or v.platnost_do is null)
                  order by v.datum desc ";
    if(DB_select($SQL, $vystup, $pocet))
      if($pocet>0)
      {
        echo "<P>Poèet záznamù: $pocet";
        echo "<form method=\"post\">";
        echo "<p><table border=0 cellspacing=0 cellpadding=5>";

        Zahlavi_radek(array("Mazání", "Soubor", "Odesílatel",  "Tøída", "Pøedmìt", "Velikost", "Datum", "Platnost do"), "left");
        $i=0;
        while($zaz=MySQL_fetch_array($vystup))
        {
          echo "<tr valign=\"top\">";
          echo "<td><input type=\"checkbox\" name=\"vymaz[]\" value=\"".$zaz["id_soub"]."\"></td>";
          $p_adresar="files/".StrToLower($zaz["login_uc"])."/".$zaz["nazev"];
          $p_nazev=$zaz["nazev"];
          echo "<td><dl><dt><a href=\"sendfile.php?kod=$kod&p_prava=1&p_nazev=$p_nazev&p_adresar=$p_adresar\">".$zaz["nazev"]."</a></dt><dd><i><small>".$zaz["popis"]."</i></small></dd></dl></td>";
          echo "<td><small>".$zaz["zkratka"]."</small></td>";
	  echo "<td><small>".$zaz["trida"]."</small></td>";
          echo "<td><small>".$zaz["predmet"]."</small></td>";
          echo "<td><small>".Prevod($zaz["velikost"])."</small></td>";
          echo "<td><small>".Datum($zaz["datum"])."</small></td>";
          echo "<td><small>".Text_alter(Datum($zaz["platnost_do"], 0), "neuvedena")."</small></td>";
          echo "</tr>";
        echo "<input type=\"hidden\" name=\"login_uc\" value=\"".StrToLower($zaz["login_uc"])."\">";
          $i++;
        }

        echo "<tr><td colspan=\"5\"><input type=\"submit\" name=\"odeslano_vymaz\" value=\"odstranit vybrané soubory\"></td></tr>";
        echo "</table>";
        echo "</form>";
      }
      else echo Text_alter("","<p>Nebyl ulo¾en ¾ádný soubor.");
  }
  Konec();
}
?>
