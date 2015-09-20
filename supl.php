<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba)):
  NoCACHE();
  Hlavicka("Suplování", $fullname, $kod);
  Konec();
endif;
?>
