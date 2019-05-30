<?php foreach($posts as $post){ ?>
    <div>
        <h3><a href=""><?php echo $post->title ?></a></h3>
        <p><?php echo $post->description ?></p>
        <div class="text-right">
            <button class="btn btn-success">Read More</button>
        </div>
        <hr style="margin-top:5px;">
    </div>
<?php } ?>