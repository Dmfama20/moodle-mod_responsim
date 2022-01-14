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
 * Library of interface functions and constants.
 *
 * @package     responsim
 * @copyright   2021 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function responsim_supports($feature) {
    switch ($feature) {
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the responsim into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_responsim_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function responsim_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('responsim', $moduleinstance);

    return $id;
}

/**
 * Updates an instance of the responsim in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_responsim_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function responsim_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('responsim', $moduleinstance);
}

/**
 * Removes an instance of the responsim from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function responsim_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('responsim', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('responsim', array('id' => $id));

    return true;
}

/**
 * Is a given scale used by the instance of responsim?
 *
 * This function returns if a scale is being used by one responsim
 * if it has support for grading and scales.
 *
 * @param int $moduleinstanceid ID of an instance of this module.
 * @param int $scaleid ID of the scale.
 * @return bool True if the scale is used by the given responsim instance.
 */
function responsim_scale_used($moduleinstanceid, $scaleid) {
    global $DB;

    if ($scaleid && $DB->record_exists('responsim', array('id' => $moduleinstanceid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of responsim.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale.
 * @return bool True if the scale is used by any responsim instance.
 */
function responsim_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('responsim', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the given responsim instance.
 *
 * Needed by {@see grade_update_mod_grades()}.
 *
 * @param stdClass $moduleinstance Instance object with extra cmidnumber and modname property.
 * @param bool $reset Reset grades in the gradebook.
 * @return void.
 */
function responsim_grade_item_update($moduleinstance, $reset=false) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($moduleinstance->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;

    if ($moduleinstance->grade > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax']  = $moduleinstance->grade;
        $item['grademin']  = 0;
    } else if ($moduleinstance->grade < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid']   = -$moduleinstance->grade;
    } else {
        $item['gradetype'] = GRADE_TYPE_NONE;
    }
    if ($reset) {
        $item['reset'] = true;
    }

    grade_update('/mod/responsim', $moduleinstance->course, 'mod', 'responsim', $moduleinstance->id, 0, null, $item);
}

/**
 * Delete grade item for given responsim instance.
 *
 * @param stdClass $moduleinstance Instance object.
 * @return grade_item.
 */
function responsim_grade_item_delete($moduleinstance) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('/mod/responsim', $moduleinstance->course, 'mod', 'responsim',
                        $moduleinstance->id, 0, null, array('deleted' => 1));
}

/**
 * Update responsim grades in the gradebook.
 *
 * Needed by {@see grade_update_mod_grades()}.
 *
 * @param stdClass $moduleinstance Instance object with extra cmidnumber and modname property.
 * @param int $userid Update grade of specific user only, 0 means all participants.
 */
function responsim_update_grades($moduleinstance, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    // Populate array of grade objects indexed by userid.
    $grades = array();
    grade_update('/mod/responsim', $moduleinstance->course, 'mod', 'responsim', $moduleinstance->id, 0, $grades);
}

/**
 * Returns the lists of all browsable file areas within the given module context.
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@see file_browser::get_file_info_context_module()}.
 *
 * @package     responsim
 * @category    files
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return string[].
 */
function responsim_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for responsim file areas.
 *
 * @package     responsim
 * @category    files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info Instance or null if not found.
 */
function responsim_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the responsim file areas.
 *
 * @package     responsim
 * @category    files
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param stdClass $context The responsim's context.
 * @param string $filearea The name of the file area.
 * @param array $args Extra arguments (itemid, path).
 * @param bool $forcedownload Whether or not force download.
 * @param array $options Additional options affecting the file serving.
 */
function responsim_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options = array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);
    send_file_not_found();
}

/**
 * Extends the global navigation tree by adding responsim nodes if there is a relevant content.
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $responsimnode An object representing the navigation tree node.
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function responsim_extend_navigation($responsimnode, $course, $module, $cm) {
}

/**
 * Extends the settings navigation with the responsim settings.
 *
 * This function is called when the context for the page is a responsim module.
 * This is not called by AJAX so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@see settings_navigation}
 * @param navigation_node $responsimnode {@see navigation_node}
 */
function responsim_extend_settings_navigation($settingsnav, $responsimnode = null) {
}

/**
 *
 * @param $cm
 * @param $lesson
 * @param $page
 * @return unknown_type
 */
function responsim_add_fake_blocks($page, $cm) {
   
    $bc = responsim_block_contents($cm->id);
    if (!empty($bc)) {
        $page->blocks->add_fake_block($bc, $page->blocks->get_default_region());
    }

}


/**
 * If there is a variables associated with this
 * responsim, return a block_contents that displays it.
 *
 * @param int $cmid Course Module ID for this lesson
 * @return block_contents
 **/
function responsim_block_contents($cmid) {
 global $DB;

   
        
   $records_vars = $DB->get_records('responsim_variables');
   
$content="";
foreach ($records_vars as $var) {



$record_value = $DB->get_record('responsim_variable_values', ['variable' => $var->id]);
 $content .=$var->variable."= ".$record_value->variable_value ;
  $content .= "<br>" ;
    }
 
    $bc = new block_contents();
    $bc->title = "Overview block";
    $bc->content = $content;
    

    return $bc;
}




/**
 * Saves a new instance of the philosophers quiz into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @return int The id of the newly inserted philosophers record
 * @throws dml_exception
 */
function responsim_add_variables($varname, $value) {
    global $DB, $USER;
    
    // insert into db
        $add_params = ['variable' => $varname];
    $varid = $DB->insert_record('responsim_variables', $add_params);

    $add_params = ['variable' => $varid,  'mdl_user' => $USER->id,'gamesession' => 1,'variable_value'=> $value];
    $id = $DB->insert_record('responsim_variable_values', $add_params);
    

    return $id;
}


/**
 * Saves a new question into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @return int The id of the newly inserted philosophers record
 * @throws dml_exception
 */
function responsim_add_question($questionname, $questiontext) {
    global $DB;
    
        // insert into db
        $add_params = ['question_title' => $questionname, 'question_text'=> $questiontext];
        $id = $DB->insert_record('responsim_questions', $add_params);

    return $id;
}



/**
 * Saves answers given by the students
 *
 *
 * @return $id of the clicked answer
 * @throws dml_exception
 */
function responsim_track_data($data) {
    global $DB,$USER;
        $answers = $DB->get_records('question_answers', ['question' => $data->qid]);
        $numans = count($answers);
        $questionorder='';
        $i=1;
        foreach($answers as $ans)   {
            $questionorder.=$ans->id;
            if(++$i <=  $numans) {
                $questionorder.=',';
            }
        }
        $arrquest= explode(',', $questionorder);
        // insert into db
        $add_params = ['gamesession'=>'1', 'mdl_user'=> $USER->id, 'question'=> $data->qid,'answer'=> $arrquest[$data->answer -1], 'answerordering'=>$questionorder];
         $DB->insert_record('responsim_answertracking', $add_params);

        $id =$arrquest[$data->answer -1];

        
    return  $id;
}

/**
 * Saves answers given by the students
 *
 *
 * @return $id if the datta is stored correctly
 * @throws dml_exception
 */
function responsim_apply_rules($entry) {
    global $DB;
        $tag = $DB->get_record('responsim_answers',['answer' => $entry]);
        //    throw new invalid_parameter_exception("tag= " . $tag->tag );
       
        if($tag->tag=='gut')    {
            $var=$DB->get_record('responsim_variable_values',  ['variable' => '1']);
            echo var_dump($var);
            $varvalue=($var->variable_value)*2;
            $id=$DB->update_record('responsim_variable_values', ['id'=>'1','variable' => '1', 'variable_value'=>$varvalue]);

        }

        else    {

            $var=$DB->get_record('responsim_variable_values',  ['variable' => '1']);
            $varvalue=($var->variable_value)/2;
            $id=$DB->update_record('responsim_variable_values', ['id'=>'1','variable' => '1', 'variable_value'=>$varvalue]);



        }

    return $id ;
}

/**
 * Saves a new answer into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @return int The id of the newly inserted philosophers record
 * @throws dml_exception
 */
function responsim_add_answer($answername, $answertext) {
    global $DB;
        // insert into db
        $add_params = ['answer_title' => $answername, 'answer_text'=> $answertext];
        $id = $DB->insert_record('responsim_answers', $add_params);

    return $id;
}


/**
 * Saves a new simulation into the database
 *
 *
 * @return int The id of the newly inserted responsim record
 * @throws dml_exception
 */
function responsim_add_simulation($simname) {
    global $DB, $USER;
        // insert into db
    
               
                $add_params = ['name' => $simname,'mdl_user'=> $USER->id, 'timecreated'=>time()];
                $id= $DB->insert_record('responsim_simulations', $add_params);
        
    return $id;
}




/**
 * Saves a new simulation into the database
 *
 *
 * @return int The id of the newly inserted responsim record
 * @throws dml_exception
 */
function responsim_add_simulation_data($simdata,$simid,$simquestions) {
    global $DB;
        // save raw data to db
        $DB->update_record('responsim_simulations',['id'=> $simid, 'questions_raw'=> $simquestions]);
        // Delete existing records if necessary
        $check= $DB->record_exists('responsim_simulation_data',['simulation'=> $simid]);

        if($check)  {
            $DB->delete_records('responsim_simulation_data',['simulation'=> $simid]);
        }


    $counter=1;
    $questionids = array();
        // We need more than one loop through the data since fields like "next_question" etc. has to be filled
        foreach($simdata as $data)  {
            $lentgth=count($simdata );
            $copydata= $simdata;

            if($counter==1) {
                $add_params = ['simulation' => $simid,'first_question'=>true, 'question'=>$data, 'next_question'=>$simdata[1]];
                $questionids[] = $DB->insert_record('responsim_simulation_data', $add_params);
                next($simdata);
            }
            else    {
                

                if($counter !=$lentgth)    {
                    // Check if this is the index  of the array
                    $add_params = ['simulation' => $simid,'first_question'=>false, 'question'=>$data,'last_question'=>$simdata[key($simdata)-1]
                    ,'next_question'=>next($simdata)];
                    $questionids[] = $DB->insert_record('responsim_simulation_data', $add_params);

                }
                else{
                    // This is the lasz index of the array
                    $add_params = ['simulation' => $simid,'first_question'=>false, 'question'=>$data,'last_question'=>$simdata[key($simdata)-1],
                    'end_question'=>true];
                $questionids[] = $DB->insert_record('responsim_simulation_data', $add_params);

                }
                
            }
            $counter++;
            
        }

        
    return  $questionids;
}





/**
 * Returns a list of all current variables
 *
 * @return array table of variables
 */
function list_all_variables($editable=false) {
    global $DB, $PAGE;
   //Standard values without submitting the form

//    $activities = local_dexpmod_get_activities($courseID, null, 'orderbycourse');
//    $numactivies = count($activities);
   
   $table = new html_table();
   if($editable) 
   {
   $table->head = array( 'Variable' , 'Wert', 'bearbeiten');
   }
   else {
   $table->head = array( 'Variable' , 'Wert');
   }
   $records_vars = $DB->get_records('responsim_variables');
   
    foreach ($records_vars as $var) {

    $record_value = $DB->get_record('responsim_variable_values', ['variable' => $var->id]);
    if($editable)   {
        $data = array();
        $url = new moodle_url('/mod/responsim/edit_variable.php', array(
            'id'     => $PAGE->cm->id,
            'variableid'=> $var->id
        ));
        $data[] = html_writer::link($url, $var->variable);
        // $data[] = html_writer::link($url, format_string($page->title, true), array('id' => 'lesson-' . $page->id));
        $data[] = $record_value ->variable_value;

        $data[] = "hide/delete";
        $table->data[] = $data;

    }
    
    else    {
    $table->data[] = array($var->variable,$record_value ->variable_value);
    }
    }

  return $table;
}



/**
 * Returns a list of all current questions
 *
 * @return array table of variables
 */
function list_all_questions($formdata) {
    global $DB, $PAGE;
   //Standard values without submitting the form
//    $activities = local_dexpmod_get_activities($courseID, null, 'orderbycourse');
//    $numactivies = count($activities);
   
   $table = new html_table();
   
   $table->head = array( 'Frage' , 'ID');
   
   
foreach($formdata->selectcategories as $cat)    {

   $getquestionss_config=  ['category' => $cat];   
   $records_questions = $DB->get_records('question',$getquestionss_config);
   
foreach ($records_questions as $question) {
        $data = array();
        $url = new moodle_url('/mod/responsim/edit_question.php', array(
            'id'=> $PAGE->cm->id,
            'questionid' =>$question->id
        ));
        $data[] = html_writer::link($url, $question->name);
        $data[] = $question->id;
        $table->data[] = $data;
    }

}

  return $table;
}

/**
 * Returns a list of all current questions
 *
 * @return array table of variables
 */
function list_all_answers() {
    global $DB;
   //Standard values without submitting the form
//    $activities = local_dexpmod_get_activities($courseID, null, 'orderbycourse');
//    $numactivies = count($activities);
   
   $table = new html_table();
   
   $table->head = array( 'Antwort' , 'anzeigen');
   
   $records_answers = $DB->get_records('responsim_answers');
   
foreach ($records_answers as $answer) {
   
    
    $table->data[] = array($answer->answer_title,'bearbeiten');
    
    }

  return $table;
}




/**
 * Returns a list of all current simulations
 *
 * @return array table of simulations
 */
function list_all_simulations() {
    global $DB, $PAGE;
   //Standard values without submitting the form

//    $activities = local_dexpmod_get_activities($courseID, null, 'orderbycourse');
//    $numactivies = count($activities);
   
   $table = new html_table();
   $table->align[1] = 'right';
  
   $table->head = array( 'Simulation', ' ' );
   
  
   $records_sims = $DB->get_records('responsim_simulations');
   
    foreach ($records_sims as $sim) {
        $data = array();
        $url = new moodle_url('/mod/responsim/edit_simulations.php', array(
            'id'     => $PAGE->cm->id,
            'simulationid'=> $sim->id
        ));
        $url2 = new moodle_url('/mod/responsim/edit_simulations.php', array(
            'id'     => $PAGE->cm->id,
            'simulationid'=> $sim->id
        ));
        $data[] = html_writer::link($url, $sim->name);
        $data[] = html_writer::link($url2, 'löschen');
        $table->data[] = $data;
    }

  return $table;
}



/**
 * Returns a list of all current simulations
 *
 * @return array table of simulations
 */
function list_all_simulations_and_start() {
    global $DB, $PAGE;
   //Standard values without submitting the form

//    $activities = local_dexpmod_get_activities($courseID, null, 'orderbycourse');
//    $numactivies = count($activities);
   
   $table = new html_table();
   $table->align[1] = 'right';
  
   $table->head = array( 'Simulation' );
   
  
   $records_sims = $DB->get_records('responsim_simulations');
   
    foreach ($records_sims as $sim) {

        $simdata=$DB->get_records('responsim_simulation_data',['simulation'=>$sim->id] );
        // Move pointer to first element of the array
        $firstquestion=reset($simdata)->question;
        $data = array();
        $url = new moodle_url('/mod/responsim/view.php', array(
            'id'     => $PAGE->cm->id,
            'simulationid'=> $sim->id,
            'questionid'=>$firstquestion,
        ));
        $data[] = html_writer::link($url, $sim->name);
        $table->data[] = $data;
    }

  return $table;
}




 /**
     * Gets all moodle question categories which are applicable for this game.
     *
     * @param int $coursemoduleid
     *
     * @return array
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws restricted_context_exception
     */
     function get_mdl_categories($coursemoduleid, $courseID) {
        
        $modinfo = get_fast_modinfo($courseID);
        $coursemodule = $modinfo->get_cm($coursemoduleid);
        $ctx = $coursemodule->context;

        // load categories
        $question_contexts = new question_edit_contexts($ctx);
        $usable_question_contexts = $question_contexts->having_cap('moodle/question:useall');
        $question_categories = question_category_options($usable_question_contexts);
        /**
         * structure of categories result:
         * two-dimensional array with
         * - first level (contexts): key = context name, value = array of categories in that context.
         *   Contexts normally are 1: course, 2: course area, 3: core system.
         * - second level (i.e. categories per context): key = "categoryId,contextId], value = name of the
         *   category with proper indentation (visualizes hierarchy)
         */
        // transform categories
        
        // echo var_dump($question_categories['Kurs: TK1']);

        foreach ($question_categories as $contextname => $categories) {
           
            
            foreach($categories as $ids => $categoryname) {
                



                $tmpids = \explode(",", $ids);
                 $contextid = \intval($tmpids[1]);
                  $categoryid = \intval($tmpids[0]);

                  $categories_array[] = array (
                    'id'         => $categoryid,
                    'contextid'   => $contextid,
                    'name'       => format_string($categoryname),
                    'contextname'=> $contextname,
                );
            }
        }
        return $categories_array;
    }

