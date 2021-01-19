<?php
/**
 * @todo: comment
 */
$hasParams = (strpos($currentUrl, '?') !== false);
$prevPage = 1;
$nextPage = 2;
if (isset($_GET['page'])) {
    $prevPage = max($_GET['page'] - 1, 1);
    $nextPage = min($_GET['page'] + 1, $numberOfPages);
}

$prevParam = "?page=$prevPage";
$nextParam = "?page=$nextPage";
if ($hasParams === true) {
    $prevParam = "&page=$prevPage";
    $nextParam = "&page=$nextPage";
}
?>
<div class="paginator row">
    <div class="btn-group">
        <a href="<?php echo $currentUrl . $prevParam ?>" class="btn btn-outline-secondary">&lt;</a>

        <?php for ($i = 1; $i <= $numberOfPages; $i++): ?>
            <?php
            /**
             * @todo: comment
             */
            $pageParam = "?page=$i";
            if ($hasParams === true) {
                $pageParam = "&page=$i";
            }

            $buttonClass = 'btn-outline-secondary';
            if (isset($_GET['page']) && (int)$_GET['page'] === $i) {
                $buttonClass = 'btn-secondary';
            }
            if (!isset($_GET['page']) && $i === 1) {
                $buttonClass = 'btn-secondary';
            }
            ?>
            <a class="btn <?php echo $buttonClass; ?>" href="<?php echo $currentUrl . $pageParam; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <a href="<?php echo $currentUrl . $nextParam ?>" class="btn btn-outline-secondary">&gt;</a>

    </div>
</div>
