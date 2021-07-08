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
 * @copyright 2019, VdoCipher Media Solutions <info@vdocipher.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configpasswordunmask(
    'filter_vdocipher/csk',
    get_string('csk', 'filter_vdocipher'),
    get_string('csk_desc', 'filter_vdocipher'),
    null
));

$settings->add(new admin_setting_configtext(
    'filter_vdocipher/width',
    get_string('width', 'filter_vdocipher'),
    get_string('width_desc', 'filter_vdocipher'),
    '720',
    PARAM_NOTAGS,
    32
));

$settings->add(new admin_setting_configtext(
    'filter_vdocipher/height',
    get_string('height', 'filter_vdocipher'),
    get_string('height_desc', 'filter_vdocipher'),
    'auto',
    PARAM_NOTAGS,
    32
));

$settings->add(new admin_setting_configtext(
    'filter_vdocipher/playerVersion',
    get_string('playerVersion', 'filter_vdocipher'),
    get_string('playerVersion_desc', 'filter_vdocipher'),
    '1.x',
    PARAM_NOTAGS,
    32
));

$settings->add(new admin_setting_configtext(
    'filter_vdocipher/speedOptions',
    get_string('speedOptions', 'filter_vdocipher'),
    get_string('speedOptions_desc', 'filter_vdocipher'),
    '',
    PARAM_NOTAGS,
    32
));

$settings->add(new admin_setting_configtext(
    'filter_vdocipher/playerTheme',
    get_string('playerTheme', 'filter_vdocipher'),
    get_string('playerTheme_desc', 'filter_vdocipher'),
    '9ae8bbe8dd964ddc9bdb932cca1cb59a',
    PARAM_NOTAGS,
    64
));

$settings->add(new admin_setting_configtextarea(
    'filter_vdocipher/watermark',
    get_string('watermark', 'filter_vdocipher'),
    get_string('watermark_desc', 'filter_vdocipher'),
    null,
    PARAM_NOTAGS,
    100,
    10
));
