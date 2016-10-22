<?php
include_once('../config.php'); // local site configuration
include_once('../lib/SLAP-lib.php');

session_start();

$SLAP_ = new SLAP_();

print $SLAP_->do_page();
