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
 * Form class for defining quiz access overrides for a group of users
 *
 * @package    local_eledia_quizattempt_monitoring
 * @author     Ralf Wiederhold <ralf.wiederhold@eledia.de>
 * @copyright  Ralf Wiederhold 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_eledia_quizattempt_monitoring\form;

defined('MOODLE_INTERNAL') or die();

require_once $CFG->libdir . '/formslib.php';
require_once $CFG->dirroot . '/mod/quiz/mod_form.php';

class overrideform extends \moodleform {

    protected function definition() {
        global $DB;

        $mform = $this->_form;
        $quiz = $this->_customdata['quizinstance'];

        // Add hidden fields to satisfy/trigger page logic.
        $mform->addElement('hidden', 'id', $this->_customdata['cm']->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'overrideform', 1);
        $mform->setType('overrideform', PARAM_INT);

        // Add the selected attempt ids as hidden fields to the form
        // and prepare list of users the attempts belong to for display
        // in form.
        $userinfotext = '<ul>';
        $i = 0;
        foreach ($this->_customdata['attemptids'] as $attemptid) {

            // Get userrec from attempt.
            $sql = 'SELECT u.* FROM {user} u, {quiz_attempts} qa WHERE qa.userid = u.id AND qa.id = :attemptid';
            $params = ['attemptid' => $attemptid];
            $userrec = $DB->get_record_sql($sql, $params);
            if (empty($userrec)) {
                continue;
            }

            // Prepare user info for output.
            $userinfotext .= '<li>' . fullname($userrec) . ' (' . $userrec->username . ', ' . $userrec->idnumber . ')</li>';

            // Add hidden field with attemptid.
            $fieldname = 'attemptoverride['.$i++.']';

            $mform->addElement('hidden', $fieldname, $attemptid);
            $mform->setType($fieldname, PARAM_INT);
        }
        $userinfotext .= '</ul>';

        // Add static info about users the override will be created for.
        $mform->addElement(
            'static',
            'userlist',
            get_string('form_overrideform_label_userlist', 'local_eledia_quizattempt_monitoring'),
            $userinfotext
        );

        // Add date/time selector.
        $mform->addElement(
            'date_time_selector',
            'timeend',
            get_string('quizclose', 'quiz'),
            \mod_quiz_mod_form::$datefieldoptions
        );
        $mform->setDefault('timeend', $quiz->timeopen);


        // Add duration input field.
        $mform->addElement(
            'duration',
            'timelimit',
            get_string('timelimit', 'quiz'),
            ['optional' => true]
        );
        $mform->addHelpButton('timelimit', 'timelimit', 'quiz');
        $mform->setDefault('timelimit', $quiz->timelimit);

        $this->add_action_buttons();
    }
}
