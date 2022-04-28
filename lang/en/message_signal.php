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
 * Strings for signal message plugin.
 *
 * @package message_signal
 * @author  Pascal Hürten
 * @copyright  2022 onwards Pascal Hürten (pascal.huerten@th-luebeck.de)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accountcreated'] = 'Signal account sucessfully created';
$string['accountdeleted'] = 'Signal account sucessfully deleted';
$string['accountverified'] = 'Signal account sucessfully verified';
$string['botaccount'] = 'Signal account number';
$string['botaccount_help'] = 'Your mobile number in an internationale format: "+ countrycode number" e.g. +99 123 456789';
$string['botname'] = 'Bot name';
$string['captcha'] = 'Captcha token';
$string['captchainvalid'] = 'Captcha token is invalid';
$string['configbotname'] = 'The name of the signal bot.';
$string['configbotaccount'] = 'Signal messages will be sent from this number. It has to be a valid and active phone number in international format, that you have access to.';
$string['connectinstructions'] = 'Instructions';
$string['connectinstructions_desc'] = 'Please enter a valid mobile number in an international fromat. After you have entered your number, we will send you an inital message from following number: {$a} Please follow the instructions in the message to allow this Moodle-Site to send messages to your Signal account.';
$string['connectuseraccount'] = 'Connect my account to Signal';
$string['consentmessage'] = 'Hello {$a->name}.
Please respond with "{$a->consentword}" to allow this number to send you messages in the future.
Respond with anything else or do not respond within the next 60 seconds to deny your consent.
If you are not {$a->name} and do not want to receive any messages from this number, please do not respond to this message.';
$string['consentgiven'] = 'Thank you for your consent.';
$string['consentdenied'] = 'Consent denied.';
$string['consentword'] = 'I agree';
$string['errorcreatingaccount'] = 'There was an error while creating a signal account';
$string['errorcreatingwebhook'] = 'There was an error while setting the webhook';
$string['errordeletingaccount'] = 'There was an error while deleting the signal account';
$string['errordeletingwebhook'] = 'There was an error while deleting the webhook';
$string['entercaptcha'] = 'Captcha input';
$string['initialmessage'] = 'Hello {$a},
you connected your Signal account successfully to your Moodle account!';
$string['missingcaptcha'] = 'Please genereate and enter a valid Captcha token';
$string['errorverifingaccount'] = 'There was an error while verifing the signal account';
$string['notconfigured'] = 'The Signal Bot hasn\'t been configured yet so Signal messages cannot be sent';
$string['numberinvalid'] = 'Please input a valid international phone number. Correct format: "+ (country code) (number)"';
$string['pluginname'] = 'Signal';
$string['removeuseraccount'] = 'Remove Signal connection';
$string['requirehttps'] = 'Site must use HTTPS for Signal\'s webhook function';
$string['setupinstructions'] = 'TODO';
$string['setwebhook'] = 'Set webhook';
$string['signalchatid'] = 'Signal chat id';
$string['urlinvalid'] = 'Please input a valid url';
$string['verificationtoken'] = 'Signal token';
$string['verificationtokeninvalid'] = 'The given token is not valid';
$string['verifyaccount'] = 'Account verification';
$string['webhook'] = 'Signal webhook';
$string['webhookcreated'] = 'Webhook successfully set';
$string['webhookdeleted'] = 'Webhook was unset';
$string['webhookdesc'] = 'This is the endpoint that receives and handles updates from the set Signal Bot. Leave this field blank to not setup a webhook.';
$string['webhooksettings'] = 'Webhook Settings';
