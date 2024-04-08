<h1 class="h3 mb-5"><?php ee('Advanced Settings') ?></h1>
<div class="row">
    <div class="col-md-3 d-none d-lg-block">
        <?php view('admin.partials.settings_menu') ?>
    </div>
    <div class="col-md-12 col-lg-9">
        <form method="post" action="<?php echo route('admin.settings.save') ?>" enctype="multipart/form-data">
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php echo csrf() ?>
                    <div class="form-group">
                        <label for="root_domain" class="form-label fw-bold"><?php ee('Shorten links with') ?> <strong><?php echo str_replace(["http://", "https://"], "", config("url")) ?></strong></label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" data-binary="true" id="root_domain" name="root_domain" value="1" <?php echo config("root_domain") ? 'checked':'' ?>>
                            <label class="form-check-label" for="root_domain"><?php ee('Enable') ?></label>
                        </div>
                        <p class="form-text"><?php ee('If you have additional domains and you want to prevent people from using the root domain to shorten, disable this.') ?></p>
                    </div>
                    <div class="form-group">
                        <label for="multiple_domains" class="form-label fw-bold"><?php ee('Multiple Domain Names') ?></label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" data-binary="true" id="multiple_domains" name="multiple_domains" value="1" <?php echo config("multiple_domains") ? 'checked':'' ?> data-toggle="togglefield" data-toggle-for="domain_names">
                            <label class="form-check-label" for="multiple_domains"><?php ee('Enable') ?></label>
                        </div>
                        <p class="form-text"><?php ee('If enabled users will have the choice to select their preferred domain name from the list below. Make sure that all these point to the script.') ?></p>
                    </div>
                    <div class="form-group <?php echo config("multiple_domains") ? '':'d-none' ?>">
                        <label for="domain_names" class="form-label fw-bold"><?php ee('Domains') ?></label>
                        <textarea name="domain_names" id="domain_names" rows="5" class="form-control p-2"><?php echo config("domain_names") ?></textarea>
                        <p class="form-text"><?php ee('One domain per line including http://, do not include your main domain name (read documentation).') ?></p>
                    </div>
                    <div class="form-group mb-4">
					    <label for="serverip" class="form-label fw-bold"><?php ee('Server IP') ?></label>
					    <input class="form-control p-2" name="serverip" id="serverip" value="<?php echo config('serverip') ?>">
					    <p class="form-text"><?php ee('Add your server IP here to enable A records. Otherwise your customers can only use CNAME.') ?></p>
                    </div>
                    <div class="form-group mb-4">
					    <label for="analytic" class="form-label fw-bold"><?php ee('Google Analytics Account ID') ?></label>
					    <input class="form-control p-2" name="analytic" id="analytic" value="<?php echo config('analytic') ?>">
					    <p class="form-text"><?php ee('Your Google Analytics account id e.g. G-12345678-1. This will be used to collect data separately for your information only.') ?></p>
                    </div>
                    <hr>
                    <div class="form-group mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" data-binary="true" id="deepl[enabled]" name="deepl[enabled]" value="1" <?php echo config("deepl")->enabled ? 'checked':'' ?>>
                            <label class="form-check-label" for="deepl[enabled]"><?php ee('Enable') ?></label>
                        </div>
                        <p class="form-text"><?php ee('Enable DeepL API') ?></p>
                    </div>
                    <div class="form-group mb-4">
					    <label for="deepl[key]" class="form-label fw-bold"><?php ee('DeepL API Key') ?></label>
					    <input class="form-control p-2" name="deepl[key]" id="deepl[key]" value="<?php echo config('deepl')->key ?>">
					    <p class="form-text"><?php ee('Enter your DeepL API key so you can translate languages files automatically.') ?> <a href="https://www.deepl.com/" target="blank">DeepL</a></p>
                    </div>
                    <button type="submit" class="btn btn-success"><?php ee('Save Settings') ?></button>
                </div>
            </div>           
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex mb-5">
                        <h5 class="fw-bold"><?php ee('CDN Integration') ?></h5>
                        <?php if(config('cdn')->key && config('cdn')->secret && config('cdn')->region): ?>
                            <div class="ms-auto">
                                <a href="<?php echo route('admin.settings.cdnsync', [\Core\Helper::nonce('cdnsync')]) ?>" class="btn btn-primary"><span data-bs-toggle="modal" data-bs-target="#syncing"><?php ee('Sync') ?></span></a>
                            </div>
                        <?php endif ?>
                    </div>                    
                    <p>
                        <?php ee('You can setup a CDN such as S3 and serve all files via CDN. You will also be able to sync current files to CDN. Please note that at the moment only files uploaded by users (located in public/content folder) will be uploaded/synced to the CDN.') ?>
                    </p>
                    <div class="form-group my-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" data-binary="true" id="cdn[enabled]" name="cdn[enabled]" value="1" <?php echo config("cdn")->enabled ? 'checked':'' ?>>
                            <label class="form-check-label" for="cdn[enabled]"><?php ee('Enable') ?></label>
                        </div>
                        <p class="form-text"><?php ee('Enable CDN Uploads') ?></p>
                    </div>                    
                    <div class="form-group mb-4">
                        <label class="form-label d-block"><?php ee('Provider') ?></label>
                        <select name="cdn[provider]" class="form-select p-2">
                            <?php foreach(\Helpers\CDN::providers() as $name => $provider): ?>
                                <option value="<?php echo $name ?>" <?php echo config('cdn')->provider == $name ? 'selected' : ''  ?>><?php echo $provider['name'] ?></option>
                            <?php endforeach ?>
                        </select>                        
                    </div>
                    <div class="form-group mb-4">
					    <label for="cdn[key]" class="form-label fw-bold"><?php ee('Key ID') ?>*</label>
					    <input class="form-control p-2" name="cdn[key]" id="cdn[key]" value="<?php echo config('cdn')->key ?>">
                    </div>
                    <div class="form-group mb-4">
					    <label for="cdn[secret]" class="form-label fw-bold"><?php ee('Secret Key') ?>*</label>
					    <input class="form-control p-2" name="cdn[secret]" id="cdn[secret]" value="<?php echo config('cdn')->secret ?>">
                    </div>
                    <div class="form-group mb-4">
					    <label for="cdn[region]" class="form-label fw-bold"><?php ee('Region') ?>*</label>
					    <input class="form-control p-2" name="cdn[region]" id="cdn[region]" value="<?php echo config('cdn')->region ?>" placeholder="e.g. us-east-1">
                    </div>
                    <div class="form-group mb-4">
					    <label for="cdn[bucket]" class="form-label fw-bold"><?php ee('Bucket Name') ?>*</label>
					    <input class="form-control p-2" name="cdn[bucket]" id="cdn[bucket]" value="<?php echo config('cdn')->bucket ?>" placeholder="e.g. urlshortener">
                    </div>
                    <div class="form-group mb-4">
					    <label for="cdn[url]" class="form-label fw-bold"><?php ee('CDN URL/Endpoint') ?></label>
					    <input class="form-control p-2" name="cdn[url]" id="cdn[url]" value="<?php echo config('cdn')->url ?>" placeholder="e.g. https://12abc.cloudfront.net">
                        <p class="form-text"><?php ee('In most cases you will not be able to access CDN files directly. You will need to either make the bucket public or setup a CDN such as Cloudfront to access files.') ?></p>
                    </div>
                    <button type="submit" class="btn btn-success"><?php ee('Save Settings') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="syncing" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="syncing" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span class="preloader me-2"><span class="spinner-border spinner-border-sm" role="status"></span></span> <?php ee('Syncing files...') ?> </h5>
      </div>
      <div class="modal-body">
        <p><?php ee("Syncing files with selected CDN. Please hold. Don't close this page or press update again. The page will refresh once it is done.") ?></p>
      </div>
    </div>
  </div>
</div>