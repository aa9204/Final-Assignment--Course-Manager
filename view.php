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

require('../../config.php');

require_login();

$url = new moodle_url('/local/final/view.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_heading($SITE->fullname);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_final'));

$PAGE->set_heading(get_string('pluginname', 'local_final'));

echo $OUTPUT->header();


echo html_writer::start_tag('table', ['class' => 'generaltable']);
echo html_writer::start_tag('thead');
echo html_writer::tag('tr', html_writer::tag('th', '#') .
                        html_writer::tag('th', 'Course Image') .
                        html_writer::tag('th', 'Course Name') .
                        html_writer::tag('th', 'Date Created'));
echo html_writer::end_tag('thead');
echo html_writer::start_tag('tbody');

$courses = $DB->get_records('local_final_courses');

foreach ($courses as $course) {
    echo html_writer::start_tag('tr');
    echo html_writer::tag('td', $course->id);
    echo html_writer::tag('td', $course->image);
    echo html_writer::tag('td', $course->name);
    echo html_writer::tag('td', date('Y-m-d H:i:s', $course->datecreated));
    echo html_writer::end_tag('tr');
}

echo html_writer::end_tag('tbody');
echo html_writer::end_tag('table');


echo $OUTPUT->footer();
