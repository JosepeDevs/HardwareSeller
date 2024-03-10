<?php
if(session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
//esto NO hay que protegerlo
session_unset();
session_destroy();
print "<script>history.back();</script>";//ponemos esto por si la pagina de donde salieron no requiere estar logeado, si requiere login entonces sí le dará patada a index
exit;
?>