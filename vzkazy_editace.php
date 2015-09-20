<? include ("./include/unit.php");
if(Prihlasen3($kod, $REMOTE_ADDR, $skupina, 5, $fullname, $login, $chyba))
{
  NoCACHE();
  Hlavicka("Editace vzkaz�", $fullname, $kod);
  for($i=0;$i<=4;$i++) $selected[$i] = "";
  if($odeslano_edit_del) $id_vzkazu="";
  if($id_vzkazu=="")
  {
    if($odeslano)
    {
      switch($vyber)
      {
        case 0:
             $razeni = "v.datum desc";
             $selected[0] = "selected";
             break;
        case 1:
             $razeni = "v.platnost_do desc";
             $selected[1] = "selected";
             break;
      }
    }
    else
    {
      $razeni = "v.datum desc";
      $selected[0] = "selected";
    }
    if($odeslano_vymaz or $odeslano_edit_del)
    {
      $podminka_vs = " id_vzkaz = '".$vymaz[0]."' ";
      $podminka_v = " id = '".$vymaz[0]."' ";
      for($i=1;$i<count($vymaz);$i++)
      {
        $podminka_vs .= " or id_vzkaz = '".$vymaz[$i]."' ";
        $podminka_v .= " or id = '".$vymaz[$i]."' ";
      }
      $SQL = "delete from vzkazy_skupiny where $podminka_vs";
      DB_exec($SQL);
      $SQL = "delete from vzkazy where $podminka_v";
      DB_exec($SQL);
    }
    $SQL = "      select distinct v.id id_vzkazu, v.text text, v.datum datum,
                         v.platnost_do platnost_do, v.trida trida, v.predmet predmet,
                         u.jmeno jmeno, u.prijmeni prijmeni, s.skupina skupina
                  from vzkazy_skupiny vs, vzkazy v,  ucitele u, skupiny s
                  where (v.platnost_do>=Now() or v.platnost_do='0000-00-00' or v.platnost_do is null) and
                        u.login = v.login_uc and
                        s.id = v.id_skup_odesilatel and
                        v.id = vs.id_vzkaz and
                        v.login_uc = '$login'
                  order by $razeni ";
    if(DB_select($SQL, $vystup, $pocet))
      if($pocet>0)
      {
        echo "<form method=post>�adit zpr�vy podle ";
        echo "<select name=\"vyber\">";
        echo "<option value=\"0\" ".$selected[0]."> data odesl�n� zpr�vy";
        echo "<option value=\"1\" ".$selected[1]."> data ukon�en� platnosti zpr�vy";
        echo "</select>";
        echo "&nbsp;<input type=submit value=\"zobraz zpr�vy\" name=\"odeslano\">";
        echo "</form>";
        echo "<P>&nbsp;</P><P>Po�et z�znam�: $pocet";
        echo "<form method=\"post\">";
        echo "<p><table border=0 cellspacing=0 cellpadding=5>";
        Zahlavi_radek(array("Editace", "Maz�n�", "T��da", "P�edm�t", "Datum odesl�n�", "Platnost do"), "left");
        $i=0;
        while($zaz=MySQL_fetch_array($vystup))
        {
          echo "<tr>";
          echo "<td><a href=\"./vzkazy_editace.php?kod=$kod&id_vzkazu=".$zaz["id_vzkazu"]."\"><img src=\"./images/edit.gif\" border=none></a></td>";
          echo "<td><input type=\"checkbox\" name=\"vymaz[]\" value=\"".$zaz["id_vzkazu"]."\"></td>";
          echo "<td>".$zaz["trida"]."</td>";
          echo "<td>".$zaz["predmet"]."</td>";
          echo "<td>".Datum($zaz["datum"])."</td>";
          echo "<td>".Text_alter(Datum($zaz["platnost_do"], 0), "neuvedena")."</td>";
          echo "</tr>";
          $i++;
        }
        echo "<tr><td colspan=\"5\"><input type=\"submit\" name=\"odeslano_vymaz\" value=\"vymazat vybran� zpr�vy\"></td></tr>";
        echo "</table>";
        echo "</form>";
      }
      else echo "<p><i>Neodeslal(a) jste ��dnou zpr�vu.</i>";
  }
  else
  {
    if($odeslano_edit)
      {
        $platnost_do = Datum_datab($platnost_do);
        $SQL = "update vzkazy set platnost_do = '$platnost_do' where id = '$id_vzkazu'";
        DB_exec($SQL);
        if(count($prijemce)<>0)
        {
	  $SQL = "delete from vzkazy_skupiny where id_vzkaz = '$id_vzkazu'";
          DB_exec($SQL);
	  for($i=0; $i<count($prijemce); $i++)
          {
            $SQL = "insert into vzkazy_skupiny (id_skup, id_vzkaz) values ('".$prijemce[$i]."', '$id_vzkazu')";
            DB_exec($SQL);
          }
        }
        else
        {
          if(count($vsichni_studenti[0])<>0)
          {
            $SQL = "delete from vzkazy_skupiny where id_vzkaz = '$id_vzkazu'";
            DB_exec($SQL);
            $SQL = "insert into vzkazy_skupiny (id_skup, id_vzkaz) values ('-1', '$id_vzkazu')";
            DB_exec($SQL);
          }
          else
          {
            if(count($vsichni[0])<>0)
            {
              $SQL = "delete from vzkazy_skupiny where id_vzkaz = '$id_vzkazu'";
              DB_exec($SQL);
              $SQL = "insert into vzkazy_skupiny (id_skup, id_vzkaz) values ('-2', '$id_vzkazu')";
              DB_exec($SQL);
            }
          }
        }
      }
    $SQL = "select *
            from vzkazy v, skupiny s, vzkazy_skupiny vs
            where v.id = '$id_vzkazu' and
                  vs.id_vzkaz = v.id and
                  s.id = vs.id_skup and
                  v.login_uc='$login'";
    if(DB_select($SQL, $vystup, $pocet))
    {
      if($zaz = MySQL_fetch_array($vystup))
      {
        $platnost_do = Datum_bez_mezer($zaz["platnost_do"], 0);
        echo "<form method=\"post\">";
        echo "<table>";
        echo "<tr><td><a href=\"./vzkazy_editace.php?kod=$kod\"><img src=\"./images/sipka.gif\" border=none><P></P></a></td></tr>";
        echo "<tr><td colspan=3><b><".c_font.">P��jemce zpr�vy (ro�n�k):</font></b>";
        echo "<br><font color=gray><small>- zvol�te-li <i>u�itel</i>, zpr�va se zobraz� i �editeli, z�stupc�m a administr�tor�m<br>";
        echo "- jednotliv� skupiny maj� p�ednost p�ed volbou <i>v�ichni</i> i <i>v�ichni studenti</i></li></small></font></td></tr>";
        $SQL = "select * from skupiny where id<100 order by id";
        DB_select($SQL, $vystup, $pocet);
        $i=0;
        while($zaznam = MySQL_fetch_array($vystup))
        {
          $skup[$i] = new Cskupina($zaznam["id"], $zaznam["skupina"]);
          $i++;
        }
        for($i=0;$i<count($skup);$i++) $checked[$i] = "";
	$SQL = "select id_skup from vzkazy_skupiny where id_vzkaz = '$id_vzkazu'";
        DB_select($SQL, $vystup, $pocet);
        while($zaznam = MySQL_fetch_array($vystup))
          for($i=0; $i<count($skup); $i++)
            if($skup[$i]->id==$zaznam["id_skup"]) $checked[$i] = " checked";
        echo "<tr><td><br><input type=\"checkbox\" name=\"vsichni[]\" value=\"".$skup[0]->id."\" ".$checked[0].">".$skup[0]->skupina;
        echo "<br><input type=\"checkbox\" name=\"vsichni_studenti[]\" value=\"".$skup[1]->id."\" ".$checked[1].">".$skup[1]->skupina."</td></tr>";
        echo "<tr><td colspan=3><hr></td></tr>";
        echo "<tr><td valign=\"top\">";
        for($i=2; $i<count($skup); $i++)
        {
          if((($i-2)%9)==0 and $i<>2)
          {
            echo "</td><td valign=\"top\">";
            echo "<input type=\"checkbox\" name=\"prijemce[]\" value=\"".$skup[$i]->id."\" ".$checked[$i].">".$skup[$i]->skupina;
          }
          else if($i==2) echo "<input type=\"checkbox\" name=\"prijemce[]\" value=\"".$skup[$i]->id."\" ".$checked[$i].">".$skup[$i]->skupina;
               else echo "<br><input type=\"checkbox\" name=\"prijemce[]\" value=\"".$skup[$i]->id."\" ".$checked[$i].">".$skup[$i]->skupina;
        }
        echo "</td></tr></table>";
        echo "<table border=\"0\"><p><tr><td><b><".c_font.">Platnost zpr�vy do:</b>";
      	echo "<br><font color=gray><small>- datum pi�te ve form�tu <i>den.m�s�c.rok</i> bez mezer, rok na 4 ��slice</small></font>";
      	echo "<br><input type=\"text\" name=\"platnost_do\" value=\"$platnost_do\"></td></tr>";
        echo "</table>";
	echo "<table>";

        echo "<TR><td colspan=2>&nbsp;</td></tr>";
/*        echo "<tr><td width=\"150\"><b>P��jemce zpr�vy:</b></td><td><input type=\"text\" name=\"skupiny\" value=\"$skupiny\">".$zaz["trida"]."</td></tr>";*/
        echo "<tr><td width=\"150\"><b>Pro t��dy/skupiny:</b></td><td>".Text_alter($zaz["trida"], "neuvedeno")."</td></tr>";
        echo "<tr><td width=\"150\"><b>P�edm�t:</b></td><td>".Text_alter($zaz["predmet"], "neuvedeno")."</td></tr>";
        echo "<tr><td colspan=2>&nbsp;</td></tr><tr><td colspan=2><i>".Text_alter($zaz["text"], "zpr�va neobsahuje ��dn� text")."</i></td></tr>";
        echo "<tr><td colspan=2>&nbsp;</td></tr>";
        echo "<input type=\"hidden\" name=\"vymaz[0]\" value=\"$id_vzkazu\">";
        echo "<tr><td colspan=2><input type=\"submit\" name=\"odeslano_edit\" value=\"potvrdit zm�ny\">&nbsp;<input type=\"submit\" name=\"odeslano_edit_del\" value=\"odstranit zpr�vu\"></td></tr>";
	echo "</table>";
        echo "<P>";

      }
    }
  }
  Konec();
}
?>
