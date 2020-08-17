<?php

if ( in_array('cmb2/init.php', get_option('active_plugins')) ) {
    require 'cmb2/helpers.php';
    require 'cmb2/settings.php';
    require 'cmb2/user.php';
}