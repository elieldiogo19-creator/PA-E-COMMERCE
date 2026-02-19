<?php
session_start();
session_unset();   // limpa variáveis da sessão
session_destroy(); // destrói a sessão

header('Location: index.php'); // volta pra home
exit;