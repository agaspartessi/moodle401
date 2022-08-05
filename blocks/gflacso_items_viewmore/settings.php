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
 * gflacso_items_viewmore block settings
 *
 * @package    block_gflacso_items_viewmore
 * @copyright  Cooperativa GENEOS <info@geneos.com.ar>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_gflacso_items_viewmore/maxnumitems', new lang_string('maxnumitems', 'block_gflacso_items_viewmore'),
        new lang_string('maxnumitemsdesc', 'block_gflacso_items_viewmore'), 50, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_gflacso_items_viewmore/maxadditionaltextitems', new lang_string('maxadditionaltextitems', 'block_gflacso_items_viewmore'),
        new lang_string('maxadditionaltextitemsdesc', 'block_gflacso_items_viewmore'), 1, PARAM_INT));
}
