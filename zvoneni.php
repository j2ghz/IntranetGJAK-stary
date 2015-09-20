<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Zvonìní", $fullname, $kod);
  $SQL = "select * from zvoneni order by hodina";
  if(DB_select($SQL, $vyst, $poc))
  {
    echo "<center><table border=0 bgcolor=\"#efefef\" cellspacing=\"10\" class=\"zvoneni\">";
    while($zaz=mysql_fetch_array($vyst))
    {
      echo "<tr><td align=\"right\"><b>".$zaz["hodina"]."</b></td>";
      echo "<td align=\"right\">".Cas($zaz["od"])."</td>";
      echo "<td align=\"center\"> - </td>";
      echo "<td align=\"right\">".Cas($zaz["do"])."</td></tr>";
    }
    echo "</table></center>";
  }
  Konec();
}
?>


