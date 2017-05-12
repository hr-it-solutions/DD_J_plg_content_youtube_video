# DD_J_plg_content_youtube_video
is a Joomla! content pluginn to add YouTube videos inside an article.

[![GPL Licence](https://badges.frapsoft.com/os/gpl/gpl.png?v=102)](https://opensource.org/licenses/GPL-2.0/)

YouTube provides free video-hosting, this plugin allows you to simple embed YouTube videos inside articles in the extended privacy mode.
By default it is setup for **EU Privacy**. This means the YouTube **extended privacy** mode ('youtube-nocookie' iframe url)
and a spcial two-click solution to embed the frame not bevore clicking to your cover.
Recommended for Member States of the European Union as well as for other parties to the Agreement on the European Economic Area.
If you dont wish the EU Privacy mode, extended privacy can also be specified at plugin settings.

Please take note of the privacy policy of your country. We privde no liability for legal correctness!

# How to use
The simplest way, insert our snipped into article:

    {dd_yt_video}videoid:XXXXXXXXXXX:cover:images/yourimagefile.jpg{/dd}

Just two parameters needed:
- The video ID (The video ID is the part between v= and & of the video URL)
- The cover image path (The cover image path to your image file at your website)

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