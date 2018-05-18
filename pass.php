<?php
if (isset($_GET['pass'])) {
    echo md5(floor((time() - $_GET['pass']) / 30));
} else {
    header('location: /Page/error');
}
