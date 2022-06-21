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
 * Signal helper manager class
 *
 * @package message_signal
 * @author  Pascal Hürten
 * @copyright  2022 onwards Pascal Hürten (pascal.huerten@th-luebeck.de)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_signal;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/filelib.php');

/**
 * Signal helper manager class
 *
 * @package message_signal
 * @copyright  2022 onwards Pascal Hürten (pascal.huerten@th-luebeck.de)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager
{

    /**
     * @var \curl $curl The curl object used in this run. Avoids continuous creation of a curl object.
     */
    private $curl = null;

    /**
     * @var string $signalapihost
     */
    private $signalapihost = null;

    /**
     * Constructor. Loads all needed data.
     */
    public function __construct()
    {
        $this->config = get_config('message_signal');
        $this->signalapihost = get_config('message_signal', 'signalapiurl');
    }

    /**
     * Set the config item to the specified value, in the object and the database.
     *
     * @param string $name The name of the config item.
     * @param string $value The value of the config item.
     */
    public function set_config($name, $value)
    {
        set_config($name, $value, 'message_signal');
        $this->config->{$name} = $value;
    }

    /**
     * Return the requested configuration item or null. Should have been loaded in the constructor.
     *
     * @param string $configitem The requested configuration item.
     * @return mixed The requested value or null.
     */
    public function config($configitem)
    {
        return isset($this->config->{$configitem}) ? $this->config->{$configitem} : null;
    }

    /**
     * Return the HTML for the user preferences form.
     *
     * @param array $preferences An array of user preferences.
     * @param int $userid Moodle id of the user in question.
     * @return string The HTML for the form.
     */
    public function config_form($preferences, $userid)
    {
        // If the account number is not set, display the form to set it.
        if (!static::is_user_account_set($userid, $preferences)) {
            $url = new \moodle_url(static::redirect_uri(), [
                'action' => 'connectuseraccount',
                'userid' => $userid,
                'sesskey' => sesskey()
            ]);
            return '<a href="' . $url . '">' . get_string('connectuseraccount', 'message_signal') . '</a>';
        }

        $url = new \moodle_url(static::redirect_uri(), [
            'action' => 'removeuseraccount',
            'userid' => $userid,
            'sesskey' => sesskey()
        ]);
        return '<a href="' . $url . '">' . get_string('removeuseraccount', 'message_signal') . '</a>';
    }

    /**
     * Verify that a user has their Signal account number set.
     *
     * @param int $userid The id of the user to check.
     * @param object|null $preferences Contains the Signal user preferences for the user, if present.
     * @return boolean True if the id is set.
     */
    public static function is_user_account_set($userid, $preferences = null)
    {
        if ($preferences === null) {
            $preferences = new \stdClass();
        }
        if (!isset($preferences->signal_chatid)) {
            $preferences->signal_chatid = get_user_preferences('message_processor_signal_chatid', '', $userid);
        }
        return !empty($preferences->signal_chatid);
    }

    /**
     * Return the redirect URI to handle the callback for OAuth.
     * @return string The URI.
     */
    public static function redirect_uri()
    {
        global $CFG;

        return $CFG->wwwroot . '/message/output/signal/signalconnect.php';
    }

    /**
     * Stores the signal account information of a user.
     *
     * @param string $account The phone botaccount the users Signal account is connected to.
     * @param int|null $userid The id of the user in question.
     * @return void.
     */
    public function set_user_account($account, $userid = null)
    {
        global $USER;

        if ($userid === null) {
            $userid = $USER->id;
        }

        if (empty($this->config('botaccount'))) {
            return get_string('notconfigured', 'message_signal');
        }

        set_user_preference('message_processor_signal_chatid', $account, $userid);
        return true;
    }

    /**
     * Removes the user's Signal account number from the preferences.
     *
     * @param int|null $userid The id to be cleared.
     * @return void.
     */
    public function remove_user_account($userid = null)
    {
        global $USER;

        if ($userid === null) {
            $userid = $USER->id;
        } else if ($userid != $USER->id) {
            require_capability('moodle/site:config', \context_system::instance());
        }

        unset_user_preference('message_processor_signal_chatid', $userid);
    }


    /**
     * Updates the Signal Bot when the configs change.
     *
     * @param string $key The name of the field that contains the changed config.
     *
     * @return mixed
     */
    public function on_config_change($key)
    {
        // Get submitted data.
        $data = data_submitted();

        if ($key === 's_message_signal_botaccount') {
            $botaccount = $data->{$key};
            $previous = $this->config('botaccount');
            $this->set_config('botaccount', null);

            // If nothing has changed, return.
            if ($botaccount == $previous) {
                $this->set_config('botaccount', '');
                return;
            }

            // If no botaccount is set, delete any previously set account.
            if (!isset($botaccount) || empty($botaccount) || $botaccount === '') {
                return $this->delete_account($botaccount);
            }

            if ($this->is_account_verified($botaccount)) {
                $this->set_config('botaccount', $botaccount);
                redirect(
                    new \moodle_url('/admin/settings.php?section=messagesettingsignal'),
                    get_string('accountcreated', 'message_signal'),
                    null,
                    \core\output\notification::NOTIFY_SUCCESS
                );
            }

            redirect(new \moodle_url(static::redirect_uri(), [
                'action' => 'captcha',
                'sesskey' => sesskey(),
                'botaccount' => $botaccount,
                'returnurl' => qualified_me()
            ]));
        }

        if ($key === 's_message_signal_botname') {
            $botaccount = $this->config('botaccount');
            $botname = $data->{$key};
            $this->set_config('botname', null);

            // Do not set name if botaccount is not yet set.
            if (!isset($botname) || empty($botname) || $botname === '') {
                return \core\notification::warning('Please set the botaccount number first.');
            }

            // If no botname is set, delete any previously set name.
            if (!isset($botname) || empty($botname) || $botname === '') {
                if (empty($this->config('botname'))) {
                    return;
                }

                return $this->update_account_info($botaccount, '');
            }

            $result = $this->update_account_info($botaccount, $botname);
            if ($result === true) {
                $this->set_config('botname', $botname);
                return \core\notification::success('Account name updated successfully.');
            }

            if (!$this->is_account_verified()) {
                $this->set_config('botaccount', null);
            }
            // Error.
            return $result;
        }

        if ($key === 's_message_signal_botabout') {
            $botaccount = $this->config('botaccount');
            $botabout = $data->{$key};
            $this->set_config('botname', null);

            // Do not set name if botaccount is not yet set.
            if (!isset($botaccount) || empty($botaccount) || $botaccount === '') {
                return \core\notification::warning('Please set the botaccount number first.');
            }

            // If no botabout is set, delete any previously set name.
            if (!isset($botabout) || empty($botabout) || $botabout === '') {
                if (empty($this->config('botabout'))) {
                    return;
                }

                return $this->update_account_info($botaccount, null, '');
            }

            $result = $this->update_account_info($botaccount, null, $botabout);
            if ($result === true) {
                $this->set_config('botabout', $botabout);
                return \core\notification::success('About info updated successfully.');
            }

            if (!$this->is_account_verified()) {
                $this->set_config('botaccount', null);
            }
            // Error.
            return $result;
        }

        if ($key === 's_message_signal_webhook') {
            $webhook = $data->{$key};

            // If no webhook is set, delete any previously set webhook.
            if (!isset($webhook) || empty($webhook)) {
                if (empty($this->config('webhook'))) {
                    return;
                }

                return $this->delete_webhook();
            }

            if (strpos($webhook, 'https:') !== 0) {
                // If submitted webhook is invalid reset config to previous configuration.
                $this->set_config('webhook', null);
                return \core\notification::error(get_string('requirehttps', 'message_signal'));
            }

            return $this->create_webhook($webhook);
        }
    }

    /**
     * Sets the webhook of the Signal Bot.
     *
     * @param string $webhook The url of the webhook.
     * @return mixed
     */
    public function create_webhook($webhook)
    {
        $this->get_curl()->setHeader('Content-Type: application/json');
        $json = json_encode(['webhook' => $webhook]);
        $response = json_decode(
            $this->get_curl()->post(
                $this->signalapihost  . '/accounts/' . $this->config('botaccount') . '/webhook',
                $json
            )
        );
        $httpcode = $this->get_curl()->get_info()['http_code'];
        if ($httpcode < 200 || $httpcode >= 300) {
            return \core\notification::error(get_string('errorcreatingwebhook', 'message_signal'));
        }
        \core\notification::success(get_string('webhookcreated', 'message_signal'));
        return true;
    }

    /**
     * Deletes the webhook of the Signal Bot.
     *
     * @param string $webhook The url of the webhook.
     * @return mixed
     */
    public function delete_webhook()
    {
        $response = json_decode(
            $this->get_curl()->delete(
                $this->signalapihost  . '/accounts/' . $this->config('botaccount') . '/webhook'
            )
        );
        $httpcode = $this->get_curl()->get_info()['http_code'];
        if ($httpcode < 200 || $httpcode >= 300) {
            return \core\notification::error(get_string('errordeletingwebhook', 'message_signal'));
        }
        \core\notification::success(get_string('webhookdeleted', 'message_signal'));
        return true;
    }

    /**
     * Checks wether a signal account is already verified.
     *
     * @param string|null $botaccount The signal account number.
     * @return mixed
     */
    public function is_account_verified($botaccount = null)
    {
        if (!$botaccount) {
            $botaccount = $this->config('botaccount');
        }

        $verified = false;

        $response = json_decode($this->get_curl()->get($this->signalapihost  . '/accounts/' . $botaccount));
        $httpcode = $this->get_curl()->get_info()['http_code'];

        if (!empty($response) && $httpcode >= 200 && $httpcode < 300) {
            $accounts = explode('\n', $response->body);
            foreach ($accounts as $account) {
                $status = explode(':', $account);
                if (trim($status[0]) === $botaccount) {
                    $verified = trim($status[1]) === 'true';
                    break;
                }
            }
        }

        $this->set_config('verified', $verified);
        return $verified;
    }

    /**
     * Verifies a signal account.
     *
     * @param string $botaccount The signal account number.
     * @param string $token Signal token used for verification.
     * @return mixed
     */
    public function verify_account($botaccount, $token)
    {
        $this->get_curl()->setHeader('Content-Type: application/json');
        $json = json_encode(['token' => $token]);
        $response = json_decode($this->get_curl()->patch("$this->signalapihost/accounts/$botaccount/verify", $json));
        $httpcode = $this->get_curl()->get_info()['http_code'];
        if ($httpcode < 200 || $httpcode >= 300) {
            return get_string('errorverifingaccount', 'message_signal');
        }
        $this->set_config('verified', true);
        return true;
    }

    /**
     * Verifies a signal account.
     *
     * @param string $botaccount The signal account number.
     * @param string $token Signal token used for verification.
     * @return mixed
     */
    public function update_account_info($botaccount, $botname = null, $botabout = null)
    {
        $this->get_curl()->setHeader('Content-Type: application/json');
        $json = json_encode(['name' => $botname, 'about' => $botabout]);
        $response = json_decode($this->get_curl()->patch("$this->signalapihost/accounts/$botaccount", $json));
        $httpcode = $this->get_curl()->get_info()['http_code'];
        if ($httpcode < 200 || $httpcode >= 300) {
            return get_string('errorupdatingaccount', 'message_signal');
        }
        return true;
    }

    /**
     * Creates a new Signal Account for the Signal Bot.
     *
     * @param string $botaccount The signal account number.
     * @param string $captcha A Captcha code to verify integrity of request.
     * @return bool|string
     */
    public function check_consent($account)
    {
        global $USER;
        $botaccount = $this->config('botaccount');
        $this->get_curl()->setHeader('Content-Type: application/json');
        $consentword = (new \lang_string('consentword', 'message_signal'))->out($USER->lang);
        $response = json_decode(
            $this->get_curl()->post(
                $this->signalapihost  . '/messages/consent/' . $botaccount,
                json_encode(
                    array(
                        "consentMessage" => (new \lang_string('consentmessage', 'message_signal', array('name' => $USER->firstname, 'consentword' => $consentword))
                        )->out($USER->lang),
                        "consentGiven" => (new \lang_string('consentgiven', 'message_signal'))->out($USER->lang),
                        "consentDenied" => (new \lang_string('consentdenied', 'message_signal'))->out($USER->lang),
                        "consentWord" => $consentword,
                        "caseSensitive" => false,
                        "recipient" => $account
                    )
                )
            )
        );
        $httpcode = $this->get_curl()->get_info()['http_code'];
        if ($httpcode < 200 || $httpcode >= 300) {
            // Return error message.
            return get_string('consentdenied', 'message_signal') . ($response ? ": '$response->body'" : "");
        }
        return true;
    }

    /**
     * Creates a new Signal Account for the Signal Bot.
     *
     * @param string $botaccount The signal account number.
     * @param string $captcha A Captcha code to verify integrity of request.
     * @return mixed
     */
    public function create_account($botaccount, $captcha)
    {
        $this->get_curl()->setHeader('Content-Type: application/json');
        $response = json_decode(
            $this->get_curl()->post(
                $this->signalapihost  . '/accounts/' . $botaccount,
                json_encode(array('captcha' => $captcha, 'use_voice' => false))
            )
        );
        $httpcode = $this->get_curl()->get_info()['http_code'];
        if ($httpcode < 200 || $httpcode >= 300) {
            // Return error message.
            return get_string('errorcreatingaccount', 'message_signal') . ($response ? ": '$response->body'" : "");
        }
        $this->set_config('botaccount', $botaccount);
        $this->set_config('verified', false);
        return true;
    }

    /**
     * Deletes the Signal Account of the Signal Bot.
     *
     * @return mixed
     */
    private function delete_account()
    {
        $this->delete_webhook();
        $response = json_decode($this->get_curl()->delete($this->signalapihost  . '/accounts/' . $this->config('botaccount')));
        $httpcode = $this->get_curl()->get_info()['http_code'];
        if ($httpcode < 200 || $httpcode >= 300) {
            return \core\notification::error(get_string('errordeletingaccount', 'message_signal'));
        }
        \core\notification::success(get_string('accountdeleted', 'message_signal'));
        return true;
    }



    /**
     * Send the message to Signal.
     *
     * @param string|array $message The message text.
     * @param int|null $userid The Moodle user id that is being sent to.
     * @param array|null $params Additional optional paramaters (chatid, text, parse_mode, reply_markup and more).
     * @return boolean True if message was successfully sent, else false.
     */
    public function send_message($message, $userid = null, $params = array())
    {
        $botaccount = $this->config('botaccount');

        if (!isset($botaccount) || empty($botaccount)) {
            throw new \moodle_exception(get_string('notconfigured', 'message_signal'));
        }

        if ($userid) {
            $params['recipient'] = get_user_preferences('message_processor_signal_chatid', '', $userid);
        }

        if (is_array($message)) {
            $params = array_merge($message, $params);
        } else {
            $params['message'] = $message;
        }

        if (!array_key_exists('message', $params)) {
            throw new \coding_exception('Could not send message because the message was not set.');
        }

        if (!array_key_exists('recipient', $params)) {
            throw new \coding_exception('Could not send message because the recipient was not set.');
        }

        // Send message.
        $this->get_curl()->setHeader('Content-Type: application/json');
        $response = json_decode($this->get_curl()->post($this->signalapihost  . '/messages/' . $botaccount, json_encode($params)));
        $httpcode = $this->get_curl()->get_info()['http_code'];
        return ($httpcode >= 200 && $httpcode < 300);
    }

    private function get_curl()
    {
        if ($this->curl === null) {
            $this->curl = new \curl();
        };
        return $this->curl;
    }
}
