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

namespace local_final;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for Final Assignment plugin 
 *
 * @package    local_final
 * @category   test
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use advanced_testcase;

class local_final_view_test extends advanced_testcase
{

    /**
     * Set up before each test.
     */
    protected function setUp(): void
    {
        $this->resetAfterTest(true);
    }

    /**
     * Test if admin can see all courses.
     */
    public function test_admin_can_see_all_courses()
    {
        global $DB;


        $admin = $this->getDataGenerator()->create_user(['username' => 'admin']);
        $this->getDataGenerator()->role_assign($this->getDataGenerator()->create_role(['shortname' => 'manager']), $admin->id);


        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Log in as admin.
        $this->setUser($admin);


        $output = $this->capture_view_output();

        // Verify that both courses are displayed.
        $this->assertStringContainsString($course1->fullname, $output);
        $this->assertStringContainsString($course2->fullname, $output);
    }

    /**
     * Test if student can see only enrolled courses.
     */
    public function test_student_can_see_only_enrolled_courses()
    {
        global $DB;


        $student = $this->getDataGenerator()->create_user(['username' => 'student']);
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);


        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Enroll student in course1.
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentrole->id);


        $this->setUser($student);

        $output = $this->capture_view_output();

        // Verify that only course1 is displayed.
        $this->assertStringContainsString($course1->fullname, $output);
        $this->assertStringNotContainsString($course2->fullname, $output);
    }

    /**
     * Helper function to capture the output of the view page.
     *
     * @return string The captured output.
     */
    protected function capture_view_output()
    {
        ob_start();
        include($CFG->dirroot . '/local/final/view.php');
        return ob_get_clean();
    }
}
