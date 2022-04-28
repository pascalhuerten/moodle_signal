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
 * Form to let users connect a signal account.
 *
 * @package   message_signal
 * @copyright 2022, Pascal Hürten <pascal.huerten@th-luebeck.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_signal\output\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/formslib.php");

/**
 * Form to enter a signal account number to connect it to the moodle account.
 *
 * @package   message_signal
 * @copyright 2022, Pascal Hürten <pascal.huerten@th-luebeck.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class connect_account_form extends \moodleform {
    /**
     * Form definition.
     * @return void
     */
    public function definition() {

        $mform = $this->_form;

        $mform->addElement('static', 'description', get_string('connectinstructions', 'message_signal'),
            get_string('connectinstructions_desc', 'message_signal', get_config('message_signal')->botaccount));

        $mform->addElement('text',  'account',  get_string('botaccount', 'message_signal'));
        $mform->setType('account', PARAM_TEXT);
        $mform->addHelpButton('account', 'botaccount', 'message_signal');

        $this->add_action_buttons(true);
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
     * Connect the signal account with the moodel account.
     *
     * @return bool
     **/
    public function action() {
        $data = $this->get_data();
        $manager = new \message_signal\manager();

        $consent = $manager->check_consent($data->account);
        if ($consent !== true) {
            return $consent;
        }

        return $manager->set_user_account($data->account);
    }

    /**
     * Form validation
     *
     * @param array $data data from the form.
     * @param array $files files uploaded.
     *
     * @return array of errors.
     */
    public function validation($data, $files) {

        if (empty($data['account'])) {
            return get_string('numberinvalid', 'message_signal');
        } else {
            $matches = preg_match('/^\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|
            2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|
            4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/x', $data['account']);
            if (!$matches) {
                return get_string('numberinvalid', 'message_signal');
            }
        }

        return parent::validation($data, $files);;
    }
}
