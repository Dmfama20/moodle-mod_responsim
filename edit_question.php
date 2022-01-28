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

// question id.
$questionid = optional_param('questionid',0, PARAM_INT);

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

$PAGE->set_url('/mod/responsim/edit_question.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);



$question= $DB->get_record('question', ['id' =>$questionid ]);
$answers= $DB->get_records('question_answers', ['question' =>$questionid  ]);
$SESSION->num_ans_qe= count($answers);




$mform = new responsim_questions_form_edit(null, array('question'=>$question ,'answers'=>$answers, 'numans'=>$numans ));


// $mform->set_data((object)$currentparams);
if($data = $mform->get_data()) {
    
    if(strlen($data->questiontag)>0)  {
        // Check if record already erxists
        $check = $DB->record_exists('responsim_questions',['question'=>$data->qid ]);
        if($check)  {
            $rec= $DB->get_record('responsim_questions',['question'=>$data->qid ]);
            $update_params = ['id'=>$rec->id,'question' =>  $data->qid, 'tag' => $data->questiontag];
            $DB->update_record('responsim_questions',$update_params ); 
        }
        else{

         $insert_params = ['question' =>  $data->qid, 'tag' => $data->questiontag];
            $DB->insert_record('responsim_questions',$insert_params ); 

        }


    } 
    $answers  = $DB->get_records('question_answers', ['question' => $data->qid ] ); 
    
    
    $counter=1;
     
    for($i = 1; $i <= 4;$i++)   {
        if(strlen($data->$i)>0) {
            // Check if record already exists
            $check = $DB->record_exists('responsim_answers',['answer'=>$data->{"hidden_".$i} ]);
            if($check)  {
                $rec=$DB->get_record('responsim_answers',['answer'=>$data->{"hidden_".$i} ]);
                $update_params = ['id'=>$rec->id,'answer' =>  $data->{"hidden_".$i}, 'tag' => $data->$i];
                $DB->update_record('responsim_answers',$update_params ); 
            }

            else{
                $insert_params = ['answer' => $data->{"hidden_".$i}, 'tag' => $data->$i];
                $DB->insert_record('responsim_answers',$insert_params );   
            }

           
        }
        

     }

     redirect(new moodle_url('/mod/responsim/questions.php', array('id'=>$cm->id)));
                
}

echo $OUTPUT ->header( );

    //display the form
$mform->display();


echo $OUTPUT->footer();
