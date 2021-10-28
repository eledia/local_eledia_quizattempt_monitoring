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
 * Renderer class implementation
 *
 * @package    local_eledia_quizattempt_monitoring
 * @author     Ralf Wiederhold <ralf.wiederhold@eledia.de>
 * @copyright  Ralf Wiederhold 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_eledia_quizattempt_monitoring\output;

defined('MOODLE_INTERNAL') or die();

require_once $CFG->dirroot . '/mod/quiz/attemptlib.php';
require_once $CFG->dirroot . '/mod/quiz/accessmanager.php';
require_once $CFG->dirroot . '/mod/quiz/locallib.php';

class renderer extends \plugin_renderer_base {

    public function render_attempt_list($cm, $instance, $baseurl) {
        global $DB;

        // Get filter settings from cache.
        $filtersettings = \cache::make('local_eledia_quizattempt_monitoring', 'filtersettings');
        $studentnamefilter = $filtersettings->get('studentnamefilter');
        $selectedstates = $filtersettings->get('selectedstates');
        if (false === $selectedstates) {
            $selectedstates = [];
        }

        // Prepare filter form data for rendering.
        $filtersettingstemplate = (object) [
            'formurl' => $baseurl,
            'studentnamefilter' => $studentnamefilter,
            'stateoptions' => [
                (object) [
                    'name' => \quiz_attempt_state_name(\quiz_attempt::IN_PROGRESS),
                    'identifier' => \quiz_attempt::IN_PROGRESS,
                    'active' => in_array(\quiz_attempt::IN_PROGRESS, $selectedstates) ? 1 : 0
                ],
                (object) [
                    'name' => \quiz_attempt_state_name(\quiz_attempt::OVERDUE),
                    'identifier' => \quiz_attempt::OVERDUE,
                    'active' => in_array(\quiz_attempt::OVERDUE, $selectedstates) ? 1 : 0
                ],
                (object) [
                    'name' => \quiz_attempt_state_name(\quiz_attempt::ABANDONED),
                    'identifier' => \quiz_attempt::ABANDONED,
                    'active' => in_array(\quiz_attempt::ABANDONED, $selectedstates) ? 1 : 0
                ],
                (object) [
                    'name' => \quiz_attempt_state_name(\quiz_attempt::FINISHED),
                    'identifier' => \quiz_attempt::FINISHED,
                    'active' => in_array(\quiz_attempt::FINISHED, $selectedstates) ? 1 : 0
                ],
            ]
        ];

        // Prepare template data.
        $templatedata = (object) [
            'cmid' => $cm->id,
            'sesskey' => sesskey(),
            'filterform' => $filtersettingstemplate,
            'attempts' => []
        ];

        // Build base DB query for retrieving attempts.
        $sql = '
            SELECT qa.*
            FROM {quiz_attempts} qa
            WHERE
                qa.quiz = :instanceid
            AND
                qa.preview = 0         
        ';
        $params = ['instanceid' => $instance->id];

        // Add state filter, if any states have been selected.
        if (!empty($selectedstates)) {
            list($in_qry, $in_params) = $DB->get_in_or_equal($selectedstates, SQL_PARAMS_NAMED);
            $sql .= ' AND state ' . $in_qry;
            $params = array_merge($params, $in_params);
        }

        // Add student filter if one has been provided.
        if (!empty($studentnamefilter)) {
            $sql .= ' AND userid IN (
                SELECT id 
                FROM {user}
                WHERE
                    '.$DB->sql_fullname().' LIKE :userfilter1
                OR
                    username LIKE :userfilter2
                OR
                    email LIKE :userfilter3
                OR
                    idnumber LIKE :userfilter4
            )';
            $params['userfilter1'] = '%'.$studentnamefilter.'%';
            $params['userfilter2'] = '%'.$studentnamefilter.'%';
            $params['userfilter3'] = '%'.$studentnamefilter.'%';
            $params['userfilter4'] = '%'.$studentnamefilter.'%';
        }

        // Get query results and prepare data for template.
        foreach ($DB->get_records_sql($sql, $params) as $attemptid => $attemptrec) {

            // Prepare user data.
            $userrec = $DB->get_record('user', ['id' => $attemptrec->userid]);
            $userstr = fullname($userrec) . ' (' . $userrec->username . ', ' . $userrec->idnumber . ')';
            $userurl = new \moodle_url('/user/view.php', ['id' => $userrec->id, 'course' => $instance->course]);

            // Prepare attempt state.
            $attemptstate = \quiz_attempt_state_name($attemptrec->state);

            // Prepare attempt progress.
            $attemptobj = \quiz_attempt::create($attemptid);
            $questionslots = $attemptobj->get_slots();
            $questionscompleted = 0;
            $questionsincomplete = 0;
            $questionstodo = 0;
            foreach ($questionslots as $slot) {
                $questionattempt = $attemptobj->get_question_attempt($slot);
                $numsteps = $questionattempt->get_num_steps();
                if ($numsteps < 2) {
                    $questionstodo++;
                } else {
                    $step = $questionattempt->get_step(1);
                    if ($step->get_state() instanceof \question_state_complete) {
                        $questionscompleted++;
                    } else {
                        $questionsincomplete++;
                    }
                }
            }

            $strparams = [
                'questioncount' => count($questionslots),
                'answered' => $questionsincomplete + $questionscompleted,
                'complete' => $questionscompleted,
                'invalid' => $questionsincomplete,
                'todo' => $questionstodo
            ];
            $progressstr = get_string('renderer_render_attempt_list_attemptprogressdescription', 'local_eledia_quizattempt_monitoring', $strparams);

            // Prepare time due display.
            $currenttime = time();
            $accessmngr = $attemptobj->get_access_manager($currenttime);
            $timeduets = $accessmngr->get_end_time($attemptrec);
            $timeleft = $accessmngr->get_time_left_display($attemptrec, $currenttime);

            $timeduestr = get_string('renderer_render_attempt_list_attempttimedue_nolimit', 'local_eledia_quizattempt_monitoring');
            if (false !== $timeduets) {
                $timeendstr = date('d.m.Y - H:i:s', $timeduets);
                $timeduestr = get_string('renderer_render_attempt_list_attempttimedue', 'local_eledia_quizattempt_monitoring', $timeendstr);
            }


            // Get overrides for user/attempt and prepare it for display.
            $createoverrideurl = new \moodle_url(
                '/local/eledia_quizattempt_monitoring/view.php',
                [
                    'id' => $cm->id,
                    'addoverride' => 1,
                    'overrideform' => 1,
                    'sesskey' => sesskey(),
                    'attemptoverride[0]' => $attemptid
                ]
            );
            $deleteoverrideurl = '';
            $overridestrs = [];
            if ($override = $DB->get_record('quiz_overrides', ['userid' => $userrec->id, 'quiz' => $instance->id])) {
                $deleteoverrideurl = new \moodle_url(
                    '/local/eledia_quizattempt_monitoring/view.php',
                    [
                        'id' => $cm->id,
                        'deleteoverride' => 1,
                        'overrideform' => 1,
                        'sesskey' => sesskey(),
                        'attemptoverride[0]' => $attemptid
                    ]
                );

                if (!empty($override->timeopen)) {
                    $overridestrs[] = get_string(
                        'renderer_render_attemptslist_override_timeopen',
                        'local_eledia_quizattempt_monitoring',
                        date('d.m.Y - H:i:s', $override->timeopen)
                    );
                }

                if (!empty($override->timeclose)) {
                    $overridestrs[] = get_string(
                        'renderer_render_attemptslist_override_timeclose',
                        'local_eledia_quizattempt_monitoring',
                        date('d.m.Y - H:i:s', $override->timeclose)
                    );
                }

                if (!empty($override->timelimit)) {
                    $overridestrs[] = get_string(
                        'renderer_render_attemptslist_override_timelimit',
                        'local_eledia_quizattempt_monitoring',
                    format_time($override->timelimit)
                    );
                }

                if (!empty($override->attempts)) {
                    $overridestrs[] = get_string(
                        'renderer_render_attemptslist_override_attempts',
                        'local_eledia_quizattempt_monitoring',
                        $override->attempts
                    );
                }

                if (!empty($override->password)) {
                    $overridestrs[] = get_string(
                        'renderer_render_attemptslist_override_password',
                        'local_eledia_quizattempt_monitoring',
                        $override->password
                    );
                }
            }


            // Add attempt data to template data.
            $templatedata->attempts[] = (object) [
                'studentname' => $userstr,
                'studenturl' => $userurl,
                'state' => $attemptstate,
                'progress' => $progressstr,
                'timedue' => $timeduestr,
                'attemptid' => $attemptid,
                'attemptdetailurl' => new \moodle_url('/mod/quiz/review.php', ['attempt' => $attemptid]),
                'hasoverrides' => count($overridestrs) > 0,
                'overrides' => $overridestrs,
                'createoverrideurl' => $createoverrideurl,
                'deleteoverrideurl' => $deleteoverrideurl
            ];
        }
        $templatedata->hasattempts = count($templatedata->attempts) > 0;

        return $this->render_from_template('local_eledia_quizattempt_monitoring/page_view', $templatedata);
    }
}
