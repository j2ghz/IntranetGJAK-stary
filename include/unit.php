<?
include("./include/konstanty.php");

function NoCACHE()
{
 header("Pragma:no-cache");
 header("Cache-Control:no-cache");
 header("Expires: ".GMDate("D, d M Y H:i:s")." GMT");
}
//barvy 3d9973 398e79 578297 78b3d1 seda f1f1f1




class Cosoba
{
var $login, $jmeno, $prijm, $email;

    function Cosoba($login, $jmeno, $prijm, $email)
    {
    $this -> login = $login;
    $this -> jmeno = $jmeno;
    $this -> prijm = $prijm;
    $this -> email = $email;
    }
}

class Cskupina
{
var $id, $skupina;

    function Cskupina($id, $skupina)
    {
    $this -> id = $id;
    $this -> skupina = $skupina;
    }
}

class Cvzkaz
{
var $id, $jmeno, $prijmeni, $skup, $trida, $predmet, $datum, $platnost_do, $text;

    function Cvzkaz($id, $jmeno, $prijmeni, $skup, $trida, $predmet, $datum, $platnost_do, $text)
    {
    $this -> id = $id;
    $this -> jmeno = $jmeno;
    $this -> prijmeni = $prijmeni;
    $this -> skup = $skup;
    $this -> trida = $trida;
    $this -> predmet = $predmet;
    $this -> datum = $datum;
    $this -> platnost_do = $platnost_do;
    $this -> text = $text;
    }

    function Cvzkaz_porjmena($a, $b)
    {
      if($a->prijmeni == $b->prijmeni)
      {
        if($a-jmeno == $b->jmeno) return 0;
        else return ($a->jmeno > $b->jmeno) ? 1 : -1;
      }
      else return ($a->prijmeni > $b->prijmeni) ? 1 : -1;
    }

    function Cvzkaz_porskup($a, $b)
    {
      if($a->skup == $b->skup) return 0;
      else return ($a->skup > $b->skup) ? 1 : -1;
    }

    function Cvzkaz_portridy($a, $b)
    {
      if($a->trida == $b->trida) return 0;
      else return ($a->trida > $b->trida) ? 1 : -1;
    }

    function Cvzkaz_porpredm($a, $b)
    {
      if($a->predmet == $b->predmet) return 0;
      else return ($a->predmet > $b->predmet) ? 1 : -1;
    }

    function Cvzkaz_pordatum($a, $b)
    {
      if($a->datum == $b->datum) return 0;
      else return ($a->datum > $b->datum) ? 1 : -1;
    }
}


/*adminovi vypise komentar*/
function Vypis($skup, $text)
{
  if($skup==1) echo "<br>$text<br>";
}

function Datum($datum, $minuty=1)
{
  if($datum=="0000-00-00" or $datum=="0000-00-00 00:00:00") return "";
  $pom = explode("-", $datum);
  $pom1 = explode(" ", $pom[2]);
  if($minuty==1)
  {
    $minuty = explode(":", $pom1[1]);
    $datum = $pom1[0].".&nbsp;".$pom[1].".&nbsp;".$pom[0]." ".$minuty[0].":".$minuty[1];
  }
  else
  {
    $datum = $pom1[0].".&nbsp;".$pom[1].".&nbsp;".$pom[0];
  }
  return $datum;
}

function Datum_bez_mezer($datum, $minuty=1)
{
  if($datum=="0000-00-00" or $datum=="0000-00-00 00:00:00") return "";
  $pom = explode("-", $datum);
  $pom1 = explode(" ", $pom[2]);
  if($minuty==1)
  {
    $minuty = explode(":", $pom1[1]);
    $datum = $pom1[0].".".$pom[1].".".$pom[0]." ".$minuty[0].":".$minuty[1];
  }
  else
  {
    $datum = $pom1[0].".".$pom[1].".".$pom[0];
  }
  return $datum;
}

function Datum_den($datum, &$den, $minuty=1)
{
  if($datum=="0000-00-00" or $datum=="0000-00-00 00:00:00") return "";
  $dentydne = date("w", strtotime($datum));
  $den = $dentydne;
  
  $pom = explode("-", $datum);
  $pom1 = explode(" ", $pom[2]);
  if($minuty==1)
  {
    $minuty = explode(":", $pom1[1]);
    $datum = $pom1[0].". ".$pom[1].". ".$pom[0]." ".$minuty[0].":".$minuty[1];
  }
  else
  {
    $datum = $pom1[0].". ".$pom[1].". ".$pom[0];
  }
  switch ($dentydne)
  {
    case 0: $datum .= "&nbsp;&nbsp;Ne";
    break;
    case 1: $datum .= "&nbsp;&nbsp;Po";
    break;
    case 2: $datum .= "&nbsp;&nbsp;Út";
    break;
    case 3: $datum .= "&nbsp;&nbsp;St";
    break;
    case 4: $datum .= "&nbsp;&nbsp;Èt";
    break;
    case 5: $datum .= "&nbsp;&nbsp;Pá";
    break;
    case 6: $datum .= "&nbsp;&nbsp;So";
    break;
  }
  return $datum;
}

function Mesic($mesic)
{
  switch($mesic)
  {
    case 1:
         return "leden";
         break;
    case 2:
         return "únor";
         break;
    case 3:
         return "bøezen";
	 break;
    case 4:
	 return "duben";
	 break;
    case 5:
         return "kvìten";
	 break;
    case 6:
         return "èerven";
         break;
    case 7:
         return "èervenec";
         break;
    case 8:
         return "srpen";
         break;
    case 9:
         return "záøí";
         break;
    case 10:
         return "øíjen";
         break;
    case 11:
         return "listopad";
         break;
    case 12:
         return "prosinec";
         break;
  }
}

function Den_max($mesic, $rok)
{
  if($mesic==1 or $mesic==3 or $mesic==5 or $mesic==7 or $mesic==8 or $mesic==10 or $mesic==12) return 31;
  if($mesic==4 or $mesic==6 or $mesic==9 or $mesic==11) return 30;
  if($mesic==2)
    if($rok%4==0) return 29;
    else return 28;
}

function Cas($cas)
{
  if ($cas=="00:00:00") return "";
  return substr($cas, 0, 5);
}


function Datum_datab($datum)
{
  SetType($datum, string);
  $datum = EReg_Replace(" ", "", $datum);
  if($datum<>"")
  {
    $pom = explode(".", $datum);
    $datum = $pom[2]."-".$pom[1]."-".$pom[0];
  }
  return $datum;
}


/*function Datum_datab($datum)
{
  SetType($datum, "string");
  echo "<br>datum s mezerami = $datum";
  $pozice = "0";
  for($i=0;$i<strlen($datum);$i++)
  {
    echo "<br>datum $i = \"".$datum[$i]."\"";
    if($datum[$i]<>chr(20))
    {
      echo "<br>pridano do retezce";
      $pom_datum .= $datum[$i];
    }
  }
  echo "<br>datum bez mezer = $datum";
  if($datum<>"")
  {
    $pom = explode(".", $pom_datum);
    $vysl = $pom[2]."-".$pom[1]."-".$pom[0];
  }
  return $vysl;
}  */


function Cas_datab($cas)
{
  $cas = EReg_Replace(" ", "", $cas);
  if($cas=="") return "NULL";
  else
  {
    $pom = explode(".",$cas);
    if(count($pom)>1) $cas=$pom[0].":".$pom[1];
    return $cas;
  }
}

function Napis($text)
{
  echo "<br>$text = <<".$$text.">><br>";
}

function Hvezdicka()
{
  return "<font color=red><b>*</b></font>";
}

function Hlaska($chyba, $chyba_info, $ok_info)
{
  if($chyba<>"ok" and $chyba<>"") return "<P><table border=1 bgcolor=\"#e6e6e6\" cellpadding=15><tr><td>
                         <font color=red><b><center>$chyba_info
                         </center></b></font><ul>$chyba</ul></td></tr></table><p>&nbsp;</p>";
  else if($chyba=="ok") return "<P><table border=1 bgcolor=\"#e6e6e6\" cellpadding=15><tr>
  				<td><font color=\"#5555bb\"><b><center>$ok_info</center></b></td>
				</tr></table><p>&nbsp;</p>";
}

/*** pokud je text prazdny, vrati modre, kurzivou alternativni text ***********/
function Text_alter($text, $alter)
{
  if($text=="") return "<i><font color=\"#5555bb\">$alter</font></i>";
  else return $text;
}

/*** nedokoncena funkce pro vypis udaju o ucitelich ***************************/
function Vynech($nazev, $pred, $obsah, $za)
{
  $obsah = EReg_Replace(" ", "", $obsah);
  if($obsah<>"") echo "<tr><td valign=\"top\">$nazev: </td><td>".$pred.$obsah.$za."</td></tr>";
}

/*** prevede hodnotu v B na kB/MB ******************************/
function Prevod($hodnota)
{
  if($hodnota>1024)
  {
    $hodnota /= 1024;
    if($hodnota>1024) return round($hodnota/1024,2)." MB";
    else return round($hodnota,2)." kB";
  }
  else return $hodnota." B";
}

function Podnadpis($text)
{
  echo "<table border=\"0\" width=\"100%\"><TR><TD bgcolor=\"".c_pozadi_nadpis."\"><font color=\"#610D00\" size=\"4\"><b>$text</b></font></td></tr></table>";
}

/*function Tlacitka($kod, $stranka, $pole_parametru, $pole_tlacitek, $promackle_tlacitko)
{
  echo "<table cellpadding=\"1\" cellspacing=\"1\"><tr>";
  for($i=0;$i<count($pole_tlacitek);$i++)
  {
    if($i==$promackle_tlacitko-1)
      {
        $trida_tab = "tlacitka_promackla";
        $trida_odkaz = "tlacitka_odkaz";
      }
      else
      {
        $trida_tab = "tlacitka";
        $trida_odkaz = "tlacitka_odkaz";
      }
    echo "<td valign=\"middle\" class=\"$trida_tab\"><A href=\"".$stranka."?kod=$kod&".$pole_parametru[$i]."\" class=\"$trida_odkaz\">".$pole_tlacitek[$i]."</a></td>";
  }
  echo "</tr></table>";
  echo "<P>";
}
*/
//*******************************************************************
// Hlavicky 8bbccf
//*******************************************************************
function Hlavicka($stranka="", $fullname, $kod, $nazev="", $pole_vyberu="", $pole_tlacitek="", $promackle_tlacitko="")
/* pekne pozadi do boku #D5D5E0 #DFD5CF #D5CBC5
        pismo pro odkazy link=\"#0000B7\" vlink=\"#0000B7\" alink=\"#0000B7\"
	background = \"./images/piskovec_d1.gif\"
	*/
{ /*global $session;*/
  $pom = explode("|", $kod);
  $login = $pom[0];
  $skupina = $pom[1];
  ?>

  <HTML><HEAD>
         <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-2">
         <link rel="stylesheet" type="text/css" href="./styly.css">
         <script>
            function styl()
            {
              if (tabulka.style.backgroundColor=='#F3E9E3')
              {
                tabulka.style.backgroundColor='transparent';
              }
              else
              {
                tabulka.style.backgroundColor='#F3E9E3';
              }
            }
         </script>

       </HEAD>
       <BODY bgcolor="#BDD6EB"  link="#0000B7" vlink="#0000B7" alink="#0000B7"> <?
 /* echo "     <TABLE cellspacing=0 cellpadding=0 width=100% border=0>";
  echo "       <TR><TD ROWSPAN=5 valign=\"top\" bgcolor=\"".c_pozadi."\" width=\"150\">";
  echo "               <TABLE cellpadding=5 cellspacing=2 border=0 BGCOLOR=\"".c_pozadi."\">
                              <TR><TD>";
                                      include (c_Cesta2."include/odkazy.php");
  echo "                          </TD>
                              </TR>
                       </TABLE>";
  echo "           </TD>
                   <td width=\"30px\" background=\"./background/vazba4.gif\">&nbsp;</td>";
  echo "             <TD valign=top background = ./background/sesit.gif>
	              <TABLE border=0 height=1% width=100%>
                          <TR>
	           	      <TD valign = middle>
            	              </TD>
                   	      <TD valign=middle><B><FONT size=4>
                       		  <center>Informaèní systém Gymnázia Jana Ámose Komenského v Uherském Brodì</CENTER></FONT></B>
		   	      </TD>
                   	      <TD valign = middle><IMG align = left SRC=./images/logo.gif ALT=logo GJAK>

		   	      </TD>
                          </TR>" ;

                    
            echo "     <TR><TD colspan=\"3\" bgcolor=\"".c_pozadi_nadpis."\">";
          if($stranka!="") echo "  <CENTER><FONT size=\"5\"><B>$stranka</B></FONT></CENTER>";
          echo "         </TD>
                         </TR></TABLE><p>"; */
                         
  echo "  <table cellspacing=\"0\" cellpadding=\"0\" margin=\"0\" width=99% border=0>";
	echo "<tr>";
          	echo "<td bgcolor=".c_pozadi." style=\"text-valign:middle;\" valign=\"middle\" bgcolor=\"".c_pozadi."\" colspan=\"3\" height=\"69px\"><IMG SRC=\"./images/logo.gif\" ALT=\"logo ¹koly\" style=\"margin: 0px; border: none; \">&nbsp;&nbsp;&nbsp;<span style=\"text-valign:top; font-family:Arial, sans-serif; font-weight:bold; font-size:14px; \">Informaèní systém Gymnázia Jana Ámose Komenského v Uherském Brodì</span>";  //
	          //echo "	<td colspan=\"2\" valign=\"middle\" style=\"padding-left:7;\"><B><FONT size=\"3\"><center>Informaèní systém Obchodní akademie T. Bati a VO©E Zlín</CENTER></FONT></B> <HR size=\"1\" color=\"#0C1072\">";
            echo " </td>";
	echo "</tr>";
	echo "<tr style=\"margin:0;\">";
          	echo "<td bgcolor=\"#49559f\" colspan=\"3\" height=\"30px\">";
          
	          //echo "	<td colspan=\"2\" valign=\"middle\" style=\"padding-left:7;\"><B><FONT size=\"3\"><center>Informaèní systém Obchodní akademie T. Bati a VO©E Zlín</CENTER></FONT></B> <HR size=\"1\" color=\"#0C1072\">";
              if($stranka!="") echo "<div style=\"font-size:20px;  font-family: Arial, Helvetica, sans-serif; text-align:left; color:#e9eaf3;\" >&nbsp;$stranka</div>";
            echo " </td>";
	echo "</tr>";
	echo "<tr>";
	     echo "	<td rowspan=\"2\" width=\"200\" valign=\"top\" style=\"padding-right:5;\"><p>&nbsp;</p>";
         include (c_Cesta2."include/odkazy.php");
       echo "  </td>";
        //echo "  <table cellspacing=\"0\" cellpadding=\"0\" margin=\"0\" width=\"100%\" height=\"2050\" border=1 bordercolor=blue>";
       echo "	  	<td height=\"28 px\">&nbsp;</td>";
	     echo "	    <td>&nbsp;";
       Tlacitka($kod, $nazev, $pole_vyberu, $pole_tlacitek, $promackle_tlacitko);
       echo "     </td>";
  echo "       </tr>";
  
	echo "       <tr>";
	     echo "	   <td width=\"24px\" background=\"./images/vazba6.gif\">&nbsp;</td>";
	     echo "	   <td valign=top style=\"border-right: 1px solid #ABABAB; background-color: #E9EAF3; padding-left:20; padding-top:10;\">&nbsp;";
//pokracuje hlavni telo stranky
}
function Konec()
{
  echo "</TD></TR></TABLE><br>
        <small><div align=\"right\">V pøípadì problémù <A HREF = \"mailto:viktoryn@gjak.cz\">pi¹te správci ISu</a>.&nbsp;&nbsp;&nbsp;&copy; Lenka Volfová, 2005</small></BODY></HTML>";
}
/*function Konec()
{
  echo "</TD></TR><TR><td background=\"./background/vazba6.gif\">&nbsp;</td><TD  background = \"./background/sesit.gif\" colspan=\"2\" align=\"right\" valign=\"bottom\">
        <HR><small>v pøípadì problémù <A HREF = \"mailto:viktoryn@gjak.cz\">pi¹te správci ISu</a></small></TD></TR></TD></TR></TABLE></BODY></HTML>";
}
*/

function Hlavicka_menu()
{
  echo "<HTML><HEAD><meta http-equiv=\"Content-Type\"
          content=\"text/html; charset=ISO-8859-2\"></HEAD>";
  echo "<body bgcolor=\"#77aabb\">";
}


//*******************************************************************
//vraci cestu ke konfiguracnim souborum
//*******************************************************************
function SecureFiles()
{
  return "/etc/securefiles";
}

//*******************************************************************
//vraci cestu k logovemu souboru
//*******************************************************************
function LogFiles()
{
  return "../logfiles";
}

// ******************************************************************
// ******************************************************************
// prace s databazi

//*******************************************************************
// otevreni spojeni s databazi
//*******************************************************************

function DB_spojeni ($db, &$error, &$spojeni)
{
$error = "";
$file = securefiles()."/".$db;
if (!file_exists($file))
{
   $error = "konfiguracni soubor pro databazi ".$db." nenalezen";
   return false;
}
else
{
   $fo = fopen($file, "r");
   if (!$fo)
   {
      $error = "nelze otevrit konfiguracni soubor pro databazi".$db;
      return false;
   }
   else
   {
      $record = fgets ($fo, 30);
      fclose($fo);
      list($user, $passwd, $bogus) = explode (":", $record);
//      $spojeni = MySQL_Connect("localhost", "uziv", "68t8g6er867");
//      echo "<P>uzivatel $user - $passwd";
      $spojeni = MySQL_Connect("localhost", $user, $passwd);
      if (!$spojeni)
      {
         $error = "pristup odmitnut";
         return false;
      }
      if (!MySQL_Select_DB($db))
      {
         $error = "nelze zpristupnit databazi ".$db;
         MySQL_close($spojeni);
         return false;
      }
   }
}
return true;
}

//*******************************************************************
// uzavreni spojeni s databazi
//*******************************************************************

function DB_odpojeni(&$spojeni)
{
MySQL_close($spojeni);
}

/* *******************************************************************
   posle select na databazi a vrati true pri uspechu (+vysledek dotazu)
  *******************************************************************
*/

function DB_select ($SQL, &$vystup, &$pocet_zaznamu)
{	//echo "<P>SQL = ".$SQL."<P>";
	$funguje = false;
	if (!(DB_spojeni(c_Databaze, $chyba, $spojeni))):
		echo "<FONT COLOR=red><B><P>Do¹lo k chybì pøi spojení s databází.
			($chyba)<P></B></FONT><BR>\n";
		return false;
	endif;
	$vysledek = MySQL_Query($SQL);
	if (!$vysledek):
		echo "<FONT COLOR=red><B><P>Do¹lo k chybì pøi zpracování dotazu v
				databázi.<P></B></FONT><BR>\n";
	else:
        	$funguje = true;
		$vystup = $vysledek;
                $pocet_zaznamu = MySQL_num_rows($vystup);
        endif;
	DB_odpojeni($spojeni);
	return $funguje;
}

function DB_exec ($SQL)
{	//echo "<P>SQL = ".$SQL."<P>";
	$funguje = false;
	if (!(DB_spojeni(c_Databaze, $chyba, $spojeni))):
		echo "<FONT COLOR=red><B><P>Do¹lo k chybì pøi spojení s databází.
			($chyba)<P></B></FONT><BR>\n";
		return false;
	endif;
	MySQL_Query($SQL);
	DB_odpojeni($spojeni);
	return $funguje;
}

function DB_insert ($SQL, &$id)
{	//echo "<P>SQL = ".$SQL."<P>";
	$funguje = false;
	if (!(DB_spojeni(c_Databaze, $chyba, $spojeni))):
		echo "<FONT COLOR=red><B><P>Do¹lo k chybì pøi spojení s databází.
			($chyba)<P></B></FONT><BR>\n";
		return false;
	endif;
	MySQL_Query($SQL);
        $id = MySQL_Insert_Id();
	DB_odpojeni($spojeni);
	return $funguje;
}


//*******************************************************************
// zjisti, zdali je uzivatel platne prihlasen
//*******************************************************************

function Prihlasen($kod, $ip, &$skup, &$fullname, &$login, &$error)
{
  $SQL = "select * from prihl_uziv where kod='$kod' and ip='$ip'";
  if(DB_select($SQL, $vysledek, $zaznamu) and $zaznamu>=1)
  {
    $SQL = "select * from prihl_uziv where kod='$kod' and ip='$ip'
            and DATE_ADD(cas, INTERVAL ".c_Limit." MINUTE)>=Now()";
    if(DB_select($SQL, $vysledek, $zaznamu) and $zaznamu>=1)
    {
      $zaznam = MySQL_fetch_array($vysledek);
  /*  if(!(cas_limit(TimeLimit(), $zaznam["cas"], date("Y-m-d H:i:s")))):
      $SQL = "delete from prihl_uziv where login='".$zaznam["login"]."' or kod='".$zaznam["kod"]."'";
      DB_exec($SQL);
      header("Location: ".c_Cesta."index.php?err=3");
    else:*/
      $fullname = $zaznam["fullname"];
      $skup = $zaznam["skupina"];
      $login = StrToLower($zaznam["login_uc"]);
      $SQL = "update prihl_uziv set cas = '".date("Y-m-d H:i:s")."' where kod='".$kod."'";
      DB_exec($SQL);
      return true;
    }
    else header("Location: ".c_Cesta."index.php?err=3");
  }
  else header("Location: ".c_Cesta."index.php?err=2");
}

function Prihlasen2($kod, $ip, &$skup, &$fullname, &$login, &$error, $pravaskup)
{
  $SQL = "select * from prihl_uziv where kod='$kod' and ip='$ip'";
  if(DB_select($SQL, $vysledek, $zaznamu) and $zaznamu>=1)
  {
    $SQL = "select * from prihl_uziv where kod='$kod' and ip='$ip'
            and DATE_ADD(cas, INTERVAL ".c_Limit." MINUTE)>=Now()";
    if(DB_select($SQL, $vysledek, $zaznamu) and $zaznamu>=1)
    {
      $zaznam = MySQL_fetch_array($vysledek);
  /*  if(!(cas_limit(TimeLimit(), $zaznam["cas"], date("Y-m-d H:i:s")))):
      $SQL = "delete from prihl_uziv where login='".$zaznam["login"]."' or kod='".$zaznam["kod"]."'";
      DB_exec($SQL);
      header("Location: ".c_Cesta."index.php?err=3");
    else:*/
      $fullname = $zaznam["fullname"];
      $skup = $zaznam["skupina"];
      $login = StrToLower($zaznam["login_uc"]);
      $SQL = "update prihl_uziv set cas = '".date("Y-m-d H:i:s")."' where kod='".$kod."'";
      DB_exec($SQL);
  /*  endif;*/
    }
    else header("Location: ".c_Cesta."index.php?err=3");
  }
  else header("Location: ".c_Cesta."index.php?err=2");
  $pravocteni = 0;
  for($i=0;$i<count($pravaskup);$i++)
  {
    if($pravaskup[$i]==$skup) $pravocteni = 1;
  }
  if($pravocteni==1) return true;
  else header("Location: ".c_Cesta."index.php?err=5");
}

function Prihlasen3($kod, $ip, &$skup, $prava, &$fullname, &$login, &$error)
{
  $SQL = "select * from prihl_uziv where kod='$kod' and ip='$ip'";
  if(DB_select($SQL, $vysledek, $zaznamu) and $zaznamu>=1)
  {
    $SQL = "select * from prihl_uziv where kod='$kod' and ip='$ip'
            and DATE_ADD(cas, INTERVAL ".c_Limit." MINUTE)>=Now()";
    if(DB_select($SQL, $vysledek, $zaznamu) and $zaznamu>=1)
    {
      $zaznam = MySQL_fetch_array($vysledek);
  /*  if(!(cas_limit(TimeLimit(), $zaznam["cas"], date("Y-m-d H:i:s")))):
      $SQL = "delete from prihl_uziv where login='".$zaznam["login"]."' or kod='".$zaznam["kod"]."'";
      DB_exec($SQL);
      header("Location: ".c_Cesta."index.php?err=3");
    else:*/
      $fullname = $zaznam["fullname"];
      $skup = $zaznam["skupina"];
      $login = StrToLower($zaznam["login_uc"]);
      if(($zaznam["prava"])<=$prava)
      {
        $SQL = "update prihl_uziv set cas = '".date("Y-m-d H:i:s")."' where kod='".$kod."'";
        DB_exec($SQL);
        return true;
      }
      else header("Location: ".c_Cesta."index.php?err=6");
  /*  endif;*/
    }
    else header("Location: ".c_Cesta."index.php?err=3");
  }
  else header("Location: ".c_Cesta."index.php?err=2");
}

function Prihlasen4($prava, &$skup, &$fullname, &$login, &$error)
{
  $log = "log.txt";
  ZapisDoLogu($log, "kod po vstupu k overeni prihlaseni = ".session_id("oasasession")." \n");
  if(session_id("oasasession")<>"")
  {
    $SQL = "select * from prihl_uziv where kod='".session_id("oasasession")."' and ip='$REMOTE_ADDR'";
    if(DB_select($SQL, $vysledek, $zaznamu) and $zaznamu>=1)
    {
      $SQL = "select * from prihl_uziv where kod='".session_id("oasasession")."' and ip='$REMOTE_ADDR'
              and DATE_ADD(cas, INTERVAL ".c_Limit." MINUTE)>=Now()";
      if(DB_select($SQL, $vysledek, $zaznamu) and $zaznamu>=1)
      {
        $zaznam = MySQL_fetch_array($vysledek);
    /*  if(!(cas_limit(TimeLimit(), $zaznam["cas"], date("Y-m-d H:i:s")))):
        $SQL = "delete from prihl_uziv where login='".$zaznam["login"]."' or kod='".$zaznam["kod"]."'";
        DB_exec($SQL);
        header("Location: ".c_Cesta."index.php?err=3");
      else:*/
        $fullname = $zaznam["fullname"];
        $skup = $zaznam["skupina"];
        $login = StrToLower($zaznam["login_uc"]);
        if(($zaznam["prava"])<=$prava)
        {
          $SQL = "update prihl_uziv set cas = '".date("Y-m-d H:i:s")."' where kod='".session_id("oasasession")."'";
          DB_exec($SQL);
          return true;
        }
        else header("Location: ".c_Cesta."index.php?err=6");
    /*  endif;*/
      }
      else header("Location: ".c_Cesta."index.php?err=3");
    }
    else header("Location: ".c_Cesta."index.php?err=2");
  }
  else header("Location: ".c_Cesta."index.php?err=2");
}

/**********************************************************/
/* zapis do pomocneho logu*/

function VytvorLog($soubor)
{
  $log = fopen("/var/log/intranet/$soubor", "w+");
  fclose($log);
}

function ZapisDoLogu($soubor, $text)
{
  $log = fopen("/var/log/intranet/$soubor", "a");
  fwrite($log, $text."\n");
  fclose($log);

}
//*******************************************************************
// kodovani hesla
//*******************************************************************


function dbpas($ret)
{
  if (StrLen($ret)>0)
     return "password('".$ret."')";
  else
     return "null";
}


/*
*********************************************************************
LDAP
*********************************************************************
*/

/*
---------------------------------------------------------------------
pripoji se k LDAP serveru
---------------------------------------------------------------------
*/
function LDAP_spojeni(&$ds)
{
  $ds = LDAP_connect(c_LDAP);
  if($ds):
      if(LDAP_bind($ds)):
          return true;
      else:
          echo "Unable to bind to LDAP server.";
          LDAP_close($ds);
          return false;
      endif;
  else:
     echo "Unable to connect to LDAP server.";
     return false;
  endif;
}

/*
---------------------------------------------------------------------
odpoji se od LDAP serveru
---------------------------------------------------------------------
*/
function LDAP_odpojeni($ds)
{
  LDAP_close($ds);
}


/*
---------------------------------------------------------------------
zjisti prislusnost uzivatele ke skupine
---------------------------------------------------------------------
*/

function Skupina($ds, $dn, $login, &$jmeno, &$prijmeni, &$id_skup, &$prava)
{
  $filtr = "sn=*";
  //echo "dn=$dn, filtr=$filtr<br>";
  $vysledek = @LDAP_search($ds, $dn, $filtr, Array("sn", "givenName", "mail"));
  //echo "skup = $skup";
  if($vysledek)
  {
    $polozka = LDAP_first_entry($ds, $vysledek);
    if($polozka)
    {
      $osoba[] = LDAP_get_values($ds, $polozka, "sn");
      $osoba[] = LDAP_get_values($ds, $polozka, "givenName");
      if($skupina_novell == "U")
      {
        $SQL = "select s.id, s.prava from skupiny s, ucitele u where u.login='$login' and u.id_skup=s.id";
        if(DB_select($SQL, $vystup, $zaznamu))
        {
          if($zaznam=MySQL_fetch_array($vystup))
          {
            $id_skup = $zaznam["id"];
            $prava = $zaznam["prava"];
          }
          else
          {
            $id_skup = "4";
            $prava = 3;
          }
        }
      }
      else
      {
        $SQL = "select id, prava from skupiny where skupina_novell = '$skupina_novell'";
        if(DB_select($SQL, $vystup, $zaznamu))
        {
          if($zaznam=MySQL_fetch_array($vystup))
          {
            $id_skup = $zaznam["id"];
            $prava = $zaznam["prava"];
          }
        }
      }
      $prijmeni = $osoba[0][0];
      $jmeno = $osoba[1][0];
    }
    return 1;
  }
  else return 0;
}

function Uloz_uziv($login, $fullname, $id_skup, $prava, &$kod)
{ global $REMOTE_ADDR;
  $log = "log_uloz.txt";
  VytvorLog($log);
  srand((double)microtime()*1e6);
  $kod = "$login|$id_skup|".rand();
  ZapisDoLogu($log, "kod=$kod");
  $cas = date("Y-m-d H:i:s");
  /*$SQL = "delete from prihl_uziv where ip='$REMOTE_ADDR' and kod<>'$kod' ";
  DB_exec($SQL);*/
  $SQL = "select count(*) pocet from prihl_uziv where login_uc = '$login'";
  DB_select($SQL, $vystup, $zaznamu);
  $zaznam = MySQL_fetch_array($vystup);
  if ($zaznam["pocet"]==0)
  {
     $SQL = "insert into prihl_uziv(kod, ip, fullname, cas, login_uc, skupina, prava) values
  	    ('$kod', '$REMOTE_ADDR', '$fullname', '$cas', '$login', '$id_skup', '$prava')";
  }
  else
  {
     $SQL = "update prihl_uziv set kod='$kod', ip='$REMOTE_ADDR', fullname='$fullname',
             cas='$cas', skupina='$id_skup' where login_uc='$login' ";
  }
  DB_exec($SQL);
  $cas = time()+3600*24*365;
  /*echo "kod = $kod";
  echo "cas = $cas";*/

}

function Sestav_jmeno($titpred, $jm, $prijm, $titza)
{
  $jmeno = "";
  if($titpred<>"") $jmeno .= $titpred." ";
  $jmeno .= $jm." ".$prijm;
  if($titza<>"") $jmeno .= ", ".$titza;
  return $jmeno;
  }

function Tlacitka($kod, $stranka, $pole_parametru, $pole_tlacitek, $promackle_tlacitko)
{
  if($pole_tlacitek=="")
  { //echo "nejsou tlacitka &nbsp;";
  }
  else
  {
  //  echo "jsou tlacitka, umistim tabulku ";
    echo "<table cellpadding=\"0\" cellspacing=\"0\" border=0 bordercolor=0><tr width=100% style=\"border-bottom: #2f8156 solid 2px;\">";
    for($i=0;$i<count($pole_tlacitek);$i++)
    {
       if($i==$promackle_tlacitko-1)
         {
           echo "<td width=\"106\" height=\"28\" background=\"./images/btn_vybrane2.gif\"><center><A href=\"".$stranka."?kod=$kod&".$pole_parametru[$i]."\" style=\"font-family: Arial, Helvetica, sans-serif;	font-size: 11px; text-decoration: none; color: #0C1072;\" ><b>".$pole_tlacitek[$i]."</b></a></center></td>";
         }
       else
         {
           echo "<td width=\"106\" height=\"28\" background=\"./images/btn_zelene2.gif\"><center><A href=\"".$stranka."?kod=$kod&".$pole_parametru[$i]."\" style=\"font-family: Arial, Helvetica, sans-serif;	font-size: 11px; text-decoration: none; color: #0C1072; \" ><b>".$pole_tlacitek[$i]."</b></a></center></td>";
         }
    }
    echo "</TR></TABLE>";
  }
}

/*** vykresleni zahlavi tabulky ************/
function Zahlavi($texty, $zarovnani)
{
   global $barva;
   echo "<table class = \"polozky_small\" style=\"border: 1px solid ".c_ohraniceni.";\" cellpadding=\"5\" cellspacing=\"0\" width=\"99%\">";
   echo "<tr bgcolor=\"".c_zahlavi."\" style=\"font-weight: bold\" align=\"$zarovnani\">";
   for($i=0;$i<count($texty);$i++)
     echo "<td style=\"border-bottom: 1px solid ".c_ohraniceni."\">".$texty[$i]."</td>";
   echo "</TR>";
   $barva = "#dddddd";
}

/*** vykresleni zahlavi tabulky bez definice tabulky ***/
function Zahlavi_radek($texty, $zarovnani, $colspan=1)
{
   global $barva;
   if($colspan<>1) $slouceni="colspan=$colspan"; else $slouceni = "";
   echo "<tr style=\"
             background:".c_zahlavi.";
	     font-family: Arial, Helvetica, sans-serif;
	     font-size: 13px;
	     color: Black;
	     font-weight: bold;
	     border-top: 1px solid ".c_ohraniceni.";
	     border-bottom: 1px solid ".c_ohraniceni.";
	     align:$zarovnani\">";
   for($i=0;$i<count($texty);$i++)
     echo "<td style=\"border-top: 1px solid ".c_ohraniceni.";
	     border-bottom: 1px solid ".c_ohraniceni.";\" $slouceni>".$texty[$i]."</td>";
   echo "</TR>";
   $barva = "#dddddd";
 }

?>
