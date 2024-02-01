<section id="blog" class="bg-primary">
	<div class="container">
		<div class="py-8 text-center">
            <h2 class="display-6 fw-bold">
                <?php ee('Blog') ?>
            </h2>
        </div>
        <?php view('blog.menu', compact('menu')) ?>
        <div class="py-5">
            <div class="row">
                <div class="col-md-8 mb-5">
                    <?php foreach($posts as $post): ?>
                        <?php view('blog.partial', compact('post', 'categories')); ?>
                    <?php endforeach ?>
                    <?php echo pagination('pagination bg-white rounded p-2 shadow-sm', 'page-item', 'page-link') ?>
                </div>
                <div class="col-md-4">
                    <?php \Helpers\App::ads('blogsidebar') ?>
                    <h5 class="fw-bolder mb-3"><?php ee('Popular Posts') ?></h5>
                    <?php foreach($popular as $post): ?>
                        <a href="<?php echo route('blog.post', [$post->slug]) ?>" class="mb-2 d-block" title="<?php echo $post->title ?>"><?php echo $post->title ?></a>
                    <?php endforeach ?>
                    <?php plug('blogsidebar') ?>
                </div>
            </div>
        </div>
    </div>
</section>