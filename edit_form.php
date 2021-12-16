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
        
          $mform->addElement('static', '', '', "Liste der aktuellen Variablen");
    
    
        $this->add_action_buttons($cancel = false, $submitlabel='OK');
    
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
         
    
        
          $mform->addElement('static', '', '', "Liste der aktuellen Fragen");
    
    
        $this->add_action_buttons($cancel = false, $submitlabel='OK');
    
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
        $mform->addElement('static', '', '', "Fragen bearbeiten");
        $attr_question_name=array('size'=>'20');
        //  echo var_dump( $this->_customdata['questionid']);

        $question= $DB->get_record('responsim_questions', ['id' =>$this->_customdata['questionid'] ]);

        //  echo var_dump($question);

        $mform->addElement('text', 'questionname', "Fragen-Name", $attr_question_name)->setValue($question->question_title);
        $mform->setType('questionname', PARAM_TEXT);

        $mform->addElement('editor', 'questiontext', "Fragentext")->setValue( array('text' => $question->question_text) );
        $mform->setType('questiontext', PARAM_RAW);
    
        $this->add_action_buttons($cancel = false, $submitlabel='OK');
    
    }
    // //Custom validation should be added here
    // function validation($data, $files) {
    //     return array();
    // }
}

