<div class="gallery">
    <div class="gallery__full">
        <figure class="figure">
            <img src="<?php echo $product->getImages()[0]; ?>" alt="Bild 1">
            <figcaption>Bild 1</figcaption>
        </figure>
    </div>
    <div class="gallery__thumbs">
        <?php foreach ($product->getImages() as $index => $image): ?>
            <figure class="figure<?php echo($index === 0 ? ' current' : '') ?>">
                <img src="<?php echo $image; ?>" alt="Bild <?php echo $index + 1; ?>">
            </figure>
        <?php endforeach; ?>
    </div>
    <div class="gallery__navigation">

        <nav>
            <ul class="pagination">
                <li class="page-item">
                    <button class="page-link btn-link">&laquo;</button>
                </li>
                <?php foreach ($product->getImages() as $index => $image): ?>
                    <li class="page-item<?php echo($index === 0 ? ' active' : '') ?>">
                        <button class="page-link btn-link"><?php echo $index + 1; ?></button>
                    </li>
                <?php endforeach; ?>
                <li class="page-item">
                    <button class="page-link btn-link">&raquo;</button>
                </li>
            </ul>
        </nav>

    </div>
    <div class="gallery__dots"></div>
</div>
