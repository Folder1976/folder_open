<?php
/*
LiveImport (c) MaxD, 2016. Write to liveimport@devs.mx for support and purchase.
*/
 goto D6ce; D6ce: $c5d = "\x74\x65\x6d\x70\57"; $ab4 = 512 * 512 * 2; $A0e = true; function B83($B38) { echo json_encode(array("\145\162\x72\157\162" => $B38)); die; } if (isset($_POST)) { goto b9bd; } goto fdc0; dccd: $Cf3 = $_POST["\164\x6f\x74\141\x6c\123\x69\172\x65"] . "\174" . preg_replace("\57\133\x5e\101\x2d\132\141\55\x7a\60\55\71\x5c\x2f\135\57", '', $_POST["\164\x79\160\x65"]) . "\174" . preg_replace("\x2f\133\x5e\101\55\132\141\x2d\172\60\55\x39\x5f\134\56\x5d\x2f", '', $_POST["\x66\x69\154\x65\x4e\141\155\145"]); $Fc9 = time() . rand(1, 150000); $ace = md5($Cf3); if ($C03 = fopen($c5d . $Fc9 . "\55" . $ace . "\x2e\x69\x6e\x66\x6f", "\x77")) { goto f156; } b83("\125\x6e\141\142\x6c\145\40\164\157\x20\x63\162\x65\x61\x74\145\x20\x6e\x65\167\40\146\x69\154\x65\40\x66\x6f\162\x20\155\x65\x74\x61\144\x61\164\141"); goto F3ce; b71d: B83("\116\x6f\x20\146\151\x6c\x65\x20\146\x6f\x75\x6e\144\40\146\157\162\40\x74\150\145\x20\160\x72\157\166\x69\144\145\144\40\111\104\x20\x2f\40\164\157\x6b\145\156"); a08e: unlink($c5d . $_POST["\x66\151\154\x65\151\144"] . "\55" . $_POST["\164\x6f\153\x65\x6e"] . "\x2e\151\156\146\x6f"); $ce6 = array("\x61\x63\164\151\x6f\156" => "\143\x6f\x6d\x70\154\145\x74\145", "\146\x69\154\x65" => $_POST["\x66\x69\154\145\151\144"]); a029: goto F94d; b63c: if (!(fwrite($C03, file_get_contents("\x70\x68\x70\72\x2f\x2f\x69\x6e\160\165\x74")) === FALSE)) { goto fad6; } B83("\x55\156\141\142\x6c\x65\40\x74\157\x20\x77\x72\151\x74\145\x20\164\157\40\160\141\x63\153\141\x67\145\x20\x23" . $_GET["\x70\x61\x63\x6b\x65\x74"]); fad6: fclose($C03); Bdca: goto dd00; A1ae: if (isset($_POST["\164\157\x74\141\x6c\123\151\172\x65"]) && isset($_POST["\x74\x79\x70\x65"]) && isset($_POST["\146\151\x6c\x65\x4e\x61\x6d\145"]) && is_numeric($_POST["\x74\x6f\164\141\154\123\x69\172\x65"])) { goto be83; } if (isset($_POST["\146\x69\154\145\x69\x64"]) && isset($_POST["\164\157\x6b\145\156"]) && is_numeric($_POST["\x66\x69\154\145\151\144"]) && preg_match("\x2f\133\x41\55\x5a\x61\x2d\172\x30\x2d\x39\x5d\57", $_POST["\x74\157\x6b\145\x6e"])) { goto cc50; } goto a029; be83: @unlink($c5d . $_POST["\x66\x69\x6c\145\116\x61\x6d\145"]); goto dccd; e6f4: $ce6 = array("\x61\143\x74\151\x6f\156" => "\x6e\x65\167\x5f\x75\160\x6c\x6f\141\x64", "\146\151\x6c\x65\151\x64" => $Fc9, "\164\157\x6b\145\x6e" => $ace); goto a029; cc50: $D44 = @file_get_contents($c5d . $_POST["\146\151\x6c\145\x69\x64"] . "\55" . $_POST["\164\157\x6b\145\x6e"] . "\56\151\x6e\x66\157"); if ($D44) { goto a08e; } goto b71d; dd00: $ce6 = array("\141\x63\x74\x69\157\156" => "\156\x65\167\x5f\x70\141\x63\x6b\x65\164", "\x72\145\x73\x75\154\x74" => "\x73\x75\x63\x63\145\x73\x73", "\160\141\x63\153\145\x74" => $_GET["\160\x61\143\153\145\x74"]); Ab7b: b672: goto C748; cbaf: goto A1ae; F94d: C748: Dd78: if (!isset($ce6)) { goto edaf; } echo json_encode($ce6); edaf: goto d298; fdc0: b83("\x4e\157\40\160\157\x73\x74\40\162\145\161\165\145\x73\x74"); goto Dd78; b9bd: if (count($_GET) == 1) { goto cbaf; } if (!(isset($_GET["\x66\151\x6c\x65\151\144"]) && isset($_GET["\x74\157\153\145\x6e"]) && isset($_GET["\160\x61\143\153\x65\x74"]) && is_numeric($_GET["\x70\141\143\x6b\x65\x74"]) && is_numeric($_GET["\x66\x69\154\145\151\x64"]))) { goto b672; } goto d342; F3ce: f156: if (!(fwrite($C03, $Cf3) === FALSE)) { goto c051; } B83("\125\156\141\142\154\x65\x20\x74\157\x20\167\x72\x69\164\x65\x20\155\145\x74\141\144\141\164\x61\x20\146\x6f\x72\40\146\151\x6c\145"); c051: fclose($C03); goto e6f4; d342: if (!file_exists($c5d . $_GET["\146\x69\154\145\151\x64"] . "\55" . $_GET["\x74\157\153\145\x6e"] . "\56\x69\x6e\x66\x6f")) { goto Ab7b; } if (!$A0e) { goto Bdca; } if ($C03 = fopen($c5d . $_GET["\x66\x69\154\145\x4e\141\x6d\x65"], "\x61")) { goto E3a6; } die; E3a6: goto b63c; d298: ?>
