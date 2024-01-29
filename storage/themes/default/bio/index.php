<div class="d-flex mb-5">
	<div>
		<h1 class="h3 mb-0 fw-bolder"><?php ee('Bio Pages') ?></h1>
	</div>
	<div class="ms-auto">
		<?php if(user()->teamPermission('bio.create')): ?>
			<a role="button" data-bs-toggle="modal" data-bs-target="#createModal" class="btn btn-primary"><?php ee('Create Bio') ?></a>
		<?php endif ?>
	</div>
</div>
<div class="card p-2 shadow-sm">
	<div class="d-flex align-items-center">
		<div>
			<span class="h3 ms-2"><?php echo $count ?></span> <span class="text-muted"> <?php ee('Bio Pages') ?> / <?php echo $total == 0 ? e('Unlimited') : $total ?></span>			
		</div>
		<div class="border-start ms-3 ps-3">
			<span class="h3"><?php echo $views ?></span> <span class="text-muted"><?php ee('views') ?></span>			
		</div>
		<div class="ms-auto">
			<form action="<?php echo route('bio') ?>" method="get" class="d-flex align-items-center border rounded-3 p-1">
                <div class="me-2">
                    <input type="text" class="form-control border-0 p-2" name="q" value="<?php echo clean(request()->q) ?>" placeholder="<?php ee('Search for {t}', null, ['t' => e('Bio Pages')]) ?>">
                </div>
                <div class="me-2">
                    <div class="input-select d-block">
                        <select name="sort" id="sortable" data-name="sort" class="form-select border-0 border-start p-2 pe-5 rounded-0">
                            <optgroup label="Sort by">
                                <option value=""<?php if(!request()->sort) echo " selected" ?>><?php ee('Newest') ?></option>
                                <option value="old"<?php if(request()->sort == 'old') echo " selected" ?>><?php ee('Oldest') ?></option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn bg-white border rounded py-2 px-3"><i data-feather="search"></i></button>
                </div>
            </form>
		</div>
	</div>
</div>
<?php if($bios): ?>
	<div class="row">
		<?php foreach($bios as $bio): ?>
			<div class="col-md-6 mb-3">
				<div class="card p-3 h-100 shadow-sm">
					<div class="d-block d-md-flex align-items-center">						
						<div class="flex-grow-1">
							<div class="float-end">
								<button type="button" class="btn btn-default bg-white" data-bs-toggle="dropdown" aria-expanded="false"><i data-feather="more-horizontal"></i></button>
								<ul class="dropdown-menu">
									<?php if(user()->teamPermission('bio.edit')): ?>
										<li><a class="dropdown-item" href="<?php echo route('bio.edit', [$bio->id]) ?>" class="align-middle"><i data-feather="edit"></i> <?php ee('Edit') ?></a></li>
									<?php endif ?>
									<li><a class="dropdown-item" href="<?php echo route('stats', [$bio->urlid]) ?>"><i data-feather="bar-chart-2"></i> <?php ee('Statistics') ?></span></a></li>
									<li><a class="dropdown-item" href="<?php echo $bio->url ?>"><i data-feather="eye"></i> <?php ee('View Bio') ?></a></li>
									<?php if(user()->defaultbio != $bio->id): ?>
									<li><a class="dropdown-item" href="<?php echo route('bio.default', [$bio->id]) ?>"><i data-feather="check-circle"></i> <?php ee('Set as Default') ?></a></li>
									<?php endif ?>
									<?php if(user()->has('qr')): ?>
										<li><a class="dropdown-item" href="<?php echo route('qr.create', ['url' => $bio->url]) ?>"><i data-feather="aperture"></i> <?php ee('Custom QR Code') ?></a></li>
									<?php endif ?>
									<?php if(user()->teamPermission('bio.edit')): ?>
										<li><a class="dropdown-item" href="#" data-id="<?php echo $bio->id ?>" data-bs-toggle="modal" data-trigger="modalopen" data-bs-target="#channelModal" data-toggle="addtochannel"><i data-feather="package"></i> <?php ee('Add to Channel') ?></a></li>
										<li><a class="dropdown-item" href="<?php echo route('links.reset', [$bio->urlid, \Core\Helper::nonce('link.reset')]) ?>" data-bs-toggle="modal" data-trigger="modalopen" data-bs-target="#resetModal"><i data-feather="rotate-ccw"></i> <?php ee('Reset Stats') ?></a></li>
										<li><a class="dropdown-item" href="<?php echo route('bio.duplicate', [$bio->id]) ?>"><i data-feather="copy"></i> <?php ee('Duplicate') ?></a></li>
									<?php endif ?>
									<?php if(user()->teamPermission('bio.delete')): ?>
									<li class="dropdown-divider"></li>
									<li><a class="dropdown-item text-danger" data-bs-toggle="modal" data-trigger="modalopen" data-bs-target="#deleteModal" href="<?php echo route('bio.delete', [$bio->id, \Core\Helper::nonce('bio.delete')]) ?>"><i data-feather="trash"></i> <?php ee('Delete') ?></a></li>
									<?php endif ?>
								</ul>
							</div>
							<div class="d-flex align-items-center">
								<div class="me-3 rounded-circle border p-2">
									<img src="<?php echo $bio->avatar ?>" class="icon-45 rounded-circle" alt="">
								</div>
								<div>
									<a href="<?php echo $bio->url ?>" target="_blank" class="mt-1"><strong><?php echo $bio->name ?: 'n\a' ?></strong></a>
									<div class="mt-1">
										<small class="text-muted fs-6" data-href="<?php echo $bio->url ?>"><?php echo $bio->url ?></small>
										<a href="#copy" class="copy inline-copy" data-clipboard-text="<?php echo $bio->url ?>"><small><?php echo e("Copy")?></small></a>
									</div>									
								</div>
							</div>							
							<?php if($channels = $bio->channels): ?>
								<div class="mt-3">
								<?php foreach($channels as $channel): ?>
									<small class="badge text-xs me-2 p-1 px-2 border" style="border-color: <?php echo $channel->color ?>!important;color: <?php echo $channel->color ?>"><?php echo $channel->name ?> <a href="<?php echo route('channel.removefrom', [$channel->id, 'bio', $bio->id]) ?>" class="ms-2 text-danger" data-bs-toggle="modal" data-trigger="modalopen" data-bs-target="#deleteModal"><span data-bs-toggle="tooltip" data-bs-placement="top" title="<?php ee('Remove from channel') ?>">X</a></small>
								<?php endforeach ?>
								</div>
							<?php endif ?>
							<div class="border p-2 rounded mt-4">
								<?php echo (user()->defaultbio == $bio->id ? '<span class="badge bg-primary me-2">'.e('Default').'</span>' : '') ?>
								<?php if($bio->status == '0') : ?>
									<span class="badge bg-danger me-2"><?php ee('Disabled') ?></span>
								<?php endif ?>
								<?php if(isset($bio->views)):?>
									<i data-feather="eye"></i> <span class="align-middle ms-1 me-2"><?php echo $bio->views .' '.e('Views') ?></span>
								<?php endif ?>
								<i data-feather="clock"></i> <span class="align-middle ms-1"><?php echo \Core\Helper::timeago($bio->created_at) ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach ?>
	</div>
	<?php echo pagination('bg-white shadow rounded pagination p-3') ?>
<?php else: ?>
	<div class="card flex-fill shadow-sm">
		<div class="card-body text-center">
			<p><?php ee('No content found. You can create some.') ?></p>
			<?php if(user()->teamPermission('bio.create')): ?>
				<a href="#" data-bs-toggle="modal" data-bs-target="#createModal" class="btn btn-primary"><?php ee('Create Bio') ?></a>
			<?php endif ?>
		</div>
	</div>
<?php endif ?>
<?php if(user()->teamPermission('bio.create')): ?>
<div class="modal fade" id="createModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
	  		<form action="<?php echo route('bio.save') ?>" method="post" data-trigger="server-form">
				<?php echo csrf() ?>
				<div class="modal-header">
					<h5 class="modal-title fw-bold"><?php ee('Create Bio') ?></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="form-group mb-3">
						<label for="name" class="form-label fw-bolder"><?php ee('Name') ?> <i data-bs-toggle="tooltip" title="<?php ee('A unique name will help you identify your bio page') ?>" class="ms-1 text-muted small fa fa-question-circle"></i></label>
						<input type="text" class="form-control p-2" name="name" id="name" placeholder="e.g. Bio Page" data-min="3" data-error="<?php ee('Please enter a valid name (min 3 characters)') ?>" required>
					</div>
					<div class="row">
						<?php if(count($domains) > 1): ?>
						<div class="col-md-6">
							<div class="form-group mb-3">
								<label for="domain" class="form-label fw-bolder"><?php ee('Domain') ?></label>
								<select name="domain" id="domain" class="form-select p-2">
								<?php foreach($domains as $domain): ?>
									<option value="<?php echo $domain ?>"><?php echo $domain ?></option>
								<?php endforeach ?>
								</select>
								<div class="form-text">
									<?php ee('Choose domain to generate the link with') ?>
								</div>
							</div>
						</div>
						<?php endif ?>
						<?php if(user()->has('alias')): ?>
						<div class="col-md-6">
							<div class="form-group mb-3">
								<label for="custom" class="form-label fw-bolder"><?php ee('Alias') ?></label>
								<input type="text" class="form-control p-2" name="custom" id="custom" placeholder="e.g. mybio">
								<p class="form-text"><?php ee('Leave this field empty to generate a random alias') ?></p>								
							</div>
						</div>
						<?php endif ?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php ee('Cancel') ?></button>
					<button type="submit" class="btn btn-success"><?php ee('Create') ?></a>
				</div>        
			</form>
		</div>
	</div>
</div>
<?php endif ?>
<?php if(user()->teamPermission('bio.delete')): ?>
<div class="modal fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title"><?php ee('Are you sure you want to delete this?') ?></h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	  </div>
	  <div class="modal-body">
		<p><?php ee('You are trying to delete a record. This action is permanent and cannot be reversed.') ?></p>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php ee('Cancel') ?></button>
		<a href="#" class="btn btn-danger" data-trigger="confirm"><?php ee('Confirm') ?></a>
	  </div>
	</div>
  </div>
</div>
<div class="modal fade" id="resetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title"><?php ee('Are you sure you want to reset this?') ?></h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	  </div>
	  <div class="modal-body">
		<p><?php ee('You are trying to reset all statistic data for this link. This action is permanent and cannot be reversed.') ?></p>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php ee('Cancel') ?></button>
		<a href="#" class="btn btn-danger" data-trigger="confirm"><?php ee('Confirm') ?></a>
	  </div>
	</div>
  </div>
</div>
<?php endif ?>
<div class="modal fade" id="channelModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <form action="<?php echo route('channel.addto', ['bio', null]) ?>" data-trigger="server-form">
		<?php echo csrf() ?>
		<div class="modal-header">
			<h5 class="modal-title"><?php ee('Add to Channels') ?></h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<div class="modal-body">
			<label for="channels" class="form-label d-block mb-2"><?php ee('Channels') ?></label>
			<div class="form-group rounded input-select">
				<select name="channels[]" id="channels" class="form-control" multiple data-toggle="select">
					<?php foreach(\Core\DB::channels()->where('userid', user()->rID())->findArray() as $channel): ?>
						<option value="<?php echo $channel['id'] ?>"><?php echo $channel['name'] ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<input type="hidden" name="channelids" id="channelids" value="">
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php ee('Cancel') ?></button>
			<button type="submit" class="btn btn-success" class="btn btn-success" data-bs-dismiss="modal" data-trigger="addtocampaign"><?php ee('Add') ?></button>
		</div>
	  </form>
	</div>
  </div>
</div>