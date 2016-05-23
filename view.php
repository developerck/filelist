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
 * File List
 *
 *
 * @package    local_filelist
 * @category   local
 * @copyright  2016 Chandra Kishor
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once( '../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->dirroot . '/local/filelist/lib.php');
$courseid = required_param('course', PARAM_INT); // Category id

if (!$courseid) {
    redirect(new moodle_url('/local/filelist/index.php'));
}
redirect_if_major_upgrade_required();

$params = array('id' => $courseid);
$course = $DB->get_record('course', $params, '*', MUST_EXIST);
require_login($course);
$context = context_user::instance($USER->id);
$pagetitle = get_string('pageheading_index', 'local_filelist'); 
// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/local/filelist/view.php', $params);
$PAGE->set_pagelayout('course');
$PAGE->blocks->add_region('content');
$PAGE->set_title($pagetitle);
$PAGE->navbar->add($course->shortname, new moodle_url("/local/filelist/index.php"));
$records = local_filelist_file_list($courseid);
echo $OUTPUT->header();
$table = new html_table();
$table->head = array();
$table->head[] = "S.No.";
$table->head[] = "File Name";
$table->head[] = "Download";
$table->id = "filelist";
$cnt = 0;
foreach ($records as $r) {
    $cnt++;
    $row = array();
    $file = $DB->get_record("files", array("id" => $r->fileid));
    $url = moodle_url::make_pluginfile_url(
                    $file->contextid, $file->component, $file->filearea, $file->itemid, $file->filepath, $file->filename, true);
    $row[] = $cnt;
    $row[] = $r->filename;
    $row[] = "<a href='" . $url . "'>Download</a>";
    $table->data[] = $row;
}
echo html_writer::start_tag('div', array('class' => 'no-overflow'));
echo html_writer::table($table);
echo html_writer::end_tag('div');
echo $OUTPUT->footer();
