<?php
$captionPosition = html_escape($params['captionLocation'] ?? 'left');
$showDesc = filter_var($params['showDescr'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
$float = html_escape($params['float'] ?? 'left');
$width = html_escape($params['width'] ?? '100%');
$noNav = filter_var($params['noNav'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
$idSuffix = $id_suffix ?? 0;
$carouselId = "connected-carousel-$idSuffix";
$navId = !$noNav ? "connected-nav-$idSuffix" : null;
?>

<div id="<?= $carouselId ?>" class="connected-carousel" style="float: <?= $float ?>; width: <?= $width ?>;">
    <div class="stage">
        <?php foreach ($items as $item): ?>
            <?php foreach ($item->Files as $file): ?>
                <div class="image-container">
                    <?php
                    $img = file_image('fullsize', [], $file);
                    echo link_to($file, 'show', $img, ['class' => 'fancybox', 'data-fancybox' => 'gallery']);
                    ?>
                    <?php if ($captionPosition !== 'none'): ?>
                        <div class="caption" style="text-align: <?= $captionPosition ?>;">
                            <?= metadata($file, ['Dublin Core', 'Description']) ?: metadata($item, ['Dublin Core', 'Description']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($showDesc): ?>
                        <div class="description">
                            <?= all_element_texts($item, ['show_empty_elements' => false]) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

        <div id="<?= $navId ?>" class="navigation">
            <?php foreach ($items as $item): ?>
                <?php foreach ($item->Files as $file): ?>
                    <div class="thumbnail">
                        <?= file_image('square_thumbnail', [], $file) ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
</div>

<script>
jQuery(document).ready(function($) {
    var $carousel = $('#<?= $carouselId ?> .stage');
    var navSelector = <?= !$noNav ? "'#$navId'" : 'null' ?>;

    // Initialize the main carousel
    $carousel.slick({
        slidesToShow: <?= json_encode($configs['slidesToShow']) ?>,
        slidesToScroll: <?= json_encode($configs['slidesToScroll']) ?>,
        centerMode: <?= json_encode(filter_var($configs['centerMode'], FILTER_VALIDATE_BOOLEAN)) ?>,
        autoplay: <?= json_encode(filter_var($configs['autoPlay'], FILTER_VALIDATE_BOOLEAN)) ?>,
        autoplaySpeed: <?= json_encode($configs['autoplaySpeed']) ?>,
        focusOnSelect: <?= json_encode(filter_var($configs['focusOnSelect'], FILTER_VALIDATE_BOOLEAN)) ?>,
        arrows: true, // Ensure this is true
        asNavFor: navSelector
    });

    // Initialize navigation carousel if enabled
    if (navSelector) {
        $(navSelector).slick({
            slidesToShow: <?= json_encode($configs['slidesToShow']) ?>,
            slidesToScroll: <?= json_encode($configs['slidesToScroll']) ?>,
            asNavFor: $carousel,
            focusOnSelect: true,
            centerMode: <?= json_encode(filter_var($configs['centerMode'], FILTER_VALIDATE_BOOLEAN)) ?>
        });
    }

    // Initialize Fancybox for image gallery
	$('[data-fancybox="gallery"]').fancybox({
		buttons: ['slideShow', 'fullScreen', 'thumbs', 'close'],
		loop: true
	});
});
</script>
