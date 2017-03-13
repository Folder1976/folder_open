<?php
/*
LiveImport (c) MaxD, 2016. Write to liveimport@devs.mx for support and purchase.
*/
 goto ec46; a6f6: foreach ($B5a as $Af4) { ?>
                <option value="<?php  echo $Af4; ?>
" <?php  if (!($Af4 == $settings["\155\141\x74\x63\x68"])) { goto d4ac; } echo "\163\x65\x6c\145\x63\x74\145\x64\x3d\42\164\x72\x75\145\x22"; d4ac: ?>
>
                    <?php  echo $Af4; ?>
                </option>
            <?php  Ff7f: } e588: ?>
        </select>
    </span>
<br/><br/>
<style type="text/css">
    .mxtable {
        font-size: 10px;
        background: #ddd;
        padding: 5px;
        border: 1px solid grey;
    }

    .product {
        background: grey;
        white-space: nowrap;
    }

    .product:hover {
        background: #456f9a;
    }

    .mxtable tr {
        background: white;
    }

    .mxtable tr:hover {
        background: #eee;
    }

    .grey {
        background: #eee;
    }

    .mxtable td {
        border: 1px solid grey;
        vertical-align: top;
        padding: 4px;
    }

    .ui-select {
        margin-top: 0;
        margin-bottom: 0;
    }

</style>

<div style="overflow:auto; background: #eee">
<table class="mxtable">
    <tr style="font-weight: bold; color: white; text-shadow: none;"><td style="background: grey;"><?php  echo E3B("\106\x69\145\154\144"); ?>
</td>
        <?php  goto D178; df54: ?>
        <input type="submit" name="start" data-direction="reverse" data-inline="true" data-icon="play" data-theme="b" value="<?php  echo e3b("\123\164\141\162\x74"); ?>
"/>
        <input type="submit" name="create_donor" data-inline="true" data-icon="calendar" data-theme="d" value="<?php  echo E3b("\123\x61\x76\x65\x20\146\157\162\x20\120\145\x72\151\157\x64\x69\x63\40\111\x6d\160\x6f\162\164"); ?>
"/>
    <?php  goto b56e; c39d: ?>
) {
            def = group;
            if (sel.value != 'option') def = sel.options[sel.selectedIndex].text;
            name = prompt('Enter option name', def);
            if (name == 'null' || !name) {
                sel.selectedIndex = 0;
                return;
            }
            name = name.replace(':', '');
            sel.options[sel.selectedIndex].text = name;
            sel.options[sel.selectedIndex].value = '@#' + name;
        }
    }

    function SaveGo(url) {
        save_editors();
        if (data_changed) {
            $.post( '<?php  F6(); ?>
import2&ajax=1&id=<?php  echo $donor_id; goto F1b3; be5d: e0d7: $settings["\x63\x6f\x6c\163"] = $a23; C3($settings); if (!isset($_POST["\141\x6a\141\x78"])) { goto Ce1a; } die; goto A76a; c03a: c3($settings); de("\151\155\160\157\162\x74\46\x69\144\75" . $donor_id); B023: $settings["\162\x6f\x77\x73"] = $E7d; $settings["\145\162\162\x6f\x72"] = false; goto B93c; e675: ?>
</table>
</div>
    <br/>
    <?php  e25("\151\x6d\160\x6f\x72\164", "\x63\157\156\146\151\147\57" . $a6["\x68\157\163\164"] . "\57\164\141\163\x6b\137\144\x65\146\141\165\154\x74\56\160\x68\160", "\74\x73\164\x72\157\156\147\76\x50\x72\157\x64\165\x63\164\x20\120\x48\x50\x20\123\143\x72\151\x70\x74\x3a\74\57\x73\164\162\157\x6e\x67\x3e\40\151\x74\40\x77\151\x6c\154\40\x62\145\x20\x65\170\145\x63\x75\164\145\x64\x20\146\x6f\x72\40\x65\x61\143\x68\40\160\x72\157\x64\x75\x63\x74\40\142\145\146\157\x72\x65\x20\151\156\x73\x65\162\164\151\156\147", $Cba); ?>
<a data-role="button" data-direction="reverse" data-inline="true" data-icon="arrow-l" href="<?php  f6(); ?>
"><?php  goto Bb6a; Dfea: De("\x69\x6d\x70\x6f\x72\164\46\151\x64\x3d{$donor_id}"); Fd24: if (!isset($_POST["\x73\x61\x76\145"])) { goto D883; } dE("\164\x69\164\154\145"); D883: goto Cc80; c90b: @unlink("\x74\145\x6d\x70\x2f" . $donor_id . "\137\x69\x6d\x70\157\162\x74\145\x64"); $settings["\163\153\x69\160"] = $_POST["\163\153\151\x70"]; $settings["\x6d\x61\164\x63\150"] = $_POST["\x6d\x61\164\x63\150"]; $a23 = array(); foreach ($_POST as $af => $x) { if (!(substr($af, 0, 3) == "\143\157\154")) { goto B3af; } $a23[substr($af, 3)] = $x; B3af: ed12: } goto be5d; E58c: $Cba["\44\x72\x6f\167\137\x6e"] = "\x43\x75\162\x72\x65\x6e\164\x20\162\x6f\x77\x20\156\x75\155\142\x65\162\x2e"; foreach (reset($D3) as $e58 => $fcc) { $Cba["\44" . $e58] = "\x43\x6f\x6c\x75\x6d\156\40\74\x73\x74\162\x6f\156\x67\x3e{$e58}\x3c\57\x73\164\162\x6f\156\147\x3e\40\146\x72\x6f\x6d\x20\x74\150\145\40\143\165\162\x72\145\156\164\40\162\x6f\x77\40\x6f\146\40\x3c\163\164\162\x6f\x6e\147\x3e{$c0f}\74\57\163\164\x72\x6f\x6e\147\x3e"; B9dd: } d3e9: require B0 . "\x6c\151\x62\57\143\x6f\144\x65\57\143\145\62\61\56\x70\x68\160"; $D89 = "\151\x6d\160\157\x72\164"; goto Ce6f; Bb6a: echo e3B("\103\141\x6e\143\145\x6c"); ?>
</a>
<input type="submit" name="settings" data-direction="reverse" data-inline="true" data-icon="gear" value="<?php  echo E3b("\124\141\163\153\40\x53\145\164\164\151\156\x67\x73"); ?>
"/>
    <?php  if ($donor_id == 1) { goto be33; } goto Bf46; b56e: F88e: ?>

<script type="text/javascript">

    var data_changed = false;
        group = "";

    function RoleChange(sel) {
        data_changed = true;
        if (sel.selectedIndex == <?php  echo array_search("\141\164\x74\162\151\142\165\164\145", $b04) + 1; ?>
) {
            def = group;
            if (sel.value != 'attribute') def = sel.value;
            name = prompt('Enter attribute name (you can use specify group like "Group:Attr")', def);
            if (name == 'null' || !name) {
                sel.selectedIndex = 0;
                return;
            }
            if (name.indexOf(':')==-1) name = ':' + name;
            group = name.substr(0, name.indexOf(':'));
            if (group) group = group + ':';
            sel.options[sel.selectedIndex].text = name;
            sel.options[sel.selectedIndex].value = name;
        }
        if (sel.selectedIndex == <?php  echo array_search("\157\160\x74\x69\157\156", $b04) + 1; goto c39d; F8f5: if (!($a6["\144\x74\171\x70\145"] != "\146\x69\154\x65")) { goto f67c; } die("\124\x68\x69\x73\x20\x64\157\x6e\x6f\x72\40\x69\163\40\156\157\x74\x20\111\x6d\160\157\x72\x74\x2d\164\x79\160\x65"); f67c: $settings = D3(); $d40 = "\x74\145\155\x70\57"; goto c0ed; ec46: $donor_id = @$_GET["\x69\144"]; if ($donor_id) { goto bc8c; } $donor_id = 1; bc8c: Df($donor_id); goto F8f5; A76a: Ce1a: if (!isset($_POST["\x73\145\164\164\x69\x6e\x67\x73"])) { goto e53b; } dE("\x69\x6e\x73\46\x69\x64\x3d\x2d\x31\46\144\x6f\156\157\162\x5f\x69\144\75{$donor_id}\46\x62\x61\143\153\75\x69\x6d\160\x6f\162\164\62"); e53b: if (!isset($_POST["\143\150\141\x6e\147\145\x5f\146\x69\x6c\x65"])) { goto Fd24; } goto Dfea; a3c7: ?>
" />
    </span>
    <a class="ui-btn ui-icon-carat-r ui-btn-icon-notext ui-btn-inline ui-mini" style="margin-left: -3px"
       onclick="$('#skip').val($('#skip').val()-1+2); SkipChange()"></a>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php  echo e3B("\x4d\141\164\x63\x68\x20\x65\170\x69\163\x74\x69\156\x67\x20\x70\162\157\x64\x75\x63\164\x73\40\x62\171"); ?>
: &nbsp;
    <span style='width:90px; display:inline-block'>
        <select onchange="data_changed=true" name="match" data-inline='true' data-mini='true'">
            <option value="" ?>(<?php  echo e3b("\x6e\157\x6e\x65"); ?>
)</option>
            <?php  goto a6f6; f3d4: ?>
 <font color='grey'><?php  echo $c0f; ?>
</font>
        <span style="font-size:16px; color:green; font-weight: normal">(<?php  echo $settings["\x72\157\x77\163"]; ?>
 <?php  goto Faf7; c0ed: $b56 = !@$_GET["\x75\x70\x64\x61\x74\145"]; $A66 = http_if_file_changed($d40, $settings["\146\151\x6c\145\x6e\141\155\x65"], $b56); if (file_exists($d40)) { goto cae4; } $settings["\x65\x72\x72\157\x72"] = "\106\x69\x6c\145\40\x6e\157\164\x20\x66\x6f\x75\x6e\144\56"; C3($settings); goto f8a3; c5e2: ?>
import2&id=<?php  echo $donor_id; ?>
" method="post">

    <?php  if (!($donor_id > 1)) { goto C924; } ?>
        <input type="submit" name="change_file" data-theme="e" data-direction="reverse" data-inline="true" data-icon="calendar" value="<?php  goto df4c; B93c: file_put_contents($C2f, serialize($D3)); C3($settings); e021: if (!empty($settings["\163\x6b\x69\160"])) { goto f545; } $settings["\x73\153\151\x70"] = 0; goto f712; C902: goto e021; d995: $D3 = load_spreadsheet($d40, 0, 30); if ($D3) { goto B023; } $settings["\145\162\x72\157\162"] = "\x49\156\166\x61\154\151\x64\40\146\x69\154\145\x20\146\x6f\x72\155\141\164\x2e"; goto c03a; F9de: $name = if_inside('', "\x3d", $name); $name = str_replace("\x3d", '', $name); header("\x4c\x6f\x63\141\x74\151\x6f\x6e\72\40\150\x74\164\160\72\57\57\x6c\151\166\145\151\155\160\157\x72\164\x2e\144\145\x76\x73\56\155\x78\57\163\145\x72\x76\x69\143\145\x2f\x70\x65\162\x69\x6f\144\151\143\x2e\160\x68\x70\x3f\x6e\141\155\x65\75" . $name); db0a: ef37: goto F5a2; D178: foreach ($D3 as $af => $a9) { goto B5d7; B5d7: ?>
            <td id="product<?php  echo $af; ?>
" class="product" onmouseover="$('.row<?php  echo $af; ?>
').addClass('grey')"
                onmouseout="$('.row<?php  goto b878; Fe12: ?>
 <span class="product_num"><?php  echo $af + 1; ?>
</span> <span style="color:#ccc">Click here to test</span>
                </a>
                <span style="display:none; width:100px; color:#ccc"><?php  echo E3b("\123\x6b\151\160\160\145\144"); ?>
</span>
            </td>
        <?php  goto dedb; b878: echo $af; ?>
').removeClass('grey')">
                <a href="<?php  echo BE("\151\x6d\160\x5f" . $af); ?>
" onclick="SaveGo('')" target="_blank" style="text-decoration: none; color: white">
                        <?php  echo e3B("\x50\x72\x6f\144\165\143\164"); goto Fe12; dedb: A664: goto cd28; cd28: } e23b: ?>
    </tr>
<?php  foreach (reset($D3) as $e58 => $fcc) { goto B5d9; B5d9: ?>
    <tr>
        <td><select onchange="RoleChange(this)" data-mini="true" name="col<?php  echo $e58; ?>
">
                <option value="<?php  echo $e58; ?>
"><?php  goto c918; B46f: foreach ($D3 as $af => $a9) { ?>
            <td class="row<?php  echo $af; ?>
"><?php  echo shorten_text($a9[$e58], 500); ?>
</td>
        <?php  cb85: } aa84: ?>
    </tr>
<?php  a102: goto a3c9; c918: echo $e58; ?>
</option>
                <?php  foreach ($b04 as $A5c) { goto Db63; D4b1: Fbf7: d63f: ?>
                    <option value="<?php  echo $A5c; ?>
"
                            <?php  goto a90a; a90a: if (!(@$settings["\x63\157\x6c\163"][$e58] == $A5c)) { goto Ebf0; } echo "\x73\x65\x6c\x65\143\164\x65\x64\75\x22\x74\x72\x75\145\x22"; Ebf0: ?>
                        ><?php  echo str_replace("\100\x23", '', $A5c); goto D237; Db63: if (strpos(@$settings["\x63\157\x6c\x73"][$e58], "\x3a") !== false) { goto A014; } if (!(strpos(@$settings["\143\157\154\163"][$e58], "\100\43") !== false)) { goto c8d9; } if (!($A5c == "\x6f\x70\x74\x69\x6f\156")) { goto d3c6; } $A5c = $settings["\143\x6f\x6c\x73"][$e58]; d3c6: goto E801; E801: c8d9: goto d63f; A014: if (!($A5c == "\x61\164\x74\162\151\142\165\164\145")) { goto Fbf7; } $A5c = $settings["\x63\157\x6c\163"][$e58]; goto D4b1; D237: ?>
</option>
                <?php  D4f4: goto c096; c096: } Cd38: ?>
        </select></td>
        <?php  goto B46f; a3c9: } C149: goto e675; f712: f545: if (!empty($settings["\155\x61\x74\143\x68"])) { goto Db6b; } $settings["\155\141\x74\143\150"] = ''; Db6b: $D6 = query("\x53\x45\x4c\105\103\124\x20\151\x6e\x73\137\151\x64\x20\x46\x52\x4f\115\x20\x70\141\x72\x73\145\155\170\x5f\x69\156\x73\40\x57\x48\105\x52\105\x20\144\157\x6e\x6f\162\x5f\151\144\75{$donor_id}")->row; goto Cfa5; Cc80: if (!isset($_POST["\x73\x74\x61\x72\164"])) { goto d65f; } E7($donor_id); de("\x74\x69\x74\x6c\x65"); d65f: if (!isset($_POST["\x63\x72\145\141\164\145\137\144\x6f\156\157\x72"])) { goto db0a; } goto edf2; Faf7: echo e3B("\x72\157\167\163"); ?>
)</span>
    </h2>

<?php  echo E3b("\x46\x69\162\x73\x74\40\162\x6f\x77\163\x20\164\157\40\163\x6b\x69\160"); ?>
:&nbsp;
    <a class="ui-btn ui-icon-carat-l ui-btn-icon-notext ui-btn-inline ui-mini" style="margin-right: -3px"
        onclick="if ($('#skip').val()>0) $('#skip').val($('#skip').val()-1); SkipChange()"></a>
    <span style='width:50px; display:inline-block'>
        <input onchange="SkipChange()" name="skip" id="skip" data-inline='true' data-mini='true' value="<?php  echo $settings["\163\x6b\151\160"]; goto a3c7; edf2: $name = $c0f; $name = if_inside('', "\77", $name); $name = str_replace("\77", '', $name); $name = if_inside('', "\46", $name); $name = str_replace("\46", '', $name); goto F9de; F5a2: $b04 = explode("\x20", "\x63\141\x74\145\147\x6f\x72\171\40\x64\x65\x73\143\x72\151\x70\x74\x69\157\156\x20\144\x6f\x77\x6e\x6c\x6f\141\x64\x20\x65\141\x6e\40\x68\145\x69\x67\150\x74\40\151\155\x61\147\x65\x20\x69\163\142\156\x20\152\141\x6e\40\154\x65\156\147\164\x68\x20\154\157\143\x61\164\x69\x6f\156\40\x6d\141\156\165\146\x61\x63\x74\x75\x72\145\162\40\155\145\x74\x61\137\144\145\163\143\x72\151\160\x74\x69\157\x6e\40\155\x65\x74\141\137\153\145\x79\167\x6f\162\144\40\x6d\x6f\144\145\x6c\40\155\160\156\x20\156\141\x6d\x65\40\x70\162\151\x63\145\40\x6e\145\167\x5f\x70\x72\x69\x63\145\40\160\x72\x6f\x64\165\143\164\137\x69\x64\40\x71\165\x61\156\164\151\164\x79\x20\x73\x65\157\137\x75\162\x6c\40\163\x6b\165\x20\x74\x61\x67\163\x20\165\x70\x63\40\167\145\151\147\x68\x74\x20\x77\151\144\164\150\40\150\x31\40\164\x69\164\x6c\x65\40\x61\164\x74\x72\x69\x62\165\x74\145\40\157\160\x74\x69\157\156\40\x6f\x70\x74\151\x6f\x6e\x5f\151\x6d\141\147\x65\x20\x6f\x70\164\x69\x6f\156\137\160\x72\x69\143\x65\x20\x6f\x70\x74\x69\x6f\x6e\x5f\x71\x75\x61\x6e\164\151\164\171"); $B5a = explode("\40", "\145\x61\156\40\151\x73\x62\156\x20\152\x61\156\x20\x6d\157\x64\x65\x6c\40\x6d\160\156\x20\x6e\x61\x6d\x65\x20\160\x72\x6f\144\x75\x63\164\x5f\x69\144\x20\x73\145\157\x5f\165\x72\154\x20\x73\x6b\x75\40\x75\160\x63"); sort($b04); sort($B5a); $Cba = array(); goto E58c; Bf46: ?>
        <input type="submit" name="save" data-direction="reverse" data-inline="true" data-icon="check" data-theme="b" value="<?php  echo e3B("\x53\x61\166\145"); ?>
"/>
    <?php  goto F88e; be33: goto df54; Cfa5: $c0f = $settings["\146\151\x6c\145\x6e\141\155\145"]; if (!($Bf = strrpos($c0f, "\x2f"))) { goto C1f9; } $c0f = substr($c0f, $Bf + 1); C1f9: if (!($_SERVER["\122\x45\x51\125\105\x53\124\137\x4d\x45\x54\x48\117\x44"] == "\x50\117\123\124")) { goto ef37; } goto c90b; f8a3: de("\151\155\160\157\x72\164\46\x69\x64\75" . $donor_id); cae4: $C2f = "\x74\x65\155\160\x2f\151\x6d\x70\x6f\162\164\137{$donor_id}" . "\137\x70\162\145\166\151\145\167\x2e\x64\x61\x74"; if ($A66 or !file_exists($C2f) or filemtime($C2f) < filemtime($d40)) { goto d995; } $D3 = unserialize(file_get_contents($C2f)); goto C902; df4c: echo e3B("\103\150\x61\x6e\x67\x65\x20\x66\151\x6c\x65"); ?>
"/>
    <?php  C924: ?>

    <h2><?php  echo e3B("\x49\155\160\157\162\x74\x20\x53\143\150\145\x6d\x65\x20\146\157\x72"); goto f3d4; Ce6f: require B0 . "\154\x69\142\57\x63\157\x64\145\x2f\x37\67\60\x35\56\160\150\160"; ?>

<script type="text/javascript">
    function SkipChange() {
     data_changed=true;
     var n = $('#skip').val();
     for(var i=0;i<<?php  echo count($D3); ?>
; i++)
        if (i<n-1) {
            $('#product' + i).hide();
            $('.row' + i).hide();
        } else if (i == n-1) {
            $('#product'+i+' a').hide();
            $('#product'+i+' > span').show();
            $('#product'+i).show();
            $('.row'+i).show();
        } else {
            $('#product'+i+' .product_num').html(i-n+1);
            $('#product'+i+' a').show();
            $('#product'+i+' > span').hide();
            $('#product'+i).show();
            $('.row'+i).show();
        }
    }
</script>

<form id="form" rel="external" data-ajax="false" action="<?php  F6(); goto c5e2; F1b3: ?>
', $('#form').serialize().replace(/\%3A\%2F\%2F/g,".%2F%2F"), function(data2) {
                if (url) {
                    location = url;
                }
            });
        } else if (url) location = url;
    }

    SkipChange();
    $(document).on('submit','form',function(){
        save_editors();
    });
</script>
