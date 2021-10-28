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
 * Core lib implementations
 *
 * @package    local_eledia_quizattempt_monitoring
 * @author     Ralf Wiederhold <ralf.wiederhold@eledia.de>
 * @copyright  Ralf Wiederhold 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

function local_eledia_quizattempt_monitoring_extend_settings_navigation(\settings_navigation $settingsnav, $context) {
    global $CFG, $PAGE;

    // We only want to work with module context.
    if (!($context instanceof \context_module)) {
        return;
    }

    // Check if it is a quiz module.
    $cm = get_coursemodule_from_id('quiz', $context->instanceid);
    if (empty($cm)) {
        return;
    }

    // Make sure the current user may see our settings node.
    if (!has_any_capability(['local/eledia_quizattempt_monitoring:view', 'local/eledia_quizattempt_monitoring:override'], $context)) {
        return;
    }

    // Get the quiz settings node.
    $settingnode = $settingsnav->find('modulesettings', navigation_node::TYPE_SETTING);

    // Add our node.
    $text = get_string('nav_view', 'local_eledia_quizattempt_monitoring');
    $url = new moodle_url('/local/eledia_quizattempt_monitoring/view.php', array('id' => $context->instanceid));
    $foonode = navigation_node::create(
        $text,
        $url,
        navigation_node::NODETYPE_LEAF,
        $text,
        'elediaquizattemptmonitoringview',
        new pix_icon('t/download', $text)
    );
    if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
        $foonode->make_active();
    }
    $settingnode->add_node($foonode);
}
