<?php
/**
 * @package    DD_YouTube_Video
 *
 * @author     HR-IT-Solutions Florian Häusler <info@hr-it-solutions.com>
 * @copyright  Copyright (C) 2017 - 2019 HR-IT-Solutions GmbH
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 **/

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.access.access');

jimport('joomla.filesystem.file');

/**
 * Class PlgContentDD_YouTube_Video
 *
 * @since  Version  1.0.0.0
 */
class PlgContentDD_YouTube_Video extends JPlugin
{
	protected $app;

	protected $euprivacy;

	protected $defaultCover;

	protected $coverdiv;

	protected $thumbnailapi;

	protected $thumbnailiamge;

	protected $allowfullscreen;

	protected $auto_width;

	protected $auto_center;

	protected $playbutton;

	protected $autoloadLanguage = true;

	protected $bt_responsiveembed;

	protected $gdpr_text_simple;

	protected $gdpr_lc;

	protected $gdpr_text_on_hover;

	/**
	 * Plugin that place YouTube videos inside an article.
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   object   &$article  The article object.  Note $article->text is also available
	 * @param   mixed    &$params   The article params
	 * @param   integer  $page      The 'page' number
	 *
	 * @return  mixed   true if there is an error. Void otherwise.
	 *
	 * @since   Version  1.0.0.0
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed
		if ($context === 'com_finder.indexer')
		{
			return true;
		}

		// Get plugin parameter
		$this->euprivacy          = (int) $this->params->get('euprivacy');
		$this->defaultCover       = htmlspecialchars($this->params->get('defaultcover'), ENT_QUOTES);
		$this->coverdiv           = (int) $this->params->get('coverdiv');
		$this->thumbnailapi       = (int) ($this->params->get('thumbnailapi'));
		$this->thumbnailiamge     = htmlspecialchars($this->params->get('thumbnailiamge'), ENT_QUOTES);
		$this->allowfullscreen    = (int) $this->params->get('allowfullscreen');
		$this->bt_responsiveembed = (int) $this->params->get('bt_responsiveembed');
		$this->auto_width         = (int) $this->params->get('auto_width');
		$this->auto_center        = (int) $this->params->get('auto_center');
		$this->playbutton         = (int) $this->params->get('playbutton');
		$this->gdpr_text_simple   = htmlspecialchars($this->params->get('gdpr_text_simple'), ENT_QUOTES, 'UTF-8');
		$this->gdpr_lc            = (int) $this->params->get('gdpr_lc');
		$this->gdpr_text_on_hover = (int) $this->params->get('gdpr_text_on_hover');

		JHtml::_('stylesheet', 'plg_content_dd_youtube_video/dd_youtube_video.css', array('version' => 'auto', 'relative' => true));

		// Expression to search for (dd_yt_video)
		$regex = '/{dd_yt_video}(.*?){\/dd}/s';

		// Find all instances
		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

		// Img in htmal and scriptheader
		if ($matches && $this->euprivacy)
		{
			$elementScriptActions = '';

			foreach ($matches as $key => $match)
			{
				$ifram = $this->YouTubeVideoHTML($article->id . $key, $match[1])['iframe'];
				$elementScriptActions .= $this->buildjQueryElementClickEvent($article->id . $key, $ifram);

				$article->text = str_replace($match[0], $this->YouTubeVideoHTML($article->id . $key, $match[1])['img'], $article->text);
			}

			$this->setScriptStyleHeader($elementScriptActions);

		}
		// IFrame in html
		elseif($matches)
		{
			foreach ($matches as $key => $match)
			{
				$article->text = str_replace($match[0], $this->YouTubeVideoHTML($article->id . $key, $match[1])['iframe'], $article->text);
			}
		}
	}

	/**
	 * YouTubeVideoHTML
	 *
	 * @param   int     $matchID  order number
	 * @param   string  $match    the matches string videoid:XXXXX:autoplay:1:control:1 etc...
	 *
	 * @return array returns needed html
	 */
	private function YouTubeVideoHTML($matchID, $match)
	{
		$VideoParams = array();
		$matchParts = explode(':', trim($match, ':'));

		if ($matchParts % 2 == 0)
		{
			$this->throwMessageInvalidSnipped();
		}

		// Build associated arraay $VideoParams Array ( [videoid] => XXXXXXXXXXX [cover] => images/yourimagefile.jpg )
		foreach ($matchParts as $key => $matchPart)
		{
			if ($key % 2 == 0)
			{
				if (isset($matchParts[$key + 1]))
				{
					$VideoParams[$matchPart] = $matchParts[$key + 1];
				}
				else
				{
					$this->throwMessageInvalidSnipped();
				}
			}
		}

		// YouTube VideoID
		if (!isset($VideoParams['videoid']))
		{
			$this->app->enqueueMessage(JText::_('PLG_CONTENT_DD_YOUTUBE_VIDEO_ALERT_VIDEOID_MISSING'), 'warning');
			$VideoParams['videoid'] = '';
		}
		// Cover image path
		if (isset($VideoParams['cover']))
		{
			$imagePath = $VideoParams['cover'];
		}
		else
		{
			$imagePath = $this->defaultCover;
		}

		// Img width attribute
		if (isset($VideoParams['width']))
		{
			$width = $VideoParams['width'];
		}
		else
		{
			$width = 640;
		}

		// Img height attribute
		if (isset($VideoParams['height']))
		{
			$height = $VideoParams['height'];
		}
		else
		{
			$height = 315;
		}

		// Img & iframe class attribute
		if (isset($VideoParams['class']))
		{
			$class = $VideoParams['class'];
		}
		else
		{
			$class = '';
		}

		// YouTube video url params
		$YouTubeParams = $this->buildYouTubeVideoURLParams($VideoParams);

		// GDPR Text
		$gdpr_text = $this->gdpr_text_simple;
		if($gdpr_text || $this->gdpr_lc)
		{
			if($this->gdpr_lc){
				$gdpr_text = JText::_('PLG_CONTENT_DD_YOUTUBE_VIDEO_GDPR_LC');
			}
			$gdpr_text = '<div class="dd_yt_video_gdpr_text">' . $gdpr_text .'</div>';
		}

		// YouTube Thumbnails API
		if ($this->euprivacy && $this->thumbnailapi)
		{
			$this->genertaThumbnailsAPI($VideoParams['videoid']);

			$imagePathAPILoaded = 'images/dd_youtube_video/' . $VideoParams['videoid'] . '/' . $this->thumbnailiamge . '.jpg';

			if (JFile::exists(JPATH_ROOT . '/' .$imagePathAPILoaded))
			{
				$imagePath = JUri::base() . $imagePathAPILoaded;
			}
		}

		// Outer + Auto Classes
		$outerClass = 'dd_yt_video_outer';
		if ($this->auto_width)
		{
			$outerClass .= ' auto_width';
		}
		if ($this->auto_center)
		{
			$outerClass .= ' auto_center';
		}
		if($this->gdpr_text_on_hover){
			$outerClass .= ' on_hover';
		}

		$playbutton = '';
		if($this->playbutton){
			$playbuttonStyle  = 'background-image: url(\'' . JUri::root() . 'media/plg_content_dd_youtube_video/img/playbutton.png' . '\')';
			$playbutton       .= '<div class="dd_yt_video_playbutton" style="' . $playbuttonStyle . '"></div>';
		}

		if ($this->euprivacy && !$this->coverdiv)
		{
			$nocookie = '-nocookie';
			$img = '<div id="dd_yt_video' . $matchID . '" ';
				$img .= 'class="' . $outerClass . '">';
			$img .= '<img src="' . $imagePath . '" width="' . $width . '" height="' . $height . '" class="dd_yt_video ' . $class . '"/>';
			$img .= $gdpr_text . $playbutton;
			$img .=	'</div>';
		}
		else if ($this->euprivacy && $this->coverdiv)
		{
			$nocookie = '-nocookie';
			$img = '<div id="dd_yt_video' . $matchID . '" ';
			$img .='class="' . $outerClass . '" style="background-image: url(\'' . $imagePath . '\'); width: ' . $width . 'px; height:' . $height . 'px;" class="dd_yt_video ' . $class . '">';
			$img .= $gdpr_text . $playbutton;
			$img .= '</div>';
		}
		else
		{
			$nocookie = $img = '';
		}

		// Allow fullscreen
		$allowfullscreen = '';

		if ($this->allowfullscreen)
		{
			$allowfullscreen = ' allowfullscreen';
		}

		$ifram = '<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube' .
			$nocookie . '.com/embed/' . $VideoParams['videoid'] . $YouTubeParams . '" class="dd_yt_video_frame ' . $class . '" ' . $allowfullscreen . '></iframe>';

		if ($this->bt_responsiveembed)
		{
			$ifram = '<div class="embed-responsive embed-responsive-16by9">' . $ifram . '</div>';
		}

		return array("iframe" => $ifram, "img" => $img);

	}

	/**
	 * buildYouTubeVideoURLParams
	 *
	 * @param   array  $VideoParams  youtube video params
	 *
	 * @return string  video paramter url string &autoplay=value&param=value etc...
	 */
	private function buildYouTubeVideoURLParams($VideoParams)
	{
		// Parameter URL
		$paramURL = '?';

		// Autoplay setup
		if ($this->euprivacy)
		{
			$paramURL .= 'autoplay=1';
		}
		elseif(isset($VideoParams['autoplay']))
		{
			$paramURL .= 'autoplay=' . $VideoParams['autoplay'];
		}

		// YouTube possible params without autoplay!
		$ytparams = array(
			'cc_load_policy', 'color', 'controls', 'disablekb',  'enablejsapi', 'end',
			'fs', 'hl',	'iv_load_policy', 'list', 'listType', 'loop', 'modestbranding',
			'origin', 'playlist', 'playsinline', 'rel', 'showinfo', 'start'
		);

		// Parameter seup
		foreach ($VideoParams as $key => $value)
		{
			if (in_array($key, $ytparams))
			{
				$paramURL .= '&' . $key . '=' . $value;
			}
		}

		return $paramURL;
	}

	/**
	 * buildjQueryElementClickEvent
	 *
	 * @param   int     $matchID  order number
	 * @param   string  $iframe   the html iframe snipped
	 *
	 * @return string   the jQuery click event for matchID
	 */
	private function buildjQueryElementClickEvent($matchID, $iframe)
	{
		return '$("#dd_yt_video' . $matchID . ' a, #dd_yt_video' . $matchID . ' button").click(function(e){
                    e.stopPropagation();; });
				$("#dd_yt_video' . $matchID . '").click(function(){
                    $(this).before(\'' . $iframe . '\').remove()
                });';
	}

	/**
	 * setScriptStyleHeader
	 *
	 * @param   string  $elementClickEvents  the jQuery click events for all matchIDs
	 *
	 * @return void
	 */
	private function setScriptStyleHeader($elementClickEvents)
	{
		$doc = JFactory::getDocument();

		$scriptheader = "(function($){ $(document).ready(function() { $elementClickEvents }) })(jQuery);";
		$doc->addScriptDeclaration($scriptheader);

		$doc->addStyleDeclaration('.dd_yt_video { cursor: pointer; }');
	}

	/**
	 * throwMessageInvalidSnipped
	 *
	 * @return void
	 */
	private function throwMessageInvalidSnipped()
	{
		$this->app->enqueueMessage(JText::_('PLG_CONTENT_DD_YOUTUBE_VIDEO_ALERT_INVALID_SNIPPED'), 'warning');
	}

	/**
	 * generateThumbnailsAPI
	 *
	 * @param $video_id
	 * @since 1.0.0.8
	 *
	 * @return void
	 */
	protected function genertaThumbnailsAPI($video_id){
		
		$path = JPATH_SITE . '/images/dd_youtube_video/' . $video_id . '/';
		
		$curl = curl_init();

		$types = array('0', '1', '2', '3',
		               'default', 'sddefault', 'mqdefault', 'hqdefault', 'maxresdefault');

		foreach ($types as $type)
		{
			$thumburl = 'http://img.youtube.com/vi/' . $video_id . '/' . $type . '.jpg';

			curl_setopt($curl, CURLOPT_URL, $thumburl);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$image = curl_exec($curl);
			$info  = curl_getinfo($curl);

			if ($info['http_code'] == 200)
			{
				JFile::write($path . $type . '.jpg', $image);
			}
		}

		curl_close($curl);
	}
}
