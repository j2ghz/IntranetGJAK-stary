<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba)):
  NoCACHE();
  Hlavicka("Aktualizace sd�len� �editele �koly", $fullname, $kod);
  Konec();
endif;
?>
