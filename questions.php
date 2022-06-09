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
$categoryid = optional_param('categoryid', 0, PARAM_INT);

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

$PAGE->set_url('/mod/responsim/view.php', array('id' => $cm->id, 'categoryid'=>$categoryid));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

//Add a fake block which is displaying some addtional data
// responsim_add_fake_blocks($PAGE,$cm);



// $url_add_question = html_writer::link(new moodle_url('/mod/responsim/add_question.php', array('id' => $PAGE->cm->id ))
// , "Frage erstellen", array('class' => 'btn btn-primary'));

// echo $OUTPUT->add_button($url_add_question);


$mform = new questions_form(null, array('cmid'=>$cm->id, 'courseid'=>$course->id, 'categoryid'=>$categoryid ));


// $mform->set_data((object)$currentparams);
if($data = $mform->get_data()) {
    //  throw new dml_exception(var_dump($data));

    // show questions as a html table
    if($data->bulkdownload=='0')    {

        
        $table=list_all_questions($data );

        // echo html_writer::table($table);                    
}
// Use Bulk download of questions
    else    {   

        if(!empty($data->selectcategories[0]))  {
            // User has selected a category
            $rdurl=new moodle_url('/mod/responsim/questions.php',array('id' => $cm->id, 'categoryid'=>$data->selectcategories[0]));
    
           
         }
         if(!empty($data->selectquestions))     {
            // User has selected some questions
            $bulk_questions = array();
           $counter=0;
            foreach($data->selectquestions as $qu)   {
                $question=$DB->get_record('question',['id'=>$qu]);
                $answers= $DB->get_records('question_answers',['question'=>$qu]);
                foreach($answers as $ans)   {
                    foreach($data->selectvariables as $var)   {
                        $variable=$DB->get_record('responsim_variables',['id'=>$var]);
                        $bulk_questions[$counter]= array($qu, clean_param($question->questiontext,PARAM_TEXT),$ans->id,clean_param($ans->answer,PARAM_TEXT), 
                        $var,$variable->variable);
                        $counter++;


                    }    

                }
           
            }

            download_send_headers("data_export_" . date("Y-m-d") . ".csv");
            echo array2csv($bulk_questions);
            die();
         }

      

         redirect($rdurl); 
    }

    

    }
    

else    {

    
  
    }


    $OUTPUT = $PAGE->get_renderer('mod_responsim');
$currenttab = 'questions';
echo $OUTPUT ->header( $cm, $currenttab, false, null, "TEst");
//display the form
$mform->display();

if(isset($table))  {
    echo html_writer::table($table); 
}



echo $OUTPUT->footer();
