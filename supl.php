<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba)):
  NoCACHE();
  Hlavicka("Suplov�n�", $fullname, $kod);
  Konec();
endif;
?>
