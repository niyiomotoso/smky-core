<form action="<?php echo route('bio.update', [$bio->id]) ?>" method="post">
    <div class="d-flex mb-5">
        <h1 class="h4 mb-0 fw-bold"><?php ee('Customize') ?>  <?php echo $bio->name ?>  <?php echo (user()->defaultbio == $bio->id ? '<span class="badge bg-primary ms-2">'.e('Default').'</span>' : '') ?></h1>
        <div class="ms-auto">
            <button type="submit" class="btn btn-lg btn-primary"><span id="loading"></span> <?php ee('Save') ?></button>
        </div>
    </div>
    <div class="card card-body shadow-sm">
        <div class="d-block d-md-flex align-items-center">
            <div>
                <ul class="nav nav-pills overflow-x">
                    <li class="nav-item mb-3 mb-md-0">
                        <a href="#links" class="nav-link text-muted active" data-trigger="switcher"><i class="fa fa-layer-group me-2"></i> <span class="align-middle"><?php ee('Content') ?></span></a>
                    </li>
                    <li class="nav-item mb-3 mb-md-0">
                        <a href="#defaultbios" class="nav-link text-muted" data-trigger="switcher"><i class="fa fa-layer-group me-2"></i> <span class="align-middle"><?php ee('Template Pages') ?></span></a>
                    </li>
                    <li class="nav-item mb-3 mb-md-0">
                        <a href="#social" class="nav-link text-muted" data-trigger="switcher"><i class="fa fa-share me-2"></i>  <span class="align-middle"><?php ee('Social Links') ?></span></a>
                    </li>
                    <li class="nav-item mb-3 mb-md-0">
                        <a href="#appearance" class="nav-link text-muted" data-trigger="switcher"><i class="fa fa-desktop me-2"></i>  <span class="align-middle"><?php ee('Appearance') ?></span></a>
                    </li>
                    <li class="nav-item mb-3 mb-md-0">
                        <a href="#advanced" class="nav-link text-muted" data-trigger="switcher"><i class="fa fa-cogs me-2"></i>  <span class="align-middle"><?php ee('Settings') ?></span></a>
                    </li>
                    <li class="nav-item mb-3 mb-md-0">
                        <a href="#data" class="nav-link text-muted" data-trigger="switcher"><i class="fa fa-download me-2"></i>  <span class="align-middle"><?php ee('Data') ?></span></a>
                    </li>
                </ul>
            </div>
            <div class="ms-auto">
                <a href="<?php echo route('stats', [$bio->urlid]) ?>" class="btn btn-light border"><i class="text-black" data-feather="bar-chart"></i></a>
                <button type="button" data-bs-toggle="modal" data-trigger="shortinfo" data-shorturl="<?php echo Helpers\App::shortRoute($url->domain, $bio->alias) ?>" class="btn btn-light border"><i class="text-black" data-feather="info"></i></button>
                <a href="<?php echo \Helpers\App::shortRoute($url->domain, $bio->alias) ?>" class="btn btn-success" target="_blank"><?php ee('View Bio') ?></a>
            </div>
        </div>
    </div>
    <?php echo csrf() ?>
    <div class="row">
        <div class="col-md-7" id="generator">
            <div class="collapse switcher" id="defaultbios">
                <?php foreach($defaultBios as $category => $defaultBiosByCategory): ?>
                    <h4 class="fw-bold mb-3 mt-5"><?php echo $category ?></h4>
                    <div class="card card-body shadow-sm">
                        <div class="row mt-3">
                            <br/>
                            <?php foreach($defaultBiosByCategory as $defaultBio):
                                $bgColors = extractBgColors($defaultBio->data);
                                ?>
                                <div class="col-6 col-sm-4 col-xl-3 mb-2">
                                    <a  href="<?php echo route('bio.applyDefaultBio', [$bio->id, $defaultBio->id]) ?>">
                                        <div role="button" class="d-block text-center border rounded p-3" style="height:100px;
                                                background-color: <?php echo $bgColors['bg'] ?>; background-image: linear-gradient( <?php echo $bgColors['gradient_start'] ?>, <?php echo $bgColors['gradient_stop'] ?> );">
                                            <div class="rounded-pill d-block text-decoration-none" style="background:#fff;width:100%;height:20px;color: #000"><?php echo $defaultBio->name ?></div>
                                            <div class="d-block pt-1" style="color: <?php echo $bgColors['text_color'] ?>;"><?php echo $defaultBio->description ?></div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endforeach ?>

            </div>
            <div class="collapse switcher show" id="links">
                <div class="card card-body shadow-sm">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="me-3 mb-2 position-relative" style="max-width:200px">
                                <a href="#" data-trigger="uploadavatar">
                                <?php if(isset($bio->data->avatar)): ?>
                                    <img src="<?php echo uploads($bio->data->avatar, 'profile') ?>" class="rounded w-100" id="useravatar">
                                <?php else: ?>
                                    <img src="<?php echo user()->avatar()?>" class="rounded w-100" id="useravatar">
                                <?php endif ?>
                                </a>
                                <div class="position-absolute top-0 end-0">
                                    <button type="button" class="btn btn-default btn-sm" data-trigger="uploadavatar" aria-expanded="false"><i data-feather="upload"></i></button>
                                    <input type="file" name="avatar" id="avatar" class="d-none" data-error="<?php ee('Avatar must be either a PNG or a JPEG (Max 500kb).') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="form-group">
                                <label class="form-label"><?php ee('Bio Page Name') ?></label>
                                <input type="text" class="form-control p-2" name="name" placeholder="e.g. For Instagram" value="<?php echo $bio->name ?>" data-required>
                            </div>
                            <div class="form-group mt-4">
                                <label class="form-label"><?php ee('Bio Page Alias') ?></label>
                                <div class="d-flex">
                                    <div class="input-select rounded">
                                        <!--<select name="domain" id="domain" class="form-select p-2" data-toggle="select">
                                            <?php /*foreach($domains as $domain): */?>
                                                <option value="<?php /*echo $domain */?>" <?php /*echo $domain == $url->domain ? 'selected' : '' */?>><?php /*echo $domain */?></option>
                                            <?php /*endforeach */?>
                                        </select>-->
                                        <select name="domain" id="domain" class="form-select p-2" data-toggle="select">
                                                <option value="<?php echo $url->domain  ?>" selected><?php echo $url->domain  ?></option>
                                        </select>
                                        <p class="form-text"><?php ee('Choose domain to generate the link with') ?></p>
                                    </div>
                                    <div class="ps-2">
                                        <input type="text" class="form-control p-2" name="custom" value="<?php echo $bio->alias ?>" placeholder="e.g. my-page">
                                        <p class="form-text"><?php ee('Leave this field empty to generate a random alias.') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="linkcontent"></div>
                <div class="text-center mb-5">
                    <button type="button" class="btn btn-primary btn-lg w-100 p-2" data-bs-toggle="modal" data-bs-target="#contentModal"><i class="fa fa-plus me-2"></i> <?php ee('Add Link or Content') ?></button>
                </div>
            </div>
            <div class="collapse switcher" id="social">
                <h4 class="fw-bold mb-3"><?php ee('Social Links') ?></h4>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="input-select">
                                    <select name="platform" class="form-select p-2 ignore" data-toggle="select">
                                        <?php foreach($platforms as $key => $array): ?>
                                            <?php if(!isset($bio->data->social->{$key}) || empty($bio->data->social->{$key})): ?>
                                                <option value="<?php echo $key ?>" data-icon="<?php echo urlencode($array['icon']) ?>"><?php echo $array['name'] ?></option>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control p-2 ignore" name="socialink" placeholder="https://">
                            </div>
                        </div>
                        <button type="button" data-trigger="addsocial" class="btn btn-primary mt-3" data-error="<?php ee('Please enter a valid link') ?>" data-error-alt="<?php ee('You have already added a link to this platform') ?>"><?php ee('Add') ?></button>
                    </div>
                </div>
                <div class="card card-body shadow-sm" id="sociallinksholder">
                    <?php if(isset($bio->data->social)): ?>
                        <?php foreach($bio->data->social as $name => $sociallink): ?>
                            <?php if(empty($sociallink) || !isset($platforms[$name])) continue ?>
                            <div class="border rounded p-2 mb-3 socialsortable">
                                <div class="mb-3 d-flex align-items-center">                                    
                                    <i class="fs-4 fa fa-align-justify handle ms-1" data-bs-toggle="tooltip" title="<?php ee('Move') ?>"></i>
                                    <span class="ms-2 fw-bold"><?php echo ucfirst($name) ?></span>
                                    <div class="ms-auto d-flex align-items-center">
                                        <a class="ms-auto fs-6 pe-2" data-trigger="deletesocial" href=""><i class="fa fa-times text-dark fs-4" data-bs-toggle="tooltip" title="<?php ee('Delete') ?>"></i></a>
                                    </div>                                    
                                </div>
                                <div class="input-group">
                                    <div class="input-group-text bg-white"><i class="fab fa-<?php echo $name ?>"></i></div>
                                    <input type="text" class="form-control p-2" name="social[<?php echo $name ?>]" value="<?php echo $sociallink ?>" placeholder="https://" data-error="<?php ee('Please enter a valid link') ?>">
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>
                </div>

                <h4 class="fw-bold mb-3 mt-5"><?php ee('Settings') ?></h4>
                <div class="card card-body shadow-sm">
                    <div class="form-group">
                        <label class="form-label fw-bold"><?php ee('Position') ?></label>
                        <select name="socialposition" class="form-select p-2">
                            <option value="off"<?php echo isset($bio->data->style->socialposition) && $bio->data->style->socialposition == 'off' ? ' selected' : '' ?>><?php ee('Off') ?></option>
                            <option value="top"<?php echo isset($bio->data->style->socialposition) && $bio->data->style->socialposition == 'top' ? ' selected' : '' ?>><?php ee('Top') ?></option>
                            <option value="bottom"<?php echo isset($bio->data->style->socialposition) && $bio->data->style->socialposition == 'bottom' ? ' selected' : '' ?>><?php ee('Bottom') ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="collapse switcher" id="appearance">
                <h4 class="fw-bold mb-3"><?php ee('Header Layout') ?></h4>
                <div class="card card-body shadow-sm">
                    <div class="row">
                        <div class="col-md-4">
                            <label role="button" data-trigger="chooselayout" data-value="layout1" class="d-block text-center border rounded-3 <?php echo !isset($bio->data->style->layout) || $bio->data->style->layout == 'layout1' ? 'border-secondary': '' ?>" style="height: 150px;">
                                <svg width="200" height="150">
                                    <circle cx="50%" cy="50" r="40" stroke="#ccc" stroke-width="4" fill="#eee" />
                                    <rect x="50" y="110" width="100" height="20" fill="#ddd" />
                                </svg>
                                <input type="radio" name="layout" value="layout1" class="d-none" <?php echo !isset($bio->data->style->layout) || $bio->data->style->layout == 'layout1' ? 'checked': '' ?>>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label role="button" data-trigger="chooselayout" data-value="layout2" class="d-block text-center border rounded-3 <?php echo isset($bio->data->style->layout) && $bio->data->style->layout == 'layout2' ? 'border-secondary': '' ?>" style="height: 150px;">
                                <svg width="100%" height="150" class="rounded">
                                    <rect x="0" y="0" width="100%" height="50" fill="#222e3c" />
                                    <circle cx="50%" cy="50" r="40" stroke="#ccc" stroke-width="4" fill="#eee" />
                                    <rect x="50" y="110" width="100" height="20" fill="#ddd" />
                                </svg>
                                <input type="radio" name="layout" value="layout2" class="d-none" <?php echo isset($bio->data->style->layout) && $bio->data->style->layout == 'layout2' ? 'checked': '' ?>>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label role="button" data-trigger="chooselayout" data-value="layout3" class="d-block text-center border rounded-3 <?php echo isset($bio->data->style->layout) && $bio->data->style->layout == 'layout3' ? 'border-secondary': '' ?>" style="height: 150px;">
                                <svg width="100%" height="150" class="rounded">
                                    <rect x="0" y="0" width="100%" height="150" fill="#222e3c" />
                                    <circle cx="40" cy="70" r="30" stroke="#ccc" stroke-width="4" fill="#eee" />
                                    <rect x="80" y="60" width="100" height="20" fill="#ddd" />
                                </svg>
                                <input type="radio" name="layout" value="layout3" class="d-none" <?php echo isset($bio->data->style->layout) && $bio->data->style->layout == 'layout3' ? 'checked': '' ?>>
                            </label>
                        </div>
                    </div>

                    <div class="form-group collapse mt-3 <?php echo isset($bio->data->style->layout) && ($bio->data->style->layout == 'layout2' || $bio->data->style->layout == 'layout3') ? 'show': '' ?>" id="layoutbanner">
                        <label for="layoutbanner" class="form-label"><?php ee('Header Banner') ?></label>
                        <input type="file" class="form-control mb-4" name="layoutbanner" id="layoutbanner" data-error="<?php ee('Please choose a valid image. JPG, PNG') ?>">
                    </div>
                </div>
                <h4 class="fw-bold mb-3 mt-5"><?php ee('Themes') ?></h4>
                <input type="hidden" name="themeid" value="">
				<div class="card card-body shadow-sm">
                    <input type="hidden" name="theme" value="<?php echo $bio->data->style->theme ?? '' ?>">
                    <div class="row mt-3">
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3 biobg_gradientbg" style="height:100px;" data-trigger="changetheme" data-theme="biobg_gradientbg" onclick="customTheme('biobg_gradientbg', '#ffffff', '#000000', '#ffffff');">
                                <p class="d-block" style="color:#ffffff">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#ffffff;width:100%;height:30px;color:#000000"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3 biobg_boxes" style="height:100px;" data-trigger="changetheme" data-theme="biobg_boxes" onclick="customTheme('biobg_boxes', '#000000', '#ffffff', '#000000');">
                                <p class="d-block" style="color:#000000">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#000000;width:100%;height:30px;color:#ffffff"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3 biobg_shapes" style="height:100px;overflow:hidden;position:relative;" data-trigger="changetheme" data-theme="biobg_shapes" onclick="customTheme('biobg_shapes', '#ffffff', '#000000', '#ffffff');">
                                <p class="d-block" style="color:#ffffff;z-index:1;position:relative;">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#ffffff;width:100%;height:30px;color:#000000;z-index:1;position:relative;"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3 biobg_iso" style="height:100px;" data-trigger="changetheme" data-theme="biobg_iso" onclick="customTheme('biobg_iso', '#252731', '#ffffff', '#000000');">
                                <p class="d-block" style="color:#000000">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#252731;width:100%;height:30px;color:#fff"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3 biobg_paper" style="height:100px;" data-trigger="changetheme" data-theme="biobg_paper" onclick="customTheme('biobg_paper', '#e6b800', '#ffffff', '#000000');">
                                <p class="d-block" style="color:#000000">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#e6b800;width:100%;height:30px;color:#fff"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3 biobg_pattern" style="height:100px;" data-trigger="changetheme" data-theme="biobg_pattern" onclick="customTheme('biobg_pattern', '#00E692', '#ffffff', '#000000');">
                                <p class="d-block" style="color:#000000">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#00E692;width:100%;height:30px;color:#fff"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3 biobg_coil" style="height:100px;" data-trigger="changetheme" data-theme="biobg_coil" onclick="customTheme('biobg_coil', '#f42a8b', '#ffffff', '#000000');">
                                <p class="d-block" style="color:#000000">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#f42a8b;width:100%;height:30px;color:#fff"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <?php foreach($themes as $theme): ?>
                            <div class="col-6 col-sm-4 col-xl-3 mb-2">
                                <div role="button" class="d-block text-center border rounded p-3" style="height:100px;<?php echo $theme->data->style ?>" data-trigger="changetheme" onclick="changeTheme('<?php echo $theme->data->singlecolor ?? '' ?>', '<?php echo $theme->data->gradientstart ?? '' ?>', '<?php echo $theme->data->gradientstop ?? '' ?>', '<?php echo $theme->data->buttoncolor ?>', '<?php echo $theme->data->buttontextcolor ?>', '<?php echo $theme->data->textcolor ?>', '<?php echo $theme->data->bgtype ?>', '<?php echo $theme->data->buttonstyle ?? '' ?>', '<?php echo $theme->data->gradientangle ?? '' ?>', '<?php echo $theme->data->shadow ?? '' ?>', '<?php echo $theme->data->shadowcolor ?? '' ?>', '<?php echo $theme->id ?>');">
                                    <p class="d-block" style="color:<?php echo $theme->data->textcolor ?>">Hello</p>
                                    <a href="#" class="d-block py-1 text-decoration-none" style="<?php echo $theme->data->button ?>"><?php ee('Link') ?></a>
                                </div>
                            </div>
                        <?php endforeach ?>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3" style="height:100px;background: linear-gradient(-45deg, #000851 0%, #1CB5E0 100%);" data-trigger="changetheme" onclick="changeTheme('#1CB5E0', '#1CB5E0', '#000851', '#000851', '#ffffff', '#ffffff', 'gradient');">
                                <p class="d-block" style="color:#fff">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#000851;width:100%;height:30px;color:#fff"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3" style="height:100px;background: linear-gradient(-45deg, #FC466B 0%, #3F5EFB 100%);" data-trigger="changetheme" onclick="changeTheme('#FC466B', '#3F5EFB', '#FC466B', '#ffffff', '#FC466B', '#ffffff', 'gradient');">
                                <p class="d-block" style="color:#fff">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#fff;width:100%;height:30px;color:#FC466B"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3" style="height:100px;background: linear-gradient(-45deg, #FDBB2D 0%, #22C1C3 100%);" data-trigger="changetheme" onclick="changeTheme('#FDBB2D', '#22C1C3', '#FDBB2D', '#ffffff', '#FDBB2D', '#ffffff', 'gradient');">
                                <p class="d-block" style="color:#fff">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#fff;width:100%;height:30px;color:#FDBB2D"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3" style="height:100px;background: linear-gradient(-45deg, #00c6ff 0%, #0072ff 100%);" data-trigger="changetheme" onclick="changeTheme('#00c6ff', '#0072ff', '#00c6ff', '#ffffff', '#00c6ff', '#ffffff', 'gradient');">
                                <p class="d-block" style="color:#fff">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#fff;width:100%;height:30px;color:#00c6ff"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3" style="height:100px;background: linear-gradient(-45deg, #d53369 0%, #daae51 100%);" data-trigger="changetheme" onclick="changeTheme('#d53369', '#daae51', '#d53369', '#ffffff', '#d53369', '#ffffff', 'gradient');">
                                <p class="d-block" style="color:#fff">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#fff;width:100%;height:30px;color:#d53369"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3" style="height:100px;background: linear-gradient(-45deg, #ED4264 0%, #FFEDBC 100%);" data-trigger="changetheme" onclick="changeTheme('#ED4264', '#FFEDBC', '#ED4264', '#ffffff', '#ED4264', '#ffffff', 'gradient');">
                                <p class="d-block" style="color:#fff">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#fff;width:100%;height:30px;color:#ED4264"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3" style="height:100px;background: linear-gradient(-45deg, #232526 0%, #414345 100%);" data-trigger="changetheme" onclick="changeTheme('#232526', '#414345', '#232526', '#ffffff', '#232526', '#ffffff', 'gradient');">
                                <p class="d-block" style="color:#fff">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#fff;width:100%;height:30px;color:#232526"><?php ee('Link') ?></a>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-xl-3 mb-2">
                            <div role="button" class="d-block text-center border rounded p-3" style="height:100px;background:#1e2028" data-trigger="changetheme" onclick="changeTheme('#1e2028', '#1e2028', '#1e2028', '#252731', '#ffffff', '#ffffff');">
                                <p class="d-block" style="color:#ffffff">Hello</p>
                                <a href="#" class="rounded-pill d-block pt-1 text-decoration-none" style="background:#252731;width:100%;height:30px;color:#fff"><?php ee('Link') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <h4 class="fw-bold mb-3 mt-5"><?php ee('Fonts') ?></h4>
                <div class="card card-body shadow-sm">
                    <div class="row" data-toggle="buttons">
                        <?php foreach(['Arial', 'Helvetica_Neue', 'Courier_New', 'Times_New_Roman', 'Comic_Sans_MS', 'Verdana', 'Impact', 'Tahoma'] as $font): ?>
                            <div class="col-4 col-xl-2 mb-2" data-bs-toggle="tooltip" title="<?php echo str_replace(['_', '+'], ' ', $font) ?>">
                                <label data-trigger="choosefont" class="<?php echo strtolower("font-{$font}") ?> btn text-center border bg-white rounded p-2 h-100 me-1 fs-1 w-100 <?php echo isset($bio->data->style->font) && $bio->data->style->font == str_replace('_', '+', $font) ? 'border-secondary': '' ?>">
                                    <span class="d-block"><strong>A</strong>B<i>C</i></span>
                                    <input type="radio" name="fonts" value="<?php echo str_replace('_', '+', $font) ?>" class="d-none" <?php echo isset($bio->data->style->font) && $bio->data->style->font == str_replace('_', '+', $font) ? 'checked': '' ?>>
                                </label>
                            </div>
                        <?php endforeach ?>
                        <?php foreach(\Helpers\App::fonts() as $font): ?>
                            <div class="col-4 col-xl-2 mb-2" data-bs-toggle="tooltip" title="<?php echo str_replace(['_', '+'], ' ', $font) ?>">
                                <label data-trigger="choosefont" class="<?php echo strtolower("font-{$font}") ?> btn text-center border bg-white rounded p-2 h-100 me-1 fs-1 w-100 <?php echo isset($bio->data->style->font) && $bio->data->style->font == str_replace('_', '+', $font) ? 'border-secondary': '' ?>">
                                    <span class="d-block"><strong>A</strong>B<i>C</i></span>
                                    <input type="radio" name="fonts" value="<?php echo str_replace('_', '+', $font) ?>" class="d-none" <?php echo isset($bio->data->style->font) && $bio->data->style->font == str_replace('_', '+', $font) ? 'checked': '' ?>>
                                </label>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <h5 class="mt-4"><?php ee('Text Color') ?></h5>
                    <div class="form-group mb-4">
                        <input type="text" name="textcolor" id="textcolor" value="<?php echo $bio->data->style->textcolor ?? '' ?>" data-default="<?php echo $bio->data->style->textcolor ?? '' ?>" data-trigger="color">
                    </div>
                </div>
                <h4 class="fw-bold mb-3 mt-5"><?php ee('Custom Background') ?></h4>
                <div class="card card-body shadow-sm" id="background">
                    <h5><?php ee('Background') ?></h5>
                    <div class="mb-3 mt-3">
                        <div data-toggle="buttons">
                            <a href="#singlecolor" id="forsinglecolor" class="btn btn text-center border bg-white rounded p-2 h-100 me-1 <?php echo isset($bio->data->style->mode) && $bio->data->style->mode == 'singlecolor' ? 'border-secondary' : '' ?>" data-trigger="bgtype"><?php ee('Single Color') ?></a>
                            <a href="#gradient" id="forgradient" class="btn btn text-center border bg-white rounded p-2 h-100 me-1 <?php echo isset($bio->data->style->mode) && $bio->data->style->mode == 'gradient' ? 'border-secondary' : '' ?>" data-trigger="bgtype"><?php ee('Gradient Color') ?></a>
                            <a href="#image" id="forimage" class="btn btn text-center border bg-white rounded p-2 h-100 me-1 <?php echo isset($bio->data->style->mode) && $bio->data->style->mode == 'image' ? 'border-secondary' : '' ?>" data-trigger="bgtype"><?php ee('Image') ?></a>
                        </div>
                    </div>
                    <input type="hidden" name="mode" value="<?php echo $bio->data->style->mode ?? 'singlecolor' ?>">
                    <div id="singlecolor" class="collapse bgtype <?php echo isset($bio->data->style->mode) && $bio->data->style->mode == 'singlecolor' ? 'show' : '' ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="bg"><?php ee("Background") ?></label><br>
                                    <input type="text" name="bg" id="bg" value="<?php echo $bio->data->style->bg ?>" data-default="<?php echo $bio->data->style->bg ?? '' ?>" data-trigger="color">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="gradient" class="collapse bgtype <?php echo isset($bio->data->style->mode) && $bio->data->style->mode == 'gradient' ? 'show' : '' ?>">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="bgst"><?php ee("Gradient Start") ?></label><br>
                                    <input type="text" name="gradient[start]" id="bgst" value="<?php echo $bio->data->style->gradient->start ?? '' ?>" data-trigger="color">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="bgsp"><?php ee("Gradient Stop") ?></label><br>
                                    <input type="text" name="gradient[stop]" id="bgsp" value="<?php echo $bio->data->style->gradient->stop ?? '' ?>" data-trigger="color">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold" for="gradientangle"><?php ee("Gradient Angle") ?></label><br>
                            <input type="range" id="gradientangle" name="gradient[angle]" min="0" max="360" value="<?php echo $bio->data->style->gradient->angle ?? '135' ?>" class="form-range">
                        </div>
                    </div>
                    <div id="image" class="collapse bgtype <?php echo isset($bio->data->style->mode) && $bio->data->style->mode == 'image' ? 'show' : '' ?>">
                        <input type="file" class="form-control mb-4" name="bgimage" id="bgimage" data-error="<?php ee('Please choose a valid background image. JPG, PNG') ?>">
                    </div>
                </div>
                <h4 class="fw-bold mb-3 mt-5"><?php ee('Buttons') ?></h4>
                <div class="card card-body shadow-sm">
                    <h5><?php ee('Button Color') ?></h5>
                    <div class="form-group mb-4">
                        <input type="text" name="buttoncolor" id="buttoncolor" value="<?php echo $bio->data->style->buttoncolor ?? '' ?>">
                    </div>
                    <h5><?php ee('Button Text Color') ?></h5>
                    <div class="form-group mb-4">
                        <input type="text" name="buttontextcolor" id="buttontextcolor" value="<?php echo $bio->data->style->buttontextcolor ?? '' ?>">
                    </div>
                    <h5><?php ee('Button Style') ?></h5>
                    <div class="form-group mb-4">
                        <select name="buttonstyle" id="buttonstyle" class="form-select p-2">
                            <option value="none"<?php echo isset($bio->data->style->buttonstyle) && $bio->data->style->buttonstyle == 'none' ? ' selected' : '' ?>><?php ee('None') ?></option>
                            <option value="rectangular"<?php echo isset($bio->data->style->buttonstyle) && $bio->data->style->buttonstyle == 'rectangular' ? ' selected' : '' ?>><?php ee('Rectangular') ?></option>
                            <option value="rounded"<?php echo isset($bio->data->style->buttonstyle) && $bio->data->style->buttonstyle == 'rounded' ? ' selected' : '' ?>><?php ee('Rounded') ?></option>
                            <option value="trec"<?php echo isset($bio->data->style->buttonstyle) && $bio->data->style->buttonstyle == 'trec' ? ' selected' : '' ?>><?php ee('Transparent') ?> <?php ee('Rectangular') ?></option>
                            <option value="tro"<?php echo isset($bio->data->style->buttonstyle) && $bio->data->style->buttonstyle == 'tro' ? ' selected' : '' ?>><?php ee('Transparent') ?> <?php ee('Rounded') ?></option>
                        </select>
                    </div>
                    <h5><?php ee('Button Shadow') ?></h5>
                    <div class="form-group mb-4">
                        <select name="shadow" id="shadow" class="form-select p-2">
                            <option value="none"<?php echo isset($bio->data->style->shadow) && $bio->data->style->shadow == 'none' ? ' selected' : '' ?>><?php ee('None') ?></option>
                            <option value="soft"<?php echo isset($bio->data->style->shadow) && $bio->data->style->shadow == 'soft' ? ' selected' : '' ?>><?php ee('Soft') ?></option>
                            <option value="hard"<?php echo isset($bio->data->style->shadow) && $bio->data->style->shadow == 'hard' ? ' selected' : '' ?>><?php ee('Hard') ?></option>
                        </select>
                    </div>
                    <h5><?php ee('Shadow Color') ?></h5>
                    <div class="form-group mb-4">
                        <input type="text" name="shadowcolor" id="shadowcolor" data-trigger="color" value="<?php echo $bio->data->style->shadowcolor ?? '' ?>" data-default="<?php echo $bio->data->style->shadowcolor ?? '' ?>">
                    </div>
				</div>
			</div>
            <div class="collapse switcher" id="advanced">
                <h4 class="fw-bold mb-3"><?php ee('SEO') ?></h4>
                <div class="card card-body shadow-sm">
                    <div class="form-group mb-3">
                        <label for="title" class="form-label"><?php ee('Meta Title') ?></label>
                        <input type="text" class="form-control p-2" name="title" id="title" autocomplete="off" value="<?php echo $url->meta_title ?>">
                    </div>
                    <div class="form-group mb-3">
                        <label for="description" class="form-label"><?php ee('Meta Description') ?></label>
                        <textarea class="form-control" name="description" id="description" autocomplete="off"><?php echo $url->meta_description ?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description" class="form-label"><?php ee('Meta Image') ?></label>
                        <input type="file" class="form-control" name="metaimage" id="metaimage" autocomplete="off">
                    </div>
                </div>
                <h4 class="fw-bold mb-3 mt-5"><?php ee('Settings') ?></h4>
                <div class="card card-body shadow-sm">
                    <div class="form-group">
                        <div class="form-group">
                            <div class="d-flex">
                                <div>
                                    <label class="form-check-label fw-bold" for="avatarenabled"><?php ee('Display Avatar') ?></label>
                                    <p class="form-text"><?php ee('Display or hide your avatar from your Bio page') ?></p>
                                </div>
                                <div class="form-check form-switch ms-auto">
                                    <input class="form-check-input" type="checkbox" data-binary="true" id="avatarenabled" name="avatarenabled" value="1" data-toggle="togglefield" data-toggle-for="avatarstyle" <?php echo $bio->data->avatarenabled ? 'checked' : ''?>>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3 <?php echo !$bio->data->avatarenabled ? 'd-none' : ''?>">
                            <label class="form-label fw-bold" for="avatarstyle"><?php ee('Avatar Style') ?></label>
                            <select name="avatarstyle" class="form-select rounded" id="avatarstyle">
                                <option value="rounded"<?php echo !isset($bio->data->avatarstyle) || $bio->data->avatarstyle == 'rounded' ? ' selected' : '' ?>><?php ee('Rounded') ?></option>
                                <option value="rectangular" <?php echo isset($bio->data->avatarstyle) && $bio->data->avatarstyle == 'rectangular' ? ' selected' : '' ?>><?php ee('Rectangular') ?></option>
                            </select>
                        </div>
                        <div class="d-flex">
                            <div>
                                <label class="form-check-label fw-bold" for="sensitive"><?php ee('Sensitive Content') ?></label>
                                <p class="form-text"><?php ee('Sensitive content warns users before showing them the Bio Page') ?></p>
                            </div>
                            <div class="form-check form-switch ms-auto">
                                <input class="form-check-input" type="checkbox" data-binary="true" id="sensitive" name="sensitive" value="1" <?php echo isset($bio->data->settings->sensitive) && $bio->data->settings->sensitive ? 'checked' : '' ?>>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="d-flex">
                            <div>
                                <label class="form-check-label fw-bold" for="cookie"><?php ee('Cookie Popup') ?></label>
                                <p class="form-text"><?php ee('Cookie popup allows users to review cookie collection terms') ?></p>
                            </div>
                            <div class="form-check form-switch ms-auto">
                                <input class="form-check-input" type="checkbox" data-binary="true" id="cookie" name="cookie" value="1" <?php echo isset($bio->data->settings->cookie) && $bio->data->settings->cookie ? 'checked' : '' ?>>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="d-flex">
                            <div>
                                <label class="form-check-label fw-bold" for="share"><?php ee('Share Icon') ?></label>
                                <p class="form-text"><?php ee('Share icon allows users to quickly share the Bio Page') ?></p>
                            </div>
                            <div class="form-check form-switch ms-auto">
                                <input class="form-check-input" type="checkbox" data-binary="true" id="share" name="share" value="1" <?php echo isset($bio->data->settings->share) && $bio->data->settings->share ? 'checked' : '' ?>>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="d-flex <?php echo !user()->has('poweredby') ? 'text-muted' : '' ?>" <?php echo !user()->has('poweredby') ? 'data-bs-toggle="tooltip" title="'.e('Please choose a premium package to unlock this feature').'"' : '' ?>>
                            <div>
                                <label class="form-check-label fw-bold" for="branding"><?php ee('Remove Branding') ?></label>
                                <p class="form-text"><?php ee('Remove our branding from your Bio Page.') ?></p>
                            </div>
                            <div class="form-check form-switch ms-auto">
                                <input class="form-check-input" type="checkbox" data-binary="true" id="branding" name="branding" value="1" <?php echo isset($bio->data->settings->branding) && $bio->data->settings->branding ? 'checked' : '' ?> <?php echo !user()->has('poweredby') ? 'disabled' : '' ?>>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pass" class="form-label fw-bold"><?php ee('Password Protection') ?></label>
                        <p class="form-text"><?php ee('By adding a password, you can restrict the access') ?></p>
                        <div class="input-group">
                            <div class="input-group-text bg-white"><i data-feather="lock"></i></div>
                            <input type="text" class="form-control border-start-0 ps-0 p-2" name="pass" id="pass" value="<?php echo $url->pass ?>" placeholder="<?php echo e("Type your password here")?>" autocomplete="off">
                        </div>
                    </div>
                    <?php if(\Core\Auth::user()->has("pixels") !== false):?>
                    <div id="pixels" class="mt-4">
                        <label class="form-label fw-bold"><?php echo e("Targeting Pixels")?></label>
                        <p class="form-text"><?php echo e('Add your targeting pixels below from the list. Please make sure to enable them in the pixels settings.')?></p>
                        <div class="input-group input-select rounded">
                            <span class="input-group-text bg-white"><i data-feather="filter"></i></span>
                            <select name="pixels[]" data-placeholder="Your Pixels" multiple data-toggle="select">
                                <?php foreach(\Core\Auth::user()->pixels() as $type => $pixels): ?>
                                    <optgroup label="<?php echo ucwords($type) ?>">
                                    <?php foreach($pixels as $pixel): ?>
                                        <option value="<?php echo $pixel->type ?>-<?php echo $pixel->id ?>" <?php echo in_array($pixel->type.'-'.$pixel->id, explode(',', $url->pixels ?? '')) ? 'selected': '' ?>><?php echo $pixel->name ?></option>
                                    <?php endforeach ?>
                                    </optgroup>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <?php endif ?>
                </div>
                <h4 class="fw-bold mb-3 mt-5"><?php ee('Custom CSS') ?></h4>
                <div class="card card-body shadow-sm">
                    <?php if(user()->has('biocss')): ?>
                        <div class="form-group">
                            <textarea class="form-control" name="customcss" id="customcss" rows="5" placeholder="e.g. .btn { display: block }"><?php echo $bio->data->style->custom ?></textarea>
                        </div>                        
                    <?php else: ?>
                        <div class="form-group">
                            <textarea class="form-control" name="customcss" id="customcss" rows="5" placeholder="e.g. .btn { display: block }" disabled data-bs-toggle="tooltip" title="<?php ee('Upgrade to unlock this feature') ?>"></textarea>
                        </div>                        
                    <?php endif ?>
                </div>
            </div>
            <div class="collapse switcher" id="data">
                <div class="card card-body shadow-sm">
                    <?php ee('You will be able to download submitted data here once available.') ?>
                </div>
                <?php if(isset($bio->responses->newsletter)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="fw-bold"><?php ee("Newsletter Emails") ?></h5>
                    </div>
                    <div class="card-body pt-0">
                        <p><?php ee('Collected {c} emails in total', null, ['c' => count($bio->responses->newsletter)]) ?></p>
                        <a href="?newsletterdata=1" class="btn btn-success"><?php ee('Download as CSV') ?></a>
                    </div>
                </div>
                <?php endif ?>
            </div>
        </div>
        <div class="col-md-5 justify-content-center">
            <div class="card border border-5 border-rounded border-dark card-preview ms-0 ms-lg-4 w-100">
                <iframe src="<?php echo route('bio.preview', $bio->id) ?>" width="100%" height="100%"></iframe>
            </div>
        </div>
    </div>
</form>
<?php view('bio.widgets') ?>
<div class="modal fade" id="successModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><?php ee('Short Link Info') ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex">
            <div class="modal-qr me-3">
                <p></p>
                <div class="btn-group" role="group" aria-label="downloadQR">
                    <a href="#" class="btn btn-primary" id="downloadPNG"><?php ee('Download') ?></a>
                    <div class="btn-group" role="group">
                        <button id="btndownloadqr" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">PNG</button>
                        <ul class="dropdown-menu" aria-labelledby="btndownloadqr">
                            <li><a class="dropdown-item" href="#">PDF</a></li>
                            <li><a class="dropdown-item" href="#">SVG</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <div class="form-group">
                    <label for="short" class="form-label"><?php ee('Short Link') ?></label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="modal-input" name="shortlink" value="">
                        <div class="input-group-text bg-white">
                            <button class="btn btn-primary copy" data-clipboard-text=""><?php ee('Copy') ?></button>
                        </div>
                    </div>
                </div>
                <div class="mt-3" id="modal-share">
                    <?php echo \Helpers\App::share('--url--') ?>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal"><?php ee('Done') ?></button>
      </div>
    </div>
  </div>
</div>