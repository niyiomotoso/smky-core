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
namespace User;

use Core\Request;
use Core\Response;
use Core\DB;
use Core\Auth;
use Core\Helper;
use Core\View;
use Core\Plugin;
use Models\User;
use Helpers\BioWidgets;
use Helpers\App;

class Bio {

    use \Traits\Links;

    /**
     * Verify Permission
     *
     * @author GemPixel <https://gempixel.com>
     * @version 6.0
     */
    public function __construct(){

        if(User::where('id', Auth::user()->rID())->first()->has('bio') === false){
			return \Models\Plans::notAllowed();
		}
    }
    /**
     * QR Generator
     *
     * @author GemPixel <https://gempixel.com>
     * @version 6.9
     * @param \Core\Request $request
     * @return void
     */
    public function index(Request $request){
        $bios = [];

        $count = DB::profiles()->where('userid', Auth::user()->rID())->count();

        $total = Auth::user()->hasLimit('bio');

        $views = DB::url()->where('userid', Auth::user()->rID())->whereNotNull('profileid')->sum('click');
        
        if(!$views) $views = 0;

        $query = DB::profiles()->where('userid', Auth::user()->rID());

        if(!$request->sort || $request->sort == "latest"){
            $query->orderByDesc('created_at');
        }

        if($request->sort == "old"){
            $query->orderByAsc('created_at');
        }

        if($request->q){
            $query->whereLike('name', '%'.clean($request->q).'%');
        }

        $limit = 14;

        if($request->perpage && is_numeric($request->perpage) && $request->perpage > 14 && $request->perpage <= 100) $limit = $request->perpage;
        
        $user = Auth::user();

        foreach($query->paginate($limit) as $bio){
            $bio->data = json_decode($bio->data);

            if($bio->urlid && $url = DB::url()->where('id', $bio->urlid)->first()){
                $bio->views = $url->click;
                $bio->url =  \Helpers\App::shortRoute($url->domain, $bio->alias);
                $bio->status = $url->status;
            }

            if(isset($bio->data->avatar) && $bio->data->avatar){
                $bio->avatar = uploads($bio->data->avatar, 'profile');
            } else {
                $bio->avatar = $user->avatar();
            }

            $bio->channels = \Core\DB::tochannels()->join(DBprefix.'channels', [DBprefix.'tochannels.channelid' , '=', DBprefix.'channels.id'])->where(DBprefix.'tochannels.itemid', $bio->id)->where('type', 'bio')->findMany();

            $bios[] = $bio;
        }

        
        if(isset($user->profiledata) && $data = json_decode($user->profiledata)){

            if($request->importoldbio == 'true'){
                return $this->importBio();
            }

            View::push('<script>$(".col-md-9").prepend("<div class=\"card\"><div class=\"card-body text-center\">'.e('We have detected that you have an old bio page. Do you want to import it?<br><br><a href=\"?importoldbio=true\" class=\"btn btn-primary\">'.e('Import').'</a>').'</div></div>")</script>', 'custom')->toFooter();
        }

        $domains = [];
        foreach(\Helpers\App::domains() as $domain){
            $domains[] = $domain;
        }


        View::set('title', e('Bio Pages'));

        View::push(assets('frontend/libs/clipboard/dist/clipboard.min.js'), 'js')->toFooter();

        return View::with('bio.index', compact('bios', 'count', 'total', 'domains', 'views'))->extend('layouts.dashboard');
    }
    /**
     * Create a Bio Page
     *
     * @author GemPixel <https://gempixel.com>
     * @version 6.9
     * @param \Core\Request $request
     * @return void
     */
    public function save(Request $request){

        $user = Auth::user();
        // hardcode domain for Bio creation to be linkdom.me
        $request->domain = "https://linkdom.me";

        if(Auth::user()->teamPermission('bio.create') == false){
			return Response::factory(['error' => true, 'message' => e('You do not have this permission. Please contact your team administrator.'), 'token' => csrf_token()])->json();
		}

        $count = DB::profiles()->where('userid', $user->rID())->count();

        $total = $user->hasLimit('bio');

        if($total > 0 && $count >= $total) {
            return Response::factory(['error' => true, 'message' => e('You have reach the maximum limit for this feature.'), 'token' => csrf_token()])->json();
        }

        if(!$request->name) return Response::factory(['error' => true, 'message' => e('Please enter a name for your profile.'), 'token' => csrf_token()])->json();

        if($request->custom){
            if(strlen($request->custom) < 3){
                return Response::factory(['error' => true, 'message' => e('Custom alias must be at least 3 characters.'), 'token' => csrf_token()])->json();

            }elseif($this->wordBlacklisted($request->custom)){
                return Response::factory(['error' => true, 'message' => e('Inappropriate aliases are not allowed.'), 'token' => csrf_token()])->json();

            }elseif(($request->domain == config('url') || !$request->domain) && DB::url()->where('custom', Helper::slug($request->custom))->whereRaw("(domain = '' OR domain IS NULL OR domain = '".config('url')."')")->first()){
                return Response::factory(['error' => true, 'message' => e('That alias is taken. Please choose another one.'), 'token' => csrf_token()])->json();

            }elseif(DB::url()->where('custom', Helper::slug($request->custom))->where('domain', $request->domain)->first()){
                return Response::factory(['error' => true, 'message' => e('That alias is taken. Please choose another one.'), 'token' => csrf_token()])->json();

            }elseif(DB::url()->where('alias', Helper::slug($request->custom))->whereRaw('(domain = ? OR domain = ?)', [$request->domain, ''])->first()){
                return Response::factory(['error' => true, 'message' => e('That alias is taken. Please choose another one.'), 'token' => csrf_token()])->json();

            }elseif($this->aliasReserved($request->custom)){
                return Response::factory(['error' => true, 'message' => e('That alias is reserved. Please choose another one.'), 'token' => csrf_token()])->json();

            }elseif($user && !$user->pro() && $this->aliasPremium($request->custom)){
                return Response::factory(['error' => true, 'message' => e('That is a premium alias and is reserved to only pro members.'), 'token' => csrf_token()])->json();
            }
		}

        $data = [];

        $data['avatarenabled'] = 1;
        $data['style']['bg'] = null;
        $data['style']['font'] = null;
        $data['style']['gradient'] = ['start' => null, 'stop' => null];
        $data['style']['socialposition'] = null;
        $data['style']['buttoncolor'] = null;
        $data['style']['buttontextcolor'] = null;
        $data['style']['buttonstyle'] = null;
        $data['style']['textcolor'] = null;
        $data['style']['custom'] = null;
        $data['style']['mode'] = null;

        $data['settings']['share'] = 0;
        $data['settings']['sensitive'] = 0;
        $data['settings']['cookie'] = 0;

        $data['links'] = [];

        $alias = $request->custom ? $this->slug($request->custom) : $this->alias();

        $url = DB::url()->create();
        $url->userid = $user->rID();
        $url->url = '';

        if($request->domain && $this->validateDomainNames(trim($request->domain), $user, false)){
            $url->domain = trim(clean($request->domain));
        }

        if((!$request->domain || $request->domain == config('url')) && !config("root_domain")) {

            $sysdomains = array_map('trim', explode("\n", config("domain_names")));

            if(!empty($sysdomains[0])){
				$url->domain = trim(trim($sysdomains[0]));
			}else{
				$url->domain = trim(config("url"));
			}
		}

        if(is_null($url->domain) && !config("root_domain")){
            $sysdomains = array_map('trim', explode("\n", config("domain_names")));
            $url->domain = trim($sysdomains[0]);
        }

        $url->alias = null;
        $url->custom = $alias;
        $url->date = Helper::dtime();

        if($request->pass){
            $url->pass = clean($request->pass);
        }

        $url->save();

        $profile = DB::profiles()->create();
        $profile->userid = $user->rID();
        $profile->alias = $alias;
        $profile->urlid = $url ? $url->id : null;
        $profile->name = clean($request->name);
        $profile->data = json_encode($data);
        $profile->status = 1;
        $profile->created_at = Helper::dtime();
        $profile->save();

        if(!empty($urlids) && is_array($urlids)){
            DB::url()->where_in('id', $urlids)->update(['profileid' => $profile->id]);
        }

        if($url){
            $url->profileid = $profile->id;
            $url->save();
        }

        return Response::factory(['error' => false, 'message' => e('Profile has been successfully created.'), 'token' => csrf_token(), 'html' => '<script>window.location="'.route('bio.edit', $profile->id).'"</script>'])->json();
    }
    /**
     * Delete Profile
     *
     * @author GemPixel <https://gempixel.com>
     * @version 6.0
     * @param [type] $id
     * @return void
     */
    public function delete(int $id, string $nonce){

        \Gem::addMiddleware('DemoProtect');

        if(Auth::user()->teamPermission('bio.delete') == false){
			return Helper::redirect()->to(route('bio'))->with('danger', e('You do not have this permission. Please contact your team administrator.'));
		}

        if(!Helper::validateNonce($nonce, 'bio.delete')){
            return Helper::redirect()->back()->with('danger', e('An unexpected error occurred. Please try again.'));
        }

        if(!$bio = DB::profiles()->where('id', $id)->where('userid', Auth::user()->rID())->first()){
            return back()->with('danger', e('Profile does not exist.'));
        }

        $bio->delete();

        DB::tochannels()->where('itemid', $id)->where('type', 'bio')->deleteMany();

        if($url = DB::url()->where('profileid', $id)->where('userid', Auth::user()->rID())->first()){
            $this->deleteLink($url->id);
        }
        return back()->with('success', e('Profile has been successfully deleted.'));
    }
    /**
     * Edit bio Link
     *
     * @author GemPixel <https://gempixel.com>
     * @version 6.3.2
     * @param integer $id
     * @return void
     */
    public function edit(Request $request, int $id){

        if(Auth::user()->teamPermission('bio.edit') == false){
			return Helper::redirect()->to(route('bio'))->with('danger', e('You do not have this permission. Please contact your team administrator.'));
		}

        if(!$bio = DB::profiles()->where("userid", Auth::user()->rID())->where('id', $id)->first()){
            return back()->with('danger', e('Profile does not exist.'));
        }

        $domains = [];
        foreach(\Helpers\App::domains() as $domain){
            $domains[] = $domain;
        }

        $url = DB::url()->where('id', $bio->urlid)->first();

        // We connect the default bio accounts to be configurable/editable from super admin account with profile id = 1
        // This means that whenever the admin user id 1 changes the profile data, it will affect the default bio that matches the changed profile id.
        $defaultBios = [];
        $generalProfileTable = 'profiles';
        $defaultBiosTable = 'defaultbios';
        foreach(DB::defaultbios()->select("{$generalProfileTable}.*")->select("{$defaultBiosTable}.*")->select("{$generalProfileTable}.data", 'profile_data')->select("{$defaultBiosTable}.data", 'defaultbio_data')->join(DBprefix.'profiles', [DBprefix.'profiles.id' , '=', DBprefix.'defaultbios.profile_id'])->where('status', 1)->findMany() as $defaultBio) {
            $defaultBios[] = $defaultBio;
        }

        $defaultBios = Helper::formatDefaultBios($defaultBios);

        $bio->data = json_decode($bio->data ?? '');
        $bio->responses = json_decode($bio->responses ?? '');


        if($request->downloadqr){
            if(in_array($request->downloadqr, ['png', 'pdf', 'svg'])){

                $data = \Helpers\QR::factory(\Helpers\App::shortRoute($url->domain, $bio->alias), 1000)->format($request->downloadqr);

                return \Core\File::contentDownload('Bio-Qr-'.$bio->alias.'.'.$data->extension(), function() use ($data) {
                    return $data->string();
                });
            }
        }

        if($request->newsletterdata){
			$emails = $bio->responses->newsletter;
			\Core\File::contentDownload('emails.csv', function() use ($emails){
				echo "ID, Email\n";
				foreach($emails as $i => $email){
					echo ($i+1).",{$email}\n";
				}
			});
			exit;
		}

        foreach($bio->data->links as $id => $block){
            if($block->type == "link"){
                if($block_url = \Core\DB::url()->first($block->urlid)){
                    $bio->data->links->{$id}->clicks = $block_url->click;
                }
            }
        }

        View::set('title', e('Customize').' '.$bio->name);

        Plugin::dispatch('bio.edit', $bio);

        \Helpers\CDN::load('spectrum');
        View::push(assets('frontend/libs/clipboard/dist/clipboard.min.js'), 'js')->toFooter();
        View::push('<script>
            var appurl = "'.config('url').'";            
        </script>', 'custom')->toHeader();
        \Helpers\CDN::load('simpleeditor');

        \Helpers\CDN::load('datetimepicker');

        View::push(assets('frontend/libs/fontawesome-picker/dist/css/fontawesome-iconpicker.min.css'))->toHeader();
        View::push(assets('frontend/libs/fontawesome-picker/dist/js/fontawesome-iconpicker.min.js').'?v=1.0', 'script')->toFooter();
        View::push(assets('biopages.min.css').'?v=1.2')->toHeader();

        View::push("<script>
                        $('input[name=icon]').iconpicker();
                    </script>", "custom")->toFooter();

        View::push(assets('fonts/index.css'))->toHeader();

        View::push(assets('bio.min.js').'?v=2.2', 'script')->toFooter();
        View::push(route('bio.widgetjs'), 'script')->toFooter();
        
        if(isset($bio->data->style->mode)){
            if($bio->data->style->mode == 'custom') View::push('<script>$(document).ready(function() { customTheme("'.$bio->data->style->theme.'","'.$bio->data->style->buttoncolor.'","'.$bio->data->style->buttontextcolor.'","'.$bio->data->style->textcolor.'","'.$bio->data->style->mode.'", "'.$bio->data->style->buttonstyle.'", "-45", "'.($bio->data->style->shadow ?? 'false').'", "'.($bio->data->style->shadowcolor ?? 'false').'") } ); </script>', 'custom')
            ->toFooter();

            if($bio->data->style->mode == 'gradient') View::push('<script>$(document).ready(function() { changeTheme("'.$bio->data->style->bg.'","'.($bio->data->style->gradient->start ?? '').'","'.($bio->data->style->gradient->stop ?? '').'","'.$bio->data->style->buttoncolor.'","'.$bio->data->style->buttontextcolor.'","'.$bio->data->style->textcolor.'","'.$bio->data->style->mode.'", "'.$bio->data->style->buttonstyle.'", "-45", "'.($bio->data->style->shadow ?? 'false').'", "'.($bio->data->style->shadowcolor ?? 'false').'") } ); </script>', 'custom')->toFooter();

            if($bio->data->style->mode == 'singlecolor') View::push('<script>$(document).ready(function() { changeTheme("'.$bio->data->style->bg.'","","","'.$bio->data->style->buttoncolor.'","'.$bio->data->style->buttontextcolor.'","'.$bio->data->style->textcolor.'","'.$bio->data->style->mode.'", "'.$bio->data->style->buttonstyle.'", "-45", "'.($bio->data->style->shadow ?? 'false').'", "'.($bio->data->style->shadowcolor ?? 'false').'") } ); </script>', 'custom')->toFooter();

            if($bio->data->style->mode == 'image') View::push('<script>$(document).ready(function() { changeTheme("'.$bio->data->style->bg.'","","","'.$bio->data->style->buttoncolor.'","'.$bio->data->style->buttontextcolor.'","'.$bio->data->style->textcolor.'","'.$bio->data->style->mode.'", "'.$bio->data->style->buttonstyle.'", "-45", "'.($bio->data->style->shadow ?? 'false').'", "'.($bio->data->style->shadowcolor ?? 'false').'") } ); </script>', 'custom')->toFooter();

        } else {
            View::push('<script>$(document).ready(function() { changeTheme("'.$bio->data->style->bg.'","'.($bio->data->style->gradient->start ?? '').'","'.($bio->data->style->gradient->stop ?? '').'","'.$bio->data->style->buttoncolor.'","'.$bio->data->style->buttontextcolor.'","'.$bio->data->style->textcolor.'") } ); </script>', 'custom')->toFooter();
        }

        View::push('<script> var biodata = '.json_encode($bio->data->links).'; bioupdate();</script>', 'custom')->toFooter();

        $themes = [];
        foreach(DB::themes()->where('status', 1)->find() as $theme){
            
            if($theme->paidonly && !Auth::user()->pro()) continue;

            $theme->data = json_decode($theme->data);
            if($theme->data->bgtype == 'single') { 
                $theme->data->style = "background:{$theme->data->singlecolor} !important";
            }
            if($theme->data->bgtype == 'gradient') { 
                $theme->data->style = "background: linear-gradient({$theme->data->gradientangle}deg, {$theme->data->gradientstart} 0%, {$theme->data->gradientstop} 100%);";
            }
            if($theme->data->bgtype == 'image') { 
                $theme->data->style = "background-image: url(".uploads($theme->data->bgimage, 'profile').");background-size: cover;";
            }
            if($theme->data->bgtype == 'css') { 
                $theme->data->style = $theme->data->customcss;
            }

            if($theme->data->buttonstyle == 'rectangle') { 

                $theme->data->button = "background:{$theme->data->buttoncolor} !important; color: {$theme->data->buttontextcolor}; border-radius: 5px;";

                if($theme->data->shadow == 'soft'){
                    $theme->data->button .= "box-shadow: 2px 2px 5px {$theme->data->shadowcolor};";
                }

                if($theme->data->shadow == 'hard'){
                    $theme->data->button .= "box-shadow: 5px 5px 0px 1px {$theme->data->shadowcolor};";
                }
            }
            if($theme->data->buttonstyle == 'rounded') { 
                
                $theme->data->button = "background:{$theme->data->buttoncolor} !important; color: {$theme->data->buttontextcolor}; border-radius: 20px;";

                if($theme->data->shadow == 'soft'){
                    $theme->data->button .= "box-shadow: 2px 2px 5px {$theme->data->shadowcolor};";
                }

                if($theme->data->shadow == 'hard'){
                    $theme->data->button .= "box-shadow: 5px 5px 0px 1px {$theme->data->shadowcolor};";
                }
            }
            if($theme->data->buttonstyle == 'trec') { 
                
                $theme->data->button = "background:transparent; border: 2px solid {$theme->data->buttoncolor} !important; color: {$theme->data->buttontextcolor}; border-radius: 5px;";

                if($theme->data->shadow == 'soft'){
                    $theme->data->button .= "box-shadow: 2px 2px 5px {$theme->data->shadowcolor};";
                }

                if($theme->data->shadow == 'hard'){
                    $theme->data->button .= "box-shadow: 5px 5px 0px 1px {$theme->data->shadowcolor};";
                }
            }
            if($theme->data->buttonstyle == 'tro') { 
                
                $theme->data->button = "background:transparent; border: 2px solid {$theme->data->buttoncolor} !important; color: {$theme->data->buttontextcolor}; border-radius: 20px;";

                if($theme->data->shadow == 'soft'){
                    $theme->data->button .= "box-shadow: 2px 2px 5px {$theme->data->shadowcolor};";
                }

                if($theme->data->shadow == 'hard'){
                    $theme->data->button .= "box-shadow: 5px 5px 0px 1px {$theme->data->shadowcolor};";
                }
            }
            $themes[] = $theme;
        }

        $platforms = \Helpers\BioWidgets::socialPlatforms();

        return View::with('bio.edit', compact('bio', 'domains', 'url', 'themes', 'platforms', 'defaultBios'))->extend('layouts.dashboard');

    }
    /**
     * Update BioPage
     *
     * @author GemPixel <https://gempixel.com>
     * @version 6.3.2
     * @param \Core\Request $request
     * @param integer $id
     * @return void
     */
    public function update(Request $request, int $id){

        \Gem::addMiddleware('DemoProtect');

        if(Auth::user()->teamPermission('bio.edit') == false){
			return Response::factory(['error' => true, 'message' => e('You do not have this permission. Please contact your team administrator.'), 'token' => csrf_token()])->json();
		}

        if(!$profile = DB::profiles()->where('id', $id)->where('userid', Auth::user()->rID())->first()){
            return Response::factory(['error' => true, 'message' => e('Profile does not exist.')])->json();
        }

        $user = Auth::user();

        if(!$request->name) return Response::factory(['error' => true, 'message' => e('Please enter a name for your profile.'), 'token' => csrf_token()])->json();

        $data = json_decode($profile->data, true);

        $url = DB::url()->first($profile->urlid);

        if($request->custom && $request->custom != $profile->alias){
            if(strlen($request->custom) < 3){
                return Response::factory(['error' => true, 'message' => e('Custom alias must be at least 3 characters.'), 'token' => csrf_token()])->json();

            }elseif($this->wordBlacklisted($request->custom)){
                return Response::factory(['error' => true, 'message' => e('Inappropriate aliases are not allowed.'), 'token' => csrf_token()])->json();

            }elseif(($request->domain == config('url') || !$request->domain) && DB::url()->where('custom', $this->slug($request->custom))->whereRaw("(domain = '' OR domain IS NULL)")->first()){
                return Response::factory(['error' => true, 'message' => e('That alias is taken. Please choose another one.'), 'token' => csrf_token()])->json();

            }elseif(DB::url()->where('custom', $this->slug($request->custom))->where('domain', $request->domain)->first()){
                return Response::factory(['error' => true, 'message' => e('That alias is taken. Please choose another one.'), 'token' => csrf_token()])->json();

            }elseif(DB::url()->where('alias', $this->slug($request->custom))->whereRaw('(domain = ? OR domain = ?)', [$request->domain, ''])->first()){
                return Response::factory(['error' => true, 'message' => e('That alias is taken. Please choose another one.'), 'token' => csrf_token()])->json();

            }elseif($this->aliasReserved($request->custom)){
                return Response::factory(['error' => true, 'message' => e('That alias is reserved. Please choose another one.'), 'token' => csrf_token()])->json();

            }elseif($user && !$user->pro() && $this->aliasPremium($request->custom)){
                return Response::factory(['error' => true, 'message' => e('That is a premium alias and is reserved to only pro members.'), 'token' => csrf_token()])->json();
            }

            $profile->alias = $this->slug($request->custom);
            $url->alias = null;
            $url->custom = $profile->alias;
        }

        $url->pass = clean($request->pass);

        if($request->pixels){
            $url->pixels = $request->pixels && $user && $user->has('pixels') ? clean(implode(",", $request->pixels)) : null;
        }
        
        $appConfig = appConfig('app');
        $sizes = $appConfig['sizes'];
        $extensions = $appConfig['extensions'];

        $url->meta_title = clean($request->title);
        $url->meta_description = clean($request->description);

        if($image = $request->file('metaimage')){
            if(!$image->mimematch || !in_array($image->ext, ['jpg', 'png'])) return Response::factory(['error' => true, 'message' => e('Banner must be either a PNG or a JPEG (Max 500kb).'), 'token' => csrf_token()])->json();

            if($image->sizekb >= 500) return Response::factory(['error' => true, 'message' => e('Banner must be either a PNG or a JPEG (Max 500kb).'), 'token' => csrf_token()])->json();

            $filename = Helper::rand(6)."_".str_replace(' ', '-',$image->name); 

            request()->move($image, $appConfig['storage']['images']['path'], $filename);

            if($url->meta_image){
                \Helpers\App::delete( $appConfig['storage']['images']['path'].'/'.$url->meta_image);
            }
            $url->meta_image = $filename;
        }


        if($request->domain && $this->validateDomainNames(trim($request->domain), $user, false)){
            $url->domain = trim(clean($request->domain));
        }

        if((!$request->domain || $request->domain == config('url')) && !config("root_domain")) {

            $sysdomains = array_map('trim', explode("\n", config("domain_names")));

            if(!empty($sysdomains[0])){
				$url->domain = trim(trim($sysdomains[0]));
			}else{
				$url->domain = trim(config("url"));
			}
		}

        if(is_null($url->domain) && !config("root_domain")){
            $sysdomains = array_map('trim', explode("\n", config("domain_names")));
            $url->domain = trim($sysdomains[0]);
        }

        $url->save();

        $data['avatarenabled'] = in_array($request->avatarenabled, [0, 1]) ? $request->avatarenabled : 1;        
        $data['avatarstyle'] = in_array($request->avatarstyle, ['rectangular', 'rounded']) ? $request->avatarstyle : 'rounded';        

        if($image = $request->file('avatar')){

            if(!$image->mimematch || !in_array($image->ext, $extensions['bio']['avatar']) || $image->sizekb > $sizes['bio']['avatar']) return Response::factory(['error' => true, 'message' => e('Avatar must be either a PNG or a JPEG (Max 500kb).'), 'token' => csrf_token()])->json();

            $filename = "profile_avatar".Helper::rand(6).str_replace(' ', '-', $image->name);

            $request->move($image, $appConfig['storage']['profile']['path'], $filename);

            if(isset($data['avatar']) && $data['avatar']){
                \Helpers\App::delete($appConfig['storage']['profile']['path']."/".$data['avatar']);
            }

            $data['avatar']= $filename;
        }
        
        if($request->themeid && $theme = DB::themes()->where('id', clean($request->themeid))->first()){
            $data['themeid'] = $theme->id;
        } else {
            $data['themeid'] = null;
        }


        if($image = $request->file('bgimage')){

            if(!$image->mimematch || !in_array($image->ext, $extensions['bio']['background']) || $image->sizekb > $sizes['bio']['background']) return Response::factory(['error' => true, 'message' => e('Background must be either a PNG or a JPEG (Max 1mb).'), 'token' => csrf_token()])->json();

            $filename = "profile_imagebg".Helper::rand(6).str_replace(' ', '-', $image->name);

			$request->move($image, $appConfig['storage']['profile']['path'], $filename);

            if(isset($data['bgimage']) && $data['bgimage']){
                \Helpers\App::delete($appConfig['storage']['profile']['path']."/".$data['bgimage']);
            }

            $data['bgimage'] = $filename;
        }

        if($request->layout == 'layout1' && isset($data['layoutbanner']) && $data['layoutbanner'] && file_exists($appConfig['storage']['profile']['path'].'/'.$data['layoutbanner'])){
            \Helpers\App::delete($appConfig['storage']['profile']['path']."/".$data['layoutbanner']);
            $data['layoutbanner'] = '';
        }

        if($image = $request->file('layoutbanner')){

            if(!$image->mimematch || !in_array($image->ext, ['jpg', 'png', 'jpeg']) || $image->sizekb > 1000) return Response::factory(['error' => true, 'message' => e('Background must be either a PNG or a JPEG (Max 1mb).'), 'token' => csrf_token()])->json();

            $filename = "profile_layoutbanner".Helper::rand(6).str_replace(' ', '-', $image->name);

			$request->move($image, $appConfig['storage']['profile']['path'], $filename);

            if(isset($data['layoutbanner']) && $data['layoutbanner']){
                \Helpers\App::delete($appConfig['storage']['profile']['path']."/".$data['layoutbanner']);
            }

            $data['layoutbanner'] = $filename;
        }

        $links = [];

        $profiledata = $data;

        foreach($data['links'] as $id => $olddata){
            if($olddata['type'] != 'link') continue;
            $links[$id] = $olddata['urlid'];
        }

        $data['links'] = [];
        if($request->data){
            foreach($request->data as $id => $blockdata){                
                // Validate and Update Block
                try{
                    $blockdata['id'] = $id;
                    if(isset($links[$id])) $blockdata['urlid'] = $links[$id];
                    $data['links'][$id] = BioWidgets::update($request, $profiledata, $blockdata);
                } catch(\Exception $e){
                    return Response::factory(['error' => true, 'message' => $e->getMessage(), 'token' => csrf_token()])->json();
                }
            }
        }
        
        if($request->theme){
            $data['style']['theme'] = clean($request->theme);
        }

        if($request->social){
            $data['social'] = [];
            foreach($request->social as $key => $value){
                if(empty($value)) continue;
                $data['social'][$key] = clean($value);
            }
        }        

        $data['style']['socialposition'] = clean($request->socialposition);
        $data['style']['bg'] = clean($request->bg);
        $data['style']['font'] = clean($request->fonts);
        $data['style']['gradient'] = array_map('clean', $request->gradient);
        $data['style']['mode'] = Helper::clean($request->mode, 3);
        $data['style']['layout'] = Helper::clean($request->layout, 3);

        $data['style']['buttonstyle'] = clean($request->buttonstyle, 3);
        $data['style']['buttoncolor'] = clean($request->buttoncolor, 3);
        $data['style']['buttontextcolor'] = clean($request->buttontextcolor, 3);
        $data['style']['shadow'] = clean($request->shadow, 3);
        $data['style']['shadowcolor'] = clean($request->shadowcolor, 3);
        $data['style']['textcolor'] = clean($request->textcolor, 3);

        if($user->has('biocss')){
            $data['style']['custom'] = Helper::clean($request->customcss, 3);
        }        

        $data['settings']['share'] = (int) $request->share ? Helper::clean($request->share, 3) : 0;
        $data['settings']['sensitive'] = (int) $request->sensitive ? Helper::clean($request->sensitive, 3) : 0;
        $data['settings']['cookie'] = (int) $request->cookie ? Helper::clean($request->cookie, 3) : 0;

        if($user->has('poweredby')){
            $data['settings']['branding'] = $request->branding ? Helper::clean($request->branding, 3) : 0;
        }

        $profile->userid = $user->rID();
        $profile->name = clean($request->name, 3);
        $profile->data = json_encode($data);
        $profile->save();

        return Response::factory(['error' => false, 'message' => e('Profile has been successfully updated.'), 'html' => '<script>$("[data-trigger=shortinfo]").data("shorturl", "'.\Helpers\App::shortRoute($url->domain, $profile->alias).'")</script>', 'token' => csrf_token()])->json();
    }
    /**
     * Preview Bio
     *
     * @author GemPixel <https://gempixel.com>
     * @version 6.9
     * @param integer $id
     * @return void
     */
    public function preview(int $id){

        $user = Auth::user();

        if(!$profile = DB::profiles()->where("userid", $user->rID())->where('id', $id)->first()){
            stop(404);
        }
        $url = DB::url()->where('profileid', $id)->first();

        $profiledata = json_decode($profile->data, true);

        View::push(assets('biopages.min.css').'?v=1.0')->toHeader();

         View::push('<style>body{min-height: 100vh;color: '.$profiledata['style']['textcolor'].';'.(isset($profiledata['style']['mode']) && $profiledata['style']['mode'] == 'singlecolor' ? 'background: '.$profiledata['style']['bg'].';' : '').''.(!isset($profiledata['style']['mode']) || $profiledata['style']['mode'] == 'gradient' ? 'background: linear-gradient('.(isset($profiledata['style']['gradient']['angle']) && is_numeric($profiledata['style']['gradient']['angle']) ? $profiledata['style']['gradient']['angle'] : '135').'deg,'.$profiledata['style']['gradient']['start'].' 0%, '.$profiledata['style']['gradient']['stop'].' 100%);' : '').'}.fa,.fab,.far,.fas,.fa-brands,.fa-solid{font-size: 1.5em}h1,h3,em,p,a{color: '.$profiledata['style']['textcolor'].' !important;}a:hover{color: '.$profiledata['style']['textcolor'].';opacity: 0.8;}.btn-custom,.btn-custom.active{font-weight:700; background: '.$profiledata['style']['buttoncolor'].';color: '.$profiledata['style']['buttontextcolor'].' !important;}a.btn-custom:hover,button.btn-custom:hover{opacity: 0.8;background: '.$profiledata['style']['buttoncolor'].';color: '.$profiledata['style']['buttontextcolor'].';}.btn-custom p, .btn-custom h3, .btn-custom span{color: '.$profiledata['style']['buttontextcolor'].' !important;}.rss{font-weight:400;background:'.$profiledata['style']['buttoncolor'].';color: '.$profiledata['style']['buttontextcolor'].';height:300px} .rss a{color:'.$profiledata['style']['buttontextcolor'].' !important}.item > h1,.item > h2,.item > h3,.item > h4,.item > h5,.item > h6{color:'.$profiledata['style']['textcolor'].';}.cc-floating.cc-type-info.cc-theme-classic .cc-btn{color:#000 !important}.modal-backdrop.show{opacity:0.85!important}#social a:first-child{margin-left: 0 !important}.form-control{background:#fff !important;color:#000 !important}.layout2 .d-block{height:150px;}.layout2 .useravatar{margin-top: -60px;}.card{background: '.$profiledata['style']['buttoncolor'].';color: '.$profiledata['style']['buttontextcolor'].' !important;}.card a, .card h6, .card p, .card .card-body {color: '.$profiledata['style']['buttontextcolor'].' !important;}.fa-animated .fa{transition: transform 0.2s linear;font-size: 18px !important}.fa-animated:not(.collapsed) .fa{transform: rotate(180deg);.btn-icon-only{width:36px;heigth:36px;}}.btn+.btn{margin-left: 0 !important}</style>','custom')->toHeader();

        if(isset($profiledata['style']['buttonstyle'])){
            if($profiledata['style']['buttonstyle'] == 'trec'){
                View::push('<style>.btn-custom,.card{background-color:transparent;border:2px solid '.$profiledata['style']['buttoncolor'].';}</style>','custom')->toHeader();
            }elseif($profiledata['style']['buttonstyle'] == 'tro'){
                View::push('<style>.btn-custom,.card{background-color:transparent;border:2px solid '.$profiledata['style']['buttoncolor'].';border-radius:50px;}.btn-custom.faqs{border-radius:5px}</style>','custom')->toHeader();
            }elseif($profiledata['style']['buttonstyle'] == 'rounded'){
                View::push('<style>.btn-custom,.card{border-radius:50px;}.btn-custom.faqs{border-radius:5px}</style>','custom')->toHeader();
            }elseif($profiledata['style']['buttonstyle'] == 'none'){
                View::push('<style>.btn-custom,.card{border-radius:0;}</style>','custom')->toHeader();
            }
        }
        if(isset($profiledata['style']['shadow']) && !empty($profiledata['style']['shadowcolor'])){
            if($profiledata['style']['shadow'] == 'soft'){
                View::push('<style>.btn-custom,.card{box-shadow: 0px 5px 10px rgba('.implode(',',sscanf($profiledata['style']['shadowcolor'], "#%02x%02x%02x")).',0.4)}</style>','custom')->toHeader();
            }elseif($profiledata['style']['shadow'] == 'hard'){
                View::push('<style>.btn-custom,.card{box-shadow: 5px 5px 0px 1px '.$profiledata['style']['shadowcolor'].'}</style>','custom')->toHeader();
            }
        }

        if(isset($profiledata['settings']['share']) && $profiledata['settings']['share']){
            
            View::push(assets('frontend/libs/clipboard/dist/clipboard.min.js'), 'js')->toFooter();

            View::push("<script>
                    if(typeof navigator.share == 'function' && navigator.share){
                        $('#modal-share').append('<a href=\"#\" data-trigger=\"share\" class=\"d-flex align-items-center text-left text-start btn text-black d-block w-100 p-3 border mb-2\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-share mr-2 me-2\"><path d=\"M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8\"></path><polyline points=\"16 6 12 2 8 6\"></polyline><line x1=\"12\" y1=\"2\" x2=\"12\" y2=\"15\"></line></svg><span class=\"align-middle\">".e('More share options')."</span></a>');
                    }
                    $('[data-trigger=share]').click(function(e){
                        e.preventDefault();
                        navigator.share({
                            title: '{$url->meta_title}',
                            text: '{$url->meta_decription}',
                            url: '".App::shortRoute($url->domain??null, $profile->alias)."'
                        });
                    });
                    new ClipboardJS('.copy', {
                        container: document.getElementById('modal-share')
                    })
                    </script>",'custom')->toFooter();
        }

        if(isset($profiledata['settings']['cookie']) && (is_null($profiledata['settings']['cookie']) || !$profiledata['settings']['cookie'])){
            $config = config();
            $config->cookieconsent->enabled = 0;
            Helper::set("config", $config);
        }
        
        if(isset($profiledata['settings']['sensitive']) && $profiledata['settings']['sensitive']){
            View::push('<div class="modal fade" id="sensitiveModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="sensitiveModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title text-dark" id="sensitiveModalLabel"><i class="fa fa-warning text-danger"></i> '.e('Sensitive Content').'</h5>
                                </div>
                                <div class="modal-body text-dark">
                                '.e('This page contains sensitive content which may not be suitable for all ages. By continuing, you agree to our terms of service.').'
                                </div>
                                <div class="modal-footer">
                                    <a href="'.url('?utm_source=biopage-'.$profile->alias.'&utm_medium=sensitivemodal').'" class="btn btn-primary text-white">'.e('Go Back').'</a>
                                    <button type="button" class="btn btn-danger text-white" data-dismiss="modal" data-bs-dismiss="modal">'.e('Continue').'</button>
                                </div>
                            </div>
                            </div>
                        </div><script>new bootstrap.Modal(document.getElementById("sensitiveModal"), {backdrop:\'static\',keyboard: false}).show();$(\'.modal-backdrop.show\').attr(\'style\', \'opacity: 1 !important\');if(typeof modal == "function") $(\'#sensitiveModal\').modal(\'show\');</script>','custom')->toFooter();
        }
        if(isset($profiledata['themeid']) && $profiledata['themeid'] && $theme = DB::themes()->where('id', clean($profiledata['themeid']))->first()){
            $theme->data = json_decode($theme->data);
            if($theme->data->bgtype == 'image'){
                View::push('<style>body{background-image: url(\''.uploads($theme->data->bgimage, 'profile').'\');background-size:cover}</style>','custom')->toHeader();
            }
            if($theme->data->bgtype == 'css'){
                View::push('<style>body{'.$theme->data->customcss.'}</style>','custom')->toHeader();
            }
        }
        // @group Plugin
        \Core\Plugin::dispatch('type.profile', $profile);

        if(isset($profiledata['style']['custom']) && $profiledata['style']['custom']){
            View::push('<style>'.$profiledata['style']['custom'].'</style>','custom')->toHeader();
        }

        if((!isset($profiledata['style']['mode']) && isset($profiledata['bgimage']) && $profiledata['bgimage']) ||
            (isset($profiledata['style']['mode']) && $profiledata['style']['mode'] == "image" && isset($profiledata['bgimage']) && $profiledata['bgimage'])) {
            View::push('<style>body{background-image: url('.uploads($profiledata['bgimage'], 'profile').');background-size:cover}</style>','custom')->toHeader();
        }
        if(isset($profiledata['style']['theme']) && $profiledata['style']['theme'] && $profiledata['style']['mode'] == 'custom'){
            View::set('bodyClass', $profiledata['style']['theme']);
        }
        
        View::push(($url->domain ?? config('url')).'/static/frontend/libs/fontawesome/all.min.css')->toHeader();
        if(isset($profiledata['style']['font']) && !empty($profiledata['style']['font'])){
            View::push(($url->domain ?? config('url')).'/static/fonts/index.css')->toHeader();
            View::push('<style>body{font-family: "'.str_replace('+', ' ', $profiledata['style']['font']).'" !important;}</style>', 'custom')->toHeader();
        }

        $profile->url = App::shortRoute($url->domain??null, $profile->alias);

        $platforms = BioWidgets::socialPlatforms();

        $socials = $profiledata['social'] ?? [];

        $profiledata['social'] = [];

        foreach($socials as $key => $sociallink){
            
            if(!isset($platforms[$key])) continue;
        
            $profiledata['social'][$key] = $platforms[$key]+['link' => $sociallink];
        }

        return View::with('gates.profile', compact('profile', 'profiledata', 'user'))->extend('layouts.auth');

    }
    /**
     * Set bio as default
     *
     * @author GemPixel <https://gempixel.com>
     * @version 6.0
     * @param integer $id
     * @return void
     */
    public function default(int $id){

        if(Auth::user()->teamPermission('bio.edit') == false){
			return Helper::redirect()->to(route('bio'))->with('danger', e('You do not have this permission. Please contact your team administrator.'));
		}

        $user = Auth::user();

        if(!$profile = DB::profiles()->where('id', $id)->where('userid', $user->rID())->first()){
            return Helper::redirect()->back()->with('danger', e('Profile does not exist.'));
        }

        $user->defaultbio = $profile->id;
        $user->save();

        if($user->public){
            return Helper::redirect()->back()->with('success', e('Profile has been set as default and can now be access via your profile page.'));
        } else {
            return Helper::redirect()->back()->with('info', e('Profile has been set as default and can now be access via your profile page. Your profile setting is currently set on private.'));
        }
    }
    /**
     * Import Old Bio
     *
     * @author GemPixel <https://gempixel.com>
     * @version 6.0
     * @return void
     */
    public function importBio(){

        if(Auth::user()->teamPermission('bio.create') == false){
			return Helper::redirect()->to(route('bio'))->with('danger', e('You do not have this permission. Please contact your team administrator.'));
		}

        \Gem::addMiddleware('DemoProtect');

        $user = Auth::user();

        $old = json_decode($user->profiledata);

        $data = [];

        foreach($old->links as $link){
            if(!isset($link->link) || empty($link->link)) continue;
            if(!$url = DB::url()->where('userid', $user->id)->where('url', $link->link)->first()){
                $url = DB::url()->create();
                $url->url = $link->link;
                $url->custom = 'P'.Helper::rand(3).'M'.Helper::rand(3);
                $url->type = 'direct';
                $url->userid = $user->id;
                $url->date = Helper::dtime();
                $url->save();
            }

            $data['links'][Helper::slug($link->link)] = ['text' => $link->text, 'link' => $link->link, 'urlid' => $url->id, 'type' => 'link'];
        }

        $data["social"] = ["facebook" => "","twitter" => "","instagram" => "","tiktok" => "","linkedin" => ""];

        $data["style"] = ["bg" => "#FDBB2D","gradient" => ["start" => "#0072ff","stop" => "#00c6ff"],"buttoncolor" => "#ffffff","buttontextcolor" => "#00c6ff","textcolor" => "#ffffff"];

        $profile = DB::profiles()->create();

        $alias = $this->alias();

        $url = DB::url()->create();
        $url->userid = $user->rID();
        $url->url = '';
        $url->domain = clean($request->domain);
        $url->alias = $alias;
        $url->date = Helper::dtime();
        $url->save();

        $profile = DB::profiles()->create();
        $profile->userid = $user->rID();
        $profile->alias = $alias;
        $profile->urlid = $url ? $url->id : null;
        $profile->name = clean($old->name);
        $profile->data = json_encode($data);
        $profile->status = 1;
        $profile->created_at = Helper::dtime();
        $profile->save();
        $url->profileid = $profile->id;
        $url->save();

        $user->defaultbio = $profile->id;
        $user->profiledata = null;
        $user->save();

        return Helper::redirect()->back()->with('success', 'Migration complete.');
    }

    /**
     * apply Default Bio
     *
     * @param integer $id
     * @return void
     */
    public function applyDefaultBio(int $id, int $defaultBioId){

        $user = Auth::user();

        $count = DB::profiles()->where('userid', Auth::user()->rID())->count();

        $total = Auth::user()->hasLimit('bio');

        \Models\Plans::checkLimit($count, $total);

        if(!$profile = DB::profiles()->where('id', $id)->where('userid', $user->rID())->first()){
            return Helper::redirect()->back()->with('danger', e('Profile does not exist.'));
        }

        if(!$defaultBio = DB::defaultbios()->where('id', $defaultBioId)->first()){
            return Helper::redirect()->back()->with('danger', e('Profile does not exist.'));
        }

        $profile->data = $defaultBio->data;
        $profile->save();

        return Helper::redirect()->back()->with('success', e('New template applied.'));
    }

    /**
     * Duplicate
     *
     * @author GemPixel <https://gempixel.com>
     * @version 6.4
     * @param integer $id
     * @return void
     */
    public function duplicate(int $id){
        if(Auth::user()->teamPermission('bio.edit') == false){
			return Helper::redirect()->to(route('bio'))->with('danger', e('You do not have this permission. Please contact your team administrator.'));
		}

        $user = Auth::user();

        $count = DB::profiles()->where('userid', Auth::user()->rID())->count();

        $total = Auth::user()->hasLimit('bio');

        \Models\Plans::checkLimit($count, $total);

        if(!$profile = DB::profiles()->where('id', $id)->where('userid', $user->rID())->first()){
            return Helper::redirect()->back()->with('danger', e('Profile does not exist.'));
        }

        $url = DB::url()->first($profile->urlid);

        $new = DB::profiles()->create();

        $new->name = $profile->name.' ('.e('Copy').')';
        $new->alias = $this->alias();
        $new->userid = $user->rID();

        $newurl = DB::url()->create();
        $newurl->userid = $user->rID();
        $newurl->url = '';
        $newurl->domain = $url->domain;
        $newurl->alias = $new->alias;
        $newurl->date = Helper::dtime();
        $newurl->save();

        $new->urlid = $newurl->id;
        $new->data = $profile->data;
        $new->created_at = Helper::dtime();

        $new->save();

        $newurl->profileid = $new->id;
        $newurl->save();

        return Helper::redirect()->back()->with('success', e('Item has been successfully duplicated.'));
    }
    /**
     * Dynamic Widgets
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 1.0
     * @return void
     */
    public function widgets(){

        $widgets = new BioWidgets;

        $i = 0;
        $js = "";

        foreach($widgets->widgets() as $tag => $widget){
            $js .= \call_user_func($widget['setup']);
            $i++;
        }

        ob_start(function($js) { return str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $js); });
        
        header("Content-type: text/javascript");
        
        echo $js;
        
		ob_end_flush(); 
    }
}