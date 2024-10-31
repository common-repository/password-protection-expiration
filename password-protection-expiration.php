<?php 

/*
 Plugin name: Password Protection Expiration
 Description: Change the amount of time after which a password entered for a password-protected post will expire and need to be re-entered.
 Version: 0.2
 Author: Seth A. Yoder
 License: GPL3
 License URI: https://www.gnu.org/licenses/gpl.html
*/

/*
    Password Protection Expiration plugin for WordPress
    Copyright (C) 2015 Seth A. Yoder

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('ABSPATH') or die( 'Not allowed to view this file.' );

add_option('ppe_expire_time','7200');

function register_pass_protection_exp_setting () {
    add_settings_section(
        'ppe-settings-section',
        '',
        'ppe_settings_section_callback',
        'pass-protection-exp'
    );
    add_settings_field(
        'ppe_expire_time', 
        'Password Protection Expiration Time',
        'ppe_expire_time_input_callback',
        'pass-protection-exp',
        'ppe-settings-section');
         
    register_setting('pass-protection-exp', 'ppe_expire_time'); 
}

function expire_plugin_menu() {
    add_options_page('Password-Protection Expiration', 
                     'Password-Protection Expiration', 
                     'manage_options', 
                     'pass-protection-exp', 
                     'expire_options');
}

function ppe_expire_time_input_callback() {
    echo '<input name="ppe_expire_time" id="ppe_expire_time" type=text value="' . (string) ((int) get_option('ppe_expire_time')) . '"/> (seconds after password entry)';
}

function ppe_settings_section_callback() {
    echo '';
}

function expire_options() {
?>
    <div class="wrap">
    <h2>Password-Protection Expiration</h2>
        <form method="post" action="options.php">
            <?php settings_fields('pass-protection-exp');
                  do_settings_sections('pass-protection-exp');
                  submit_button(); ?>
        </form>
    </div>
<?php
}

function expire_password() {
    if (isset ( $_COOKIE['wp-postpass_' . COOKIEHASH] ) ) {
        if (!isset ($_COOKIE['password_expire_plugin'] ) ) {
            $expire = time() + get_option('ppe_expire_time');
            $cookieval = $_COOKIE['wp-postpass_' . COOKIEHASH];
            setcookie( 'wp-postpass_' . COOKIEHASH , $cookieval, $expire, COOKIEPATH);
            setcookie( 'password_expire_plugin', 'enabled', $expire, COOKIEPATH);
        }
    }
}

add_action('wp','expire_password');

if(is_admin()) {
    add_action('admin_menu', 'expire_plugin_menu');
    add_action('admin_init', 'register_pass_protection_exp_setting');
}
