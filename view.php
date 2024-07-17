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

    echo html_writer::link(
        new moodle_url('/course/edit.php'),
    get_string('create_course', 'local_final'),
    ['class' => 'btn btn-primary', 'title' => get_string('create_course', 'local_final_manager')]
    );

    $sql = "SELECT m.id, m.image, m.name, m.datecreated {$userfieldssql->selects}
    FROM {local_final_courses} m
    ORDER BY datecreated DESC";

    $courses = $DB->get_records_sql($sql);
    echo html_writer::start_tag('table', ['class' => 'generaltable']);
    echo html_writer::start_tag('thead');
    echo html_writer::tag('tr', html_writer::tag('th', '#') .
                        html_writer::tag('th', 'Course Image') .
                        html_writer::tag('th', 'Course Name') .
                        html_writer::tag('th', 'Date Created'));
    echo html_writer::end_tag('thead');
    echo html_writer::start_tag('tbody');

    foreach ($courses as $course) {
        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', html_writer::img($course->image ? $course->image : $OUTPUT->image_url('course', 'moodle'),
         $course->fullname, ['width' => 100]));
        echo html_writer::tag('td', html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]),
        $course->fullname));
        echo html_writer::tag('td', userdate($course->startdate));
        echo html_writer::tag('td',
            html_writer::link(new moodle_url('/course/edit.php', ['id' => $course->id]), get_string('update_course',
            'local_course_manager'), ['class' => 'btn btn-update',
            'title' => get_string('update_course', 'local_course_manager')]) .
            html_writer::start_tag('form', ['action' => new moodle_url('/local/course_manager/delete.php'),
            'method' => 'post', 'style' => 'display:inline;']) .
            html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $course->id]) .
            html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]) .
            html_writer::empty_tag('button', ['type' => 'submit', 'class' => 'btn btn-delete', 'title' => get_string(
                'delete_course', 'local_course_manager'), 'name' => 'delete',
                'text' => get_string('delete_course', 'local_course_manager')]) .
            html_writer::end_tag('form')
        );
        echo html_writer::end_tag('tr');
    }
    echo html_writer::end_tag('tbody');
    echo $OUTPUT->footer();
