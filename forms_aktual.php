<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba)):
  NoCACHE();
  Hlavicka("Aktualizace formul��� pro u�itele", $fullname, $kod);
  Konec();
endif;
?>
