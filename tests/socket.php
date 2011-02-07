<?php
error_reporting(E_ALL);

/* Autorise l'exécution infinie du script, en attente de connexion. */
set_time_limit(100);

/* Active le vidage implicite des buffers de sortie, pour que nous
 * puissions voir ce que nous lisons au fur et  à  mesure. */
ob_implicit_flush();

echo "Ouverture d'un socket en lecture / écriture ! <br/>";

$address = "localhost";
$port = 2000;
$block_length = 10;

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() échoué : raison : " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($sock, $address, $port) === false) {
    echo "socket_bind() échoué: raison : " . socket_strerror(socket_last_error($sock)) . "\n";
}

if (socket_listen($sock, 5) === false) {
    echo "socket_listen() échoué: raison : " . socket_strerror(socket_last_error($sock)) . "\n";
}

do {
    if (($msgsock = socket_accept($sock)) === false) {
        echo "socket_accept() échoué: raison : " . socket_strerror(socket_last_error($sock)) . "\n";
        break;
    }
    /* Send instructions. */
    $msg = "\Bienvenue sur le serveur de test PHP.\n" .
        "Pour quitter, tapez 'quit'. Pour éteindre le serveur, tapez 'shutdown'.\n";
    socket_write($msgsock, $msg, strlen($msg));

    do {
        if (false === ($buf = socket_read($msgsock, $block_length, PHP_NORMAL_READ))) {
            echo "socket_read() échoué, raison : " . socket_strerror(socket_last_error($msgsock)) . "\n";
            break 2;
        }
        if (!$buf = trim($buf)) {
            continue;
        }
        if ($buf == 'quit') {
            break;
        }
        if ($buf == 'shutdown') {
            socket_close($msgsock);
            break 2;
        }
        $talkback = "PHP (taille max '$block_length'): Tu as dit '$buf' .\n";
        socket_write($msgsock, $talkback, strlen($talkback));
        echo "$buf\n";
    } while (true);
    socket_close($msgsock);
} while (true);

socket_close($sock);
?>