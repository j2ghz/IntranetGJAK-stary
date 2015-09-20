<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 5, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Odeslání vzkazu", $fullname, $kod);
  echo "<P>";
  $SQL = "select * from ucitele where login='$login'";
  DB_select($SQL, $vystup, $pocet);
  if($pocet==0) echo "<b><font color=\"red\"><p>Va¹e základní údaje je¹tì nebyly ulo¾eny do databáze! <BR>Bez základních údajù nelze poslat vzkaz.<P>Kontaktujte prosím administrátora nebo zástupce øeditele.</b></font>";
  else
  {
    echo "<center>".Hlaska($chyba, "Vzkaz se nepodaøilo odeslat", "Vzkaz byl úspì¹nì odeslán")."</center>";
    /*echo "<center>".Hlaska_zpravy($chyba)."</center>";*/
    if($chyba<>"ok")
    {
      echo "<form action=\"./vzkazy_send.php?kod=$kod\" method=post><table>";
      echo "<tr><td colspan=3><b><".c_font.">Pøíjemce zprávy (roèník):</font></b>";
      echo "<br><font color=gray><small>- zvolíte-li <i>uèitel</i>, zpráva se zobrazí i øediteli, zástupcùm a administrátorùm";
      echo "<br>- jednotlivé skupiny mají pøednost pøed volbou <i>v¹ichni</i> i <i>v¹ichni studenti</i>";
      echo "<br>- podobnì <i>v¹ichni studenti</i> má pøednost pøed volbou <i>v¹ichni</i></li></small></font></td></tr>";
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

      echo "<p><table border=0><tr><td><b><".c_font.">Zpráva se týká pouze pøedmìtové komise:</font></b></td></tr>";
      $SQL = "select * from komise order by zkratka";
      if(DB_select($SQL, $vystup, $pocet))
      {
        echo "<tr><td><select name=\"komise\">";
        while($zaz=mysql_fetch_array($vystup))
        {
          echo "<option value=\"".$zaz["zkratka"]."\">".$zaz["nazev"];
        }
        echo "</select></td></tr></table>";
      }

      echo "<p><table border=0><tr><td><b><".c_font.">Tøída/studijní skupina:</font></b>";
      echo "<br><font color=gray><small>- uvádìjte pro lep¹í orientaci studentù ve zprávách <BR>";
      echo "</small></font></td></tr>";
      echo "<tr><td><input type=\"text\" name=\"trida\" value=\"$trida\"></td></tr></table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Pøedmìt (popø. jiná specifikace):</font></b>";
      echo "<br><font color=gray><small>- uvádìjte pro lep¹í orientaci studentù ve zprávách</td></tr>";
      echo "<tr><td><input type=\"text\" name=\"predmet\" value=\"$predmet\"></td></tr></table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Text zprávy:</td></tr>";
      echo "<tr><td><textarea name=\"text\" value=\"$text\" rows=10 cols=45></textarea></td></tr></table>";

      $cas = time()+7*24*3600;
      $platnost_do = date("d. m. Y", $cas);

      echo "<p><table border=0><tr><td><b><".c_font.">Platnost zprávy do:</b>";
      echo "<br><font color=gray><small>- datum pi¹te ve formátu <i>den.mìsíc.rok</i> bez mezer, rok na 4 èíslice</small></font>";
      echo "<br><input type=\"text\" name=\"platnost_do\" value=\"$platnost_do\"></td></tr>";
      echo "</table>";

      if($skupina<>c_ucitel)
      {
        echo "<p><table border=0><tr><td><b><".c_font.">Odeslat jako:</font></b>";
        $SQL = "select skupina from skupiny where id = '$skupina' ";
        if(DB_select($SQL, $vystup, $pocet))
        {
          if($zaznam = MySQL_fetch_array($vystup))
          {
           echo "<tr><td><input type=\"radio\" name=\"special\" value=\"$skupina\">".$zaznam["skupina"]."<br>";
          }
        }
        echo "<input type=\"radio\" name=\"special\" value=\"".c_ucitel."\" checked>uèitel</td></tr>";
      }
      else
      {
        echo "<table border=0><tr><td><input type=\"hidden\" name=\"special\" value=\"$skupina\"></td></tr>";
      }

      echo "<tr><td><P>&nbsp;</P><input type = \"submit\" value=\"odeslat zprávu\" name=\"odeslano\"></td></tr></table>";
            echo "<p><table border=1 bgcolor=\"#eeeeee\">";
      echo "<tr><td><font color=\"red\">Chcete-li, aby Vá¹ vzkaz byl formátován do èitelnìj¹í podoby, mù¾ete v textu zprávy pou¾ít následující editaèní znaèky:</font><P>";
      echo "dal¹í øádek = &lt;BR&gt;<br>nový odstavec s vynecháním 1 øádku = &lt;P&gt;<br>tuèný text (kolem zvýrazòovaného textu) = &lt;B&gt;&lt;/B&gt;
      <br> podtr¾ený text (kolem podtrhávaného textu) = &lt;U&gt;&lt;/U&gt;<P>Napø. napí¹ete-li text:<p><i>Zítra se koná &lt;B&gt;porada&lt;/B&gt; v uèebnì 208,&lt;br&gt;dostavte se &lt;U&gt;v¹ichni&lt;/U&gt;.&lt;P&gt;podpis</i><P>
      text se zobrazí následovnì: <p><i>Zítra se koná <B>porada</B> v uèebnì 208,<br>dostavte se <U>v¹ichni</U>.<P>podpis</i><p></td></tr>";
      echo "</table>";

      echo "</form>";
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

function Hlaska_zpravy($chyba)
{
if($chyba<>"ok" and $chyba<>"") return "<P><table border=1 bgcolor=\"#e6e6e6\" cellpadding=15><tr><td>
                       <font color=red><b><center>Vzkaz se nepodaøilo odeslat
		       </center></b></font><ul>$chyba</ul></td></tr></table><p>&nbsp;</p>";
else if($chyba=="ok") return "<P><table border=1 bgcolor=\"#e6e6e6\" cellpadding=15><tr><td><font color=red><b>Vzkaz byl úspì¹nì odeslán</b></font></td></tr></table></p>";
}



?>
