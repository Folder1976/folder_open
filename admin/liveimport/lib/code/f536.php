<?php
/*
LiveImport (c) MaxD, 2016. Write to liveimport@devs.mx for support and purchase.
*/
 goto f6cd; f6cd: $b57 = "\x74\145\x6d\160\x2f\146\x65\x65\x64\56\154\x6f\147"; if (@filesize($b57) > 50000) { goto Bd27; } $D3 = @file_get_contents($b57); goto D71c; Bd27: goto Ea53; Ea53: $D3 = @file_get_contents($b57, false, fb, filesize($b57) - 10000); D71c: if ($D3) { goto c4a9; } $D3 = ''; c4a9: goto A8c7; A8c7: @unlink($b57); goto dadf; dadf: echo json_encode($D3);
