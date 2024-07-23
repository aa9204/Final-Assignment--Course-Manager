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
 * TODO describe file delete
 *
 * @package    local_final
 * @copyright  2024 Anya <anyama679@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$id = optional_param('id', 0, PARAM_INT);
$context = context_system::instance();
require_login();

if (!has_capability('local/final:managecourses', $context)) {
    throw new moodle_exception('You cannot access this page');
}

// Check if the course ID is valid
if ($id <= 0) {
    throw new moodle_exception('invalidcourseid', 'local_final');
}

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

$course->visible = !$course->visible;
$DB->update_record('course', $course);

redirect(new moodle_url('/local/final/view.php'), get_string('course_visibility_updated', 'local_final'));
