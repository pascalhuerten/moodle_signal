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
 * Signal connection handler.
 *
 * @package message_signal
 * @author  Pascal Hürten
 * @copyright  2022 onwards Pascal Hürten (pascal.huerten@th-luebeck.de)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
// require_once($CFG->dirroot . '/lib/filelib.php');

$action = optional_param('action', 'setwebhook', PARAM_TEXT);
$returnurl = optional_param('returnurl', new \moodle_url('/'), PARAM_URL);


$context = context_system::instance();
$PAGE->set_context($context);

require_login();

$signalmanager = new message_signal\manager();

if ($action == 'connectuseraccount') {
    require_sesskey();
    $userid = optional_param('userid', null, PARAM_INT);

    if (!isset($userid)) {
        $userid = $USER->id;
    }

    $returnurl = new moodle_url('/message/notificationpreferences.php', ['userid' => $userid]);

    $PAGE->set_url(new moodle_url('/message/output/signal/signalconnect.php', [
        'action' => 'connectuseraccount',
        'sesskey' => sesskey(),
        'returnurl' => $returnurl
    ]));
    $PAGE->set_title(get_string('connectuseraccount', 'message_signal'));
    $PAGE->set_heading(get_string('connectuseraccount', 'message_signal'));

    $connectaccountform = new \message_signal\output\form\connect_account_form(qualified_me());
    // Set default data.
    if ($connectaccountform->get_data()) {
        $error = $connectaccountform->action();
        if ($error !== true) {
            redirect($returnurl, $error, null, \core\output\notification::NOTIFY_ERROR);
        }
        redirect($returnurl, get_string('accountcreated', 'message_signal'), null, \core\output\notification::NOTIFY_SUCCESS);
    }

    // Build page.
    echo $OUTPUT->header();
    echo $OUTPUT->heading($PAGE->heading);
    echo $connectaccountform->render();
    echo $OUTPUT->footer();
}
if ($action == 'removeuseraccount') {
    require_sesskey();
    $userid = optional_param('userid', null, PARAM_INT);

    if (!isset($userid)) {
        $userid = $USER->id;
    }

    $PAGE->set_url(new moodle_url('/message/output/signal/signalconnect.php', [
        'action' => 'removeuseraccount',
        'userid' => $userid,
        'returnurl' => $returnurl
    ]));

    $message = $signalmanager->remove_user_account($userid);

    redirect(new moodle_url('/message/notificationpreferences.php', ['userid' => $userid]), $message);
}
if ($action == 'captcha') {
    require_sesskey();
    $botaccount = required_param('botaccount', PARAM_TEXT);

    $PAGE->set_url(new moodle_url('/message/output/signal/signalconnect.php', [
        'action' => 'captcha',
        'sesskey' => sesskey(),
        'botaccount' => $botaccount,
        'returnurl' => $returnurl
    ]));
    $PAGE->set_title(get_string('entercaptcha', 'message_signal'));
    $PAGE->set_heading(get_string('entercaptcha', 'message_signal'));

    $captchaform = new \message_signal\output\form\captcha_form(qualified_me());
    if ($captchaform->is_cancelled()) {
        redirect(new moodle_url('/admin/settings.php?section=messagesettingsignal'));
    } else if (!$captchaform->get_data()) {
        // Set default data.
        $captchaformdata = (object) [
            'captcha' => '',
            'botaccount' => $botaccount
        ];

        // Set default data (if any).
        $captchaform->set_data($captchaformdata);
    } else {
        // Create account.
        $result = $captchaform->action();
        if ($result === true) { // Account creation successful.
                redirect(new \moodle_url($signalmanager->redirect_uri(), [
                    'action' => 'verifyaccount',
                    'sesskey' => sesskey(),
                    'botaccount' => $botaccount,
                    'returnurl' => $returnurl
                ]), '', 3);
        } else { // If account creation unsucessful, prompt error message.
            \core\notification::error($result);
        }
    }

    // Build page.
    echo $OUTPUT->header();
    echo $OUTPUT->heading($PAGE->heading);
    echo $captchaform->render();
    echo $OUTPUT->footer();
}
if ($action == 'verifyaccount') {
    require_sesskey();
    $botaccount = required_param('botaccount', PARAM_TEXT);

    // If account already verified return the user to the starting point.
    if ($signalmanager->is_account_verified($botaccount)) {
        redirect($returnurl, get_string('accountverified', 'message_signal'), null, \core\output\notification::NOTIFY_SUCCESS);
    }

    $PAGE->set_url(new moodle_url('/message/output/signal/signalconnect.php', [
        'action' => 'verifyaccount',
        'sesskey' => sesskey(),
        'botaccount' => $botaccount,
        'returnurl' => $returnurl
    ]));
    $PAGE->set_title(get_string('verifyaccount', 'message_signal'));
    $PAGE->set_heading(get_string('verifyaccount', 'message_signal'));

    $verifyform = new \message_signal\output\form\verify_form(qualified_me());

    if ($verifyform->is_cancelled()) {
        $signalmanager->set_config('botaccount', null);
        redirect(new moodle_url('/admin/settings.php?section=messagesettingsignal'));
    } else if (!$verifyform->get_data()) {
        // Set default data.
        $verifyformdata = (object) [
            'token' => '',
            'botaccount' => $botaccount
        ];

        // Set default data (if any).
        $verifyform->set_data($verifyformdata);
    } else {
        if ($verifyform->action() === true) {
            redirect($returnurl, get_string('accountverified', 'message_signal'), null, \core\output\notification::NOTIFY_SUCCESS);
        }
        core\notification::error(get_string('verificationtokeninvalid', 'message_signal'));
    }

    // Build page.
    echo $OUTPUT->header();
    echo $OUTPUT->heading($PAGE->heading);
    echo $verifyform->render();
    echo $OUTPUT->footer();
}
