<?
include ("./include/unit.php");
$SQL = "delete from prihl_uziv where kod='$kod'";
DB_exec($SQL);
/*session_unset("oasasession");
session_destroy("oasasession");        */
header("Location: ".c_Cesta."index.php");
?>