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
 * Plugin upgrade steps are defined here.
 *
 * @package     responsim
 * @category    upgrade
 * @copyright   2021 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/upgradelib.php');

/**
 * Execute responsim upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_responsim_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // For further information please read {@link https://docs.moodle.org/dev/Upgrade_API}.
    //
    
    
        if ($oldversion < 2021120700) {

         // Define table responsim_questions to be created.
        $table = new xmldb_table('responsim_questions');

        // Adding fields to table responsim_questions.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('question', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tag', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table responsim_questions.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for responsim_questions.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        // Responsim savepoint reached.
        upgrade_mod_savepoint(true, 2021120700, 'responsim');
    }
    
    
        if ($oldversion < 2021120700) {

        // Define table responsim_answers to be created.
        $table = new xmldb_table('responsim_answers');

        // Adding fields to table responsim_answers.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('tag', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('answer', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table responsim_answers.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for responsim_answers.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Responsim savepoint reached.
        upgrade_mod_savepoint(true, 2021120700, 'responsim');
    }

    
        if ($oldversion < 2021120700) {

        // Define table responsim_gamesession to be created.
        $table = new xmldb_table('responsim_gamesession');

        // Adding fields to table responsim_gamesession.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('mdl_user', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('state', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, 'progress');

        // Adding keys to table responsim_gamesession.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for responsim_gamesession.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Responsim savepoint reached.
        upgrade_mod_savepoint(true, 2021120700, 'responsim');
    }

    
        if ($oldversion < 2021120700) {

               // Define table responsim_answertracking to be created.
               $table = new xmldb_table('responsim_answertracking');

               // Adding fields to table responsim_answertracking.
               $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
               $table->add_field('gamesession', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
               $table->add_field('mdl_user', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
               $table->add_field('question', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
               $table->add_field('answer', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
               $table->add_field('answer_correct', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
       
               // Adding keys to table responsim_answertracking.
               $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
       
               // Conditionally launch create table for responsim_answertracking.
               if (!$dbman->table_exists($table)) {
                   $dbman->create_table($table);
               }

        // Responsim savepoint reached.
        upgrade_mod_savepoint(true, 2021120700, 'responsim');
    }

    
        if ($oldversion < 2021120700) {

        // Define table responsim_variables to be created.
        $table = new xmldb_table('responsim_variables');

        // Adding fields to table responsim_variables.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('variable', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table responsim_variables.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for responsim_variables.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Responsim savepoint reached.
        upgrade_mod_savepoint(true, 2021120700, 'responsim');
    }

    
        if ($oldversion < 2021120700) {

        // Define table responsim_variable_values to be created.
        $table = new xmldb_table('responsim_variable_values');

        // Adding fields to table responsim_variable_values.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('variable', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gamesession', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('mdl_user', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('variable_value', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table responsim_variable_values.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for responsim_variable_values.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Responsim savepoint reached.
        upgrade_mod_savepoint(true, 2021120700, 'responsim');
    }


    if ($oldversion < 2021120700) {

         // Define table responsim_simulation_data to be created.
         $table = new xmldb_table('responsim_simulation_data');

         // Adding fields to table responsim_simulation_data.
         $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
         $table->add_field('simulation', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
         $table->add_field('question', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
         $table->add_field('first_question', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
         $table->add_field('next_question', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
         $table->add_field('last_question', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
         $table->add_field('end_question', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
 
         // Adding keys to table responsim_simulation_data.
         $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
 
         // Conditionally launch create table for responsim_simulation_data.
         if (!$dbman->table_exists($table)) {
             $dbman->create_table($table);
         }
 

        // Responsim savepoint reached.
        upgrade_mod_savepoint(true, 2021120700, 'responsim');
    }

    if ($oldversion < 2021120700) {

             // Define table responsim_simulations to be created.
             $table = new xmldb_table('responsim_simulations');

             // Adding fields to table responsim_simulations.
             $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
             $table->add_field('questions_raw', XMLDB_TYPE_TEXT, null, null, null, null, null);
             $table->add_field('name', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
             $table->add_field('mdl_user', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
             $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
             $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
     
             // Adding keys to table responsim_simulations.
             $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
     
             // Conditionally launch create table for responsim_simulations.
             if (!$dbman->table_exists($table)) {
                 $dbman->create_table($table);
             }

        // Responsim savepoint reached.
        upgrade_mod_savepoint(true, 2021120700, 'responsim');
    }





    return true;
}
