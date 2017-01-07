<?php
// This file is part of VdoCipher plugin for Moodle ( moodle-filter_vdocipher ) - https://www.vdocipher.com/
//
// moodle-filter_vdocipher is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// moodle-filter_vdocipher is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with moodle-filter_vdocipher.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   filter_vdocipher
 * @copyright 2017, VdoCipher Media Solutions <info@vdocipher.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configtext('filter_vdocipher/csk',
    get_string('csk', 'filter_vdocipher'),
    get_string('csk_desc', 'filter_vdocipher'), null, PARAM_NOTAGS, 32));

$settings->add(new admin_setting_configtextarea('filter_vdocipher/watermark',
    get_string('watermark', 'filter_vdocipher'),
    get_string('watermark_desc', 'filter_vdocipher'), null, PARAM_NOTAGS, 100, 10));
