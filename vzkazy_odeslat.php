<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 5, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Odesl�n� vzkazu", $fullname, $kod);
  echo "<P>";
  $SQL = "select * from ucitele where login='$login'";
  DB_select($SQL, $vystup, $pocet);
  if($pocet==0) echo "<b><font color=\"red\"><p>Va�e z�kladn� �daje je�t� nebyly ulo�eny do datab�ze! <BR>Bez z�kladn�ch �daj� nelze poslat vzkaz.<P>Kontaktujte pros�m administr�tora nebo z�stupce �editele.</b></font>";
  else
  {
    echo "<center>".Hlaska($chyba, "Vzkaz se nepoda�ilo odeslat", "Vzkaz byl �sp�n� odesl�n")."</center>";
    /*echo "<center>".Hlaska_zpravy($chyba)."</center>";*/
    if($chyba<>"ok")
    {
      echo "<form action=\"./vzkazy_send.php?kod=$kod\" method=post><table>";
      echo "<tr><td colspan=3><b><".c_font.">P��jemce zpr�vy (ro�n�k):</font></b>";
      echo "<br><font color=gray><small>- zvol�te-li <i>u�itel</i>, zpr�va se zobraz� i �editeli, z�stupc�m a administr�tor�m";
      echo "<br>- jednotliv� skupiny maj� p�ednost p�ed volbou <i>v�ichni</i> i <i>v�ichni studenti</i>";
      echo "<br>- podobn� <i>v�ichni studenti</i> m� p�ednost p�ed volbou <i>v�ichni</i></li></small></font></td></tr>";
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

      echo "<p><table border=0><tr><td><b><".c_font.">Zpr�va se t�k� pouze p�edm�tov� komise:</font></b></td></tr>";
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

      echo "<p><table border=0><tr><td><b><".c_font.">T��da/studijn� skupina:</font></b>";
      echo "<br><font color=gray><small>- uv�d�jte pro lep�� orientaci student� ve zpr�v�ch <BR>";
      echo "</small></font></td></tr>";
      echo "<tr><td><input type=\"text\" name=\"trida\" value=\"$trida\"></td></tr></table>";

      echo "<p><table border=0><tr><td><b><".c_font.">P�edm�t (pop�. jin� specifikace):</font></b>";
      echo "<br><font color=gray><small>- uv�d�jte pro lep�� orientaci student� ve zpr�v�ch</td></tr>";
      echo "<tr><td><input type=\"text\" name=\"predmet\" value=\"$predmet\"></td></tr></table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Text zpr�vy:</td></tr>";
      echo "<tr><td><textarea name=\"text\" value=\"$text\" rows=10 cols=45></textarea></td></tr></table>";

      $cas = time()+7*24*3600;
      $platnost_do = date("d. m. Y", $cas);

      echo "<p><table border=0><tr><td><b><".c_font.">Platnost zpr�vy do:</b>";
      echo "<br><font color=gray><small>- datum pi�te ve form�tu <i>den.m�s�c.rok</i> bez mezer, rok na 4 ��slice</small></font>";
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
        echo "<input type=\"radio\" name=\"special\" value=\"".c_ucitel."\" checked>u�itel</td></tr>";
      }
      else
      {
        echo "<table border=0><tr><td><input type=\"hidden\" name=\"special\" value=\"$skupina\"></td></tr>";
      }

      echo "<tr><td><P>&nbsp;</P><input type = \"submit\" value=\"odeslat zpr�vu\" name=\"odeslano\"></td></tr></table>";
            echo "<p><table border=1 bgcolor=\"#eeeeee\">";
      echo "<tr><td><font color=\"red\">Chcete-li, aby V� vzkaz byl form�tov�n do �iteln�j�� podoby, m��ete v textu zpr�vy pou��t n�sleduj�c� edita�n� zna�ky:</font><P>";
      echo "dal�� ��dek = &lt;BR&gt;<br>nov� odstavec s vynech�n�m 1 ��dku = &lt;P&gt;<br>tu�n� text (kolem zv�raz�ovan�ho textu) = &lt;B&gt;&lt;/B&gt;
      <br> podtr�en� text (kolem podtrh�van�ho textu) = &lt;U&gt;&lt;/U&gt;<P>Nap�. nap�ete-li text:<p><i>Z�tra se kon� &lt;B&gt;porada&lt;/B&gt; v u�ebn� 208,&lt;br&gt;dostavte se &lt;U&gt;v�ichni&lt;/U&gt;.&lt;P&gt;podpis</i><P>
      text se zobraz� n�sledovn�: <p><i>Z�tra se kon� <B>porada</B> v u�ebn� 208,<br>dostavte se <U>v�ichni</U>.<P>podpis</i><p></td></tr>";
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
                       <font color=red><b><center>Vzkaz se nepoda�ilo odeslat
		       </center></b></font><ul>$chyba</ul></td></tr></table><p>&nbsp;</p>";
else if($chyba=="ok") return "<P><table border=1 bgcolor=\"#e6e6e6\" cellpadding=15><tr><td><font color=red><b>Vzkaz byl �sp�n� odesl�n</b></font></td></tr></table></p>";
}



?>
