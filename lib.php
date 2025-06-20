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
 * This file contains general functions for the course format Week
 *
 * @since 2.0
 * @package moodlecore
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Indicates this format uses sections.
 *
 * @return bool Returns true
 */
function callback_qmulweeks_uses_sections() {
    return true;
}

/**
 * Used to display the course structure for a course where format=qmulweeks
 *
 * This is called automatically by {@link load_course()} if the current course
 * format = qmulweeks.
 *
 * @param navigation_node $navigation The course node
 * @param array $path An array of keys to the course node
 * @param stdClass $course The course we are loading the section for
 */
function callback_qmulweeks_load_content(&$navigation, $course, $coursenode) {
    return $navigation->load_generic_course_sections($course, $coursenode, 'qmulweeks');
}

/**
 * The string that is used to describe a section of the course
 * e.g. Topic, Week...
 *
 * @return string
 */
function callback_qmulweeks_definition() {
    return get_string('week');
}

/**
 * The GET argument variable that is used to identify the section being
 * viewed by the user (if there is one)
 *
 * @return string
 */
function callback_qmulweeks_request_key() {
    return 'week';
}

/**
 * Gets the name for the provided section.
 *
 * @param stdClass $course
 * @param stdClass $section
 * @return string
 */
function callback_qmulweeks_get_section_name($course, $section) {
    // We can't add a node without text
    if (!empty($section->name)) {
        // Return the name the user set
        return format_string($section->name, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id)));
    } else if ($section->section == 0) {
        // Return the section0name
        return get_string('section0name', 'format_qmulweeks');
    } else {
        // Got to work out the date of the week so that we can show it
        $sections = get_all_sections($course->id);
        $weekdate = $course->startdate+7200;
        foreach ($sections as $sec) {
            if ($sec->id == $section->id) {
                break;
            } else if ($sec->section != 0) {
                $weekdate += 604800;
            }
        }
        $strftimedateshort = ' '.get_string('strftimedateshort');
        $weekday = userdate($weekdate, $strftimedateshort);
        $endweekday = userdate($weekdate+518400, $strftimedateshort);
        return $weekday.' - '.$endweekday;
    }
}

/**
 * Declares support for course AJAX features
 *
 * @see course_format_ajax_support()
 * @return stdClass
 */
function callback_qmulweeks_ajax_support() {
    $ajaxsupport = new stdClass();
    $ajaxsupport->capable = true;
    $ajaxsupport->testedbrowsers = array('MSIE' => 6.0, 'Gecko' => 20061111, 'Safari' => 531, 'Chrome' => 6.0);
    return $ajaxsupport;
}

/**
 * Returns a URL to arrive directly at a section
 *
 * @param int $courseid The id of the course to get the link for
 * @param int $sectionnum The section number to jump to
 * @return moodle_url
 */
function callback_qmulweeks_get_section_url($courseid, $sectionnum) {
    return new moodle_url('/course/view.php', array('id' => $courseid, 'week' => $sectionnum));
}
