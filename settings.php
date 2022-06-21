<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Signal message plugin settings.
 *
 * @package message_signal
 * @author  Pascal Hürten
 * @copyright  2022 onwards Pascal Hürten (pascal.huerten@th-luebeck.de)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $signalmanager = new message_signal\manager();

    $signalapiurl = $signalmanager->config('signalapiurl');
    $botaccount = $signalmanager->config('botaccount');
    $botname = $signalmanager->config('botname');
    $botabout = $signalmanager->config('botabout');
    $webhook = $signalmanager->config('webhook');

    $signalmanager = new message_signal\manager();

    if (empty($signalapiurl)) {
        $signalapiurl = "localhost:8080";
    }

    if (empty($botaccount)) {
        $signalmanager->set_config('botname', null);
        $signalmanager->set_config('webhook', null);
        $signalmanager->set_config('botabout', null);
    } else {
        if (!$signalmanager->is_account_verified()) {
            $signalmanager->set_config('botaccount', null);
        }
    }

    if (empty($botname)) {
        $sitehostname = parse_url($CFG->wwwroot, PHP_URL_HOST);
        $lastdot = strrpos($sitehostname, '.');
        if ($lastdot !== false) {
            $sitehostname = substr($sitehostname, 0, $lastdot);
        }
        $botname = strrchr($sitehostname, '.');
        if ($botname === false) {
            $botname = $sitehostname;
        } else {
            $botname = str_replace('.', '', $botname);
        }
        // The username cannot be longer than 32 characters total, and must end in "bot".
        $botname = substr($botname, 0, 29) . 'Bot';
    }

    if (empty($botabout)) {
        $botabout = "I am a Bot. I live here $CFG->wwwroot";
    }

    $settings->add($botaccountsetting = new admin_setting_configtext(
        'message_signal/signalapiurl',
        get_string('signalapiurl', 'message_signal'),
        get_string('signalapiurldesc', 'message_signal'),
        "localhost:8080",
        PARAM_URL
    ));

    if (!empty($signalapiurl)) {
        $settings->add($botaccountsetting = new message_signal\admin_setting_config_international_phone_number(
            'message_signal/botaccount',
            get_string('botaccount', 'message_signal'),
            get_string('configbotaccount', 'message_signal'),
            $botaccount,
            PARAM_TEXT
        ));
        // Create a new signal account if message_signal/botaccount changes.
        $botaccountsetting->set_updatedcallback(array($signalmanager, 'on_config_change'));
    }

    if (!empty($botaccount)) {
        $settings->add($botnamesetting = new admin_setting_configtext(
            'message_signal/botname',
            get_string('botname', 'message_signal'),
            get_string('configbotname', 'message_signal'),
            $botname,
            PARAM_TEXT
        ));
        // Update account information if message_signal/botname changes.
        $botnamesetting->set_updatedcallback(array($signalmanager, 'on_config_change'));
    }

    if (!empty($botaccount)) {
        $settings->add($botnamesetting = new admin_setting_configtext(
            'message_signal/botabout',
            get_string('botabout', 'message_signal'),
            get_string('botaboutdesc', 'message_signal'),
            $botabout,
            PARAM_TEXT
        ));
        // Update account information if message_signal/botname changes.
        $botnamesetting->set_updatedcallback(array($signalmanager, 'on_config_change'));
    }

    if (!empty($botaccount)) {
        $settings->add($webhooksetting = new admin_setting_configtext(
            'message_signal/webhook',
            get_string('webhook', 'message_signal'),
            get_string('webhookdesc', 'message_signal'),
            $webhook,
            PARAM_URL
        ));
        // Set a new webhook if message_signal/webhook config changes.
        $webhooksetting->set_updatedcallback(array($signalmanager, 'on_config_change'));
    }
}
