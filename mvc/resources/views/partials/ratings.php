<?php if (\App\Models\User::isLoggedIn()): ?>
    <div class="comment-rating">
        <h2>Rating & Comment</h2>
        <?php
        $existingRating = \App\Models\Rating::findByUserIdAndProductId(\App\Models\User::getLoggedIn()->id, $product->id);
        if (empty($existingRating)): ?>
            <form action="products/<?php echo $product->id; ?>/add-rating" method="post">
                <div class="form-group">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-secondary">
                            <input type="radio" name="rating" id="rating1" value="1"> 1
                        </label>
                        <label class="btn btn-secondary">
                            <input type="radio" name="rating" id="rating2" value="2"> 2
                        </label>
                        <label class="btn btn-secondary">
                            <input type="radio" name="rating" id="rating3" value="3"> 3
                        </label>
                        <label class="btn btn-secondary">
                            <input type="radio" name="rating" id="rating4" value="4"> 4
                        </label>
                        <label class="btn btn-secondary">
                            <input type="radio" name="rating" id="rating5" value="5"> 5
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="comment">Comment</label>
                    <textarea name="comment" id="comment" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        <?php else: ?>
            <div class="alert alert-info">Sie haben bereits einen Kommentar zu diesem Produkt abgegeben.</div>
        <?php endif; ?>

        <?php foreach ($ratings as $rating): ?>
            <div class="rating card">
                <div class="card-body">
                    <strong class="rating__author card-title">
                        <?php echo \App\Models\User::find($rating->user_id)->username; ?>
                        <small class="rating__rating text-muted"><?php echo $rating->rating; ?></small>
                    </strong>
                    <?php if (!empty($rating->comment)): ?>
                        <div class="rating__comment card-text"><?php echo $rating->comment; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($ratings)): ?>
            <div class="alert alert-info">Es gibt noch keine Bewertungen zu diesem Produkt.</div>
        <?php endif; ?>

    </div>
<?php endif; ?>
