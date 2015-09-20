<?
function Odkaz($soubor, $nazev, $podminky="")
{
  global $kod;
  return "<A class=\"odkaz\" HREF = \"".c_Cesta.$soubor."?kod=$kod$podminky\">$nazev</A><BR>";
}

function Odkaz_NewTarget($soubor, $nazev)
{
  global $kod;
  return "<A class=\"odkaz\" HREF = \"".c_Cesta.$soubor."\" target = \"_new\" >$nazev</A><BR>";
}


function Nazev($nazev)
{
  return "<div style=\"line-height: 5px\">&nbsp;</div><div style=\"border-top: 1px solid #0C1072; line-height: 5px\">&nbsp;</div><span class=\"nadpis\">$nazev</span><br>";
}


$pom = explode("|", $kod);
$skupina = $pom[1];

$prava=6;
$SQL = "select prava from skupiny where id='$skupina'";
if(DB_select($SQL, $vyst, $pocet))
  if($zaz=mysql_fetch_array($vyst))
  {
     $prava=$zaz["prava"];
  }


switch($prava):
  /* admin */
  case 1:
      echo Odkaz("logout.php", "Odhl�sit se z IS");
    /*echo Odkaz("help.php", "V�echno o IS");*/
     echo Odkaz("is_main.php", "Novinky");
     echo Odkaz("novinky_edit.php", "Editace novinek");
     echo Odkaz("hledani.php", "Vyhled�v�n� soubor� na ISu");
     echo Nazev("Zam�stnanci");
     echo Odkaz("ucitele.php", "Seznam zam�stnanc�");
     echo Odkaz("ucitele_udaje_admin.php", "Opravit �daje zam�stnanc�");  /* !!! nutno pozdeji opravit na ucitele_udaje_admin */
     echo Odkaz("ucitele_novy.php", "P�idat zam�stnance");
     echo Odkaz("ucitele_smaz.php", "Odstranit zam�stnance");
     echo Odkaz("ucitele_foto.php", "Fotografie zam�stnanc�");
     echo Nazev("Vzkazy");
     echo Odkaz("vzkazy_vedeni.php", "Zobrazit vzkazy veden�");
     echo Odkaz("vzkazy_komisi.php", "Zobrazit vzkazy PK");
     echo Odkaz("vzkazy.php", "Zobrazit vzkazy");
     echo Odkaz("vzkazy_odeslat.php", "Odeslat vzkaz");
     echo Odkaz("vzkazy_editace.php", "Mnou odeslan� vzkazy");
     echo Odkaz("vzkazy_vsechny.php", "Zobrazit v�echny vzkazy");
     echo Nazev("Soubory");
     echo Odkaz("soubory.php", "Zobrazit soubory");
     echo Odkaz("soubory_odeslat.php", "Ulo�it soubor");
     echo Odkaz("soubory_editace.php", "Mnou ulo�en� soubory");
     echo Odkaz("soubory_vsechny.php", "V�echny soubory");
     echo Odkaz("soubory_edit_vse.php", "Maz�n� lib. soubor�");
     echo Nazev("Pl�nov�n�");
     echo Odkaz_NewTarget("./is/rozvrh/rozvrh.htm", "Rozvrhy hodin");
     echo Odkaz_NewTarget("./is/suplovani/suplov.htm", "Suplov�n�");
     echo Odkaz("plan_kal.php","Kalend�� akc�");
     echo Odkaz("plan_kal_editace.php", "Editace kalend��e");
     echo Odkaz("plan_uc.php","Zpr�vy pro veden�");
     echo Odkaz("zvoneni.php", "Zvon�n�");
  /*   echo Nazev("Informa�n� centrum");
     echo Odkaz("ic.php", "IC");
     echo Odkaz("ic_katalog_akt.php", "IC - aktualizace");      */
     /*echo Odkaz("plan_kal_opr.php", "Zm�na tabulek kal.");*/
     echo Nazev("Pro u�itele");
     echo Odkaz("uc_forms.php", "V�echny kategorie", "&typ=vse");
     echo Odkaz("uc_forms.php", "Pokyny �editele", "&typ=sdeleni_red");
     echo Odkaz("uc_forms.php", "Pokyny z�stupc�", "&typ=sdeleni_zast");
     echo Odkaz("uc_forms.php", "Formul��e pro u�itele", "&typ=formular");
     echo Odkaz("uc_forms.php", "Seznamy student�", "&typ=seznam");
     echo Odkaz("uc_forms.php", "Vnit�n� sm�rnice", "&typ=smernice");
     echo Odkaz("uc_forms.php", "Organiza�n�&nbsp;zabezpe�en�&nbsp;VVP", "&typ=organizace");
     echo Odkaz("uc_forms.php", "Pracovn� n�pln�", "&typ=naplne");
     echo Odkaz("uc_forms.php", "SRPG", "&typ=srpg");
     echo Odkaz("uc_forms.php", "Ostatn�", "&typ=ostatni");
     echo Odkaz("uc_edit.php", "Aktualizace&nbsp;soubor�");

     echo Nazev("Pro studenty");
     echo Odkaz("g_forms.php", "Vnit�n� p�edpisy", "&typ=g_predpisy");
     echo Odkaz("g_forms.php", "Formul��e pro studenty", "&typ=g_formular");
     echo Odkaz("g_forms.php", "Krou�ky", "&typ=g_krouzky");
     echo "Voliteln� p�edm�ty:<br>";
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">Seznam p�edm�t�", "&typ=g_vol_seznam");
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">N�pln�&nbsp;jednolet�ch&nbsp;p�edm�t�", "&typ=g_vol_naplne1");
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">N�pln�&nbsp;dvoulet�ch&nbsp;p�edm�t�", "&typ=g_vol_naplne2");
     echo Odkaz("g_forms.php", "Ostatn�", "&typ=g_ostatni");
     echo Odkaz("g_edit.php", "Aktualizace&nbsp;soubor�");
     
     echo Nazev("Pro admina");
     echo Odkaz("admin_data.php", "Spr�va dat");

     echo Nazev("Kraj&nbsp;-&nbsp;�kolstv�");
     echo Odkaz("k_forms.php", "Zpravodaj K�", "&typ=k_zpravodaj");
     echo Odkaz("k_forms.php", "Sm�rnice K�", "&typ=k_smernice");
     echo Odkaz("k_forms.php", "Ostatn�", "&typ=k_ostatni");
     echo Odkaz("k_edit.php", "Aktualizace&nbsp;soubor�");
     echo Odkaz("k_edit_prilohy.php", "Aktualizace&nbsp;p��loh");
   break;

/* �editel, zastupci */
  case 2:
      echo Odkaz("logout.php", "Odhl�sit se z is");
    /*echo Odkaz("help.php", "V�echno o is");*/
     echo Odkaz("is_main.php", "Novinky");
     echo Nazev("Zam�stnanci");
     echo Odkaz("ucitele.php", "Seznam zam�stnanc�");
     echo Odkaz("ucitele_udaje_admin.php", "Opravit �daje zam�stnanc�");  /* !!! nutno pozdeji opravit na ucitele_udaje_admin */
     echo Odkaz("ucitele_novy.php", "P�idat zam�stnance");
     echo Odkaz("ucitele_smaz.php", "Odstranit zam�stnance");
     echo Odkaz("ucitele_foto.php", "Fotografie zam�stnanc�");
     echo Nazev("Vzkazy");
     echo Odkaz("vzkazy_vedeni.php", "Zobrazit vzkazy veden�");
     echo Odkaz("vzkazy_komisi.php", "Zobrazit vzkazy PK");
     echo Odkaz("vzkazy.php", "Zobrazit vzkazy");
     echo Odkaz("vzkazy_odeslat.php", "Odeslat vzkaz");
     echo Odkaz("vzkazy_editace.php", "Mnou odeslan� vzkazy");
     echo Nazev("Soubory");
     echo Odkaz("soubory.php", "Zobrazit soubory");
     echo Odkaz("soubory_odeslat.php", "Ulo�it soubor");
     echo Odkaz("soubory_editace.php", "Mnou ulo�en� soubory");
     echo Nazev("Pl�nov�n�");
     echo Odkaz_NewTarget("./is/rozvrh/rozvrh.htm", "Rozvrhy hodin");
     echo Odkaz_NewTarget("./is/suplovani/suplov.htm", "Suplov�n�");
     echo Odkaz("plan_kal.php","Kalend�� akc�");
     echo Odkaz("plan_kal_editace.php", "Editace kalend��e");
     echo Odkaz("plan_uc.php","Zpr�vy pro veden�");
     echo Odkaz("zvoneni.php", "Zvon�n�");
    /* echo Nazev("Informa�n� centrum");
     echo Odkaz("ic.php", "IC");
     echo Odkaz("ic_katalog_akt.php", "IC - aktualizace");    */
     /*echo Odkaz("plan_kal_opr.php", "Zm�na tabulek kal.");*/
     echo Nazev("Pro u�itele");
     echo Odkaz("uc_forms.php", "V�echny kategorie", "&typ=vse");
     echo Odkaz("uc_forms.php", "Pokyny �editele", "&typ=sdeleni_red");
     echo Odkaz("uc_forms.php", "Pokyny z�stupc�", "&typ=sdeleni_zast");
     echo Odkaz("uc_forms.php", "Formul��e pro u�itele", "&typ=formular");
     echo Odkaz("uc_forms.php", "Seznamy student�", "&typ=seznam");
     echo Odkaz("uc_forms.php", "Vnit�n� sm�rnice", "&typ=smernice");
     echo Odkaz("uc_forms.php", "Organiza�n�&nbsp;zabezpe�en�&nbsp;VVP", "&typ=organizace");
     echo Odkaz("uc_forms.php", "Pracovn� n�pln�", "&typ=naplne");
     echo Odkaz("uc_forms.php", "SRPG", "&typ=srpg");
     echo Odkaz("uc_forms.php", "Ostatn�", "&typ=ostatni");
     echo Odkaz("uc_edit.php", "Aktualizace&nbsp;soubor�");

     echo Nazev("Pro studenty");
     echo Odkaz("g_forms.php", "Vnit�n� p�edpisy", "&typ=g_predpisy");
     echo Odkaz("g_forms.php", "Formul��e pro studenty", "&typ=g_formular");
     echo Odkaz("g_forms.php", "Krou�ky", "&typ=g_krouzky");
     echo "Voliteln� p�edm�ty:<br>";
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">Seznam p�edm�t�", "&typ=g_vol_seznam");
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">N�pln�&nbsp;jednolet�ch&nbsp;p�edm�t�", "&typ=g_vol_naplne1");
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">N�pln�&nbsp;dvoulet�ch&nbsp;p�edm�t�", "&typ=g_vol_naplne2");
     echo Odkaz("g_forms.php", "Ostatn�", "&typ=g_ostatni");
     echo Odkaz("g_edit.php", "Aktualizace&nbsp;soubor�");

     echo Nazev("Kraj&nbsp;-&nbsp;�kolstv�");
     echo Odkaz("k_forms.php", "Zpravodaj K�", "&typ=k_zpravodaj");
     echo Odkaz("k_forms.php", "Sm�rnice K�", "&typ=k_smernice");
     echo Odkaz("k_forms.php", "Ostatn�", "&typ=k_ostatni");
     echo Odkaz("k_edit.php", "Aktualizace&nbsp;soubor�");
     echo Odkaz("k_edit_prilohy.php", "Aktualizace&nbsp;p��loh");
/*
     echo Nazev("Ofici�ln� sd�len� �editele");
     echo Odkaz("sdeleni.php", "Zobrazit sd�len�");
     echo Odkaz("sdeleni_aktual.php", "Aktualizovat sd�len�");
     echo Nazev("Formul��e pro u�itele");
     echo Odkaz("forms.php", "Zobrazit formul��e");
     echo Odkaz("forms_aktual.php", "Aktualizovat formul��e");
     echo Nazev("Pro studenty");
     echo Odkaz("povinnost.php", "Povinnosti student�");
     echo Odkaz("povinnost_aktual.php", "Aktualizovat&nbsp;povinnosti");    */
  break;

/* sekretariat */
  case 3:
      echo Odkaz("logout.php", "Odhl�sit se z is");
    /*echo Odkaz("help.php", "V�echno o is");*/
     echo Odkaz("is_main.php", "Novinky");

     echo Nazev("Zam�stnanci");
     echo Odkaz("ucitele.php", "Seznam zam�stnanc�");
     echo Odkaz("ucitele_udaje_admin.php", "Opravit �daje zam�stnanc�");  /* !!! nutno pozdeji opravit na ucitele_udaje_admin */
     echo Odkaz("ucitele_novy.php", "P�idat zam�stnance");
     echo Odkaz("ucitele_smaz.php", "Odstranit zam�stnance");
     echo Odkaz("ucitele_foto.php", "Fotografie zam�stnanc�");

     echo Nazev("Vzkazy");
     echo Odkaz("vzkazy_vedeni.php", "Zobrazit vzkazy veden�");
     echo Odkaz("vzkazy_komisi.php", "Zobrazit vzkazy PK");
     echo Odkaz("vzkazy.php", "Zobrazit vzkazy");
     echo Odkaz("vzkazy_odeslat.php", "Odeslat vzkaz");
     echo Odkaz("vzkazy_editace.php", "Mnou odeslan� vzkazy");

     echo Nazev("Soubory");
     echo Odkaz("soubory.php", "Zobrazit soubory");
     echo Odkaz("soubory_odeslat.php", "Ulo�it soubor");
     echo Odkaz("soubory_editace.php", "Mnou ulo�en� soubory");

     echo Nazev("Pl�nov�n�");
     echo Odkaz_NewTarget("./is/rozvrh/rozvrh.htm", "Rozvrhy hodin");
     echo Odkaz_NewTarget("./is/suplovani/suplov.htm", "Suplov�n�");
     echo Odkaz("plan_kal.php","Kalend�� akc�");
     echo Odkaz("plan_kal_editace.php", "Editace kalend��e");
     echo Odkaz("plan_uc.php","Zpr�vy pro veden�");
     echo Odkaz("zvoneni.php", "Zvon�n�");
 /*    echo Nazev("Informa�n� centrum");
     echo Odkaz("ic.php", "IC");
     echo Odkaz("ic_katalog_akt.php", "IC - aktualizace");    */
     /*echo Odkaz("plan_kal_opr.php", "Zm�na tabulek kal.");*/

     echo Nazev("Pro u�itele");
     echo Odkaz("uc_forms.php", "V�echny kategorie", "&typ=vse");
     echo Odkaz("uc_forms.php", "Pokyny �editele", "&typ=sdeleni_red");
     echo Odkaz("uc_forms.php", "Pokyny z�stupc�", "&typ=sdeleni_zast");
     echo Odkaz("uc_forms.php", "Formul��e pro u�itele", "&typ=formular");
     echo Odkaz("uc_forms.php", "Seznamy student�", "&typ=seznam");
     echo Odkaz("uc_forms.php", "Vnit�n� sm�rnice", "&typ=smernice");
     echo Odkaz("uc_forms.php", "Organiza�n�&nbsp;zabezpe�en�&nbsp;VVP", "&typ=organizace");
     echo Odkaz("uc_forms.php", "Pracovn� n�pln�", "&typ=naplne");
     echo Odkaz("uc_forms.php", "SRPG", "&typ=srpg");
     echo Odkaz("uc_forms.php", "Ostatn�", "&typ=ostatni");
     echo Odkaz("uc_edit.php", "Aktualizace&nbsp;soubor�");
     
     echo Nazev("Pro studenty");
     echo Odkaz("g_forms.php", "Vnit�n� p�edpisy", "&typ=g_predpisy");
     echo Odkaz("g_forms.php", "Formul��e pro studenty", "&typ=g_formular");
     echo Odkaz("g_forms.php", "Krou�ky", "&typ=g_krouzky");
     echo "Voliteln� p�edm�ty:<br>";
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">Seznam p�edm�t�", "&typ=g_vol_seznam");
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">N�pln�&nbsp;jednolet�ch&nbsp;p�edm�t�", "&typ=g_vol_naplne1");
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">N�pln�&nbsp;dvoulet�ch&nbsp;p�edm�t�", "&typ=g_vol_naplne2");
     echo Odkaz("g_forms.php", "Ostatn�", "&typ=g_ostatni");
     echo Odkaz("g_edit.php", "Aktualizace&nbsp;soubor�");

     echo Nazev("Kraj&nbsp;-&nbsp;�kolstv�");
     echo Odkaz("k_forms.php", "Zpravodaj K�", "&typ=k_zpravodaj");
     echo Odkaz("k_forms.php", "Sm�rnice K�", "&typ=k_smernice");
     echo Odkaz("k_forms.php", "Ostatn�", "&typ=k_ostatni");
     echo Odkaz("k_edit.php", "Aktualizace&nbsp;soubor�");
     echo Odkaz("k_edit_prilohy.php", "Aktualizace&nbsp;p��loh");

/*     echo Nazev("Suplov�n�");
     echo Odkaz("supl.php", "Zobrazit suplov�n�");
     echo Odkaz("supl_aktual.php", "Aktualizovat suplov�n�");
     echo Nazev("Ofici�ln� sd�len� �editele");
     echo Odkaz("sdeleni.php", "Zobrazit sd�len�");
     echo Odkaz("sdeleni_aktual.php", "Aktualizovat sd�len�");
     echo Nazev("Formul��e pro u�itele");
     echo Odkaz("forms.php", "Zobrazit formul��e");
     echo Odkaz("forms_aktual.php", "Aktualizovat formul��e");
     echo Nazev("Pro studenty");
     echo Odkaz("povinnost.php", "Povinnosti student�");
     echo Odkaz("povinnost_aktual.php", "Aktualizovat&nbsp;povinnosti");   */
  break;

/* IC */
  case 4:
     echo Odkaz("logout.php", "Odhl�sit se z is");
    /*echo Odkaz("help.php", "V�echno o is");*/
     echo Odkaz("is_main.php", "Novinky");
     echo Nazev("Zam�stnanci");
     echo Odkaz("ucitele.php", "Seznam zam�stnanc�");
     echo Odkaz("ucitele_udaje.php", "Opravit sv� �daje");  /* !!! nutno pozdeji opravit na ucitele_udaje_admin */
     echo Nazev("Vzkazy");
     echo Odkaz("vzkazy_vedeni.php", "Zobrazit vzkazy veden�");
     echo Odkaz("vzkazy_komisi.php", "Zobrazit vzkazy PK");
     echo Odkaz("vzkazy.php", "Zobrazit vzkazy");
     echo Odkaz("vzkazy_odeslat.php", "Odeslat vzkaz");
     echo Odkaz("vzkazy_editace.php", "Mnou odeslan� vzkazy");
     echo Nazev("Soubory");
     echo Odkaz("soubory.php", "Zobrazit soubory");
     echo Odkaz("soubory_odeslat.php", "Ulo�it soubor");
     echo Odkaz("soubory_editace.php", "Mnou ulo�en� soubory");
     echo Nazev("Pl�nov�n�");
     echo Odkaz_NewTarget("./is/rozvrh/rozvrh.htm", "Rozvrhy hodin");
     echo Odkaz_NewTarget("./is/suplovani/suplov.htm", "Suplov�n�");
     echo Odkaz("plan_kal.php","Kalend�� akc�");
     echo Odkaz("plan_uc.php","Zpr�vy pro veden�");
     echo Odkaz("zvoneni.php", "Zvon�n�");
     echo Nazev("Informa�n� centrum");
     echo Odkaz("ic.php", "IC");
     echo Odkaz("ic_katalog_akt.php", "IC - aktualizace");
     /*echo Odkaz("plan_kal_opr.php", "Zm�na tabulek kal.");*/
     echo Nazev("Pro u�itele");
     echo Odkaz("uc_forms.php", "V�echny kategorie", "&typ=vse");
     echo Odkaz("uc_forms.php", "Pokyny �editele", "&typ=sdeleni_red");
     echo Odkaz("uc_forms.php", "Pokyny z�stupc�", "&typ=sdeleni_zast");
     echo Odkaz("uc_forms.php", "Formul��e pro u�itele", "&typ=formular");
     echo Odkaz("uc_forms.php", "Seznamy student�", "&typ=seznam");
     echo Odkaz("uc_forms.php", "Vnit�n� sm�rnice", "&typ=smernice");
     echo Odkaz("uc_forms.php", "Organiza�n�&nbsp;zabezpe�en�&nbsp;VVP", "&typ=organizace");
     echo Odkaz("uc_forms.php", "Pracovn� n�pln�", "&typ=naplne");
     echo Odkaz("uc_forms.php", "SRPG", "&typ=srpg");
     echo Odkaz("uc_forms.php", "Ostatn�", "&typ=ostatni");

     echo Nazev("Pro studenty");
     echo Odkaz("g_forms.php", "Vnit�n� p�edpisy", "&typ=g_predpisy");
     echo Odkaz("g_forms.php", "Formul��e pro studenty", "&typ=g_formular");
     echo Odkaz("g_forms.php", "Krou�ky", "&typ=g_krouzky");
     echo "Voliteln� p�edm�ty:<br>";
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">Seznam p�edm�t�", "&typ=g_vol_seznam");
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">N�pln�&nbsp;jednolet�ch&nbsp;p�edm�t�", "&typ=g_vol_naplne1");
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">N�pln�&nbsp;dvoulet�ch&nbsp;p�edm�t�", "&typ=g_vol_naplne2");
     echo Odkaz("g_forms.php", "Ostatn�", "&typ=g_ostatni");

     echo Nazev("Kraj&nbsp;-&nbsp;�kolstv�");
     echo Odkaz("k_forms.php", "Zpravodaj K�", "&typ=k_zpravodaj");
     echo Odkaz("k_forms.php", "Sm�rnice K�", "&typ=k_smernice");
     echo Odkaz("k_forms.php", "Ostatn�", "&typ=k_ostatni");
 /*    echo Nazev("Suplov�n�");
     echo Odkaz("supl.php", "Zobrazit suplov�n�");
     echo Nazev("Ofici�ln� sd�len� �editele");
     echo Odkaz("sdeleni.php", "Zobrazit sd�len�");
     echo Nazev("Formul��e pro u�itele");
     echo Odkaz("forms.php", "Zobrazit&nbsp;formul��e");
     echo Nazev("Pro studenty");
     echo Odkaz("povinnost.php", "Povinnosti student�");  */
  break;

/* u�itel, extern� u�itel, jidelna, ostatni zamestnanci */
  case 5:
     echo Odkaz("logout.php", "Odhl�sit se z is");
    /*echo Odkaz("help.php", "V�echno o is");*/
     echo Odkaz("is_main.php", "Novinky");
     echo Nazev("Zam�stnanci");
     echo Odkaz("ucitele.php", "Seznam zam�stnanc�");
     echo Odkaz("ucitele_udaje.php", "Opravit sv� �daje");  /* !!! nutno pozdeji opravit na ucitele_udaje_admin */
     echo Nazev("Vzkazy");
     echo Odkaz("vzkazy_vedeni.php", "Zobrazit vzkazy veden�");
     echo Odkaz("vzkazy_komisi.php", "Zobrazit vzkazy PK");
     echo Odkaz("vzkazy.php", "Zobrazit vzkazy");
     echo Odkaz("vzkazy_odeslat.php", "Odeslat vzkaz");
     echo Odkaz("vzkazy_editace.php", "Mnou odeslan� vzkazy");
     echo Nazev("Soubory");
     echo Odkaz("soubory.php", "Zobrazit soubory");
     echo Odkaz("soubory_odeslat.php", "Ulo�it soubor");
     echo Odkaz("soubory_editace.php", "Mnou ulo�en� soubory");
     echo Nazev("Pl�nov�n�");
     echo Odkaz_NewTarget("./is/rozvrh/rozvrh.htm", "Rozvrhy hodin");
     echo Odkaz_NewTarget("./is/suplovani/suplov.htm", "Suplov�n�");
     echo Odkaz("plan_kal.php","Kalend�� akc�");
     echo Odkaz("plan_uc.php","Zpr�vy pro veden�");
     echo Odkaz("zvoneni.php", "Zvon�n�");
 /*    echo Nazev("Informa�n� centrum");
     echo Odkaz("ic.php", "IC");
     echo Odkaz("ic_katalog_akt.php", "IC - aktualizace");    */
     /*echo Odkaz("plan_kal_opr.php", "Zm�na tabulek kal.");*/
     echo Nazev("Pro u�itele");
     echo Odkaz("uc_forms.php", "V�echny kategorie", "&typ=vse");
     echo Odkaz("uc_forms.php", "Pokyny �editele", "&typ=sdeleni_red");
     echo Odkaz("uc_forms.php", "Pokyny z�stupc�", "&typ=sdeleni_zast");
     echo Odkaz("uc_forms.php", "Formul��e pro u�itele", "&typ=formular");
     echo Odkaz("uc_forms.php", "Seznamy student�", "&typ=seznam");
     echo Odkaz("uc_forms.php", "Vnit�n� sm�rnice", "&typ=smernice");
     echo Odkaz("uc_forms.php", "Organiza�n�&nbsp;zabezpe�en�&nbsp;VVP", "&typ=organizace");
     echo Odkaz("uc_forms.php", "Pracovn� n�pln�", "&typ=naplne");
     echo Odkaz("uc_forms.php", "SRPG", "&typ=srpg");
     echo Odkaz("uc_forms.php", "Ostatn�", "&typ=ostatni");

     echo Nazev("Pro studenty");
     echo Odkaz("g_forms.php", "Vnit�n� p�edpisy", "&typ=g_predpisy");
     echo Odkaz("g_forms.php", "Formul��e pro studenty", "&typ=g_formular");
     echo Odkaz("g_forms.php", "Krou�ky", "&typ=g_krouzky");
     echo "Voliteln� p�edm�ty:<br>";
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">Seznam p�edm�t�", "&typ=g_vol_seznam");
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">N�pln�&nbsp;jednolet�ch&nbsp;p�edm�t�", "&typ=g_vol_naplne1");
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">N�pln�&nbsp;dvoulet�ch&nbsp;p�edm�t�", "&typ=g_vol_naplne2");
     echo Odkaz("g_forms.php", "Ostatn�", "&typ=g_ostatni");

     echo Nazev("Kraj&nbsp;-&nbsp;�kolstv�");
     echo Odkaz("k_forms.php", "Zpravodaj K�", "&typ=k_zpravodaj");
     echo Odkaz("k_forms.php", "Sm�rnice K�", "&typ=k_smernice");
     echo Odkaz("k_forms.php", "Ostatn�", "&typ=k_ostatni");

/*     echo Nazev("Formul��e pro u�itele");
     echo Odkaz("forms.php", "Zobrazit formul��e");
     echo Odkaz("forms_aktual.php", "Aktualizovat&nbsp;formul��e");
     echo Nazev("Pro studenty");
     echo Odkaz("povinnost.php", "Povinnosti student�");    */
  break;

/*     echo Nazev("Formul��e pro u�itele");
     echo Odkaz("forms.php", "Zobrazit formul��e");
     echo Odkaz("forms_aktual.php", "Aktualizovat&nbsp;formul��e");
     echo Nazev("Pro studenty");
     echo Odkaz("povinnost.php", "Povinnosti student�");    */

/* studenti a ostatni */
  default:
      echo Odkaz("logout.php", "Odhl�sit se z is");
    /*echo Odkaz("help.php", "V�echno o is");*/
     echo Odkaz("is_main.php", "Novinky");
     echo Nazev("Zam�stnanci");
     echo Odkaz("ucitele.php", "Seznam zam�stnanc�");
     echo Nazev("Vzkazy");
     echo Odkaz("vzkazy_vedeni.php", "Zobrazit vzkazy veden�");
     echo Odkaz("vzkazy.php", "Zobrazit vzkazy");
     echo Nazev("Soubory");
     echo Odkaz("soubory.php", "Zobrazit soubory");
     echo Nazev("Pl�nov�n�");
     echo Odkaz_NewTarget("./is/rozvrh/rozvrh.htm", "Rozvrhy hodin");
     echo Odkaz_NewTarget("./is/suplovani/suplov.htm", "Suplov�n�");
     echo Odkaz("plan_kal.php","Kalend�� akc�");
     echo Odkaz("zvoneni.php", "Zvon�n�");
  /*   echo Nazev("Informa�n� centrum");
     echo Odkaz("ic.php", "IC");
     echo Odkaz("ic_katalog_akt.php", "IC - aktualizace");        */
     /*echo Odkaz("plan_kal_opr.php", "Zm�na tabulek kal.");*/
     echo Nazev("Pro studenty");
     echo Odkaz("g_forms.php", "Vnit�n� p�edpisy", "&typ=g_predpisy");
     echo Odkaz("g_forms.php", "Formul��e pro studenty", "&typ=g_formular");
     echo Odkaz("g_forms.php", "Krou�ky", "&typ=g_krouzky");
     echo "Voliteln� p�edm�ty:<br>";
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">Seznam p�edm�t�", "&typ=g_vol_seznam");
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">N�pln�&nbsp;jednolet�ch&nbsp;p�edm�t�", "&typ=g_vol_naplne1");
     echo Odkaz("g_forms.php", "<img src=\"./images/sip.gif\" border=\"0\">N�pln�&nbsp;dvoulet�ch&nbsp;p�edm�t�", "&typ=g_vol_naplne2");
     echo Odkaz("g_forms.php", "Ostatn�", "&typ=g_ostatni");

  /*   echo Nazev("Kraj&nbsp;-&nbsp;�kolstv�");
     echo Odkaz("k_forms.php", "Zpravodaj K�", "&typ=k_zpravodaj");
     echo Odkaz("k_forms.php", "Sm�rnice K�", "&typ=k_smernice");
     echo Odkaz("k_forms.php", "Ostatn�", "&typ=k_ostatni");
   */

/*     echo Nazev("Pro studenty");
     echo Odkaz("supl.php", "Zobrazit&nbsp;suplov�n�");
     echo Odkaz("povinnost.php", "Povinnosti student�");   */
  break;

endswitch;


?>
