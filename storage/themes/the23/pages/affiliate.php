<section class="bg-primary py-10">
    <div class="container">
        <div class="card border-0 shadow-sm mb-5 mx-lg-5">
            <div class="card-body text-center py-10 px-5">
                <h1 class="display-2 fw-bold"><?php ee('Earn {p} commission on affiliate sales', null, ['p' => "<u><strong class=\"gradient-primary clip-text\">".(isset($affiliate->type) && $affiliate->type == 'fixed' ? '$'.$affiliate->rate : $affiliate->rate.'%')."</strong></u>"]) ?></h1>
                <p class="my-5 lead"><?php ee('Refer customers to us and we will reward you a {p} commission on all qualifying sales made on our website. Anyone can join the affiliate program.', null, ['p' => (isset($affiliate->type) && $affiliate->type == 'fixed' ? '$'.$affiliate->rate : $affiliate->rate.'%')]) ?></p>
                <?php if(\Core\Auth::logged()): ?>
                    <a href="<?php echo route('user.affiliate') ?>" class="btn btn-secondary px-5 py-3 fw-bold"><?php ee('View Affiliate Portal') ?></a>
                <?php else: ?>
                    <a href="<?php echo route('register') ?>" class="btn btn-secondary px-5 py-3 fw-bold"><?php ee('Join now') ?></a>
                <?php endif ?>
            </div>
        </div>
        <?php if($affiliatefaqs = \Core\DB::faqs()->where('category', 'affiliate')->find()): ?>
        <div class="py-5">
            <div class="text-center mb-5">
                <h2 class="mb-4 fw-bold"><?php ee('Frequently Asked Questions') ?></h2>
                <p><?php echo $affiliate->terms ?></p>
                <a href="<?php echo route('contact') ?>" class="btn btn-primary"><?php ee('Contact us') ?></a>
            </div>
            <div class="row">
                <div class="col-md-6">
                <?php foreach($affiliatefaqs as $i => $faq): ?>
                    <?php if($i == round(count($affiliatefaqs) / 2)): ?>
                        </div>
                        <div class="col-md-6">
                    <?php endif; ?>                
                        <div id="<?php echo 'faq-holder-'.$faq->slug ?>" class="mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header border-0 bg-transparent py-4" id="<?php echo $faq->slug ?>" data-bs-toggle="collapse" role="button" data-bs-target="#faq-<?php echo $faq->id ?>" aria-expanded="false" aria-controls="faq-<?php echo $faq->id ?>">
                                    <h6 class="mb-0 fw-bold"><?php ee($faq->question) ?></h6>
                                </div>
                                <div id="faq-<?php echo $faq->id ?>" class="collapse" aria-labelledby="<?php echo $faq->slug ?>" data-parent="#<?php echo 'faq-holder-'.$faq->slug ?>">
                                    <div class="card-body">
                                        <?php ee(strip_tags($faq->answer)) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php endforeach ?>
                </div>
            </div>
        </div>
        <?php endif ?>
    </div>
</section>