# moodle-filter_vdocipher
A moodle filter plugin to add download-protected videos from vdocipher inside any moodle content. This replaces shortcodes with embedded video player.

You shall get an API key and and ids for each video from your vdocipher dashboard. This can be used to embed your videos inside any moodle content such as a page or an activity.

**About VdoCipher:** VdoCipher is an online video streaming service for premium content. VdoCipher Video Plugin for Moodle ensures highest protection against content piracy. Encryption, Watermarking & Backend Authentication ensures that no downloader or plugin can download videos embedded using VdoCipher. This enables our customers to earn maximum revenues from their customers.  The video plugin is easy to use and you can integrate and start streaming in 10 minutes. This makes it the ideal choice for easily hosting premium video content like lecture vidoes, music or movies. VdoCipher provides a nice smooth moodle embed video player to have your viewers best video streaming experience. Multiple bitrates can be allowed on the moodle video player.


## Installation instructions

1. Download the zip file.
2. Login to your moodle dashboard
3. Go to "Site-administration"
4. Under the plugin section, choose *Install plugins*
5. Upload the zip file that you downloaded in step 1
6. You should be taken to the configuration page where you can enter the API Secret. (Copy this from the config section of your vdocipher dashboard) and save the changes. Leave watermark field blank for now.
7. Under plugin section, go to *filters* and then *manage filters*
8. Here change the dropdown across *VdoCipher* to **On** and save changes.
9. Go to a page editor and paste this shortcode: `[vdo id="__________"]` . Replace the blank field with video id from your vdocipher dashboard. It is a 32 character alphanumeric string.
10. Save and display!! You should be able to see the video player.

## Setting height and width

By default, the video player takes 1280X720 limited by local responsive styles. You can change the default height and width as extra attributes in your shortcode. Example:

```
[vdo id="_________" height="360" width="640"]
```


## Setting user-specific watermark

While adding videos watermark or annotation code on vdocipher videos using the Moodle plugin, you can configure the text to fetch dynamic user variables. These variables can be user name, email or date in custom format. Here is the complete list of variables which can be used in the annotation code.

- `{name}` : This shows the display name of the user who is viewing the video using global $USER.
- `{email}`
- `{username}`
- `{id}`
- `{ip}` : `$_SERVER[‘REMOTE_ADDR’]`
- `{date}` : This function allows you to configure the video to show date in custom format. You can use the predefined PHP date constants to display date. The format is `{date.pattern}`, where pattern will be the input of PHP’s date function.

An example code using the above variable tokens:

```
[
{'type':'rtext', 'text':'Your IP : {ip}', 'alpha':'0.8', 'color':'0xFF0000','size':'12','interval':'5000'},
{'type':'text', 'text':'Time: {date.h:i:s A}', 'alpha':'0.5' , 'x':'150', 'y':'100', 'color':'0xFF0000', 'size':'12'}
]

```
[ Check example video for the above code here ]( https://www.vdocipher.com/blog/2014/12/add-text-to-videos-with-watermark/ )
[Add player themes ]( https://www.vdocipher.com/blog/2018/10/video-player-themes/ )

## Support

Send an email to [support@vdocipher.com](mailto:support@vdocipher.com )


## Supported moodle versions

2.7 (2014051200) and later

- tested upto 4.0

## License

GPL-3
