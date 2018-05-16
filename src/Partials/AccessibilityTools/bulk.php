<div class="wrap">
    <h1>Microsoft SCC Accessibility Tools - Bulk Optimize</h1>
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="accessibility-card">
                <h2>Optimized Images</h2>
                <h4>
                    <span id="images-optimized"><?= $total_optimized_images; ?></span> / <span id="images-total"><?= $total_images; ?></span>
                </h4>
                <div class="progress">
                    <div id="images-progress" class="progress-bar progress-bar-striped <?= $images_finished; ?>" role="progressbar" aria-valuenow="<?= $total_images_progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $total_images_progress; ?>%"></div>
                </div>
                <div class="btn-container">
                    <button id="btn-optimize-images" class="button button-primary">Optimize Images</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="accessibility-card">
                <h2>Optimized Posts</h2>
                <h4>
                    <span id="posts-optimized"><?= $total_optimized_posts; ?></span> / <span id="posts-total"><?= $total_posts; ?></span>
                </h4>
                <div class="progress">
                    <div id="posts-progress" class="progress-bar progress-bar-striped <?= $posts_finished; ?>" role="progressbar" aria-valuenow="<?= $total_posts_progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $total_posts_progress; ?>%"></div>
                </div>
                <div class="btn-container">
                    <button id="btn-optimize-posts" class="button button-primary">Optimize Posts</button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <p>
                Attention: It's highly recommended that you optimize all the images first because the posts optimization will use the images captions.
            </p>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="clear"></div>
<script>

    document.getElementById("btn-optimize-images").addEventListener("click", function() {
        document.getElementById("images-progress").classList.add("progress-bar-animated");
        optimizeItem( 'images' );
    });

    document.getElementById("btn-optimize-posts").addEventListener("click", function() {
        document.getElementById("posts-progress").classList.add("progress-bar-animated");
        optimizeItem( 'posts' );
    });

    function updateTotalOptimized( type, add ) {
        var total_container = document.getElementById(type + "-total");
        var optimized_container = document.getElementById(type + "-optimized");
        var progress_container = document.getElementById(type + "-progress");

        var total = parseInt(total_container.innerHTML);
        var optimized = parseInt(optimized_container.innerHTML) + add;
        var progress = Math.round(optimized / total * 100);

        optimized_container.innerHTML = optimized;
        progress_container.setAttribute("aria-valuenow", progress);
        progress_container.style.width = progress + '%';
    }

    function optimizeItem( type ) {
        var request = new XMLHttpRequest();
        var response = "";
        request.onreadystatechange = function() {
            if( request.readyState === 4 ) {
                if ( request.status === 200 ) {
                    response = JSON.parse(request.responseText);
                    if (response.finished == false) {
                        if (response.error == false) {
                            updateTotalOptimized( type, 1 );
                        }
                        optimizeItem( type );
                    } else {
                        if (response.finished == true) {
                            document.getElementById(type + "-progress").classList.add("bg-success");
                        }
                        document.getElementById(type + "-progress").classList.remove("progress-bar-animated");
                    }
                } else {
                    optimizeItem( type );
                }
            }
        }
        request.open( 'GET', ajaxurl + '?action=optimize_' + type );
        request.send();
    }

</script>