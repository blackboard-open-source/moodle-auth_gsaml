<?php

/**
* Copyright (C) 2009  Moodlerooms Inc.
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see http://opensource.org/licenses/gpl-3.0.html.
*
* @copyright  Copyright (c) 2009 Moodlerooms Inc. (http://www.moodlerooms.com)
* @license    http://opensource.org/licenses/gpl-3.0.html     GNU Public License
* @author Chris Stones
* @version $Id$
* @package auth_saml
**/


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
 * Manage files in folder in private area - to be replaced by something better hopefully....
 *
 * @package   block_private_files
 * @copyright 2010 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/blocks/private_files/edit_form.php");
require_once("$CFG->dirroot/repository/lib.php");

require_login();
if (isguestuser()) {
    die();
}
//TODO: add capability check here!

global $CFG;
// The system as admin
//http://docs.moodle.org/en/Development:Using_the_file_API

//$context = get_context_instance(CONTEXT_SYSTEM);
$context = get_context_instance(CONTEXT_USER, $USER->id);

$PAGE->set_url('/auth/gsaml/uploads.php');

require_once($CFG->dirroot.'/auth/gsaml/uploads_form.php');

$data = new object();
$options = array('subdirs'=>1, 'maxbytes'=>$CFG->userquota, 'maxfiles'=>-1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);
file_prepare_standard_filemanager($data, 'files', $options, $context, 'user', 'private', 0);

$mform = new auth_gsaml_uploads_form(null, array('data'=>$data, 'options'=>$options));

$formdata = '';
$privatekey = '';
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php/',array('section' => 'authsettinggsaml') ));

} else if ($formdata = $mform->get_data()) {


    // Save the file contents
    //$privatekey = $mform->get_file_content('privatekey');


    // For later use new file system
    //$mform->save_stored_file('privatekey', $newcontextid, $newcomponent, $newfilearea, $newitemid, $newfilepath='/',
    //                          $newfilename=null, $overwrite=false, $newuserid=null);

    // for now save file to standard path
    $mform->save_file('privatekey', $CFG->dataroot.'/samlkeys/privatekey.pem', true);
    $mform->save_file('certificate', $CFG->dataroot.'/samlkeys/certificate.pem', true);

    // save the file contents
    //$certificate = $mform->get_file_content('certificate');

    redirect(new moodle_url('/admin/settings.php/',array('section' => 'authsettinggsaml') ));
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
