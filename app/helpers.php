<?php
function orthodox_eastern($year)
{
    $a = $year % 4;
    $b = $year % 7;
    $c = $year % 19;
    $d = ((19 * $c + 15) % 30);
    $e = (2 * $a + 4 * $b - $d + 34) % 7;
    $month = floor(($d + $e + 114) / 31);
    $day = (($d + $e + 114) % 31) + 2;

    $dt = mktime(0, 0, 0, $month, $day + 13, $year);

    return $dt;
}

function memorial_orthodox_eastern($year)
{
    $a = $year % 4;
    $b = $year % 7;
    $c = $year % 19;
    $d = ((19 * $c + 15) % 30);
    $e = (2 * $a + 4 * $b - $d + 34) % 7;
    $month = floor(($d + $e + 114) / 31);
    $day = (($d + $e + 114) % 31) + 9;

    $dt = mktime(0, 0, 0, $month, $day + 13, $year);

    return $dt;
}

function twHours($hours)
{
    return round($hours, 2);
}
