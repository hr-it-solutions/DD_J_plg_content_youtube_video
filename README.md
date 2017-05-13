# DD_J_plg_content_youtube_video
is a Joomla! content plugin to add YouTube videos inside an article.

[![GPL Licence](https://badges.frapsoft.com/os/gpl/gpl.png?v=102)](https://opensource.org/licenses/GPL-2.0/)

YouTube provides free video-hosting, this plugin allows to simple embed YouTube videos inside articles, in the extended privacy mode.
There are options to define custom covers and playerparameters. By default it is setup for **EU Privacy**. This means the YouTube **extended privacy** mode ('youtube-nocookie' iframe url)
and a special two-click solution to embed the frame not before clicking to your cover. Recommended for Member States of the European Union as well as for other parties to the Agreement on the European Economic Area.
If you don't wish the EU Privacy mode, extended privacy can also be specified at plugin settings. The other settings can be setup for each video through our snipped parameters.

Please take note of the privacy policy of your country. We provide no liability for legal correctness!

For extended usage it is possible to define iframe parameter (width, higth and framborder),
as well as any YouTube Player url parameters like autoplay, size, etc..
https://developers.google.com/youtube/player_parameters

# How to use
####The simplest way,
insert this snipped into your article:

    {dd_yt_video}videoid:XXXXXXXXXXX:cover:images/yourimagefile.jpg{/dd}

Just two parameters needed:
- The video ID (The video ID is the part between v= and & of the video URL)
- The cover image path (The cover image path to your image file at your website)

Replace ***XXXXXXXXXXX*** with your Video ID and
replace ***images/yourimagefile.jpg*** width your cover image path

----

####For extended usge,
you can define your own insert snippet:

An example with YouTube Player parameters:

    {dd_yt_video}videoid:XXXXXXXXXXX:cover:images/yourimagefile.jpg:autoplay:1:control:1{/dd}

An example with iframe parameters:

    {dd_yt_video}videoid:XXXXXXXXXXX:cover:images/yourimagefile.jpg:width:640:height:360{/dd}

Parameters can also be combined. A Special usage has the cover image. You can also omit it from the plugin setting options to use a default cover from plugin setting options.

# System requirements
Joomla 3.x +                                                                                <br>
PHP 5.6.13 or newer is recommended.

# DD_ Namespace
DD_ stands for  **D**idl**d**u e.K. | HR IT-Solutions (Brand recognition)                   <br>
It is a namespace prefix, provided to avoid element name conflicts.

<br>
Author: HR IT-Solutions Florian Häusler https://www.hr-it-solution.com                      <br>
Copyright: (C) 2011 - 2017 Didldu e.K. | HR IT-Solutions                                    <br>
http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only