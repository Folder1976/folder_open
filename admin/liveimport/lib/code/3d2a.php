<?php
/*
LiveImport (c) MaxD, 2016. Write to liveimport@devs.mx for support and purchase.
*/
 goto ffca; D42a: if (@feof($f72)) { goto E4ec; } $d8 = fgets($f72); $Dc0 = ''; if (!strpos($d8, "\x3a\x72\145\144\42")) { goto Cd6c; } $Dc0 = "\162\145\144"; goto b031; bcd7: a9da: require B0 . "\154\x69\142\57\143\157\144\x65\57\x63\x65\62\61\56\x70\x68\160"; ?>
    <h2>Log <?php  if (file_exists($log_file)) { goto af1a; } echo "\x69\163\x20\x65\155\x70\164\171"; goto b255; b031: Cd6c: if (!strpos($d8, "\x3a\147\162\x65\x65\x6e\42")) { goto a7ac; } $Dc0 = "\x67\x72\145\145\156"; a7ac: if (!strpos($d8, "\x3a\160\165\162\160\154\145\x22")) { goto E7e6; } goto E991; b255: af1a: ?>
</h2>
<?php  if (!file_exists($log_file)) { goto Aaec; } @($f72 = fopen($log_file, "\162")); D7a6: goto D42a; Efa2: goto D7a6; E4ec: fclose($f72); Aaec: ?>
    <a id="end"/>
    <a data-transition="reverse" rel="external" data-role="button" data-icon="arrow-l" data-inline="true" href="<?php  goto Beed; d9d4: D76a: $Cd = "\164\145\x6d\160\x2f\155\x78\154\x6f\x67" . rand(1, 10000) . "\x2e\x68\x74\155\x6c"; copy($log_file, $Cd); header("\x4c\x6f\143\141\164\x69\157\x6e\x3a\40" . $Cd); die; goto bcd7; E991: $Dc0 = "\x70\165\x72\x70\154\145"; E7e6: if (!$Dc0) { goto E60e; } echo $d8; E60e: goto Efa2; ffca: if (!isset($_GET["\143\x6c\145\x61\x72"])) { goto fe82; } if (!file_exists($log_file)) { goto C99; } @unlink($log_file); C99: DE(''); goto f3d3; Fd0f: ?>
        <a data-transition="slideup" rel="external" target="_blank" data-role="button" data-icon="grid" data-inline="true" href="<?php  F6(); ?>
log&full=1">Detailed</a>
    <?php  D6c5: ?>
    <a data-transition="slideup" data-role="button" rel="external" data-theme="b" data-icon="delete" data-inline="true" href="<?php  goto ab47; ab47: f6(); ?>
log&clear=1">Clear</a>
    <script type="text/javascript">
        $("html, body").animate({ scrollTop: $(document).height()+50000 }, "slow");
    </script>
<?php  Ff74: goto D7b9; f3d3: fe82: if (!isset($_GET["\146\165\x6c\x6c"])) { goto a9da; } if (!($D0 = glob("\x74\145\x6d\160\x2f\x6d\170\x6c\157\147\52\x2e\x68\164\155\154"))) { goto D76a; } foreach ($D0 as $Cd) { unlink($Cd); b6fc: } c8e9: goto d9d4; Beed: f6(); ?>
">Back</a>
<?php  if (!file_exists($log_file)) { goto Ff74; } ?>
    <?php  if (@$_GET["\x66\x75\154\x6c"]) { goto D6c5; } goto Fd0f; D7b9: ?>
