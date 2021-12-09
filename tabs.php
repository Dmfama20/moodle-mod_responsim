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
* Sets up the tabs used by the lesson pages for teachers.
*
* This file was adapted from the mod/quiz/tabs.php
*
 * @package mod_responsim
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
*/

defined('MOODLE_INTERNAL') || die();

/// This file to be included so we can assume config.php has already been included.
global $DB;

if (!isset($currenttab)) {
    $currenttab = '';
}
if (!isset($cm)) {  
      $cm = get_coursemodule_from_id('responsim', $id, 0, false, MUST_EXIST);
//     $context = context_module::instance($cm->id);
}
if (!isset($course)) {
   $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
}

$tabs = $row = $inactive = $activated = array();



$row[] = new tabobject('view', "$CFG->wwwroot/mod/responsim/view.php?id=$cm->id", "Simulation","Previewlesson");
$row[] = new tabobject('edit', "$CFG->wwwroot/mod/responsim/edit.php?id=$cm->id", "Variablen", "Variablen");
$row[] = new tabobject('questions', "$CFG->wwwroot/mod/responsim/questions.php?id=$cm->id", "Fragen", "Fragen");
$row[] = new tabobject('rules', "$CFG->wwwroot/mod/responsim/rules.php?id=$cm->id", "Regeln","Regeln");
$row[] = new tabobject('edit-simulations', "$CFG->wwwroot/mod/responsim/edit-simulations.php?id=$cm->id", "Simulations-Verwaltung","Simulations-Verwaltung");
$row[] = new tabobject('summary', "$CFG->wwwroot/mod/responsim/summary.php?id=$cm->id", "Auswertung", "Auswertung");



$tabs[] = $row;


switch ($currenttab) {
    case 'reportoverview':
    case 'reportdetail':
    /// sub tabs for reports (overview and detail)
        $inactive[] = 'reports';
        $activated[] = 'reports';

        $row    = array();
        $row[]  = new tabobject('reportoverview', "$CFG->wwwroot/mod/lesson/report.php?id=$cm->id&amp;action=reportoverview", get_string('overview', 'lesson'));
        $row[]  = new tabobject('reportdetail', "$CFG->wwwroot/mod/lesson/report.php?id=$cm->id&amp;action=reportdetail", get_string('detailedstats', 'lesson'));
        $tabs[] = $row;
        break;
    case 'collapsed':
    case 'full':
    case 'single':
    /// sub tabs for edit view (collapsed and expanded aka full)
        $inactive[] = 'edit';
        $activated[] = 'edit';

        $row    = array();
        $row[]  = new tabobject('collapsed', "$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id&amp;mode=collapsed", get_string('collapsed', 'lesson'));
        $row[]  = new tabobject('full', "$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id&amp;mode=full", get_string('full', 'lesson'));
        $tabs[] = $row;
        break;
}

print_tabs($tabs, $currenttab, $inactive, $activated);
