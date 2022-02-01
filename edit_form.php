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
 * Library of functions for mod_responsim.
 *
 * @package     mod_responsim
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class responsim_variables_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $CFG;
       
        $mform = $this->_form; // Don't forget the underscore! 
        $attributes=array('size'=>'20');

       
        // $mform->addElement('text', 'addtime', 'Tage drauf rechnen', $attributes);
        $mform->setType('addtime', PARAM_INT);
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
        $mform->addElement('static', '', '', "Variablen anlegen");
        $attr_var_name=array('size'=>'20');
        $mform->addElement('text', 'varname', "Variablen-Name", $attr_var_name);
         $mform->setType('varname', PARAM_TEXT);
         
          $attr_var_value=array('size'=>'20');
        $mform->addElement('text', 'varvalue', "Variablen-Wert", $attr_var_value);
         $mform->setType('varvalue', PARAM_TEXT);
        $this->add_action_buttons($cancel = true, $submitlabel='Speichern!');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}

class responsim_variable_form_edit extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $DB;
       
        $mform = $this->_form; // Don't forget the underscore! 
        $attributes=array('size'=>'20');

       
        // $mform->addElement('text', 'addtime', 'Tage drauf rechnen', $attributes);
        $mform->setType('addtime', PARAM_INT);
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }

        $variable= $DB->get_record('responsim_variables', ['id' =>$this->_customdata['variableid'] ]);
        $variable_value= $DB->get_record('responsim_variable_values', ['id' =>$this->_customdata['variableid'] ]);

        $mform->addElement('static', '', '', "Variablen bearbeiten");
        $attr_var_name=array('size'=>'20');
        $mform->addElement('text', 'varname', "Variablen-Name", $attr_var_name)->setValue($variable->variable);
         $mform->setType('varname', PARAM_TEXT);
         
          $attr_var_value=array('size'=>'20');
        $mform->addElement('text', 'varvalue', "Variablen-Wert", $attr_var_value)->setValue($variable_value->variable_value);
         $mform->setType('varvalue', PARAM_TEXT);

         $mform->addElement('hidden', 'valid', $variable_value->id);
        $mform->setType('valid', PARAM_INT);
        $this->add_action_buttons($cancel = true, $submitlabel='Speichern!');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}

class responsim_simulation_edit_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $DB;
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }

        $simdata= $DB->get_record('responsim_simulations',['id'=>  $this->_customdata['simid']]);

        $mform->addElement('static', '', '', "Fragen zur Simulation hinzufügen");
        $attr_simedit=array('size'=>'40');
        if($simdata)    {
            $mform->addElement('text', 'simedit', "Fragen-IDs mit Kommas separiert", $attr_simedit)->setValue($simdata->questions_raw); 
        }
        else    {
            $mform->addElement('text', 'simedit', "Fragen-IDs mit Kommas separiert", $attr_simedit);
        }
         
        // $mform->addElement('text', 'simedit', "Fragen-IDs mit Kommas separiert", $attr_simedit); 
        $mform->setType('simedit', PARAM_TEXT);
        $mform->addElement('hidden', 'simid', $this->_customdata['simid']);
        $mform->setType('simid', PARAM_INT);

         
        $this->add_action_buttons($cancel = true, $submitlabel='Speichern!');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}



class responsim_edit_rules_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $DB;
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }

        

         
        $this->add_action_buttons($cancel = false, $submitlabel='Neue Regel anlegen');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}

class responsim_add_rule_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $DB, $CFG;
    $categories= get_mdl_categories($this->_customdata['cmid'], $this->_customdata['courseid']);
       
    $mform = $this->_form; // Don't forget the underscore! 
    foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
    

    $mform->addElement('advcheckbox', 'bulkupload', 'Bulk Upload', 'use CSV-file');
    $mform->setDefault('bulkupload', 0);
   
    $maxbytes = $CFG->maxbytes;

    $mform->addElement('filepicker', 'csvfile', 'upload your CSV-file', null,
                   array('maxbytes' => $maxbytes, 'accepted_types' => '*'));
    $mform->hideif('csvfile', 'bulkupload', 'eq', '0');

    $options = array(
        'comma' => ',',
        'semicolon' => ';'
    );
    $select = $mform->addElement('select', 'delimiter', 'CSV delimiter', $options);
    // This will select the colour blue.
    $select->setSelected('comma');
    $mform->hideif('delimiter', 'bulkupload', 'eq', '0');

    // Selected category 
    $categorytoinclude = array();
    foreach ($categories as $index => $category) {
            
            $categorytoinclude[$category['id']] = $category['name'];
            
        }
     $categoryid= $this->_customdata['categoryid'];
    if($categoryid>0) {
        $mform->addElement('select', 'selectcategory', 'select categories', $categorytoinclude)->setSelected($categoryid); 
    }
    else{
        $mform->addElement('select', 'selectcategory', 'select categories', $categorytoinclude);
    }
    $mform->hideif('selectcategory', 'bulkupload', 'eq', '1');
      

    $getquestions_config=  ['category' => $categoryid];   
    $questions = $DB->get_records('question',$getquestions_config);

    $questiontoinclude=array();
    
    foreach ($questions as $qu) {
        $questiontoinclude[$qu->id]=$qu->questiontext;
         }
    $questionid= $this->_customdata['questionid'];
    if($questionid) {
        $mform->addElement('select', 'selectquestion', 'select question', $questiontoinclude)->setSelected($questionid);
    }
    else{
        $mform->addElement('select', 'selectquestion', 'select question', $questiontoinclude);
    }
    $mform->hideif('selectquestion', 'bulkupload', 'eq', '1');


    $getanswers_config=  ['question' => $questionid];   
    $answers = $DB->get_records('question_answers',$getanswers_config);

    $answerstoinclude=array();
    
    foreach ($answers as $ans) {
        $answerstoinclude[$ans->id]=$ans->answer;
         }
    $answerid= $this->_customdata['answerid'];
    if($questionid) {
        $mform->addElement('select', 'selectanswer', 'select anser', $answerstoinclude)->setSelected($answerid);
    }
    else{
        $mform->addElement('select', 'selectanswer', 'select answer', $answerstoinclude);
    }
    $mform->hideif('selectanswer', 'bulkupload', 'eq', '1');
    


      
    $vars = $DB->get_records('responsim_variables');

    $varstoinclude=array();
    
    foreach ($vars as $var) {
        $varstoinclude[$var->id]=$var->variable;
         }
    $varid= $this->_customdata['variableid'];
    if($varid) {
        $mform->addElement('select', 'selectvariable', 'select variable', $varstoinclude)->setSelected($varid);
    }
    else{
        $mform->addElement('select', 'selectvariable', 'select variable', $varstoinclude);
        
    }
    $mform->hideif('selectvariable', 'bulkupload', 'eq', '1');

    $mform->addElement('text', 'varchange', "Variablen-Änderung", ['size'=>'40']);
    $mform->setType('varchange', PARAM_RAW);
    $mform->hideif('varchange', 'bulkupload', 'eq', '1');



   



        
    $this->add_action_buttons($cancel = true, $submitlabel='OK!');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}


class responsim_show_question_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $DB, $SESSION;
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }

        $mform->addElement('hidden', 'qid',  $this->_customdata['questionid']);
        $mform->setType('qid', PARAM_RAW);

        $radioarray=array();
        $alignment=array();
        $i=1;
        $curqu= $DB->get_record('responsim_gamesession',['id'=>'1']);
        $params = array('questionid' => $this->_customdata['questionid']);
        // $answers=$DB->get_records('question_answers',['question'=> '10504']);
           // build query for moodle question selection
           $sql = "
           SELECT answer
             FROM {question_answers} 
            WHERE question = :questionid
       ";
       
       // Get all available questions.
       $answers = $DB->get_records_sql($sql,$params);
        foreach($answers as $ans)    {
            $radioarray[] = $mform->createElement('radio', 'answer', '', clean_param($ans->answer, PARAM_TEXT), $i);
            $alignment[]='<br/>';
            $i++;            
        }
        $mform->addGroup($radioarray, 'radioar', '', $alignment, false);
        $this->add_action_buttons($cancel = false, $submitlabel='Abschicken!');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}

class responsim_questions_form_add extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $CFG;
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
        $mform->addElement('static', '', '', "Fragen anlegen");
        $attr_question_name=array('size'=>'20');
        $mform->addElement('text', 'questionname', "Fragen-Name", $attr_question_name);
        $mform->setType('questionname', PARAM_TEXT);

        $mform->addElement('editor', 'questiontext', "Fragentext");
        $mform->setType('questiontext', PARAM_RAW);
        $this->add_action_buttons($cancel = true, $submitlabel='Speichern!');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}

class responsim_simulations_form_add extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $CFG;
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
        $mform->addElement('static', '', '', "Simulation anlegen");
        $attr_simulation_name=array('size'=>'20');
        $mform->addElement('text', 'name', "Simulations-Name", $attr_simulation_name);
        $mform->setType('name', PARAM_TEXT);
        $this->add_action_buttons($cancel = false, $submitlabel='Anlegen!');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}

class responsim_simulations_form_delete extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $CFG;
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        } 
        $this->add_action_buttons($cancel = true, $submitlabel='OK!');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}

class responsim_delete_rule_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $CFG;
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
        
        $this->add_action_buttons($cancel = true, $submitlabel='Regel löschen!');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}

class responsim_delete_variable_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $CFG;
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
        
        $this->add_action_buttons($cancel = true, $submitlabel='Variable löschen!');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}


class responsim_answer_form_add extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $CFG;
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
        $mform->addElement('static', '', '', "Antwort anlegen");
        $attr_question_name=array('size'=>'20');
        $mform->addElement('text', 'answername', "Antwort-Name", $attr_question_name);
        $mform->setType('answername', PARAM_TEXT);

        $mform->addElement('editor', 'answertext', "Antworttext");
        $mform->setType('answertext', PARAM_RAW);
         
    
    
        $this->add_action_buttons($cancel = true, $submitlabel='Speichern!');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}

class responsim_questions_form_edit extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $DB, $SESSION;

        // throw new dml_exception(var_dump($SESSION->num_ans));
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
        $mform->addElement('static', '', '', "Frage:");
        $attr_question_name=array('size'=>'30');
      


        $mform->addElement('hidden', 'qid',   $this->_customdata['question']->id  );
        $mform->setType('qid', PARAM_INT);    
       
        
        reset( $this->_customdata['answers']);
         for($i = 1; $i <= $SESSION->num_ans_qe;$i++)  {
            $mform->addElement('hidden',"hidden_".$i,   current($this->_customdata['answers'])->id  );
            $mform->setType('hidden_'.$i , PARAM_INT);
            next( $this->_customdata['answers']);
        
         }

            $mform->addElement('static', '', '',$this->_customdata['question']->questiontext);

            $check = $DB->record_exists('responsim_questions',['question' => $this->_customdata['question']->id]);
            if($check)  {
                $val= $DB->get_record('responsim_questions',['question' => $this->_customdata['question']->id]);
                $mform->addElement('text', 'questiontag', "Frage-Tag", $attr_question_name)->setValue($val->tag);
                $mform->setType('questiontag', PARAM_RAW);

            }

            else{
                $mform->addElement('text', 'questiontag', "Frage-Tag", $attr_question_name);
                $mform->setType('questiontag', PARAM_RAW);

            }


            reset( $this->_customdata['answers']);
    
            for($i = 1; $i <= $SESSION->num_ans_qe;$i++)  {


                $mform->addElement('static', '', '',  current( $this->_customdata['answers'])->answer);
                $check = $DB->record_exists('responsim_answers',['answer' => current( $this->_customdata['answers'])->id ]);
                if($check)  {
                    $val=$DB->get_record('responsim_answers',['answer' => current( $this->_customdata['answers'])->id]);
                    $mform->addElement('text', $i, current( $this->_customdata['answers'])->id, $attr_question_name)->setValue($val->tag);
                    $mform->setType( $i, PARAM_RAW);
                }
                else{
                    $mform->addElement('text', $i, current( $this->_customdata['answers'])->id, $attr_question_name);
                    $mform->setType( $i, PARAM_RAW);
                }
                next( $this->_customdata['answers']);

             }



        $this->add_action_buttons($cancel = true, $submitlabel='OK');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}


class questions_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $DB;
        $categories= get_mdl_categories($this->_customdata['cmid'], $this->_customdata['courseid']);
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }

        $mform->addElement('advcheckbox', 'bulkdownload', 'Bulk download', 'download CSV-file');
        $mform->setDefault('bulkdownload', 1);


        $mform->addElement('static', '', '', "Kategorie wählen");
     
        if($this->_customdata['categoryid']>0)  {
            // Selected category->Category already selected
        $categorytoinclude = array();
        foreach ($categories as $index => $category) {
            
            $categorytoinclude[$category['id']] = $category['name'];
            
        }
        $mform->addElement('select', 'selectcategories', 'select categories', $categorytoinclude)->setSelected($this->_customdata['categoryid']);  
        $mform->getElement('selectcategories')->setMultiple(true);
        $mform->getElement('selectcategories')->setSize(count($categorytoinclude));    
        $mform->setAdvanced('selectcategories', true);   

        }
        else{
             // Selected category->No Category selected
        $categorytoinclude = array();
        foreach ($categories as $index => $category) {
            
            $categorytoinclude[$category['id']] = $category['name'];
            
        }
        $mform->addElement('select', 'selectcategories', 'select categories', $categorytoinclude);  
        $mform->getElement('selectcategories')->setMultiple(true);
        $mform->getElement('selectcategories')->setSize(count($categorytoinclude));    
        $mform->setAdvanced('selectcategories', true);   

        }
        


        if($this->_customdata['categoryid']>0)  {
              // Select questions
         $questionstoinclude = array();
         $questions=$DB->get_records('question',['category'=> $this->_customdata['categoryid']]);
        //  All questions of all Simulations
        $recs=$DB->get_records('responsim_simulations',['cmid'=>$this->_customdata['cmid']]);
        $simquestions=array();
        foreach($recs as $sim)   {
            $questions_temp = explode(',',$sim->questions_raw);
            $simquestions=array_merge($simquestions,$questions_temp);
        }
         foreach ($questions as $qu) {
             if(in_array($qu->id,$simquestions))    {
                $questionstoinclude[$qu->id] = $qu->name;
             }   
         }
         $mform->addElement('select', 'selectquestions', 'select questions', $questionstoinclude);  
         $mform->getElement('selectquestions')->setMultiple(true);
         $mform->getElement('selectquestions')->setSize(count($questionstoinclude));    
         $mform->setAdvanced('selectquestions', true);   


        } 
      
        if($this->_customdata['categoryid']>0)  {
             // Select variables
         $variablestoinclude = array();
         $variables=$DB->get_records('responsim_variables',['cmid'=>$this->_customdata['cmid']]);
         foreach ($variables as $var) {
             
             $variablestoinclude[$var->id] = $var->variable;
             
         }
         $mform->addElement('select', 'selectvariables', 'select variables', $variablestoinclude);  
         $mform->getElement('selectvariables')->setMultiple(true);
         $mform->getElement('selectvariables')->setSize(count($variablestoinclude));    
         $mform->setAdvanced('selectvariables', true);   
         $mform->hideIf('selectvariables', 'bulkdownload','neq','1');  

        }
        


        $this->add_action_buttons($cancel = true, $submitlabel='OK');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}
