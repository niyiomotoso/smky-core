<?php
/**
 * =======================================================================================
 *                           GemFramework (c) GemPixel
 * ---------------------------------------------------------------------------------------
 *  This software is packaged with an exclusive framework as such distribution
 *  or modification of this framework is not allowed before prior consent from
 *  GemPixel. If you find that this framework is packaged in a software not distributed
 *  by GemPixel or authorized parties, you must not use this software and contact gempixel
 *  at https://gempixel.com/contact to inform them of this misuse.
 * =======================================================================================
 *
 * @package GemPixel\Premium-URL-Shortener
 * @author GemPixel (https://gempixel.com)
 * @license https://gempixel.com/licenses
 * @link https://gempixel.com
 */

namespace Helpers;

use Core\DB;
use Core\View;
use Core\Auth;
use Core\Helper;
use Core\Response;
use Core\Request;
use Core\Plugin;
use Traits\Links;
use Helpers\App;
use Exception;

class BioWidgets {

    use Links;

    /**
     * Bio Widgets
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return string
     */
    public static function widgets($type = null, $action = null){

        $list = [
                'tagline' => [
                    'category' => 'content',
                    'icon' => '<h1><i class="fa fa-info-circle"></i></h1>',
                    'title' => e('Tagline'),
                    'description' => e('Add a tagline under your profile name'),
                    'setup' => [BioWidgets::class, 'taglineSetup'],
                    'save' => [BioWidgets::class, 'taglineSave'],
                    'block' => [BioWidgets::class, 'taglineBlock'],
                    'processor' => null,
                ],
                'heading' => [
                    'category' => 'content',
                    'icon' => '<h1><i class="fa fa-heading"></i></h1>',
                    'title' => e('Heading'),
                    'description' => e('Add a heading with different sizes'),
                    'setup' => [BioWidgets::class, 'headingSetup'],
                    'save' => [BioWidgets::class, 'headingSave'],
                    'block' => [BioWidgets::class, 'headingBlock'],
                    'processor' => null,
                ],
                'text' => [
                    'category' => 'content',
                    'icon' => '<h1><i class="fa fa-align-center"></i></h1>',
                    'title' => e('Text'),
                    'description' => e('Add a text body to your page'),
                    'setup' => [BioWidgets::class, 'textSetup'],
                    'save' => [BioWidgets::class, 'textSave'],
                    'block' => [BioWidgets::class, 'textBlock'],
                    'processor' => null,
                ],
                'divider' => [
                    'category' => 'content',
                    'icon' => '<h1><i class="fa fa-grip-lines"></i></h1>',
                    'title' => e('Divider'),
                    'description' => e('Separate your content with a line'),
                    'setup' => [BioWidgets::class, 'dividerSetup'],
                    'save' => [BioWidgets::class, 'dividerSave'],
                    'block' => [BioWidgets::class, 'dividerBlock'],
                    'processor' => null,
                ],
                'link' => [
                    'category' => 'content',
                    'icon' => '<h1><i class="fa fa-link"></i></h1>',
                    'title' => e('Link'),
                    'description' => e('Add a trackable button to a link'),
                    'setup' => [BioWidgets::class, 'linkSetup'],
                    'save' => [BioWidgets::class, 'linkSave'],
                    'block' => [BioWidgets::class, 'linkBlock'],
                    'processor' => [BioWidgets::class, 'linkProcessor'],
                ],
                'html' => [
                    'category' => 'content',
                    'icon' => '<h1><i class="fa fa-code"></i></h1>',
                    'title' => e('HTML'),
                    'description' => e('Add custom HTML code. Script codes are not accepted'),
                    'setup' => [BioWidgets::class, 'htmlSetup'],
                    'save' => [BioWidgets::class, 'htmlSave'],
                    'block' => [BioWidgets::class, 'htmlBlock'],
                    'processor' => null,
                ],
                'image' => [
                    'category' => 'content',
                    'icon' => '<h1><i class="fa fa-image"></i></h1>',
                    'title' => e('Image'),
                    'description' => e('Upload an image or 2 images in a row'),
                    'setup' => [BioWidgets::class, 'imageSetup'],
                    'save' => [BioWidgets::class, 'imageSave'],
                    'block' => [BioWidgets::class, 'imageBlock'],
                    'processor' => null,
                ],
                'phone' => [
                    'category' => 'content',
                    'icon' => '<h1><i class="fa fa-phone"></i></h1>',
                    'title' => e('Phone Call'),
                    'description' => e('Set your phone number to call directly'),
                    'setup' => [BioWidgets::class, 'phoneSetup'],
                    'save' => [BioWidgets::class, 'phoneSave'],
                    'block' => [BioWidgets::class, 'phoneBlock'],
                    'processor' => null,
                ],

                'vcard' => [
                    'category' => 'content',
                    'icon' => '<h1><i class="fa fa-address-card"></i></h1>',
                    'title' => e('vCard'),
                    'description' => e('Add a downloadable vCard'),
                    'setup' => [BioWidgets::class, 'vcardSetup'],
                    'save' => [BioWidgets::class, 'vcardSave'],
                    'block' => [BioWidgets::class, 'vcardBlock'],
                    'processor' => [BioWidgets::class, 'vcardProcessor'],
                ],
                'paypal' => [
                    'category' => 'content',
                    'icon' => '<img src="'.assets('images/paypal.svg').'" width="30">',
                    'title' => e('PayPal Button'),
                    'description' => e('Generate a PayPal button to accept payments'),
                    'setup' => [BioWidgets::class, 'paypalSetup'],
                    'save' => [BioWidgets::class, 'paypalSave'],
                    'block' => [BioWidgets::class, 'paypalBlock'],
                    'processor' => null,
                ],
                'whatsappcall' => [
                    'category' => 'content',
                    'icon' => '<img src="'.assets('images/whatsapp.svg').'" width="30">',
                    'title' => e('WhatsApp Call'),
                    'description' => e('Add button to initiate a Whatsapp call'),
                    'setup' => [BioWidgets::class, 'whatsappcallSetup'],
                    'save' => [BioWidgets::class, 'whatsappcallSave'],
                    'block' => [BioWidgets::class, 'whatsappcallBlock'],
                    'processor' => null,
                ],
                'whatsapp' => [
                    'category' => 'content',
                    'icon' => '<img src="'.assets('images/whatsapp.svg').'" width="30">',
                    'title' => e('WhatsApp Message'),
                    'description' => e('Add button to send a Whatsapp message'),
                    'setup' => [BioWidgets::class, 'whatsappSetup'],
                    'save' => [BioWidgets::class, 'whatsappSave'],
                    'block' => [BioWidgets::class, 'whatsappBlock'],
                    'processor' => null,
                ],
                'rss' => [
                    'category' => 'widgets',
                    'icon' => '<h1><i class="text-danger fa fa-rss"></i></h1>',
                    'title' => e('RSS Feed'),
                    'description' => e('Add a dynamic RSS feed widget'),
                    'setup' => [BioWidgets::class, 'rssSetup'],
                    'save' => [BioWidgets::class, 'rssSave'],
                    'block' => [BioWidgets::class, 'rssBlock'],
                    'processor' => null,
                ],
                'newsletter' => [
                    'category' => 'widgets',
                    'icon' => '<h1><i class="text-primary fa fa-envelope-open"></i></h1>',
                    'title' => e('Newsletter'),
                    'description' => e('Add a newsletter form to store emails'),
                    'setup' => [BioWidgets::class, 'newsletterSetup'],
                    'save' => [BioWidgets::class, 'newsletterSave'],
                    'block' => [BioWidgets::class, 'newsletterBlock'],
                    'processor' => [BioWidgets::class, 'newsletterProcessor'],
                ],
                'contact' => [
                    'category' => 'widgets',
                    'icon' => '<h1><i class="text-success fa fa-envelope-square"></i></h1>',
                    'title' => e('Contact Form'),
                    'description' => e('Add a contact form to receive emails'),
                    'setup' => [BioWidgets::class, 'contactSetup'],
                    'save' => [BioWidgets::class, 'contactSave'],
                    'block' => [BioWidgets::class, 'contactBlock'],
                    'processor' => [BioWidgets::class, 'contactProcessor'],
                ],
                'faqs' => [
                    'category' => 'widgets',
                    'icon' => '<h1><i class="text-info fa fa-question-circle "></i></h1>',
                    'title' => e('FAQs'),
                    'description' => e('Add a widget of questions and answers'),
                    'setup' => [BioWidgets::class, 'faqsSetup'],
                    'save' => [BioWidgets::class, 'faqsSave'],
                    'block' => [BioWidgets::class, 'faqsBlock'],
                    'processor' => null,
                ],
                'product' => [
                    'category' => 'widgets',
                    'icon' => '<h1><i class="text-warning fa fa-store"></i></h1>',
                    'title' => e('Product'),
                    'description' => e('Add a widget to a product on your site'),
                    'setup' => [BioWidgets::class, 'productSetup'],
                    'save' => [BioWidgets::class, 'productSave'],
                    'block' => [BioWidgets::class, 'productBlock'],
                    'processor' => null,
                ],
                'youtube' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/youtube.svg').'" width="30">',
                    'title' => e('Youtube Video or Playlist'),
                    'description' => e('Embed a Youtube video or a playlist'),
                    'setup' => [BioWidgets::class, 'youtubeSetup'],
                    'save' => [BioWidgets::class, 'youtubeSave'],
                    'block' => [BioWidgets::class, 'youtubeBlock'],
                    'processor' => null,
                ],
                'spotify' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/spotify.svg').'" width="30">',
                    'title' => e('Spotify Embed'),
                    'description' => e('Embed a Spotify music or playlist widget'),
                    'setup' => [BioWidgets::class, 'spotifySetup'],
                    'save' => [BioWidgets::class, 'spotifySave'],
                    'block' => [BioWidgets::class, 'spotifyBlock'],
                    'processor' => null,
                ],
                'itunes' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/itunes.svg').'" width="30">',
                    'title' => e('Apple Music Embed'),
                    'description' => e('Embed an Apple music widget'),
                    'setup' => [BioWidgets::class, 'itunesSetup'],
                    'save' => [BioWidgets::class, 'itunesSave'],
                    'block' => [BioWidgets::class, 'itunesBlock'],
                    'processor' => null,
                ],
                'tiktok' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/tiktok.svg').'" width="30">',
                    'title' => e('TikTok Embed'),
                    'description' => e('Embed a tiktok video'),
                    'setup' => [BioWidgets::class, 'tiktokSetup'],
                    'save' => [BioWidgets::class, 'tiktokSave'],
                    'block' => [BioWidgets::class, 'tiktokBlock'],
                    'processor' => null,
                ],
                'opensea' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/opensea.svg').'" width="30">',
                    'title' => e('OpenSea NFT'),
                    'description' => e('Embed your NFT from OpenSea'),
                    'setup' => [BioWidgets::class, 'openseaSetup'],
                    'save' => [BioWidgets::class, 'openseaSave'],
                    'block' => [BioWidgets::class, 'openseaBlock'],
                    'processor' => null,
                ],
                'twitter' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/twitter.svg').'" width="30">',
                    'title' => e('Embed Tweets'),
                    'description' => e('Embed your latest tweets'),
                    'setup' => [BioWidgets::class, 'twitterSetup'],
                    'save' => [BioWidgets::class, 'twitterSave'],
                    'block' => [BioWidgets::class, 'twitterBlock'],
                    'processor' => null,
                ],
                'soundcloud' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/soundcloud.svg').'" width="30">',
                    'title' => e('SoundCloud'),
                    'description' => e('Embed a SoundCloud track'),
                    'setup' => [BioWidgets::class, 'soundcloudSetup'],
                    'save' => [BioWidgets::class, 'soundcloudSave'],
                    'block' => [BioWidgets::class, 'soundcloudBlock'],
                    'processor' => null,
                ],
                'facebook' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/facebook.svg').'" width="30">',
                    'title' => e('Facebook Post'),
                    'description' => e('Embed a Facebook post'),
                    'setup' => [BioWidgets::class, 'facebookSetup'],
                    'save' => [BioWidgets::class, 'facebookSave'],
                    'block' => [BioWidgets::class, 'facebookBlock'],
                    'processor' => null,
                ],
                'instagram' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/instagram.svg').'" width="30">',
                    'title' => e('Instagram Post'),
                    'description' => e('Embed an Instagram post'),
                    'setup' => [BioWidgets::class, 'instagramSetup'],
                    'save' => [BioWidgets::class, 'instagramSave'],
                    'block' => [BioWidgets::class, 'instagramBlock'],
                    'processor' => null,
                ],
                'typeform' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/typeform.svg').'" width="30">',
                    'title' => e('Typeform'),
                    'description' => e('Embed a Typeform form'),
                    'setup' => [BioWidgets::class, 'typeformSetup'],
                    'save' => [BioWidgets::class, 'typeformSave'],
                    'block' => [BioWidgets::class, 'typeformBlock'],
                    'processor' => null
                ],
                'pinterest' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/pinterest.svg').'" width="30">',
                    'title' => e('Pinterest'),
                    'description' => e('Embed a Pinterest board'),
                    'setup' => [BioWidgets::class, 'pinterestSetup'],
                    'save' => [BioWidgets::class, 'pinterestSave'],
                    'block' => [BioWidgets::class, 'pinterestBlock'],
                    'processor' => null,
                ],
                'reddit' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/reddit.svg').'" width="30">',
                    'title' => e('Reddit'),
                    'description' => e('Embed a Reddit profile'),
                    'setup' => [BioWidgets::class, 'redditSetup'],
                    'save' => [BioWidgets::class, 'redditSave'],
                    'block' => [BioWidgets::class, 'redditBlock'],
                    'processor' => null,
                ],
                'calendly' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/calendly.svg').'" width="30">',
                    'title' => e('Calendly'),
                    'description' => e('Schedule booking & appointments'),
                    'setup' => [BioWidgets::class, 'calendlySetup'],
                    'save' => [BioWidgets::class, 'calendlySave'],
                    'block' => [BioWidgets::class, 'calendlyBlock'],
                    'processor' => [BioWidgets::class, 'calendlyProcessor'],
                ],
                'threads' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/threads.svg').'" width="30">',
                    'title' => e('Threads'),
                    'description' => e('Display a Threads post'),
                    'setup' => [BioWidgets::class, 'threadsSetup'],
                    'save' => [BioWidgets::class, 'threadsSave'],
                    'block' => [BioWidgets::class, 'threadsBlock'],
                    'processor' => null,
                ],
                'tiktokprofile' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/tiktok.svg').'" width="30">',
                    'title' => e('TikTok Profile'),
                    'description' => e('Display your profile'),
                    'setup' => [BioWidgets::class, 'tiktokprofileSetup'],
                    'save' => [BioWidgets::class, 'tiktokprofileSave'],
                    'block' => [BioWidgets::class, 'tiktokprofileBlock'],
                    'processor' => null,
                ],
                'googlemaps' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/maps.svg').'" width="30">',
                    'title' => e('Google Maps'),
                    'description' => e('Add a pin to your location on Google Maps'),
                    'setup' => [BioWidgets::class, 'googlemapsSetup'],
                    'save' => [BioWidgets::class, 'googlemapsSave'],
                    'block' => [BioWidgets::class, 'googlemapsBlock'],
                    'processor' => null,
                ],
                'opentable' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/opentable.svg').'" width="30">',
                    'title' => e('OpenTable Reservation'),
                    'description' => e('Allow visitors to easily book a table'),
                    'setup' => [BioWidgets::class, 'opentableSetup'],
                    'save' => [BioWidgets::class, 'opentableSave'],
                    'block' => [BioWidgets::class, 'opentableBlock'],
                    'processor' => null,
                ],
                'eventbrite' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/eventbrite.svg').'" width="30">',
                    'title' => e('EventBrite'),
                    'description' => e('Allow visitors to easily book an event'),
                    'setup' => [BioWidgets::class, 'eventbriteSetup'],
                    'save' => [BioWidgets::class, 'eventbriteSave'],
                    'block' => [BioWidgets::class, 'eventbriteBlock'],
                    'processor' => null,
                ],
                'snapchat' => [
                    'category' => 'widgets',
                    'icon' => '<img src="'.assets('images/snapchat.svg').'" width="30">',
                    'title' => e('Snapchat'),
                    'description' => e('Add a Snapchat widget on your page'),
                    'setup' => [BioWidgets::class, 'snapchatSetup'],
                    'save' => [BioWidgets::class, 'snapchatSave'],
                    'block' => [BioWidgets::class, 'snapchatBlock'],
                    'processor' => null,
                ]
            ];

        if($extended = \Core\Plugin::dispatch('biowidgets.extend')){
			foreach($extended as $fn){
				$list = array_merge($list, $fn);
			}
		}

		if($type && $action) {
            return $list[$type][$action] ?? false;
        }

		if($type){
            return $list[$type] ?? false;
        }

		return $list;
    }
    /**
     * Social Platforms
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @return void
     */
    public static function socialPlatforms($key = null){

        $list = [
            'amazon' => [
                'name' => e('Amazon'),
                'icon' => '<i class="fab fa-amazon"></i>',
            ],
            'behance' => [
                'name' => e('Behance'),
                'icon' => '<i class="fab fa-behance"></i>',
            ],
            'discord' => [
                'name' => e('Discord'),
                'icon' => '<i class="fab fa-discord"></i>',
            ],
            'dribbble' => [
                'name' => e('Dribbble'),
                'icon' => '<i class="fab fa-dribbble"></i>',
            ],
            'email' => [
                'name' => e('Email'),
                'icon' => '<i class="fa fa-envelope"></i>',
            ],
            'facebook' => [
                'name' => e('Facebook'),
                'icon' => '<i class="fab fa-facebook"></i>',
            ],
            'github' => [
                'name' => e('Github'),
                'icon' => '<i class="fab fa-github"></i>',
            ],
            'instagram' => [
                'name' => e('Instagram'),
                'icon' => '<i class="fab fa-instagram"></i>',
            ],
            'line' => [
                'name' => e('Line Messenger'),
                'icon' => '<i class="fab fa-line"></i>',
            ],
            'linkedin' => [
                'name' => e('Linkedin'),
                'icon' => '<i class="fab fa-linkedin"></i>',
            ],
            'medium' => [
                'name' => e('Medium'),
                'icon' => '<i class="fab fa-brands fa-medium"></i>',
            ],
            'pinterest' => [
                'name' => e('Pinterest'),
                'icon' => '<i class="fab fa-pinterest"></i>',
            ],
            'phone' => [
                'name' => e('Phone Number'),
                'icon' => '<i class="fa fa-phone"></i>',
            ],
            'shopify' => [
                'name' => e('Shopify'),
                'icon' => '<i class="fab fa-shopify"></i>',
            ],
            'snapchat' => [
                'name' => e('Snapchat'),
                'icon' => '<i class="fab fa-snapchat"></i>',
            ],
            'spotify' => [
                'name' => e('Spotify'),
                'icon' => '<i class="fab fa-spotify"></i>',
            ],
            'telegram' => [
                'name' => e('Telegram'),
                'icon' => '<i class="fab fa-telegram"></i>',
            ],
            'threads' => [
                'name' => e('Threads'),
                'icon' => '<i class="fab fa-threads"></i>',
            ],
            'tiktok' => [
                'name' => e('TikTok'),
                'icon' => '<i class="fab fa-tiktok"></i>',
            ],
            'twitch' => [
                'name' => e('Twitch'),
                'icon' => '<i class="fab fa-twitch"></i>',
            ],
            'twitter' => [
                'name' => e('Twitter'),
                'icon' => '<i class="fab fa-twitter"></i>',
            ],
            'viber' => [
                'name' => e('Viber'),
                'icon' => '<i class="fab fa-viber"></i>',
            ],
            'whatsapp' => [
                'name' => e('Whatsapp'),
                'icon' => '<i class="fab fa-whatsapp"></i>',
            ],
            'x' => [
                'name' => e('X'),
                'icon' => '<i class="fab fa-x-twitter"></i>',
            ],
            'youtube' => [
                'name' => e('Youtube'),
                'icon' => '<i class="fab fa-youtube"></i>',
            ],
        ];

        if($extended = \Core\Plugin::dispatch('biosocials.extend')){
			foreach($extended as $fn){
				$list = array_merge($list, $fn);
			}
		}

		if($key){
            return $list[$key] ?? false;
        }

		return $list;
    }
    /**
     * Widgets by Category
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return string
     */
    public static function widgetsByCategory(){
        $widgets = [];
        foreach(self::widgets() as $name => $widget){
            $widgets[$widget['category']][$name] = $widget;
        }
        return $widgets;
    }
    /**
     * Render Block
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @param string $id
     * @param array $value
     * @return string
     */
    public static function render($id, $value){

        if(self::isCountryAllowed($value) == false) return;

        if(self::isLanguageAllowed($value) == false) return;

        if(self::isScheduled($value) == false) return;
        
        if($class = self::widgets($value['type'], 'block')){
            if(isset($value['active']) && !$value['active']) return;
            return '<div class="item mb-3">'.call_user_func($class, $id, $value).'</div>';
        }
    }
    /**
     * Processors
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $data
     * @param [type] $url
     * @return void
     */
    public static function processors($profile, $url, $user){

        $profiledata = json_decode($profile->data, true);

        foreach($profiledata['links'] as $id => $block){
            if($class = self::widgets($block['type'], 'processor')){
                if(isset($block['active']) && !$block['active']) continue;
                call_user_func($class, $block, $profile, $url, $user);
            }
        }
    }
    /**
     * Validate data and update block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param \Core\Request $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function update(Request $request, $profiledata, $data){

        // Validate Geo Data
        if(isset($data['countries']) && $data['countries']){
            foreach($data['countries'] as $country){
                if(!in_array($country, \Core\Helper::Country(false))) throw new Exception(e('{b} Error: One or more countries are invalid.', null, ['b' => e('Tagline')]));
            }
        }else{
            $data['countries'] = [];
        }
        
        if(isset($data['languages'])) $data['languages'] = [];
        
        if($class = self::widgets($data['type'], 'save')){
            return call_user_func($class, $request, $profiledata, $data);
        }
    }
    /**
     * Check if block has country restrictions
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param array $data
     * @return boolean
     */
    public static function isCountryAllowed($data){

        if(!isset($data['countries']) || empty($data['countries']) || !is_array($data['countries'])) return true;

        $location = request()->country();

        if($location['country'] && $data['countries'] && in_array($location['country'], $data['countries'])) return true;

        return false;
    }
    /**
     * Check if block has language restrictions
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param array $data
     * @return boolean
     */
    public static function isLanguageAllowed($data){

        if(!isset($data['languages']) || empty($data['languages']) || !is_array($data['countries'])) return true;

        $request = request();

        $browser_language = $request->server('http_accept_language') ? substr($request->server('http_accept_language'), 0, 2) : null;

        if($browser_language && strpos($browser_language, ' ') !== false){
            $language = strtolower(implode(' ', explode(' ',$browser_language, -1)));
        } else {
            $language = $browser_language ? strtolower($browser_language) : null;
        }

        if($language && $data['languages'] && in_array($language, $data['languages'])) return true;

        return false;
    }
    /**
     * Check if Scheduled
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param array $data
     * @return boolean
     */
    public static function isScheduled($data){

        $currenttime = strtotime('now');

        $displayed = true;

        if(isset($data['startdate']) && strtotime($data['startdate']) && $currenttime <= strtotime($data['startdate'])) $displayed = false;

        if(isset($data['enddate']) && strtotime($data['enddate']) && $currenttime >= strtotime($data['enddate'])) $displayed = false;

        return $displayed;
    }
    /**
     * Remove Lines
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @param string $string
     * @return string
     */
    public static function format(string $string){
        return preg_replace("/[\n\r\t]/", "", $string);
    }
    /**
     * Translate for Javascript
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 7.2
     * @param [type] $string
     * @return void
     */
    public static function e($string, $plural = null, $vars = []){
        return addslashes(e($string, $plural, $vars));
    }
    /**
     * Generate Template
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @param array $fields
     * @return string
     */
    public static function generateTemplate(string $fields, $type = null){

        $countries = '';
        foreach (\Core\Helper::Country(false) as $country){
            $countries .= '<option value="'.$country.'"  \'+(content && content[\'countries\'] && content[\'countries\'].indexOf(\''.$country.'\') !== -1 ? \'selected\':\'\')+\'>'.$country.'</option>';
        }

        $languages = '';
        foreach (\Helpers\App::languagelist(null, false, true) as $key => $language){
            $languages .= '<option value="'.$key.'"  \'+(content && content[\'languages\'] && content[\'languages\'].indexOf(\''.$key.'\') !== -1 ? \'selected\':\'\')+\'>'.$language.'</option>';
        }

        return '<div class="px-1 pt-2 pb-1 border rounded widget mb-3 '.($type && $type != 'tagline' ? 'sortable' : '').'" data-id="\'+did+\'">
                    <div class="d-flex align-items-center">
                    '.($type && $type != 'tagline' ? '<i class="fs-4 fa fa-align-justify handle ms-1" data-bs-toggle="tooltip" title="'.self::e('Move').'"></i>' : '').'
                        <div class="ms-auto d-flex align-items-center">
                            <a class="ms-auto fs-6 pe-2" data-bs-toggle="modal" data-bs-target="#removecard" data-trigger="removeCard" href=""><i class="fa fa-times text-dark fs-4" data-bs-toggle="tooltip" title="'.self::e('Delete').'"></i></a>
                        </div>
                    </div>
                    <div class="card mt-2 mb-0 p-2 shadow-sm border flex-fill">
                        <div class="d-flex align-items-center">
                            <div class="mb-0 flex-fill"><a class="text-dark d-block py-3" data-bs-toggle="collapse" data-bs-target="#container-\'+did+\'" aria-expanded="false"><h5 class="mb-0"><i class="fa fa-chevron-down me-2"></i><span class="align-top fw-bold">\'+$(\'[data-type="'.$type.'"] h5\').text()+\'</span></h5></a></div>

                            \'+(typeof clicks !== "undefined" && clicks !== null ? \'<div class="me-4"><span class="text-muted"><i class="fa fa-mouse me-1"></i> \'+(urlid !== null ? \'<a href="\'+appurl+\'\'+urlid+\'/stats" class="text-muted text-small" target="_blank" data-bs-toggle="tooltip" title="'.self::e('View Stats').'">\'+clicks+\' '.self::e('Clicks').'</a>\' : clicks)+\' </span></div>\' : \'\')+\'
                            <div class="ms-auto">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" data-binary="true" name="data[\'+slug(did)+\'][active]" value="1" data-bs-toggle="tooltip" title="'.self::e('Toggle Block').'" \'+(content && typeof content[\'active\'] == \'undefined\' ? \'checked\' : \'\')+\' \'+(content && content[\'active\'] && content[\'active\'] ==\'1\' ? \'checked\' : \'\')+\'>
                                </div>
                            </div>
                        </div>

                        <div class="collapse mt-2" id="container-\'+did+\'">
                            <input type="hidden" name="data[\'+slug(did)+\'][type]" value="'.$type.'">
                            '.$fields.'
                            <button type="button" data-bs-toggle="collapse" data-bs-target="#advanced-\'+did+\'" class="btn btn-secondary w-100 mt-3 py-2"><i class="fa fa-cog me-2"></i> '.self::e('Advanced Settings').'</button>
                            <div class="collapse mt-2" id="advanced-\'+did+\'">
                                <div class="form-group mt-4 border rounded p-2">
                                    <label class="form-label fw-bold">'.self::e('Geo Targeting').'</label>
                                    <p class="form-text">'.self::e('Display this block for specific countries').'</p>
                                    <div class="input-select">
                                        <select name="data[\'+slug(did)+\'][countries][]" class="form-control" data-toggle="select" multiple placeholder="e.g. United States">
                                            '.$countries.'
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mt-4 border rounded p-2">
                                    <label class="form-label fw-bold">'.self::e('Language Targeting').'</label>
                                    <p class="form-text">'.self::e('Display this block for specific languages').'</p>
                                    <div class="input-select">
                                        <select name="data[\'+slug(did)+\'][languages][]" class="form-control" data-toggle="select" multiple placeholder="e.g. English">
                                            '.$languages.'
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mt-4 border rounded p-2">
                                    <label class="form-label fw-bold">'.self::e('Schedule').'</label>
                                    <p class="form-text">'.self::e('Schedule when this blocks goes live and ends').'</p>
                                    <div class="d-block d-sm-flex">
                                        <div class="flex-fill mb-2">
                                            <label class="form-label">'.self::e('Start').'</label>
                                            <input name="data[\'+slug(did)+\'][startdate]" class="form-control p-2 me-0 me-sm-1" data-toggle="biodatepicker" placeholder="e.g. 2023-01-01" value="\'+(content && content[\'startdate\'] ? content[\'startdate\'] : \'\')+\'" autocomplete="off">
                                        </div>
                                        <div class="flex-fill mb-2">
                                            <label class="form-label">'.self::e('End').'</label>
                                            <input name="data[\'+slug(did)+\'][enddate]" class="form-control p-2 ms-0 ms-sm-1" data-toggle="biodatepicker" placeholder="e.g. 2023-03-01" value="\'+(content && content[\'enddate\'] ? content[\'enddate\'] : \'\')+\'" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                '.($type == 'link' ? '
                                <div class="form-group mt-4 border rounded p-2">
                                    <label class="form-label fw-bold">'.self::e('Gate Access').'</label>
                                    <p class="form-text">'.self::e('Visitors can be gated before accessing the link. Please note that you can only activate one at a time.').'</p>
                                    <div class="d-flex">
                                        <div>
                                            <label class="form-check-label fw-bold">'.self::e('Sensitive Content').'</label>
                                            <p class="form-text">'.self::e('Visitors must acknowledge that the link may contain sensitive content').'</p>
                                        </div>
                                        <div class="form-check form-switch ms-auto">
                                            <input class="form-check-input" type="checkbox" data-binary="true" name="data[\'+slug(did)+\'][sensitive]" value="1" data-toggle="togglefield" data-toggle-for="sensitivemessage" \'+(content && content[\'sensitive\'] && content[\'sensitive\'] ==\'1\' ? \'checked\' : \'\')+\'>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3 \'+(content && content[\'sensitive\'] && content[\'sensitive\'] ==\'1\' ? \'\' : \'d-none\')+\'">
                                        <label class="form-label">'.self::e('Custom Message').'</label>
                                        <textarea class="form-control" name="data[\'+slug(did)+\'][sensitivemessage]" id="sensitivemessage">\'+(content && content[\'sensitivemessage\'] ? content[\'sensitivemessage\'] : \'\')+\'</textarea>
                                    </div>
                                    <div class="d-flex">
                                        <div>
                                            <label class="form-check-label fw-bold">'.self::e('Subscribe').'</label>
                                            <p class="form-text">'.self::e('Visitors must subscribe before being redirected').'</p>
                                        </div>
                                        <div class="form-check form-switch ms-auto">
                                            <input class="form-check-input" type="checkbox" data-binary="true" name="data[\'+slug(did)+\'][subscribe]" value="1" \'+(content && content[\'subscribe\'] && content[\'subscribe\'] ==\'1\' ? \'checked\' : \'\')+\'>
                                        </div>
                                    </div>
                                </div>' : '').'
                            </div>
                        </div>
                    </div>
                </div>';
    }
    /**
     * Tagline Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Content
     * @version 7.2
     * @return string
     */
    public static function taglineSetup(){

        $type = 'tagline';

        return "function fntagline(el, content = null, did = null){

            if($('[data-id=bio-tag]').length > 0) {
                $.notify({
                    message: '".self::e('You already have a tagline widget.')."'
                },{
                    type: 'danger',
                    placement: {
                        from: 'top',
                        align: 'right'
                    },
                });
                $('#contentModal .btn-close').click();
                return false;
            }

            if(content){
                var text = content['text'];
            } else {
                var text = '';
            }

            if(!did) did = 'tagline';

            if(did == 'bio-tag') did = 'tagline';

            let html = '".self::format(self::generateTemplate('<div class="form-group">
                        <input type="text" class="form-control p-2" name="data['.$type.'][text]" placeholder="e.g. My Bio Page" value="\'+text+\'">
                    </div>', $type))."';

            $('#linkcontent').prepend(html);
        }";
    }
    /**
     * Save Tagline
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $value
     * @return void
     */
    public static function taglineSave($request, $profiledata, $data){

        $data['active'] = $data['active'] == '1' ? 1 : 0;
        $data['text'] = clean($data['text']);

        return $data;
    }
    /**
     * Tagline Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $value
     * @return void
     */
    public static function taglineBlock($id, $value){

        if(!$value) return;

        if(isset($value['active']) && !$value['active']) return;

        if(isset($value['text']) && !empty($value['text'])){
            return '<p>'.clean($value['text']).'</p>';
        }
    }
    /**
     * Heading Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Content
     * @version 7.2
     * @return string
     */
    public static function headingSetup(){

        $type = 'heading';

        return "function fnheading(el, content = null, did = null){
            var text = '', format, color='#000000';

            if(content){
                var text = content['text'];
                var format = content['format'];
                var color = content['color'];
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div class="row" id="container-\'+did+\'">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Style').'</label>
                                <select name="data[\'+slug(did)+\'][format]" class="form-select mb-2 p-2">
                                    <option value="h1" \'+(format == \'h1\' ? \'selected\':\'\')+\'>H1</option>
                                    <option value="h2" \'+(format == \'h2\' ? \'selected\':\'\')+\'>H2</option>
                                    <option value="h3" \'+(format == \'h3\' ? \'selected\':\'\')+\'>H3</option>
                                    <option value="h4" \'+(format == \'h4\' ? \'selected\':\'\')+\'>H4</option>
                                    <option value="h5" \'+(format == \'h5\' ? \'selected\':\'\')+\'>H5</option>
                                    <option value="h6" \'+(format == \'h6\' ? \'selected\':\'\')+\'>H6</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Text').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][text]" placeholder="e.g. Bio Page" value="\'+text+\'">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mt-3">
                                <label class="form-label fw-bold d-block mb-2">'.self::e('Color').'</label>
                                <input type="color" name="data[\'+slug(did)+\'][color]" value="\'+color+\'" class="form-control p-2">
                            </div>
                        </div>
                    </div>', $type))."';

            $('#linkcontent').append(html);

            $('[data-id='+did+'] [type=color]').spectrum({
                color: color,
                showInput: true,
                preferredFormat: 'hex',
                move: function (color) { Color('#'+did, color, $(this)); },
                hide: function (color) { Color('#'+did, color, $(this)); saveBio();}
            });
        }";
    }
    /**
     * Save Heading
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function headingSave($request, $profiledata, $data){

        $data['active'] = $data['active'] == '1' ? 1 : 0;
        $data['format'] = in_array($data['format'], ['h1','h2','h3','h4','h5','h6']) ? $data['format'] : 'h1';
        $data['text'] = clean($data['text']);

        $color = str_replace('#', '', $data['color']);
        $data['color'] = ctype_xdigit($color) && strlen($color) == 6 ? "#{$color}" : "#000000";

        return $data;
    }
    /**
     * Heading Block
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @param mixed $id
     * @param array $value
     * @return string
     */
    public static function headingBlock($id, $value){

        if(in_array($value['format'], ['h1','h2','h3','h4','h5','h6'])){
            return '<'.$value['format'].' style="color:'.($value['color'] ?? 'inherit').' !important">'.$value['text'].'</'.$value['format'].'>';
        }else{
            return '<h1>'.$value['text'].'</h1>';
        }
    }
    /**
     * Divider
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function dividerSetup(){

        $type = 'divider';

        return "function fndivider(el, content = null, did = null){

            if(content){
                var color = content['color'];
                var style = content['style'];
                var height = content['height'];
            } else {
                var color = '#000000';
                var style = 'solid';
                var height = 2;
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                            <div class="form-group mb-2">
                                <label class="form-label fw-bold">'.self::e('Style').'</label>
                                <select name="data[\'+slug(did)+\'][style]" class="form-select mb-2 p-2">
                                    <option value="solid" \'+(style == \'solid\' ? \'selected\':\'\')+\'>'.self::e('Solid').'</option>
                                    <option value="dotted" \'+(style == \'dotted\' ? \'selected\':\'\')+\'>'.self::e('Dotted').'</option>
                                    <option value="dashed" \'+(style == \'dashed\' ? \'selected\':\'\')+\'>'.self::e('Dashed').'</option>
                                    <option value="double" \'+(style == \'double\' ? \'selected\':\'\')+\'>'.self::e('Double').'</option>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-label fw-bold">'.self::e('Height').'</label>
                                <input type="range" min="1" max="10" class="form-range mt-2" name="data[\'+slug(did)+\'][height]" placeholder="e.g. 5" value="\'+height+\'">
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-label fw-bold d-block mb-2">'.self::e('Color').'</label>
                                <input type="color" name="data[\'+slug(did)+\'][color]" value="\'+color+\'" class="form-control p-2">
                            </div>
                    </div>', $type))."';

            $('#linkcontent').append(html);

            $('[data-id='+did+'] [type=color]').spectrum({
                color: color,
                showInput: true,
                preferredFormat: 'hex',
                move: function (color) { Color('#'+did, color, $(this)); },
                hide: function (color) { Color('#'+did, color, $(this)); saveBio();}
            });
        }";
    }
    /**
     * Save Divider
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function dividerSave($request, $profiledata, $data){

        $data['active'] = $data['active'] == '1' ? 1 : 0;
        $data['style'] = in_array($data['style'], ['solid', 'dotted', 'dashed', 'double']) ? $data['style'] : 'solid';
        $data['height'] = is_numeric($data['height']) && $data['height'] > 1 && $data['height'] < 10 ? $data['height'] : 3;

        $color = str_replace('#', '', $data['color']);
        $data['color'] = ctype_xdigit($color) && strlen($color) == 6 ? "#{$color}" : "#000000";

        return $data;
    }
    /**
     * Divider Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param string $id
     * @param array $value
     * @return void
     */
    public static function dividerBlock($id, $value){

        if(!isset($value['height']) || !$value['height'] || !is_numeric($value['height']) || $value['height'] < 1 || $value['height'] > 10) $value['height'] = 2;

        if(!isset($value['style']) || !$value['style'] || !in_array($value['style'], ['solid', 'dotted', 'dashed', 'double'])) $value['style'] = 'solid';

        if(!isset($value['color']) || !$value['color'] || !ctype_xdigit(str_replace('#', '', $value['color']))) $value['style'] = '#000000';

        return '<hr style="background:transparent;border-top-style:'.$value['style'].' !important;border-top-width:'.$value['height'].'px !important;border-top-color:'.$value['color'].' !important;">';
    }
    /**
     * Text Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return string
     */
    public static function textSetup(){

        $type = 'text';

        return "function fntext(el, content = null, did = null){

            if(content){
                var text = content['text'];
            } else {
                var text = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                        <div class="form-group">
                            <textarea id="\'+did+\'_editor" class="form-control p-2" name="data[\'+did+\'][text]" placeholder="e.g. some description here">\'+text+\'</textarea>
                        </div>
                    </div>', $type))."';

            $('#linkcontent').append(html);
            $('#'+did+'_editor').summernote({
                toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['link','ul', 'ol', 'paragraph']],
                ],
                height: 150
            });
            $('#container-'+did+' .note-editable').blur(function(){
                saveBio();
            });
        }";
    }
    /**
     * Save Text
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function textSave($request, $profiledata, $data){

        $data['active'] = $data['active'] == '1' ? 1 : 0;

        if(strlen(clean($data['text'])) > 2000) throw new Exception(e('{b} Error: Text is too long.', null, ['b' => e('Text')]));

        $data['text'] =  Helper::clean($data['text'], 3, false, '<strong><i><a><b><u><img><iframe><ul><ol><li><p><span>');

        return $data;
    }
    /**
     * Text Block
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @param string $id
     * @param array $value
     * @return void
     */
    public static function textBlock($id, $value){
        return $value['text'];
    }
    /**
     * Link Block Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function linkSetup(){

        $type = 'link';

        return "function fnlink(el, content = null, did = null){

            var text = '', link = '', animation = '', icon = '', urlid = null, clicks = 0, opennew = 0;

            if(content){
                var text = content['text'];
                var icon = content['icon'];
                var animation = content['animation'];
                var link = content['link'];
                var urlid = content['urlid'];
                var clicks = content['clicks'];
                var opennew = content['opennew'];
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Icon').'</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="\'+did+\'_icon_preview"><i class="\'+icon+\'"></i></span>
                                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][icon]" id="\'+did+\'_icon" placeholder="e.g. fab fa-twitter" value="\'+icon+\'">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Text').'</label>
                                <input type="text" class="form-control p-2 text" name="data[\'+slug(did)+\'][text]" value="\'+text+\'" placeholder="e.g. My Site">
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="form-group">
                            <div class="d-flex">
                                <label class="form-label fw-bold">'.self::e('Link').'</label>
                                <div class="form-check form-switch ms-auto">
                                    <input class="form-check-input" type="checkbox" data-binary="true" id="data[\'+slug(did)+\'][opennew]" name="data[\'+slug(did)+\'][opennew]" value="1"\'+(opennew == 1 ? \'checked\': \'\')+\'>
                                    <label class="form-check-label fw-bold" for="data[\'+slug(did)+\'][opennew]">'.self::e('New window').'</label>
                                </div>
                            </div>
                            <input type="text" class="form-control p-2 text" name="data[\'+slug(did)+\'][link]" value="\'+link+\'" placeholder="e.g. https://google.com">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Animation').'</label>
                                <select name="data[\'+slug(did)+\'][animation]" class="animation form-select mb-2 p-2">
                                <option value="none" \'+(animation == \'none\' ? \'selected\':\'\')+\'>'.self::e('None').'</option>
                                <option value="shake" \'+(animation == \'shake\' ? \'selected\':\'\')+\'>'.self::e('Shake').'</option>
                                <option value="scale" \'+(animation == \'scale\' ? \'selected\':\'\')+\'>'.self::e('Scale').'</option>
                                <option value="jello" \'+(animation == \'jello\' ? \'selected\':\'\')+\'>'.self::e('Jello').'</option>
                                <option value="vibrate" \'+(animation == \'vibrate\' ? \'selected\':\'\')+\'>'.self::e('Vibrate').'</option>
                                <option value="wobble" \'+(animation == \'wobble\' ? \'selected\':\'\')+\'>'.self::e('Wobble').'</option>
                            </select>
                        </div>
                    </div>
                  </div>
                </div>', $type))."';

            $('#linkcontent').append(html);

            $('#'+did+'_icon').iconpicker();

            $('#'+did+'_icon').on('iconpickerSelected', function(){
                $('#'+did+'_icon_preview i').attr('class', $(this).val());
                saveBio();
            });

            $('#'+did+'_link').change(function(e){
                if($(this).val() == ''){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save Link
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function linkSave($request, $profiledata, $data){

        $data['active'] = $data['active'] == '1' ? 1 : 0;

        $data['opennew'] = $data['opennew'] == '1' ? 1 : 0;

        $data['sensitive'] = $data['sensitive'] == '1' ? 1 : 0;

        $data['subscribe'] = $data['subscribe'] == '1' ? 1 : 0;

        $data['animation'] = in_array($data['animation'], ['shake','wobble','vibrate','jello','scale']) ? $data['animation'] : 'none';

        $data['icon'] = clean($data['icon']);

        $data['sensitivemessage'] = Helper::clean($data['sensitivemessage'], 3);

        if($data['sensitive'] && $data['subscribe']) throw new Exception(e('{b} Error: You can either Sensitive Content or Subscribe gate but not both.', null, ['b' => e('Link')]));

        $user = Auth::user();

        $profileid = $request->segment(3);

        $self = new self();
        
        if($data['link']){
            if(isset($data['urlid'])){

                $currenturl = DB::url()->where('userid', $user->rID())->where('id', $data['urlid'])->first();

                if(!$currenturl){

                    if(
                        $self->domainBlacklisted($data['link']) ||
                        $self->wordBlacklisted($data['link']) ||
                        !$self->safe($data['link']) ||
                        $self->phish($data['link']) ||
                        $self->virus($data['link'])
                    ) {
                        throw new Exception(e('{b} Error: This link cannot be accepted because either it is invalid or it might not be safe.', null, ['b' => e('Link')]));
                    }

                    $newlink = DB::url()->create();
                    $newlink->url = Helper::clean($data['link'], 3);
                    $newlink->userid = $user->rID();
                    $newlink->alias = null;
                    $newlink->custom = null;
                    $newlink->date = Helper::dtime();
                    $newlink->profileid = $profileid;
                    $newlink->save();
                    $data['urlid'] = $newlink->id;

                }else{

                    if(
                        $self->domainBlacklisted($data['link']) ||
                        $self->wordBlacklisted($data['link']) ||
                        !$self->safe($data['link']) ||
                        $self->phish($data['link']) ||
                        $self->virus($data['link'])
                    ) {
                        throw new Exception(e('{b} Error: This link cannot be accepted because either it is invalid or it might not be safe.', null, ['b' => e('Link')]));
                    }

                    $currenturl->url = Helper::clean($data['link'], 3);

                    if(!$currenturl->profileid) {
                        $currenturl->date = Helper::dtime();
                        $currenturl->profileid = $profileid;
                    }

                    $currenturl->save();
                }

            }else {

                if(
                    $self->domainBlacklisted($data['link']) ||
                    $self->wordBlacklisted($data['link']) ||
                    !$self->safe($data['link']) ||
                    $self->phish($data['link']) ||
                    $self->virus($data['link'])
                ) {
                    throw new Exception(e('{b} Error: This link cannot be accepted because either it is invalid or it might not be safe.', null, ['b' => e('Link')]));
                }

                $newlink = DB::url()->create();
                $newlink->url = Helper::clean($data['link'], 3);
                $newlink->userid = $user->rID();
                $newlink->alias = null;
                $newlink->custom = null;
                $newlink->date = Helper::dtime();
                $newlink->profileid = $profileid;
                $newlink->save();
                $data['urlid'] = $newlink->id;
            }
        }

        return $data;
    }
    /**
     * Process Links
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @return void
     */
    public static function linkProcessor($block, $profile, $url, $user){

        $request = request();

        if($request->isPost()){
            if($request->action == "clicked" && $request->blockid && is_numeric($request->blockid)){

                \Gem::addMiddleware('BlockBot');

                if($link = \Core\DB::url()->where('id', $request->blockid)->first()){
                    (new BioWidgets)->updateStats($request, $link, $user);
                    return Response::factory('success')->exit();
                } else {
                    return Response::factory('error')->exit();
                }
            }

            if($request->action == "newslettergate"){

                if(!$request->email || !$request->validate($request->email, 'email')) return Response::factory(['error' => true, 'message' => e('Please enter a valid email.')])->json();

                $resp = json_decode($profile->responses, true);

                if(!isset($resp['newsletter']) || !in_array($request->email, $resp['newsletter'])){
                    $resp['newsletter'][] = clean($request->email);

                    $profile->responses = json_encode($resp);
                    $profile->save();
                }

                \Gem::addMiddleware('BlockBot');

                if($link = \Core\DB::url()->where('id', $request->blockid)->first()){
                    (new BioWidgets)->updateStats($request, $link, $user);
                    Response::factory(['error' => false, 'message' => null, 'html' => "<script>window.location = '".$block['link']."';</script>"])->json();
                    exit;
                } else {
                    Response::factory(['error' => true, 'message' => e('An error occurred. Please try again.')])->json();
                    exit;
                }
            }
        }
    }
    /**
     * Link Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param string $id
     * @param array $value
     * @return void
     */
    public static function linkBlock($id, $value){

        $value['animation'] = isset($value['animation']) && in_array($value['animation'], ['shake','wobble','vibrate','jello','scale']) ? ' animate_'.$value['animation'] : '';

        if(isset($value['sensitive']) && $value['sensitive']){
            return '<a href="#" data-toggle="modal" data-bs-toggle="modal" data-target="#modal-'.$id.'" data-bs-target="#modal-'.$id.'" class="btn btn-block p-3 mb-2 d-block btn-custom position-relative'.$value['animation'].'">
                '.($value['icon'] ?? '' ? '<i class="'.$value['icon'].' position-absolute start-0 left-0 ms-3 ml-3"></i>' : '').'
                <span class="align-top">'.$value['text'].'</span>
            </a>
            <div class="modal fade" id="modal-'.$id.'" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <button type="button" class="btn-close close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            '.($value['sensitivemessage'] ?? e('This link may contain inappropriate content not suitable for all ages.')).'
                        </div>
                        <div class="modal-body">
                            <a href="'.$value['link'].'" '.(isset($value['opennew']) && $value['opennew'] ? 'target="_blank"' : '').' rel="nofollow" data-blockid="'.$value['urlid'].'" class="btn btn-dark text-white rounded-pill w-100 d-block py-2">'.self::e('Continue').'</a>
                        </div>
                    </div>
                </div>
            </div>';
        }

        if(isset($value['subscribe']) && $value['subscribe']){
            return '<a href="#" data-toggle="modal" data-bs-toggle="modal" data-target="#modal-'.$id.'" data-bs-target="#modal-'.$id.'" class="btn btn-block p-3 mb-2 d-block btn-custom position-relative'.$value['animation'].'">
                '.($value['icon'] ?? '' ? '<i class="'.$value['icon'].' position-absolute start-0 left-0 ms-3 ml-3"></i>' : '').'
                <span class="align-top">'.$value['text'].'</span>
            </a>
            <div class="modal fade" id="modal-'.$id.'" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-bolder">'.self::e('Subscribe to unlock').'</h5>
                            <button type="button" class="btn-close close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="" data-trigger="server-form">
                                <div class="form-group position-relative mb-2">
                                    <input type="email" class="form-control p-3" name="email" placeholder="'.self::e('Please enter a valid email').'" data-error="'.self::e('Please enter a valid email').'" required>
                                    <input type="hidden" name="action" value="newslettergate">
                                    <input type="hidden" name="blockid" value="'.$value['urlid'].'">
                                    <input type="hidden" name="target" value="'.$value['link'].'">
                                    <button type="submit" class="btn btn-secondary btn-sm position-absolute top-50 right-0 end-0 translate-middle-y btn-dark me-2 mr-2">'.self::e('Subscribe').'</button>
                                </div>
                            </form>
                            <span class="text-muted text-start text-left d-block">'.self::e('By subscribing, I agree to the terms and conditions and privacy policy.').'</span>
                        </div>
                    </div>
                </div>
            </div>';
        }

        return '<a href="'.$value['link'].'" '.(isset($value['opennew']) && $value['opennew'] ? 'target="_blank"' : '').' rel="nofollow" data-blockid="'.$value['urlid'].'" class="btn btn-block p-3 mb-2 d-block btn-custom position-relative'.$value['animation'].'">
            '.($value['icon'] ?? '' ? '<i class="'.$value['icon'].' position-absolute start-0 left-0 ms-3 ml-3"></i>' : '').'
            <span class="align-top">'.$value['text'].'</span>
        </a>';
    }
    /**
     * Whatsapp Call Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function whatsappcallSetup(){

        $type = 'whatsappcall';

        return "function fnwhatsappcall(el, content = null, did = null){

            var label = '', phone = '';

            if(content){
                var label = content['label'];
                var phone = content['phone'];
            }


            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div class="row" id="container-\'+did+\'">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Phone').'</label>
                        <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][phone]" placeholder="e.g. +123456789" value="\'+phone+\'">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Label').'</label>
                        <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][label]" placeholder="e.g. Call us" value="\'+label+\'">
                    </div>
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);
        }";
    }
    /**
     * Save Whatsapp
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function whatsappcallSave($request, $profiledata, $data){

        $data['active'] = $data['active'] == '1' ? 1 : 0;

        $data['phone'] = clean($data['phone']);

        $data['label'] = Helper::clean($data['label'], 3);

        return $data;
    }
    /**
     * Whatsapp Calls Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function whatsappcallBlock($id, $value){
        return '<a href="https://wa.me/'.(str_replace([' ', '-'], '', $value['phone'])).'" class="btn btn-block d-block p-3 btn-custom position-relative"><img src="'.assets('images/whatsapp.svg').'" height="26" class="ms-3 ml-3 position-absolute left-0 start-0"> '.(isset($value['label']) && $value['label'] ? $value['label'] : $value['phone']).'</a>';
    }
    /**
     * Whatsapp Messages Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function whatsappSetup(){

        $type = 'whatsapp';

        return "function fnwhatsapp(el, content = null, did = null){

            var label = '', phone = '', message='';

            if(content){
                var label = content['label'];
                var phone = content['phone'];
                var message = content['message'];
            }


            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div class="row" id="container-\'+did+\'">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Phone').'</label>
                        <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][phone]" placeholder="e.g. +123456789" value="\'+phone+\'">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Label').'</label>
                        <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][label]" placeholder="e.g. Call us" value="\'+label+\'">
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Message').'</label>
                        <textarea class="form-control p-2" name="data[\'+slug(did)+\'][message]" placeholder="">\'+message+\'</textarea>
                    </div>
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);
        }";
    }
    /**
     * Save Whatsapp
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function whatsappSave($request, $profiledata, $data){

        $data['active'] = $data['active'] == '1' ? 1 : 0;

        if(strlen(clean($data['message'])) > 1000) throw new Exception(e('{b} Error: Text is too long.', null, ['b' => e('Whatsapp Message')]));

        $data['phone'] = clean($data['phone']);

        $data['label'] = Helper::clean($data['label'], 3);

        $data['message'] = Helper::clean($data['message'], 3);

        return $data;
    }
    /**
     * Whatsapp Message
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function whatsappBlock($id, $value){
        return '<a href="https://wa.me/'.(str_replace([' ', '-'], '', $value['phone'])).'?text='.urlencode(clean($value['message'], 3)).'" class="btn btn-block d-block p-3 btn-custom position-relative"><img src="'.assets('images/whatsapp.svg').'" height="26" class="ms-3 position-absolute start-0 ml-3 left-0"> '.(isset($value['label']) && $value['label'] ? $value['label'] : $value['phone']).'</a>';
    }
    /**
     * Call Phone Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function phoneSetup(){

        $type = 'phone';

        return "function fnphone(el, content = null, did = null){

            var label = '', phone = '';

            if(content){
                var label = content['label'];
                var phone = content['phone'];
            }


            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div class="row" id="container-\'+did+\'">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Phone').'</label>
                        <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][phone]" placeholder="e.g. +123456789" value="\'+phone+\'">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Label').'</label>
                        <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][label]" placeholder="e.g. Call us" value="\'+label+\'">
                    </div>
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);
        }";
    }
    /**
     * Save Phone Call
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function phoneSave($request, $profiledata, $data){

        $data['active'] = $data['active'] == '1' ? 1 : 0;

        $data['phone'] = clean($data['phone']);

        $data['label'] = Helper::clean($data['label'], 3);

        return $data;
    }
    /**
     * Phone Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function phoneBlock($id, $value){
        return '<a href="tel:'.(str_replace([' ', '-'], '', $value['phone'])).'" class="btn btn-block d-block p-3 btn-custom position-relative"><i class="fa fa-phone ms-3 position-absolute start-0 ml-3 left-0"></i> '.(isset($value['label']) && $value['label'] ? $value['label'] : $value['phone']).'</a>';
    }
    /**
     * Spotify Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function spotifySetup(){

        $type = 'spotify';

        return "function fnspotify(el, content = null, did = null){
            let regex = /^https:\/\/open.spotify.com\/(track|playlist|episode|album)\/([a-zA-Z0-9]+)(.*)$/i;

            var link = '';
            if(content){
                var link = content['link'];
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Link').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://open.spotify.com/..." value="\'+link+\'">
                    <p class="form-text">'.self::e('You can add a link to a spotify song, a playlist or a podcast.').'</p>
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid Spotify track, playlist or podcast link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Spotify Save
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function spotifySave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !preg_match("/^https:\/\/open.spotify.com\/(track|playlist|episode|album)\/([a-zA-Z0-9]+)(.*)$/i", $data['link'])) {
            throw new Exception(e('Please enter a valid Spotify track, playlist or podcast link'));
        }

        return $data;
    }
    /**
     * Spotify Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function spotifyBlock($id, $value){

        if(empty($value['link'])) return;

        preg_match("/^https:\/\/open.spotify.com\/(track|playlist|episode|album)\/([a-zA-Z0-9]+)(.*)$/i", $value['link'], $match);
        
        if(isset($match[1])){
            if($match[1] == 'playlist'){
                $link = str_replace('/playlist/', '/embed/playlist/', $value['link']);
            }elseif($match[1] == 'episode'){
                $link = str_replace('/episode/', '/embed/episode/', $value['link']);
            }elseif($match[1] == 'album'){
                $link = str_replace('/album/', '/embed/album/', $value['link']);
            }else{
                $link = str_replace('/track/', '/embed/track/', $value['link']);
            }
        }
        return '<iframe width="100%" height="152" style="aspect-ratio: 16/9;" src="'.$link.'" class="rounded rounded-4 btn-custom"></iframe>';
    }
    /**
     * iTunes / Apple Music Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function itunesSetup(){

        $type = 'itunes';

        return "function fnitunes(el, content = null, did = null){

            let regex = /^https:\/\/music.apple.com\/(.*)/i;

            var link = '';
            if(content){
                var link = content['link'];
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Link').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://music.apple.com/..." value="\'+link+\'">
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid Apple Music link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Itunes Save
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function itunesSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !preg_match("/^https:\/\/music.apple.com\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid Apple Music link'));
        }

        return $data;
    }
    /**
     * Apple Music Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function itunesBlock($id, $value){
        $link = str_replace('music.apple', 'embed.music.apple', $value['link']);
        return '<iframe width="100%" height="450" style="aspect-ratio: 16/9;" src="'.$link.'" class="rounded rounded-4 btn-custom"></iframe>';
    }
    /**
     * PayPal Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function paypalSetup(){

        $type = 'paypal';
        $list = '';

        foreach (\Helpers\App::currency() as $code => $info){
            $list .= '<option value="'.$code.'"  \'+(currency == \''.$code.'\' ? \'selected\':\'\')+\'>'.$code.' - '.$info["label"].'</option>';
        }

        return "function fnpaypal(el, content = null, did = null){

            if(content){
                var label = content['label'];
                var email = content['email'];
                var amount = content['amount'];
                var currency = content['currency'];
            } else {
                var label = '';
                var email = '';
                var amount = '';
                var currency = '';
            }
            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">'.self::e('Label').'</label>
                            <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][label]" placeholder="e.g. Purchase Course" value="\'+label+\'">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">'.self::e('Email').'</label>
                            <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][email]" placeholder="e.g. mybusiness@email.com" value="\'+email+\'">
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">'.self::e('Amount').'</label>
                            <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][amount]" placeholder="e.g. 9.99" value="\'+amount+\'">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold d-block mb-2">'.self::e('Currency').'</label>
                            <div class="input-group input-select rounded">
                                <select name="data[\'+slug(did)+\'][currency]" class="form-select mb-2 p-2" data-toggle="select">
                                    '.$list.'
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);
        }";
    }
    /**
     * Save Paypal
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function paypalSave($request, $profiledata, $data){

        $data['label'] = Helper::clean($data['label'], 3);

        $data['email'] = Helper::clean($data['email'], 3);

        $data['amount'] = (double) Helper::clean($data['amount'], 3);

        if($data['email'] && !Helper::Email($data['email'])) throw new Exception(e('Please enter a valid email'));

        $data['currency'] = strtoupper($data['currency']);

        return $data;
    }
    /**
     * Paypal Widget
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function paypalBlock($id, $value){
        return '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">

            <input type="hidden" name="business" value="'.$value['email'].'">

            <input type="hidden" name="cmd" value="_xclick">

            <input type="hidden" name="item_name" value="'.$value['label'].'">
            <input type="hidden" name="amount" value="'.$value['amount'].'">
            <input type="hidden" name="currency_code" value="'.$value['currency'].'">

            <button type="submit" name="submit" class="btn btn-block d-block p-3 btn-custom w-100">'.$value['label'].'</button>
        </form>';
    }
    /**
     * Tiktok Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function tiktokSetup(){

        $type = 'tiktok';

        return "function fntiktok(el, content = null, did = null){
            let regex = /^https?:\/\/(?:www|m)\.(?:tiktok.com)\/(.*)\/video\/(.*)/i;

            var link = '';
            if(content){
                var link = content['link'];
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Link').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://tiktok.com/..." value="\'+link+\'">
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid TikTok video link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * TikTok Save
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function tiktokSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !preg_match("/^https?:\/\/(?:www|m)\.(?:tiktok.com)\/(.*)\/video\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid TikTok video link'));
        }

        return $data;
    }
    /**
     * Tiktok Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function tiktokBlock($id, $value){
        $tid = explode('/', $value['link']);
        $tid = end($tid);
        return '<blockquote class="tiktok-embed rounded btn-custom" cite="'.$value['link'].'" data-video-id="'.$tid.'" style="max-width: 660px;min-width: 325px;"><section></section></blockquote> <script async src="https://www.tiktok.com/embed.js"></script>';
    }
    /**
     * Tiktok Profile Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function tiktokprofileSetup(){

        $type = 'tiktokprofile';

        return "function fntiktokprofile(el, content = null, did = null){
            let regex = /^https?:\/\/(?:www|m)\.(?:tiktok.com)\/@(.*)/i;

            var link = '';
            if(content){
                var link = content['link'];
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Link').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://tiktok.com/..." value="\'+link+\'">
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid TikTok profile link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save Tiktok
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function tiktokprofileSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !preg_match("/^https?:\/\/(?:www|m)\.(?:tiktok.com)\/@(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid TikTok profile link'));
        }

        return $data;
    }
    /**
     * Threads Profile Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param string $id
     * @param array $value
     * @return void
     */
    public static function tiktokprofileBlock($id, $value){
        return '<blockquote class="tiktok-embed btn-custom rounded" cite="'.$value['link'].'" data-unique-id="'.str_replace('https://www.tiktok.com/@', '', $value['link']).'" data-embed-type="creator" style="max-width: 660px; min-width: 288px;"><section><a target="_blank" href="'.$value['link'].'?refer=creator_embed">@'.str_replace('https://www.tiktok.com/@', '', $value['link']).'</a> </section> </blockquote> <script async src="https://www.tiktok.com/embed.js"></script>';
    }
    /**
     * Youtube Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function youtubeSetup(){

        $type = 'youtube';

        return "function fnyoutube(el, content = null, did = null){
            let regex = /http(?:s?):\/\/(?:www\.)?youtu(?:be\.com\/(watch|playlist)\?(v|list)=|\.be\/)([\w\-\_]*)(&(amp;)?‌​[\w\?‌​=]*)?/i;

            var link = '';
            if(content){
                var link = content['link'];
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Link').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://youtube.com/watch/..." value="\'+link+\'">
                    <p class="form-text">'.self::e('You can add a link to a video or a playlist.').'</p>
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid Youtube video or playlist link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Youtube Save
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function youtubeSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !preg_match("/http(?:s?):\/\/(?:www\.)?youtu(?:be\.com\/(watch|playlist)\?(v|list)=|\.be\/)([\w\-\_]*)(&(amp;)?‌​[\w\?‌​=]*)?/i", $data['link'])) {
            throw new Exception(e('Please enter a valid Youtube video or playlist link'));
        }

        return $data;
    }
    /**
     * Youtube Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function youtubeBlock($id, $value){

        if(empty($value['link'])) return false;

        preg_match("/http(?:s?):\/\/(?:www\.)?youtu(?:be\.com\/(watch|playlist)\?(v|list)=|\.be\/)([\w\-\_]*)(&(amp;)?‌​[\w\?‌​=]*)?/i", $value['link'], $match);
        if(isset($match[1])){
            if($match[1] == 'playlist'){
                $link = 'https://www.youtube.com/embed/videoseries?list='.$match[3];
            }elseif($match[1] == 'watch') {
                $link = 'https://www.youtube.com/embed/'.$match[3];
            }else {
                $link = 'https://www.youtube.com/embed/'.$match[3];
            }
        }
        return '<iframe width="100%" height="315" style="aspect-ratio: 16/9;" src="'.$link.'" class="rounded btn-custom"></iframe>';
    }
    /**
     * RSS Feed
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function rssSetup(){

        $type = 'rss';

        return "function fnrss(el, content = null, did = null){

            let regex = /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:[/?#]\S*)?$/i;

            var link = '';
            if(content){
                var link = content['link'];
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Link').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://mysite.com/rss" value="\'+link+\'">
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid RSS Feed link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Undocumented function
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function rssSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !Helper::isURL($data['link'])) throw new Exception(e('Please enter a valid RSS Feed link'));

        if($data['link'] && \Helpers\App::rss($data['link']) == 'Invalid RSS') throw new Exception(e('Please enter a valid RSS Feed link'));

        return $data;
    }
    /**
     * RSS Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param string $id
     * @param array $value
     * @return void
     */
    public static function rssBlock($id, $value){

        $items = \Helpers\App::rss($value['link']);

        $html ='<div class="rss card card-body overflow-auto btn-custom rounded">';
            if(!is_array($items)){
                $html .= $items;
            }else {
                foreach($items as $item){
                    $html .='<div class="media mb-3 text-start text-left">
                        '.(isset($item['image']) && $item['image'] ? '<img class="me-3" src="'.Helper::clean($item['image'], 3).'" alt="'.Helper::clean($item['title'], 3).'">':'').'
                        <div class="media-body">
                            <h6 class="mt-3 fw-bolder"><a href="'.Helper::clean($item['link'], 3).'" target="_blank">'.Helper::clean($item['title'], 3).'</a></h6>
                            '.Helper::clean($item['description'], 3).'
                        </div>
                    </div>';
                }
            }
        $html.='</div>';
        return $html;
    }
    /**
     * Image
     *
     * @author GemPixel <https://gempixel.com>
     * @category Content
     * @version 7.2
     * @return void
     */
    public static function imageSetup(){

        $type = 'image';

        return "function fnimage(el, content = null, did = null){

            if(content){
                var link = content['link'];
                var link2 = content['link2'];
            } else {
                var link = '';
                var link2 = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Link').'</label>
                                <input type="text" class="form-control p-2" id="link-\'+slug(did)+\'" name="data[\'+slug(did)+\'][link]" value="\'+link+\'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold d-block">'.self::e('File').' \'+(content && content[\'image\'] ? \'<span class="float-end"><input type="checkbox" name="data[\'+slug(did)+\'][removeimage]" value="1" class="me-1" id="remove-\'+slug(did)+\'"><span class="align-text-bottom">'.self::e('Remove').'</span></span></label>\':\'\')+\'</label>
                                <input type="file" class="form-control p-2" name="\'+slug(did)+\'" accept=".jpg, .png">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Link').'</label>
                                <input type="text" class="form-control p-2" id="link2-\'+slug(did)+\'" name="data[\'+slug(did)+\'][link2]" value="\'+link2+\'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold d-block">'.self::e('File').' \'+(content && content[\'image2\'] ? \'<span class="float-end"><input type="checkbox" id="link-\'+slug(did)+\'"  name="data[\'+slug(did)+\'][removeimage2]" value="1" class="me-1" id="remove2-\'+slug(did)+\'"><span class="align-text-bottom">'.self::e('Remove').'</span></span></label>\':\'\')+\'</label>
                                <input type="file" class="form-control p-2" name="\'+slug(did)+\'-2" accept=".jpg, .png">
                            </div>
                        </div>
                    </div>
                </div>', $type))."';

            $('#linkcontent').append(html);

            if($('#remove-'+slug(did)).is(':checked')){
                $(this).prop('checked', false).removeAttr('checked');
                $('#link-'+slug(did)).val('');
            }

            if($('#remove2-'+slug(did)).is(':checked')){
                $(this).prop('checked', false).removeAttr('checked');
                $('#link2-'+slug(did)).val('');
            }
        }";
    }
    /**
     * Image Save
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function imageSave($request, $profiledata, $data){

        $appConfig = appConfig('app');
        $sizes = $appConfig['sizes'];
        $extensions = $appConfig['extensions'];

        $key = $data['id'];        

        if($image = $request->file($key)){

            if(!$image->mimematch || !in_array($image->ext, $extensions['bio']['image']) || $image->sizekb > $sizes['bio']['image']) {
                throw new Exception(e('Image must be either a PNG or a JPEG (Max {s}kb).', null, ['s' =>  $sizes['bio']['image']]));
            }

            $filename = "profile_imagetype".Helper::rand(6).str_replace(' ', '-', $image->name);

            $request->move($image, $appConfig['storage']['profile']['path'], $filename);

            if(isset($profiledata['links'][$key]['image']) && $profiledata['links'][$key]['image']){
                App::delete($appConfig['storage']['profile']['path'].'/'.$profiledata['links'][$key]['image']);
            }

            $data['image'] = $filename;

        } else {
            if(isset($profiledata['links'][$key]['image'])) $data['image'] = $profiledata['links'][$key]['image'];
        }

        if($image = $request->file($key.'-2')){

            if(!$image->mimematch || !in_array($image->ext, $extensions['bio']['image']) || $image->sizekb > $sizes['bio']['avatar']){
                throw new Exception(e('Image must be either a PNG or a JPEG (Max {s}kb).', null, ['s' =>  $sizes['bio']['image']]));
            }

            $filename = "profile_imagetype".Helper::rand(6).str_replace(' ', '-', $image->name);

            $request->move($image, $appConfig['storage']['profile']['path'], $filename);

            if(isset($profiledata['links'][$key]['image2']) && $profiledata['links'][$key]['image2']){
                App::delete($appConfig['storage']['profile']['path'].'/'.$profiledata['links'][$key]['image2']);
            }

            $data['image2'] = $filename;

        } else {
            if(isset($profiledata['links'][$key]['image2'])) $data['image2'] = $profiledata['links'][$key]['image2'];
        }

        if(isset($data['removeimage']) && $data['removeimage']){
            if(isset($profiledata['links'][$key]['image']) && $profiledata['links'][$key]['image']){
                App::delete($appConfig['storage']['profile']['path'].'/'.$profiledata['links'][$key]['image']);
            }
            $data['image'] = '';
            $data['link'] = '';
        }

        if(isset($data['removeimage2']) && $data['removeimage2']){
            if(isset($profiledata['links'][$key]['image2']) && $profiledata['links'][$key]['image2']){
                App::delete($appConfig['storage']['profile']['path'].'/'.$profiledata['links'][$key]['image2']);
            }
            $data['image2'] = '';
            $data['link2'] = '';
        }

        return $data;
    }
    /**
     * Image Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function imageBlock($id, $value){

        if(!isset($value['image']) || !$value['image']) return;

        if(isset($value['image2']) && $value['image2']){
            return '<div class="row">
                <div class="col-6">
                    '.($value['link'] ? '
                        <a href="'.$value['link'].'" target="_blank" rel="nofollow"><img src="'.uploads($value['image'], 'profile').'" class="img-responsive img-fluid rounded w-100"></a>
                    ' : '
                        <img src="'.uploads($value['image'], 'profile').'" class="img-responsive img-fluid rounded w-100">
                    ').'
                </div>
                <div class="col-6">
                    '.(isset($value['link2']) && $value['link2'] ? '
                        <a href="'.$value['link2'].'" target="_blank" rel="nofollow"><img src="'.uploads($value['image2'], 'profile').'" class="img-responsive img-fluid rounded w-100"></a>
                    ' : '
                        <img src="'.uploads($value['image2'], 'profile').'" class="img-responsive img-fluid rounded w-100">
                    ').'
                </div>
            </div>';
        }else{
            if($value['link']){
                return '<a href="'.$value['link'].'" target="_blank" rel="nofollow"><img src="'.uploads($value['image'], 'profile').'" class="img-responsive img-fluid rounded w-100"></a>';
            } else {
                return '<img src="'.uploads($value['image'], 'profile').'" class="img-responsive img-fluid rounded w-100">';
            }
        }
    }
    /**
     * Newsletter
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function newsletterSetup(){

        $type = 'newsletter';

        return "function fnnewsletter(el, content = null, did = null){

            if(content){
                var text = content['text'];
            } else {
                var text = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Text').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][text]" value="\'+text+\'" placeholder="e.g. Subscribe">
                            </div>
                        </div>
                    </div>
                </div>', $type))."';

            $('#linkcontent').append(html);
        }";
    }
    /**
     * Save Newsletter
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function newsletterSave($request, $profiledata, $data){

        $data['text'] = clean($data['text']);

        return $data;
    }
    /**
     * Newsletter Processor
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function newsletterProcessor($block, $profile, $url, $user){

        $request = request();

        if($request->isPost()){

            if($request->action == 'newsletter'){

                if(!$request->email || !$request->validate($request->email, 'email')) return back()->with('danger', e('Please enter a valid email.'));

                $resp = json_decode($profile->responses, true);

                if(!isset($resp['newsletter']) || !in_array($request->email, $resp['newsletter'])){
                    $resp['newsletter'][] = clean($request->email);

                    $profile->responses = json_encode($resp);
                    $profile->save();
                }
                return back()->with('success', e('You have been successfully subscribed.'));
            }
        }
    }
    /**
     * Newsletter Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function newsletterBlock($id, $value){

        return '<a href="#" data-bs-target="#N'.$id.'" data-bs-toggle="collapse" data-target="#N'.$id.'" data-toggle="collapse"  role="button" class="btn btn-block p-3 d-block btn-custom position-relative fa-animated collapsed">
                    <span class="align-top">'.($value['text'] ?? e('Subscribe')).'</span>
                    <i class="fa fa-chevron-down position-absolute end-0 me-3 right-0 mr-3"></i>
                </a>
                <form method="post" action="" class="collapse" id="N'.$id.'">
                    <div class="d-flex align-items-center btn-custom rounded p-3 mt-4">
                        <div class="flex-fill me-1 mr-1">
                            <input type="email" class="form-control border-0 bg-white p-2" name="email" placeholder="e.g. johnsmith@company.com" required>
                        </div>
                        <div class="ms-auto">
                            <button type="submit" class="btn btn-dark p-2">'.($value['text'] ?? e('Subscribe')).'</button>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="newsletter">
                    <input type="hidden" name="blockid" value="'.$id.'">
                </form>';
    }
    /**
     * Contact Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function contactSetup(){

        $type = 'contact';

        return "function fncontact(el, content = null, did = null){

            if(content){
                var text = content['text'];
                var email = content['email'];
            } else {
                var text = '';
                var email = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Text').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][text]" value="\'+text+\'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Email').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][email]" value="\'+email+\'">
                            </div>
                        </div>
                    </div>
                </div>', $type))."';

            $('#linkcontent').append(html);
        }";
    }
    /**
     * Save Contact
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function contactSave($request, $profiledata, $data){

        $data['text'] = clean($data['text']);
        $data['email'] = clean($data['email']);

        if($data['email'] && !Helper::Email($data['email'])) throw new Exception(e('Please enter a valid email'));

        return $data;
    }
    /**
     * Contact Form Processor
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function contactProcessor($block, $profile, $url, $user){

        $request = request();

        if($request->isPost()){

            if($request->action == 'contact'){

                \Gem::addMiddleware('ValidateCaptcha');

                if(!$request->email || !$request->validate($request->email, 'email')) return back()->with('danger', e('Please enter a valid email.'));

                $profiledata = json_decode($profile->data, true);

                $data = $profiledata['links'][$request->blockid];
                $message = clean($request->message);
                $email = clean($request->email);
                $page = \Helpers\App::shortRoute($url->domain??null, $profile->alias);

                Plugin::dispatch('profile.contacted', [$message, $email, $page]);

                Emails::setup()
                        ->replyto([Helper::RequestClean($request->email)])
                        ->to($block['email'])
                        ->send([
                            'subject' => '['.config('title').'] You were contacted from your Bio Page: '.$profile->name,
                            'message' => function($template, $block) use ($message, $email, $page){
                                if(config('logo')){
                                    $title = '<img align="center" border="0" class="center autowidth" src="'.uploads(config('logo')).'" style="text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 166px; display: block;" width="166"/>';
                                } else {
                                    $title = '<h3>'.config('title').'</h3>';
                                }

                                return \Core\Email::parse($template, ['content' => "<p>You have received an email from <strong>{$email}</strong> sent via the Bio Page {$page}.</p><strong>Message:</strong><br><p>{$message}</p>", 'brand' => $title]);
                            }
                        ]);

                return back()->with('success', e('Message sent successfully.'));
            }
        }
    }
    /**
     * Contact Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function contactBlock($id, $value){

        return '<a href="#" data-bs-target="#C'.$id.'" data-bs-toggle="collapse" data-target="#C'.$id.'" data-toggle="collapse"  role="button" class="btn btn-block p-3 d-block btn-custom position-relative fa-animated collapsed">
                    <span class="align-top">'.$value['text'].'</span>
                    <i class="fa fa-chevron-down position-absolute end-0 me-3 right-0 mr-3"></i>
                </a>
                <form method="post" action="#" id="C'.$id.'" class="btn-custom border-0 collapse rounded rounded-3 text-start text-left p-3 mt-3">
                    <div class="form-group mb-3">
                        <label for="email" class="form-label fw-bold">'.self::e('Email').'</label>
                        <input type="text" class="form-control" name="email" placeholder="johnsmith@company.com" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email" class="form-label fw-bold">'.self::e('Message').'</label>
                        <textarea class="form-control" name="message"></textarea>
                    </div>
                    '.csrf().'
                    <input type="hidden" name="action" value="contact">
                    <input type="hidden" name="blockid" value="'.$id.'">
                    '.\Helpers\Captcha::display().'
                    <button type="submit" class="btn btn-dark d-block">'.self::e('Send').'</button>
                </form>';
    }
    /**
     * FAQS Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function faqsSetup(){

        $type = 'faqs';

        return "function fnfaqs(el, content = null, did = null){

            if(content){
                var question = content['question'];
                var answer = content['answer'];
            } else {
                var question = [];
                var answer = [];
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                    <div class="faq-container">\';
                        question.forEach(function(value, i){
                            html += \'<div class="faq-holder row mt-2">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold">'.self::e('Question').'</label>
                                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][question][]" value="\'+value+\'">
                                                <button type="button" data-trigger="deletefaq" class="btn btn-sm btn-danger mt-1">'.self::e('Delete').'</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                            <label class="form-label fw-bold">'.self::e('Answer').'</label>
                                            <textarea class="form-control p-2" name="data[\'+slug(did)+\'][answer][]">\'+answer[i]+\'</textarea>
                                        </div>
                                    </div>
                                </div>\';
                        });
                html += \'</div>
                    <button type="button" data-trigger="addfaq" class="btn btn-success mt-3">'.self::e('Add FAQ').'</button>
                </div>', $type))."';

            $('#linkcontent').append(html);

            $('[data-trigger=addfaq]').click(function(e){
                e.preventDefault();
                $('#container-'+did+' button[data-trigger=addfaq]').before('".self::format('<div class="faq-holder row mt-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">'.self::e('Question').'</label>
                            <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][question][]" value="">
                            <button type="button" data-trigger="deletefaq" class="btn btn-sm btn-danger mt-1">'.self::e('Delete').'</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">'.self::e('Answer').'</label>
                            <textarea class="form-control p-2" name="data[\'+slug(did)+\'][answer][]"></textarea>
                        </div>
                    </div>
                </div>')."');
              });
              $(document).on('click','[data-trigger=deletefaq]', function(e){
                    e.preventDefault();
                    $(this).parents('.faq-holder').fadeOut('fast', function(){
                        $(this).remove();
                        saveBio();
                    })
              });
        }";
    }
    /**
     * Save Faq
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function faqsSave($request, $profiledata, $data){

        $data['question'] = isset($data['question']) && $data['question'] ? array_map('clean', $data['question']) : [];
        $data['answer'] = isset($data['answer']) && $data['answer'] ? array_map('clean', $data['answer']) : [];

        return $data;
    }
    /**
     * FAQS Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param string $id
     * @param array $value
     * @return void
     */
    public static function faqsBlock($id, $value){

        if(!isset($value['question'])) return;

        $html = '<div class="btn-custom card d-block border-0 mb-2 faqs">';
        foreach($value['question'] as $i => $question){
            $html .='<div class="card-body text-start text-left">
                <a href="#faq-'.$i.'" class="collapsed fa-animated" data-bs-toggle="collapse" data-toggle="collapse" data-target="#faq-'.$i.'" data-bs-target="#faq-'.$i.'">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="fa fa-chevron-down me-2"></i>
                        <span class="align-middle">'.$question.'</span>
                    </h6>
                </a>
                <div class="collapse pt-3" id="faq-'.$i.'">
                    '.$value['answer'][$i].'
                </div>
            </div>';
        }
        $html .='</div>';

        return $html;
    }
    /**
     * vCard Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function vcardSetup(){

        $type = 'vcard';

        $list = '';
        foreach (\Core\Helper::Country(false) as $country){
            $list .= '<option value="'.$country.'"  \'+(country == \''.$country.'\' ? \'selected\':\'\')+\'>'.$country.'</option>';
        }

        return "function fnvcard(el, content = null, did = null){

            if(content){
                var button = content['button'];
                var fname = content['fname'];
                var lname = content['lname'];
                var phone = content['phone'];
                var cell = content['cell'];
                var fax = content['fax'];
                var email = content['email'];
                var company = content['company'];
                var address = content['address'];
                var city = content['city'];
                var state = content['state'];
                var country = content['country'];
                var zip = content['zip'];
                var site = content['site'];
            } else {
                var button = '';
                var fname = '';
                var lname = '';
                var phone = '';
                var cell = '';
                var fax = '';
                var company = '';
                var email = '';
                var address = '';
                var city = '';
                var state = '';
                var country = '';
                var site = '';
                var zip = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('First Name').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][fname]" value="\'+fname+\'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Last Name').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][lname]" value="\'+lname+\'">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Email').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][email]" value="\'+email+\'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Phone').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][phone]" value="\'+phone+\'">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Cellphone').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][cell]" value="\'+cell+\'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Fax').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][fax]" value="\'+fax+\'">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Company').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][company]" value="\'+company+\'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Site').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][site]" value="\'+site+\'">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Address').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][address]" value="\'+address+\'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('City').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][city]" value="\'+city+\'">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Zip').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][zip]" value="\'+zip+\'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">'.self::e('Country').'</label>
                                <select class="form-select p-2" name="data[\'+slug(did)+\'][city]">
                                    '.$list.'
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Label').'</label>
                        <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][button]" value="\'+button+\'">
                    </div>
                </div>', $type))."';

            $('#linkcontent').append(html);
        }";
    }
    /**
     * vCard Save
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function vCardSave($request, $profiledata, $data){
        return array_map('clean', $data);
    }
    /**
     * vCard Processor
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function vcardProcessor($block, $profile, $url, $user){

        $request = request();

        if($request->isPost()){
            if($request->action == 'vcard'){

                $vcard = "BEGIN:VCARD\r\nVERSION:3.0\r\n";

                if((isset($block['fname']) && $block['fname']) && (isset($block['lname']) && $block['lname'])){
                    $vcard .= "N:{$block['lname']};{$block['fname']}\r\n";
                }
                if(isset($block['company']) && $block['company']){
                    $vcard .= "ORG:{$block['company']}\r\n";
                }

                if(isset($block['phone']) && $block['phone']){
                    $vcard .= "TEL;TYPE=work,voice:{$block['phone']}\r\n";
                }
                if(isset($block['cell']) && $block['cell']){
                    $vcard .= "TEL;TYPE=cell,voice:{$block['cell']}\r\n";
                }
                if(isset($block['fax']) && $block['fax']){
                    $vcard .= "TEL;TYPE=fax:{$block['fax']}\r\n";
                }

                if(isset($block['email']) && $block['email']){
                    $vcard .= "EMAIL;TYPE=INTERNET;TYPE=WORK;TYPE=PREF:{$block['email']}\r\n";
                }

                if(isset($block['site']) && $block['site']){
                    $vcard .= "URL;TYPE=work:{$block['site']}\r\n";
                }
                if(isset($block['address']) && isset($block['city']) && isset($block['state']) && isset($block['country'])){
                    $vcard .= "ADR;TYPE=work:;;{$block['address']};{$block['city']};{$block['state']};{$block['country']}\r\n";
                }

                $vcard .= "\r\nREV:" . date("Ymd") . "T195243Z\r\nEND:VCARD";

                return \Core\File::contentDownload('vcard.vcf', function() use ($vcard){
                    echo $vcard;
                });
            }
        }
    }
    /**
     * vCard Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function vcardBlock($id, $value){

        return '<form method="post" action="?downloadvcard">
                    '.csrf().'
                    <input type="hidden" name="action" value="vcard">
                    <input type="hidden" name="blockid" value="'.$id.'">
                    <button type="submit" class="btn btn-custom btn-block d-block w-100 p-3">'.(isset($value['button']) && !empty($value['button']) ? $value['button'] : e('Download vCard')).'</button>
                </form>';
    }
    /**
     * Product Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function productSetup(){

        $type = 'product';

        return "function fnproduct(el, content = null, did = null){

            if(content){
                var text = content['name'];
                var description = content['description'];
                var amount = content['amount'];
                var link = content['link'];
            } else {
                var text = '';
                var description = '';
                var amount = '';
                var link = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label class="form-label fw-bold">'.self::e('Name').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][name]" placeholder="e.g. Product" value="\'+text+\'">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label fw-bold">'.self::e('Description').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][description]" placeholder="e.g. Product description."  value="\'+description+\'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label fw-bold">'.self::e('Amount').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][amount]" placeholder="e.g. $9.99" value="\'+amount+\'">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label fw-bold">'.self::e('Image').'</label>
                                <input type="file" class="form-control p-2" name="\'+slug(did)+\'" accept=".jpg, .png">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label fw-bold">'.self::e('Links').'</label>
                                <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" value="\'+link+\'" placeholder="http://">
                            </div>
                        </div>
                    </div>
                </div>', $type))."';

            $('#linkcontent').append(html);
        }";
    }
    /**
     * Save Product
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function productSave($request, $profiledata, $data){

        $appConfig = appConfig('app');
        $sizes = $appConfig['sizes'];
        $extensions = $appConfig['extensions'];

        $key = $data['id'];

        if($image = $request->file($key)){
            if(!$image->mimematch || !in_array($image->ext, $extensions['bio']['image']) || $image->sizekb > $sizes['bio']['avatar']){
                throw new Exception(e('Image must be either a PNG or a JPEG (Max {s}kb).', null, ['s' => $sizes['bio']['avatar']]));
            }

            $filename = "profile_producttype".Helper::rand(6).str_replace(' ', '-', $image->name);

            $request->move($image, $appConfig['storage']['profile']['path'], $filename);
            if(isset($profiledata['links'][$key]['image']) && $profiledata['links'][$key]['image']){
                App::delete($appConfig['storage']['profile']['path'].'/'.$profiledata['links'][$key]['image']);
            }

            $data['image'] = $filename;
        } else {
            $data['image'] = $profiledata['links'][$key]['image'];
        }

        return $data;
    }
    /**
     * Product Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function productBlock($id, $value){
        return '<a href="'.$value['link'].'" target="_blank" class="d-block btn-custom rounded rounded-3 p-3 text-start text-left" rel="nofollow">
                <div class="d-flex align-items-center">
                    '.(isset($value['image']) && $value['image'] ? '
                    <div class="mr-3 me-3">
                        <img src="'.uploads($value['image'], 'profile').'" class="rounded" style="max-width: 130px">
                    </div>
                    ' : '').'
                    <div class="text-left text-start">
                        <h3 class="mb-1">'.$value['name'].'</h3>
                        <strong>'.$value['amount'].'</strong>
                        <p class="mb-0">'.$value['description'].'</p>
                    </div>
                </div>
            </a>';
    }
    /**
     * HTML Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function htmlSetup(){

        $type = 'html';

        return "function fnhtml(el, content = null, did = null){

            if(content){
                var code = content['html'];
            } else {
                var code = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div class="row" id="container-\'+did+\'">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('HTML').'</label>
                        <textarea class="form-control p-2" name="data[\'+slug(did)+\'][html]" placeholder="e.g. some description here">\'+code+\'</textarea>
                    </div>
                </div>', $type))."';

            $('#linkcontent').append(html);
        }";
    }
    /**
     * Save HTML
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profieldata
     * @param [type] $data
     * @return void
     */
    public static function htmlSave($request, $profieldata, $data){
        $data['html'] = Helper::clean($data['html'], 3, false, '<strong><i><a><b><u><img><iframe><ul><ol><li><p><span>');
        return $data;
    }
    /**
     * HTML Widget
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function htmlBlock($id, $value){
        return $value['html'];
    }
    /**
     * OpenSea NFT Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function openseaSetup(){

        $type = 'opensea';

        return "function fnopensea(el, content = null, did = null){

            let regex = /^https?:\/\/(www.)?(opensea.io)\/assets\/(.*)\/(.*)\/(.*)/i;

            var link = '';
            if(content){
                var link = content['link'];
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Link').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://opensea.io/assets/..." value="\'+link+\'">
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid OpenSea NFT link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save OpenSea
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function openseaSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !preg_match("/^https?:\/\/(www.)?(opensea.io)\/assets\/(.*)\/(.*)\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid OpenSea NFT link'));
        }

        return $data;
    }
    /**
     * Opensea Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function openseaBlock($id, $value){

        if(empty($value['link'])) return;

        preg_match("/^https?:\/\/(www.)?(opensea.io)\/assets\/(.*)\/(.*)\/(.*)/i", $value['link'], $match);
        return '<nft-card width="100%" contractAddress="'.$match[4].' ?>" tokenId="'.$match[5].' ?>"> </nft-card><script src="https://unpkg.com/embeddable-nfts/dist/nft-card.min.js"></script>';
    }
    /**
     * Twitter Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function twitterSetup(){

        $type = 'twitter';

        return "function fntwitter(el, content = null, did = null){

            let regex = /^https?:\/\/(www.)?(twitter.com)\/(.*)/i;

            if(content){
                var link = content['link'];
                var amount = content['amount'];
            } else {
                var link = '';
                var amount = 1;
            }
            if(!parseInt(amount)) amount = 1;

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div class="row" id="container-\'+did+\'">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">'.self::e('Link').'</label>
                            <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://twitter.com/..." value="\'+link+\'">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">'.self::e('Amount').'</label>
                            <input type="number" class="form-control p-2" name="data[\'+slug(did)+\'][amount]" value="\'+amount+\'" placeholder="e.g. 2">
                        </div>
                    </div>
                </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid Tweet link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save Twitter
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function twitterSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        $data['amount'] = (int) $data['amount'];

        if($data['link'] && !preg_match("/^https?:\/\/(www.)?(twitter.com)\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid Tweet link'));
        }

        return $data;
    }
    /**
     * Twitter Widget
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function twitterBlock($id, $value){
        if(!isset($value['amount']) || !$value['amount'] || !is_numeric($value['amount']) || $value['amount'] < 1) $value['amount'] = 1;

        return '<a class="twitter-timeline" data-width="100%" data-tweet-limit="'.$value['amount'].'" href="'.$value['link'].'" data-chrome="nofooter">Tweets</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>';
    }
    /**
     * Soundcloud Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function soundcloudSetup(){

        $type = 'soundcloud';

        return "function fnsoundcloud(el, content = null, did = null){

            let regex = /^https?:\/\/(www.)?(soundcloud.com)\/(.*)/i;

            if(content){
                var link = content['link'];
            } else {
                var link = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Link').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://soundcloud.com/..." value="\'+link+\'">
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid SoundCloud link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save Soundcloud
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function soundcloudSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !preg_match("/^https?:\/\/(www.)?(soundcloud.com)\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid SoundCloud link'));
        }

        return $data;
    }
    /**
     * Soundcloud Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function soundcloudBlock($id, $value){
        return '<iframe width="100%" height="166" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url='.urlencode($value['link']).'&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true"></iframe>';
    }
    /**
     * Facebook Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function facebookSetup(){

        $type = 'facebook';

        return "function fnfacebook(el, content = null, did = null){

            let regex = /^https?:\/\/(www.)?(((.*).)?facebook.com)\/(.*)/i;

            if(content){
                var link = content['link'];
            } else {
                var link = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Link').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://facebook.com/..." value="\'+link+\'">
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid Facebook Post link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save Facebook Post
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function facebookSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !preg_match("/^https?:\/\/(www.)?(((.*).)?facebook.com)\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid Facebook Post link'));
        }

        return $data;
    }
    /**
     * Facebook Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function facebookBlock($id, $value){
        
        if(!$value['link'] || empty($value['link'])) return;

        return '<div id="fb-root"></div><script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v14.0" nonce="WaCixDC1"></script><div class="fb-post" data-href="'.$value['link'].'" data-show-text="true"></div>';
    }
    /**
     * Instagram Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function instagramSetup(){

        $type = 'instagram';

        return "function fninstagram(el, content = null, did = null){

            let regex = /^https?:\/\/(www.)?(((.*).)?instagram.com)\/(.*)/i;

            if(content){
                var link = content['link'];
            } else {
                var link = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Link').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://instagram.com/..." value="\'+link+\'">
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid Instagram Post link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save Instagram
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function instagramSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !preg_match("/^https?:\/\/(www.)?(((.*).)?instagram.com)\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid Instagram Post link'));
        }

        return $data;
    }
    /**
     * Instagram Widget
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function instagramBlock($id, $value){
        return '<blockquote class="instagram-media" data-instgrm-permalink="'.$value['link'].'" data-instgrm-version="14" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"></blockquote><script async src="//www.instagram.com/embed.js"></script>';
    }
    /**
     * Typeform Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function typeformSetup(){

        $type = 'typeform';

        return "function fntypeform(el, content = null, did = null){

            let regex = /^https?:\/\/(www.)?((.*).typeform.com)\/to\/(.*)/i;

            if(content){
                var name = content['name'];
                var link = content['link'];
            } else {
                var name = '';
                var link = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div class="row" id="container-\'+did+\'">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Label').'</label>
                        <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][name]" placeholder="e.g. Survey" value="\'+name+\'">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Link').'</label>
                        <input type="url" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://XXXXXX.typeform.com/to/XXXXXX" value="\'+link+\'">
                    </div>
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=url]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid Typeform link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save Typeform
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function typeformSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !preg_match("/^https?:\/\/(www.)?((.*).typeform.com)\/to\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid Typeform link'));
        }

        return $data;
    }
    /**
     * Typeform Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param string $id
     * @param array $value
     * @return void
     */
    public static function typeformBlock($id, $value){

        preg_match("/^https?:\/\/(www.)?((.*).typeform.com)\/to\/(.*)/i", $value['link'], $match);
        $typeformid = end($match);

        return '<a href="#" class="btn btn-block d-block p-3 btn-custom position-relative" data-toggle="modal" data-bs-toggle="modal" data-target="#modal-'.$id.'" data-bs-target="#modal-'.$id.'">'.(isset($value['name']) && $value['name'] ? $value['name'] : 'Typeform').'</a>
            <div class="modal fade" id="modal-'.$id.'" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <button type="button" class="btn-close close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div data-tf-widget="'.$typeformid.'"></div>
                            <script src="//embed.typeform.com/next/embed.js"></script>
                        </div>
                        <div class="modal-body">
                            <a href="'.$value['link'].'" class="btn btn-dark text-white rounded-pill w-100 d-block py-2" rel="nofollow" target="_blank">'.self::e('Open in a new tab').'</a>
                        </div>
                    </div>
                </div>
            </div>';
    }
    /**
     * Pinterest Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function pinterestSetup(){

        $type = 'pinterest';

        return "function fnpinterest(el, content = null, did = null){

            let regex = /^https?:\/\/(www.)?(((.*).)?pinterest.com)\/(.*)/i;

            if(content){
                var name = content['name'];
                var link = content['link'];
            } else {
                var name = '';
                var link = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div class="row" id="container-\'+did+\'">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Label').'</label>
                        <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][name]" placeholder="e.g. My Board" value="\'+name+\'">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Link').'</label>
                        <input type="url" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://pinterest.com/..." value="\'+link+\'">
                    </div>
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=url]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid Pinterest link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save Pinterest
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function pinterestSave($request, $profiledata, $data){

        $data['link'] = trim(clean($data['link']), '/');
        $data['name'] = Helper::clean($data['name'], 3);

        if($data['link'] && !preg_match("/^https?:\/\/(www.)?(((.*).)?pinterest.(com|ca|co.uk))\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid Pinterest link'));
        }

        return $data;
    }
    /**
     * Pintereset Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @return void
     */
    public static function pinterestBlock($id, $value){

        return '<a href="#" class="btn btn-block d-block p-3 btn-custom position-relative" data-toggle="modal" data-target="#modal-'.$id.'" data-bs-toggle="modal" data-bs-target="#modal-'.$id.'">'.(isset($value['name']) && $value['name'] ? $value['name'] : 'Pinterest Board').'</a>
        <div class="modal fade" id="modal-'.$id.'" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="sensitiveModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <script type="text/javascript" async defer src="//assets.pinterest.com/js/pinit.js"></script>
                        <a data-pin-do="embedUser" data-pin-board-width="400" data-pin-scale-height="320" data-pin-scale-width="80" href="'.$value['link'].'"></a>
                    </div>
                </div>
            </div>
        </div>';
    }
    /**
     * Reddit Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function redditSetup(){

        $type = 'reddit';

        return "function fnreddit(el, content = null, did = null){

            let regex = /^https?:\/\/(www.)?((.*).)?reddit.com\/user\/(.*)/i;

            if(content){
                var name = content['name'];
                var link = content['link'];
            } else {
                var name = '';
                var link = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div class="row" id="container-\'+did+\'">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Label').'</label>
                        <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][name]" placeholder="e.g. My profile" value="\'+name+\'">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Link').'</label>
                        <input type="url" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://www.reddit.com/user/...." value="\'+link+\'">
                    </div>
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=url]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid Reddit link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Reddit Save
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function redditSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);
        $data['name'] = Helper::clean($data['name'], 3);

        if($data['link'] && !preg_match("/^https?:\/\/(www.)?((.*).)?reddit.com\/user\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid Reddit link'));
        }

        return $data;
    }
    /**
     * Reddit Widget Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param string $id
     * @param array $value
     * @return void
     */
    public static function redditBlock($id, $value){

        preg_match("/^https?:\/\/(www.)?((.*).)?reddit.com\/user\/(.*)/i", $value['link'], $match);

        $json = \Core\Http::url('https://www.reddit.com/user/'.trim(end($match), '/').'/about.json')
        ->with('user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36')->get();

        $user = $json->bodyObject();

        $html = '<a href="#" class="btn btn-block d-block p-3 btn-custom position-relative" data-bs-toggle="modal" data-bs-target="#modal-'.$id.'" data-toggle="modal" data-target="#modal-'.$id.'">'.(isset($value['name']) && $value['name'] ? $value['name'] : 'Reddit').'</a>
        <div class="modal fade" id="modal-'.$id.'" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="sensitiveModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">';
                        if(isset($user->data)){
                            $user = $user->data;
                            $html .='<div class="text-center">
                                <img src="'.$user->icon_img.'" class="img-responsive rounded-3 mb-2" width="100">
                                <h4 class="mb-0 text-dark">'.$user->subreddit->title.'</h4>
                                <small class="text-muted">'.str_replace('_', '/', $user->subreddit->display_name).'</small>
                                <div class="border p-3 mt-3 rounded text-start text-left">
                                    <p class="text-dark">'.self::e('Karma').' <span class="float-end float-right fw-bold font-weight-bold">'.$user->total_karma.'</span></p>
                                    <p class="text-dark mb-0">'.self::e('Member since').' <span class="float-end fw-bold float-right font-weight-bold">'.date('d F, Y', $user->created).'</span></p>
                                </div>
                                <a href="'.$value['link'].'" class="btn btn-dark text-white mt-3 d-block">'.self::e('Visit Profile').'</a>
                            </div>';
                        }else {
                            $html .='An error occurred';
                        }
            $html .='</div>
                </div>
            </div>
        </div>';

        return $html;
    }
    /**
     * Calendly Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @return void
     */
    public static function calendlySetup(){

        $type = 'calendly';

        return "function fncalendly(el, content = null, did = null){

            let regex = /^https?:\/\/(www.)?(((.*).)?calendly.com)\/(.*)/i;

            if(content){
                var name = content['name'];
                var link = content['link'];
            } else {
                var name = '';
                var link = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div class="row" id="container-\'+did+\'">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Label').'</label>
                        <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][name]" placeholder="e.g. Book an appointment" value="\'+name+\'">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">'.self::e('Link').'</label>
                        <input type="url" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://www.calendly.com/..." value="\'+link+\'">
                    </div>
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=url]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid Calendly link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save Calendly
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function calendlySave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);
        $data['name'] = Helper::clean($data['name'], 3);

        if($data['link'] && !preg_match("/^https?:\/\/(www.)?(((.*).)?calendly.com)\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid Calendly link'));
        }

        return $data;
    }
    /**
     * Calendly Processor
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function calendlyProcessor($id, $value){
        View::push('https://assets.calendly.com/assets/external/widget.css', 'css')->toHeader();
        View::push('https://assets.calendly.com/assets/external/widget.js', 'script')->toFooter();
    }
    /**
     * Calendly Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param string $id
     * @param array $value
     * @return void
     */
    public static function calendlyBlock($id, $value){

        return '<a href="#" class="btn btn-block d-block p-3 btn-custom position-relative" onclick="Calendly.initPopupWidget({url: \''.$value['link'].'\'});return false;">'.(isset($value['name']) && $value['name'] ? $value['name'] : 'Calendly').'</a>';
    }
    /**
     * Threads Widget Setup
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function threadsSetup(){

        $type = 'threads';

        return "function fnthreads(el, content = null, did = null){

            let regex = /^https?:\/\/(www.)?(((.*).)?threads.net)\/(.*)\/post\/(.*)/i;

            if(content){
                var link = content['link'];
            } else {
                var link = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Link').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://threads.net/post/..." value="\'+link+\'">
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid Threads post link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save Threads
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function threadsSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !preg_match("/^https?:\/\/(www.)?(((.*).)?threads.net)\/(.*)\/post\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid Threads post link'));
        }

        return $data;
    }
    /**
     * Threads Widget Block
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param string $id
     * @param array $value
     * @return void
     */
    public static function threadsBlock($id, $value){

        return '<blockquote class="text-post-media btn-custom" data-text-post-permalink="'.$value['link'].'" data-text-post-version="0" id="ig-tp-Cvk_NVnyZV9" style=" background:#FFF; border-width: 1px; border-style: solid; border-color: #00000026; border-radius: 16px; max-width:660px; margin: 1px; min-width:270px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"> <a href="'.$value['link'].'" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%; font-family: -apple-system, BlinkMacSystemFont, sans-serif;" target="_blank"> <div style=" padding: 40px; display: flex; flex-direction: column; align-items: center;"><div style=" display:block; height:32px; width:32px; padding-bottom:20px;"> <svg aria-label="Threads" height="32px" role="img" viewBox="0 0 192 192" width="32px" xmlns="http://www.w3.org/2000/svg"> <path d="M141.537 88.9883C140.71 88.5919 139.87 88.2104 139.019 87.8451C137.537 60.5382 122.616 44.905 97.5619 44.745C97.4484 44.7443 97.3355 44.7443 97.222 44.7443C82.2364 44.7443 69.7731 51.1409 62.102 62.7807L75.881 72.2328C81.6116 63.5383 90.6052 61.6848 97.2286 61.6848C97.3051 61.6848 97.3819 61.6848 97.4576 61.6855C105.707 61.7381 111.932 64.1366 115.961 68.814C118.893 72.2193 120.854 76.925 121.825 82.8638C114.511 81.6207 106.601 81.2385 98.145 81.7233C74.3247 83.0954 59.0111 96.9879 60.0396 116.292C60.5615 126.084 65.4397 134.508 73.775 140.011C80.8224 144.663 89.899 146.938 99.3323 146.423C111.79 145.74 121.563 140.987 128.381 132.296C133.559 125.696 136.834 117.143 138.28 106.366C144.217 109.949 148.617 114.664 151.047 120.332C155.179 129.967 155.42 145.8 142.501 158.708C131.182 170.016 117.576 174.908 97.0135 175.059C74.2042 174.89 56.9538 167.575 45.7381 153.317C35.2355 139.966 29.8077 120.682 29.6052 96C29.8077 71.3178 35.2355 52.0336 45.7381 38.6827C56.9538 24.4249 74.2039 17.11 97.0132 16.9405C119.988 17.1113 137.539 24.4614 149.184 38.788C154.894 45.8136 159.199 54.6488 162.037 64.9503L178.184 60.6422C174.744 47.9622 169.331 37.0357 161.965 27.974C147.036 9.60668 125.202 0.195148 97.0695 0H96.9569C68.8816 0.19447 47.2921 9.6418 32.7883 28.0793C19.8819 44.4864 13.2244 67.3157 13.0007 95.9325L13 96L13.0007 96.0675C13.2244 124.684 19.8819 147.514 32.7883 163.921C47.2921 182.358 68.8816 191.806 96.9569 192H97.0695C122.03 191.827 139.624 185.292 154.118 170.811C173.081 151.866 172.51 128.119 166.26 113.541C161.776 103.087 153.227 94.5962 141.537 88.9883ZM98.4405 129.507C88.0005 130.095 77.1544 125.409 76.6196 115.372C76.2232 107.93 81.9158 99.626 99.0812 98.6368C101.047 98.5234 102.976 98.468 104.871 98.468C111.106 98.468 116.939 99.0737 122.242 100.233C120.264 124.935 108.662 128.946 98.4405 129.507Z" /></svg></div><div style=" font-size: 15px; line-height: 21px; color: #000000; font-weight: 600;">View on Threads</div></div></a></blockquote> <script async src="https://www.threads.net/embed.js"></script>';
    }
    /**
     * Google Maps Widget
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function googlemapsSetup(){

        $type = 'googlemaps';

        return "function fngooglemaps(el, content = null, did = null){

            if(content){
                var address = content['address'];
            } else {
                var address = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Address').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][address]" placeholder="e.g. 1 Apple Park Way" value="\'+address+\'">
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);
        }";
    }
    /**
     * Google Maps
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function googlemapsSave($request, $profiledata, $data){

        $data['address'] = clean($data['address']);

        return $data;
    }
    /**
     * Google Maps Blog
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param string $id
     * @param array $value
     * @return void
     */
    public static function googlemapsBlock($id, $value){

        if(!isset($value['address'])) $value['address'] = '';

        return '<iframe src="https://maps.google.com/maps?q='.urlencode($value['address']).'&t=&z=13&ie=UTF8&iwloc=&output=embed" width="100%" height="350" style="border:0;" class="rounded btn-custom" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
    }
    /**
     * Open Table Widget
     *
     * @author GemPixel <https://gempixel.com>
     * @category Widget
     * @version 7.2
     * @return void
     */
    public static function opentableSetup(){

        $type = 'opentable';

        $langlist = '';

        foreach(['en-US' => 'English-US','fr-CA' => 'Français-CA','de-DE' => 'Deutsch-DE','es-MX' => 'Español-MX','ja-JP' => '日本語-JP','nl-NL' => 'Nederlands-NL','it-IT' => 'Italiano-IT'] as $key => $value){
            $langlist .= '<option value="'.$key.'"  \'+(lang == \''.$key.'\' ? \'selected\':\'\')+\'>'.$value.'</option>';
        }

        return "function fnopentable(el, content = null, did = null){

            if(content){
                var id = content['rid'];
                var lang = content['lang'];
            } else {
                var id = '';
                var lang = 'en-US';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">'.self::e('Restaurant ID').'</label>
                            <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][rid]" placeholder="e.g. 12345678" value="\'+id+\'">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">'.self::e('Language').'</label>
                            <select name="data[\'+slug(did)+\'][lang]" class="form-select mb-2 p-2">
                                '.$langlist.'
                            </select>
                        </div>
                    </div>
                </div>
            </div>', $type))."';
            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!parseInt($(this).val())){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid OpenTable restaurant ID')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save OpenTable
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function opentableSave($request, $profiledata, $data){

        if($data['rid'] && !is_numeric($data['rid'])) throw new Exception(e('{b} Error: Please enter a valid ID', null, ['b' => 'Eventbrite']));

        $data['lang'] = clean($data['lang']);

        return $data;
    }
    /**
     * Opentable Widget
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function opentableBlock($id, $value){
        
        if(!isset($value['rid']) || !$value['rid']) return;

        return '<div class="df-opentable rounded btn-custom"><script type="text/javascript" src="//www.opentable.com/widget/reservation/loader?rid='.$value['rid'].'&domain=com&type=standard&theme=standard&lang='.$value['lang'].'&overlay=true&iframe=false&newtab=false"></script></div>';
    }
    /**
     * EventBrite
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @return void
     */
    public static function eventbriteSetup(){

        $type = 'eventbrite';

        return "function fneventbrite(el, content = null, did = null){

            if(content){
                var id = content['eid'];
                var label = content['label'];
            } else {
                var id = '';
                var label = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div class="row" id="container-\'+did+\'">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">'.self::e('Event ID').'</label>
                            <input type="text" class="form-control p-2" id="event-\'+did+\'" name="data[\'+slug(did)+\'][eid]" placeholder="e.g. 12345678" value="\'+id+\'">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">'.self::e('Label').'</label>
                            <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][label]" placeholder="e.g. Book now" value="\'+label+\'">
                        </div>
                    </div>
                </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' #event-'+did+'').change(function(e){
                if(!parseInt($(this).val())){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid EventBrite ID')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Save Eventbrite
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function eventbriteSave($request, $profiledata, $data){

        if($data['eid'] && !is_numeric($data['eid'])) throw new Exception(e('{b} Error: Please enter a valid ID', null, ['b' => 'Evenbrite']));

        $data['label'] = clean($data['label']);

        return $data;
    }
    /**
     * EventBrite Widget
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function eventbriteBlock($id, $value){

        return '<a class="btn btn-custom btn-block d-block w-100 p-3" id="evenbrite-'.$id.'">'.(isset($value['label']) && !empty($value['label']) ? $value['label'] : e('Book now')).'</a>
        <script src="https://www.eventbrite.com/static/widgets/eb_widgets.js"></script>

        <script type="text/javascript">
            window.EBWidgets.createWidget({
                widgetType: \'checkout\',
                eventId: \''.$value['eid'].'\',
                modal: true,
                modalTriggerElementId: \'evenbrite-'.$id.'\'
            });
        </script>';
    }
    /**
     * Snapchat Embed
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @return void
     */
    public static function snapchatSetup(){

        $type = 'snapchat';

        return "function fnsnapchat(el, content = null, did = null){

            let regex = /^https?:\/\/(www.)?(((.*).)?snapchat.com)\/(spotlight|add|lens)\/(.*)/i;

            if(content){
                var link = content['link'];
            } else {
                var link = '';
            }

            if(did == null) did = (Math.random() + 1).toString(36).substring(2);

            let html = '".self::format(self::generateTemplate('<div id="container-\'+did+\'">
                <div class="form-group">
                    <label class="form-label fw-bold">'.self::e('Link').'</label>
                    <input type="text" class="form-control p-2" name="data[\'+slug(did)+\'][link]" placeholder="e.g. https://www.snapchat.com/spotlight/..." value="\'+link+\'">
                    <p class="form-text">'.self::e('Insert a link to a Snapchat Spotlight, Profile or Lens.').'</p>
                </div>
            </div>', $type))."';

            $('#linkcontent').append(html);

            $('#container-'+did+' input[type=text]').change(function(e){
                if(!$(this).val().match(regex)){
                    e.preventDefault();
                    $.notify({
                        message: '".self::e('Please enter a valid Snapchat post link')."'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                    });
                    return false;
                }
            })
        }";
    }
    /**
     * Snapchat Save
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $request
     * @param [type] $profiledata
     * @param [type] $data
     * @return void
     */
    public static function snapchatSave($request, $profiledata, $data){

        $data['link'] = clean($data['link']);

        if($data['link'] && !preg_match("/^https?:\/\/(www.)?(((.*).)?snapchat.com)\/(spotlight|add|lens)\/(.*)/i", $data['link'])) {
            throw new Exception(e('Please enter a valid Snapchat post link'));
        }

        return $data;
    }
    /**
     * Snapchat Widget
     *
     * @author GemPixel <https://gempixel.com>
     * @version 7.2
     * @param [type] $id
     * @param [type] $value
     * @return void
     */
    public static function snapchatBlock($id, $value){
        if(empty($value['link'])) return;

        if(strpos($value['link'], '?') !== 0) $value['link'] = explode('?', $value['link'])[0];

        return '<blockquote class="snapchat-embed" data-snapchat-embed-width="100%" data-snapchat-embed-height="692" data-snapchat-embed-url="'.$value['link'].'/embed" data-snapchat-embed-style="border-radius: 40px;" style="background:#C4C4C4; border:0; border-radius:40px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:416px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px); display: flex; flex-direction: column; position: relative; height:650px;"><div style=" display: flex; flex-direction: row; align-items: center;"><a href="'.$value['link'].'" style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px; margin:16px; cursor: pointer"></a><div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"></div></div><div style="flex: 1;"></div><div style="display: flex; flex-direction: row; align-items: center; border-end-end-radius: 40px; border-end-start-radius: 40px;"><a href="'.$value['link'].'" style="background-color: yellow; width:100%; padding: 10px 20px; border: none; border-radius: inherit; cursor: pointer; text-align: center; display: flex;flex-direction: row;justify-content: center; text-decoration: none; color: black;">View more on Snapchat</a></div></blockquote><script async src="https://www.snapchat.com/embed.js"></script>';
    }
}