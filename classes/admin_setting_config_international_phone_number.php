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
 * Signal message plugin version information.
 *
 * @package message_signal
 * @author  Pascal Hürten
 * @copyright  2022 onwards Pascal Hürten (pascal.huerten@th-luebeck.de)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_signal;

/**
 * Config option for admin settings that only allows international phone numbers as input.
 *
 * @package message_signal
 * @copyright  2022 onwards Pascal Hürten (pascal.huerten@th-luebeck.de)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_config_international_phone_number extends \admin_setting_configtext {
    /**
     * Validate data before storage
     * @param string data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {
        // Allow paramtype to be a custom regex if it is the form of /pattern/ .

        $validconfigtext = parent::validate($data);
        if ($validconfigtext !== true) {
            return $validconfigtext;
        }

        if (empty($data) || trim($data) === '') {
            return true;
        }

        $matches = preg_match('/^\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|
        2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|
        4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/x', $data);
        if (!$matches) {
            return get_string('numberinvalid', 'message_signal');
        }

        return true;
    }
}
