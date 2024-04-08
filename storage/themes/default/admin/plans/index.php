<div class="d-flex">
    <div>
        <h1 class="h3 mb-5"><?php ee('Plans') ?></h1>
    </div>
    <div class="ms-auto">
        <a href="<?php echo route('admin.plans.new') ?>" class="btn btn-primary"> <?php ee('Add Plan') ?></a>
        <?php if (\Helpers\App::possible()): ?>
            <a href="<?php echo route("admin.plans.sync") ?>" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php ee('Sync Plans') ?>"><i data-feather="repeat"></i></a> 
            <a href="<?php echo route("admin.settings.config",['payments']) ?>" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title=" <?php ee('Settings') ?>"><i data-feather="settings"></i></a> 
        <?php endif ?>
    </div>
</div>
<div class="card flex-fill shadow-sm">    
    <div class="table-responsive">
        <table class="table table-hover my-0">
            <thead>
                <tr>
                    <th scope="col"><?php ee('Name') ?></th>
                    <th scope="col"><?php ee('Price M/Y/L') ?></th>
                    <th scope="col"><?php ee('Users') ?></th>
                    <th scope="col" class="w-50"><?php ee('Details') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($plans as $plan): ?>
                    <tr>
                        <td>
                            <?php echo $plan->name ?> <br>
                            <?php if($plan->status): ?>
                                <span class="badge border border-success text-success"><?php ee('Enabled') ?></span>
                            <?php else: ?>
                                <span class="badge border border-danger text-danger"><?php ee('Disabled') ?></span>
                            <?php endif ?>
                            <?php if($plan->hidden): ?>
                                <span class="badge border border-warning text-danger"><?php ee('Hidden') ?></span>
                            <?php endif ?>
                            <?php echo ($plan->trial_days ? "<span class='badge text-info border border-info'>{$plan->trial_days}-day trial</span>" : "") ?>
                        </td>
                        <td>
                            <?php if ($plan->free): ?>
                                <?php ee('Free') ?>
                            <?php else: ?>
                                <?php echo $plan->price_monthly ? \Helpers\App::currency(config('currency'), $plan->price_monthly).' /' : 'none' ?>
                                <?php echo $plan->price_yearly ? \Helpers\App::currency(config('currency'), $plan->price_yearly).' /' : 'none' ?>
                                <?php echo $plan->price_lifetime ? \Helpers\App::currency(config('currency'), $plan->price_lifetime) : 'none' ?>
                            <?php endif ?>                        
                        </td>
                        <td>
                            <a href="<?php echo route('admin.users', ['plan' => $plan->id]) ?>" class="badge bg-primary"><?php echo \Core\DB::user()->where('planid', $plan->id)->count() ?> <?php ee('users') ?>
                        </td>
                        <td>
                            <span class="badge text-primary border border-primary"><?php echo $plan->numurls == "0" ? "Unlimited" : $plan->numurls ?> <?php echo e('links').' '.($plan->counttype ?? '') ?></span>
                            <?php foreach (json_decode($plan->permission) as $type => $p): ?>
                                <?php if (isset($p->enabled) && $p->enabled): ?>
                                    <?php $count = NULL; if (isset($p->count)): ?>
                                        <?php $count = $p->count == "0" ? "Unlimited" : $p->count ?>
                                    <?php endif ?>
                                    <span class="badge text-primary border border-primary"><?php echo $count ?> <?php echo $type == "api" ? "API Access" : ucfirst($type) ?></span>
                                <?php endif ?>
                            <?php endforeach ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default bg-white" data-bs-toggle="dropdown" aria-expanded="false"><i data-feather="more-horizontal"></i></button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo route('admin.plans.toggle', [$plan->id]) ?>"> <?php echo $plan->status == '1' ? '<i data-feather="toggle-left"></i> '.e('Disable') : '<i data-feather="toggle-right"></i> '.e('Enable') ?></a></li>
                                <li><a class="dropdown-item" href="<?php echo route('admin.plans.edit', [$plan->id]) ?>"><i data-feather="edit"></i> <?php ee('Edit') ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" data-bs-toggle="modal" data-trigger="modalopen" data-bs-target="#deleteModal" href="<?php echo route('admin.plans.delete', [$plan->id, \Core\Helper::nonce('plan.delete')]) ?>"><i data-feather="trash"></i> <?php ee('Delete') ?></a></li>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>    
    </div>
    <?php echo pagination('pagination') ?>
</div>
<div class="modal fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
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