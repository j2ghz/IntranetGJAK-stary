<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba)):
  NoCACHE();
  Hlavicka("Aktualizace formuláøù pro uèitele", $fullname, $kod);
  Konec();
endif;
?>
