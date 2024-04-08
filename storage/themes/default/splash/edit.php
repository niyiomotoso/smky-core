<div class="d-flex mb-5">
    <h1 class="h3"><?php ee('Update a Custom Splash') ?></h1>
    <div class="ms-auto">
        <a href="#previewModal" data-bs-toggle="modal" class="btn btn-success"><?php ee('Preview') ?></a>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
		<form method="post" action="<?php echo route("splash.update", [$splash->id]) ?>" enctype="multipart/form-data" autocomplete="off">		
			<div class="card shadow-sm">
				<div class="card-body">
                    <?php echo csrf() ?>
                    <div class="row">
						<div class="col-md-6">
							<div class="form-group mb-3">
								<label class="form-label fw-bold" for="name"><?php ee("Name") ?></label>
								<input type="text" class="form-control p-2" name="name" id="name"  placeholder="e.g. Promo" value="<?php echo $splash->name ?>" data-required="true">
							</div>	
						</div>
                        <div class="col-md-6">
							<div class="form-group mb-3">
								<label class="form-label fw-bold" for="counter"><?php ee("Counter") ?></label>
								<input type="text" class="form-control p-2" name="counter" id="counter"  placeholder="e.g. 15" value="<?php echo $splash->data->counter ?? '' ?>">
							</div>	
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="product"><?php ee('Link to Product') ?></label>
                                <input type="text" class="form-control p-2" name="product" id="product" value="<?php echo $splash->data->product ?>" placeholder="e.g. http://domain.com/">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="title"><?php ee('Custom Title') ?></label>
                                <input type="text" class="form-control p-2" name="title" id="title" value="<?php echo $splash->data->title ?>" placeholder="e.g. <?php ee("Get a $10 discount") ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="banner"><?php ee('Upload Banner') ?></label>
                                <input type="file" class="form-control" name="banner" id="banner">
                                <div class="form-text"><?php ee("The minimum width must be 980px and the height must be between 250 and 500. The format must be a PNG or a JPG. Maximum size is 500KB") ?></div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                            <label class="form-label fw-bold fw-bold" for="avatar"><?php ee('Upload Avatar') ?></label>
                                <input type="file" class="form-control" name="avatar" id="avatar" >
                                <p class="form-text"><?php ee('The avatar can be in png, jpg or gif with a maximum size of 500kb.') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-group">
                            <label class="form-label fw-bold" for="message"><?php ee('Custom Message') ?></label>
                            <textarea name="message" id="message" cols="30" rows="5" class="form-control p-2" placeholder="e.g. <?php ee("Get a $10 discount with any purchase more than $50") ?>"><?php echo $splash->data->message ?></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php ee("Update") ?></button>
				</div>
			</div>
		</form>
    </div>    
</div>
<div class="modal fade" id="previewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">        
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><?php ee('Preview') ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>        
        <div class="mb-3 p-2">
            <img src="<?php echo uploads($splash->data->banner) ?>" class="rounded w-100">
            <div class="d-flex align-items-center">
                <div>
                    <img src="<?php echo uploads($splash->data->avatar) ?>" height="100" class="rounded">
                </div>
                <div class="ms-3 py-2">
                    <strong><?php echo $splash->data->title ?></strong>
                    <p class="mt-2"><?php echo $splash->data->message ?></p>
                    <p><a href="<?php echo $splash->data->product ?>" rel="nofollow" target="_blank" class='btn btn-primary btn-sm'><?php ee('View site') ?></a></p>
                </div>
            </div> 
        </div> 
    </div>
  </div>
</div>