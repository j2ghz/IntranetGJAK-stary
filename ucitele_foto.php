<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 3, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Editace fotografií", $fullname, $kod);
  if($odeslano_foto)
  {
    if($soubor_foto_type=="image/gif")
    {
      $novy = "./photos/".$login_uc.".gif";
      copy($soubor_foto, $novy);
      $chyba="ok";
    }
    else if($soubor_foto_type=="image/jpeg")
         {
           $novy = "./photos/".$login_uc.".jpg";
           copy($soubor_foto, $novy);
           $chyba="ok";
         }
         else $chyba="<li>Obrázek nemá povolený formát. Soubor musí být typu jpg nebo gif.";
    echo "<center>".Hlaska($chyba, "Fotografii se nepodaøilo ulo¾it", "Fotografie byla úspì¹nì ulo¾ena")."</center>";
  }
  if($odeslano_vymaz)
  {
    $chyba="";
    $nasel=0;
    $foto = "./photos/".$login_uc.".jpg";
    if(file_exists($foto))
    {
      $chyba="ok";
      unlink($foto);
      $nasel=1;
    }
    $foto = "./photos/".$login_uc.".gif";
    if(file_exists($foto))
    {
      $chyba="ok";
      unlink($foto);
      $nasel=1;
    }
    if($nasel=0) $chyba.="<li>soubor asi neexistuje</li>";
    echo "<center>".Hlaska($chyba, "Fotografii se nepodaøilo odstranit", "Fotografie byla úspì¹nì odstranìna")."</center>";
  }
  $SQL = "select jmeno, prijmeni, login from ucitele order by prijmeni, jmeno";
  echo "<form action=\"./ucitele_foto.php?kod=$kod\"  method=post enctype=\"multipart/form-data\">";
  if(DB_select($SQL, $vystup, $pocet))
  {
    echo "<select name=\"login_uc\">";
    while($zaz=mysql_fetch_array($vystup))
    {
      echo "<option value=\"".$zaz["login"]."\">".$zaz["prijmeni"]." ".$zaz["jmeno"];
    }
  }
  echo "</select>";
  echo "<p><b>Foto:</b>";
  echo "<br><input type=\"file\" name=\"soubor_foto\" value=\"$soubor_foto\">";
  echo "<p><input type=\"submit\" name=\"odeslano_foto\" value=\"Odeslat foto\">";
  echo " <input type=\"submit\" name=\"odeslano_vymaz\" value=\"Odstranit foto\">";
  echo "<dl><dt>Poznámka: </dt><dd><i>Tlaèítko \"Odstranit foto\" je nutné pou¾ít pouze v pøípadì, ¾e je potøeba fotografii odstranit.
        Pøi ukládání nové fotografie se pùvodní automaticky pøepí¹e.</i></dd>";
 /* if($klic)
  {
    echo "<h3>Foto $klic</h3>";
    if(file_exists("./photos/".$login.$klic.".jpg")) echo "<img src=\"./photos/$login$klic.jpg\">";
    else if(file_exists("./photos/".$login.$klic.".gif")) echo "<img src=\"./photos/$login$klic.jpg\">";
  }         */
  Konec();
}


function UlozFoto($soubor, $typ, &$novy_nazev, $poradi, $chyba)
{
global $login;

if($soubor<>"none")
{
  if($typ=="image/gif") $novy_nazev = $ucitel_login.$poradi.".gif";
  else $novy_nazev = $ucitel_login.$poradi.".jpg";
  Copy($soubor, "./photos/".$novy_nazev);
  }
}

?>
