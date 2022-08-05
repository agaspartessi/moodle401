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
 * gflacso module upgrade
 *
 * @package     mod_gflacsoslider
 * @copyright   2016 FLACSO & Cooperativa de trabajo GENEOS (www.geneos.com.ar}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This file keeps track of upgrades to
// the extendedlabel module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

defined('MOODLE_INTERNAL') || die;

function xmldb_gflacsoslider_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016102400) {

        // Define table gflacsoslider_slides to be created.
        $table = new xmldb_table('gflacsoslider_slides');

        // Adding fields to table gflacsoslider_slides.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('gflacsoslider', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table gflacsoslider_slides.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('gflacsoslider', XMLDB_KEY_FOREIGN, array('gflacsoslider'), 'gflacsoslider', array('id'));

        // Conditionally launch create table for gflacsoslider_slides.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Gflacsoslider savepoint reached.
        upgrade_mod_savepoint(true, 2016102400, 'gflacsoslider');
    }

     if ($oldversion < 2016102600) {

        // Define field bannernumber to be added to gflacsoslider_slides.
        $table = new xmldb_table('gflacsoslider_slides');
        
        $field = new xmldb_field('bannerimage', XMLDB_TYPE_CHAR, '100', null, null, null, null, '0');
        // Conditionally launch add field bannernumber.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Gflacsoslider savepoint reached.
        upgrade_mod_savepoint(true, 2016102600, 'gflacsoslider');
    }


    if ($oldversion < 2016102800) {

        // Define field bannercolor to be added to gflacsoslider_slides.
        $table = new xmldb_table('gflacsoslider_slides');
        
        $field = new xmldb_field('bannercolor', XMLDB_TYPE_CHAR, '7', null, null, null, null, 'bannernumber');
        // Conditionally launch add field bannercolor.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('bannertitlecolor', XMLDB_TYPE_CHAR, '7', null, XMLDB_NOTNULL, null, null, 'bannertitlesize');
        // Conditionally launch add field bannertitlecolor.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('bannertextcolor', XMLDB_TYPE_CHAR, '7', null, XMLDB_NOTNULL, null, null, 'bannertextsize');
        // Conditionally launch add field bannertextcolor.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('bannervideo', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'bannerlinkurltext');
        // Conditionally launch add field bannervideo.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('bannervideotype', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1, 'bannervideo');
        // Conditionally launch add field bannervideotype.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('bannervideoautoplay', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'bannervideotype');
        // Conditionally launch add field bannervideoautoplay.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Gflacsoslider savepoint reached.
        upgrade_mod_savepoint(true, 2016102800, 'gflacsoslider');
    }

    if ($oldversion < 2016103000) {

        // Define field bannercolorselect to be added to gflacsoslider_slides.
        $table = new xmldb_table('gflacsoslider_slides');

        $field = new xmldb_field('bannercolorselect', XMLDB_TYPE_INTEGER, '2', null, null, null, '0', 'bannercolor');
        // Conditionally launch add field bannercolorselect.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('bannertitleselect', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'bannertitlecolor');
        // Conditionally launch add field bannertitleselect.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('bannertextcolorselect', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'bannertextcolor');

        // Conditionally launch add field bannertextcolorselect.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Gflacsoslider savepoint reached.
        upgrade_mod_savepoint(true, 2016103000, 'gflacsoslider');
    }

    if ($oldversion < 2017070100) {

       // Define field bannerimage to be added to gflacsoslider_slides.
        $table = new xmldb_table('gflacsoslider_slides');
        
        $field = new xmldb_field('bannerimage', XMLDB_TYPE_CHAR, '100', null, null, null, null, '0');
        // Conditionally launch add field bannernumber.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Gflacsoslider savepoint reached.
        upgrade_mod_savepoint(true, 2017070100, 'gflacsoslider');
    }


    return true;
}


