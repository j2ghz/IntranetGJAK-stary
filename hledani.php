<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 6, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Vyhledávání souborù", $fullname, $kod);
  echo "<font color=red>Vyhledávání zatím funguje (nebo spí¹ úplnì nefunguje ;-) jenom adminovi, pracuji na nìm.</font>";
  $sloupcu = 5;
  $polozka_text = $polozka;
  $SQL_skola = "select skola from skupiny where id='$skupina'";
  if(DB_select($SQL_skola, $vystup, $pocet)) 
  if($zaz=mysql_fetch_array($vystup)) $skola = $zaz["skola"];
  if($odeslano)
  {
    for($i=0;$i<6;$i++)
    {
      $selected1[$i];
      $selected2[$i];
      $selected3[$i];
    }
    for($i=1;$i<=6;$i++) $checked[$i] = "";
    switch($polozka_text)
    {
      case "s.popis":
        $selected2[0] = "selected";
      break;
      case "s.nazev":
        $selected2[1] = "selected";
      break;
      case "u.prijmeni":
        $selected2[2] = "selected";
      break;
      case "s.predmet":
        $selected2[3] = "selected";
      break;
      case "s.trida":
        $selected2[4] = "selected";
      break;
    }
    switch($kde)
    {
      case "lib":
        $selected1[0] = "selected";
        $pom_ret = "%".StrToLower($retezec)."%";
      break;
      case "zac":
        $selected1[1] = "selected";
        $pom_ret = StrToLower($retezec)."%";
      break;
      case "kon":
        $selected1[2] = "selected";
        $pom_ret = "%".StrToLower($retezec);
      break;
    }
    switch($razeni)
    {
      case "nazev":
        $selected3[0] = "selected";
        $razeni = "order by s.nazev";
      break;
      case "datum":
        $selected3[1] = "selected";
        $razeni = "order by s.datum";
      break;
      case "login_uc":
        $selected3[2] = "selected";
        $razeni = "order by s.login_uc";
      break;
      default:
        $razeni = "";
      break;
    }
    if($hledat1==1) $checked[1] = "checked";  
    if($hledat2==1) $checked[2] = "checked";
    if($hledat3==1) $checked[3] = "checked";
    if($hledat4==1) $checked[4] = "checked";
    if($hledat5==1) $checked[5] = "checked";
        
  }
  else
  {
    $selected1[0] = "selected";
    $selected2[0] = "selected";
    $selected3[0] = "selected";
    for($i=1;$i<=5;$i++) $checked[$i] = "checked";
    $hledat1 = 1;
    $hledat2 = 1;
    $hledat3 = 1;
    $hledat4 = 1;
    $hledat5 = 1; 
  }
  if($retezec==""  or $retezec == "bez podmínky")
  {
    $retezec = "bez podmínky";
    $je_podm = 0;
  }
  else $je_podm = 1;
  echo "<form action=\"hledani.php?kod=$kod&vyber=$vyber\" method=\"post\">";
  echo "<table border=\"0\">";
  echo "<tr><td colspan=2><b>Vyhledávání souboru v oase</b></td></tr>";
  echo "<tr><td>Hledaný øetìzec:</td><td><input type=\"text\" name=\"retezec\" value=\"$retezec\"></td></tr>";
  echo "<tr><td>Hledat:</td>";
  echo "<td><select name=\"kde\">";
  echo   "<option value=\"lib\" ".$selected1[0]."> kdekoli";
  echo   "<option value=\"zac\" ".$selected1[1]."> na zaèátku";
  echo   "<option value=\"kon\" ".$selected1[2]."> na konci";
  echo "</select></td></tr>";
  echo "<tr><td>Hledat v polo¾ce:</td>";
  echo "<td><select name=\"polozka\">";
  echo   "<option value=\"s.popis\" ".$selected2[0]."> popis souboru (informace o obsahu - nejèastìj¹í polo¾ka)";
  echo   "<option value=\"s.nazev\" ".$selected2[1]."> název souboru (bez diakritiky a mezer)";
  echo   "<option value=\"u.prijmeni\" ".$selected2[2]."> pøíjmení odesílatele";
  echo   "<option value=\"s.predmet\" ".$selected2[3]."> pøedmìt (vìt¹inou zkratka vyuèovaného pøedmìtu)";
  echo   "<option value=\"s.trida\" ".$selected2[4]."> tøída";
  echo "</select></td></tr>";
  echo "<tr><td>Øadit soubory podle:</td>";
  echo "<td><select name=\"razeni\">";
  echo "<option value=\"nazev\" ".$selected3[0]."> názvu";
  echo "<option value=\"datum\" ".$selected3[1]."> data odeslání";
  echo "<option value=\"login_uc\" ".$selected3[2]."> odesílatele";
  echo "</select></td></tr>";
  echo "<tr><td>Vyhledávat v oddílech:</td></tr>";
  echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"hledat1\" value=\"1\" $checked[1]> Soubory</td></tr>";
  echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"hledat2\" value=\"1\" $checked[2]> Kalendáø akcí</td></tr>";
  if($skola=="u") echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"hledat3\" value=\"1\" $checked[3]> Pro uèitele</td></tr>";
  if($skola=="u" or $skola=="g") echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"hledat4\" value=\"1\" $checked[4]> Pro studenty</td></tr>";
  if($skola=="u") echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"hledat5\" value=\"1\" $checked[5]> Kraj - ¹kolství</td></tr>";
  echo "<tr><td colspan=\"2\"><input type = \"submit\" value=\"zobraz soubory\" name=\"odeslano\"></td></tr>";
  echo "</table>";
  echo "</form>";
  echo "<br>";

  if($odeslano)
  {
    if($je_podm==0) 
    {
      $pom_ret = "%";
      $poz_naz = "#eeeeee";
    }

    

    
    echo "<table border=0 cellspacing=0 cellpadding=5>";
    Zahlavi_radek(array("Název souboru", "Popis", "Odesílatel", "Pøedmìt", "Tøída"), "left");
    echo "<tr><td>&nbsp;</td></tr>";
/* 1. soubory */
    if($hledat1==1)
    {
      if($skupina==1) $podminka_skupiny = "s.id = ss.id_soub";
      else if($skupina<c_ucitel) $podminka_skupiny = "(ss.id_skup='".c_ucitel."' or ss.id_skup='$skupina' or ss.id_skup='-2') and s.id = ss.id_soub";
      else if($skupina>=20 and $skupina<=40) $podminka_skupiny = "(ss.id_skup='$skupina' or ss.id_skup='-2' or ss.id_skup='-1') and s.id = ss.id_soub";
      else $podminka_skupiny = "(ss.id_skup='$skupina' or ss.id_skup='-2') and s.id = ss.id_soub";
          
      
        /*  echo "<p>SQL = $SQL";  */
      $SQL = "select distinct s.*, t.popis typs, t.skola, u.* 
              from soubory_skupiny ss, soubory s left join typ t on s.typ = t.nazev, ucitele u
              where lower($polozka) like '$pom_ret' and s.login_uc = u.login and s.typ is null and
              $podminka_skupiny
              $razeni";
    
  /*    echo "<p>SQL = $SQL";*/
    	if(DB_select($SQL, $vystup, $pocet))
      {
        
        $barva="#dddddd";
        
        echo "<tr><td colspan=$sloupcu class=\"oddil\">Soubory</td></tr>";
        if($pocet==0) echo "<tr><td colspan=$sloupcu>".Text_alter("", "Nebyly nalezeny ¾ádné polo¾ky.")."</td></tr>";
        else
        {          
          while($zaz=mysql_fetch_array($vystup))
          {         
            if($retezec == "bez podmínky") $polozka_text="";
            if($barva=="#eeeeee") $barva="#dddddd"; else $barva="#eeeeee";
            $poz_nazev = $barva;
            $poz_popis = $barva;
            $poz_prijmeni = $barva;
            $poz_predmet = $barva;
            $poz_trida = $barva;
            switch($polozka_text)
            {
              case "s.popis":
                    if($barva=="#eeeeee") $poz_popis=c_zahlavi; else $poz_popis=c_zahlavi_tmave;
              break;
              case "s.nazev":
                    if($barva=="#eeeeee") $poz_nazev=c_zahlavi; else $poz_nazev=c_zahlavi_tmave;
              break;
              case "u.prijmeni":
                    if($barva=="#eeeeee") $poz_prijmeni=c_zahlavi; else $poz_prijmeni=c_zahlavi_tmave;
              break;
              case "s.predmet":
                    if($barva=="#eeeeee") $poz_predmet=c_zahlavi; else $poz_predmet=c_zahlavi_tmave;
              break;
              case "s.trida":
                    if($barva=="#eeeeee") $poz_trida=c_zahlavi; else $poz_trida=c_zahlavi_tmave;
              break;
            }
/*            if($zaz["typ"]<>"" and $zaz["skola"]<>"")
                switch($zaz["skola"])
                {
		            case "u": $popis = "pro uèitele &nbsp;<img src=\"images/sip.gif\" border=\"0\">&nbsp; ".$zaz["typs"];
                          $p_adresar="files_ucitelum/".$zaz["nazev"];
                          echo "<p>soubor pro ucitele, adresar je ".$p_adresar;
		  break;
                  case "v": $popis = "vy¹¹í odborné studium &nbsp;<img src=\"images/sip.gif\" border=\"0\">&nbsp; ".$zaz["typs"];
                            $p_adresar="files_vos/".$zaz["nazev"];
		  break;
                  case "b": $popis = "bakaláøské studium &nbsp;<img src=\"images/sip.gif\" border=\"0\">&nbsp; ".$zaz["typs"];
                            $p_adresar="files_bak/".$zaz["nazev"];
		  break;
                  case "o": $popis = "obchodní akademie &nbsp;<img src=\"images/sip.gif\" border=\"0\">&nbsp; ".$zaz["typs"];
                            $p_adresar="files_oa/".$zaz["nazev"];
		  break;
                  case "i": $popis = "kalendáø akcí ".Datum($zaz["platnost_do"],0);
                            $p_adresar="files_kalendar/".$zaz["nazev"];
		  break;
                }
              else if($zaz["typ"]=="")*/
                   {
	                   $popis = "soubory";
                     $p_adresar="files/".StrToLower($zaz["login_uc"])."/".$zaz["nazev"];
                   }
            $p_nazev=$zaz["nazev"];            
            echo "<TR>";         
            echo "<TD bgcolor=\"$poz_nazev\"><a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=$p_nazev&p_adresar=$p_adresar\">".$zaz["nazev"]."</a></TD>";
            echo "<TD bgcolor=\"$poz_popis\">".$zaz["popis"]."&nbsp;</TD>";
            echo "<TD bgcolor=\"$poz_prijmeni\">".$zaz["prijmeni"]." ".$zaz["jmeno"]."</TD>";
            echo "<TD bgcolor=\"$poz_predmet\">".$zaz["predmet"]."&nbsp;</TD>";
            echo "<TD bgcolor=\"$poz_trida\">".$zaz["trida"]."&nbsp;</TD>";
          /*  echo "<TD bgcolor=\"$barva\">$popis&nbsp;</TD>";*/
            echo "</TR>";            
          }
        }            
        if($polozka_text=="u.prijmeni") 
        {
          $retezec = "bez podmínky";
          $je_podm = 0;
          $polozka = "s.datum";
          $pom_ret = Datum_datab("1.1.1900");
        }
        echo "<tr><td>&nbsp;</td></tr>";
      }
    }
/* 2. kalendar akci */
    if($hledat2==1)
    { 
      switch($skola)
      {
        case "u": $SQL = "select * from soubory s, kalendar k where lower($polozka) like '$pom_ret' and s.id=k.id_soub $razeni ";
        break;
        case "o": $SQL = "select * from soubory s, kalendar k where lower($polozka) like '$pom_ret' and s.id=k.id_soub and k.oa='1' $razeni ";
        break;
        case "v": $SQL = "select * from soubory s, kalendar k where lower($polozka) like '$pom_ret' and s.id=k.id_soub and k.vose='1' $razeni ";
        break;
        case "b": $SQL = "select * from soubory s, kalendar k where lower($polozka) like '$pom_ret' and s.id=k.id_soub and k.bak='1' $razeni ";
        break;
      }
      echo "<tr><td colspan=$sloupcu class=\"oddil\">Kalendáø akcí</td></tr>";      
      if(DB_select($SQL, $vystup, $pocet))
        if($pocet==0) echo "<tr><td colspan=$sloupcu>".Text_alter("", "Nebyly nalezeny ¾ádné polo¾ky.")."</td></tr>";
        else
        {
          
          $barva="#dddddd";           
          while($zaz=mysql_fetch_array($vystup))
          {
       /*     switch($skola)
                {
		              case "u": $popis = "pro uèitele &nbsp;<img src=\"images/sip.gif\" border=\"0\">&nbsp; ".$zaz["typs"];
                            $p_adresar="files_ucitelum/".$zaz["nazev"];
		              break;
                  case "v": $popis = "vy¹¹í odborné studium &nbsp;<img src=\"images/sip.gif\" border=\"0\">&nbsp; ".$zaz["typs"];
                            $p_adresar="files_vos/".$zaz["nazev"];
		              break;
                  case "b": $popis = "bakaláøské studium &nbsp;<img src=\"images/sip.gif\" border=\"0\">&nbsp; ".$zaz["typs"];
                            $p_adresar="files_bak/".$zaz["nazev"];
		              break;
                  case "o": $popis = "obchodní akademie &nbsp;<img src=\"images/sip.gif\" border=\"0\">&nbsp; ".$zaz["typs"];
                            $p_adresar="files_oa/".$zaz["nazev"];
		              break;
                  case "i": $popis = "kalendáø akcí ".Datum($zaz["platnost_do"],0);
                            $p_adresar="files_kalendar/".$zaz["nazev"];
		              break;
                }*/
            $p_adresar="files_kalendar/".$zaz["nazev"];
            if($retezec == "bez podmínky") $polozka_text="";
            if($barva=="#eeeeee") $barva="#dddddd"; else $barva="#eeeeee";
            $poz_nazev = $barva;
            $poz_popis = $barva;
            $poz_prijmeni = $barva;
            $poz_predmet = $barva;
            $poz_trida = $barva;
            switch($polozka_text)
            {
              case "s.popis":
                    if($barva=="#eeeeee") $poz_popis=c_zahlavi; else $poz_popis=c_zahlavi_tmave;
              break;
              case "s.nazev":
                    if($barva=="#eeeeee") $poz_nazev=c_zahlavi; else $poz_nazev=c_zahlavi_tmave;
              break;
              case "u.prijmeni":
                    if($barva=="#eeeeee") $poz_prijmeni=c_zahlavi; else $poz_prijmeni=c_zahlavi_tmave;
              break;
              case "s.predmet":
                    if($barva=="#eeeeee") $poz_predmet=c_zahlavi; else $poz_predmet=c_zahlavi_tmave;
              break;
              case "s.trida":
                    if($barva=="#eeeeee") $poz_trida=c_zahlavi; else $poz_trida=c_zahlavi_tmave;
              break;
            }                           
            $p_nazev=$zaz["nazev"];              
            echo "<TR>";         
            echo "<TD bgcolor=\"$poz_nazev\"><a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=$p_nazev&p_adresar=$p_adresar\">".$zaz["nazev"]."</a></TD>";
            echo "<TD bgcolor=\"$poz_popis\">".$zaz["popis"]."&nbsp;</TD>";
            echo "<TD bgcolor=\"$poz_prijmeni\">".$zaz["prijmeni"]." ".$zaz["jmeno"]."</TD>";
            echo "<TD bgcolor=\"$poz_predmet\">&nbsp;</TD>";
            echo "<TD bgcolor=\"$poz_trida\">".$zaz["trida"]."&nbsp;</TD>";
          /*  echo "<TD bgcolor=\"$barva\">$popis&nbsp;</TD>";*/
            echo "</TR>";
          }       
        }
        echo "<tr><td>&nbsp;</td></tr>";
    }   
       /* pro ucitele, studenty apod */
    
 
    if($hledat3==1)
    { 
      if($skola=="u") 
      {           
        $barva="#dddddd";
        echo "<tr><td colspan=$sloupcu class=\"oddil\">Pro uèitele</td></tr>";
        $jsoupolozky = 0;         
        $SQL_typ = "select nazev, popis from typ where skola='u' order by id";
       /*  echo "<p>SQL=$SQL_typ";*/
        if(DB_select($SQL_typ, $vystup, $pocet)) 
        { 
          while($zaznam=mysql_fetch_array($vystup))
          {
            $SQL = "select * from soubory s where lower($polozka) like '$pom_ret' and s.typ = '".$zaznam["nazev"]."' $razeni";   
            if(DB_select($SQL, $vyst, $poc))  
            {
              if($poc<>0) $jsoupolozky=1; 
              while($zaz=mysql_fetch_array($vyst))
              {
                $p_adresar="files_ucitelum/".$zaz["nazev"];
                if($retezec == "bez podmínky") $polozka_text="";
                if($barva=="#eeeeee") $barva="#dddddd"; else $barva="#eeeeee";
                $poz_nazev = $barva;
                $poz_popis = $barva;
                $poz_prijmeni = $barva;
                $poz_predmet = $barva;
                $poz_trida = $barva;
                switch($polozka_text)
                {
                  case "s.popis":
                       if($barva=="#eeeeee") $poz_popis=c_zahlavi; else $poz_popis=c_zahlavi_tmave;
                  break;
                  case "s.nazev":
                       if($barva=="#eeeeee") $poz_nazev=c_zahlavi; else $poz_nazev=c_zahlavi_tmave;
                  break;
                  case "u.prijmeni":
                       if($barva=="#eeeeee") $poz_prijmeni=c_zahlavi; else $poz_prijmeni=c_zahlavi_tmave;
                  break;
                  case "s.predmet":
                       if($barva=="#eeeeee") $poz_predmet=c_zahlavi; else $poz_predmet=c_zahlavi_tmave;
                  break;
                  case "s.trida":
                       if($barva=="#eeeeee") $poz_trida=c_zahlavi; else $poz_trida=c_zahlavi_tmave;
                  break;
                }         
                 
                $p_nazev=$zaz["nazev"];
                 
                echo "<TR>";         
                echo "<TD bgcolor=\"$poz_nazev\"><a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=$p_nazev&p_adresar=$p_adresar\">".$zaz["nazev"]."</a></TD>";
                echo "<TD bgcolor=\"$poz_popis\">".$zaz["popis"]."&nbsp;</TD>";
                echo "<TD bgcolor=\"$poz_prijmeni\">&nbsp;</TD>";
                echo "<TD bgcolor=\"$poz_predmet\">&nbsp;</TD>";
                echo "<TD bgcolor=\"$poz_trida\">".$zaz["trida"]."&nbsp;</TD>";
                echo "</TR>";
              }
            }
          }
          
        }    
        if($jsoupolozky==0) echo "<tr><td colspan=$sloupcu>".Text_alter("", "Nebyly nalezeny ¾ádné polo¾ky.")."</td></tr>";        
        echo "<tr><td>&nbsp;</td></tr>";
      }
    }  
    if($hledat4==1)
    {
      if($skola=="u" or $skola=="b")
      {
        $barva="#dddddd";
        echo "<tr><td colspan=$sloupcu class=\"oddil\">Pro studenty</td></tr>";
        $jsoupolozky = 0;
        $SQL_typ = "select nazev, popis from typ where skola='g' order by id";
      /*  echo "<p>SQL=$SQL_typ";*/
        if(DB_select($SQL_typ, $vystup, $pocet))
          while($zaznam=mysql_fetch_array($vystup))
          {
            $SQL = "select * from soubory s where lower($polozka) like '$pom_ret' and s.typ = '".$zaznam["nazev"]."' $razeni";
          /*  echo "<p>SQL=$SQL";*/
            if(DB_select($SQL, $vyst, $poc))
            {
              if($poc<>0) $jsoupolozky=1;
              while($zaz=mysql_fetch_array($vyst))
              {
                $p_adresar="files_bak/".$zaz["nazev"];
                if($retezec == "bez podmínky") $polozka_text="";
                if($barva=="#eeeeee") $barva="#dddddd"; else $barva="#eeeeee";
                $poz_nazev = $barva;
                $poz_popis = $barva;
                $poz_prijmeni = $barva;
                $poz_predmet = $barva;
                $poz_trida = $barva;
                switch($polozka_text)
                {
                  case "s.popis":
                        if($barva=="#eeeeee") $poz_popis=c_zahlavi; else $poz_popis=c_zahlavi_tmave;
                  break;
                  case "s.nazev":
                        if($barva=="#eeeeee") $poz_nazev=c_zahlavi; else $poz_nazev=c_zahlavi_tmave;
                  break;
                  case "u.prijmeni":
                        if($barva=="#eeeeee") $poz_prijmeni=c_zahlavi; else $poz_prijmeni=c_zahlavi_tmave;
                  break;
                  case "s.predmet":
                        if($barva=="#eeeeee") $poz_predmet=c_zahlavi; else $poz_predmet=c_zahlavi_tmave;
                  break;
                  case "s.trida":
                        if($barva=="#eeeeee") $poz_trida=c_zahlavi; else $poz_trida=c_zahlavi_tmave;
                  break;
                }

                $p_nazev=$zaz["nazev"];

                echo "<TR>";
                echo "<TD bgcolor=\"$poz_nazev\"><a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=$p_nazev&p_adresar=$p_adresar\">".$zaz["nazev"]."</a></TD>";
                echo "<TD bgcolor=\"$poz_popis\">".$zaz["popis"]."&nbsp;</TD>";
                echo "<TD bgcolor=\"$poz_prijmeni\">&nbsp;</TD>";
                echo "<TD bgcolor=\"$poz_predmet\">&nbsp;</TD>";
                echo "<TD bgcolor=\"$poz_trida\">".$zaz["trida"]."&nbsp;</TD>";
              /*  echo "<TD bgcolor=\"$barva\">$popis&nbsp;</TD>";*/
                echo "</TR>";
              }
              
            }
        }
        
        if($jsoupolozky==0) echo "<tr><td colspan=$sloupcu>".Text_alter("", "Nebyly nalezeny ¾ádné polo¾ky.")."</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
      }
    }
    if($hledat5==1)
    {
      if($skola=="u" or $skola=="v")
      {
        $barva="#dddddd";
        echo "<tr><td colspan=$sloupcu class=\"oddil\">Kraj - ¹kolství</td></tr>";
        $jsoupolozky = 0;         
        $SQL_typ = "select nazev, popis from typ where skola='k' order by id";
      /*  echo "<p>SQL=$SQL_typ";*/
        if(DB_select($SQL_typ, $vystup, $pocet)) 
        { 
          while($zaznam=mysql_fetch_array($vystup))
          {
            $SQL = "select * from soubory s where lower($polozka) like '$pom_ret' and s.typ = '".$zaznam["nazev"]."' $razeni";   
            if(DB_select($SQL, $vyst, $poc))  
            { 
              if($poc<>0) $jsoupolozky=1; 
              while($zaz=mysql_fetch_array($vyst))
              {
                $p_adresar="files_vos/".$zaz["nazev"];
                if($retezec == "bez podmínky") $polozka_text="";
                if($barva=="#eeeeee") $barva="#dddddd"; else $barva="#eeeeee";
                $poz_nazev = $barva;
                $poz_popis = $barva;
                $poz_prijmeni = $barva;
                $poz_predmet = $barva;
                $poz_trida = $barva;
                switch($polozka_text)
                {
                  case "s.popis":
                        if($barva=="#eeeeee") $poz_popis=c_zahlavi; else $poz_popis=c_zahlavi_tmave;
                  break;
                  case "s.nazev":
                        if($barva=="#eeeeee") $poz_nazev=c_zahlavi; else $poz_nazev=c_zahlavi_tmave;
                  break;
                  case "u.prijmeni":
                        if($barva=="#eeeeee") $poz_prijmeni=c_zahlavi; else $poz_prijmeni=c_zahlavi_tmave;
                  break;
                  case "s.predmet":
                        if($barva=="#eeeeee") $poz_predmet=c_zahlavi; else $poz_predmet=c_zahlavi_tmave;
                  break;
                  case "s.trida":
                        if($barva=="#eeeeee") $poz_trida=c_zahlavi; else $poz_trida=c_zahlavi_tmave;
                  break;
                }         
                
                $p_nazev=$zaz["nazev"];
                
                echo "<TR>";         
                echo "<TD bgcolor=\"$poz_nazev\"><a class=\"seznam\" href=\"sendfile.php?kod=$kod&p_prava=6&p_nazev=$p_nazev&p_adresar=$p_adresar\">".$zaz["nazev"]."</a></TD>";
                echo "<TD bgcolor=\"$poz_popis\">".$zaz["popis"]."&nbsp;</TD>";
                echo "<TD bgcolor=\"$poz_prijmeni\">&nbsp;</TD>";
                echo "<TD bgcolor=\"$poz_predmet\">&nbsp;</TD>";
                echo "<TD bgcolor=\"$poz_trida\">".$zaz["trida"]."&nbsp;</TD>";
              /*  echo "<TD bgcolor=\"$barva\">$popis&nbsp;</TD>";*/
                echo "</TR>";
              }
            }  
          }
        }    
        if($jsoupolozky==0) echo "<tr><td colspan=$sloupcu>".Text_alter("", "Nebyly nalezeny ¾ádné polo¾ky.")."</td></tr>"; 
        echo "<tr><td>&nbsp;</td></tr>";
      }
    }
    echo "</table>";
  }
}
?>



