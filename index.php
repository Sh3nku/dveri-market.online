<?require $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php'?>

<?$Includes::Template(
    'main:content.list',
    'slider',
    array(
        'select' => array(
            'bind',
            'text',
            'picture',
            'link'
        ),
        'filter' => array(
            'iblock_id' => 25,
            'active' => 1
        ),
        'order' => array(
            'sort' => 'asc'
        )
    )
)?>

<?$Includes::Template(
    'main:catalog.offers.list',
    'carousel',
    array(
        'select' => array(
            'name',
            'model',
            'code',
            'offer_name',
            'offer_code',
            'offer_available',
            'picture',
            'offer_price',
            'offer_discount',
            'offer_discount_price',
            'offer_discount_type'
        ),
        'filter' => array(
            'iblock_id' => 29,
            'active' => 1,
            'offer_active' => 1,
            'offer_available' => 'new'
        ),
        'order' => array(
            'sort' => 'asc',
            'offer_sort' => 'asc'
        ),
        'nav' => array(
            'count_on_page' => 1000
        )
    )
)?>

<div class="container">

    <div class="main-o-nas">

        <div class="main-o-nas-picture">
            <img src="<?=TPL?>/images/o-nas.jpg" alt="Дверь в интерьере">
        </div>

        <div class="main-o-nas-text user-content">

            <h2>Мы сделаем Ваши двери!</h2>

            <p>Более 5000 дверей в наличии на складе в Санкт-Петербурге.</p>
            <p>Бесплатный замер. Реально быстрая доставка и монтаж.</p>
            <p>Изготовление любого нестандарта металлических и межкомнатных дверей.</p>
            <p>Мы не обещаем, что будет дешево. Мы обещаем, что будет качественно.</p>

        </div>

    </div>

</div>

<?$Includes::Template(
    'main:catalog.offers.list',
    'carousel',
    array(
        'select' => array(
            'name',
            'model',
            'code',
            'offer_name',
            'offer_code',
            'offer_available',
            'picture',
            'offer_price',
            'offer_discount',
            'offer_discount_price',
            'offer_discount_type'
        ),
        'filter' => array(
            'iblock_id' => 29,
            'active' => 1,
            'offer_active' => 1,
            '>offer_discount' => 0.01
        ),
        'order' => array(
            'sort' => 'asc',
            'offer_sort' => 'asc'
        )
    )
);

$Includes::Template(
    'main:content.form',
    'order-measurement',
    array(
        'filter' => array(
            'iblock_id' => 26
        )
    )
);?>

<div class="block-yellow">

    <div class="container">
        <div class="main-about">

            <div class="main-about-picture">

                <img src="<?=TPL?>/images/about-door-1.jpg">
                <img src="<?=TPL?>/images/about-door-2.jpg">
                <img src="<?=TPL?>/images/about-door-3.jpg">

            </div>

            <div class="main-about-content">

                <div class="main-about-title">
                    <?$Includes::IncludeArea(
                        array(
                            'path' => '/includes/area/main-about-title.php'
                        )
                    )?>
                </div>

                <div class="main-about-text">
                    <?$Includes::IncludeArea(
                        array(
                            'path' => '/includes/area/main-about-text.php'
                        )
                    )?>
                </div>

            </div>

        </div>
    </div>

    <?$Includes::Template(
        'main:content.list',
        'advantages',
        array(
            'filter' => array(
                'iblock_id' => 23,
                'active' => 1
            ),
            'order' => array(
                'sort' => 'asc',
                'offer_sort' => 'asc'
            )
        )
    )?>

</div>

<?require $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php';