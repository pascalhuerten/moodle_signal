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
 * Form to verify a signal account.
 *
 * @package   message_signal
 * @copyright 2022, Pascal Hürten <pascal.huerten@th-luebeck.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_signal\output\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/formslib.php");

/**
 * Form to enter a signal token for account verification.
 *
 * @package   message_signal
 * @copyright 2022, Pascal Hürten <pascal.huerten@th-luebeck.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class verify_form extends \moodleform {
    /**
     * Form definition.
     * @return void
     */
    public function definition() {

        $mform = $this->_form;

        // Private key input.
        $mform->addElement('hidden', 'botaccount', '');
        $mform->setType('botaccount', PARAM_RAW);

        $mform->addElement('text',  'token',  get_string('verificationtoken', 'message_signal'));
        $mform->setType('token', PARAM_INT);

        $this->add_action_buttons(true, get_string('confirm'));
    }

    /**
     * Gets input data of submitted form.
     *
     * @return object
     **/
    public function get_data() {
        $data = parent::get_data();

        if (empty($data)) {
            return false;
        }

        return $data;
    }


    /**
     * Verify signal account.
     *
     * @return bool
     **/
    public function action() {
        $data = $this->get_data();
        $manager = new \message_signal\manager();

        return $manager->verify_account($data->botaccount, $data->token);
    }
}
