<?php

echo strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http'
?>