<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba)):
  NoCACHE();
  Hlavicka("Aktualizace sdìlení øeditele ¹koly", $fullname, $kod);
  Konec();
endif;
?>
