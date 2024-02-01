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

namespace Admin;

use Core\DB;
use Core\View;
use Core\Request;
use Core\Helper;
Use Helpers\CDN;
Use Models\User;

class Users {
    /**
     * Users
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 7.1.3
     * @return void
     */
    public function index(Request $request){

        $users = [];
        $query = User::orderByDesc('date');

        if($request->plan) $query->where('planid', $request->plan);

        foreach($query->paginate(15) as $user){
            if(_STATE == "DEMO") $user->email="demo@demo.com";
            if(empty($user->email)) $user->email = ucfirst($user->auth)." User";   
            $user->count = DB::url()->where('userid', $user->id)->count();

            if($user->pro) {
                if($plan = DB::plans()->where('id', $user->planid)->first()){
                    $user->pro = $plan->name;
                } else{
                    $user->pro = e('Pro');
                }
            }

            $users[] = $user;         
        }

        View::set('title', e('Users'));

        return View::with('admin.users.index', compact('users'))->extend('admin.layouts.main');
    }
    /**
     * Inactive User
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @return void
     */
    public function inactive(){
        $users = [];

        foreach(User::where('active', 0)->orderByDesc('date')->paginate(15) as $user){
            if(_STATE == "DEMO") $user->email="demo@demo.com";
            if(empty($user->email)) $user->email = ucfirst($user->auth)." User";   
            $user->count = DB::url()->where('userid', $user->id)->count();
            $users[] = $user;         
        }

        View::set('title', e('Inactive Users'));

        return View::with('admin.users.index', compact('users'))->extend('admin.layouts.main');
    }
    /**
     * Blocked User
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @return void
     */
    public function banned(){
        $users = [];

        foreach(User::where('banned', 1)->orderByDesc('date')->paginate(15) as $user){
            if(_STATE == "DEMO") $user->email="demo@demo.com";
            if(empty($user->email)) $user->email = ucfirst($user->auth)." User";   
            $user->count = DB::url()->where('userid', $user->id)->count();
            $users[] = $user;         
        }

        View::set('title', e('Banned Users'));

        return View::with('admin.users.index', compact('users'))->extend('admin.layouts.main');
    }
    /**
     * Admin User
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @return void
     */
    public function admin(){
        $users = [];

        foreach(User::where('admin', 1)->orderByDesc('date')->paginate(15) as $user){
            if(_STATE == "DEMO") $user->email="demo@demo.com";
            if(empty($user->email)) $user->email = ucfirst($user->auth)." User";   
            $user->count = DB::url()->where('userid', $user->id)->count();
            $users[] = $user;         
        }

        View::set('title', e('Admin Users'));

        return View::with('admin.users.index', compact('users'))->extend('admin.layouts.main');
    }
    /**
     * Add Post
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @return void
     */
    public function new(){
        
        View::set('title', e('New User'));

        $plans = DB::plans()->findMany();

        CDN::load('datetimepicker');

        return View::with('admin.users.new', compact('plans'))->extend('admin.layouts.main');
    }
    /**
     * Save post
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @param \Core\Request $request
     * @return void
     */
    public function save(Request $request){
        
        \Gem::addMiddleware('DemoProtect');

        $request->save('email', clean($request->email));
        $request->save('username', clean($request->username));
        $request->save('lastpayment', clean($request->lastpayment));
        $request->save('expiration', clean($request->expiration));

        if(!$request->email || !$request->password) return Helper::redirect()->back()->with('danger', e('The email and the password are required.'));

        $user = DB::user()->create();

        if(!$request->validate($request->email, 'email') || DB::user()->where('email', $request->email)->first()) return Helper::redirect()->back()->with('danger', e('Invalid email or an account already exists with this email.'));

        $user->email = Helper::clean($request->email);

        if($request->username){
            if(!$request->validate($request->username, 'username')) return Helper::redirect()->back()->with('danger', e('Please enter a valid username.'));
            if(DB::user()->where('username', $request->username)->first()) return Helper::redirect()->back()->with('danger', e('Username already exists.'));
        }

        $user->username = Helper::clean($request->username);
        
        if(strlen($request->password) < 5) return Helper::redirect()->back()->with('danger', e('Password must be at least 5 characters.'));

        Helper::set("hashCost", 8);

        $user->password = Helper::Encode($request->password);
        
        if($request->plan){
            $plan = DB::plans()->where('id', $request->plan)->first();
            $user->pro = $plan->free ? 0 : 1;
        }else{
            $user->pro = 0;
        }

        $user->planid = $request->plan ?: null;
        $user->last_payment = !$request->lastpayment  || $request->lastpayment == '' ? null : $request->lastpayment;
        $user->expiration = !$request->expiration  || $request->expiration == '' ? null : $request->expiration;
        $user->date = Helper::dtime();
        $user->api = Helper::rand(16);
        $user->public = $request->public ?? 0;
        $user->admin = $request->admin ?? 0;
        $user->active= $request->active ?? 0;
        $user->auth_key = Helper::Encode($user->email.$user->id.uniqid().rand(0, 99999));
        $user->banned = 0;  
        $user->save();
        $request->clear();
        return Helper::redirect()->to(route('admin.users'))->with('success', e('User has been added successfully'));
    }
    /**
     * Edit Post
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @param integer $id
     * @return void
     */
    public function edit(int $id){
        
        if(!$user = DB::user()->where('id', $id)->first()) return Helper::redirect()->back()->with('danger', e('User does not exist.'));
        
        $user->last_payment = $user->last_payment ? Helper::dtime($user->last_payment, 'Y-m-d') : Helper::dtime('now', 'Y-m-d');
        $user->expiration = $user->expiration ? Helper::dtime($user->expiration, 'Y-m-d') : Helper::dtime('now', 'Y-m-d');

        $plans = DB::plans()->findMany();

        View::set('title', e('Edit User'));

        CDN::load('datetimepicker');

        if(_STATE == "DEMO") $user->email="demo@demo.com";

        return View::with('admin.users.edit', compact('user', 'plans'))->extend('admin.layouts.main');
    }
    /**
     * Update Post
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @param \Core\Request $request
     * @param integer $id
     * @return void
     */
    public function update(Request $request, int $id){
        \Gem::addMiddleware('DemoProtect');

        if(!$user = DB::user()->where('id', $id)->first()) return Helper::redirect()->back()->with('danger', e('User does not exist.'));
        
        if(!$request->validate($request->email, 'email') || DB::user()->where('email', $request->email)->whereNotEqual('id', $user->id)->first()) return Helper::redirect()->back()->with('danger', e('An account with the new email already exists.'));

        $user->email = Helper::clean($request->email);

        if($request->username){
            if(!$request->validate($request->username, 'username')) return Helper::redirect()->back()->with('danger', e('Please enter a valid username.'));
            if(DB::user()->where('username', $request->username)->whereNotEqual('id', $user->id)->first()) return Helper::redirect()->back()->with('danger', e('Username already exists.'));
        }

        $user->username = Helper::clean($request->username);
        
        Helper::set("hashCost", 8);

        if($request->password){

            if(strlen($request->password) < 5) return Helper::redirect()->back()->with('danger', e('Password must be at least 5 characters.'));
            $user->password = Helper::Encode($request->password);
        }

        if($request->plan){
            $plan = DB::plans()->where('id', $request->plan)->first();
            $user->pro = $plan->free ? 0 : 1;
        }else{
            $user->pro = 0;
        }

        $user->planid = $request->plan ?: null;
        $user->last_payment = !$request->lastpayment  || $request->lastpayment == '' ? null : $request->lastpayment;
        $user->expiration = !$request->expiration  || $request->expiration == '' ? null : $request->expiration;
        $user->api = $request->api;
        $user->public = $request->public ?? 0;
        $user->admin = $request->admin ?? 0;
        $user->active= $request->active ?? 0;
        $user->banned = $request->banned ?? 0;
        $user->secret2fa = $request->secret2fa;
        $user->save();

        return Helper::redirect()->back()->with('success', e('User has been updated successfully'));
    }
    /**
     * Delete User
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @param \Core\Request $request
     * @param integer $id
     * @param string $nonce
     * @return void
     */
    public function delete(Request $request, int $id, string $nonce){
        
        \Gem::addMiddleware('DemoProtect');

        if(!Helper::validateNonce($nonce, 'user.delete')){
            return Helper::redirect()->back()->with('danger', e('An unexpected error occurred. Please try again.'));
        }

        if(!$user = DB::user()->where('id', $id)->first()){
            return Helper::redirect()->back()->with('danger', e('User not found. Please try again.'));
        }
        DB::profiles()->where('userid', $id)->deleteMany();
        DB::qrs()->where('userid', $id)->deleteMany();
        DB::url()->where('userid', $id)->whereNotNull('profileid')->deleteMany();
        DB::url()->where('userid', $id)->whereNotNull('qrid')->deleteMany();
        DB::url()->where('userid', $id)->update(['userid' => 0]);
        DB::members()->where('userid', $id)->deleteMany();
        DB::verification()->where('userid', $id)->deleteMany(); 
        $user->delete();
        return Helper::redirect()->back()->with('success', e('User has been deleted.'));
    }
    /**
     * Wipe User
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @param \Core\Request $request
     * @param integer $id
     * @param string $nonce
     * @return void
     */
    public function wipe(Request $request, int $id, string $nonce){
        
        \Gem::addMiddleware('DemoProtect');

        if(!Helper::validateNonce($nonce, 'user.delete')){
            return Helper::redirect()->back()->with('danger', e('An unexpected error occurred. Please try again.'));
        }

        if(!$user = DB::user()->where('id', $id)->first()){
            return Helper::redirect()->back()->with('danger', e('User not found. Please try again.'));
        }

        DB::bundle()->where('userid', $id)->deleteMany();
        DB::channels()->where('userid', $id)->deleteMany();
        DB::domains()->where('userid', $id)->deleteMany();
        DB::imports()->where('userid', $id)->deleteMany();
        DB::members()->where('userid', $id)->deleteMany();
        DB::overlay()->where('userid', $id)->deleteMany();
        DB::pixels()->where('userid', $id)->deleteMany();
        DB::profiles()->where('userid', $id)->deleteMany();
        DB::qrs()->where('userid', $id)->deleteMany();
        DB::splash()->where('userid', $id)->deleteMany();
        DB::stats()->where('urluserid', $id)->deleteMany();
        DB::tochannels()->where('userid', $id)->deleteMany();
        DB::url()->where('userid', $id)->deleteMany();
        DB::verification()->where('userid', $id)->deleteMany();   

        if(\Helpers\App::possible()){
            DB::subscription()->where('userid', $id)->deleteMany();
        }
        $user->delete();
        return Helper::redirect()->back()->with('success', e('User has been deleted and the data has been wiped out.'));
    }
    /**
     * Ban/Unban User
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @param integer $id
     * @return void
     */
    public function ban(int $id){

        \Gem::addMiddleware('DemoProtect');

        if(!$user = DB::user()->where('id', $id)->first()){
            return Helper::redirect()->back()->with('danger', e('User not found. Please try again.'));
        }

        if($user->banned){
            $user->banned = 0;
            $user->save();
            return Helper::redirect()->back()->with('success', e('User has been unbanned.'));
        } else {
            $user->banned = 1;
            $user->save();
            return Helper::redirect()->back()->with('success', e('User has been banned.')); 
        }

    }
    /**
     * View User
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @param integer $id
     * @return void
     */
    public function view(int $id){
        if(!$user = User::where('id', $id)->first()){
            return Helper::redirect()->to(route('admin.users'))->with('danger', e('User not found. Please try again.')); 
        }
        $plan = null;

        if($user->planid){
            $plan = DB::plans()->where('id', $user->planid)->first();
        }

        $urls = DB::url()->where('userid', $user->id)->orderByDesc('date')->paginate(15);

        if(_STATE == "DEMO") $user->email="demo@demo.com";

        View::set('title', e('View User'));

        return View::with('admin.users.view', compact('user', 'plan', 'urls'))->extend('admin.layouts.main');
    }
    /**
     * Login As
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @param integer $id
     * @param string $nonce
     * @return void
     */
    public function loginAs(Request $request, int $id, string $nonce){
        
        \Gem::addMiddleware('DemoProtect');

        if(!Helper::validateNonce($nonce, 'user.login.'.$id)){
            return Helper::redirect()->back()->with('danger', e('An unexpected error occurred. Please try again.'));
        }

        if(\Core\Auth::id() == $id) {
            return Helper::redirect()->back()->with('danger', e('You cannot login as your own account using this feature.'));
        }

        if(!$user = DB::user()->first($id)){
            return Helper::redirect()->to(route('admin.users'))->with('danger', e('User not found. Please try again.')); 
        }

        $current = user();
        
        \Core\Auth::logout();
        
        $request->session('logged_as', Helper::encrypt(json_encode(["id" => $current->id, "key" => $current->auth_key])));

        // Set Session
        $sessiondata = Helper::encrypt(json_encode(["loggedin" => true, "key" => $user->auth_key.$user->id]));
                
        $request->session(\Core\Auth::COOKIE, $sessiondata);

        return Helper::redirect()->to(route('dashboard'))->with('success', e("You have been successfully logged in as another user's account."));
    }

    /**
     * Delete Multiple Users
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @param \Core\Request $request
     * @param integer $id
     * @param string $nonce
     * @return void
     */
    public function deleteAll(Request $request){
        
        \Gem::addMiddleware('DemoProtect');
        
        $ids = json_decode($request->selected);

        if(!$ids || empty($ids)) return Helper::redirect()->back()->with('danger', e('No users was selected. Please try again.')); 

        foreach($ids as $id){

            if(!$user = DB::user()->where('id', $id)->first()) continue;

            DB::url()->where('userid', $id)->deleteMany();
            DB::stats()->where('urluserid', $id)->deleteMany();
            DB::payment()->where('userid', $id)->deleteMany();
            DB::domains()->where('userid', $id)->deleteMany();
            DB::splash()->where('userid', $id)->deleteMany();
            DB::overlay()->where('userid', $id)->deleteMany();
            DB::bundle()->where('userid', $id)->deleteMany();
            if(\Helpers\App::possible()){
                DB::subscription()->where('userid', $id)->deleteMany();
            }
            $user->delete();           
        }
        
        return Helper::redirect()->back()->with('success', e('Selected users have been deleted.'));
    } 
    /**
     * Ban Selected
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.3.4
     * @param \Core\Request $request
     * @return void
     */
    public function banAll(Request $request){
        
        \Gem::addMiddleware('DemoProtect');
        
        $ids = json_decode($request->selected);

        if(!$ids || empty($ids)) return Helper::redirect()->back()->with('danger', e('No users was selected. Please try again.')); 

        foreach($ids as $id){

            if(!$user = DB::user()->where('id', $id)->first()) continue; 
            
            $user->banned = 1;

            $user->save();           
        }
        
        return Helper::redirect()->back()->with('success', e('Selected users have been banned.'));
    } 
    /**
     * Email All
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.4
     * @param \Core\Request $request
     * @return void
     */
    public function emailAll(Request $request){
        
        \Gem::addMiddleware('DemoProtect');
        
        $ids = json_decode($request->selected);

        if(!$ids || empty($ids)) return Helper::redirect()->back()->with('danger', e('No users was selected. Please try again.')); 

        $emails = [];

        foreach($ids as $id){

            if(!$user = DB::user()->where('id', $id)->first()) continue; 

            if(!$user->email || empty($user->email)) continue;
            
            $emails[] = $user->email;
        }
        
        return Helper::redirect()->to(route('admin.email', ['email' => implode(',', $emails)]));
    } 
    /**
     * Testimonials
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @return void
     */
    public function testimonial(){
        
        $testimonials = config('testimonials');

        View::set('title', e('User Testimonials'));

        return View::with('admin.users.testimonials', compact('testimonials'))->extend('admin.layouts.main');
    }
    /**
     * Add testimonial
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @param \Core\Request $request
     * @return void
     */
    public function testimonialSave(Request $request){
        
        $testimonial = config('testimonials');

        if(!$testimonial) $testimonial = [];

        if(!$request->name) return Helper::redirect()->back()->with('danger', e('User name is required.')); 

        $testimonial[] = ['name' => Helper::RequestClean($request->name), 'email' => clean($request->email), 'job' => clean($request->job), 'testimonial' => Helper::RequestClean($request->testimonial)];

        $setting = DB::settings()->where('config', 'testimonials')->first();

        $setting->var = json_encode($testimonial);
        $setting->save();
        return Helper::redirect()->back()->with('success', e('Testimonial has been added.'));
    }    
    /**
     * Delete testimonial
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.0
     * @param \Core\Request $request
     * @param integer $id
     * @param string $nonce
     * @return void
     */
    public function testimonialDelete(Request $request, string $key, string $nonce){
        
        \Gem::addMiddleware('DemoProtect');

        if(!Helper::validateNonce($nonce, 'testimonial.delete')){
            return Helper::redirect()->back()->with('danger', e('An unexpected error occurred. Please try again.'));
        }
        
        $testimonial = (array) config('testimonials');
        
        $key = $key - 1;

        if(!isset($testimonial[$key])){
            return Helper::redirect()->back()->with('danger', e('Testimonial not found. Please try again.'));
        }
        
        unset($testimonial[$key]);

        

        $newarray = [];

        foreach($testimonial as $new){
            $newarray[] = $new;
        }

        $setting = DB::settings()->where('config', 'testimonials')->first();

        $setting->var = json_encode($newarray);
        $setting->save();

        return Helper::redirect()->back()->with('success', e('Testimonial has been deleted.'));
    }
    /**
     * Verify User
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.5
     * @param integer $userid
     * @param string $nonce
     * @return void
     */
    public function verify(int $userid, string $nonce){

        if(!Helper::validateNonce($nonce, 'verify-'.$userid)){
            return Helper::redirect()->back()->with('danger', e('An unexpected error occurred. Please try again.'));
        }
        
        if($user = DB::user()->first($userid)){
            $user->verified = 1;
            $user->save();
        }

        return Helper::redirect()->back()->with('success', e('User has been verified.'));
    }
    /**
     * Unverify
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 6.7
     * @param integer $userid
     * @param string $nonce
     * @return void
     */
    public function unverify(int $userid, string $nonce){

        if(!Helper::validateNonce($nonce, 'unverify-'.$userid)){
            return Helper::redirect()->back()->with('danger', e('An unexpected error occurred. Please try again.'));
        }
        
        if($user = DB::user()->first($userid)){
            $user->verified = 0;
            $user->save();
        }

        return Helper::redirect()->back()->with('success', e('User has been unverified.'));
    }
    /**
     * Send Verification Email
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 7.0.3
     * @param integer $id
     * @return void
     */
    public function verifyEmail(int $id){
        if(!$user = User::first($id)) return Helper::redirect()->back()->with('danger', e('An unexpected error occurred. Please try again.')); 

        \Helpers\Emails::renewEmail($user);            
        return Helper::redirect()->back()->with('success', e("An email has been sent to activate the account."));
    }
    /**
     * Import Users
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 7.1
     * @param \Core\Request $request
     * @return void
     */
    public function import(Request $request){

        if($request->isPost()){

            \Gem::addMiddleware('DemoProtect');

            if(!$file = $request->file('file')){
                return back()->with('danger', e('Incorrect format or empty file. Please upload .csv file.'));
            }

            if($file->ext != 'csv'){
                return back()->with('danger', e('Incorrect format or empty file. Please upload .csv file.'));
            }

            if($file->sizemb > \Helpers\App::maxSize()){
                return back()->with('danger', e('File is larger than {s}mb.', null, ['s' => \Helpers\App::maxSize()]));
            }
    
            $content = array_map('str_getcsv', file($file->location));
            unset($content[0]);
    
            $count = count($content);
    
            if($count == 0) return back()->with('danger', e('No users found.'));
            
            $error = null;
            $i = 0;

            foreach($content as $id => $data){

                if(DB::user()->where('username', $data[0])->first()) {
                    $error .= "<br>#".($id)." Failed: Username already exists";
                    continue;
                }
                
                if(!Helper::Email($data[1])) {
                    $error .= "<br>#".($id)." Failed: Invalid Email";
                    continue;
                }

                if(DB::user()->where('email', $data[1])->first()) {
                    $error .= "<br>#".($id)." Failed: Email already exists";
                    continue;
                }
                
                Helper::set("hashCost", 8);

                $user = DB::user()->create();                
                $user->username = clean($data[0]);
                $user->email = clean($data[1]);                                
                $user->password = Helper::Encode(md5($user->email.time().Helper::rand()));
                $user->date = Helper::dtime();
                $user->api = md5(Helper::rand(32).time());
                $user->uniquetoken = Helper::rand(32);
                $user->public = 0;
                $user->auth_key = Helper::Encode($user->email.Helper::dtime());
                $user->active = 1;   

                if(isset($data[2]) && !empty($data[2])){
                    if($plan = DB::plans()->where('id', clean($data[2]))->first()){
                        $user->pro = $plan->free ? 0 : 1;
                        $user->planid = $plan->id;
                        if(isset($data[3]) && !empty($data[3])) {
                            $user->expiration = date('Y-m-d H:i:s', strtotime($data[3]));
                        }
                    }
                }
                $i++;

                $user->save();
            }
            if($error){
                return back()->with('danger', e('{num} users were successfully imported but some errors occurred:'.$error, null, ['num' => $i]));
            }
    
            return back()->with('success', e('{num} users were successfully imported.', null, ['num' => $i]));   
        }

        View::set('title', e('Import Users'));

        return View::with('admin.users.import')->extend('admin.layouts.main');
    }
}