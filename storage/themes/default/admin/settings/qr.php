<h1 class="h3 mb-5"><?php ee('QR Settings') ?></h1>
<div class="row">
    <div class="col-md-3 d-none d-lg-block">
        <?php view('admin.partials.settings_menu') ?>
    </div>
    <div class="col-md-12 col-lg-9">
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="<?php echo route('admin.settings.save') ?>" enctype="multipart/form-data">
                    <?php echo csrf() ?>                                        
                    <div class="form-group">
                        <label for="" class="form-label fw-bold"><?php ee('Public QR Generator') ?></label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" data-binary="true" id="publicqr" name="publicqr" value="1" <?php echo config("publicqr") ? 'checked':'' ?>>
                            <label class="form-check-label" for="publicqr"><?php ee('Enable') ?></label>
                        </div>
                        <p class="form-text"><?php ee('If enabled, users will be able to generate static QR codes on the QR Code feature page.') ?></p>
                    </div>
                    <div class="form-group mb-4">
                        <label for="qrlogo" class="form-label fw-bold"><?php ee('QR Code Logo') ?></label>
                        <?php if(!empty(config("qrlogo"))):  ?>
                            <p><img src="<?php echo \Helpers\QR::factory('Sample QR', 400, 0)->withLogo(PUB.'/content/'.config('qrlogo'), 100)->format('svg')->create('uri') ?>" height="350" alt="" class="rounded"></p>
                        <?php endif ?>					    	
                        <input type="file" name="qrlogo_path" id="qrlogo" class="form-control mb-2">
                        <?php if(!empty(config("qrlogo"))):  ?>
                            <p class="form-text"><a href="#" id="remove_qrlogo" data-trigger="removeimage" class="btn btn-danger btn-sm"><?php ee('Remove Logo') ?></a></p>
                        <?php endif ?>
                        <p class="form-text"><?php ee('Set a default QR code logo for free users and anonymous users. Logo must be square with a recommended size of 500x500 and PNG format.') ?></p>
                    </div> 
                    <button type="submit" class="btn btn-success"><?php ee('Save Settings') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>