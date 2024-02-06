<?php
/**
 * =======================================================================================
 *                           GemFramework (c) GemPixel                                     
 * ---------------------------------------------------------------------------------------
 *  This software is packaged with an exclusive framework as such distribution
 *  or modification of this framework is not allowed before prior consent from
 *  GemPixel. If you find that this framework is packaged in a software not distributed 
 *  by GemPixel or authorized parties, you must not use this software and contact GemPixel
 *  at https://gempixel.com/contact to inform them of this misuse.
 * =======================================================================================
 *
 * @package GemPixel\Premium-URL-Shortener
 * @author GemPixel (https://gempixel.com) 
 * @license https://gempixel.com/licenses
 * @link https://gempixel.com  
 */

use Core\View;
use Core\File;
use Core\Helper;
use Core\Request;
use Core\Response;
use Core\Localization;
use Core\DB;

class Sitemap {

	/**
	 * Track count
	 *
	 * @author GemPixel <https://gempixel.com> 
	 * @version 6.3
	 */
    protected $count = 0;

   /**
     * Generate Site map
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @return void
     */
    public function index(){

		$time = microtime(true);

		$config = appConfig('app.sitemap');

		if(isset($config['enabled']) && $config['enabled'] == false) return stop(404);
            
        $this->setHeader();

		echo $this->url(route('home'), date("c", filemtime(View::$path."/index.php")), 1);

		foreach(DB::page()->findArray() as $page){
            echo $this->url(route('page', [$page['seo']]),  date("c", strtotime($page['lastupdated'])), 0.6);
        }
		if(config('blog')){
			foreach(DB::posts()->where('published', 1)->findArray() as $post){
				echo $this->url(route('blog.post', [$post['slug']]),  date("c", strtotime($post['date'])), 0.8);
			}
			foreach(DB::postcategories()->where('status', 1)->findArray() as $category){
				echo $this->url(route('blog.category', [$category['slug']]),  date("c", strtotime($category['created_at'])), 0.8);
			}
			echo $this->url(route('blog'),  date("c", strtotime($post['date'] ?? date('Y-m-d'))), 0.8);
		}

        if(config('helpcenter')){
			foreach(DB::faqs()->findArray() as $post){
				echo $this->url(route('help.single', [$post['slug']]),  date("c", strtotime($post['created_at'])), 0.8);
			}

			foreach(config('faqcategories') as $id => $category){
				echo $this->url(route('help.category', [$id]),  date("c", strtotime($post['created_at'])), 0.8);
			}
			
			echo $this->url(route('help'),  date("c", strtotime($post['created_at'])));
		} 

		if(config('contact')){
			echo $this->url(route('contact'),  date("c", filemtime(View::$path."/pages/contact.php")));
		}
		
		if(config('report')){
			echo $this->url(route('report'),  date("c", filemtime(View::$path."/pages/contact.php")));
		}

        if(config('privacy')){
            echo $this->url(route('privacy'),  date("c", filemtime(View::$path."/pages/privacy.php")));
        }

        if(config('api')) {
        	echo $this->url(route('apidocs'),  date("c", filemtime(ROOT."/app/config/api.php")));
		}

		if(!isset($config['urls']) || (isset($config['urls']) && $config['urls'])){
			foreach(DB::url()->where('public', '1')->limit($config['numberoflinks'])->orderByDesc('date')->findArray() as $url){
				echo $this->url(\Helpers\App::shortRoute($url['domain'], urlencode($url['alias'].$url['custom'])),  date("c", strtotime($url['date'])), 0.5);
			}
		}
		
		if(isset($config['bio']) && $config['bio']){
			foreach(DB::profiles()->limit($config['numberofbio'])->orderByDesc('created_at')->findArray() as $bio){
				$url = DB::url()->where('id', $bio['urlid'])->first();
				echo $this->url(\Helpers\App::shortRoute($url->domain, urlencode($url->alias.$url->custom)),  date("c", strtotime($url->date)), 0.5);
			}
		}

		$this->setFooter($time);
    }

    /**
	 * [setHeader description]
	 * @author KBRmedia <https://gempixel.com>
	 * @version 1.0
	 */
	protected function setHeader(){
		header('Content-type: application/xml');
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\nhttp://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">";
	}
	/**
	 * [setFooter description]
	 * @author KBRmedia <https://gempixel.com>
	 * @version 1.0
	 */
	protected function setFooter($time){
		echo "\n<!-- Generated {$this->count} urls in ".round((microtime(true) - $time), 3)." seconds-->\n</urlset>";
	}
	/**
	 * [url description]
	 * @author KBRmedia <https://gempixel.com>
	 * @version 1.0
	 * @param   [type] $loc      [description]
	 * @param   [type] $lastmod  [description]
	 * @param   [type] $priority [description]
	 * @return  [type]           [description]
	 */
	protected function url($loc, $lastmod, $priority = 1){
		$this->count++;
		$lastmod = date("c", strtotime($lastmod));
		return "\n\t<url>\n\t\t<loc>$loc</loc>\n\t\t<lastmod>$lastmod</lastmod>\n\t\t<priority>$priority</priority>\n\t</url>";
	}        
}