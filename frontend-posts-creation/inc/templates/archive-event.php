<?php get_header();?>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<div class="container">
    <div class="row">
        <div class="col-lg-12 my-3">
            <div class="pull-right">
                <div class="btn-group">
                    <button class="btn btn-info mr-2" id="list">
                        List View
                    </button>
                    <button class="btn btn-danger" id="grid">
                        Grid View
                    </button>
                </div>
            </div>
        </div>
    </div> 
    <div id="products" class="row view-group">
    <?php 
    $counter = 1;
    if(have_posts()):
        while(have_posts()): the_post();
        ?>
        <div class="item col-xs-4 col-lg-4">
            <div class="thumbnail card">
                <div class="img-event">
                    <img class="group list-group-image img-fluid" src="http://placehold.it/400x250/000/fff" alt="" />
                </div>
                <div class="caption card-body">
                    <h4 class="group card-title inner list-group-item-heading"> <?php echo get_the_title();?></h4>
                    <p class="group inner list-group-item-text"> 
                                    <?php echo get_the_excerpt();?></p>
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <a class="btn btn-success" href="<?php echo get_the_permalink();?>">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php 
        $counter++;
        endwhile;
        else:
            echo 'Sorry Nothing to show.';
endif;
    ?>
    </div>
</div>
<?php get_footer();?>