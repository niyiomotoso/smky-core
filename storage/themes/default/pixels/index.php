<div class="d-flex">
    <div>
        <h1 class="h3 mb-5"><?php ee('Tracking Pixels') ?></h1>
    </div>
    <div class="ms-auto">
        <?php if(\Core\Auth::user()->teamPermission('pixels.create')): ?>
            <a href="<?php echo route('pixel.create') ?>" class="btn btn-primary"> <?php ee('Add Pixel') ?></a>
        <?php endif ?>
    </div>
</div>
<div class="row">
    <div class="col-md-9">
        <div class="card p-2">
            <div class="d-flex">
                <div class="ms-auto">
                    <form action="<?php echo route('pixels') ?>" method="get" class="d-flex align-items-center">
                        <div class="me-2">
                            <input type="text" class="form-control border p-2" name="q" value="<?php echo clean(request()->q) ?>" placeholder="<?php ee('Search for {t}', null, ['t' => e('Tracking Pixels')]) ?>">
                        </div>
                        <div class="me-2">
                            <div class="input-select d-block">
                                <select name="sort" id="sortable" class="form-select p-2 border">
                                    <optgroup label="<?php ee('Provider') ?>">
                                    <option value="all"><?php ee('All') ?></a>
                                    <?php foreach($providers as $key => $provider): ?>
                                        <option value="<?php echo $key ?>"><?php echo $provider['name'] ?></option>
                                    <?php endforeach ?>
                                    </optgroup>
                                </select>
                            </div>                                
                        </div>
                        <div>
                            <button type="submit" class="btn btn-default bg-white"><i data-feather="search"></i></button>  
                        </div>                          
                    </form>
                </div>
            </div>
        </div>        
        <?php if($pixels): ?>            
            <div class="row">
                <?php foreach($pixels as $pixel): ?>
                    <div class="col-md-6">
                        <div class="card flex-fill shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo ucfirst(\Helpers\App::pixelName($pixel->type)) ?>"><img src="<?php echo assets('images/'.str_replace(['pixel','fb'], ['', 'facebook'], $pixel->type).'.svg') ?>" width="45"></span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="float-end">
                                            <button type="button" class="btn btn-default bg-white" data-bs-toggle="dropdown" aria-expanded="false"><i data-feather="more-horizontal"></i></button>
                                            <ul class="dropdown-menu">
                                            <?php if(\Core\Auth::user()->teamPermission('pixels.edit')): ?>
                                                <li><a class="dropdown-item" href="<?php echo route('pixel.edit', [$pixel->id]) ?>"><i data-feather="edit"></i> <?php ee('Edit Pixel') ?></a></li>
                                            <?php endif ?>
                                            <?php if(\Core\Auth::user()->teamPermission('pixels.delete')): ?>
                                                <li class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" data-bs-toggle="modal" data-trigger="modalopen" data-bs-target="#deleteModal" href="<?php echo route('pixel.delete', [$pixel->id, \Core\Helper::nonce('pixel.delete')]) ?>"><i data-feather="trash"></i> <?php ee('Delete') ?></a></li>
                                            <?php endif ?>
                                            </ul>
                                        </div>
                                        <strong><?php echo $pixel->name ?: 'n\a' ?></strong> <br>
                                        <small class="text-navy"><?php echo \Core\Helper::timeago($pixel->created_at) ?></small>
                                        <br>
                                        <small class="badge border border-dark text-dark"><?php echo \Core\Helper::truncate($pixel->tag, 20) ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
            <?php echo pagination('bg-white shadow rounded pagination p-3') ?>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <p><?php ee('No content found. You can create some.') ?></p>
                    <?php if(\Core\Auth::user()->teamPermission('pixels.create')): ?>
                        <a href="<?php echo route('pixel.create') ?>" class="btn btn-primary btn-sm"><?php ee('Add Pixel') ?></a>
                    <?php endif ?>
                </div>
            </div>
        <?php endif ?>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3"><?php ee('Pixels') ?> <small class="float-end"><?php echo $count ?> / <?php echo $total == 0 ? e('Unlimited') : $total ?></small></h5>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $total == 0 ? 100 : round($count*100/$total) ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex">
                    <h5 class="card-title mb-0"><?php ee('What are tracking pixels?') ?></h5>
                </div>
            </div>
            <div class="card-body">
                <p> <?php echo ee('Ad platforms such as Facebook and Adwords provide a conversion tracking tool to allow you to gather data on your customers and how they behave on your website. By adding your pixel ID from either of the platforms, you will be able to optimize marketing simply by using short URLs.') ?></p>
                <a href="<?php echo route('faq') ?>#pixels" class="btn btn-primary btn-sm"><?php ee("More info") ?></a>
            </div>
        </div>
        <?php plug('sidebar.pixel') ?>
    </div>
</div>
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