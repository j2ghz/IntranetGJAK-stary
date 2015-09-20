<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 5, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Ulo¾ení souboru", $fullname, $kod);
  echo "<P>";
  $SQL = "select * from ucitele where login='$login'";
  DB_select($SQL, $vystup, $pocet);
  if($pocet==0) echo "<b><font color=\"red\"><p>Va¹e základní údaje je¹tì nebyly ulo¾eny do databáze! <BR>Bez základních údajù nelze ukládat soubory na server.<P>Kontaktujte prosím administrátora nebo zástupce øeditele.</b></font>";
  else
  {
    echo "<center>".Hlaska($chyba, "Soubor se nepodaøilo ulo¾it", "Soubor byl úspì¹nì ulo¾en")."</center>";
    if($chyba<>"ok")
    {
      echo "<form action=\"./soubory_send.php?kod=$kod\" method=post enctype=\"multipart/form-data\"><table>";
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
      $SQL = "select count(*) pocet from skupiny where id<20";
      if(DB_select($SQL, $vystup, $pocet)) $zaznam = MySQL_fetch_array($vystup);
      $zaklad = $zaznam["pocet"]-2;
      echo "<tr><td><br><input type=\"checkbox\" name=\"vsichni[]\" value=\"".$skup[0]->id."\">".$skup[0]->skupina;
      echo "<br><input type=\"checkbox\" name=\"vsichni_studenti[]\" value=\"".$skup[1]->id."\">".$skup[1]->skupina."</td></tr>";
      echo "<tr><td colspan=3><hr></td></tr>";
      echo "<tr><td valign=\"top\">";
      for($i=2; $i<count($skup); $i++)
      {
        if((($i-2)%$zaklad)==0 and $i<>2)
        {
          echo "</td><td valign=\"top\">";
          echo "<input type=\"checkbox\" name=\"prijemce[]\" value=\"".$skup[$i]->id."\">".$skup[$i]->skupina;
        }
        else if($i==2) echo "<input type=\"checkbox\" name=\"prijemce[]\" value=\"".$skup[$i]->id."\">".$skup[$i]->skupina;
             else echo "<br><input type=\"checkbox\" name=\"prijemce[]\" value=\"".$skup[$i]->id."\">".$skup[$i]->skupina;
      }
      echo "</td></tr></table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Tøída:</font></b>";
      echo "<br><font color=gray><small>- uvádìjte pro lep¹í orientaci studentù
            v souborech <BR>";
      echo "</small></font></td></tr>";
      echo "<tr><td><input type=\"text\" name=\"trida\" value=\"$trida\"></td></tr></table>";
      echo "<p><table border=0><tr><td><b><".c_font.">Pøedmìt:</font></b> ";
      echo "</td></tr>";
      echo "</td></tr><tr><td><input type=\"text\" name=\"predmet\" value=\"$predmet\"></td></tr></table>";
      echo "<p><table border=0><tr><td><b><".c_font.">Soubor k odeslání:</td></tr>";
      echo "<tr><td><input type=\"file\" name=\"soubor\" value=\"$soubor\"></td></tr></table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Nový název souboru:</b>";
      echo "<br><font color=gray><small>- nový název souboru nesmí být prázdný a mù¾e obsahovat
                      pouze tyto znaky:<br>a-z, A-Z, 0-9, ., _, -,
		      <br>tj. nesmíte pou¾ívat diakritiku a mezery (napø. namísto \"vzorové pøíklady\" pi¹te \"vzorove_priklady\")</small></td></tr>";
      echo "<tr><td><input type=\"textbox\" name=\"nazev\" value=\"$nazev\"></td></tr></table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Struèný popis:</td></tr>";
      echo "<tr><td><textarea name=\"popis\" value=\"$popis\" rows=3 cols=20></textarea></td></tr></table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Platnost souboru do:</b>";
      echo "<br><font color=gray><small>- datum pi¹te ve formátu <i>den.mìsíc.rok</i> bez mezer, rok na 4 èíslice</small></font>";
      echo "<br><input type=\"text\" name=\"platnost_do\" value=\"$platnost_do\"></td></tr>";
      echo "</table>";


      echo "<table border=0><tr><td><P>&nbsp;</P><input type = \"submit\" value=\"odeslat soubor\" name=\"odeslano\"></td></tr>";
      echo "</table></form>";
    }
  }
  Konec();
}

define(c_font, "font size=4");

function NabidniDatum(&$den, &$mesic, &$rok)
{
  echo "<select name=\"den\">";
  for($i=1; $i<=31; $i++)
  {
    echo "<option value=\"$i\">$i.";
  }
  echo "</select>";

  echo "<select name=\"mesic\">";
  for($i=1; $i<=12; $i++)
  {
    echo "<option value=\"$i\">$i.";
  }
  echo "</select>";

  $rok = Date("Y");
  $rok_za = $rok+1;
  echo "<select name=\"rok\">";
  echo "<option value = \"$rok\" selected>$rok";
  echo "<option value = \"$rok_za\">$rok_za";
  echo "</select>";

}

function UpravString(&$text)
{
$text = EReg_Replace(" ", "", $text);
$text = StrToUpper($text);
}

?>
