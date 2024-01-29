<div class="d-flex">
		<div>
				<h1 class="h3 mb-5"><?php ee('Email Templates') ?></h1>
		</div>
		<div class="ms-auto">
				<a href="<?php echo route("admin.email.template.new") ?>" class="btn btn-primary"><?php ee('Create Translation') ?></a>
		</div>
</div>
<div class="row">
	<div class="col-md-9">
		<div class="card shadow-sm">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th><?php ee('Name') ?></th>
							<th><?php ee('Code') ?></th>
							<th><?php ee('Last Update') ?></th>
							<th><?php ee('% Translated') ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($languages as $language): ?>
							<tr>
								<td><?php echo $language["name"] ?> <?php echo config('default_lang') && config('default_lang') == $language["code"] ? '<span class="badge bg-primary">Default</span>': '' ?></td>
								<td><?php echo $language["code"] ?></td>
								<td><?php echo $language["date"] ?></td>
								<td>
									<div class="progress">
										<div class="progress-bar" role="progressbar" style="width: <?php echo $language["percent"] ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $language["percent"] ?>%</div>
									</div>
								</td>
								<td class="d-flex">
									<button type="button" class="btn btn-default bg-white ms-auto" data-bs-toggle="dropdown" aria-expanded="false"><i data-feather="more-horizontal"></i></button>
									<ul class="dropdown-menu">										
										<li><a class="dropdown-item" href="<?php echo route('admin.email.template.edit', [$language["code"]]) ?>"><i data-feather="edit"></i> <?php ee('Edit') ?></a></li>
										<li><hr class="dropdown-divider"></li>
										<li><a class="dropdown-item text-danger" data-bs-toggle="modal" data-trigger="modalopen" data-bs-target="#deleteModal" href="<?php echo route('admin.email.template.delete', [$language["code"], \Core\Helper::nonce('template.delete')]) ?>"><i data-feather="trash"></i> <?php ee('Delete') ?></a></li>
									</ul>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card card-default">
			<div class="card-header fw-bold"><?php ee('Emails') ?></div>
			<div class="card-body">
				<p><?php ee('You can now translate emails so your user receive emails in their preferred language.') ?></p>
			</div>
		</div>
	</div>
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