<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 3, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Oprava �daj� zam�stnanc�", $fullname, $kod);
  if($ucitel=="")
  {
    echo "<center><table border=0 cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">";
    /****** vedeni *****/
    NapisSkupinu("Veden�");
    $SQL = "select u.*, s.skupina
	    from ucitele u, skupiny s
            where s.id=u.id_skup and (s.id='2' or s.id='3')
	    order by id_skup, u.prijmeni, u.jmeno ";
    VypisZam($SQL);
    /****** ucitele *****/
    NapisSkupinu("U�itel�");
    $SQL = "select u.*, s.skupina
	    from ucitele u, skupiny s
            where s.id=u.id_skup and (s.id='1' or s.id='4' or s.id='5')
	    order by u.prijmeni, u.jmeno ";
    VypisZam($SQL);
    /****** spravni zamestnanci *****/
    NapisSkupinu("Spr�vn� zam�stnanci");
    $SQL = "select u.*, s.skupina
	    from ucitele u, skupiny s
            where s.id=u.id_skup and s.id>='6' and s.id<='18'
	    order by id_skup, u.prijmeni, u.jmeno ";
    VypisZam($SQL);
    /******* skolni jidelna *****/
    NapisSkupinu("�koln� j�delna");
    $SQL = "select u.*, s.skupina
	    from ucitele u, skupiny s
            where s.id=u.id_skup and s.id='19'
	    order by u.prijmeni, u.jmeno ";
    VypisZam($SQL);
    echo "</table></center>";
  }
  else
  {
    if($odeslano_udaje)
      {
      if(!(Neprazdny($jmeno))) $chyba .= "<li><i>Jm�no</i> je povinn� �daj.</li>";
      if(!(Neprazdny($prijmeni))) $chyba .= "<li><i>P��jmen�</i> je povinn� �daj.</li>";
      if(!(Neprazdny($zkratka))) $chyba .= "<li><i>Zkratka</i> je povinn� �daj.</li>";
      if(!(Neprazdny($kabinet))) $chyba .= "<li><i>Kabinet</i> je povinn� �daj.</li>";
      if(!(Neprazdny($tel1))) $chyba .= "<li><i>�koln� telefon</i> je povinn� �daj.</li>";
/*       OtestujFoto($foto1, $foto1_name, $foto1_size, $foto1_type, $chyba);
      OtestujFoto($foto2, $foto2_name, $foto2_size, $foto2_type, $chyba);
      OtestujFoto($foto3, $foto3_name, $foto3_size, $foto3_type, $chyba);*/
      if($chyba=="")
        {
        if(StrPos(StrToLower(" ".$url), "http://")==false and EReg_Replace(" ", "", $url)<>"") $url = "http://$url";

     /*   UlozFoto($foto1, $foto1_type, $foto1_nazev, 1);
        UlozFoto($foto2, $foto2_type, $foto2_nazev, 2);
        UlozFoto($foto3, $foto3_type, $foto3_nazev, 3);*/
        $SQL = "select * from ucitele where login = '$ucitel'";
        DB_select($SQL, $vystup, $pocet);
        $SQL_update = "update ucitele set titul_pred='$titul_pred',
                       titul_za='$titul_za', jmeno='$jmeno', 
		       prijmeni='$prijmeni', id_skup = '$skupina_id', 
		       zkratka='$zkratka', kabinet='$kabinet', 
		       tel1='$tel1', tel2='$tel2', mail1='$mail1', 
		       mail2='$mail2', url='$url', vyuc_oa='$vyucuje_OA', 
		       vyuc_vose='$vyucuje_VOSE', aktivni='$aktivni'
		       where login='$ucitel'";
        DB_exec($SQL_update);
        $chyba="ok";
        }
      }
    else
    {
      $SQL = "select * from ucitele where login='$ucitel'";
      if(DB_select($SQL, $vystup, $pocet))
      {
        if($zaznam=MySQL_fetch_array($vystup))
        {
          $login = $zaznam["login"];
          $jmeno = $zaznam["jmeno"];
          $prijmeni = $zaznam["prijmeni"];
          $skupina_id = $zaznam["id_skup"];
          $titul_pred = $zaznam["titul_pred"];
          $titul_za = $zaznam["titul_za"];
          $zkratka = $zaznam["zkratka"];
          $kabinet = $zaznam["kabinet"];
          $mail1 = $zaznam["mail1"];
          $mail2 = $zaznam["mail2"];
          $tel1 = $zaznam["tel1"];
          $tel2 = $zaznam["te2"];
          $url= $zaznam["url"];
          $vyucuje_OA = $zaznam["vyuc_oa"];
          $vyucuje_VOSE = $zaznam["vyuc_vose"];
          $aktivni = $zaznam["aktivni"];
        }
      }
    }


    if($chyba<>"") echo Hlaska($chyba, "�daje se nepoda�ilo opravit", "�daje byly �sp�n� opraveny");
    echo "<P><h3>Osobn� �daje:</h3>";
    echo "<FORM action=\"./ucitele_udaje_admin.php?kod=$kod&ucitel=$ucitel\" method=post><TABLE border=0>";
    echo "<tr><td>Jm�no:</td><td><input type=\"text\" value=\"$jmeno\" name=\"jmeno\">".Hvezdicka()."</td></tr>";
    echo "<tr><td>P��jmen�: </td><td><input type=\"text\" value=\"$prijmeni\" name=\"prijmeni\">".Hvezdicka()."</td></tr>";
    if($login=="") $login = "nen�";
    echo "<tr><td>Login u�itele: </td><td><i>$login</i></td></tr>";   
    echo "<tr><td>Funkce: </td><td>";
    if($skupina_id==c_admin)
      {
      $SQL = "select skupina from skupiny where id = '$skupina_id'";
      DB_select($SQL, $vystup, $pocet);
      if($zaz = MySQL_fetch_array($vystup)) echo Bunka($zaz["skupina"]);
      echo "<tr><td><input type=\"hidden\" name=\"skupina_id\" value=\"1\"></td></tr>";
      }
    else
      {
      echo "<select size=\"1\" name=\"skupina_id\">";
      $SQL = "select id, skupina from skupiny where skola='u'";
      if(DB_select($SQL, $vystup, $pocet))
        {
        $i = 0;
        while($zaznam=MySQL_fetch_array($vystup))
          {
          /*echo "<tr><td>id = ".$zaznam["id"]."</td></tr>";
          echo "<tr><td>skupina_id = ".$skupina_id."</td></tr>";*/
          if($zaznam["id"]==$skupina_id) $selected1 = "selected";
          else $selected1 = "";
          echo "<option value=\"".$zaznam["id"]."\" ".$selected1.">".$zaznam["skupina"];
          $i++;
          }
        }
      echo "</select>";
      }
    echo "</td></tr>";
    $checked1=""; $checked="";
    if($aktivni=="1") $checked1 = "checked";
    else $checked2 = "checked";
    
    echo "<tr><td>Tituly p�ed jm�nem: </td><td><input type=\"text\" value=\"$titul_pred\" name=\"titul_pred\"></td></tr>";
    echo "<tr><td>Tituly za jm�nem: </td><td><input type=\"text\" value=\"$titul_za\" name=\"titul_za\"></td></tr>";
    echo "<tr><td>Zkratka: </td><td><input type=\"text\" value=\"$zkratka\" name=\"zkratka\">".Hvezdicka()."</td></tr>";
    echo "<tr><td>Kabinet: </td><td><input type=\"text\" value=\"$kabinet\" name=\"kabinet\">".Hvezdicka()."</td></tr>";
    echo "<tr><td>Vyu�ovan� p�edm�ty na gymn�ziu: </td><td><input type=\"text\" name=\"vyucuje_OA\" value=\"$vyucuje_OA\" ></td></tr>";
    echo "<tr><td>Vyu�ovan� p�edm�ty na jaz. �kole: </td><td><input type=\"text\" name=\"vyucuje_VOSE\" value=\"$vyucuje_VOSE\"></td></tr>";
    echo "<tr><td>�koln� telefon (pouze klapka): </td><td><input type=\"text\" value=\"$tel1\" name=\"tel1\">".Hvezdicka()."</td></tr>";
    echo "<tr><td>Vlastn� telefon: </td><td><input type=\"text\" value=\"$tel2\" name=\"tel2\"></td></tr>";
    echo "<tr><td>�koln� e-mail: </td><td><input type=\"text\" value=\"$mail1\" name=\"mail1\">".c_mail."</td></tr>";
    echo "<tr><td>Vlastn� e-mail: </td><td><input type=\"text\" value=\"$mail2\" name=\"mail2\"></td></tr>";
    echo "<tr><td>Internetov� adresa vlastn�ch str�nek: </td><td><input type=\"text\" value=\"$url\" name=\"url\"></td></tr>";
    echo "<tr><td>Zam�stnanec: </td><td>
                  <input name=\"aktivni\" type=\"radio\" value=\"1\" $checked1> moment�ln� pracuje na �kole<br>
                  <input name=\"aktivni\"type=\"radio\" value=\"0\" $checked2> do�asn� na �kole nepracuje (nap�. z d�vodu mate�sk� dvolen�)</td></tr>";
/*    echo "<tr><td>�koln� foto: </td><td><input type=\"file\" name=\"foto1\" value=\"$foto1_name\"></td></tr>";
    echo "<tr><td>Foto 2: </td><td><input type=\"file\" name=\"foto2\" value=\"$foto2_name\"></td></tr>";
    echo "<tr><td>Foto 3: </td><td><input type=\"file\" name=\"foto3\" value=\"$foto3_name\"></td></tr>";*/
    echo "<tr><td colspan=2><small>(Hv�zdi�kou (".Hvezdicka().") jsou ozna�eny povinn� �daje.)</small></td></tr>";
    echo "</table>";
    echo "<input type=\"submit\" name=\"odeslano_udaje\" value=\"ode�li osobn� �daje\"></FORM>";
  }
  Konec();
}

function Bunka($text)
{
return "<table border=1 bgcolor=\"#e6e6e6\"><tr><td width=\"150\">$text</td></tr></table>";
}

function Neprazdny($text)
{
if($text=="") return false;
$i = 0;
while($text[$i]==" " and $i<StrLen($text)) $i++;
if($text[$i-1]==" ") return false;
else return true;
}

function OtestujFoto($soubor, $jmeno, $velikost, $typ, &$err)
{
if($soubor<>"")
  {
  if($velikost>102400)
    {
    $err .= "<li>Foto (<i>$soubor</i>) je p��li� velk� soubor (zvolte soubor do velikosti 100 kB).</li>";
    }
  if($typ<>"image/jpeg" and $typ<>"image/gif") $err .= "<li>Foto (<i>$soubor</i>) nem� vhodn� form�t (zvolte \"jpg\" nebo \"gif\"). </li>";
  }
}

function UlozFoto($soubor, $typ, &$novy_nazev, $poradi)
{
global $ucitel_login;
if($soubor<>"none")
  {
  if($typ=="image/gif") $novy_nazev = $ucitel_login.$poradi.".gif";
  else $novy_nazev = $ucitel_login.$poradi.".jpg";
  Copy($soubor, "./photos/".$novy_nazev);
  }
}

function UpravString(&$text)
{
$text = EReg_Replace(" ", "", $text);
$text = StrToUpper($text);
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
      $aktivni_text = "";
      if($zaznam["aktivni"]=="0") $aktivni_text = " (do�asn� na �kole nepracuje)";
      echo "<tr class=\"tabulka\" id=\"tabulka\" onMouseOver=\"styl();\" onMouseOut=\"styl();\"><td width=\"30%\">&nbsp;</td><td width=\"50\"><a class=\"seznam_black\" href=\"./ucitele.php?kod=$kod&ucitel=".$zaznam["login"]."\">".$zaznam["zkratka"]."</a></td><td>
               <a class=\"seznam\" href=\"./ucitele_udaje_admin.php?kod=$kod&ucitel=".$zaznam["login"]."\">".
               Sestav_jmeno($zaznam["titul_pred"], $zaznam["jmeno"],
               $zaznam["prijmeni"],$zaznam["titul_za"])."</a></td><TD>".Text_alter("", $aktivni_text)."</TD></tr>";
    }
  }
}




?>
