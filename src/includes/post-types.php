<?php

namespace IandePlugin;

require 'post-types/helpers.php';
require 'post-types/appointment.php';
require 'post-types/group/group.php';
require 'post-types/group/checkin.php';
require 'post-types/group/report.php';
require 'post-types/group/feedback.php';
require 'post-types/institution.php';
require 'post-types/exhibition.php';
require 'post-types/exception.php';

if (is_plugin_active('tainacan/tainacan.php')) {
    require 'post-types/itinerary.php';
}
