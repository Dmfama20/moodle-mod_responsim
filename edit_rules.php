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
// ID of the simulation.
$simulationid = optional_param('simulationid',0,  PARAM_INT);

// Activity instance id.
$r = optional_param('r', 0, PARAM_INT);

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

$PAGE->set_url('/mod/responsim/edit_rules.php', array('id' => $cm->id,'simulationid'=>$simulationid));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);




$simulation = $DB->get_record('responsim_simulations',['id'=>$simulationid]); 

$addurl=new moodle_url('/mod/responsim/add_rule.php',array('id' => $cm->id,'simulationid'=>$simulationid,
'categoryid'=>'0', 'questionid'=>'0','answerid'=>'0','variableid'=>'0'));
$mform = new responsim_edit_rules_form(null, array('simulationid'=>$simulationid ));

if ($mform->is_cancelled())     {

    $currentparams = ['id' => $cm->id];
    redirect(new moodle_url('/mod/responsim/view.php', $currentparams));  
}
// $mform->set_data((object)$currentparams);
if($data = $mform->get_data()) {

redirect($addurl);

}





$OUTPUT = $PAGE->get_renderer('mod_responsim');
$currenttab = 'rules';
echo $OUTPUT ->header( $cm, $currenttab, false, null, "TEst");

echo $OUTPUT->heading($simulation->name." bearbeiten:",4);
$mform->display();

$table=list_all_rules($cm->id, $simulationid);
echo html_writer::table($table);   

echo $OUTPUT->footer();
