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
 * German language strings
 *
 * @package    local_eledia_quizattempt_monitoring
 * @author     Ralf Wiederhold <ralf.wiederhold@eledia.de>
 * @copyright  Ralf Wiederhold 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

$string['cachedef_filtersettings'] = 'Session Cache für die Hinterlegung der Filtereinstellungen eines Nutzers.';
$string['eledia_quizattempt_monitoring:view'] = 'Zugriff auf Monitoring Interface';
$string['eledia_quizattempt_monitoring:override'] = 'Overrides für Nutzer/Quiz-Attempts anlegen';
$string['form_overrideform_label_userlist'] = 'Overrides für Nutzer';
$string['nav_view'] = 'Quizversuchs Monitoring';
$string['page_view_deleteoverrides_confirmstr'] = 'Quiz Overrides für folgende Nutzer löschen: <br> {$a}';
$string['page_view_heading'] = '{$a}: Quizversuchs Monitoring';
$string['page_view_heading_overrideform'] = '{$a}: Overrides anlegen / aktualisieren';
$string['page_view_status_filterupdated'] = 'Filtereinstellungen wurden hinterlegt';
$string['page_view_status_filterreset'] = 'Filtereinstellungen wurden entfernt';
$string['page_view_status_overrideform_cancelled'] = 'Anlegen / Aktualisieren von Overrides wurde abgebrochen.';
$string['page_view_status_overridescreated'] = 'Overrides wurden für die ausgewählten Nutzer angelegt / aktualisiert';
$string['page_view_status_overridesdeleted'] = 'Overrides wurden für die ausgewählten Nutzer gelöscht.';
$string['pluginname'] = 'Quizversuchs Monitoring';
$string['plugindesc'] = 'Fügt eine Seite zum Quiz-Administrationsmenü hinzu die es berechtigten Nutzern erlaubt den Fortschritt von Teilnehmern in ihren individuellen Quizversuchen einzusehen, sowie Quiz-Overrides für die jeweils sichtbaren Versuche zu definieren.';
$string['renderer_render_attempt_list_attemptgrade'] = '<a href="{$a->reviewurl}">Gesamt</a>: {$a->achieved} von {$a->max} Punkten ({$a->percent}%)';
$string['renderer_render_attempt_list_attemptprogressdescription'] = '{$a->answered} von {$a->questioncount} Fragen beantwortet<ul><li>Vollständige Antworten: {$a->complete}</li><li>Unvollständige Antworten: {$a->invalid}</li><li>Fehlende Antworten: {$a->todo}</li></ul>';
$string['renderer_render_attempt_list_attempttimedue'] = 'Abgabe fällig: {$a}';
$string['renderer_render_attempt_list_attempttimedue_nolimit'] = 'Kein Zeitlimit';
$string['renderer_render_attempt_list_questiondoesntrequiremanualgrading'] = 'Frage wird automatisch bewertet';
$string['renderer_render_attempt_list_questionnotgraded'] = 'Noch nicht bewertet';
$string['renderer_render_attempt_list_questionrequiresmanualgrading'] = 'Manuelle Bewertung notwendig';
$string['renderer_render_attempt_list_questiongradedmanually'] = 'Manuell kommentiert und / oder bewertet';
$string['renderer_render_attempt_list_questiongradedautomatically'] = 'Automatisch bewertet';
$string['renderer_render_attempt_list_questionmarks'] = '{$a->achieved} / {$a->max} Punkte';
$string['renderer_render_attempt_list_questionnumber'] = 'Frage {$a}';
$string['renderer_render_attempt_list_qstate_complete'] = 'Vollständige Antwort';
$string['renderer_render_attempt_list_qstate_invalid'] = 'Unvollständige Antwort';
$string['renderer_render_attempt_list_qstate_todo'] = 'Fehlende Antwort';
$string['renderer_render_attemptslist_override_timeopen'] = 'Quizöffnung: {$a}';
$string['renderer_render_attemptslist_override_timeclose'] = 'Quizschließung: {$a}';
$string['renderer_render_attemptslist_override_timelimit'] = 'Zeitlimit: {$a}';
$string['renderer_render_attemptslist_override_attempts'] = 'Versuche: {$a}';
$string['renderer_render_attemptslist_override_password'] = 'Passwort: {$a}';
$string['template_page_view_attemptfinished'] = 'Abgegeben um';
$string['template_page_view_attemptstarted'] = 'Gestartet um';
$string['template_page_view_filterform_label_stateoption'] = 'Statusfilter';
$string['template_page_view_filterform_label_studentnamefilter'] = 'Teilnehmerfilter';
$string['template_page_view_filterform_placeholder_studentnamefilter'] = 'Geben Sie (Teile) des Namens, der Emailadresse, des Benutzernamens oder der ID-Nummer der gesuchten Teilnehmer ein...';
$string['template_page_view_filterform_label_submitfilter'] = 'Filtern';
$string['template_page_view_filterform_label_resetfilter'] = 'Zurücksetzen';
$string['template_page_view_header_studentname'] = 'Teilnehmer';
$string['template_page_view_header_state'] = 'Status';
$string['template_page_view_header_progress'] = 'Fortschritt';
$string['template_page_view_header_grading'] = 'Bewertung';
$string['template_page_view_header_overrides'] = 'Definierte Overrides';
$string['template_page_view_header_timedue'] = 'Fälligkeit';
$string['template_page_view_label_addoverride'] = 'Override(s) anlegen/aktualisieren';
$string['template_page_view_label_deleteoverride'] = 'Override(s) löschen';
$string['template_page_view_noattempts'] = 'Keine Versuche zum Anzeigen gefunden';
$string['template_page_view_override_nooverrides'] = 'Keine Overrides für Teilnehmer';
$string['template_page_view_override_createlink'] = 'Overrides anlegen / aktualisieren';
$string['template_page_view_override_deletelink'] = 'Overrides löschen';
$string['template_page_view_viewattemptlink'] = 'Versuchsdetails anschauen';
