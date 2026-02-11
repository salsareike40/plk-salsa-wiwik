<?php
session_start();

/* hapus semua data session */
session_unset();
session_destroy();

/* arahkan ke halaman login */
header("Location: index.php");
exit;
