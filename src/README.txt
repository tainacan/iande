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

Iandé is a platform for managing appointments for educational group visits in museums, with extensive configurations via the admin dashboard.

== Installation ==

1. Upload `iande.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Select 'Iandé' on the admin menu.

There, you can configure, among other things:

* If visitants can ask for free admission (not needed for free museums);
* Target audience, available languages, and accessibility facilities;
* Templates for emails (plugin requires [WP Mail SMTP by WPForms](https://wordpress.org/plugins/wp-mail-smtp/)).

After activating the plugin, a default exhibition will be created (select 'Exibições' on the admin menu). There, you can configure, among other parameters:

* Available dates and hours;
* Group sizes, tour duration, etc.
* Special hours for holidays, etc. (via 'Exception' post-type).

Via admin dashboard, the following new post-types will be available: 'Exhibition', 'Institution', 'Appointment', 'Group', and 'Exception'.

== Frequently Asked Questions ==

= Where can I access the front-end for visitors? =

The login page is available at the following URL: `/iande/user/login/`. All other front-end URLS start with `/iande/`.

= How can I cancel or confirm an appointment? =

A box will appear on the sidebar of pending and published (confirmed) appointments.

An admin user can then confirm or cancel the appointment. Visitors have read cancellation reasons via e-mail and front-end, so it's recommended to fill the field.

= Can a museum have more than one active exhibition? =

Yes. Different exhibitions can have different hours, visit intervals, etc.

Once more than one exhibition is created, visitors can choose the show of interest.

= How can I configure special hours for the museum (e.g. holidays)? =

You can create an `Exception` post. An exception has an initial and, optionally, a final date (it the same, the exception applies to only one day).

If the exception has no hours, it means the museum/exhibition will be closed. Otherwise, the exception hours override the usual hours.

Afterward, you can assign an exception to an exhibition by editing the latter in the admin dashboard.

== Screenshots ==

1. All Iande's options available for users: Appointments, Institutions, Expositions, Groups, Exceptions & Iandé,
2. Iande's settings: Iandé, Institutions, Appointments,  E-mails
3. Institutions menu: Create a new Institution
4. Appointments menu: Create a new Appointment
5. Expositions menu: Create a new Appointment
6. Exceptions menu: Create a new Appointment
7. Groups menu: Create a new Appointment

== Changelog ==

= 0.3.3 =
* Visitors can cancel their appointment.