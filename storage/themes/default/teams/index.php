<div class="d-flex">
    <div>
        <h1 class="h3 mb-5"><?php ee('Manage Teams') ?></h1>
    </div>
    <div class="ms-auto">
        <a href="#" data-bs-toggle="modal" data-bs-target="#inviteModal" class="btn btn-primary"><?php ee('Add Member') ?></a>
    </div>
</div>
<div class="row">
    <div class="col-md-9">
        <?php if($teams): ?>
            <?php foreach($teams as $team): ?>
                <div class="card shadow-sm rounded-lg p-3">
                    <div class="d-block d-md-flex align-items-center">
                        <div class="d-flex flex-fill">
                            <img src="<?php echo $team->user->avatar() ?>" class="avatar rounded-circle">
                            <div class="ms-2">
                                <strong><?php echo $team->user->name ? $team->user->name : $team->user->username ?> <?php echo ($team->status ? '<span class="badge bg-success">'.e("Active").'</span>' : '<span class="badge bg-danger">'.e("Disabled").'</span>') ?></strong><br>
                                <span class="text-muted"><?php echo $team->user->email ?></span>
                            </div>
                        </div>
                        <div class="flex-fill text-start text-md-end">
                            <?php if($team->status == '-1'): ?>
                                <span class="text-warning fw-bold align-middle"><?php ee('Requested') ?></span>
                            <?php else: ?>
                                <?php if($team->status): ?>
                                <a class="me-2 text-dark" href="<?php echo route('team.toggle', [$team->id]) ?>"><i class="text-muted" data-feather="x-circle"></i> <span class="align-middle"><?php ee('Disable') ?></span></a>
                                <?php else: ?>
                                <a class="me-2 text-dark" href="<?php echo route('team.toggle', [$team->id]) ?>"><i class="text-muted" data-feather="check-circle"></i> <span class="align-middle"><?php ee('Enable') ?></span></a>
                                <?php endif ?>
                            <?php endif ?>
                            <button type="button" class="btn btn-default bg-white" data-bs-toggle="dropdown" aria-expanded="false"><i data-feather="more-horizontal"></i></button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo route('team.edit', [$team->id]) ?>"><i data-feather="edit"></i> <?php ee('Edit') ?></span></a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#permissionModal" data-permission="<?php echo htmlentities($team->permission, ENT_QUOTES) ?>"><i data-feather="user"></i> <?php ee('View Permissions') ?></span></a></li>                                
                                <li class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo route('team.delete', [$team->id, \Core\Helper::nonce('team.delete')]) ?>" data-bs-toggle="modal" data-trigger="modalopen" data-bs-target="#deleteModal"><i data-feather="trash"></i> <span class="align-middle ms-1"><?php ee('Remove') ?></span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
            <?php echo pagination('bg-white shadow rounded pagination p-3') ?>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <p><?php ee('No members found. You can invite one.') ?></p>
                    <?php if(!user()->team()): ?>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#inviteModal"  class="btn btn-primary btn-sm"><?php ee('Add Member') ?></a>
                    <?php endif ?>
                </div>
            </div>
        <?php endif ?>
        <?php echo pagination('bg-white shadow rounded pagination p-3') ?>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3"><?php ee('Team Members') ?> <small class="float-end"><?php echo $count ?> / <?php echo $total == 0 ? e('Unlimited') : $total ?></small></h5>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $total == 0 ? 100 : round($count*100/$total) ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex">
                    <h5 class="card-title mb-0"><?php ee('Permission') ?></h5>
                </div>
            </div>
            <div class="card-body">
                <p><?php echo e("Create: A create event will allow your team member to shorten links, create splash pages & overlay and campaigns.") ?></p>
                <p><?php echo e("Edit: An edit event will allow your team member to edit links, splash pages & overlay and campaigns.") ?></p>
                <p><?php echo e("Delete: A delete event will allow your team member to delete links, splash pages & overlay and campaigns.") ?></p>
            </div>
        </div>
        <?php plug('sidebar.team') ?>
    </div>
</div>
<div class="modal fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><?php ee('Are you sure you want to delete this?') ?></h5>
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
<div class="modal fade" id="permissionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><?php ee('Permissions') ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php ee('Cancel') ?></button>
      </div>
    </div>
  </div>
</div>
<?php if(!user()->teamid): ?>
<div class="modal fade" id="inviteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="<?php echo route('team.save') ?>">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fw-bold"><?php ee('Add Member') ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <?php echo csrf() ?>
            <div class="form-group mb-3">
                <label for="email" class="label-control mb-2"><?php echo e("Email") ?></label>
                <input type="email" value="" name="email" class="form-control" placeholder="johndoe@email.tld" />
            </div>
            <div class="form-group input-select">
                <label for="permissions" class="label-control mb-2"><?php echo e("Permissions") ?></label>
                <select name="permissions[]" class="form-control" placeholder="<?php echo e("Permissions") ?>" data-placeholder="<?php echo e("Permissions") ?>" multiple data-toggle="select">	                    						    						    						    					<optgroup label="<?php echo e("Links") ?>">
                        <option value="links.create"><?php echo e("Create Links") ?></option>
                        <option value="links.edit"><?php echo e("Edit Links") ?></option>
                        <option value="links.delete"><?php echo e("Delete Links") ?></option>
                    </optgroup>
                    <?php if (user()->has("qr") !== false): ?>
                        <optgroup label="<?php echo e("QR Codes") ?>">
                            <option value="qr.create"><?php echo e("Create QR") ?></option>
                            <option value="qr.edit"><?php echo e("Edit QR") ?></option>
                            <option value="qr.delete"><?php echo e("Delete QR") ?></option>
                        </optgroup>
                    <?php endif ?>
                    <?php if (user()->has("bio") !== false): ?>
                        <optgroup label="<?php echo e("Bio Pages") ?>">
                            <option value="bio.create"><?php echo e("Create Bio") ?></option>
                            <option value="bio.edit"><?php echo e("Edit Bio") ?></option>
                            <option value="bio.delete"><?php echo e("Delete Bio") ?></option>
                        </optgroup>
                    <?php endif ?>
                    <?php if (user()->has("splash") !== false): ?>
                        <optgroup label="<?php echo e("Custom Splash") ?>">
                            <option value="splash.create"><?php echo e("Create Splash") ?></option>
                            <option value="splash.edit"><?php echo e("Edit Splash") ?></option>
                            <option value="splash.delete"><?php echo e("Delete Splash") ?></option>
                        </optgroup>
                    <?php endif ?>
                    <?php if (user()->has("overlay") !== false): ?>
                        <optgroup label="<?php echo e("CTA Overlay") ?>">
                            <option value="overlay.create"><?php echo e("Create Overlay") ?></option>
                            <option value="overlay.edit"><?php echo e("Edit Overlay") ?></option>
                            <option value="overlay.delete"><?php echo e("Delete Overlay") ?></option>
                        </optgroup>
                    <?php endif ?>
                    <?php if (user()->has("pixels") !== false): ?>
                            <optgroup label="<?php echo e("Tracking Pixels") ?>">
                            <option value="pixels.create"><?php echo e("Create Pixels") ?></option>
                            <option value="pixels.edit"><?php echo e("Edit Pixels") ?></option>
                            <option value="pixels.delete"><?php echo e("Delete Pixels") ?></option>
                        </optgroup>
                    <?php endif ?>
                    <?php if (user()->has("domain") !== false): ?>
                            <optgroup label="<?php echo e("Branded Domain") ?>">
                            <option value="domain.create"><?php echo e("Add Custom Domain") ?></option>
                            <option value="domain.delete"><?php echo e("Delete Custom Domain") ?></option>
                        </optgroup>
                    <?php endif ?>
                    <?php if (user()->has("bundle") !== false): ?>
                            <optgroup label="<?php echo e("Campaigns") ?>/<?php ee('Channels') ?>">
                            <option value="bundle.create"><?php echo e("Create Campaigns") ?>/<?php ee('Channels') ?></option>
                            <option value="bundle.edit"><?php echo e("Edit Campaigns") ?>/<?php ee('Channels') ?></option>
                            <option value="bundle.delete"><?php echo e("Delete Campaigns") ?>/<?php ee('Channels') ?></option>
                        </optgroup>
                    <?php endif ?>
                    <?php if (user()->has("api") !== false): ?>
                        <option value="api.create"><?php echo e("Developer API") ?></option>
                    <?php endif ?>
                    <?php if (user()->has("export") !== false): ?>
                        <option value="export.create"><?php echo e("Export Data") ?></option>
                    <?php endif ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php ee('Cancel') ?></button>
            <button type="submit" class="btn btn-success"><?php ee('Invite') ?></button>
        </div>
        </div>
    </form>
  </div>
</div>
<?php endif ?>