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

// Guest access check.
require_login();
if (isguestuser()) {
    throw new moodle_exception('You cannot access this page');
}

$managecourse = has_capability('local/final:managecourses', $context);


echo $OUTPUT->header();

if ($managecourse) {
    echo html_writer::link(
        new moodle_url('/course/edit.php'),
        get_string('create_course', 'local_final'),
        ['class' => 'btn btn-primary', 'title' => get_string('create_course', 'local_final')]
    );
}

// Get courses based on user role.
global $DB, $USER;
$courses = [];
if ($managecourse) {
    // Admin or users with the capability to manage courses can see all courses.
    $sql = "SELECT c.id, c.fullname AS name, c.timecreated AS datecreated
            FROM {course} c
            WHERE c.visible = 1
            ORDER BY c.timecreated DESC";
    $courses = $DB->get_records_sql($sql);
} else {
    // Students can only see courses they are enrolled in.
    $sql = "SELECT c.id, c.fullname AS name, c.timecreated AS datecreated
            FROM {course} c
            JOIN {enrol} e ON e.courseid = c.id
            JOIN {user_enrolments} ue ON ue.enrolid = e.id
            WHERE ue.userid = :userid AND c.visible = 1
            ORDER BY c.timecreated DESC";
    $courses = $DB->get_records_sql($sql, ['userid' => $USER->id]);
}


echo html_writer::start_tag('table', ['class' => 'generaltable']);
echo html_writer::start_tag('thead');
echo html_writer::tag(
    'tr',
    html_writer::tag('th', '#') .
        html_writer::tag('th', get_string('course_image', 'local_final')) .
        html_writer::tag('th', get_string('course_name', 'local_final')) .
        html_writer::tag('th', get_string('date_created', 'local_final')) .
        ($managecourse ? html_writer::tag('th', get_string('actions', 'local_final')) : '')
);
echo html_writer::end_tag('thead');
echo html_writer::start_tag('tbody');

foreach ($courses as $course) {
    $defaultcourseimageurl = $OUTPUT->image_url('course', 'moodle');
    $courseimage = html_writer::img($defaultcourseimageurl, get_string('course_image', 'local_final'), ['width' => 100]);

    $fs = get_file_storage();
    $files = $fs->get_area_files(context_course::instance($course->id)->id, 'course', 'overviewfiles', false, 'itemid, filepath,
        filename', false);

    if ($files) {
        foreach ($files as $file) {
            if ($file->is_valid_image()) {
                $courseimageurl = moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    $file->get_itemid(),
                    $file->get_filepath(),
                    $file->get_filename()
                );
                $courseimage = html_writer::img($courseimageurl, $course->name, ['width' => 100]);
                break;
            }
        }
    }

    echo html_writer::start_tag('tr');
    echo html_writer::tag('td', $course->id);
    echo html_writer::tag('td', $courseimage);
    echo html_writer::tag('td', html_writer::link(
        new moodle_url('/course/view.php', ['id' => $course->id]),
        $course->name
    ));
    echo html_writer::tag('td', userdate($course->datecreated));

    if ($managecourse) {
        echo html_writer::start_tag('td', ['class' => 'table-actions']);
        echo html_writer::link(new moodle_url('/course/edit.php', ['id' => $course->id]), get_string(
            'update_course',
            'local_final'
        ), ['class' => 'btn btn-update', 'title' => get_string('update_course', 'local_final')]);
        echo html_writer::start_tag('form', [
            'action' => new moodle_url('/local/final/delete.php'),
            'method' => 'post', 'style' => 'display:inline;'
        ]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $course->id]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo html_writer::empty_tag('button', ['type' => 'submit', 'class' => 'btn btn-delete', 'title' => get_string(
            'delete_course',
            'local_final'
        ), 'name' => 'delete', 'text' => get_string('delete_course', 'local_final')]);
        echo html_writer::end_tag('form');
        echo html_writer::link(new moodle_url('/local/final/delete.php', ['id' => $course->id]), get_string(
            'delete_course',
            'local_final'
        ), ['class' => 'btn btn-delete', 'title' => get_string('delete_course', 'local_final')]);
        echo html_writer::end_tag('td');
    }

    echo html_writer::end_tag('tr');
}
echo html_writer::end_tag('tbody');
echo $OUTPUT->footer();
