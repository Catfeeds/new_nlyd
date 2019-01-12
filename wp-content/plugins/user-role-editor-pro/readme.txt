=== User Role Editor Pro ===
Contributors: Vladimir Garagulya (https://www.role-editor.com)
Tags: user, role, editor, security, access, permission, capability
Requires at least: 4.4
Tested up to: 5.0
Stable tag: 4.49.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

User Role Editor WordPress plugin makes user roles and capabilities changing easy. Edit/add/delete WordPress user roles and capabilities.

== Description ==

User Role Editor WordPress plugin allows you to change user roles and capabilities easy.
Just turn on check boxes of capabilities you wish to add to the selected role and click "Update" button to save your changes. That's done. 
Add new roles and customize its capabilities according to your needs, from scratch of as a copy of other existing role. 
Unnecessary self-made role can be deleted if there are no users whom such role is assigned.
Role assigned every new created user by default may be changed too.
Capabilities could be assigned on per user basis. Multiple roles could be assigned to user simultaneously.
You can add new capabilities and remove unnecessary capabilities which could be left from uninstalled plugins.
Multi-site support is provided.

== Installation ==

Installation procedure:

1. Deactivate plugin if you have the previous version installed.
2. Extract "user-role-editor-pro.zip" archive content to the "/wp-content/plugins/user-role-editor-pro" directory.
3. Activate "User Role Editor Pro" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Settings"-"User Role Editor" and adjust plugin options according to your needs. For WordPress multisite URE options page is located under Network Admin Settings menu.
5. Go to the "Users"-"User Role Editor" menu item and change WordPress roles and capabilities according to your needs.

In case you have a free version of User Role Editor installed: 
Pro version includes its own copy of a free version (or the core of a User Role Editor). So you should deactivate free version and can remove it before installing of a Pro version. 
The only thing that you should remember is that both versions (free and Pro) use the same place to store their settings data. 
So if you delete free version via WordPress Plugins Delete link, plugin will delete automatically its settings data. Changes made to the roles will stay unchanged.
You will have to configure lost part of the settings at the User Role Editor Pro Settings page again after that.
Right decision in this case is to delete free version folder (user-role-editor) after deactivation via FTP, not via WordPress.

== Changelog ==
= [4.49.1] 12.11.2018 =
* Core version: 4.47
* Fix: Content view restrictions add-on: Fatal error was fixed: Argument 1 passed to URE_Content_View_Restrictions_Posts_List:can_edit() must be an instance of WP_Post, instance of stdClass given.
* Update: Unused code was removed from user-role-editor-pro/pro/includes/classes/bbpress.php
* Core version was updated to 4.47:
* Fix: "Users->User Role Editor": Capabilities view was not refreshed properly for new selected role in case "Granted Only" filter was turned ON before other role selection.
* Update: Unused code was removed from user-role-editor/includes/classes/bbpress.php
* Update: Prevent sudden revoke role 'administrator' from a user(s) during capability with the same ID ('administrator') deletion from roles.
* Update: Adding custom capability 'administrator' was prohibited.
* Update: Marked as compatible with WordPress version 5.0

= [4.49] 21.10.2018 =
* Core version: 4.46
* New: Content view restrictions add-on: It's possible to set default URL for redirection in case of view access error. If redirection URL for post/page is not set, URE uses default value. If default value is not set URE redirects user the automatically built to the site login URL.
* Update: Content view restrictions add-on: Internal cache usage was optimized to increase performance for sites with large quantity of posts and users.
* Update: Other roles access add-on: 'Blocking any role for "administrator" is not allowed.' message is shown after click "Other Roles" button for "administrator" role.
* Update: Meta boxes access add-on: Dialog markup uses fixed columns width in order to all columns will be visible without horizontal scrolling.
* Update: Admin menu access add-on: White listed arguments are supported now for the links from the "Tools" menu.
* Fix: Widgets admin access add-on: "Undefined index ... in widgets-admin-access.php line #78" warning was fixed. Warning was shown in case if blocked widget does not exist (due to related plugin deactivation or deletion).

== Upgrade Notice ==
= [4.49.1] 12.11.2018 =
* Core version: 4.47
* Fix: Content view restrictions add-on: Fatal error was fixed: Argument 1 passed to URE_Content_View_Restrictions_Posts_List:can_edit() must be an instance of WP_Post, instance of stdClass given.
* Update: Unused code was removed from user-role-editor-pro/pro/includes/classes/bbpress.php
* Core version was updated to 4.47:
* Fix: "Users->User Role Editor": Capabilities view was not refreshed properly for new selected role in case "Granted Only" filter was turned ON before other role selection.
* Update: Unused code was removed from user-role-editor/includes/classes/bbpress.php
* Update: Prevent sudden revoke role 'administrator' from a user(s) during capability with the same ID ('administrator') deletion from roles.
* Update: Adding custom capability 'administrator' was prohibited.
* Update: Marked as compatible with WordPress version 5.0
