<div class="colors">
    <?php foreach ($colors as $color): ?>
        <div class="color">
            <div class="color__label">
                <div class="name"><?php echo $color->name; ?></div>
                <div class="year">(<?php echo $color->year; ?>)</div>
            </div>
            <?php
            /**
             * Hier müssen wir das style-Attribut verwenden, da die Background-Color aus den API-Daten kommt und nicht
             * über ein CSS File gesetzt werden kann.
             */
            ?>
            <div class="color__sample" style="background-color: <?php echo $color->color; ?>"></div>
        </div>
    <?php endforeach; ?>
</div>
