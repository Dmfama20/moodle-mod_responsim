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
 * Moodle renderer used to display special elements of the lesson module
 *
 * @package mod_responsim
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

class mod_responsim_renderer extends plugin_renderer_base {
    /**
     * Returns the header for the responsim module
     *
     * @param string $currenttab current tab that is shown.
     * @param bool   $extraeditbuttons if extra edit buttons should be displayed.
     * @param int    $lessonpageid id of the lesson page that needs to be displayed.
     * @param string $extrapagetitle String to appent to the page title.
     * @return string
     */
    public function header( $cm, $currenttab = '', $extraeditbuttons = false, $lessonpageid = null, $extrapagetitle = null) {
        global $CFG, $USER;

        $activityname = "Responsim";
        if (empty($extrapagetitle)) {
            $title = $this->page->course->shortname.": ".$activityname;
        } else {
            $title = $this->page->course->shortname.": ".$activityname.": ".$extrapagetitle;
        }

        // Build the buttons
        $context = context_module::instance($cm->id);

        // Header setup.
        $this->page->set_title($title);
        $this->page->set_heading($this->page->course->fullname);
//         lesson_add_header_buttons($cm, $context, $extraeditbuttons, $lessonpageid);
        $output = $this->output->header();

        $cminfo = cm_info::create($cm);
        $completiondetails = \core_completion\cm_completion_details::get_instance($cminfo, $USER->id);
        $activitydates = \core\activity_dates::get_dates_for_module($cminfo, $USER->id);
      

            
          
            if (!empty($currenttab)) {
                ob_start();
                include($CFG->dirroot.'/mod/responsim/tabs.php');
                $output .= ob_get_contents();
                ob_end_clean();
            }
       
            $output .= $this->output->heading($activityname);
            $output .= $this->output->activity_information($cminfo, $completiondetails, $activitydates);

            
        
/*
//         foreach ($lesson->messages as $message) {
//             $output .= $this->output->notification($message[0], $message[1], $message[2]);*/
//         }

        return $output;
    }

    /**
     * Returns the footer
     * @return string
     */
    public function footer() {
        return $this->output->footer();
    }

  

   


    /**
     * Returns HTML to display a message
     * @param string $message
     * @param single_button $button
     * @return string
     */
    public function message($questionid, single_button $button = null) {
        global $DB;
        $question= $DB->get_record('question', ['id'=>$questionid]);
        $output  = $this->output->box_start('generalbox boxaligncenter');
        $output .= $question->questiontext;
        if ($button !== null) {
            $output .= $this->output->box($this->output->render($button), 'lessonbutton standardbutton');
        }
        $output .= $this->output->box_end();
        return $output;
    }

    /**
     * Returns HTML to display a continue button
     * @param lesson $lesson
     * @param int $lastpageseen
     * @return string
     */
    public function show_question( $questionid) {
        global $DB;
        // $counter=1;
        $question= $DB->get_record('question', ['id'=>$questionid]);
        // $answers= $DB->get_records('question_answers', ['question'=>$questionid]);
        // $simulation= $DB->get_record('responsim_simulations',['id'=> '1']);
        // $simulation_data= $DB->get_records('responsim_simulation_data',['simulation'=> '1']);
        // $question_data=$DB->get_record('responsim_simulation_data',['simulation'=> '1','question'=>$questionid]);

        $output = $this->output->box_start('generalbox');
        $output .= $this->output->box($question->questiontext);
        $output .= $this->output->box_end();
        return $output;
    }


       /**
     * Returns HTML to display a continue button
     * @param lesson $lesson
     * @param int $lastpageseen
     * @return string
     */
    public function show_first_page( $simulationid) {
        global $DB;
        $simulationname=$DB->get_record('responsim_simulations',['id'=>$simulationid]);
        $simulation_data=$DB->get_records('responsim_simulation_data',['simulation'=>$simulationid]);
        $output = $this->output->box("Möchten Sie mit der Simulation "."<i>".$simulationname->name."</i>"." starten?", 'generalbox boxaligncenter');
        $output .= $this->output->box_start('center');

        $button = html_writer::link(new moodle_url('/mod/responsim/view.php', array('id' => $this->page->cm->id,
            'simulationid' => $simulationid, 'questionid' => reset($simulation_data)->question)), "Los Gehts! ", array('class' => 'btn btn-primary'));
        $output .= html_writer::tag('span', $button , array('class'=>'lessonbutton standardbutton'));

        $output .= $this->output->box_end();
        return $output;
    }


    /**
     * Returns HTML to display a continue button
     * @param lesson $lesson
     * @param int $lastpageseen
     * @return string
     */
     public function add_button( $url) {
        // $output = $this->output->box("Sie <b>können</b> folgende Wahl treffen: Bla bla bla .....", 'generalbox boxaligncenter');
        $output = $this->output->box_start('center');

        $output .= html_writer::tag('span',  $url , array('class'=>'lessonbutton standardbutton'));
        $output .= $this->output->box_end();
        return $output;
    }


    /**
     * Returns HTML to display a page to the user
     * @param lesson $lesson
     * @param lesson_page $page
     * @param object $attempt
     * @return string
     */
    public function display_page(lesson $lesson, lesson_page $page, $attempt) {
        // We need to buffer here as there is an mforms display call
        ob_start();
        echo $page->display($this, $attempt);
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    


   

    /**
     * Return HTML to display add first page links
     * @param lesson $lesson
     * @return string
     */
    public function add_first_page_links(lesson $lesson) {
        global $CFG;
        $prevpageid = 0;

        $output = $this->output->heading(get_string("whatdofirst", "lesson"), 3);
        $links = array();

        $importquestionsurl = new moodle_url('/mod/lesson/import.php',array('id'=>$this->page->cm->id, 'pageid'=>$prevpageid));
        $links[] = html_writer::link($importquestionsurl, get_string('importquestions', 'lesson'));

        $manager = lesson_page_type_manager::get($lesson);
        foreach ($manager->get_add_page_type_links($prevpageid) as $link) {
            $link['addurl']->param('firstpage', 1);
            $links[] = html_writer::link($link['addurl'], $link['name']);
        }

        $addquestionurl = new moodle_url('/mod/lesson/editpage.php', array('id'=>$this->page->cm->id, 'pageid'=>$prevpageid, 'firstpage'=>1));
        $links[] = html_writer::link($addquestionurl, get_string('addaquestionpage', 'lesson'));

        return $this->output->box($output.'<p>'.implode('</p><p>', $links).'</p>', 'generalbox firstpageoptions');
    }

    /**
     * Returns HTML to display action links for a page
     *
     * @param lesson_page $page
     * @param bool $printmove
     * @param bool $printaddpage
     * @return string
     */
    public function page_action_links(lesson_page $page, $printmove, $printaddpage=false) {
        global $CFG;

        $actions = array();

        if ($printmove) {
            $url = new moodle_url('/mod/lesson/lesson.php',
                    array('id' => $this->page->cm->id, 'action' => 'move', 'pageid' => $page->id, 'sesskey' => sesskey()));
            $label = get_string('movepagenamed', 'lesson', format_string($page->title));
            $img = $this->output->pix_icon('t/move', $label);
            $actions[] = html_writer::link($url, $img, array('title' => $label));
        }
        $url = new moodle_url('/mod/lesson/editpage.php', array('id' => $this->page->cm->id, 'pageid' => $page->id, 'edit' => 1));
        $label = get_string('updatepagenamed', 'lesson', format_string($page->title));
        $img = $this->output->pix_icon('t/edit', $label);
        $actions[] = html_writer::link($url, $img, array('title' => $label));

        // Duplicate action.
        $url = new moodle_url('/mod/lesson/lesson.php', array('id' => $this->page->cm->id, 'pageid' => $page->id,
                'action' => 'duplicate', 'sesskey' => sesskey()));
        $label = get_string('duplicatepagenamed', 'lesson', format_string($page->title));
        $img = $this->output->pix_icon('e/copy', $label, 'mod_lesson');
        $actions[] = html_writer::link($url, $img, array('title' => $label));

        $url = new moodle_url('/mod/lesson/view.php', array('id' => $this->page->cm->id, 'pageid' => $page->id));
        $label = get_string('previewpagenamed', 'lesson', format_string($page->title));
        $img = $this->output->pix_icon('t/preview', $label);
        $actions[] = html_writer::link($url, $img, array('title' => $label));

        $url = new moodle_url('/mod/lesson/lesson.php',
                array('id' => $this->page->cm->id, 'action' => 'confirmdelete', 'pageid' => $page->id, 'sesskey' => sesskey()));
        $label = get_string('deletepagenamed', 'lesson', format_string($page->title));
        $img = $this->output->pix_icon('t/delete', $label);
        $actions[] = html_writer::link($url, $img, array('title' => $label));

        if ($printaddpage) {
            $options = array();
            $manager = lesson_page_type_manager::get($page->lesson);
            $links = $manager->get_add_page_type_links($page->id);
            foreach ($links as $link) {
                $options[$link['type']] = $link['name'];
            }
            $options[0] = get_string('addaquestionpage', 'lesson');

            $addpageurl = new moodle_url('/mod/lesson/editpage.php', array('id'=>$this->page->cm->id, 'pageid'=>$page->id, 'sesskey'=>sesskey()));
            $addpageselect = new single_select($addpageurl, 'qtype', $options, null, array(''=>get_string('addanewpage', 'lesson').'...'), 'addpageafter'.$page->id);
            $addpageselector = $this->output->render($addpageselect);
        }

        if (isset($addpageselector)) {
            $actions[] = $addpageselector;
        }

        return implode(' ', $actions);
    }

    /**
     * Prints the on going message to the user.
     *
     * With custom grading On, displays points
     * earned out of total points possible thus far.
     * With custom grading Off, displays number of correct
     * answers out of total attempted.
     *
     * @param object $lesson The lesson that the user is taking.
     * @return void
     **/

     /**
      * Prints the on going message to the user.
      *
      * With custom grading On, displays points
      * earned out of total points possible thus far.
      * With custom grading Off, displays number of correct
      * answers out of total attempted.
      *
      * @param lesson $lesson
      * @return string
      */
    public function ongoing_score(lesson $lesson) {
        return $this->output->box($lesson->get_ongoing_score_message(), "ongoing center");
    }

    /**
     * Returns HTML to display a progress bar of progression through a lesson
     *
     * @param lesson $lesson
     * @param int $progress optional, if empty it will be calculated
     * @return string
     */
    public function progress_bar(lesson $lesson, $progress = null) {
        $context = context_module::instance($this->page->cm->id);

        // lesson setting to turn progress bar on or off
        if (!$lesson->progressbar) {
            return '';
        }

        // catch teachers
        if (has_capability('mod/lesson:manage', $context)) {
            return $this->output->notification(get_string('progressbarteacherwarning2', 'lesson'));
        }

        if ($progress === null) {
            $progress = $lesson->calculate_progress();
        }

        $content = html_writer::start_tag('div');
        $content .= html_writer::start_tag('div', array('class' => 'progress'));
        $content .= html_writer::start_tag('div', array('class' => 'progress-bar bar', 'role' => 'progressbar',
            'style' => 'width: ' . $progress .'%', 'aria-valuenow' => $progress, 'aria-valuemin' => 0, 'aria-valuemax' => 100));
        $content .= $progress . "%";
        $content .= html_writer::end_tag('div');
        $content .= html_writer::end_tag('div');
        $printprogress = html_writer::tag('div', get_string('progresscompleted', 'lesson', $progress) . $content);
        return $this->output->box($printprogress, 'progress_bar');
    }

    /**
     * Returns HTML to show the start of a slideshow
     * @param lesson $lesson
     */
    public function slideshow_start(lesson $lesson) {
        $attributes = array();
        $attributes['class'] = 'slideshow';
        $attributes['style'] = 'background-color:'.$lesson->properties()->bgcolor.';height:'.
                $lesson->properties()->height.'px;width:'.$lesson->properties()->width.'px;';
        $output = html_writer::start_tag('div', $attributes);
        return $output;
    }
    /**
     * Returns HTML to show the end of a slideshow
     */
    public function slideshow_end() {
        $output = html_writer::end_tag('div');
        return $output;
    }
    /**
     * Returns a P tag containing contents
     * @param string $contents
     * @param string $class
     */
    public function paragraph($contents, $class='') {
        $attributes = array();
        if ($class !== '') {
            $attributes['class'] = $class;
        }
        $output = html_writer::tag('p', $contents, $attributes);
        return $output;
    }


}
