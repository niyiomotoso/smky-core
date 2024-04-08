<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="<?php echo route('admin') ?>"><?php ee('Dashboard') ?></a></li>
	<li class="breadcrumb-item"><a href="<?php echo route('admin.links') ?>"><?php ee('Links') ?></a></li>
  </ol>
</nav>
<div class="d-flex mb-5">
	<div>
		<h1 class="h3"><?php echo \Core\View::$title ?></h1>
	</div>
	<div class="ms-auto">
	  <button type="button" class="btn btn-default bg-white shadow" data-bs-toggle="dropdown" aria-expanded="false"><i data-feather="filter"></i></button>
	   <form action="" method="get" class="dropdown-menu p-2">
		  <div class="input-select d-block mb-2">
			<label for="perpage" class="form-label"><?php ee('Contains') ?></label>
			<input type="text" class="form-control" name="q" value="<?php echo clean(request()->q) ?>" placeholder="<?php ee('Keyword in url') ?>">
		  </div>
		  <div class="input-select d-block mb-2">
			<label for="perpage" class="form-label"><?php ee('Sort By') ?></label>
			<select name="sort" id="sortable" data-name="sort" class="form-select">
			  <optgroup label="Sort by">
				<option value=""<?php if(!request()->sort) echo " selected" ?>><?php ee('Newest') ?></option>
				<option value="old"<?php if(request()->sort == 'old') echo " selected" ?>><?php ee('Oldest') ?></option>
				<option value="most"<?php if(request()->sort == 'most') echo " selected" ?>><?php ee('Most Popular') ?></option>
				<option value="less"<?php if(request()->sort == 'less') echo " selected" ?>><?php ee('Less Popular') ?></option>
			  </optgroup>
			</select>
		  </div>
		  <div class="input-select d-block mb-2">
			  <label for="perpage" class="form-label"><?php ee('Results Per Page') ?></label>
			  <select name="perpage" id="perpage" data-name="perpage" class="form-select">
				  <option value="15"<?php if(!request()->perpage) echo " selected" ?>>15</option>
				  <option value="50"<?php if(request()->perpage == 50) echo " selected" ?>>50</option>
				  <option value="100"<?php if(request()->perpage == 100) echo " selected" ?>>100</option>
			  </select>
		  </div>
		  <div class="input-select d-block mb-2">
			<label for="perpage" class="form-label"><?php ee('Older than') ?></label>
			<input type="text" class="form-control" name="date" placeholder="" value="<?php echo clean(request()->date) ?>" data-toggle="datepicker">
		  </div>
		  <button type="submit" class="btn btn-primary"><?php ee('Filter') ?></button>
		</form>
	</div>
</div>
<div class="card shadow-sm flex-fill">
	<div class="card-body h-100">
	  <form method="post" action="" data-trigger="options">
		<?php echo csrf() ?>
		<input class="form-check-input me-2" type="checkbox" data-trigger="checkall">
		<input type="hidden" name="selected">		  
		<div class="btn-group btn-group-sm border rounded px-1">
			<a class="btn btn-white" href="<?php echo route('admin.links.enableall') ?>" data-trigger="submitchecked"><i data-feather="check-circle"></i> <span class="align-middle"><?php ee('Enable Selected') ?></span></a>
			<a class="btn btn-white" href="<?php echo route('admin.links.disableall') ?>" data-trigger="submitchecked"><i data-feather="x-circle"></i> <span class="align-middle"><?php ee('Disable Selected') ?></span></a>
			<a class="btn btn-white" href="<?php echo route('admin.links.deleteall') ?>" data-trigger="submitchecked"><i data-feather="trash"></i> <span class="align-middle"><?php ee('Delete Selected') ?></span></a>
		</div>
	  </form>
	  <hr>
		<?php foreach($urls as $url): ?>
			<?php view('admin.partials.links', compact('url')) ?>
		<?php endforeach ?>
		<?php echo pagination('p-0 mt-5 pagination') ?>
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