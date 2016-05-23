<?php

require_once( '../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->dirroot . '/local/filelist/lib.php');

redirect_if_major_upgrade_required();
require_login();
$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect(new moodle_url('/admin/index.php'));
}
$context = context_user::instance($USER->id);
$pagetitle = get_string('pageheading_index', 'local_filelist'); 
// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/local/filelist/index.php', $params);
$PAGE->set_pagelayout('course');
$PAGE->set_pagetype('course-index-category');
$PAGE->blocks->add_region('content');
$PAGE->set_title($pagetitle);
$courserenderer = $PAGE->get_renderer('core', 'course');
$table = new html_table();
$table->head = array();
$table->head[] = "Course";
$table->id = "filelist";
$cnt = 0;
echo $OUTPUT->header();
$student_course_arry = enrol_get_users_courses($USER->id, true, Null, 'visible DESC,sortorder ASC');

foreach ($student_course_arry as $value) {
    $cnt++;
    $row = array();
    $url = new moodle_url('/local/filelist/view.php', array("course" => $value->id));
    $row[] = "<a href='" . $url->out() . "'>" . "$value->fullname" . "</a>";
    $table->data[] = $row;
}
echo html_writer::start_tag('div', array('class' => 'no-overflow'));
echo html_writer::table($table);
echo html_writer::end_tag('div');
echo $OUTPUT->footer();
