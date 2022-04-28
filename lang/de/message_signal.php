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

$string['accountcreated'] = 'Signal Account erfolgreich erstellt';
$string['accountdeleted'] = 'Signal Account erfolgreich gelöscht';
$string['accountverified'] = 'Signal Account erfolgreich verifiziert';
$string['botaccount'] = 'Signal Account Number';
$string['botaccount_help'] = 'Ihre Mobiltelefonnummer im internationalen Format: "+ (Ländercode) (Nummer)" z.B +99 123 456789';
$string['botname'] = 'Bot-Name';
$string['captcha'] = 'Captcha Token';
$string['captchainvalid'] = 'Captcha Token ist nicht korrekt';
$string['configbotname'] = 'Der Name des Signal Bots.';
$string['configbotaccount'] = 'Signal Nachrichten werden von dieser Nummer versendet. Die angegebene Nummer muss eine valide und aktive Telefonnummer im internationalen Format sein, auf die Sie Zugriff haben.';
$string['connectinstructions'] = 'Anleitung';
$string['connectinstructions_desc'] = 'Bitte geben Sie in folgendem Feld eine valide Mobiltelefon-Nummer im internationalen Format ("+ (Ländercode) (Nummer)") an. Nach Angabe Ihrer Nummer werden wir Ihnen eine erste Nachricht von folgender Nummer zusenden: {$a} Bitte folgen Sie den Anweisungen in der Nachricht, um dieser Moodle-Seite zu erlauben Ihnen Nachrichten an Ihren Signal-Account zu schicken.';
$string['connectuseraccount'] = 'Verknüpfe meinen Account mit Signal';
$string['consentmessage'] = 'Hallo {$a->name}.
Bitte antworten Sie mit "{$a->consentword}" um zu erlauben, dass wir Ihnen Signal-Nachrichten schicken dürfen.
Wenn Sie irgendetwas anderes antworten oder nicht in den nächsten 60 Sekunden antworten, werten wir dies als Ablehnung.
Wenn Sie nicht {$a->name} sind und Sie keine Nachrichten von uns erhalten wollen, dann antworten Sie bitte nicht auf diese Nachricht.';
$string['consentgiven'] = 'Vielen Dank für Ihr Einverständniss.';
$string['consentdenied'] = 'Einverständniss verweigert.';
$string['consentword'] = 'Einverstanden';
$string['errorcreatingaccount'] = 'Beim Anlegen des Signal Accounts ist ein Fehler aufgetreten';
$string['errorcreatingwebhook'] = 'Beim Anlegen des Webhooks ist ein Fehler aufgetreten';
$string['errordeletingaccount'] = 'Beim Löschen des Signal Accounts ist ein Fehler aufgetreten';
$string['errordeletingwebhook'] = 'Beim Löschen des Webhooks ist ein Fehler aufgetreten';
$string['errorverifingaccount'] = 'Beim Verifizieren des Signal Accounts ist ein Fehler aufgetreten';
$string['entercaptcha'] = 'Captcha Eingabe';
$string['initialmessage'] = 'Hallo {$a},
du hast erfolgreich deinen Signal-Account mit deinem Moodle-Account verbunden!';
$string['missingcaptcha'] = 'Bitte generieren Sie einen validen Captcha Token';
$string['notconfigured'] = 'Signal-Nachrichten können nicht versendet werden, da der Signal-Server noch nicht konfiguriert wurde';
$string['numberinvalid'] = 'Geben Sie eine valide internationale Nummer an. Korrektes Format: "+ (Ländercode) (Nummer)"';
$string['pluginname'] = 'Signal';
$string['removeuseraccount'] = 'Entferne Signal-Verbindung';
$string['requirehttps'] = 'Die Seite muss HTTPS für Signals Webhook-Funktion unterstützen';
$string['setupinstructions'] = 'TODO';
$string['setwebhook'] = 'Webhook anlegen';
$string['signalchatid'] = 'Signal-Chat-ID';
$string['urlinvalid'] = 'Bitte geben Sie eine valide URL an';
$string['verificationtoken'] = 'Signal Token';
$string['verificationtokeninvalid'] = 'Der angegebende Token ist nicht valide';
$string['verifyaccount'] = 'Account-Verifizierung';
$string['webhook'] = 'Signal-Webhook';
$string['webhookcreated'] = 'Webhook wurde erfolgreich angelegt';
$string['webhookdeleted'] = 'Webhook wurde entfernt';
$string['webhookdesc'] = 'Dies ist der Endpunkt, der eigehende Updates vom Bot erhält und verarbeitet. Lassen Sie dieses Feld leer um, keinen Webhook anzulegen.';
$string['webhooksettings'] = 'Webhook Einstellungen';
