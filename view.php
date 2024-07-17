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
 * TODO describe file view
 *
 * @package    local_final
 * @copyright  2024 Anya <anyama679@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$context = context_system::instance();
$url = new moodle_url('/local/final/view.php', []);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_final'));
$PAGE->set_heading(get_string('pluginname', 'local_final'));

require_login();
if (isguestuser()) {
    throw new moodle_exception('You cannot access this page');
}

$managecourse = has_capability('local/final:managecourses', $context);


echo $OUTPUT->header();

if ($managecourse){
    echo html_writer::link(
        new moodle_url('/course/edit.php'),
    get_string('create_course', 'local_final_manager'),
    ['class' => 'btn btn-primary', 'title' => get_string('create_course', 'local_final_manager')]   
    );

    $sql = "SELECT * FROM {'local_final_courses'} WHERE visible = 1";
    $courses = $DB->get_records($sql);
    // check if this is necessary or can I just do old way? 
    echo html_writer::start_tag('table', ['class' => 'generaltable']);
    echo html_writer::start_tag('thead');
    echo html_writer::tag('tr', html_writer::tag('th', '#') .
                        html_writer::tag('th', 'Course Image') .
                        html_writer::tag('th', 'Course Name') .
                        html_writer::tag('th', 'Date Created'));
    echo html_writer::end_tag('thead');
    echo html_writer::start_tag('tbody');

    foreach ($courses as $course) {
        $image_url = $course->summaryfiles ? $course->summaryfiles : $OUTPUT->image_url('course', 'moodle');
        $image = html_writer::img($image_url, $course->fullname, ['width' => 100]);
        $course_link = html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]), $course->fullname);
        $update_link = html_writer::link(new moodle_url('/course/edit.php', ['id' => $course->id]), get_string('update_course', 'local_course_manager'), ['class' => 'btn btn-update', 'title' => get_string('update_course', 'local_course_manager')]);
        $delete_form = html_writer::start_tag('form', ['action' => new moodle_url('/local/course_manager/delete.php'), 'method' => 'post', 'style' => 'display:inline;']);
        $delete_form .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $course->id]);
        $delete_form .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        $delete_form .= html_writer::empty_tag('button', ['type' => 'submit', 'class' => 'btn btn-delete', 'title' => get_string('delete_course', 'local_course_manager'), 'name' => 'delete', 'text' => get_string('delete_course', 'local_course_manager')]);
        $delete_form .= html_writer::end_tag('form');

        $rows[] = [$image, $course_link, userdate($course->startdate), $update_link . $delete_form];
    }
    
    $table->data = $rows;
    echo html_writer::table($table);
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');

} else {
    $user_courses = enrol_get_users_courses($USER->id, true, array('id', 'fullname', 'summaryfiles', 'startdate'));

    echo html_writer::start_tag('table', ['class' => 'generaltable']);
    echo html_writer::start_tag('thead');
    echo html_writer::tag('tr', html_writer::tag('th', '#') .
                        html_writer::tag('th', 'Course Image') .
                        html_writer::tag('th', 'Course Name') .
                        html_writer::tag('th', 'Date Created'));
    echo html_writer::end_tag('thead');
    echo html_writer::start_tag('tbody');

    foreach ($user_courses as $course) {
        $image_url = $course->summaryfiles ? $course->summaryfiles : $OUTPUT->image_url('course', 'moodle');
        $image = html_writer::img($image_url, $course->fullname, array('width' => 100));
        $course_link = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)), $course->fullname);
        $rows[] = array($image, $course_link, userdate($course->startdate));
        
    }
    $table->data = $rows;
    echo html_writer::table($table);
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
}

// $courses = $DB->get_records('local_final_courses');

// foreach ($courses as $course) {
//     echo html_writer::start_tag('tr');
//     echo html_writer::tag('td', $course->id);
//     echo html_writer::tag('td', $course->image);
//     echo html_writer::tag('td', $course->name);
//     echo html_writer::tag('td', date('Y-m-d H:i:s', $course->datecreated));
//     echo html_writer::end_tag('tr');
// }

echo $OUTPUT->footer();
