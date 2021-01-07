<div class="colors">
    <?php foreach ($colors as $color): ?>
        <div class="color">
            <div class="color__label">
                <div class="name"><?php echo $color->name; ?></div>
                <div class="year">(<?php echo $color->year; ?>)</div>
            </div>
            <!--            @todo: comment (style-attribut)!!-->
            <div class="color__sample" style="background-color: <?php echo $color->color; ?>"></div>
        </div>
    <?php endforeach; ?>
</div>
