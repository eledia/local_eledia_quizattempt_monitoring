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
 * English language strings
 *
 * @package    local_eledia_quizattempt_monitoring
 * @author     Ralf Wiederhold <ralf.wiederhold@eledia.de>
 * @copyright  Ralf Wiederhold 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

$string['cachedef_filtersettings'] = 'Session cache for storing a users filter settings';
$string['eledia_quizattempt_monitoring:view'] = 'View monitoring interface';
$string['eledia_quizattempt_monitoring:override'] = 'Add/edit overrides for quiz attempts';
$string['form_overrideform_label_userlist'] = 'Override for users';
$string['nav_view'] = 'Attempt monitoring';
$string['page_view_deleteoverrides_confirmstr'] = 'Delete quiz overrides for the following users: <br> {$a}';
$string['page_view_heading'] = '{$a}: Monitor Attempts';
$string['page_view_heading_overrideform'] = '{$a}: Create / Update Overrides';
$string['page_view_status_filterupdated'] = 'Filters have been applied';
$string['page_view_status_filterreset'] = 'Filters have been removed';
$string['page_view_status_overrideform_cancelled'] = 'Cancelled creating / updating overrides.';
$string['page_view_status_overridescreated'] = 'Overrides for the selected users have been created / updated.';
$string['page_view_status_overridesdeleted'] = 'Overrides for the selected users have been deleted.';
$string['pluginname'] = 'Quizattempt Monitoring';
$string['plugindesc'] = 'Adds a page to the quiz settings menu that allows authorized users to view the progress of students within their individual quiz attempts and add quiz setting overrides to attempts.';
$string['renderer_render_attempt_list_attemptprogressdescription'] = 'Answered {$a->answered} of {$a->questioncount} questions<ul><li>Full answers: {$a->complete}</li><li>Invalid answers: {$a->invalid}</li><li>Missing answers: {$a->todo}</li></ul>';
$string['renderer_render_attempt_list_attempttimedue'] = 'Attempt due: {$a}';
$string['renderer_render_attempt_list_attempttimedue_nolimit'] = 'No time limit';
$string['renderer_render_attemptslist_override_timeopen'] = 'Time open: {$a}';
$string['renderer_render_attemptslist_override_timeclose'] = 'Time close: {$a}';
$string['renderer_render_attemptslist_override_timelimit'] = 'Time limit: {$a}';
$string['renderer_render_attemptslist_override_attempts'] = 'Attempts: {$a}';
$string['renderer_render_attemptslist_override_password'] = 'Password: {$a}';
$string['template_page_view_filterform_label_stateoption'] = 'State filter';
$string['template_page_view_filterform_label_studentnamefilter'] = 'Student filter';
$string['template_page_view_filterform_placeholder_studentnamefilter'] = 'Enter (parts of) a students name, username, email or idnumber...';
$string['template_page_view_filterform_label_submitfilter'] = 'Filter';
$string['template_page_view_filterform_label_resetfilter'] = 'Reset';
$string['template_page_view_header_studentname'] = 'Student';
$string['template_page_view_header_state'] = 'State';
$string['template_page_view_header_progress'] = 'Progress';
$string['template_page_view_header_overrides'] = 'Defined Overrides';
$string['template_page_view_header_timedue'] = 'Time due';
$string['template_page_view_label_addoverride'] = 'Create/Update Override(s)';
$string['template_page_view_label_deleteoverride'] = 'Delete Override(s)';
$string['template_page_view_noattempts'] = 'No attempts found';
$string['template_page_view_override_nooverrides'] = 'No overrides for user';
$string['template_page_view_override_createlink'] = 'Create/Update overrides';
$string['template_page_view_override_deletelink'] = 'Delete overrides';
$string['template_page_view_viewattemptlink'] = 'View attempt details';