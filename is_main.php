<?
include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 6, $fullname, $login, $chyba)):
  NoCACHE();
  Hlavicka("Novinky", $fullname, $kod);

  //****************************************************************

  $SQL = "select * from skupiny where id='$skupina'";
  if(DB_select($SQL, $vystup, $pocet))
  {
    if($zaznam=mysql_fetch_array($vystup))
    echo "<h3>Jste pøihlá¹en jako ".$zaznam["skupina"].".</h3>";

    /*"<br>(Pokud do této skupiny nepatøíte,
          oznamte tuto skuteènost co nejdøíve e-mailem <A HREF = \"mailto:l.volfova@oazlin.cz\">správci oasy</a>, popø. pøímo v kabinetì 311.)<P>";*/
  }
  
  
  if($skupina<=19)
  {
    $SQL = "select max(datum) datum_max from soubory where typ='sdeleni_red'";
    if(DB_select($SQL, $vystup, $pocet))
      if($zaznam=mysql_fetch_array($vystup))
      {
        $SQL2 = "select * from soubory where typ='sdeleni_red' and datum='".$zaznam["datum_max"]."'";
        if(DB_select($SQL2, $vystup2, $pocet2))
        {

          if($zaznam2=mysql_fetch_array($vystup2))
          echo "<p>Dùle¾ité soubory: <br><a class=\"seznam\" target=_new HREF=\"sendfile.php?kod=$kod&p_prava=5&p_nazev=".$zaznam2["nazev"]."&p_adresar=files_ucitelum/".$zaznam2["nazev"]."\">aktuální pokyny øeditele</a> <small>(".Datum($zaznam["datum_max"]).")</small>";
        }
      }

    $SQL = "select max(datum) datum_max from soubory where typ='sdeleni_zast'";
    if(DB_select($SQL, $vystup, $pocet))
      if($zaznam=mysql_fetch_array($vystup))
      {
        $SQL2 = "select * from soubory where typ='sdeleni_zast' and datum='".$zaznam["datum_max"]."'";
        if(DB_select($SQL2, $vystup2, $pocet2))
        {

          if($zaznam2=mysql_fetch_array($vystup2))
          echo "<br><a class=\"seznam\" target=_new href=\"sendfile.php?kod=$kod&p_prava=5&p_nazev=".$zaznam2["nazev"]."&p_adresar=files_ucitelum/".$zaznam2["nazev"]."\">aktuální pokyny zástupcù</a> <small>(".Datum($zaznam["datum_max"]).")</small></h4><p>";
        }
      }
  }

  echo "<center><table border=1 bgcolor=\"#dddddd\" cellpadding=\"5\"><tr><td><font color=red> Po skonèení práce se nezapomeòte odhlásit.
              </font><BR> (viz 1. polo¾ka v menu)</td></tr></table></center>";

  $SQL = "select id from novinky where DATE_ADD(datum, INTERVAL 5 DAY)>=Now() order by id";
  if(DB_select($SQL, $vyst, $pocet)) while($zaznam = mysql_fetch_array($vyst)) $id[] = $zaznam["id"];



  $SQL = "select * from novinky where DATE_ADD(datum, INTERVAL 5 DAY)>=Now() order by datum desc";
  if(DB_select($SQL, $vystup, $pocet))
  {
    echo "<dl>";
    while($zaz=mysql_fetch_array($vystup))
    {
      echo "<dt><p><small>".Datum($zaz["datum"])."</small></dt>";
      echo "<dd><font color=\"red\">".$zaz["text"]."</font></dd>";
    }
    echo "</dl>";
  }

    $SQL = "select * from novinky where DATE_ADD(datum, INTERVAL 5 DAY)<Now() order by datum desc";
  if(DB_select($SQL, $vystup, $pocet))
  {
    echo "<dl>";
    while($zaz=mysql_fetch_array($vystup))
    {
      echo "<dt><p><small>".Datum($zaz["datum"])."</small></dt>";
      echo "<dd><font color=\"black\">".$zaz["text"]."</font></dd>";
    }
    echo "</dl>";
  }


  //****************************************************************


  Konec();
endif;

/* funkce pro pricteni libovolneho poctu dni k datumu*/

function day_add_x($datum, $dny)
{
 /* define(d,"2");
  define(m,"1");
  define(r,"0");   */

  for($i=0;$i<$dny;$i++)
  {
    $pom = explode("-", $datum);
    $datum= mktime(1, 0, 0, $pom[m], $pom[d], $pom[r]) + 86400;
  }
  return Date("Y-m-d", $datum);
}


?>
