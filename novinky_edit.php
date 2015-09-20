<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Editace novinek", $fullname, $kod);

  if($skupina==1)
  {
    if($odeslano_vymaz)
    {
      for($i=0;$i<count($vymaz);$i++)
      {
        $SQL = "delete from novinky where id='".$vymaz[$i]."'";
        DB_exec($SQL);
      }
    }
    else
    {
      if($odeslano_new)
      {
        $SQL = "insert into novinky (text, datum) values ('$text', Now())";
        DB_exec($SQL);
      }
    }
    $SQL = "select * from novinky order by datum desc";
    if(DB_select($SQL, $vystup, $pocet))
    {
      echo "<table border=0 cellspacing=2 cellpadding=5><tr><td colspan=\"4\"><form action=\"novinky_edit.php?kod=$kod\" method=\"post\"><textarea name=\"text\" rows=\"4\" cols=\"70\"></textarea></td></tr>";
      echo "<tr><td colspan=\"4\"><input type=\"submit\" name=\"odeslano_new\" value=\"pøidat novinku\"></td></tr>";
      echo "</form>";
      echo "<form action=\"novinky_edit.php?kod=$kod\" method=\"post\">
            <tr bgcolor=\"#dddddd\" align=\"center\">
            <td><b><center>mazání</center></b></td>
            <td><center><b>novinka</b></center></td>
            <td><center><b>datum</b></center></td></tr>";
      while($zaz=mysql_fetch_array($vystup))
      {
        echo "<tr valign=\"top\"><td><input type=\"checkbox\" name=\"vymaz[]\" value=\"".$zaz["id"]."\"></td>";
        echo "<td>".$zaz["text"]."</td>";
        echo "<td>".Datum($zaz["datum"])."</td></tr>";
      }
      echo "<tr><td colspan=\"4\"><input type=\"submit\" name=\"odeslano_vymaz\" value=\"odstranit vybrané novinky\"></form></td></tr></table>";

    }
  }
  Konec();
}
?>
