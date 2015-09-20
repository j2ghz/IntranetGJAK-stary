<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba)):
  NoCACHE();
  Hlavicka("Rozvrhy", $fullname, $kod);

  include("./rozvrhy/rozvrh.htm");

  Konec();
endif;
?>
