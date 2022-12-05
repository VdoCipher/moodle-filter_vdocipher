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


defined('MOODLE_INTERNAL') || die();

// Language string for filter/vdocipher.

$string['filtername'] = 'VdoCipher';

$string['csk'] = 'API Secret Key';
$string['csk_desc'] = 'This is the secret key received from vdocipher. Check your account settings to obtain.';

$string['width'] = 'Player Width';
$string['width_desc'] = '';

$string['height'] = 'Player Height';
$string['height_desc'] = 'Setting Height to auto preserves the video aspect ratio';

$string['playerVersion'] = 'Player version';
$string['playerVersion_desc'] = 'Setting to 1.x uses the latest and recommended video player version';

$string['playerTheme'] = 'Player Theme';
$string['playerTheme_desc'] = 'This is the player theme.';

$string['speedOptions'] = 'Speed options';
$string['speedOptions_desc'] = 'Change the player menu for speed. Accepts comma-separated string with each value between 0.2 to 2.5. Can cause error if invalid.';

$string['watermark'] = 'Watermark JSON';
$string['watermark_desc'] = '(Optional) Watermark to be applied to the videos. For details on writing the annotation code <a href="https://www.vdocipher.com/blog/2014/12/add-text-to-videos-with-watermark/" target="_blank"> check this out. </a>';

