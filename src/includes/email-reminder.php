<?php

namespace IandePlugin;

include IANDE_PLUGIN_BASEPATH . 'includes/Controller.php';
include IANDE_PLUGIN_BASEPATH . 'controllers/appointment.php';

class EmailReminder extends Appointment
{
    
    public function __construct()
    {
        \add_action('send_email_reminder', [$this, 'email_reminder'], 10, 1);
    }

    public function email_reminder($params)
    {
        $this->email('email_reminder', $params);
    }

}

$email = new EmailReminder();