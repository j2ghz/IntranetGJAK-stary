<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 5, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Ulo�en� souboru", $fullname, $kod);
  echo "<P>";
  $SQL = "select * from ucitele where login='$login'";
  DB_select($SQL, $vystup, $pocet);
  if($pocet==0) echo "<b><font color=\"red\"><p>Va�e z�kladn� �daje je�t� nebyly ulo�eny do datab�ze! <BR>Bez z�kladn�ch �daj� nelze ukl�dat soubory na server.<P>Kontaktujte pros�m administr�tora nebo z�stupce �editele.</b></font>";
  else
  {
    echo "<center>".Hlaska($chyba, "Soubor se nepoda�ilo ulo�it", "Soubor byl �sp�n� ulo�en")."</center>";
    if($chyba<>"ok")
    {
      echo "<form action=\"./soubory_send.php?kod=$kod\" method=post enctype=\"multipart/form-data\"><table>";
      echo "<tr><td colspan=3><b><".c_font.">Skupiny u�ivatel�, kter�m je soubor ur�en (ro�n�k):</font></b>";
      echo "<br><font color=gray><small>- zvol�te-li <i>u�itel</i>, soubor se zobraz� i �editeli, z�stupc�m a administr�tor�m";
      echo "<br>- jednotliv� skupiny maj� p�ednost p�ed volbou <i>v�ichni</i> i <i>v�ichni studenti</i>";
      echo "<br>- podobn� <i>v�ichni studenti</i> m� p�ednost p�ed volbou <i>v�ichni</i>";
      echo "<br>- nezvol�te-li ��dnou skupinu, bude soubor viditeln� v�em</li></small></font></td></tr>";
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

      echo "<p><table border=0><tr><td><b><".c_font.">T��da:</font></b>";
      echo "<br><font color=gray><small>- uv�d�jte pro lep�� orientaci student�
            v souborech <BR>";
      echo "</small></font></td></tr>";
      echo "<tr><td><input type=\"text\" name=\"trida\" value=\"$trida\"></td></tr></table>";
      echo "<p><table border=0><tr><td><b><".c_font.">P�edm�t:</font></b> ";
      echo "</td></tr>";
      echo "</td></tr><tr><td><input type=\"text\" name=\"predmet\" value=\"$predmet\"></td></tr></table>";
      echo "<p><table border=0><tr><td><b><".c_font.">Soubor k odesl�n�:</td></tr>";
      echo "<tr><td><input type=\"file\" name=\"soubor\" value=\"$soubor\"></td></tr></table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Nov� n�zev souboru:</b>";
      echo "<br><font color=gray><small>- nov� n�zev souboru nesm� b�t pr�zdn� a m��e obsahovat
                      pouze tyto znaky:<br>a-z, A-Z, 0-9, ., _, -,
		      <br>tj. nesm�te pou��vat diakritiku a mezery (nap�. nam�sto \"vzorov� p��klady\" pi�te \"vzorove_priklady\")</small></td></tr>";
      echo "<tr><td><input type=\"textbox\" name=\"nazev\" value=\"$nazev\"></td></tr></table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Stru�n� popis:</td></tr>";
      echo "<tr><td><textarea name=\"popis\" value=\"$popis\" rows=3 cols=20></textarea></td></tr></table>";

      echo "<p><table border=0><tr><td><b><".c_font.">Platnost souboru do:</b>";
      echo "<br><font color=gray><small>- datum pi�te ve form�tu <i>den.m�s�c.rok</i> bez mezer, rok na 4 ��slice</small></font>";
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
