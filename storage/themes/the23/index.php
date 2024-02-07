<section id="hero" class="position-relative py-5">
<!--	<img src="--><?php //echo assets('images/shapes.svg') ?><!--" class="img-fluid position-absolute top-0 start-0 w-100 h-100 animate-float opacity-50 zindex-0">-->
	<div class="container position-relative zindex-1">
		<?php echo message() ?>
		<div class="row g-lg-5">
			<div class="col-lg-7 text-center text-lg-start">
				<h1 class="display-5 fw-bolder my-5"><strong><?php ee('World\'s fastest Link Shortener, QR Code and Link-in-bio Generator') ?><br> <span class="gradient-primary clip-text" data-toggle="typed" data-list="<?php echo implode(',', [e('Shorten Links').'.',e('Generate QR Codes').'.', e('Generate Bio Pages').'.']) ?>"></span></strong></h1>
<!--				<p class="col-lg-10 fs-5 mb-5">-->
<!--					--><?php //echo config('themeconfig')->description ?? e('Boost your campaigns by creating dynamic Links, QR codes and Bio Pages and get instant analytics.') ?>
<!--				</p>-->



                <div class="mb-3">
                    <?php message() ?>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="myTabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab1" data-toggle="tab" href="/"><b>Short Link</b></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-black" id="tab2" data-toggle="tab" href="/qr-codes">QR Code</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-black" id="tab3" data-toggle="tab" href="/bio-profiles">Link in Bio</a>
                        </li>
                    </ul>
                    <form method="post" action="<?php echo route('shorten') ?>" data-trigger="shorten-form" class="border-bottom custom-border mb-5 p-3 text-start">
                        <div class="input-group input-group-lg align-items-center">
                            <input type="text" class="form-control border-5 m-2" placeholder="<?php echo e("Paste a long link") ?>" name="url" id="url">
                            <div class="input-group-append">
                                <?php if(config('user_history') && !\Core\Auth::logged() && $urls = \Helpers\App::userHistory()): ?>
                                    <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#userhistory"><i data-bs-toggle="tooltip" title="<?php ee('Your latest links') ?>" class="fa fa-clock-rotate-left"></i></button>
                                <?php endif ?>
                                <button class="btn btn-warning d-none" type="button"><?php ee('Copy') ?></button>
                                <button class="btn btn-primary btn-lg" type="submit"><strong><?php ee('Shorten') ?></strong></button>
                            </div>
                        </div>
                        <?php if(!config('pro')): ?>
                            <a href="#advanced" data-bs-toggle="collapse" class="btn btn-sm btn-primary mb-2 mt-2"><?php ee('Do More') ?></a>
                            <div class="collapse row" id="advanced">
                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label for="custom" class="control-label fw-bold mb-2"><?php ee('Custom') ?></label>
                                        <input type="text" class="form-control p-2" name="custom" id="custom" placeholder="<?php echo e("Type your custom alias here")?>" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label for="pass" class="control-label fw-bold mb-2"><?php ee('Password Protection') ?></label>
                                        <input type="text" class="form-control p-2" name="pass" id="pass" placeholder="<?php echo e("Type your password here")?>" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if(!\Core\Auth::logged()) { echo \Helpers\Captcha::display('shorten'); } ?>
                    </form>
                </div>

				<div id="output-result" class="border border-success p-3 rounded d-none mb-3">
					<div class="d-flex align-items-center">
						<div id="qr-result" class="me-2"></div>
						<div id="text-result">
							<p><?php ee('Your link has been successfully shortened. Want to more customization options?') ?></p>
							<a href="<?php echo route('register') ?>" class="btn btn-sm btn-primary"><?php ee('Get started') ?></a>
						</div>
					</div>
				</div>



				<?php if(\Core\DB::plans()->where('free', '1')->first()): ?>
					<a href="<?php echo route('register') ?>" class="btn btn-primary px-4 py-3 fw-bold mb-1"><?php ee('Get Started for Free') ?></a>
					<p>
						<ul class="list-unstyled mb-2 text-muted small">
							<li class="mb-1"><i class="fa fa-check me-2"></i> <?php ee('Start free, upgrade later') ?></li>
							<li class="mb-1"><i class="fa fa-check me-2"></i> <?php ee('No credit card required') ?></li>
							<li class="mb-1"><i class="fa fa-check me-2"></i> <?php ee('Easy to use') ?></li>
						</ul>
					</p>
				<?php else: ?>
					<?php if(\Core\DB::plans()->whereNotEqual('trial_days', '0')->first()): ?>
						<a href="<?php echo route('pricing') ?>" class="btn btn-primary px-4 py-3 fw-bold mb-1"><?php ee('Get Started') ?></a>
						<p>
							<ul class="list-unstyled mb-2 text-muted small">
								<li class="mb-1"><i class="fa fa-check me-2"></i> <?php ee('Start with a free trial') ?></li>
								<li class="mb-1"><i class="fa fa-check me-2"></i> <?php ee('No credit card required') ?></li>
								<li class="mb-1"><i class="fa fa-check me-2"></i> <?php ee('Easy to use') ?></li>
							</ul>
						</p>
					<?php else: ?>
						<a href="<?php echo route('register') ?>" class="btn btn-primary px-4 py-3 fw-bold mb-5"><?php ee('Get Started') ?></a>
					<?php endif ?>
				<?php endif ?>
			</div>
			<div class="col-md-10 mx-auto col-lg-5 h-100 d-none d-sm-block">
				<div class="zindex-100 ml-lg-6">					
					<?php if (isset($themeconfig->hero) && !empty($themeconfig->hero)): ?>
						<img src="<?php echo uploads($themeconfig->hero) ?>" alt="<?php echo config("title") ?>" class="img-fluid mw-lg-120 rounded-top zindex-100">
					<?php else: ?>
						<img src="<?php echo assets("images/landing.png") ?>" alt="<?php echo config('title') ?>" class="img-fluid mw-lg-120 rounded-top zindex-100">
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="py-10 pt-5" id="features">
	<div class="container">
		<div class="row flex-lg-row-reverse align-items-center gy-5 py-5">
			<div class="col-md-6 offset-md-1 text-start">
				<span class="bg-primary py-2 px-3 rounded-pill">
					<strong class="gradient-primary clip-text fw-bolder"><?php ee('Collect data within minutes. Hassle-free.') ?></strong>
				</span>
				<h2 class="display-6 fw-bold mt-3 mb-5">
					<?php ee('One short link, infinite possibilities.') ?>
				</h2>
				<p class="lead mb-5">
					<?php ee('A short link is a powerful marketing tool when you use it carefully. It is not just a link but a medium between your customer and their destination. A short link allows you to collect so much data about your customers and their behaviors.') ?>
				</p>
				<ul class="list-unstyled mb-2">
					<li class="mb-4">
						<div class="d-flex">
							<div>
								<strong class="icon-md bg-primary d-flex align-items-center justify-content-center rounded-3">									
									<i class="fa fa-link gradient-primary clip-text fw-bolder"></i>
								</strong>
							</div>
							<div class="ms-3">
								<span class="fw-bold"><?php ee('Short Links') ?></span>
								<p><?php ee('Intuitive and trackable links') ?></p>
							</div>
						</div>
					</li>
					<li class="mb-4">
						<div class="d-flex">
							<div>
								<strong class="icon-md bg-primary d-flex align-items-center justify-content-center rounded-3">									
									<i class="fa fa-qrcode gradient-primary clip-text fw-bolder"></i>
								</strong>
							</div>
							<div class="ms-3">
								<span class="fw-bold"><?php ee('QR Codes') ?></span>
								<p><?php ee('Customizable and secure QR codes') ?></p>
							</div>
						</div>
					</li>
					<li class="mb-4">
						<div class="d-flex">
							<div>
								<strong class="icon-md bg-primary d-flex align-items-center justify-content-center rounded-3">									
									<i class="fa fa-mobile gradient-primary clip-text fw-bolder"></i>
								</strong>
							</div>
							<div class="ms-3">
								<span class="fw-bold"><?php ee('Beautiful Bio Pages') ?></span>
								<p><?php ee('Simple yet beautiful Bio Pages for your links') ?></p>
							</div>
						</div>
					</li>
				</ul>
				<a href="<?php echo route('register') ?>" class="btn btn-primary px-3 py-2 fw-bold"><?php ee('Get Started') ?></a>
			</div>
			<div class="col-md-5">
				<div class="p-4 p-md-5 rounded-3 shadow-sm position-relative h-100 gradient-primary">
					<h6 class="fw-bold text-white mb-2"><?php ee('Turn long links into short links') ?></h6>
					<h5 class="fw-bold text-white border rounded p-3"><span data-toggle="typed" data-list="<i class='fa fa-times-circle'></i> https://longurl.com/page/article-name,<i class='fa fa-check-circle'></i> <?php echo str_replace('www.', '', url('short')) ?>"></span></h5>
					<div class="position-absolute position-sm-relative card mt-10 top-0 ms-0 ms-md-5 start-0 d-block p-5 rounded shadow w-100 opacity-90 border-0">
						<h5 class="fw-bold"><?php ee('Where are most of your users located?') ?></h5>
						<div class="mt-4">
						<div class="mt-3">
							<img src="<?php echo assets('images/flags/ca.svg') ?>" class="icon rounded">
							<span class="align-middle ms-2">
								<?php ee('Canada') ?>
							</span>
							<div class="progress progress-sm mt-2">
								<div class="progress-bar" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
						<div class="mt-3">
							<img src="<?php echo assets('images/flags/us.svg') ?>" class="icon rounded">
							<span class="align-middle ms-2">
								<?php ee('United States of America') ?>
							</span>
							<div class="progress progress-sm mt-2">
								<div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
						<div class="mt-3">
							<img src="<?php echo assets('images/flags/gb.svg') ?>" class="icon rounded">
							<span class="align-middle ms-2">
								<?php ee('United Kingdom') ?>
							</span>
							<div class="progress progress-sm mt-2">
								<div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
						<div class="mt-3">
							<img src="<?php echo assets('images/flags/ng.svg') ?>" class="icon rounded border">
							<span class="align-middle ms-2">
								<?php ee('Nigeria') ?>
							</span>
							<div class="progress progress-sm mt-2">
								<div class="progress-bar" role="progressbar" style="width: 5%" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="bg-primary py-15 text-dark">
  	<div class="container">
		<div class="text-center mb-5 px-3">			
			<h2 class="fw-bolder display-5 mb-3"><strong><?php ee("Features that<br>you'll <span class=\"gradient-primary clip-text\">ever need</span>") ?></strong></h2>
			<p class="lead"><?php ee('We provide you with all the tools you need to increase your productivity.') ?></p>
		</div>
		<div class="row gy-4 py-5 mt-sm-5 justify-content-center text-start">
			<div class="col-xs-12 col-md-6 col-lg-4">
				<div class="h-100 card shadow-sm border-0">
					<div class="card-body p-4 p-sm-5">
						<i class="fa fa-spinner fa-2x gradient-primary clip-text"></i>
						<h4 class="fw-bold my-3"><?php ee('Custom Landing Page') ?></h4>
						<p>
							<?php ee('Create a custom landing page to promote your product or service on forefront and engage the user in your marketing campaign.') ?>
						</p>						
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-lg-4">
				<div class="h-100 card shadow-sm border-0">
					<div class="card-body p-4 p-sm-5">
						<i class="fa fa-layer-group fa-2x gradient-primary clip-text"></i>
						<h4 class="fw-bold my-3"><?php ee('CTA Overlays') ?></h4>
						<p>
							<?php ee('Use our overlay tool to display unobtrusive notifications, polls or even a contact on the target website. Great for campaigns.') ?>
						</p>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-lg-4">
				<div class="h-100 card shadow-sm border-0">
					<div class="card-body p-4 p-sm-5">
						<i class="fa fa-compass fa-2x gradient-primary clip-text"></i>
						<h4 class="fw-bold my-3"><?php ee('Event Tracking') ?></h4>
						<p>
							<?php ee('Add your custom pixel from providers such as Facebook and track events right when they are happening.') ?>
						</p>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-lg-4">
				<div class="h-100 card shadow-sm border-0">
					<div class="card-body p-4 p-sm-5">
						<i class="fa fa-bullseye fa-2x gradient-primary clip-text"></i>
						<h4 class="fw-bold my-3"><?php ee('Smart Targeting') ?></h4>
						<p>
							<?php ee('Easily apply restrictions to your links and target users in specific countries & languages using specific devices.') ?>
						</p>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-lg-4">
				<div class="h-100 card shadow-sm border-0">
					<div class="card-body p-4 p-sm-5">
					<i class="fa fa-mouse-pointer fa-2x gradient-primary clip-text"></i>
						<h4 class="fw-bold my-3"><?php ee('Track Everything') ?></h4>
						<p>
							<?php ee('Track users with our advanced reporting tool and know exactly which city & country your users are based.') ?>
						</p>
					</div>
				</div>
			</div>				
			<div class="col-xs-12 col-md-6 col-lg-4">
				<div class="h-100 card shadow-sm border-0">
					<div class="card-body p-4 p-sm-5">
						<i class="fa fa-users fa-2x gradient-primary clip-text"></i>
						<h4 class="fw-bold my-3"><?php ee('Team Management') ?></h4>
						<p>
							<?php ee('Invite your team members and assign them specific privileges to manage everything and collaborate together.') ?>
						</p>
					</div>
				</div>
			</div>		
			<div class="col-xs-12 col-md-6 col-lg-4">
				<div class="h-100 card shadow-sm border-0">
					<div class="card-body p-4 p-sm-5">
						<i class="fa fa-globe fa-2x gradient-primary clip-text"></i>
						<h4 class="fw-bold my-3"><?php ee('Branded Domain Names') ?></h4>
						<p>
							<?php ee("Easily add your own domain name for short links and take control of your brand name and your users' trust.") ?>
						</p>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-lg-4">
				<div class="h-100 card shadow-sm border-0">
					<div class="card-body p-4 p-sm-5">
						<i class="fa fa-box fa-2x gradient-primary clip-text"></i>
						<h4 class="fw-bold my-3"><?php ee('Campaigns & Channels') ?></h4>
						<p>
							<?php ee('Group and organize your Links, Bio Pages and QR Codes. With Campaigns, you can also get aggregated stats.') ?>
						</p>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-lg-4">
				<div class="h-100 card shadow-sm border-0">
					<div class="card-body p-4 p-sm-5">
						<i class="fa fa-terminal fa-2x gradient-primary clip-text"></i>
						<h4 class="fw-bold my-3"><?php ee('Developer API') ?></h4>
						<p>
							<?php ee('Use our powerful API to build custom applications or extend your own application with our powerful tools.') ?>
						</p>
					</div>
				</div>
			</div>
		</div>
  	</div>
</section>
<section class="py-10">
	<div class="container">
		<div class="row flex-lg-row-reverse align-items-center gy-5 py-5">
			<div class="col-md-6 order-last order-sm-first">
				<div class="card shadow border-0 p-3 mb-4 mt-5">
					<div class="d-flex">
						<img alt="<?php ee('New York, United States') ?>" src="<?php echo assets('images/flags/fr.svg') ?>" class="avatar text-white rounded mr-3">
						<div class="ms-3 mt-2">
							<h6 class="fw-bold mb-1"><?php ee('Someone scanned your QR Code') ?></h6>
							<div class="h6 mb-0 text-sm">
								<span class="text-muted"><?php ee('Paris, France') ?></span>
							</div>
						</div>
						<div class="ms-auto d-none d-lg-block mt-3">
							<span class="badge badge-pill gradient-primary p-2"><?php ee('{d} minutes ago', null, ['d' => $numbers[0]]) ?></span>
						</div>
					</div>
				</div>
				<div class="card shadow gradient-primary border-0 p-3 mb-4">
					<div class="d-flex">
						<img alt="<?php ee('New York, United States') ?>" src="<?php echo assets('images/flags/us.svg') ?>" class="avatar text-white rounded mr-3">
						<div class="ms-3 mt-2 text-white">
							<h6 class="fw-bold mb-1"><?php ee('Someone visited your Link') ?></h6>
							<div class="h6 mb-0 text-sm">
								<span><?php ee('New York, United States') ?></span>
							</div>
						</div>
						<div class="ms-auto d-none d-lg-block mt-3">
							<span class="badge badge-pill bg-white text-dark p-2"><?php ee('{d} minutes ago', null, ['d' => $numbers[1]]) ?></span>
						</div>
					</div>
				</div>
				<div class="card shadow border-0 p-3 mb-4">
					<div class="d-flex">
						<img alt="<?php ee('New York, United States') ?>" src="<?php echo assets('images/flags/gb.svg') ?>" class="avatar text-white rounded mr-3">
						<div class="ms-3 mt-2">
							<h6 class="fw-bold mb-1"><?php ee('Someone viewed your Bio Page') ?></h6>
							<div class="h6 mb-0 text-sm">
								<span class="text-muted"><?php ee('London, United Kingdom') ?></span>
							</div>
						</div>
						<div class="ms-auto d-none d-lg-block mt-3">
							<span class="badge badge-pill gradient-primary p-2"><?php ee('{d} minutes ago', null, ['d' => $numbers[2]]) ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6 order-first order-sm-last text-start">
				<span class="bg-primary py-2 px-3 rounded-pill">
					<strong class="gradient-primary clip-text fw-bolder"><?php ee('Get instant results') ?></strong>
				</span>
				<h2 class="display-6 fw-bold mb-5 mt-3">
					<?php ee('Track & Optimize') ?>
				</h2>
				<p class="lead mb-5 pe-5">
					<?php ee('Understanding your users and customers will help you increase your conversion. Our system allows you to track everything. Whether it is the amount of clicks, the country or the referrer, the data is there for you to analyze it.') ?>
				</p>
				<a href="<?php echo route('register') ?>" class="btn btn-primary px-3 py-2 fw-bold"><?php ee('Get Started') ?></a>
			</div>
		</div>
		<div class="row flex-lg-row-reverse align-items-center gy-5 py-5 mt-8 text-start">
			<div class="col-md-6 order-first order-sm-last">
				<div class="card shadow border-0 p-4 mt-5 mx-2 mx-md-4 backdrop-cards">
					<h5 class="fw-bolder"><?php ee('Invite People') ?></h5>
					<span class="text-muted"><?php ee('Invite your teammates & work together') ?></span>
					<div class="d-block mt-3"><strong><?php ee('Members') ?></strong> <small class="text-muted">(3/5)</small></div>					
					<div class="d-flex my-2">
						<img alt="<?php ee('Invite your teammates & work together') ?>" src="<?php echo assets('images/avatar-f1.svg') ?>" class="avatar-sm rounded-circle mr-3">
						<div class="ms-3 mt-2">
							<h6 class="fw-bold mb-1"><?php ee('Jane Doe') ?></h6>
							<div class="h6 mb-0 text-sm">
								<small class="text-muted">jane.doe@<?php echo str_replace('www.', '', \Core\Helper::parseUrl(config('url'), 'host')) ?></small>
							</div>							
						</div>
					</div>					
					<div class="d-flex my-2">
						<img alt="<?php ee('Invite your teammates & work together') ?>" src="<?php echo assets('images/avatar-m2.svg') ?>" class="avatar-sm rounded-circle mr-3">
						<div class="ms-3 mt-2">
							<h6 class="fw-bold mb-1"><?php ee('Barry Tone') ?></h6>
							<div class="h6 mb-0 text-sm">
								<small class="text-muted">barry.tone@<?php echo str_replace('www.', '', \Core\Helper::parseUrl(config('url'), 'host')) ?></small>
							</div>
						</div>
						<div class="ms-auto d-none d-lg-block mt-3">
							<span class="badge badge-pill bg-primary p-2"><strong class="gradient-primary clip-text"><?php ee('Invited') ?></strong></span>
						</div>
					</div>
					<div class="d-flex my-2">
						<img alt="<?php ee('Invite your teammates & work together') ?>" src="<?php echo assets('images/avatar-m1.svg') ?>" class="avatar-sm rounded-circle mr-3">
						<div class="ms-3 mt-2">
							<h6 class="fw-bold mb-1"><?php ee('John Doe') ?></h6>
							<div class="h6 mb-0 text-sm">
								<small class="text-muted">john.doe@<?php echo str_replace('www.', '', \Core\Helper::parseUrl(config('url'), 'host')) ?></small>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6 order-last order-sm-first">
				<span class="bg-primary py-2 px-3 rounded-pill">
					<strong class="gradient-primary clip-text fw-bolder"><?php ee('Collaborate with your teammates') ?></strong>
				</span>
				<h2 class="display-6 fw-bold mb-5 mt-3">
					<?php ee('Invite & Work Together') ?>
				</h2>
				<p class="lead mb-5">
					<?php ee('Invite your teammates within seconds and work together as team to manage your Links, Bio Pages and QR codes. Team members can can be assigned specific privileges and can work on different workspaces.') ?>
				</p>
				<a href="<?php echo route('register') ?>" class="btn btn-primary px-3 py-2 fw-bold"><?php ee('Get Started') ?></a>
			</div>
		</div>
		<div class="row mt-10">
			<div class="col-md-12">
				<div class="p-2 p-md-5 bg-primary rounded-4 border-0">
					<div class="row align-items-center">
						<div class="col-md-4 order-last order-sm-first d-none d-md-block">
							<div class="row">
								<div class="col-6 text-center">
									<div class="client mb-5">
										<img alt="Slack" src="<?php echo assets("images/slack.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">Slack</p>
									</div>
									<div class="client mb-5">
										<img alt="Zapier" src="<?php echo assets("images/zapier.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">Zapier</p>
									</div>
									<div class="client mb-5">
										<img alt="Google Tag Manager" src="<?php echo assets("images/gtm.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">Google Tag Manager</p>
									</div>
									<div class="client mb-5">
										<img alt="Facebook Pixels" src="<?php echo assets("images/facebook.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">Facebook</p>
									</div>
								</div>
								<div class="col-6 py-5 text-center">
									<div class="client mb-5">
										<img alt="Bing" src="<?php echo assets("images/wp.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">WordPress</p>
									</div>									
									<div class="client mb-5">
										<img alt="Bing" src="<?php echo assets("images/shortcuts.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">Shortcuts</p>
									</div>
									<div class="client mb-5">
										<span class="icon-md bg-white shadow-sm p-2 rounded-circle d-inline-block"><i class="fab fa-x-twitter fs-2"></i></span>
										<p class="fw-bold text-dark my-3">Twitter</p>
									</div>
									<div class="client mb-5">
										<img alt="Snapchat" src="<?php echo assets("images/snapchat.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">Snapchat</p>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="text-center mb-5">
								<h3 class="fw-bold mt-4"><strong><?php ee('Integrations') ?></strong></h3>
								<p><?php ee('Connect your links to third-party applications so they can share information such as traffic and analytics.') ?></p>
								<a href="<?php echo route('register') ?>" class="btn btn-primary px-3 py-2 fw-bold mt-3"><?php ee('Get Started') ?></a>
							</div>
						</div>
						<div class="col-md-4 d-none d-md-block">
							<div class="row">
								<div class="col-6 py-5 text-center">
									<div class="client mb-5">
										<img alt="Bing" src="<?php echo assets("images/bing.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">Bing</p>
									</div>
									<div class="client mb-5">
										<img alt="Reddit" src="<?php echo assets("images/reddit.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">Reddit</p>
									</div>
									<div class="client mb-5">
										<img alt="Google Analytics" src="<?php echo assets("images/ga.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">Google Analytics</p>
									</div>
									<div class="client mb-5">
										<img alt="LinkedIn" src="<?php echo assets("images/linkedin.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">LinkedIn</p>
									</div>
								</div>
								<div class="col-6 text-center">
									<div class="client mb-5">
										<img alt="Pinterest" src="<?php echo assets("images/pinterest.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">Pinterest</p>
									</div>
									<div class="client mb-5">
										<img alt="Quora" src="<?php echo assets("images/quora.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">Quora</p>
									</div>
									<div class="client mb-5">
										<img alt="TikTok" src="<?php echo assets("images/tiktok.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">TikTok</p>
									</div>
									<div class="client mb-5">
										<img alt="Adroll" src="<?php echo assets("images/aroll.svg") ?>" class="icon-md bg-white shadow-sm p-2 rounded-circle">
										<p class="fw-bold text-dark my-3">Adroll</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row mt-5 text-start">
						<div class="col-md-6 mb-2 mb-md-0">
							<div class="h-100 card shadow-sm border-0">							
								<div class="card-body p-4 p-sm-5">
									<i class="fa fa-th fa-2x gradient-primary clip-text"></i>
									<h4 class="fw-bold my-3"><?php ee('Tracking Pixels') ?></h4>
									<p>
										<?php ee('Add your custom pixel from providers such as Facebook & Google Tag Manager and track events right when they are happening.') ?>
									</p>
								</div>
							</div>
						</div>
						<div class="col-md-6 mb-0">
							<div class="h-100 card shadow-sm border-0">
								<div class="card-body p-4 p-sm-5">
									<i class="fa fa-bell fa-2x gradient-primary clip-text"></i>
									<h4 class="fw-bold my-3"><?php ee('Notifications') ?></h4>
									<p>
										<?php ee('Get notified when users use your links via various channels such Slack and webhook services like Zapier.') ?>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php if($testimonials = (array) config('testimonials')): ?>
	<section class="bg-primary">
		<div class="container py-8">
			<div class="row my-5 justify-content-center text-center">
				<div class="col-lg-8 col-md-10">
					<h2 class="mb-2 fw-bolder h1"><strong><?php ee("Don't take our word for it.") ?></strong></h2>
					<h2 class="mb-5 fw-bolder h1"><strong class="gradient-primary clip-text"><?php ee('Trust our customers.') ?></strong></h2>
				</div>
			</div>
			<div class="row">
				<?php foreach(array_chunk($testimonials, ceil(count($testimonials)/3)) as $testimonials): ?>
					<div class="col-lg-4 px-sm-2">
						<?php foreach($testimonials as $testimonial): ?>
							<div class="card shadow-sm border-0 mb-4 mx-lg-1">
								<div class="card-body p-3">
									<p><?php echo $testimonial->testimonial ?></p>
									<div class="d-flex align-items-center mt-3">
										<div>
											<?php echo $testimonial->email ? '<img src="https://www.gravatar.com/avatar/'.md5(trim($testimonial->email)).'?s=64&d=identicon" class="avatar-sm rounded-circle" alt="'.$testimonial->name.'">': '' ?>
										</div>
										<div class="ms-3">
											<span class="h6 mb-0"><?php echo $testimonial->name ?>  <?php echo $testimonial->job  ? '<small class="d-block text-muted">'.$testimonial->job.'</small>' : '' ?></span>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach ?>
					</div>
				<?php endforeach ?>
			</div>
		</div>
	</section>
<?php endif ?>
<?php if (config("homepage_stats")): ?>
	<section class="py-8">
        <div class="container">
			<div class="row align-items-center">
				<div class="col-lg-6">
					<h2 class="fw-bolder display-5 mb-5 text-center text-md-start"><strong><?php ee("Let <br><span class=\"gradient-primary clip-text\">the numbers</span><br> do the talking") ?>.</strong></h2>
				</div>
				<div class="col-lg-6">
					<div class="row">
						<div class="col-md-6 mb-5">
							<div class="text-center bg-primary py-5 px-2 px-lg-5 rounded">
								<h3 class="h5 text-capitalize"><span class="gradient-primary fw-bolder clip-text"><?php ee('Powering') ?></span></h3>
								<div class="h1">
									<span class="counter"><?php echo "700,000" ?></span>
									<span class="counter-extra">+</span>
								</div>
								<h3 class="h6 text-capitalize fw-bold"><?php ee('Links') ?></h3>
							</div>
						</div>
						<div class="col-md-6 mb-5">
							<div class="text-center bg-primary py-5 px-2 px-lg-5 rounded">
								<h3 class="h5 text-capitalize"><span class="gradient-primary fw-bolder clip-text"><?php ee('Serving') ?></span></h3>
								<div class="h1">
									<span class="counter"><?php echo "15M" ?></span>
									<span class="counter-extra">+</span>
								</div>
								<h3 class="h6 text-capitalize fw-bold"><?php ee('Clicks') ?></h3>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-5">
							<div class="text-center bg-primary py-5 px-2 px-lg-5 rounded">
								<h3 class="h5 text-capitalize"><span class="gradient-primary fw-bolder clip-text"><?php ee('Trusted by') ?></span></h3>
								<div class="h1">
									<span class="counter"><?php echo "1.2M" ?></span>
									<span class="counter-extra">+</span>
								</div>
								<h3 class="h6 text-capitalize fw-bold"><?php ee('Amazing Customers') ?></h3>
							</div>
						</div>
						<div class="col-md-6 mb-5 d-none d-md-block">
							<a href="<?php echo route('register') ?>">
								<div class="bg-primary py-5 px-2 px-lg-5 rounded h-100 d-flex align-items-center justify-content-center">
									<h3 class="h5 text-capitalize">
										<span class="gradient-primary fw-bolder clip-text"><?php ee('Get Started') ?> <i class="fa fa-chevron-right small"></i></span>
									</h3>
								</div>
							</a>
						</div>
					</div>
				</div>
			</div>
        </div>
    </section>
<?php endif ?>
<section class="py-5">
	<div class="container">
		<div class="h-100 p-5 gradient-primary text-white with-shapes rounded-4 border-0 text-start">
			<div class="row align-items-center gy-lg-5">
				<div class="col-sm-8">
					<h2 class="fw-bold"><?php ee('Take control of your links') ?></h2>
					<p><?php ee('You are one click away from taking control of all of your links, and instantly get better results.') ?></p>
				</div>
				<div class="col-sm-4 text-end">
					<a class="btn btn-light text-primary btn-lg d-block d-sm-inline-block" href="<?php echo route('register') ?>"><?php ee('Get Started') ?></a>
				</div>
			</div>
		</div>
	</div>
</section>
<?php if(config('user_history') && !\Core\Auth::logged() && $urls = \Helpers\App::userHistory()): ?>
<div class="modal fade" id="userhistory" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title fw-bolder"><?php ee('Your latest links') ?></h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">			
				<?php foreach($urls as $url): ?>
					<h6><a href="<?php echo $url['url'] ?>" target="_blank"><?php echo $url['meta_title'] ?></a></h6>
					<a href="<?php echo \Helpers\App::shortRoute($url['domain'], $url['alias'].$url['custom']) ?>" class="text-muted"><?php echo \Helpers\App::shortRoute($url['domain'], $url['alias'].$url['custom']) ?></a>					
				<?php endforeach ?>
				<div class="d-flex mt-5 border rounded p-2">
					<div class="opacity-8">
						<?php ee('Want more options to customize the link, QR codes, branding and advanced metrics?') ?>
					</div>
					<div class="ml-auto">
						<a href="<?php echo route('register') ?>" class="btn btn-primary btn-xs"><?php ee('Get Started') ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif ?>

<style>
    .custom-border {
        border-left: 1px solid #dee2e6; /* Left border width and color */
        border-right: 1px solid #dee2e6; /* Right border width and color */
        padding: 0; /* Optional: Add padding for better appearance */
        border-radius: 0px 0px 6px 6px;
    }
</style>

<script>
    // JavaScript to handle tab redirects
    document.getElementById('tab2').addEventListener('click', function() {
        window.location.href = 'https://second.com';
    });

    document.getElementById('tab3').addEventListener('click', function() {
        window.location.href = 'https://third.com';
    });
</script>