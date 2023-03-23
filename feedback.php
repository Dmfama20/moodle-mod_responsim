<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Prints an instance of responsim.
 *
 * @package     responsim
 * @copyright   2021 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/edit_form.php');




// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$r = optional_param('r', 0, PARAM_INT);

// question-id.
$questionid = optional_param('questionid', 0, PARAM_INT);
$questionanswered = optional_param('questionanswered', 0, PARAM_INT);
$answerchosen = optional_param('answerchosen', 0, PARAM_INT);
$lastanswer = optional_param('lastanswer', 0, PARAM_INT);

// simulation-id.
$simulationid = optional_param('simulationid', 0, PARAM_INT);
$lastpage    = optional_param('lastpage', 0, PARAM_INT);
$showlastpage    = optional_param('showlastpage', 0, PARAM_BOOL);
$answergiven    = optional_param('answergiven', 0, PARAM_INT);
$alternativenextquestion    = optional_param('alternativenextquestion', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('responsim', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('responsim', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($r) {
    $moduleinstance = $DB->get_record('responsim', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('responsim', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'responsim'));
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

$event = \mod_responsim\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('responsim', $moduleinstance);
$event->trigger();

$PAGE->set_url('/mod/responsim/feedback.php', array('id' => $cm->id, 'simulationid'=>$simulationid));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);
$currenttab = 'view';
echo $OUTPUT ->header( $cm, $currenttab, false, null, "TEst");
// Show question in a nice box.
$questiontext= $DB->get_record('question', ['id'=>$questionanswered]);
$questionanswer= $DB->get_record('question_answers', ['id'=>$lastanswer]);
$questionout='<p align=“justify“;
style="margin-left: 0em;
font-weight:bold;
background-color:#f7f7f7;
padding: 2em 2em 2em 2em;
border-width: 2px; border-color: black; border-style:solid;">';
 $questionout.=clean_param($questiontext->generalfeedback,PARAM_TEXT);
 $questionout.="</br>";
 $questionout.=clean_param($questionanswer->feedback,PARAM_TEXT);
 $questionout.='</p>';
// $questionout=Example of a paragraph with margin and padding.</p>';
  echo  $questionout;
  $back_to_question_url=new moodle_url('/mod/responsim/view.php',
  array('id' => $cm->id, 'simulationid'=>$simulationid,'questionid'=>$questionid));
  echo $OUTPUT->single_button($back_to_question_url, 'Zur nächsten Frage', 'get');
echo $OUTPUT->footer();