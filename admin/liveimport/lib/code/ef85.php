<?php
/*
LiveImport (c) MaxD, 2016. Write to liveimport@devs.mx for support and purchase.
*/
 goto e927; edea: if ($bbc) { goto A321; } De(''); A321: F664: if (!empty($Fb7)) { goto Ad16; } goto D0c4; e927: $bbc = @$_GET["\x63\150\x61\156\x67\145"]; if (!isset($_POST["\x70\141\x73\x73\x77\157\162\x64\62"])) { goto Dd2f; } $bbc = true; Dd2f: if (e5()) { goto bada; } goto dcab; Cd75: $B38 = "\x50\141\163\163\167\x6f\162\144\x73\x20\x64\157\x65\163\156\x27\x74\40\x6d\141\x74\x63\150\x2c\40\164\x72\x79\40\141\x67\x61\151\156\56"; d2f2: a48("\114\x49\x5f\120\x41\x53\123", sha1($_POST["\x70\141\x73\x73\167\157\162\x64"])); A22(); dE(''); goto abbe; ee24: ?>
login" method="post">

    <?php  if (!$B38) { goto Ffaf; } ?>
        <h3 style="color: red"><?php  echo $B38; ?>
</h3>
    <?php  goto d7e0; dcab: if (!$bbc) { goto A533; } $bbc = false; A533: goto F664; bada: goto edea; abbe: Fa9f: A660: require B0 . "\x6c\x69\142\x2f\143\x6f\x64\145\x2f\x63\x65\62\x31\x2e\160\x68\160"; ?>

<form data-ajax="false" action="<?php  f6(); goto ee24; D0c4: $bbc = true; Ad16: $B38 = false; if (!($_SERVER["\122\105\x51\125\x45\x53\124\137\x4d\x45\x54\x48\x4f\x44"] == "\x50\117\x53\x54")) { goto A660; } if ($bbc) { goto E6d7; } goto c3ec; c3ec: if ($Fb7 == sha1($_POST["\x70\141\x73\x73\167\157\x72\x64"])) { goto Cdc1; } $B38 = "\111\156\166\141\x6c\x69\144\x20\160\141\163\x73\x77\x6f\162\144\41"; goto f324; Cdc1: a22(); goto A58f; d7e0: Ffaf: ?>

    <?php  if ($bbc) { goto C671; } ?>

        <h2> <?php  echo Da; goto a676; a676: ?>
 Login </h2>

        <p>Password: <input type="password" name="password"></p>

    <?php  goto B899; C671: ?>

        <h2> Access Protection </h2>

        <p>Welcome to LiveImport! Lets record access password.</p>

        <p>Password: <input type="password" name="password"></p>
        <p>Once again: <input type="password" name="password2"></p>

    <?php  B899: goto a2e8; A58f: DE(''); f324: goto Fa9f; E6d7: if (!($_POST["\160\141\x73\x73\167\157\162\x64"] != $_POST["\x70\x61\163\163\167\x6f\x72\x64\62"])) { goto d2f2; } goto Cd75; a2e8: ?>

    <input type="submit" data-direction="reverse" data-inline="true" data-icon="check" data-theme="b" value="OK"/>


</form>
