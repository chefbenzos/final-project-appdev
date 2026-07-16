<?php
    $footerVariant = $footerVariant ?? 'standard';
    $footerClass = $footerVariant === 'login'
        ? 'site-footer site-footer--login'
        : 'site-footer site-footer--standard';
?>
<footer class="<?= $footerClass ?>">
    <p>&#169; 2026 BHP University by Piosang Home Page</p>
</footer>
<?php unset($footerVariant, $footerClass); ?>
