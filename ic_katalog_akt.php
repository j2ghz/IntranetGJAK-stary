<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 4, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Informaèní centrum - aktualizace katalogu knihovny", $fullname, $kod);
  $pole_tlacitek = array("Nové<br>knihy", "Volné<br>uèebny", "Pravidla IC", "Nový katalog");
  $pole_vyberu = array("vyber=1", "vyber=2", "vyber=3", "vyber=4");

  if(!($vyber)) $vyber=1;
 /* echo "<ul><li><a href=\"./ic_katalog_akt.php?kod=$kod&vyber=1\">vlo¾ení nové knihy</a></li>";
  echo "<li><a href=\"./ic_katalog_akt.php?kod=$kod&vyber=2\">editace katalogu</a></li>";
  echo "<li><a href=\"./ic_katalog_akt.php?kod=$kod&vyber=3\">ulo¾ení souboru volných uèeben</a></li>";
  echo "<li><a href=\"./ic_katalog_akt.php?kod=$kod&vyber=4\">ulo¾ení souboru pravidel IC</a></li>";
  echo "<li><a href=\"./ic_katalog_akt.php?kod=$kod&vyber=5\">obnova celého katalogu v databázi (pouze pro admina)</a></li></ul><hr><p>";*/
  switch($vyber)
  {
    case 1:
    /**** Vlo¾ení nové knihy ****/
      Tlacitka($kod, "ic_katalog_akt.php", $pole_vyberu, $pole_tlacitek, 1);

      echo "<center>".Hlaska($chyba, "Knihu nepodaøilo ulo¾it", "Kniha byla úspì¹nì ulo¾ena do databáze")."</center>";
      echo "<form action=\"./ic_katalog_send.php?kod=$kod&typ=kniha\" method=post enctype=\"multipart/form-data\">";
      echo "<table border=\"0\">";
      echo "<tr><td>Identifikaèní èíslo</td><td><input type=\"text\" name=\"id\" value=\"$id\"></td></tr>";
      echo "<tr><td>Autor</td><td><input type=\"text\" name=\"autor\" value=\"$autor\"></td></tr>";
      echo "<tr><td>Název</td><td><input type=\"text\" name=\"nazev\" value=\"$nazev\"></td></tr>";
      echo "<tr><td>Poèet kusù</td><td><input type=\"text\" name=\"pocetkusu\" value=\"$pocetkusu\"></td></tr>";
/*      echo "<tr><td>Kategorie</td><td><input type=\"text\" name=\"kategorie\" value=\"$kategorie\"></td></tr>";*/

      $SQL = "select * from ic_doba";
      if(DB_select($SQL, $vystup, $pocet))
      {
        echo "<tr><td>Výpùjèní doba</td><td><select name=\"doba\">";
        while($zaz=mysql_fetch_array($vystup))
        {
          echo "<option value=\"".$zaz["zkratka"]."\">".$zaz["popis"];
        }
        echo "</select></td></tr>";
      }

      echo "<tr><td colspan=2><p><input type = \"submit\" value=\"odeslat\" name=\"odeslano_kniha\">";
      echo "</form>";



    break;
    /*case 2: */
     /* Podnadpis("Editace katalogu");*/
     /*Tlacitka($kod, "ic_katalog_akt.php",
               array("vyber=1", "vyber=3", "vyber=4", "vyber=5"),
	       array("ic_edit_nove_a.gif", "ic_edit_ucebny.gif", "ic_edit_pravidla.gif", "ic_edit_cely.gif"));*/

    break;
    case 2:
    /**** Ulo¾ení souboru volných uèeben ****/
      Tlacitka($kod, "ic_katalog_akt.php", $pole_vyberu, $pole_tlacitek, 2);

      echo "<font color=\"red\">soubor musí mít Unixu vyhovující název, tj. bez mezer a diakritiky</font>";
      echo "<form action=\"./ic_katalog_send.php?kod=$kod&typ=ucebny\" method=post enctype=\"multipart/form-data\">";
      echo "soubor: <input type=\"file\" name=\"soubor_uc\" value=\"$soubor_uc\">";
      echo "<p>";
      echo "<input type = \"submit\" value=\"odeslat soubor\" name=\"odeslano_ucebny\">";
      echo "</form>";
    break;
    case 3:
    /**** Ulo¾ení souboru pravidel IC ****/
      Tlacitka($kod, "ic_katalog_akt.php", $pole_vyberu, $pole_tlacitek, 3);

      echo "<font color=\"red\">soubor musí mít Unixu vyhovující název, tj. bez mezer a diakritiky</font>";
      echo "<form action=\"./ic_katalog_send.php?kod=$kod&typ=pravidla\" method=post enctype=\"multipart/form-data\">";
      echo "soubor: <input type=\"file\" name=\"soubor_prav\" value=\"$soubor_prav\">";
      echo "<p>";
      echo "<input type = \"submit\" value=\"odeslat soubor\" name=\"odeslano_pravidla\">";
      echo "</form>";
    break;
    case 4:
    /**** Obnova celého katalogu v databázi (pouze pro admina) ****/
      Tlacitka($kod, "ic_katalog_akt.php", $pole_vyberu, $pole_tlacitek, 4);

      if($skupina==1)
      {
        echo "Soubor aktualizující celý katalog knihovny musí být ve formátu txt, kódovaný v ISO-8859-2 a obsahovat øádky ve tvaru:
        identifikaèní èíslo knihy|autor|název|výpùjèní doba|poèet kusù.";
        echo "<font color=\"red\"><p>Dosavadní data budou nenávratnì ztracena!!!</font>";


        echo "<form action=\"./ic_katalog_akt.php?kod=$kod\" method=post enctype=\"multipart/form-data\">";
        echo "soubor: <input type=\"file\" name=\"soubor\" value=\"$soubor\">";
        echo "<p>";
        echo "<input type = \"submit\" value=\"odeslat soubor\" name=\"odeslano_kat\">";
        echo "</form>";

        if($odeslano_kat)
        {
          $SQL = "delete from ic_knihy";
          DB_exec($SQL);
          $novy = "./files_ic/seznam.txt";
          echo "<p>velikost souboru = ".FileSize($soubor);
          if(FileSize($soubor)<>0)
          {
            copy($soubor, $novy);
            /*$katalog = fopen($novy, r);*/
            $katalog = file($novy);
            $i = 0;
            while($i<count($katalog))
            {
              $polozka = explode('|', $katalog[$i]);
              /*echo "<p>id = ".$polozka[0];
              echo "<p>autor = ".$polozka[1];
              echo "<p>nazev = ".$polozka[2];
              echo "<p>vypujcni doba = ".$polozka[3];
              echo "<p>pocet ks = ".$polozka[4];*/
              $id = $polozka[0];
              $autor = $polozka[1];
              $nazev = $polozka[2];
              $zkr_doby = $polozka[3];
              $pocet = $polozka[4];
        /*    $kategorie = $polozka[5];*/

              $SQL = "insert into ic_knihy (id, autor, nazev, zkr_doby, pocet)
                      values ('$id', '$autor', '$nazev', '$zkr_doby', '$pocet')";
              DB_exec($SQL);
              $i++;
            }
          }
        }
      }
      else
      {
        echo Text_alter("","Na po¾adovanou akci nemáte dostateèná práva");
      }

    break;
  }
}
?>


