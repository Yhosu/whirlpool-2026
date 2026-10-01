<?php

function vardump()
{
    $arg_list = func_get_args();
    foreach ($arg_list as $variable) {
        echo '<pre style="color: #000; background-color: #fff;">';
        echo htmlspecialchars(var_export($variable, true));
        echo '</pre>';
    }
}

function diedump()
{
    $arg_list = func_get_args();
    foreach ($arg_list as $variable) {
        echo '<pre style="color: #000; background-color: #fff;">';
        echo htmlspecialchars(var_export($variable, true));
        echo '</pre>';
    }
    exit();
}
function decryptRsa($crypted_text)
{
    $crypted_text = base64_decode($crypted_text);
    $key = 'ZPNPigWvDuqvTjvqtQDb5CDUM7FTbPTj';
    $iv = 'hP7TeIeXBZHSoYQi';
    $method = 'AES-256-CBC';
    $text = openssl_decrypt($crypted_text, $method, $key, OPENSSL_RAW_DATA, $iv);

    return $text;
}

function encryptRsa($text)
{
    $key = 'ZPNPigWvDuqvTjvqtQDb5CDUM7FTbPTj';
    $iv = 'hP7TeIeXBZHSoYQi';
    $method = 'AES-256-CBC';
    $ciphertext = openssl_encrypt($text, $method, $key, OPENSSL_RAW_DATA, $iv);
    $text = openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);

    return base64_encode($ciphertext);
}

function generateUuid()
{
    $uuid = Str::uuid();
    $uuidString = $uuid->toString();

    return $uuidString;
}

function split_name($name)
{
    $name = trim($name);
    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
    $first_name = trim(preg_replace('#'.preg_quote($last_name, '#').'#', '', $name));

    return [$first_name, $last_name];
}

function clean4search($text, $cleanSpecialData = true)
{
    $a = 'áéíóúñüÁÉÍÓÚÑÜ';
    $b = 'aeiounuaeiounu';
    $text = utf8_decode($text);
    $text = strtr($text, utf8_decode($a), $b);
    $text = strtolower($text);
    $text = strtolower($text);
    if ($cleanSpecialData) {
        $text = preg_replace('#[^a-z0-9@."]#', ' ', $text);
    }
    $text = trim(preg_replace('#[[:space:]]+#', ' ', $text));
    $undoPos = 0;
    $undo = [];
    /* Se respetarán los textos entre comillas */
    while (preg_match('#"[^"]+"#', $text, $quoted)) {
        $undo[++$undoPos] = trim($quoted);
        $text = str_replace($quoted, "[:sac_undo_$undoPos:]", $text);
    }
    $text = str_replace('"', '', $text);
    if ($undoPos) {
        foreach ($undo as $undoGet => $undoText) {
            $text = str_replace("[:sac_undo_$undoGet:]", $undoText, $text);
        }
    }

    return trim($text);
}

function getTypeField($table_name, $field)
{
    return DB::getSchemaBuilder()->getColumnType($table_name, $field);
}

function getCardType($card_number)
{
    return preg_match('/^5[1-5][0-9]{14}$/', $card_number) ? 'MASTERCARD' : 'VISA';
}

function currencyFormat(
    $number,
    $decimal = 2,
    $negativeFormat = '<span class="neg">(%s)</span>')
{
    if (! is_int($decimal)) {
        $decimal = 2;
    }
    if (empty($number)) {
        $number = 0;
    }

    return sprintf(
        ($number < 0) ? $negativeFormat : '%s',
        number_format(abs($number), $decimal, ',', '.'));
}
