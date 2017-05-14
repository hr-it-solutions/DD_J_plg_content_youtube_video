<?php
/**
 * @package    DD_YouTube_Video
 *
 * @author     HR IT-Solutions Florian Häusler <info@hr-it-solutions.com>
 * @copyright  Copyright (C) 2017 - 2017 Didldu e.K. | HR IT-Solutions
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 **/

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.access.access');

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

	protected $autoloadLanguage = true;

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
		$this->euprivacy  = (int) $this->params->get('euprivacy');
		$this->defaultCover  = htmlspecialchars($this->params->get('defaultcover'), ENT_QUOTES);

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
				$ifram = $this->YouTubeVideoHTML($key, $match[1])['iframe'];
				$elementScriptActions .= $this->buildjQueryElementClickEvent($key, $ifram);

				$article->text = str_replace($match[0], $this->YouTubeVideoHTML($key, $match[1])['img'], $article->text);
			}

			$this->setJavaScriptHeader($elementScriptActions);

		}
		// IFrame in html
		elseif($matches)
		{
			foreach ($matches as $key => $match)
			{
				$article->text = str_replace($match[0], $this->YouTubeVideoHTML($key, $match[1])['iframe'], $article->text);
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
			$class = 'class = "' . $VideoParams['class'] . '"';
		}
		else
		{
			$class = '';
		}

		// YouTube video url params
		$YouTubeParams = $this->buildYouTubeVideoURLParams($VideoParams);

		if ($this->euprivacy)
		{
			$nocookie = '-nocookie';
			$img = '<img id="dd_youtube_video' . $matchID . '" src="' . $imagePath . '" width="' . $width . '" height="' . $height . '" ' . $class . '/>';
		}
		else
		{
			$nocookie = $img = '';
		}

		$ifram = '<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube' .
			$nocookie . '.com/embed/' . $VideoParams['videoid'] . $YouTubeParams . '" ' . $class . '></iframe>';

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
		return '$("#dd_youtube_video' . $matchID . '").click(function(){
                    $(this).before(\'' . $iframe . '\').remove()
                });';
	}

	/**
	 * setJavaScriptHeader
	 *
	 * @param   string  $elementClickEvents  the jQuery click events for all matchIDs
	 *
	 * @return void
	 */
	private function setJavaScriptHeader($elementClickEvents)
	{
		$scriptheader = "(function($){ $(document).ready(function() { $elementClickEvents }) })(jQuery);";
		JFactory::getDocument()->addScriptDeclaration($scriptheader);
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
}
