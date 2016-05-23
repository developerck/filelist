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
 * Library
 *
 *
 * @package    local_filelist
 * @category   local
 * @copyright  2016 Chandra Kishor
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function local_filelist_extend_navigation(global_navigation $navigation, context $context = null) {
    global $DB, $PAGE;

    $nodeproperties = array(
        'text' => "File List",
        'shorttext' => "File List",
        'action' => new moodle_url('/local/filelist/index.php')
    );

    $integrationnode = new navigation_node($nodeproperties);

    $navigation->add_node($integrationnode);
    return true;
}

function local_filelist_extends_navigation(global_navigation $navigation, context $context = null) {
    global $DB, $PAGE;

    $nodeproperties = array(
        'text' => "File List",
        'shorttext' => "File List",
        'action' => new moodle_url('/local/filelist/index.php')
    );

    $integrationnode = new navigation_node($nodeproperties);

    $navigation->add_node($integrationnode);
    return true;
}
/**
 * Provide File List by course id
 * 
 * @global type $DB
 * @param number $courseid
 * @return array
 */
function local_filelist_file_list($courseid) {
    global $DB;
    $result = array();
    $query = "SELECT fileid, course.id AS courseid, course.fullname AS CourseFullName, course.shortname AS CourseShortName, course.filename, course.filesize AS CourseSizeBytes
FROM (

SELECT f.id as fileid, c.id, c.fullname, c.shortname, cx.contextlevel,f.component, f.filearea, f.filename, f.filesize
FROM {context} cx
JOIN {course} c ON cx.instanceid=c.id
JOIN {files} f ON cx.id=f.contextid
WHERE f.filename <> '.'
AND f.component NOT IN ('private', 'automated', 'backup','draft')

UNION

SELECT f.id as fileid, cm.course, c.fullname, c.shortname, cx.contextlevel,f.component, f.filearea, f.filename, f.filesize
FROM {files} f
JOIN {context} cx ON f.contextid = cx.id
JOIN {course_modules} cm ON cx.instanceid=cm.id
JOIN {course} c ON cm.course=c.id
WHERE filename <> '.'

UNION

SELECT f.id as fileid,c.id, c.shortname, c.fullname, cx.contextlevel, f.component, f.filearea, f.filename, f.filesize
from {block_instances} bi
join {context} cx on (cx.contextlevel=80 and bi.id = cx.instanceid)
join {files} f on (cx.id = f.contextid)
join {context} pcx on (bi.parentcontextid = pcx.id)
join {course} c on (pcx.instanceid = c.id)
where filename <> '.' 
) AS course where id =?";
    $params = array('id' => $courseid);
    try {
        $result = $DB->get_records_sql($query, $params);
    } catch (Exception $ex) {
        // nothing to do
    }
    return $result;
}
