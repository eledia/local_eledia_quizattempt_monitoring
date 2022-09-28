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
 * Monitoring interface
 *
 * @package    local_eledia_quizattempt_monitoring
 * @author     Ralf Wiederhold <ralf.wiederhold@eledia.de>
 * @copyright  Ralf Wiederhold 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../config.php';

// Get params.
$cmid = required_param('id', PARAM_INT);
$filterform = optional_param('filterform', 0, PARAM_INT);
$overrideform = optional_param('overrideform', 0, PARAM_INT);

// Get quiz instance.
$cm = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
$instance = $DB->get_record('quiz', ['id' => $cm->instance], '*', MUST_EXIST);

// Get context.
$context = context_module::instance($cm->id);

// Check access.
require_login(null, false);
$hasviewcap = has_capability('local/eledia_quizattempt_monitoring:view', $context);
$hasoverridecap = has_capability('local/eledia_quizattempt_monitoring:override', $context);
if (!$hasviewcap && !$hasoverridecap) {
    throw new required_capability_exception($context, 'local/eledia_quizattempt_monitoring:view', 'nopermission', '');
}

// Define URLs.
$baseurl = new moodle_url('/local/eledia_quizattempt_monitoring/view.php');
$selfurl = new moodle_url($baseurl, ['id' => $cm->id]);


// Initialize page.
$PAGE->set_context($context);
$PAGE->set_cm($cm);
$PAGE->set_url($selfurl);
$PAGE->set_pagelayout('incourse');
$pagetitle = get_string('page_view_heading', 'local_eledia_quizattempt_monitoring', format_string($instance->name));
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

// Update filter settings if requested.
if ($filterform) {

    require_sesskey();
    $filtersettings = \cache::make('local_eledia_quizattempt_monitoring', 'filtersettings');

    if (optional_param('submitfilter', 0, PARAM_INT)) {

        $studentnamefilter = optional_param('studentnamefilter', '', PARAM_RAW);
        $selectedstates = optional_param_array('stateoption', [], PARAM_ALPHANUM);

        $filtersettings->set('studentnamefilter', $studentnamefilter);
        $filtersettings->set('selectedstates', $selectedstates);
        $redirectstr = get_string('page_view_status_filterupdated', 'local_eledia_quizattempt_monitoring');

    } else if (optional_param('resetfilter', 0, PARAM_INT)) {

        $filtersettings->set('studentnamefilter', '');
        $filtersettings->set('selectedstates', []);
        $redirectstr = get_string('page_view_status_filterreset', 'local_eledia_quizattempt_monitoring');
    }

    redirect($selfurl, $redirectstr);
    exit;
}

// Prepare output.
$renderer = $PAGE->get_renderer('local_eledia_quizattempt_monitoring');
$output = '';

// Page logic.
if ($overrideform && $hasoverridecap) {

    require_sesskey();

    // Get attempt IDs from our attempt list or use empty array if we came here
    // from our form.
    $attemptids = optional_param_array('attemptoverride', [], PARAM_INT);

    // Initialize form.
    $customdata = [
        'cm' => $cm,
        'quizinstance' => $instance,
        'attemptids' => $attemptids
    ];
    $form = new \local_eledia_quizattempt_monitoring\form\overrideform(null, $customdata);

    // Process form actions, if any.
    if ($form->is_cancelled()) {

        $redirectstr = get_string('page_view_status_overrideform_cancelled', 'local_eledia_quizattempt_monitoring');
        redirect($selfurl, $redirectstr);
        exit;

    } else if ($formdata = $form->get_data()) {

        require_once $CFG->dirroot . '/mod/quiz/lib.php';
        require_once $CFG->dirroot . '/mod/quiz/locallib.php';

        $instance->cmid = $cm->id;

        foreach ($formdata->attemptoverride as $attemptid) {

            $attemptrec = $DB->get_record('quiz_attempts', ['id' => $attemptid]);
            $override = $DB->get_record('quiz_overrides', ['quiz' => $instance->id, 'userid' => $attemptrec->userid]);

            // Define event params.
            $params = array(
                'context' => $context,
                'other' => array(
                    'quizid' => $instance->id
                ),
                'relateduserid' => $attemptrec->userid
            );

            // Update existing override.
            if (!empty($override)) {

                if (!empty($formdata->timeend)) {
                    $override->timeclose = $formdata->timeend;
                }
                if (!empty($formdata->timelimit)) {
                    $override->timelimit = $formdata->timelimit;
                }
                if (isset($formdata->attempts)) {
                    $override->attempts = $formdata->attempts;
                }

                // Update record.
                $DB->update_record('quiz_overrides', $override);

                // Trigger event.
                $params['objectid'] = $override->id;
                $event = \mod_quiz\event\user_override_updated::create($params);
                $event->trigger();

            } else {

                // Define override data.
                $override = new stdClass;
                $override->userid = $attemptrec->userid;
                $override->quiz = $instance->id;
                if (!empty($formdata->timeend)) {
                    $override->timeclose = $formdata->timeend;
                }
                if (!empty($formdata->timelimit)) {
                    $override->timelimit = $formdata->timelimit;
                }
                if (isset($formdata->attempts)) {
                    $override->attempts = $formdata->attempts;
                }

                // Insert record.
                $override->id = $DB->insert_record('quiz_overrides', $override);

                // Trigger event.
                $params['objectid'] = $override->id;
                $event = \mod_quiz\event\user_override_created::create($params);
                $event->trigger();
            }

            // Update events attached to quiz instance.
            quiz_update_events($instance, $override);
        }

        // Update attempts that may have been affected by these changes.
        quiz_update_open_attempts(['quizid' => $instance->id]);

        // Redirect user after successful operation...
        $redirectstr = get_string('page_view_status_overridescreated', 'local_eledia_quizattempt_monitoring');
        redirect($selfurl, $redirectstr);
        exit;
    }

    // Check if non-form actions have been performed.
    if (optional_param('addoverride', 0, PARAM_INT)) {

        // Update page heading / title to reflect action.
        $pagetitle = get_string('page_view_heading_overrideform', 'local_eledia_quizattempt_monitoring', format_string($instance->name));
        $PAGE->set_title($pagetitle);
        $PAGE->set_heading($pagetitle);

        // User wants to add overrides. Just display the form.
        $output = $form->render();

    } else if (optional_param('deleteoverride', 0, PARAM_INT)) {

        // User wants to delete overrides for the selected attempts/users.

        if (optional_param('confirmdeleteoverrides', 0, PARAM_INT)) {

            // User has confirmed deletion.

            require_once $CFG->dirroot . '/mod/quiz/lib.php';
            foreach ($attemptids as $attemptid) {

                $attemptrec = $DB->get_record('quiz_attempts', ['id' => $attemptid]);
                $override = $DB->get_record('quiz_overrides', ['quiz' => $instance->id, 'userid' => $attemptrec->userid]);
                if (!empty($override)) {
                    quiz_delete_override($instance, $override->id);
                }
            }

            $redirectstr = get_string('page_view_status_overridesdeleted', 'local_eledia_quizattempt_monitoring');
            redirect($selfurl, $redirectstr);

        } else {

            // User needs to confirm deletion.

            $confirmurl = new moodle_url($selfurl, ['overrideform' => 1, 'deleteoverride' => 1, 'confirmdeleteoverrides' => 1]);

            // Add attempt ids to url and build list of users for confirmation dialogue.
            $i = 0;
            $confirmuserstr = '<ul>';
            foreach ($attemptids as $attemptid) {

                // Get user for confirmation dialogue.
                $sql = 'SELECT u.* FROM {user} u, {quiz_attempts} qa WHERE qa.userid = u.id AND qa.id = :attemptid';
                $params = ['attemptid' => $attemptid];
                $userrec = $DB->get_record_sql($sql, $params);
                if (empty($userrec)) {
                    continue;
                }

                // Prepare user info for output.
                $confirmuserstr .= '<li>' . fullname($userrec) . ' (' . $userrec->username . ', ' . $userrec->idnumber . ')</li>';

                $confirmurl->param('attemptoverride[' . $i++ . ']', $attemptid);
            }
            $confirmuserstr .= '</ul>';

            $confirmstr = get_string('page_view_deleteoverrides_confirmstr', 'local_eledia_quizattempt_monitoring', $confirmuserstr);
            $output = $renderer->confirm($confirmstr, $confirmurl, $selfurl);
        }
    }

} else {

    // Render attempt list.
    $output = $renderer->render_attempt_list($cm, $instance, $baseurl);
    $PAGE->requires->js_call_amd('local_eledia_quizattempt_monitoring/attemptlist', 'init');
}

// Start output.
echo $renderer->header();
echo $output;
echo $renderer->footer();
