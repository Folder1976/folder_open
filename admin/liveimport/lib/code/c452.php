<?php
/*
LiveImport (c) MaxD, 2016. Write to liveimport@devs.mx for support and purchase.
*/
 goto a734; a734: $B38 = false; if (!($_SERVER["\x52\105\121\x55\x45\x53\x54\137\x4d\x45\x54\110\117\104"] == "\120\117\x53\x54")) { goto a880; } $LI_DB = $_POST["\x64\x61\x74\141\x62\141\x73\x65"]; $LI_DB_SERV = $_POST["\163\x65\x72\166"]; $LI_DB_USER = $_POST["\x75\163\x65\x72"]; goto F119; F119: $LI_DB_PASS = $_POST["\160\141\x73\163"]; if (!li_db_connect()) { goto B7dc; } a48("\114\111\x5f\x44\102", $LI_DB); a48("\114\x49\x5f\104\102\137\123\x45\122\x56", $LI_DB_SERV); A48("\114\111\x5f\x44\x42\x5f\x55\x53\105\x52", $LI_DB_USER); goto b247; c9bf: ?>

    <h2> MySQL Database Details </h2>

    <p><?php  echo Da; ?>
 requires database to keep data.</p>

    <p>Server: <input type="text" name="serv" value="<?php  echo $LI_DB_SERV; ?>
"></p>
    <p>User: <input type="text" name="user" value="<?php  goto D648; C00a: if (!$B38) { goto B15a; } ?>
        <h3 style="color: red"><?php  echo $B38; ?>
</h3>
    <?php  B15a: goto c9bf; f8e7: $LI_DB = "\x6c\151\166\x65\151\x6d\x70\157\162\164"; de82: if (isset($LI_DB_SERV)) { goto Dc66; } $LI_DB_SERV = "\x6c\157\143\141\x6c\x68\157\x73\164"; Dc66: goto De93; D648: echo $LI_DB_USER; ?>
"></p>
    <p>Password: <input type="text" name="pass" value="<?php  echo $LI_DB_PASS; ?>
"></p>
    <p>Database: <input type="text" name="database" value="<?php  echo $LI_DB; goto f617; b247: A48("\x4c\x49\137\104\x42\x5f\120\x41\123\x53", $LI_DB_PASS); de(''); B7dc: a880: if (isset($LI_DB)) { goto de82; } goto f8e7; De93: if (isset($LI_DB_USER)) { goto Bfbf; } $LI_DB_USER = "\x72\x6f\x6f\x74"; Bfbf: if (isset($LI_DB_PASS)) { goto A1e7; } $LI_DB_PASS = "\x72\x6f\157\164"; goto A3a5; A3a5: A1e7: require B0 . "\x6c\151\x62\x2f\143\157\144\x65\x2f\143\145\x32\x31\56\160\x68\x70"; ?>

<form data-ajax="false" action="<?php  F6(); ?>
db" method="post">

    <?php  goto C00a; f617: ?>
"></p>

    <input type="submit" data-direction="reverse" data-inline="true" data-icon="check" data-theme="b" value="Connect"/>


</form>
