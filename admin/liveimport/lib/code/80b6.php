<?php
/*
LiveImport (c) MaxD, 2016. Write to liveimport@devs.mx for support and purchase.
*/
 require B0 . "\x6c\x69\142\x2f\143\x6f\144\145\57\x63\145\62\61\x2e\x70\150\160"; ?>

<h2>About CRON Job</h2>
Without CRON job configured <b>LiveImport</b> will work when its opened only. For example, you have started site import and turned off your computer - the job will be suspended. Sheduled start will not work at all.
<h2>How to configure CRON Job?</h2>
<ol>
<li>
    In most cases CRON control is situated at your hoster's control panel. Look for "<font color="blue">CRON</font>" or "<font color="blue">sheduler</font>" there.
<br/><br/></li>
<li>
    Create new job, that is executed <b>every 5 minutes</b>. <br/><br/>If the start time is specified in strange way, try to set such a scheme: <font color="blue">*/5 * * * *</font>
<br/><br/></li>
<li>
    Specify this job command: <br/><font color="blue"><?php  echo PHP_BINDIR . "\57\x70\x68\160\x20\x2d\x66\x20" . getcwd() . "\57\143\x72\x6f\x6e\x2e\x70\150\x70"; ?>
</font>
<br/><br/></li>
<li>
        Wait for 5 minutes. If you see <span style="font-weight: bold; font-size: 12px; color: green">CRON Job: OK</span> at LiveImport start page, its good.
        If no - consult with your hoster.
<br/><br/></li>
</ol>
<a data-transition="reverse" rel="external" data-role="button" data-icon="arrow-l" data-inline="true" href="<?php  F6(); ?>
">Back</a>
