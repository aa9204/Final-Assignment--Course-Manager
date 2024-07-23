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
 * Class observer
 *
 * @package    local_final
 * @copyright  2024 Anya <anyama679@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class local_final_observer {
    public static function course_created(\core\event\course_created $event) {
        global $DB;
    
        $data = $event->get_data();
        $courseid = $data['objectid'];
        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    
        $record = new \stdClass();
        $record->id = $course->id;
    
        // Fetch the image URL
        $coursecontext = context_course::instance($course->id);
        $fs = get_file_storage();
        $files = $fs->get_area_files($coursecontext->id, 'course', 'overviewfiles', 0, 'sortorder', false);
        $courseimage = '';
        foreach ($files as $file) {
            if ($file->get_filename() !== '.' && $file->is_image()) {
                $courseimage = $file->get_url();
                break;
            }
        }
        $record->image = $courseimage ? $courseimage : ''; // Default to empty string if no image
    
        $record->name = $course->name;
        $record->datecreated = $course->datecreated;
        $record->visible = $course->visible;
    
        $DB->insert_record('local_final_courses', $record, false, true);
        }
    public static function course_updated(\core\event\course_updated $event) {
        global $DB;

        $data = $event->get_data();
        $courseid = $data['objectid'];

        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

        $record = new \stdClass();
        $record->id = $course->id;
        $record->name = $course->name;
        $record->datecreated = $course->datecreated;
        $record->visible = $course->visible;

        $DB->update_record('local_final_courses', $record, false);
    }

    public static function course_deleted(\core\event\course_deleted $event) {
        global $DB;

        $data = $event->get_data();
        $courseid = $data['objectid'];

        $DB->set_field('local_final_courses', 'visible', 0, ['id' => $courseid]);
    }
}
