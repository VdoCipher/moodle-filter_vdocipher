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
$string['csk_desc'] = '';

$string['width'] = 'Default Width';
$string['width_desc'] = '';

$string['height'] = 'Default Height';
$string['height_desc'] = 'Can be either "auto" or a number. Set to "auto" height and max width for responsive layout.';

$string['playerVersion'] = 'Player Version';
$string['playerVersion_desc'] =
'<details class="vdo_docs">
    <summary>How to choose player version?</summary>
    <div>
        <p>
            The new version of the player is v2. It is the second version of our HTML5 player. There are many improvements to player including
        </p>
        <ul>
            <li>Smaller file size resulting in faster load times</li>
            <li>Improved layout on small screen devices</li>
            <li>Work better with webpage styles</li>
            <li>Improved progress bar function with drag function</li>
            <li>Auto resume from last watched position</li>
            <li>Greater customisation capabilities, chapters, playlists and other features coming soon.</li>
        </ul>
        <p>We will continue to support v1 player without any planned end-of-support. We will continue to make security and maintenance fixes to ensure that v1 plays across browsers as much as possible.</p>
        <p>The looks of v2 is noticeably different, and we wanted to give you the choice in making the transition at your convenience. If you have to choose, we recommend to use player v2 because of all the improvements.</p>
        <p>If you have any questions or feedback or feature suggestions about the new player versions, we love to hear about it. Go to the support section on the sidebar and send us a ticket.</p>
    </div>
</details>
<br>';

$string['playerTheme'] = 'Player ID';
$string['playerTheme_desc'] = 'Leave this blank for using default theme. Player id can also be specified in the shortcode if needed to be different for different videos.';

$string['watermark'] = 'Watermark Statement';
$string['watermark_desc'] = '(Optional) Watermark to be applied to the videos. For details on writing the annotation code <a href="https://www.vdocipher.com/blog/2014/12/add-text-to-videos-with-watermark/" target="_blank"> check this out. </a>
          <p class="description" style="margin-left:20px; position: relative">
          <span style="color:purple"><b>Sample Code for Dynamic Watermark</b></span><br/>
          [{\'type\':\'rtext\', \'text\':\' {name}\', \'alpha\':\'0.60\', \'color\':\'0xFF0000\',\'size\':\'15\',\'interval\':\'5000\'}] <br/>
          <span style="color:purple"><b>Sample Code for Static Watermark</b></span><br/>
          [{\'type\':\'text\', \'text\':\'{ip}\', \'alpha\':\'0.5\' , \'x\':\'10\', \'y\':\'100\', \'color\':\'0xFF0000\', \'size\':\'12\'}] <br/>
          </p>
          </div>
          <p class="description" id="vdojsonvalidator"></p>
          <p class="description">
                Leave this text blank in case you do not need watermark over all
                videos.
          </p>
';
