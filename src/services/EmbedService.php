<?php

namespace vaersaagod\toolmate\services;

use Craft;
use craft\base\Component;
use craft\helpers\Template;
use Embed\Embed;

/**
 * Embed Service
 *
 * @author    Værsågod
 * @package   ToolMate
 * @since     1.0.0
 */
class EmbedService extends Component
{
    /**
     * @param string $url
     * @return array
     */
    public function getEmbed(string $url = '') {
        if ($url === '') {
            return null;
        }

        try {
            return \Craft::$app->cache->getOrSet(md5($url), function() use ($url) {
                $embed = new Embed();
                $data = $embed->get($url);
                return [
                    'title' => $data->title ?? '',
                    'description' => $data->description ?? '',
                    'url' => $data->url . '',
                    'html' => $data->code->html ?? '',
                    'width' => $data->code->width ?? 1280,
                    'height' => $data->code->height ?? 720,
                    'ratio' => $data->code->ratio ?? 56.25,
                    'image' => $data->image . '',
                    'provider' => $data->providerName ?? '',
                ];
            }, 3600);
        } catch (\Exception $exception) {
            // Craft::dd($exception);
            return null;
        }
    }

    /**
     * @param string $videoUrl
     * @param array $params
     * @return array
     */
    public function getVideoEmbed(string $videoUrl, array $params = []): array
    {
        //is this a YouTube URL?
        $isYouTube = strpos($videoUrl, 'youtube.com/') !== false || strpos($videoUrl, 'youtu.be/') !== false;
        $isVimeo = strpos($videoUrl, 'vimeo.com/') !== false;
        $isWistia = strpos($videoUrl, 'wistia.com/') !== false;
        $isViddler = strpos($videoUrl, 'viddler.com/') !== false;

        $cacheTimeInMinutes = 10080; // in minutes (default is 1 week)
        $cacheExpired = false;

        $pluginVars = array(
            'title' => 'title',
            'html' => 'embedCode',
            'author_name' => 'author',
            'author_url' => 'authorUrl',
            'description' => 'description',
            'duration' => 'duration',
            'thumbnail_url' => 'posterThumbnail',
            'medres_url' => 'posterMediumRes',
            'highres_url' => 'posterHighRes',
            'width' => 'width',
            'height' =>  'height',
            'provider_name' => 'provider',
            'upload_date' => 'date',
        );

        $videoData = array();
        foreach ($pluginVars as $var) {
            $videoData[$var] = false;
        }

        // if it's not YouTube, Vimeo, Wistia, or Viddler bail
        if ($isYouTube) {
            $url = 'https://www.youtube.com/oembed?format=xml&iframe=1&scheme=https&rel=0&url=';
        } else if ($isVimeo) {
            $url = 'https://vimeo.com/api/oembed.xml?url=';
        } else if ($isWistia) {
            $url = 'https://app.wistia.com/embed/oembed.xml?url=';
        } else if ($isViddler) {
            $url = 'https://www.viddler.com/oembed/?format=xml&url=';
        } else {
            return $videoData;
        }
        $url .= urlencode($videoUrl);

        // set the semi-ubiquitous parameters
        $maxWidth = isset($params['max_width']) ? '&maxwidth=' . $params['max_width'] : '';
        $maxHeight = isset($params['max_height']) ? '&maxheight=' . $params['max_height'] : '';
        $wmode_param = isset($params['wmode']) ? '&wmode=' . $params['wmode'] : '';
        $url .= $maxWidth . $maxHeight . $wmode_param;

        // cache can be disabled by setting 0 as the cache_minutes param
        if (isset($params['cache_minutes']) && $params['cache_minutes'] !== false && is_numeric($params['cache_minutes'])) {
            $cacheTimeInMinutes = $params['cache_minutes'];
        }

        // optional provider prefixed parameters
        $providerExtraParams = [];
        if ($isVimeo) {
            $providerExtraParams = $this->getPrefixedParams($params, 'vimeo_');
        } else if ($isWistia) {
            $providerExtraParams = $this->getPrefixedParams($params, 'wistia_');

            // handle legacy shortcuts
            if (isset($providerExtraParams['type'])) {
                $providerExtraParams['embedType'] = $providerExtraParams['type'];
                unset($providerExtraParams['type']);
            }
            if (isset($providerExtraParams['foam'])) {
                $providerExtraParams['videoFoam'] = $providerExtraParams['foam'];
                unset($providerExtraParams['foam']);
            }
        } else if ($isViddler) {
            $providerExtraParams = $this->getPrefixedParams($params, 'viddler_');
        }
        if (!empty($providerExtraParams)) {
            $url .= '&' . $this->makeUrlKeyValuePairsString($providerExtraParams);
        }

        // checking if url has been cached
        $cachedUrl = Craft::$app->cache->get($url);

        if (!$cacheTimeInMinutes || $cacheExpired || !$cachedUrl) {
            // create the info and header variables
            list($videoInfo, $videoHeader) = $this->getVideoInfo($url);

            // write the data to cache if caching hasn't been disabled
            if ($cacheTimeInMinutes) {
                Craft::$app->cache->set($url, $videoInfo, $cacheTimeInMinutes);
            }
        } else {
            $videoInfo = $cachedUrl;
        }

        // decode the cURL data
        libxml_use_internal_errors(true);

        $videoInfo = simplexml_load_string($videoInfo);

        // gracefully fail if the video is not found
        if ($videoInfo === false) {
            return $videoData;
        }

        // inject wmode transparent if required
        $wmode = $params['wmode'] ?? '';
        if ($wmode === 'transparent' || $wmode === 'opaque' || $wmode === 'window') {
            $param_str = '<param name="wmode" value="' . $wmode . '"></param>';
            $embed_str = ' wmode="' . $wmode . '" ';

            // determine whether we are dealing with iframe or embed and handle accordingly
            if (strpos($videoInfo->html, '<iframe') === false) {
                $param_pos = strpos($videoInfo->html, '<embed');
                $videoInfo->html = substr($videoInfo->html, 0, $param_pos) . $param_str . substr($videoInfo->html, $param_pos);
                $param_pos = strpos($videoInfo->html, '<embed') + 6;
                $videoInfo->html = substr($videoInfo->html, 0, $param_pos) . $embed_str . substr($videoInfo->html, $param_pos);
            } else {
                // determine whether to add question mark to query string
                preg_match('/<iframe.*?src="(.*?)".*?<\/iframe>/i', $videoInfo->html, $matches);
                $append_query_marker = (strpos($matches[1], '?') !== false ? '' : '?');

                $videoInfo->html = preg_replace('/<iframe(.*?)src="(.*?)"(.*?)<\/iframe>/i', '<iframe$1src="$2' . $append_query_marker . '&wmode=' . $wmode . '"$3</iframe>', $videoInfo->html);
            }
        }

        // add in the YouTube-specific params
        if ($isYouTube) {
            $youTubeParams = $this->getPrefixedParams($params, 'youtube_');
            // Make sure related videos from different YouTube channels are opt-in
            $youTubeParams['rel'] = $youTubeParams['rel'] ?? '0';
            if (!empty($youTubeParams)) {
                //handle any YouTube-specific param updates
                if (isset($youTubeParams['playlist'])) {
                    // if the playlist is set to a url and not an id, then try to update it
                    // regex from https://stackoverflow.com/a/26660288/1136822
                    $value = $youTubeParams['playlist'];
                    if (preg_match("#([\/|\?|&]vi?[\/|=]|youtu\.be\/|embed\/)(\w+)#", $value, $matches)) {
                        $youTubeParams['playlist'] = $matches[2];
                    }
                }

                //work the params into the embed URL
                preg_match('/.*?src="(.*?)".*?/', $videoInfo->html, $matches);
                if (!empty($matches[1])) {
                    $videoInfo->html = str_replace($matches[1], $matches[1] . '&' . $this->makeUrlKeyValuePairsString($youTubeParams), $videoInfo->html);
                }
            }
        }

        // add the vimeo_player_id or id param value to the iFrame HTML if set
        $id = '';
        if (!empty($params['vimeo_player_id'])) {
            $id = $params['vimeo_player_id'];
        } else if (!empty($params['id'])) {
            $id = $params['id'];
        }
        if (!empty($id)) {
            $videoInfo->html = preg_replace('/<iframe/i', '<iframe id="' . $id . '"', $videoInfo->html);
        }

        // add the class to the iFrame HTML if set
        if (!empty($params['class'])) {
            $videoInfo->html = preg_replace('/<iframe/i', '<iframe class="' . $params['class'] . '"', $videoInfo->html);
        }

        // add the attributes string to the iFrame HTML if set
        if (!empty($params['attributes'])) {
            $videoInfo->html = preg_replace('/<iframe/i', '<iframe ' . $params['attributes'], $videoInfo->html);
        }

        // actually setting thumbnails at a reasonably consistent size, as well as getting higher-res images
        if ($isYouTube) {
            $videoInfo->highres_url = str_replace('hqdefault', 'maxresdefault', $videoInfo->thumbnail_url);
            $videoInfo->medres_url = $videoInfo->thumbnail_url;
            $videoInfo->thumbnail_url = str_replace('hqdefault', 'mqdefault', $videoInfo->thumbnail_url);
        } else if ($isVimeo) {
            $videoInfo->highres_url = preg_replace('/_(.*?)\./', '_1280.', $videoInfo->thumbnail_url);
            $videoInfo->medres_url = preg_replace('/_(.*?)\./', '_640.', $videoInfo->thumbnail_url);
            $videoInfo->thumbnail_url = preg_replace('/_(.*?)\./', '_295.', $videoInfo->thumbnail_url);
        } else if ($isWistia) {
            $videoInfo->highres_url = str_replace('?image_crop_resized=100x60', '', $videoInfo->thumbnail_url);
            $videoInfo->medres_url = str_replace('?image_crop_resized=100x60', '?image_crop_resized=640x400', $videoInfo->thumbnail_url);
            $videoInfo->thumbnail_url = str_replace('?image_crop_resized=100x60', '?image_crop_resized=240x135', $videoInfo->thumbnail_url);
        } else if ($isViddler) {
            $videoInfo->highres_url = $videoInfo->thumbnail_url;
            $videoInfo->medres_url = $videoInfo->thumbnail_url;
            $videoInfo->thumbnail_url = str_replace('thumbnail_2', 'thumbnail_1', $videoInfo->thumbnail_url);
        }

        // handle full output
        foreach ($pluginVars as $key => $var) {
            if (isset($videoInfo->$key)) {
                $value = $videoInfo->$key ?? null;
                if (!$value) {
                    continue;
                }
                if (in_array($var, ['width', 'height', 'duration'])) {
                    $videoData[$var] = (int) $value;
                } else {
                    $videoData[$var] = (string) $value;
                }
            }
        }

        // replace the embed code with the Twig object
        $videoData['embedCode'] = Template::raw($videoInfo->html);

        // Add aspect ratio
        $width = (int) ($videoData['width'] ?? null);
        $height = (int) ($videoData['height'] ?? null);
        $videoData['aspectRatio'] = $width && $height ? $width / $height : null;

        return $videoData;
    }

    /**
     * Request the video info via cURL or file_get_contents.
     *
     * @param string $videoUrl The video URL.
     * @return array An array containing the video info (or false) and the response code (or false).
     */
    public function getVideoInfo($videoUrl): array
    {
        // do we have curl?
        if (function_exists('curl_init')) {
            $curl = curl_init();

            // cURL options
            $options = array(
                CURLOPT_URL => $videoUrl,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false //no ssl verification
            );

            curl_setopt_array($curl, $options);

            $videoInfo = curl_exec($curl);
            $videoHeader = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // close the request
            curl_close($curl);

        } // do we have fopen?
        elseif (ini_get('allow_url_fopen') === true) {
            $videoHeader = ($videoInfo = file_get_contents($videoUrl)) ? '200' : true;
        } else {
            $videoHeader = $videoInfo = false;
        }

        return array($videoInfo, $videoHeader);
    }

    /**
     * Gets all of the values from the params array that start with the
     * specified prefix.
     *
     * @param array $params The array of params to check.
     * @param string $prefix The prefix that keys should start with in order to be returned.
     * @return array The array of (unprefixed) key => value pairs that matched the specified prefix.
     */
    private function getPrefixedParams($params = [], $prefix = ''): array
    {
        $prefixedParams = [];

        if (empty($prefix) || empty($params)) {
            return $prefixedParams;
        }

        foreach ($params as $key => $value) {
            // if this param doesn't start with the prefix then continue the loop
            if (strpos($key, $prefix) !== 0) {
                continue;
            }

            // get the text after the prefix as the key name or continue
            $paramKey = substr($key, strlen($prefix));
            if (empty($paramKey)) {
                continue;
            }

            $prefixedParams[$paramKey] = $value;
        }

        return $prefixedParams;
    }

    /**
     * Converts an array of key => value pairs to a URL param string.
     *
     * @param array $pairs An array of key => value pairs
     * @return string The resulting string. Ex: key=value&key2=value2
     */
    private function makeUrlKeyValuePairsString($pairs = []): string
    {
        $chunks = [];

        if (!empty($pairs) && is_array($pairs)) {
            foreach ($pairs as $key => $value) {
                $chunks[] = $key . '=' . $value;
            }
        }

        return implode('&', $chunks);
    }
}
