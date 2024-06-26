<div class="d-flex">
	<div>
		<h1 class="h3 mb-5"><?php echo \Core\View::$title ?></h1>
	</div>
	<div class="ms-auto">
		<a href="<?php echo route('admin.users.new') ?>" class="btn btn-primary"><?php ee('Add User') ?></a>
	</div>
</div>
<div class="card p-3">
	<div class="d-flex">
		<div>
			<form method="post" action="" class="d-inline" data-trigger="options">
				<?php echo csrf() ?>
				<input type="hidden" name="selected">        		
				<a class="btn bg-white border" href="<?php echo route('admin.users.emailall') ?>" data-trigger="submitchecked"><i class="me-1" data-feather="send"></i> <span class="align-middle"><?php ee('Send Email') ?></span></a>
				<a class="btn bg-white border" href="<?php echo route('admin.users.banall') ?>" data-trigger="submitchecked"><i class="me-1" data-feather="x-circle"></i> <span class="align-middle"><?php ee('Ban Selected') ?></span></a>
				<a class="btn bg-white border" href="<?php echo route('admin.users.deleteall') ?>" data-trigger="submitchecked"><i class="me-1" data-feather="trash"></i> <span class="align-middle"><?php ee('Delete Selected') ?></span></a>  
			</form> 
		</div>  
		<div class="ms-auto">            			
			
		</div>
	</div>
</div>
<div class="card flex-fill shadow-sm">
	<div class="table-responsive">
		<table class="table table-hover my-0">
			<thead>
				<tr>
					<th width="1%">
					<input class="form-check-input me-2" type="checkbox" data-trigger="checkall">
					</th>
					<th><?php ee('Email') ?></th>
					<th><?php ee('User Status') ?></th>
					<th><?php ee('Registration Date') ?></th>
                    <th><?php ee('Last Seen') ?></th>
					<th><?php ee('Membership') ?></th>
					<th><?php ee('Expiration') ?></th>
					<th><?php ee('URLs') ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($users as $user): ?>
					<tr>
						<td><input class="form-check-input me-2" type="checkbox" data-dynamic="1" value="<?php echo $user->id ?>"></td>
						<td>
							<div class="d-flex align-items-center">
								<img src="<?php echo $user->avatar() ?>" alt="" width="36" class="img-responsive rounded-circle">
								<div class="ms-2">
									<?php echo ($user->admin)?"<strong>{$user->email}</strong>":$user->email ?> <?php echo ($user->trial)?"(".e('Free Trial').")":"" ?>
									<?php echo ($user->teams())?"<strong class=\"badge bg-primary\">".e("Team")."</strong>":'' ?>
									<?php if($user->verified) echo '<span class="badge bg-success ms-2">'.e('Verified').'</span>' ?>
								</div>
							</div>
						</td>
						<td><?php echo ($user->active ? '<span class="badge bg-success">'.e('Active').'</span>':'<span class="badge bg-danger">'.e('Not Active').'</span>') ?> <?php echo $user->banned ? '<span class="badge bg-danger">'.e('Banned').'</span>':'' ?></td>                
						<td><?php echo date("F d, Y",strtotime($user->date)) ?></td>
                        <td><?php echo $user->lasttimeonline ?></td>
						<td><?php echo ($user->pro ? '<span class="badge bg-success">'.$user->pro.'</span>':'<span class="badge bg-warning">'.e('Free').'</span>') ?></td>
						<td><?php echo ($user->pro ? date("F d, Y",strtotime($user->expiration)):"n/a") ?></td>                
						<td><a href="<?php echo route('admin.users.view', [$user->id]) ?>" class="btn btn-success btn-sm"><?php echo $user->count ?></a></td>
						<td>
							<button type="button" class="btn btn-default  bg-white" data-bs-toggle="dropdown" aria-expanded="false"><i data-feather="more-horizontal"></i></button>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" data-bs-toggle="modal" data-trigger="modalopen" data-bs-target="#loginModal" href="<?php echo route('admin.users.login', [$user->id, \Core\Helper::nonce('user.login.'.$user->id)]) ?>" target="_blank"><i data-feather="log-in"></i> <?php ee('Login as User') ?></a></li>
								<li><a class="dropdown-item" href="<?php echo route('admin.users.view', [$user->id]) ?>"><i data-feather="user"></i> <?php ee('User Profile') ?></a></li>
								<li><a class="dropdown-item" href="<?php echo route('admin.users.edit', [$user->id]) ?>"><i data-feather="edit"></i> <?php ee('Edit') ?></a></li>
								<li><a class="dropdown-item" href="<?php echo route('admin.users.ban', [$user->id]) ?>"><i data-feather="x-circle"></i> <?php echo $user->banned ? e('Unban') : e('Ban') ?></a></li>
								<?php if(!$user->active): ?>
									<li><a class="dropdown-item" href="<?php echo route('admin.users.verifyemail', [$user->id]) ?>"><i data-feather="send"></i> <?php ee('Verify Email') ?></a></li>
								<?php endif ?>
								<li><hr class="dropdown-divider"></li>
								<?php if($user->verified): ?>
								<li><a class="dropdown-item" href="<?php echo route('admin.users.unverify', [$user->id, \Core\Helper::nonce('unverify-'.$user->id)]) ?>"><i data-feather="check-circle" class="text-primary"></i> <?php ee('Unverify User') ?></a></li>
								<?php else: ?>
								<li><a class="dropdown-item" href="<?php echo route('admin.users.verify', [$user->id, \Core\Helper::nonce('verify-'.$user->id)]) ?>"><i data-feather="check-circle" class="text-primary"></i> <?php ee('Verify User') ?></a></li>
								<?php endif ?>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item text-danger" data-bs-toggle="modal" data-trigger="modalopen" data-bs-target="#deleteModal" href="<?php echo route('admin.users.delete', [$user->id, \Core\Helper::nonce('user.delete')]) ?>"><i data-feather="trash"></i> <?php ee('Delete User') ?></a></li>                                
								<li><a class="dropdown-item text-danger" data-bs-toggle="modal" data-trigger="modalopen" data-bs-target="#deleteModal" href="<?php echo route('admin.users.delete.all', [$user->id, \Core\Helper::nonce('user.delete')]) ?>"><i data-feather="trash-2"></i> <?php ee('Delete User + Data') ?></a></li>
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
<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title"><?php ee('You are about to login as a user') ?></h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	  </div>
	  <div class="modal-body">
		<p><?php ee("You are about to login as a user. For security reasons, you will be logged out from this account and logged in as this user. You will need to logout from this user's account and login back as your own account.") ?></p>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php ee('Cancel') ?></button>
		<a href="#" class="btn btn-success" data-trigger="confirm"><?php ee('Confirm') ?></a>
	  </div>
	</div>
  </div>
</div>