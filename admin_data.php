<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 1, $fullname, $login, $chyba))
{
  NoCACHE();
  
  $pole_tlacitek = array("Skupiny<br>studentù", "Zvonìní", "Telefonní<br>èíslo", "Pøedmìtové<br>komise");
  $pole_vyberu = array("vyber=1", "vyber=2", "vyber=3", "vyber=4");
  if(!($vyber)) $vyber=1;
  Hlavicka("Správa dat", $fullname, $kod, "admin_data.php", $pole_vyberu, $pole_tlacitek, $vyber);
  
  if($vyber=="") $vyber=1;

  switch($vyber)
  {
    case 1:
      /**** Skupiny<br>studentù ****/
      //Tlacitka($kod, "admin_data.php", $pole_vyberu, $pole_tlacitek, $vyber);
      if($odeslano_edit)
      {
        $skup[$i] = strtoupper($skup[$i]);
        for($i=0;$i<count($ident);$i++)
        {
/*          echo "<br>updatuju polozku $i: id = ".$ident[$i]."  -  skup = ".$skup[$i];*/
          $SQL = "update skupiny set skupina_novell = '".$skup[$i]."' where id='".$ident[$i]."'";
          DB_exec($SQL);
        }

      }
      $SQL = "select * from skupiny where id>='20' and id<'100' order by id";
      if(DB_select($SQL, $vystup, $pocet))
      {
        echo "<form action=\"admin_data.php?kod=$kod&vyber=$vyber\" method=\"post\">";
	echo "<table border=0 cellspacing=2 cellpadding=5>";
        $i=0;
        while($zaz=mysql_fetch_array($vystup))
        {
          echo "<tr><td>".$zaz["skupina"]."</td><td><input type=\"text\" value=\"".$zaz["skupina_novell"]."\" name=\"skup[]\"></td>";
          echo "<input type=\"hidden\" value=\"".$zaz["id"]."\" name=\"ident[]\">";
          $i++;
        }
        echo "<tr><td>&nbsp;</td></tr><tr><td><input type=\"submit\" name=\"odeslano_edit\" value=\"opravit skupiny\">";
        echo "</table>";
        echo "</form>";
      }
    break;
    case 2:
      /**** Zvonìní ****/
      //Tlacitka($kod, "admin_data.php", $pole_vyberu, $pole_tlacitek, $vyber);
      echo "Chcete-li pouze pravit èasy zvonìní, staèí je pøepsat.";
      echo "<br>Chcete-li pøidat neexistující hodinu, vepi¹te ji do posledního øádku, bude zaèlenìna do zvonìní ve správném poøadí.";
      echo "<br>Chcete-li naopak nìkterou vyuèovací hodinu zcela zru¹it, sma¾te oba èasy.";
      echo "<br>Bude-li chybìt jeden z èasù od/do, zmìna u pøíslu¹né vyuè. hodiny se neprovede.";
      if($odeslano_edit)
      {
        $pocet = count($hod)-1;
        for($i=0;$i<$pocet;$i++)
        {
          if(($cas_od[$i]=="") and ($cas_do[$i]==""))
            DB_exec("delete from zvoneni where hodina='".$hod[$i]."'");
          else if(($cas_od[$i]<>"") and ($cas_do[$i]<>""))
            DB_exec("update zvoneni set od='".$cas_od[$i]."', do='".$cas_do[$i]."' where hodina='".$hod[$i]."'");
        }
        echo $pocet;
        if(($hod[$pocet]<>"") and ($cas_od[$pocet]<>"") and ($cas_do[$pocet]<>""))
        {
          DB_exec("insert into zvoneni (hodina, od, do) values ('".$hod[$pocet]."', '".$cas_od[$pocet]."', '".$cas_do[$pocet]."')");
        }
      }
      $SQL = "select * from zvoneni order by hodina";
      if(DB_select($SQL,$vystup,$pocet))
      {
        echo "<form action=\"admin_data.php?kod=$kod&vyber=$vyber\" method=\"post\">";
	echo "<table border=0 cellspacing=2 cellpadding=5>";
        $i = 0;
        while($zaz=mysql_fetch_array($vystup))
        {
          echo "<input type=\"hidden\" name=\"hod[]\" value=\"".$zaz["hodina"]."\">";
          echo "<tr><td>".Bunka($zaz["hodina"])."</td>";
	  echo "    <td><input type=\"text\" value=\"".Cas($zaz["od"])."\" name=\"cas_od[]\"></td>";
          echo "    <td><input type=\"text\" value=\"".Cas($zaz["do"])."\" name=\"cas_do[]\"></td></tr>";
          $i++;
        }
        echo "<tr><td><input type=\"text\" value=\"\" name=\"hod[]\"></td>";
        echo "    <td><input type=\"text\" value=\"\" name=\"cas_od[]\"></td>";
        echo "    <td><input type=\"text\" value=\"\" name=\"cas_do[]\"></td></tr>";
        echo "<tr><td>&nbsp;</td></tr><tr><td colspan=\"3\"><input type=\"submit\" name=\"odeslano_edit\" value=\"opravit zvonìní\">";
        echo "</table>";
        echo "</form>";
      }

    break;
    case 3:
      /**** Telefonní<br>èíslo ****/
      //Tlacitka($kod, "admin_data.php", $pole_vyberu, $pole_tlacitek, $vyber);
      if($odeslano_edit)
      {
        $SQL = "update pomocna set hodnota='$tel' where klic='tel'";
        DB_exec($SQL);
      }

      $SQL = "select hodnota from pomocna where klic='tel'";
      if(DB_select($SQL, $vyst,$poc))
         if($zaz=mysql_fetch_array($vyst)) $tel=$zaz["hodnota"];
      echo "<form action=\"admin_data.php?kod=$kod&vyber=$vyber\" method=\"post\">";
      echo "<input type=\"text\" value=\"$tel\" name=\"tel\">";
      echo "<p><input type=\"submit\" name=\"odeslano_edit\" value=\"aktualizovat tel. èíslo\">";

    break;
    case 4:
      /**** Pøedmìtové komise ****/
      //Tlacitka($kod, "admin_data.php", $pole_vyberu, $pole_tlacitek, $vyber);
      if($odeslano_vymaz)
      {
        for($i=0;$i<count($vymaz);$i++)
        {
          $SQL = "delete from komise where zkratka='".$vymaz[$i]."'";
          DB_exec($SQL);
        }
      }
      else
      {
        if($odeslano_new)
        {
          $zkratka = strtoupper($zkratka);
          $SQL = "select * from komise where zkratka = '$zkratka'";
          if(DB_select($SQL, $vystup, $pocet))
          {
            if($pocet<>0)
            {
              echo Hlaska("<li>Komise s touto zkratkou u¾ v databázi existuje.<br>Zvolte jinou zkratku, nebo nejdøíve pùvodní akci odstraòte.</li>","komise nebyla ulo¾ena do databáze","");
            }
            else
            {
              $SQL = "insert into komise (zkratka, nazev) values ('$zkratka', '$nazev')";
              DB_exec($SQL);
            }
          }
        }
      }
      $SQL = "select * from komise where zkratka<>'aaa' order by zkratka";
      if(DB_select($SQL, $vystup, $pocet))
      {
        $SQL2 = "select k.zkratka from komise k, vzkazy v where k.zkratka=v.komise and (v.platnost_do>=Now() or v.platnost_do='0000-00-00' or v.platnost_do is null) order by k.zkratka";
        if(DB_select($SQL2, $vystup2, $pocet2))
        {
          while($zaznam=mysql_fetch_array($vystup2)) $pouzite_komise[] = $zaznam["zkratka"];
        }
        echo "<form action=\"admin_data.php?kod=$kod&vyber=$vyber\" method=\"post\">";
	echo "<table border=0 cellspacing=2 cellpadding=5><tr>";
	echo "<td colspan=\"5\">Zkratka: <input type=\"text\" value=\"$zkratka\" name=\"zkratka\">";
	echo "&nbsp;&nbsp;&nbsp;Název: <input type=\"text\" value=\"$nazev\" name=\"nazev\"></td></tr>";
        echo "<tr><td colspan=\"4\"><input type=\"submit\" name=\"odeslano_new\" value=\"pøidat komisi\"></td></tr>";
        echo "</form>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<form action=\"admin_data.php?kod=$kod&vyber=$vyber\" method=\"post\">
              <tr bgcolor=\"#dddddd\" align=\"center\">
              <td><b><center>mazání</center></b></td>
              <td><center><b>zkratka</b></center></td>
              <td><center><b>název</b></center></td></tr>";
        while($zaz=mysql_fetch_array($vystup))
        {
          $i = 0; $nasel = 0;
          while($i<count($pouzite_komise) and $nasel<>1)
          {
            if($pouzite_komise[$i]==$zaz["zkratka"]) $nasel = 1;
            $i++;
          }
          if($nasel==0) echo "<tr valign=\"top\"><td><input type=\"checkbox\" name=\"vymaz[]\" value=\"".$zaz["zkratka"]."\"></td>";
          else echo "<td>".Text_alter("", "nelze")."</td>";
          echo "<td>".$zaz["zkratka"]."</td>";
          echo "<td>".$zaz["nazev"]."</td></tr>";
        }
        echo "<tr><td colspan=\"4\"><input type=\"submit\" name=\"odeslano_vymaz\" value=\"odstranit vybrané komise\"></td></tr></table></form>";

      }
    break;
  }

  Konec();
}

function Bunka($text)
{
return "<table border=1 bgcolor=\"#e6e6e6\"><tr><td width=\"150\">$text&nbsp;</td></tr></table>";
}


?>
