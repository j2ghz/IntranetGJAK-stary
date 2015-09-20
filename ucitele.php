<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Zamìstnanci", $fullname, $kod);
  if($ucitel=="")
  {
    echo "<center><table border=0 cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">";
    /****** vedeni *****/
    NapisSkupinu("Vedení");
    $SQL = "select u.*, s.skupina
	    from ucitele u, skupiny s
            where u.aktivni='1' and s.id=u.id_skup and (s.id='2' or s.id='3')
	    order by id_skup, u.prijmeni, u.jmeno ";
    VypisZam($SQL);
    /****** ucitele *****/
    NapisSkupinu("Uèitelé");
    $SQL = "select u.*, s.skupina
	    from ucitele u, skupiny s
            where u.aktivni='1' and s.id=u.id_skup and (s.id='1' or s.id='4' or s.id='5')
	    order by u.prijmeni, u.jmeno ";
    VypisZam($SQL);
    /****** spravni zamestnanci *****/
    NapisSkupinu("Správní zamìstnanci");
    $SQL = "select u.*, s.skupina
	    from ucitele u, skupiny s
            where u.aktivni='1' and s.id=u.id_skup and s.id>='6' and s.id<='18'
	    order by id_skup, u.prijmeni, u.jmeno ";
    VypisZamSprav($SQL);
    /****** skolni jidelna *****/
    NapisSkupinu("©kolní jídelna");
    $SQL = "select u.*, s.skupina
	    from ucitele u, skupiny s
            where u.aktivni='1' and s.id=u.id_skup and s.id='19'
	    order by id_skup, u.prijmeni, u.jmeno ";
    VypisZam($SQL);

    echo "</table></center>";
  }
  else
  {
    $SQL = "select u.*, s.* from ucitele u, skupiny s where login='$ucitel' and
            s.id=u.id_skup and aktivni='1'";
    if(DB_select($SQL, $vystup, $pocet))
    {
      if(file_exists("./photos/".strtolower($ucitel).".jpg")) $foto = "<img src=\"./photos/".strtolower($ucitel).".jpg\" border=\"1\">";
      else if(file_exists("./photos/".strtolower($ucitel).".gif")) $foto = "<img src=\"./photos/".strtolower($ucitel).".gif\" border=\"1\">";
      else $foto1 = "<img src=\"./photos/blank.gif\">";
      echo "<center><table border=0 cellspacing=\"10\" cellpadding=\"0\">";
      if($zaznam=MySQL_fetch_array($vystup))
      {
        if($zaznam["id"]==1) $skup = "uèitel";
        else $skup = $zaznam["skupina"];
        echo "<tr><td rowspan=2>$foto</td><td><h2>".Sestav_jmeno($zaznam["titul_pred"], $zaznam["jmeno"],
	     $zaznam["prijmeni"],$zaznam["titul_za"])."</h2></td></tr>";
        echo "<tr><td><h3>($skup)</h3></td></tr>";
       /* echo "<tr><td>Aprobace: </td><td><b>".$zaznam["aprobace"]."</b></td></tr>";*/
        echo "<tr><td>Zkratka: </td><td>".$zaznam["zkratka"]."</td></tr>";
        echo "<tr><td>Kabinet: </td><td>".$zaznam["kabinet"]."</td></tr>";
        Vynech("Vyuèuje na gymnáziu", "",$zaznam["vyuc_oa"],"");
        Vynech("Vyuèuje na jaz. ¹kole", "",$zaznam["vyuc_vose"],"");
        echo "<tr><td>©kolní telefon: </td><td>".Tel().", kl. ".$zaznam["tel1"]."</td></tr>";
        Vynech("Vlastní telefon", "",$zaznam["tel2"],"");
        Vynech("©kolní e-mail", "<a href=\"mailto:".$zaznam["mail1"].c_mail."\">", $zaznam["mail1"], c_mail."</a>");
        Vynech("Vlastní e-mail", "<a href=\"mailto:",$zaznam["mail2"],"\">".$zaznam["mail2"]."</a>");
        Vynech("URL", "<a href=\"".$zaznam["url"]."\">",$zaznam["url"],"</a>");
 /*       echo "<tr><td colspan=2>";
	Vyber_klic($klic, "Zobrazit odeslané vzkazy",
                   "Zobrazit odeslané soubory");
	echo "</td></tr>";                      */
      }
      echo "</table>";
      echo "<p>&nbsp;</p>";
      /*switch($klic)
      {
        case 1: Vzkazy($ucitel);
        break;
        case 2: Soubory($ucitel);
        break;
      } */
      if($pocet==0) echo "<center>O tomto uèiteli neexistují v databázi ¾ádné záznamy.</center>";
    }
  }
  Konec();
}


/******  funkce  **************************************************************/
function Vzkazy($login_uc)
{
  global $kod;
  global $id_vzkazu;
  global $skupina;
  echo "<h3>Odeslané vzkazy</h3>";
  if($id_vzkazu=="")
  {

    if($skupina>=20) $podminka = "vs.id_skup = '$skupina' or vs.id_skup = '-1' or vs.id_skup = '-2'";
    else $podminka = "vs.id_skup = '$skupina' or vs.id_skup = '-2'";
    if($skupina<c_ucitel) $podminka .= "or vs.id_skup = '".c_ucitel."'";
    $SQL = "        select v.id id_vzkazu, v.text text, v.datum datum,
                           v.platnost_do platnost_do, v.trida trida, v.predmet predmet,
                           u.jmeno jmeno, u.prijmeni prijmeni, s.skupina skupina
                    from vzkazy_skupiny vs, vzkazy v, skupiny s, ucitele u
                    where (v.platnost_do>=Now() or v.platnost_do='0000-00-00' or v.platnost_do is null) and
                          u.login=v.login_uc and
                          s.id=v.id_skup_odesilatel and
                          v.id = vs.id_vzkaz and
                          v.login_uc='$login_uc' and
                          u.aktivni='1' and
                          ($podminka)
                    order by v.datum ";
    if(DB_select($SQL, $vystup, $pocet))
      if($pocet>0)
      {

        echo "<table border=0 cellspacing=2 cellpadding=5>";
        echo "<tr><td colspan=3><p><b>Poèet záznamù:</b>
              $pocet</td></tr>";
        echo "<tr><td bgcolor=\"#dddddd\"><center><b>ètení</b></center></td><td bgcolor=\"#dddddd\"><center><b>tøída</b></center></td><td bgcolor=\"#dddddd\"><center><b>pøedmìt</b></center></td><td bgcolor=\"#dddddd\"><center><b>datum</b></center></td></tr>";
        $i=0;
        while($zaz=MySQL_fetch_array($vystup))
        {
          echo "<tr>";
          echo "<td><a href=\"./ucitele.php?kod=$kod&ucitel=$login_uc&klic=1&id_vzkazu=".$zaz["id_vzkazu"]."\"><img src=\"./images/oko.gif\" border=none></a></td>";
          echo "<td>".$zaz["trida"]."</td>";
          echo "<td>".$zaz["predmet"]."</td>";
          echo "<td>".Datum($zaz["datum"])."</td>";
          echo "</tr>";
          $i++;
        }
        echo "</table>";
      }
    else echo "<p>".Text_alter("", "nejsou vám urèeny ¾ádné zprávy")."</p>";
  }
  else
  {
    $SQL = "select * from vzkazy v, skupiny s, ucitele u
            where v.id = '$id_vzkazu' and s.id = v.id_skup_odesilatel and u.login = v.login_uc";
    if(DB_select($SQL, $vystup, $pocet))
    {
      echo "<table>";
      echo "<tr><td><a href=\"./ucitele.php?kod=$kod&ucitel=$login_uc&klic=1\"><img src=\"./images/sipka.gif\" border=none></a></td></tr>";
      if($zaz = MySQL_fetch_array($vystup))
      {
        echo "<tr><td colspan=2>&nbsp;</td></tr>";
        echo "<tr><td width=\"150\"><b>Odesílatel:</b></td><td>".$zaz["jmeno"]." ".$zaz["prijmeni"].", ".$zaz["skupina"]."</td></tr>";
        echo "<tr><td width=\"150\"><b>Pro tøídy/skupiny:</b></td><td>".Text_alter($zaz["trida"], "neuvedeno")."</td></tr>";
        echo "<tr><td width=\"150\"><b>Pøedmìt:</b></td><td>".Text_alter($zaz["predmet"], "neuvedeno")."</td></tr>";
        echo "<tr><td colspan=2>&nbsp;</td></tr><tr><td colspan=2><i>".Text_alter($zaz["text"], "zpráva neobsahuje ¾ádný text")."</i></td></tr></table>";
        echo "<p>";
      }
    }
  }
}

function Soubory($login_uc)
{
  global $kod;
  global $skupina;
  echo "<p><h3>Odeslané soubory</h3>";
  if($skupina>=20) $podminka = "(vs.id_skup = '$skupina' or vs.id_skup = '-1' or vs.id_skup = '-2')";
  else $podminka = "(vs.id_skup = '$skupina' or vs.id_skup = '-2')";
  $SQL = "      select distinct v.id, v.popis popis, v.datum datum,
                       v.nazev nazev, v.velikost velikost,
                       v.login_uc login_uc,
                       u.jmeno jmeno, u.prijmeni prijmeni, v.trida, v.predmet
                from soubory_skupiny vs, soubory v, ucitele u
                where u.login=v.login_uc and
                      v.id = vs.id_soub and
                      v.login_uc = '$login_uc' and
                      $podminka
                order by v.datum ";

    if(DB_select($SQL, $vystup, $pocet))
    if($pocet>0)
    {
      echo "<p><table border=0 cellspacing=2 cellpadding=5>";
      echo "<tr><td colspan=3><p><b>Poèet záznamù:</b> $pocet</td></tr>";
      echo "<tr bgcolor=\"#dddddd\"><td><center><b>soubor</b></center></td><td>
            <center><b>tøída</b></center></td><td><center><b>pøedmìt</b></center></td><td><center><b>velikost</b></center></td><td><center><b>datum</b></center></td></tr>";
      while($zaz=MySQL_fetch_array($vystup))
      {
        echo "<tr>";
        echo "<td valign=\"top\"><dl><dt><a
href=\"".c_files.StrToLower($zaz["login_uc"])."/".$zaz["nazev"]."\">".$zaz["nazev"]."</a></dt>";
        echo "<dd><small><i>".$zaz["popis"]."</i></small></dd></dl></td>";
        echo "<td valign=\"top\"><small>".$zaz["trida"]."</small></td>";
        echo "<td valign=\"top\"><small>".$zaz["predmet"]."</small></td>";
        echo "<td valign=\"top\"><small>".Prevod($zaz["velikost"])."</small></td>";
        echo "<td valign=\"top\"><small>".Datum($zaz["datum"], 0)."</small></td>";
        echo "</tr>";
      }
      echo "</table>";
    }
    else echo "<p>".Text_alter("", "nejsou vám urèeny ¾ádné soubory");

}

function Vyber_klic($klic, $text1, $text2)
{
  global $kod;
  global $ucitel;
  switch($klic)
  {
    case 1:
      echo Text_alter("", $text1);
      echo "<br><a href=\"./ucitele.php?kod=$kod&ucitel=$ucitel&klic=2\">$text2</a>";
    break;
    case 2:
      echo "<a href=\"./ucitele.php?kod=$kod&ucitel=$ucitel&klic=1\">$text1</a>";
      echo "<br>".Text_alter("", $text2);
    break;
    case 3:
      echo "<a href=\"./ucitele.php?kod=$kod&ucitel=$ucitel&klic=1\">$text1</a>";
      echo "<br><a href=\"./ucitele.php?kod=$kod&ucitel=$ucitel&klic=2\">$text2</a>";
    break;
    default:
      echo "<a href=\"./ucitele.php?kod=$kod&ucitel=$ucitel&klic=1\">$text1</a>";
      echo "<br><a href=\"./ucitele.php?kod=$kod&ucitel=$ucitel&klic=2\">$text2</a>";
   break;
  }
}

function NapisSkupinu($text)
{
  echo "<tr><td>&nbsp;</td></tr>";
  Zahlavi_radek(array($text), "center", 3);
  /*echo "<TR><TD colspan=\"3\" class=\"podnadpis\">$text</font></td></tr>";*/
}

function VypisZam($SQL)
{
  global $kod;
  if(DB_select($SQL, $vystup, $pocet))
  {
    while($zaznam=MySQL_fetch_array($vystup))
    {
      echo "<tr class=\"tabulka\" id=\"tabulka\" onMouseOver=\"styl();\" onMouseOut=\"styl();\"><td width=\"30%\">&nbsp;</td><td width=\"50\"><a class=\"seznam_black\" href=\"./ucitele.php?kod=$kod&ucitel=".$zaznam["login"]."\">".$zaznam["zkratka"]."</a></td><td>
               <a class=\"seznam\" href=\"./ucitele.php?kod=$kod&ucitel=".$zaznam["login"]."\">".
               Sestav_jmeno($zaznam["titul_pred"], $zaznam["jmeno"],
               $zaznam["prijmeni"],$zaznam["titul_za"])."</a></td></tr>";
    }
  }
}

function VypisZamSprav($SQL)
{
  global $kod;
  if(DB_select($SQL, $vystup, $pocet))
  {
    while($zaznam=MySQL_fetch_array($vystup))
    {
      echo "<tr class=\"tabulka\" id=\"tabulka\" onMouseOver=\"styl();\" onMouseOut=\"styl();\"><td width=\"30%\">&nbsp;</td><td width=\"50\"><a class=\"seznam_black\" href=\"./ucitele.php?kod=$kod&ucitel=".$zaznam["login"]."\">".$zaznam["zkratka"]."</a></td><td>
               <a class=\"seznam\" href=\"./ucitele.php?kod=$kod&ucitel=".$zaznam["login"]."\">".
               Sestav_jmeno($zaznam["titul_pred"], $zaznam["jmeno"],
               $zaznam["prijmeni"],$zaznam["titul_za"])." </a>(".$zaznam["skupina"].")</td></tr>";
    }
  }
}

function Tel()
{
  $SQL = "select hodnota from pomocna where klic='tel'";
  if(DB_select($SQL, $vyst,$poc))
    if($zaz=mysql_fetch_array($vyst)) return $zaz["hodnota"];
}


?>
