<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 5, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Oprava vlastních údajù", $fullname, $kod);
  if($odeslano_udaje)
  {
    if(StrPos(StrToLower(" ".$url), "http://")==false and EReg_Replace(" ", "", $url)<>"") $url = "http://$url";
    $SQL = "select * from ucitele where login = '$login'";
    DB_select($SQL, $vystup, $pocet);
    $SQL_update = "update ucitele set titul_pred='$titul_pred', titul_za='$titul_za', tel2='$tel2', mail2='$mail2', url='$url', vyuc_oa='$vyucuje_OA', vyuc_vose='$vyucuje_VOSE' where login='$login'";
    DB_exec($SQL_update);
  }
  else
  {
    $SQL_insert = "insert into rozvrhy (login_uc, predmety, tridy, ucebny, aktualizace) values ('$login', '$predmet', '$trida','$ucebna', 'Now()')";
    DB_exec($SQL_insert);
  }
  $SQL = "select * from ucitele where login='$login'";
  if(DB_select($SQL, $vystup, $pocet))
  {
    if($zaznam=MySQL_fetch_array($vystup))
    {
      $jmeno = $zaznam["jmeno"];
      $prijmeni = $zaznam["prijmeni"];
      $titul_pred = $zaznam["titul_pred"];
      $titul_za = $zaznam["titul_za"];
      $zkratka = $zaznam["zkratka"];
      $kabinet = $zaznam["kabinet"];
      $vyucuje_OA = $zaznam["vyuc_oa"];
      $vyucuje_VOSE = $zaznam["vyuc_vose"];
      $mail1 = $zaznam["mail1"];
      $mail2 = $zaznam["mail2"];
      $tel1 = $zaznam["tel1"];
      $tel2 = $zaznam["tel2"];
      $url= $zaznam["url"];
    }
  }
  if($pocet=="0")
  {
    echo "<b><font color=\"red\"><p>Va¹e základní údaje je¹tì nebyly ulo¾eny do databáze! <P>Kontaktujte prosím administrátora nebo zástupce øeditele.</b></font>";
  }
  else
  {
    echo "<P><h3>Osobní údaje:</h3>";
    echo "<FORM action=\"./ucitele_udaje.php?kod=$kod\" method=post><TABLE border=0>";
    echo "<tr><td>Jméno:</td><td>".Bunka($jmeno)."</td></tr>";
    echo "<tr><td>Pøíjmení: </td><td>".Bunka($prijmeni)."</td></tr>";
    echo "<tr><td>Tituly pøed jménem: </td><td><input type=\"text\" value=\"$titul_pred\" name=\"titul_pred\"></td></tr>";
    echo "<tr><td>Tituly za jménem: </td><td><input type=\"text\" value=\"$titul_za\" name=\"titul_za\"></td></tr>";
    echo "<tr><td>Zkratka: </td><td>".Bunka($zkratka)."</td></tr>";
    echo "<tr><td>Kabinet: </td><td>".Bunka($kabinet)."</td></tr>";
    echo "<tr><td>Vyuèované pøedmìty na gymnáziu: </td><td><input type=\"text\" name=\"vyucuje_OA\" value=\"$vyucuje_OA\"></td></tr>";
    echo "<tr><td>Vyuèované pøedmìty na jaz. ¹kole: </td><td><input type=\"text\" name=\"vyucuje_VOSE\" value=\"$vyucuje_VOSE\"></td></tr>";
    echo "<tr><td>©kolní telefon (pouze klapka): </td><td>".Bunka($tel1)."</td></tr>";
    echo "<tr><td>Vlastní telefon: </td><td><input type=\"text\" value=\"$tel2\" name=\"tel2\"></td></tr>";
    echo "<tr><td>©kolní e-mail: </td><td>".Bunka($mail1.c_mail)."</td></tr>";
    echo "<tr><td>Vlastní e-mail: </td><td><input type=\"text\" value=\"$mail2\" name=\"mail2\"></td></tr>";
    echo "<tr><td>Internetová adresa vlastních stránek: </td><td><input type=\"text\" value=\"$url\" name=\"url\"></td></tr>";
    echo "</table>";
    echo "<input type=\"submit\" name=\"odeslano_udaje\" value=\"ode¹li osobní údaje\"></FORM>";
  }
  Konec();
}


function Bunka($text)
{
return "<table border=1 bgcolor=\"#e6e6e6\"><tr><td width=\"150\">$text&nbsp;</td></tr></table>";
}

function UpravString(&$text)
{
$text = EReg_Replace(" ", "", $text);
$text = StrToUpper($text);
}

?>
