# moodle
A moodle plugin to add vdocipher videos inside a moodle content


## Installation instructions

1. Download the zip file straight from github.
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
