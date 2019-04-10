# DD_J_plg_content_youtube_video
is a Joomla! content plugin to add YouTube videos inside an article.

[![GPL Licence](https://badges.frapsoft.com/os/gpl/gpl.png?v=102)](https://opensource.org/licenses/GPL-2.0/)

YouTube provides free video-hosting, this plugin allows to simple embed YouTube videos inside articles, in the extended privacy mode.
There are options to define custom covers and playerparameters. By default it is setup for **EU Privacy**. This means the YouTube **extended privacy** mode ('youtube-nocookie' iframe url)
and a special two-click solution to embed the frame not before clicking to your cover. Recommended for Member States of the European Union as well as for other parties to the Agreement on the European Economic Area.
If you don't wish the EU Privacy mode, extended privacy can also be specified at plugin settings. The other settings can be setup for each video through our snipped parameters.

Please take note of the privacy policy of your country. We provide no liability for legal correctness!

For extended usage it is possible to define iframe parameter (width, heigth, class and framborder),
img parameter (width, heigth and class),
as well as any YouTube Player url parameters like (autoplay, size, etc..
https://developers.google.com/youtube/player_parameters)

### YouTube API Thumbnail Generator
You can also enable to store thumbnails from YouTube API directly at your server to be konfirm with privacy.
PHP Extension CURL is required. The Thumbnail API must be anabled and can only be defined at EU Privacy 'ON' mode.

# How to use
#### The simplest way,
insert this snipped into your article:

    {dd_yt_video}videoid:XXXXXXXXXXX{/dd}

Replace ***XXXXXXXXXXX*** with your Video ID<br>
(The video ID is the part between v= and & of the YouTube video URL)


Or with a custom cover image

    {dd_yt_video}videoid:XXXXXXXXXXX:cover:images/yourimagefile.jpg{/dd}

Replace ***images/yourimagefile.jpg*** width your cover image path<br>
(The relative cover image path to your image file at your website)

Note: The attribute value pairs must always be as follows:<br>
attribute:value:attribute:value<br>
Colon is assignment operator as well as separator.

----

#### For extended usge,
you can define your insert snippet as follow:

An example with YouTube Player parameters:

    {dd_yt_video}videoid:XXXXXXXXXXX:cover:images/yourimagefile.jpg:autoplay:1:controls:1{/dd}

An example with iframe and img parameters:

    {dd_yt_video}videoid:XXXXXXXXXXX:cover:images/yourimagefile.jpg:width:640:height:360:class:pull-right{/dd}

Parameters can also be combined. A Special usage has the cover image. You can also omit it from the plugin setting options to use a default cover from plugin setting options.

# System requirements
Joomla 3.x +                                                                                <br>
PHP 5.6.13 or newer is recommended.															<br>
PHP CURL (Only for the YouTube API feature, if enabled at Plugin settings)

# DD_ Namespace
DD_ stands for  **D**idl**d**u e.K. | HR IT-Solutions (Brand recognition)                   <br>
It is a namespace prefix, provided to avoid element name conflicts.

<br>
Author: HR IT-Solutions Florian Häusler https://www.hr-it-solution.com                      <br>
Copyright: (C) 2011 - 2017 Didldu e.K. | HR IT-Solutions                                    <br>
http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only