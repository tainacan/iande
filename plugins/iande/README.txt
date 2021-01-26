=== Iandé ===
Contributors: percebeeduca
Tags: museums, appointment, education
Requires at least: 5.1
Tested up to: 5.6
Requires PHP: 7.2
Stable tag: 0.3.3
License: AGPLv3 or later
License URI: http://www.gnu.org/licenses/agpl-3.0.txt
Version: 0.3.3

Iandé is a platform that allows museums to manage group visits.

== Description ==

Iandé is a platform for managing appointments for educational group visits in museums, with extensive configurability via admin dashboard.

== Installation ==

1. Upload `iande.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Select 'Iandé' on the admin menu.

There, you can configure:

* If visitants can ask for free admission (not needed for free museums);
* Target audience, available languages, and accessibility facilities;
* Templates for emails (plugin requires [WP Mail SMTP by WPForms](https://wordpress.org/plugins/wp-mail-smtp/)).

After activating the plugin, a default exhibition will also be created (select 'Exibições' on the admin menu). There, you can configure:

* Available dates and hours;
* Group sizes, tour duration, etc.
* Special hours for holidays, etc. (via 'Exception' post-type).

Via admin dashboard, the following new post-types will be available: 'Exhibition', 'Institution', 'Appointment',  'Group', 'Exception'.

== Frequently Asked Questions ==

= How can I cancel or confirm an appointment? =

A metabox will appear on the sidebar of pending and published (i.e. confirmed) appointments.

The admin can then confirm or cancel the appointment. The cancellation reason will be presented to the visitor, via e-mail notice and front-end.

= Where can I access the front-end for visitors? =

The front-end is available in the following URL: `/iande/user/login/`.

== Screenshots ==

1. All Iande's options available for users: Appointments, Institutions, Expositions, Groups, Exceptions & Iandé,
2. Iande's settings: Iandé, Institutions, Appointments,  E-mails
3. Institutions menu: Create a new Instituion
4. Appointments menu: Create a new Appointment
5. Expositions menu: Create a new Appointment
6. Exceptions menu: Create a new Appointment
7. Groups menu: Create a new Appointment

== Changelog ==

= 0.3.3 =
* Visitors can cancel their own appointment.