<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/core/core.php';

$classFiles = new Files();

function highLightStr ( $text, $str ) {

    if ( !$str ) return $text;

    setlocale( LC_ALL, 'ru_RU.UTF-8' );
    return preg_replace( '/(' . preg_quote( $str ) . ')/ui', '<i class="highlight_str">$1</i>', $text );

}

if ( isset( $_POST['getAuthForm'] ) ) {

    $Includes::Template(
        'main:user.auth',
        'auth',
        array()
    );

}

if ( isset( $_POST['gelCallForm'] ) ) {

    $Includes::Template(
        'main:content.form',
        'order-call',
        array(
            'filter' => array(
                'iblock_id' => 22
            )
        )
    );

}

if ( isset( $_POST['getMeasurementForm'] ) ) {

    $Includes::Template(
        'main:content.form',
        'order-measurement',
        array(
            'filter' => array(
                'iblock_id' => 26
            )
        )
    );

}

if ( isset( $_POST['gelRecoverForm'] ) ) {

    $Includes::Template(
        'main:user.password.forgot',
        'forgot',
        array()
    );

}

if ( isset( $_POST['getRegistrationForm'] ) ) {

    $Includes::Template(
        'main:user.registration',
        'registration',
        array()
    );

}

if ( isset( $_POST['InBasket'] ) ) {

    $arResult = $Functions -> SerializeArray( $_POST['InBasket'] );

    $Shop = new Shop();

    if ( $arResult['type'] == 'accessories' ) {

        $Shop -> AddBasket(
            array(
                'iblock_id' => 21,
                'id' => $arResult['item']
            )
        );

    } else {

        $iblock_id = 29;
        $iblock_offers_id = 30;

        if ( $arResult['type'] === 'furnitura' ) {
            $iblock_id = 27;
            $iblock_offers_id = 28;
        }

        $arProduct = $Content -> GetItemWithOffers(
            array(
                'id'
            ),
            array(
                'iblock_id' => $iblock_id,
                'code' => $arResult['code'],
                'offer_id' => $arResult['offer']
            )
        );

        $arProperties = array();

        foreach ( $arResult as $key => $value ) {

            if (
                $key == 'code'
                || $key == 'offer'
                || $key == 'sample'
                || $key == 'offer_code'
                || !$value
            ) continue;

            $arResProperty = $mysql -> queryList('
                SELECT
                    t1.`id`,
                    t2.`id` value_id
                FROM `iblock_properties` t1
                LEFT JOIN `iblock_values` t2 ON t2.`property_id` = t1.`id`
                WHERE t1.`iblock_id` = ?i AND t1.`code` = ?s AND t2.`code` = ?s
            ',
                $iblock_offers_id,
                $key,
                $value
            );

            if ( !empty( $arResProperty[0]['id'] ) ) {
                $arProperties[] = array(
                    'property_id' => $arResProperty[0]['id'],
                    'value_id' => $arResProperty[0]['value_id']
                );
            }

        }

        $Shop -> AddBasket(
            array(
                'iblock_id' => $iblock_id,
                'id' => $arProduct['items'][0]['id'],
                'offer_id' => $arResult['offer'],
                'properties' => $arProperties
            )
        );

    }

    $basket_count = $Shop -> GetBasketCount(
        array('sum'),
        array(
            'cookie' => $_COOKIE['basket']
        ),
    );

    echo json_encode( $basket_count, JSON_UNESCAPED_UNICODE );

}

if ( isset( $_POST['OpenBasket'] ) ) {

    $Includes::Template(
        'main:basket.list',
        'basket',
        array(
            'select' => array(
                29 => array( 'name', 'model', 'picture' ),
                30 => array( 'name' ),
                21 => array( 'name', 'picture' ),
                27 => array( 'name' ),
                28 => array( 'name', 'picture' )
            ),
            'filter' => array(
                'cookie' => $_COOKIE['basket']
            ),
            array()
        )
    );

}

if ( isset( $_POST['logout'] ) ) {

    unset( $_SESSION['user'] );

}

if ( isset( $_POST['search_string'] ) ) {

    $string_search = htmlspecialchars( $_POST['search_string'] );

    $arSections = $Content -> GetSectionList(
        [],
        [
            'iblock_id' => 29,
            'active' => 1,
            '%name' => $string_search
        ],
        [
            'limit' => 5
        ]
    );

    if ( $is_section = !empty( $arSections['items'] ) ) {?>

        <div class="search-popup__content-sections">
            <? foreach ( $arSections['items'] as $arSection ) {

                //$Functions -> Pre( $arSection );

                $arUrl = $mysql -> query('
                    SELECT
                        t1.`id`, t1.`name`, t1.`code`,
                        t2.`id` sect_2_id, t2.`name` sect_2_name, t2.`code` sect_2_code,
                        t3.`id` sect_3_id, t3.`name` sect_3_name, t3.`code` sect_3_code
                    FROM `sections` t1
                    LEFT JOIN `sections` t2 ON t2.`id` = t1.`parent_id`
                    LEFT JOIN `sections` t3 ON t3.`id` = t2.`parent_id`
                    WHERE t1.`id` = ?i
                ',
                    $arSection['id']
                );

                $url = '/';

                while ( $row = mysqli_fetch_assoc( $arUrl ) ) {

                    foreach ( $row as $key => $value ) {
                        if ( empty( $value ) ) continue;
                        if ( str_contains( $key, 'code' ) ) {
                            $url = '/' . $value . $url;
                        }
                    }

                }

                //$Functions -> Pre($url);?>

                <div class="search-popup__content-section">
                    <a href="/katalog<?=$url?>"><?=highLightStr( $arSection['name'], $string_search )?></a>
                </div>
            <?}?>
        </div>

    <?}

    $arItems = $Content -> GetListWithOffers(
        [],
        [
            'iblock_id' => 29,
            'active' => 1,
            '%name' => $string_search
        ],
        [
            'count_on_page' => 8
        ]
    );

    if ( $is_items = !empty( $arItems['items'] ) ) {

        //$Functions -> Pre( $arItems['items'] );?>

        <div class="search-popup__content-list">
            <div class="row">
                <?foreach ( $arItems['items'] as $key => $arItem ) {

                    $arOffer = $arItem['offers'][0];

                    $arSections = array();

                    if ( $arItem['sections'] ) {

                        for ( $ii = 0; $ii < count( $arItem['sections'] ); $ii++ ) {

                            $arSection = $arItem['sections'][$ii];

                            $arSections[$arSection['id']] = array(
                                'id' => $arSection['id'],
                                'name' => $arSection['name'],
                                'code' => $arSection['code'],
                                'parent_id' => $arSection['parent_id']
                            );

                        }

                    }

                    $arSectionTree = $Functions -> BuildTree( $arSections );

                    $url = '/katalog/' . $arSectionTree[0]['code'] . '/' . ( ( $arSectionTree[0]['children'][0]['code'] ) ? $arSectionTree[0]['children'][0]['code'] . '/' : '' );?>

                    <div class="col-l-3 col-6">
                        <a class="search-popup__item-link" href="<?=$url . $arItem['code']?>/">
                            <div class="search-popup__item">
                                <?if ( !empty( $arItem['picture'] ) ) {?>
                                    <div class="search-popup__item-picture">
                                        <?$picture = $classFiles -> Resize( $arItem['picture']['path'], 51, 115, false, 'search_list_' )?>
                                        <img src="<?=$picture?>">
                                    </div>
                                <?}?>

                                <div class="search-popup__item-content">
                                    <div class="search-popup__item-title"><?=highLightStr( $arItem['name'], $string_search )?></div>
                                    <div class="search-popup__item-model"><?=$arItem['model']?></div>
                                    <div class="search-popup__item-price"><?=number_format( $arOffer['discount_price'], 0, '', ' ' )?> <span class="rub">Р</span></div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?}?>
            </div>
        </div>

    <?}

    if ( $is_section || $is_items ) {?>
        <div class="search-popup__content-footer">
            <a href="/search/?q=<?=$string_search?>">Посмотреть все результаты</a>
        </div>
    <?}

}