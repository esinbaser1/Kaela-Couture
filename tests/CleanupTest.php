<?php

require_once 'App/Database.php';
require_once 'App/Utils/Cleanup.php';

use Utils\Cleanup;

$cleanup = new Cleanup();
$result = $cleanup->deleteUser();

if (isset($result['error'])) {
    echo $result['error'];
} else {
    echo "Utilisateurs supprimés: " . $result['deletedUsers'] . "\n";
}
