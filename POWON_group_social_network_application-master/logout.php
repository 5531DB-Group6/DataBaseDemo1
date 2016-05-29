<?php

session_start();

session_destroy();

echo "You've been logged out. <a href='loginPage.php'> Click </a> here to return.  ";

