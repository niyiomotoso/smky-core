<section id="blog" class="bg-primary">
	<div class="container">
		<div class="py-10 text-center">
            <h2 class="display-6 fw-bold">
                <?php ee('Blog') ?>
            </h2>
        </div>
        <?php view('blog.menu', compact('menu', 'q')) ?>
        <div class="py-5">
            <div class="row">
                <div class="col-md-8 mb-5">
                    <?php if($posts): ?>
                        <h3 class="mb-5"><?php ee('Results for "{q}"', null, ['q' => $q]) ?></h3>
                        <?php foreach($posts as $post): ?>
                            <?php view('blog.partial', compact('post', 'categories')); ?>
                        <?php endforeach ?>
                        <?php echo pagination('pagination bg-white rounded p-2 shadow-sm', 'page-item', 'page-link') ?>
                    <?php else: ?>
                        <?php ee('No results') ?>
                    <?php endif ?>
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