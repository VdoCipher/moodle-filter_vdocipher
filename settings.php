<?php

$settings->add(new admin_setting_configtext('filter_vdocipher/csk',
	get_string('csk', 'filter_vdocipher'),
	get_string('csk_desc', 'filter_vdocipher'), null, PARAM_NOTAGS, 32));

$settings->add(new admin_setting_configtextarea('filter_vdocipher/watermark',
	get_string('watermark', 'filter_vdocipher'),
	get_string('watermark_desc', 'filter_vdocipher'), null, PARAM_NOTAGS, 100, 10));
