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
require_once($CFG->libdir . '/csvlib.class.php');

// Course module id.
$id = optional_param('id', 0, PARAM_INT);
// ID of the simulation.
$simulationid = optional_param('simulationid',0,  PARAM_INT);
$categoryid = optional_param('categoryid',0,  PARAM_INT);
$questionid = optional_param('questionid',0,  PARAM_INT);
$answerid =  optional_param('answerid',0,  PARAM_INT);
$variableid = optional_param('variableid',0,  PARAM_INT);
$bulkmode = optional_param('bulkmode',0,  PARAM_INT);

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

$PAGE->set_url('/mod/responsim/add_rule.php', array('id' => $cm->id,'simulationid'=>$simulationid,
 'categoryid'=>$categoryid, 'questionid'=>$questionid,'variableid'=>$variableid));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);




$simulation = $DB->get_record('responsim_simulations',['id'=>$simulationid]); 

$rdurl=new moodle_url('/mod/responsim/simulations.php',array('id' => $cm->id));

$mform = new responsim_add_rule_form(null, array('simulationid'=>$simulationid,'cmid' =>$cm->id, 
'courseid'=>$course->id,'categoryid'=>$categoryid, 'questionid'=>$questionid,'answerid'=>$answerid,'variableid'=>$variableid));

if ($mform->is_cancelled())     {

    $currentparams = ['id' => $cm->id];
    redirect(new moodle_url('/mod/responsim/view.php', $currentparams));  
}
// $mform->set_data((object)$currentparams);
if($data = $mform->get_data()) {
 // Mode:Bulkupload
  if($data->bulkupload==1)  {

      if($data->deletebyupload==1)  {
        // First delete all existing rules
        if($DB->record_exists('responsim_laws',['cmid'=>$cm->id,'simulation'=>$simulationid]))  {
            $records=$DB->get_records('responsim_laws',['cmid'=>$cm->id,'simulation'=>$simulationid]);
        foreach($records as $r)     {
            $lawid=$r->id;
            $DB->delete_records('responsim_laws',['id'=>$lawid]);

        }

        }
        

      }
    //   Create new rules

    $importid = csv_import_reader::get_new_iid('uploadlist');

    $cir = new csv_import_reader($importid, 'uploadlist');

    $filecontent = $mform->get_file_content('csvfile');

    if($data->delimiter=='comma')   {
        $readcount = $cir->load_csv_content($filecontent, null, 'comma');
    }
    else    {
        $readcount = $cir->load_csv_content($filecontent, null, 'semicolon');
    }

    if (!$readcount) {

        $errors[] = $cir->get_error();

    }

    $headers = $cir->get_columns();
    if (!$headers) {

        $errors[] = 'Cannot parse submitted CSV file.';

    }

    if( empty($errors) )  {

        $cir->init();

        $fieldnames = array();

        foreach ($headers as $header) {

            $fieldnames[] = $header;

        }


        $iteration = 0;

        $csvData = array();

        while ($rowdata = $cir->next()) {

            $iteration ++;

            $csvData[] = array_combine($fieldnames, $rowdata);

        }

    }
    // Iteration over each line
     $counter=0;
    foreach($csvData as $line)   {

        // echo var_dump('question: '.$ent['question']);

        // Save CSV-content to DB
        // TODO: modify gamesession!
        $data_config = ['gamesession'=>'1', 'cmid'=>$cm->id,'simulation'=>$simulationid, 'question'=>$line['question'],
                        'answer'=>$line['answer'],'variable'=>$line['variable'],
                         'variable_change'=>$line['variable_change'],'next_question'=>$line['next_question']];
       $DB->insert_record('responsim_laws',$data_config);
       $counter++;
    }


$rdurl=new moodle_url('/mod/responsim/edit_rules.php',array('id' => $cm->id,'simulationid'=>$simulationid));
redirect($rdurl);

  }

//   Mode:Add rules manually

  else  {

    if($data->selectcategory) {

        $rdurl=new moodle_url('/mod/responsim/add_rule.php',array('id' => $cm->id,'simulationid'=>$simulationid, 'categoryid'=>$data->selectcategory,
        'questionid'=>$data->selectquestion,'answerid'=>'0','variableid'=>'0'));
    
        if($data->selectquestion)   {
    
            $rdurl=new moodle_url('/mod/responsim/add_rule.php',array('id' => $cm->id,'simulationid'=>$simulationid,
             'categoryid'=>$data->selectcategory,'questionid'=>$data->selectquestion,'answerid'=>$data->selectanswer,'variableid'=>'0'));
        }
        if($data->selectanswer)   {
      
            $rdurl=new moodle_url('/mod/responsim/add_rule.php',array('id' => $cm->id,'simulationid'=>$simulationid,
             'categoryid'=>$data->selectcategory,'questionid'=>$data->selectquestion,'answerid'=>$data->selectanswer,
             'variableid'=>$data->selectvariable));
        }
        if($data->selectvariable)   {
            
         
            $rdurl=new moodle_url('/mod/responsim/add_rule.php',array('id' => $cm->id,'simulationid'=>$simulationid,
             'categoryid'=>$data->selectcategory,'questionid'=>$data->selectquestion,'answerid'=>$data->selectanswer,'variableid'=>$data->selectvariable));
    
            
    
        }
    
        if(strlen($data->varchange)>0)  {
    
            add_rule($data, $simulationid,$cm->id);
    
            $rdurl=new moodle_url('/mod/responsim/rules.php',array('id' => $cm->id));
    
        }
    
        
        
    
    }
    else{
        $rdurl=new moodle_url('/mod/responsim/edit_rules.php',array('id' => $cm->id,'simulationid'=>$simulationid));
    
    }
    
    
    redirect($rdurl);


  }




}

else {
    
}
$OUTPUT = $PAGE->get_renderer('mod_responsim');
$currenttab = 'rules';
echo $OUTPUT ->header( $cm, $currenttab, false, null, "TEst");

 echo $OUTPUT->heading($simulation->name." Regel hinzufügen",4);
$mform->display();


echo $OUTPUT->footer();
