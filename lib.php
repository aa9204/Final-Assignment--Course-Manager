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
 * Callback implementations for local_final
 *
 * @package    local_final
 * @copyright  2024 Anya <anyama679@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * Insert a link to index.php on the site front page navigation menu.
  *
  * @param navigation_node $frontpage Node representing the front page in the navigation tree.
  */
function local_final_extend_navigation_frontpage(navigation_node $frontpage) {
    if (!isguestuser()) {
        $frontpage->add(
        get_string('pluginname', 'local_final'),
        new moodle_url('/local/final/view.php'),
        navigation_node::TYPE_CUSTOM,
        );
    }
}
