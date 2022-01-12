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


class responsim_show_question_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $DB;


       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
        $answers= $DB->get_records('question_answers',['question' =>  $this->_customdata['questionid']]);
        $radioarray=array();
        $alignment=array();
        foreach($answers as $answer)    {
            $radioarray[] = $mform->createElement('radio', 'question', '', $answer->answer, $answer->id );
            $alignment[]='<br/>';
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
        global $PAGE, $DB;
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
        $mform->addElement('static', '', '', "Frage:");
        $attr_question_name=array('size'=>'30');
        //  echo var_dump( $this->_customdata['questionid']);
        
        $questionid = $this->_customdata['questionid'];

        $question= $DB->get_record('question', ['id' =>$this->_customdata['questionid'] ]);
        $answers= $DB->get_records('question_answers', ['question' =>$this->_customdata['questionid'] ]);
        
        $mform->addElement('hidden', 'qid',   $questionid );
        $mform->setType('qid', PARAM_INT);

        
     $mform->addElement('static', '', '', $question->questiontext);
      $mform->addElement('text', 'questiontag', "Frage-Tag", $attr_question_name);
        $mform->setType('questiontag', PARAM_RAW);
       $mform->addElement('static', '', '', "Antworten:");
     foreach($answers as $ans)  {
     $mform->addElement('static', '', '',  $ans->answer);
        $mform->addElement('text', $ans->id, $ans->id, $attr_question_name);
        $mform->setType($ans->id, PARAM_RAW);
     
     }
     
     
     
     
    
        $this->add_action_buttons($cancel = true, $submitlabel='OK');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}


class responsim_add_category_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $PAGE, $DB;
        $categories= get_mdl_categories($this->_customdata['cmid'], $this->_customdata['courseid']);
       
        $mform = $this->_form; // Don't forget the underscore! 
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }
        $mform->addElement('static', '', '', "Kategorie wählen");
     

        // Selected activities by the user
        $categorytoinclude = array();
        foreach ($categories as $index => $category) {
            
            $categorytoinclude[$category['id']] = $category['name'];
            
        }
        $mform->addElement('select', 'selectcategories', 'select categories', $categorytoinclude);  
        $mform->getElement('selectcategories')->setMultiple(true);
        $mform->getElement('selectcategories')->setSize(count($categorytoinclude));    
        $mform->setAdvanced('selectcategories', true);    
        $this->add_action_buttons($cancel = true, $submitlabel='OK');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}
