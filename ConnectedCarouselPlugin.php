<?php

class ConnectedCarouselPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = [
        'public_head',
        'exhibit_builder_page_head'
    ];
    protected $_filters = [
        'exhibit_layouts'
    ];

    public function setUp()
    {
        add_shortcode('concarousel', [self::class, 'carousel']);
        parent::setUp();
    }

    public function hookPublicHead($args)
    {
        $this->enqueueAssets();
    }

    public function hookExhibitBuilderPageHead($args)
    {
        $this->enqueueAssets();
    }

    public function filterExhibitLayouts($layouts)
    {
        $layouts['concarousel'] = [
            'name' => __('Connected Carousel'),
            'description' => __('Select image IDs to display in a carousel')
        ];
        return $layouts;
    }

    public static function carousel($args, $view)
    {
        static $id_suffix = 0;

        $params = [
            'float' => $args['float'] ?? 'left',
            'width' => $args['width'] ?? '100%',
            'noNav' => $args['navbar'] ?? 'true',
            'range' => $args['ids'] ?? '',
            'captionLocation' => $args['captionposition'] ?? 'left',
            'showDescr' => $args['showdescription'] ?? 'false',
            'hasImage' => 1
        ];

        $ids = self::parseIds($params['range']);
        $items = array_filter(array_map(fn($id) => get_record_by_id('item', $id), $ids));

        $configs = [
            'slidesToShow' => $args['slides'] ?? 5,
            'slidesToScroll' => $args['scroll'] ?? 1,
            'centerMode' => $args['center'] ?? 'false',
            'autoPlay' => $args['slideshow'] ?? 'false',
            'autoplaySpeed' => is_numeric($args['speed'] ?? null) ? (int) $args['speed'] : 2000,
            'focusOnSelect' => $args['focus'] ?? 'true',
            'arrows' => $args['navigation'] ?? 'false'
        ];

        $html = $view->partial('carousel.php', [
            'items' => $items,
            'id_suffix' => $id_suffix,
            'params' => $params,
            'configs' => $configs
        ]);

        $id_suffix++;
        return $html;
    }

    private function enqueueAssets(): void
    {
        queue_js_file('slick');
        queue_css_file('slick');

        queue_js_file('jquery.fancybox');
        queue_css_file('jquery.fancybox');
        queue_css_file('jquery.fancybox-buttons');
    }

    private static function parseIds($range)
    {
        $expanded = preg_replace_callback('/(\d+)-(\d+)/', fn($m) => implode(',', range($m[1], $m[2])), $range);
        return array_filter(explode(',', $expanded), 'is_numeric');
    }
}
