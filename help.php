<? include ("./include/unit.php");
if(Prihlasen($kod, $REMOTE_ADDR, $skupina, $fullname, $login, $chyba)):
  NoCACHE();
  Hlavicka("V�echno o informa�n�m syst�mu", $fullname, $kod);
  Konec();
endif;
?>
