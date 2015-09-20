<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 3, $fullname, $login, $chyba))
  {
  NoCACHE();
  Hlavicka("P�id�n� nov�ho zam�stnance do datab�ze", $fullname, $kod);
  if($chyba=="ok" or !($chyba))
  {
  if(LDAP_spojeni($ds))
  {
    $dn="ou=KABINET, o=JAKUB";
    $filtr = "sn=*";
    $vysledek = @LDAP_search($ds, $dn, $filtr, Array("cn", "sn", "givenName", "mail"));
    if($vysledek)
    {
      $polozky = @LDAP_get_entries($ds, $vysledek);
      $i=0;
      if($polozky)
      {
        for($i=0;$i<$polozky["count"];$i++)
        {
          $j=3;
          $login_uc="";
          if(substr($polozky[$i]["dn"], 0, 3)=="cn=")
          {
            while($polozky[$i]["dn"][$j]<>",")
            {
              $login_uc .= $polozky[$i]["dn"][$j];
              $j++;
            }
          }
          $osoba[$i] = new Cosoba($login_uc,
                                  $polozky[$i]["givenname"][0],
                                  $polozky[$i]["sn"][0],
                                  $polozky[$i]["mail"][0]);
        }
        $databaze = Napln_ucitele();
        $rozdil = Rozdil($osoba, $databaze);
        if(count($rozdil)==0)
        {
          echo "V informa�n�m syst�mu jsou nyn� vlo�eny z�kladn� �daje v�ech u�itel�, kte�� maj�
                vytvo�en ��et v Novellu.";
        }
      }
    }
    $dn1="ou=SPRAVA, o=JAKUB";
    $filtr1 = "sn=*";
    $vysledek1 = @LDAP_search($ds, $dn1, $filtr1, Array("cn", "sn", "givenName", "mail"));
    if($vysledek1)
    {
      $polozky1 = @LDAP_get_entries($ds, $vysledek1);

      if($polozky1)
      {

        for($i=0;$i<$polozky1["count"];$i++)
        {
          $j=3;
          $login_uc1="";
          if(substr($polozky1[$i]["dn"], 0, 3)=="cn=")
          {
            while($polozky1[$i]["dn"][$j]<>",")
            {
              $login_uc1 .= $polozky1[$i]["dn"][$j];
              $j++;
            }
          }
          $osoba1[$i] = new Cosoba($login_uc1,
                                  $polozky1[$i]["givenname"][0],
                                  $polozky1[$i]["sn"][0],
                                  $polozky1[$i]["mail"][0]);
        }
        $databaze = Napln_ucitele();
        $rozdil1 = Rozdil($osoba1, $databaze);
        if(count($rozdil1)==0)
        {
          echo "V informa�n�m syst�mu jsou nyn� vlo�eny z�kladn� �daje v�ech u�itel�, kte�� maj�
                vytvo�en ��et v Novellu.";
        }
      }
      for($i=0;$i<count($rozdil1);$i++)
      {
        $rozdil[count($rozdil)]=$rozdil1[$i];
      }
    }
    else
      {
      echo "Nebyl nalezen ��dn� u�ivatel s�t�. <br>Kontaktujte pros�m administr�tora.";
      }
    LDAP_odpojeni($ds);
    }
  else
    {
    echo "<b><font color=red>Nepoda�ilo se spojit s LDAP serverem.<br>Kontaktujte pros�m administr�tora.</font></b>";
    }
  }
  if($odeslano_login)
    {
    $pom = explode("|", $ucitel_login);
    $ucitel_login = $pom[0];
    $id = $pom[1];
    $jmeno = $rozdil[$id]->jmeno;
    $prijmeni = $rozdil[$id]->prijm;
    if ($mail1=="") $mail1 = $rozdil[$id]->email;
    $skupina_id=c_ucitel;
    for($i=0;$i<count($rozdil);$i++)
      {
      if(StrToUpper($ucitel_login)==StrToUpper($rozdil[$i]->login))
      {
        $selected[$i]="selected";
      }
      else
      {
        $selected[$i]="";
      }
    }
  }
  else
  {
    if($odeslano_udaje)
    {
      $chyba="";
      $pom = explode("|", $ucitel_login);
      $ucitel_login = $pom[0];
      $id = $pom[1];
      for($i=0;$i<count($rozdil);$i++)
      {
        if(StrToUpper($ucitel_login)==StrToUpper($rozdil[$i]->login))
        {
          $selected[$i]="selected";
        }
        else
        {
          $selected[$i]="";
        }
      }
      if(!(Neprazdny($jmeno))) $chyba .= "<li><i>Jm�no</i> je povinn� �daj.</li>";
      if(!(Neprazdny($prijmeni))) $chyba .= "<li><i>P��jmen�</i> je povinn� �daj.</li>";
      if(!(Neprazdny($zkratka))) $chyba .= "<li><i>Zkratka</i> je povinn� �daj.</li>";
      if(!(Neprazdny($kabinet))) $chyba .= "<li><i>Kabinet</i> je povinn� �daj.</li>";
      if(!(Neprazdny($tel1))) $chyba .= "<li><i>�koln� telefon</i> je povinn� �daj.</li>";

   /*   OtestujFoto($foto1, $foto1_name, $foto1_size, $foto1_type, $chyba);
      OtestujFoto($foto2, $foto2_name, $foto2_size, $foto2_type, $chyba);
      OtestujFoto($foto3, $foto3_name, $foto3_size, $foto3_type, $chyba);*/
      if($chyba=="") if(!(@MkDir(c_files."files/".StrToLower($ucitel_login), 509)))
        $chyba .= "<li>nepoda�ilo se vytvo�it adres�� soubor�</li>";
      if($chyba=="")
      {
        if(StrPos(StrToLower(" ".$url), "http://")==false and EReg_Replace(" ", "", $url)<>"") $url = "http://$url";
   /*     UlozFoto($foto1, $foto1_type, $foto1_nazev, 1);
        UlozFoto($foto2, $foto2_type, $foto2_nazev, 2);
        UlozFoto($foto3, $foto3_type, $foto3_nazev, 3);*/
        $SQL = "insert into ucitele (login, jmeno, prijmeni, titul_pred,
                titul_za, zkratka, kabinet, tel1, tel2, mail1, mail2, url,
		id_skup, vyuc_oa, vyuc_vose)
                values ('".StrToLower($ucitel_login)."', '$jmeno', '$prijmeni', '$titul_pred',
                '$titul_za', '$zkratka', '$kabinet', '$tel1', '$tel2',
                '$mail1', '$mail2', '$url',
		'$skupina_id', '$vyucuje_oa', '$vyucuje_vose')";
        DB_exec($SQL);
        $chyba = "ok";
      }
    }
    else
      {
      $selected[0]="selected";
      $skupina_id=c_ucitel;
      $jmeno = $rozdil[0]->jmeno;
      $prijmeni = $rozdil[0]->prijm;
      if ($mail1=="") $mail1 = $rozdil[0]->email;
      }
    }
  if($chyba<>"") echo Hlaska($chyba, "Z�znam se nepoda�ilo ulo�it do datab�ze", "Z�znam byl �sp�n� ulo�en do datab�ze");

  echo "<table border=0><FORM action=\"./ucitele_novy.php?kod=$kod\" method=post><TABLE border=0>";
  echo "<tr><td>Login:</td><td>";
  echo "<select size=\"1\" name=\"ucitel_login\">";
  for($i=0;$i<count($rozdil);$i++)
     {
     echo "<option value=\"".$rozdil[$i]->login."|$i|"."\" ".$selected[$i].">".$rozdil[$i]->login;
     }
  echo "</select>";
  echo "</td></tr>";
  echo "<tr><td><input type=\"submit\" name=\"odeslano_login\" value=\"ode�li login u�itele\"></td></tr></FORM>";
  echo "<tr><td>&nbsp;<P></td></tr>";
  if(($chyba<>"" and $chyba<>"ok") or $odeslano_login)
    {
    echo "<tr><td><P><h3>Osobn� �daje:</h3></td></tr>";
    echo "<FORM action=\"./ucitele_novy.php?kod=$kod\" method=post enctype=\"multipart/form-data\">";
    echo "<tr><td>Jm�no:</td><td><input type=\"text\" value=\"$jmeno\" name=\"jmeno\">".Hvezdicka()."</td></tr>";
    echo "<tr><td>P��jmen�: </td><td><input type=\"text\" value=\"$prijmeni\" name=\"prijmeni\">".Hvezdicka()."</td></tr>";
    echo "<tr><td>Funkce:</td><td>";
    echo      "<select size=\"1\" name=\"skupina_id\">";
    $SQL = "select id, skupina from skupiny where skola='u' order by id";
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
    echo "</td></tr>";
    echo "<tr><td>Tituly p�ed jm�nem: </td><td><input type=\"text\" name=\"titul_pred\" value=\"$titul_pred\"></td></tr>";
    echo "<tr><td>Tituly za jm�nem: </td><td><input type=\"text\" name=\"titul_za\" value=\"$titul_za\"></td></tr>";
    echo "<tr><td>Zkratka: </td><td><input type=\"text\" name=\"zkratka\" value=\"$zkratka\">".Hvezdicka()."</td></tr>";
    echo "<tr><td>Kabinet: </td><td><input type=\"text\" name=\"kabinet\" value=\"$kabinet\">".Hvezdicka()."</td></tr>";
    echo "<tr><td>Vyu�ovan� p�edm�ty na OA: </td><td><input type=\"text\" name=\"vyucuje_OA\" value=\"$vyucuje_OA\"></td></tr>";
    echo "<tr><td>Vyu�ovan� p�edm�ty na VO�E: </td><td><input type=\"text\" name=\"vyucuje_VOSE\" value=\"$vyucuje_VOSE\"></td></tr>";
    echo "<tr><td>�koln� telefon (pouze klapka): </td><td><input type=\"text\" value=\"$tel1\" name=\"tel1\">".Hvezdicka()."</td></tr>";
    echo "<tr><td>Vlastn� telefon: </td><td><input type=\"text\" name=\"tel2\" value=\"$tel2\"></td></tr>";
    echo "<tr><td>�koln� e-mail: </td><td><input type=\"text\" value=\"$mail1\" name=\"mail1\">".c_mail."</td></tr>";
    echo "<tr><td>Vlastn� e-mail: </td><td><input type=\"text\" name=\"mail2\" value=\"$mail2\"></td></tr>";
    echo "<tr><td>Internetov� adresa vlastn�ch str�nek: </td><td><input type=\"text\" name=\"url\" value=\"$url\"></td></tr>";
   /* echo "<tr><td>�koln� foto: </td><td><input type=\"file\" name=\"foto1\" value=\"$foto1_name\"></td></tr>";
    echo "<tr><td>Foto 2: </td><td><input type=\"file\" name=\"foto2\" value=\"$foto2_name\"></td></tr>";
    echo "<tr><td>Foto 3: </td><td><input type=\"file\" name=\"foto3\" value=\"$foto3_name\"></td></tr>";*/
    echo "<tr><td colspan=2><small>(Hv�zdi�kou (".Hvezdicka().") jsou ozna�eny povinn� �daje.)</small></td></tr>";
    echo "<tr><td><input type=\"hidden\" value=\"".$ucitel_login."\" name=\"ucitel_login\"></td></tr>";
    echo "<tr><td><input type=\"submit\" name=\"odeslano_udaje\" value=\"ode�li osobn� �daje\"></td></tr></FORM>";
    }
  echo "</table>";
  }
Konec();

function Rozdil($ucitele_ldap, $ucitele_db)
{
$rozdil="";
for($i=0;$i<count($ucitele_ldap);$i++)
  {
  $j=0;
  while((StrToUpper($ucitele_ldap[$i]->login)<>StrToUpper($ucitele_db[$j])) and ($j<count($ucitele_db))) $j++;
  if($j==count($ucitele_db))
    {
    $rozdil[] = new Cosoba($ucitele_ldap[$i]->login,
                           $ucitele_ldap[$i]->jmeno,
                           $ucitele_ldap[$i]->prijm,
                           $ucitele_ldap[$i]->email);
    }
  }
return $rozdil;
}

function Napln_ucitele()
{
$SQL = "select * from ucitele order by login";
if(DB_select($SQL, $vystup, $pocet)) 
  while($zaznam=MySQL_fetch_array($vystup)) $clovek[] = $zaznam["login"];
return $clovek;
}

function Bunka($text)
{
return "<table border=1 bgcolor=\"#e6e6e6\"><tr><td width=\"150\">$text&nbsp;</td></tr></table>";
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
if($soubor<>"none")
  {
  if($velikost>102400)
    {
    $err .= "<li>Foto (<i>$jmeno</i>) je p��li� velk� soubor (zvolte soubor do velikosti 100 kB).</li>";
    }
  if($typ<>"image/jpeg" and $typ<>"image/gif") $err .= "<li>Foto (<i>$jmeno</i>) nem� vhodn� form�t (zvolte \"jpg\" nebo \"gif\"). </li>";
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

?>
