<?php

require_once __DIR__ . '/../partials/errors.php';
require_once __DIR__ . '/../partials/success.php';

?>

<?php require __DIR__ . '/../partials/paginator.php'; ?>

<div class="posts row">
    <?php foreach ($posts as $post): ?>

        <div class="col-4 post">
            <h2>
                <a href="blog/<?php echo $post->id; ?>/<?php echo $post->getSlug(); ?>"><?php echo $post->title; ?></a>
            </h2>
            <div><?php echo $post->getContent(255); ?></div>
            <a href="blog/<?php echo $post->id; ?>/<?php echo $post->getSlug(); ?>">more ...</a>
        </div>

    <?php endforeach; ?>

</div>

<?php require __DIR__ . '/../partials/paginator.php'; ?>
