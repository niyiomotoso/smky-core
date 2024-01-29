<?php view('stats.partial', ['url' => $url, 'top' => $top]) ?>

<div class="card border-0 card-body shadow-sm mb-4">
    <div class="align-items-center">
        <?php view('partials.stats_nav', ['url' => $url]) ?>
    </div>
</div>

<div class="d-flex mt-5 mb-4 align-items-center">
    <h3 class="mb-0 fw-bold"><?php ee('Top Referrers') ?></h3>
</div>

<div class="row">            
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">            
            <div class="card-body px-4">
                <ul id="top-referrers" class="list-unstyled d-block">
                    <?php foreach($topReferrer as $referrer): ?>
                        <li class="d-block mb-2 w-100 border-bottom pb-2 fw-bold"><img src="<?php echo !empty($referrer['domain']) ? "https://icons.duckduckgo.com/ip3/".\Core\Helper::parseUrl($referrer['domain'], 'host').".ico" : assets('images/unknown.svg') ?>" width="16" class="me-2 mr-2"><span class="align-middle"><?php echo empty($referrer['domain']) ? e('Direct, email and others') : $referrer['domain'] ?></span> <small class="badge bg-dark text-white float-right float-end"><?php echo $referrer['count'] ?></small></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-0">
            <div class="p-3">
                <h5 class="card-title mb-0 fw-bold"><?php ee('Social Media') ?></h5>
            </div>
            <div class="card-body px-4">
                <canvas style="min-height:200px"></canvas>
            </div>
        </div>
    </div>
</div>