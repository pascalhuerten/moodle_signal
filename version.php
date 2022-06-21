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

defined('MOODLE_INTERNAL') || die();

$plugin->version  = 2022041319;  // The current module version (Date: YYYYMMDDXX).
$plugin->requires = 2020061500; // Moodle version.

$plugin->component = 'message_signal';

// $plugin->release  = '3.2.5 (Build - 2017071200)';
$plugin->maturity  = MATURITY_ALPHA;
