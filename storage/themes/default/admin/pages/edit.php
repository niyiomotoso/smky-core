<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo route('admin') ?>"><?php ee('Dashboard') ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo route('admin.page') ?>"><?php ee('Pages') ?></a></li>
  </ol>
</nav>
<div class="d-flex">
    <h1 class="h3 mb-5"><?php ee('Edit Page') ?></h1>

    <div class="ms-auto">
        <a href="<?php echo route('page', [$page->seo]) ?>" class="btn btn-primary" target="_blank"><?php ee('View Page') ?></a>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="<?php echo route('admin.page.update', [$page->id]) ?>" enctype="multipart/form-data" data-trigger="editor">
            <?php echo csrf() ?>
            <div class="form-group mb-4">
                <label for="name" class="form-label"><?php ee('Name') ?></label>
                <input type="text" class="form-control p-2" name="name" id="name" value="<?php echo $page->name ?>" placeholder="My Sample Page">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label for="slug" class="form-label"><?php ee('Slug') ?></label>
                        <input type="text" class="form-control p-2" name="slug" id="slug" value="<?php echo $page->seo ?>" placeholder="my-sample-page">
                        <p class="form-text"><?php ee('Leave this empty to automatically generate it from the title.') ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-4 rounded input-select">
                        <label for="category" class="form-label"><?php ee('Category') ?></label>
                        <select class="form-select p-2" name="category" id="category">
                            <?php foreach(\Helpers\App::pageCategories() as $id => $category): ?>
                                <option value="<?php echo $id ?>" <?php if($page->category == $id) echo "selected" ?>><?php echo $category ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label for="lang" class="form-label"><?php ee('Language') ?></label>
                        <select class="form-select p-2" name="lang">
                            <option value="en">English</option>
                            <?php foreach(\Core\Localization::listInfo() as $lang): ?>
                                <option value="<?php echo $lang['code'] ?>"<?php echo $page->lang == $lang['code'] ? 'selected' : '' ?>><?php echo $lang['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group mb-4">
                <label for="content" class="form-label"><?php ee('Content') ?></label>
                <p class="form-text"><?php ee('Use the rich editor below to write your page.') ?></p>
                <textarea name="content" id="editor" class="form-control"><?php echo $page->content ?></textarea>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label for="metatitle" class="form-label"><?php ee('Meta Title') ?></label>
                        <input type="text" class="form-control p-2" name="metatitle" id="metatitle" value="<?php echo $page->metadata->title ?>">
                        <p class="form-text"><?php ee('If you want to define a custom meta title fill this field otherwise leave it empty to use post title.') ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label for="metadescription" class="form-label"><?php ee('Meta Description') ?></label>
                        <input type="text" class="form-control p-2" name="metadescription" id="metadescription" value="<?php echo $page->metadata->description ?>">
                        <p class="form-text"><?php ee('If you want to define a custom meta description fill this field otherwise leave it empty to use post title.') ?></p>
                    </div>
                </div>
            </div>
            <hr>
            <div class="form-group mb-4">
                <label for="menu" class="form-label"><?php ee('Menu') ?></label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" data-binary="true" id="menu" name="menu" value="1" <?php echo $page->menu ? 'checked' : '' ?>>
                    <label class="form-check-label" for="menu"><?php ee('Enabled') ?></label>
                </div>
                <p class="form-text"><?php ee('Do you want to add a link to this page in the menu?') ?></p>
            </div>
            <div class="d-flex">
                <button type="submit" class="btn btn-success"><?php ee('Update Page') ?></button>
            </div>
        </form>

    </div>
</div>