<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba)):
  NoCACHE();
  Hlavicka("V¹echno o informaèním systému", $fullname, $kod);
  Konec();
endif;
?>
