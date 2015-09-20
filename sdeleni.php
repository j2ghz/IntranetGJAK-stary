<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba)):
  NoCACHE();
  Hlavicka("Oficiální sdìlení øeditele ¹koly", $fullname, $kod);
  Konec();
endif;
?>
