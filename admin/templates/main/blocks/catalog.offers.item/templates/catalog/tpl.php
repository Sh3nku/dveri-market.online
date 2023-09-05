<?
global $MAIN;
global $BUFFER;

if ( !empty( $arResult['items'] ) ) {

    $arItem = $arResult['items'][0];
    $arOffer = $arItem['offers'][0];
    $arAccessories =  $arOffer['accessories'];

    $arSeo = \Iblock\Seo::GetList(
        array(
            'filter' => array(
                'iblock_id' => 29,
                'type' => 'E',
                'element_id' => $arItem['id']
            )
        )
    )['items'][0];

    //if ( $_SESSION['user']['id'] == 1 ) $Functions -> Pre( $arResult['items'] );

    $title = '';
    $description = '';
    $page_title = '';

    if ( !empty( $arSeo['title'] ) ) $title = $arSeo['title'];
    if ( !empty( $arSeo['description'] ) ) $description = $arSeo['description'];
    if ( !empty( $arSeo['page_title'] ) ) $page_title = $arSeo['page_title'];

    if ( preg_match( '/mezhkomnatnye-dveri/', $_SERVER['REQUEST_URI'] ) ) {
        if ( empty( $title ) ) $title = 'Межкомнатная дверь ' . $arItem['name'] . ' ' . ( !empty( $arItem['model'] ) ? ' ' . $arItem['model'] : '' ) . ' купить в Санкт-Петербурге | «Двери Маркет»';
        if ( empty( $description ) ) $description = 'Интернет-магазин «Двери Маркет» предлагает купить дверь ' . $arItem['name'] . ' ' . ( !empty( $arItem['model'] ) ? ' ' . $arItem['model'] : '' ) . '. Доставка и установка. ☎ +7 (981) 718-01-08';
    } else {
        if ( empty( $title ) ) $title = $arItem['name'] . ( !empty( $arItem['model'] ) ? ' ' . $arItem['model'] : '' ) . ' купить в Санкт-Петербурге | Интернет-магазин «Двери Маркет»';
        if ( empty( $description ) ) $description = $arItem['name'] . ( !empty( $arItem['model'] ) ? ' ' . $arItem['model'] : '' ) . ' ✏ Широкий выбор ✏ Доступные цены! ☎ +7 (981) 718-01-08 Заказывайте в «Двери Маркет»! ➠';
    }

    $MAIN -> SetTitle( $title );
    $BUFFER -> SetBuffer( 'description', $description );
    $BUFFER -> SetBuffer( 'canonical', '/katalog' . $arItem['detail_page_url'] );

    $arGroup = $Content -> GetList(
        array( 'bind' ),
        array(
            'iblock_id' => 31,
            'bind' => $arItem['id']
        )
    );

    $arBindIds = array();

    foreach ( $arGroup['items'][0]['bind'] as $arBind ) {
        $arBindIds[] = $arBind['value'];
    }

    $arGroupItems = array();

    if ( !empty( $arBindIds ) ) {

        $arGroupItems = $Content -> GetList(
            array( 'code', 'sample' ),
            array(
                'iblock_id' => 29,
                'id' => $arBindIds
            )
        );

    }?>

    <div itemscope itemtype="https://schema.org/Product">

    <div class="product-wrapper">

        <div class="product-picture<?=( ( !empty( $arItem['picture_2'] ) ) ? ' _double' : '' )?>">

            <div class="product-available <?=$arOffer['available']['code']?>"><?=$arOffer['available']['name']?></div>

            <img class="js-picture" src="<?=$arItem['picture']['path']?>" itemprop="image"><?

            if ( !empty( $arItem['picture_2'] ) ) {?><img class="js-picture-2" src="<?=$arItem['picture_2']['path']?>"><?}?>

        </div>

        <div class="product">

            <h1 itemprop="name"><?=( ( !empty( $page_title ) ) ? $page_title : $arItem['name'] )?></h1>

            <div class="product-offer-name js-name"><?=$arItem['model']?></div>

            <form id="choiceOffer">

                <input type="hidden" name="code" value="<?=$arItem['code']?>">
                <input type="hidden" name="offer" value="<?=$arOffer['id']?>">
                <input type="hidden" name="offer_code" value="<?=$arOffer['code']?>">

                <?if ( !empty( $arGroupItems ) ) {?>

                    <div class="product-property">

                        <div class="product-property-name">
                            Цвет
                        </div>

                        <div class="product-property-values">

                            <?foreach ( $arGroupItems['items'] as $key_val => $arGroupItem ) {

                                if ( $arGroupItem['sections'] ) {

                                    for ( $ii = 0; $ii < count( $arGroupItem['sections'] ); $ii++ ) {

                                        $arSection = $arGroupItem['sections'][$ii];

                                        $arSections[$arSection['id']] = array(
                                            'id' => $arSection['id'],
                                            'name' => $arSection['name'],
                                            'code' => $arSection['code'],
                                            'parent_id' => $arSection['parent_id']
                                        );

                                    }

                                }

                                $arSectionTree = $Functions -> BuildTree( $arSections );

                                $url = '/katalog/' . $arSectionTree[0]['code'] . '/' . ( ( $arSectionTree[0]['children'][0]['code'] ) ? $arSectionTree[0]['children'][0]['code'] . '/' : '' ) . $arGroupItem['code'] . '/';

                                $selected = $arGroupItem['id'] == $arItem['id'];?>

                                <div class="product-property-value">

                                    <?if ( $selected !== true ) {?>
                                        <a href="<?=$url?>">
                                            <img src="<?=$arGroupItem['sample']['path']?>">
                                        </a>
                                    <?} else {?>
                                        <div class="product-property-color-selected">
                                            <img src="<?=$arGroupItem['sample']['path']?>">
                                        </div>
                                    <?}?>
                                </div>

                            <?}?>

                        </div>

                    </div>

                <?}?>

                <?$arOffersProperties = $Content -> GetOffersProperties(
                    array(),
                    array(
                        'iblock_id' => 29,
                        'id' => $arItem['id']
                    )
                );

                foreach ( $arOffersProperties['items'] as $key => $arProperty ) {?>

                    <div class="product-property">

                        <div class="product-property-name">
                            <?=$arProperty['name']?>
                        </div>

                        <div class="product-property-values" data-property="<?=$key?>">

                            <?$count = 0;

                            foreach ( $arProperty['values'] as $key_val => $arValue ) {?>

                                <div class="product-property-value">

                                    <input class="product-property-radio" type="radio" name="<?=$arProperty['code']?>" id="prop_<?=$arProperty['code'] . '_' . $arValue['code']?>" value="<?=$arValue['code']?>"<?=( ( empty( $count ) ) ? ' checked' : '' )?>>
                                    <label for="prop_<?=$arProperty['code'] . '_' . $arValue['code']?>"><?=$arValue['name']?></label>

                                </div>

                                <?$count++;

                            }?>

                        </div>

                    </div>

                <?}?>

                <div class="product-price-wrapper" itemprop="offers" itemscope itemtype="https://schema.org/Offer">

                    <div class="product-price-item">

                        <div class="product-price-title">За полотно</div>

                        <meta itemprop="price" content="<?=$arOffer['discount_price']?>">
                        <meta itemprop="priceCurrency" content="RUB">
                        <link itemprop="availability" href="http://schema.org/InStock">
                        <div class="product-price"><span class="js-price"><?=number_format( $arOffer['discount_price'], 0, '', ' ' )?></span> <span class="rub">Р</span></div>

                        <?if ( !empty( $arOffer['discount'] ) ) {

                            //$Functions -> Pre( $arOffer );?>

                            <div class="product-price-base-wrapper">

                                <div class="product-price-base"><span class="js-price-old-price"><?=number_format( $arOffer['price'], 0, '', ' ' )?></span> <span class="rub">Р</span></div>

                                <div class="product-price-discount">-<?=$arOffer['discount'] . ( ( $arOffer['discount_type'] == 1 ) ? '<span class="rub rub-16">Р</span>' : '%' )?></div>

                            </div>

                        <?}?>

                    </div>

                </div>

                <div class="wrapper-product-button">
                    <div class="button product-button-in-basket js-in-basket">В корзину</div>

                    <div class="product-measurement-button">
                        <span class="button _black | js-order-measurement">Заказать замер</span>
                    </div>
                </div>

                <?
                //$Functions -> Pre( $arOffersProperties );
                //$Functions -> Pre( $arOffer );
                ?>

            </form>

        </div>

    </div>

    <div class="product-tabs">

        <div class="product-tabs-buttons">

            <?if ( $arAccessories ) {?>

                <input type="radio" name="tab" id="accessories" checked>
                <label class="product-tabs-label" for="accessories">Комплектующие</label>

            <?}?>

            <input type="radio" name="tab" id="description"<?=( ( $arItem['accessories'] ) ? ' checked' : '' )?>>
            <label class="product-tabs-label" for="description">Описание</label>

        </div>

        <div class="product-tabs-sections">

            <?if ( $arAccessories ) {?>

                <section class="js-accessories-block" data-tab="accessories">

                    <?
                    $ids = array();

                    foreach ( $arAccessories as $key => $arAccessories ) {

                        $ids[] = $arAccessories['value'];

                    }

                    $arAccessories = $Content -> GetList(
                        array(
                            'name',
                            'picture',
                            'available',
                            'price',
                            'discount',
                            'discount_price'
                        ),
                        array(
                            'iblock_id' => 21,
                            'id' => $ids,
                            'active' => 1
                        ),
                        array(),
                        array(
                            'sort' => 'asc',
                            'id' => 'desc'
                        )
                    );

                    //$Functions -> Pre( $arAccessories );

                    if ( $arAccessories['items'] ) {

                        foreach ( $arAccessories['items'] as $key => $accessories ) {

                            if ( empty( $accessories['discount_price'] ) ) continue?>

                            <div class="accessories-item">

                                <div class="accessories-name"><?=$accessories['name']?></div>

                                <div class="accessories-available"><?=$accessories['available']['name']?></div>

                                <div class="accessories-picture"><img src="<?=$accessories['picture']['path']?>" alt="<?=$accessories['name']?>"></div>

                                <div class="accessories-price">

                                    <?if ( !$accessories['discount'] ) {?>
                                        <div class="accessories-price-base"><?=number_format( $accessories['discount_price'], 0, '', ' ' )?> <span class="rub rub-16">Р</span></div>
                                    <?} else {?>

                                        <div class="accessories-price-base accessories-price-old"><?=number_format( $accessories['price'], 0, '', ' ' )?> <span class="rub rub-16">Р</span></div>
                                        <div class="accessories-price-discount"><?=number_format( $accessories['discount_price'], 0, '', ' ' )?> <span class="rub rub-16">Р</span></div>

                                    <?}?>

                                </div>

                                <div class="accessories-basket">

                                    <svg class="js-in-basket accessories-in-basket" data-type="accessories" data-item="<?=$accessories['id']?>" xmlns="http://www.w3.org/2000/svg" width="40.073" height="39" viewBox="0 0 40.073 39"><g transform="translate(0 -6.028)"><g transform="translate(0 6.028)"><g transform="translate(0 0)"><path d="M100.837,350.322a4.184,4.184,0,1,0,4.183,4.184A4.183,4.183,0,0,0,100.837,350.322Z" transform="translate(-88.054 -319.689)"/><path d="M299.368,350.322a4.184,4.184,0,1,0,4.184,4.184A4.183,4.183,0,0,0,299.368,350.322Z" transform="translate(-268.921 -319.689)"/><path d="M39.883,12.257a1.162,1.162,0,0,0-.744-.372L8.878,11.467,8.042,8.91a4.277,4.277,0,0,0-4-2.882H.93a.93.93,0,0,0,0,1.859H4.044A2.417,2.417,0,0,1,6.275,9.514l5.9,17.8-.465,1.069a4.462,4.462,0,0,0,.418,4.044,4.323,4.323,0,0,0,3.486,1.952H33.7a.93.93,0,1,0,0-1.859H15.619a2.371,2.371,0,0,1-1.952-1.116,2.556,2.556,0,0,1-.232-2.231l.372-.837,19.57-2.045a5.113,5.113,0,0,0,4.416-3.9l2.231-9.343A.79.79,0,0,0,39.883,12.257Z" transform="translate(0 -6.028)"/></g></g></g></svg>

                                </div>

                            </div>

                        <?}

                    }?>

                </section>

            <?}?>

            <section class="product-tabs-sections-content" data-tab="description" itemprop="description">
                <?=$arItem['description']?>
            </section>

        </div>

    </div>

    </div>

<?}

//$Functions -> Pre( $arResult['items'] );

/*if ( $arResult['items'] ) {

    $arItem = $arResult['items'][0];

    $arAvailability = array();
    $arAccessories = array();
    $count_offer = 0;
    $uniq_offer_code = '';

    foreach ( $arItem['offers'] as $k => $arOffer ) {

        if ( $uniq_offer_code && $uniq_offer_code != $arOffer['code'] ) continue;

        if ( isset( $arOffer['accessories'] ) ) $arAccessories = array_merge( $arAccessories, $arOffer['accessories'] );

        if ( $k == 0 ) $uniq_offer_code = $arOffer['code'];

        foreach ( $arOffer as $key => $arProp ) {

            if ( $key != 'size' ) continue;

            //$Functions -> Pre( $arProp[$i]['code'] );

            for ( $i = 0; $i < count( $arProp ); $i++ ) {

                if ( $arProp[$i]['code'] == $_GET['size'] ) $count_offer = $k;
                if ( is_array( $arAvailability[$key] ) && !in_array( $arProp[$i]['code'], $arAvailability[$key] ) ) $arAvailability[$key][] = $arProp[$i]['code'];

            }

        }

    }

    $arSample = $Content -> GetItemWithOffers(
        array(
            'offer_code',
            'offer_sample'
        ),
        array(
            'iblock_id' => 19,
            'code' => $arItem['code'],
            'offer_active' => 1
        )
    );

    $arOffersPropertiesSample = array();
    $check_double = array();

    //$Functions -> Pre( $arSample['items'][0] );

    foreach ( $arSample['items'][0]['offers'] as $key => $arOffer ) {

        if ( empty( $arOffer['sample'] ) || ( !empty( $arOffer['code'] ) && in_array( $arOffer['code'], $check_double ) ) ) continue;

        $arOffersPropertiesSample['sample']['name'] = 'Цвет';
        $arOffersPropertiesSample['sample']['code'] = 'sample';

        $arOffersPropertiesSample['sample']['values'][$arOffer['sample']['id']]['code'] = $arOffer['sample']['id'];
        $arOffersPropertiesSample['sample']['values'][$arOffer['sample']['id']]['path'] = $arOffer['sample']['path'];

        $check_double[] = $arOffer['code'];

    }

    $arOffer = $arItem['offers'][$count_offer];

    $title = '';
    $description = '';

    if ( preg_match( '/mezhkomnatnye-dveri/', $_SERVER['REQUEST_URI'] ) ) {
        $title = 'Межкомнатная дверь ' . $arItem['name'] . ' ' . $arOffer['name'] . ' купить в Санкт-Петербурге | «Двери Маркет»';
        $description = 'Интернет-магазин «Двери Маркет» предлагает купить дверь ' . $arItem['name'] . ' ' . $arOffer['name'] . '. Доставка и установка. ☎ +7 (981) 718-01-08';
    } else {
        $title = $arItem['name'] . ' купить в Санкт-Петербурге | Интернет-магазин «Двери Маркет»';
        $description = $arItem['name'] . ' ✏ Широкий выбор ✏ Доступные цены! ☎ +7 (981) 718-01-08 Заказывайте в «Двери Маркет»! ➠';
    }

    $MAIN -> SetTitle( $title );
    $BUFFER -> SetBuffer( 'description', $description );

    //$Functions -> Pre( $arOffer );?>

    <div class="product-wrapper">

        <div class="product-picture<?=( ( !empty( $arOffer['picture_2'] ) ) ? ' _double' : '' )?>">

            <div class="product-available <?=$arOffer['available']['code']?>"><?=$arOffer['available']['name']?></div>

            <img class="js-picture" src="<?=$arOffer['picture']['path']?>"><?

            if ( !empty( $arOffer['picture_2'] ) ) {?><img class="js-picture-2" src="<?=$arOffer['picture_2']['path']?>"><?}?>

        </div>

        <div class="product">

            <h1><?=$arItem['name']?></h1>

            <div class="product-offer-name js-name"><?=$arOffer['name']?></div>

            <form id="choiceOffer">

                <input type="hidden" name="code" value="<?=$arItem['code']?>">
                <input type="hidden" name="offer" value="<?=$arOffer['id']?>">
                <input type="hidden" name="offer_code" value="<?=$arOffer['code']?>">

                <?
                $arOffersProperties = $Content -> GetOffersProperties(
                    array(),
                    array(
                        'iblock_id' => 19,
                        'id' => $arItem['id']
                    )
                );

                $arOffersProperties['items'] = array_merge(
                    $arOffersPropertiesSample,
                    $arOffersProperties['items']
                );

                foreach ( $arOffersProperties['items'] as $key => $arProperty ) {?>

                    <div class="product-property">

                        <div class="product-property-name">
                            <?=$arProperty['name']?>
                        </div>

                        <div class="product-property-values" data-property="<?=$key?>">

                            <?foreach ( $arProperty['values'] as $key_val => $arValue ) {

                                if ( is_array( $arAvailability[$key] ) && !in_array( $arValue['code'], $arAvailability[$key] ) ) continue;

                                if ( $key == 'sample' ) {
                                    $name = '<img src="' . $arValue['path'] . '">';
                                } else {
                                    $name = $arValue['name'];
                                }

                                $selected = false;

                                if ( $_GET[$arProperty['code']] == $arValue['code'] ) {
                                    $selected = true;
                                } else if (
                                    ( $arOffer[$arProperty['code']][0]['code'] == $arValue['code'] )
                                    || ( $arProperty['code'] == 'sample' && $arOffer[$arProperty['code']]['id'] == $arValue['code'] )
                                ) {
                                    $selected = true;
                                }?>

                                <div class="product-property-value">

                                    <input class="product-property-radio<?=( ( $key == 'sample' ) ? ' circle' : '' )?>" type="radio" name="<?=$arProperty['code']?>" id="prop_<?=$arProperty['code'] . '_' . $arValue['code']?>" value="<?=$arValue['code']?>"<?=( ( $selected ) ? ' checked' : '' )?>>
                                    <label for="prop_<?=$arProperty['code'] . '_' . $arValue['code']?>"><?=$name?></label>

                                </div>

                            <?}?>

                        </div>

                    </div>

                <?}?>

                <div class="product-price-wrapper">

                    <div class="product-price-item">

                        <div class="product-price-title">За полотно</div>

                        <div class="product-price"><span class="js-price"><?=number_format( $arOffer['discount_price'], 0, '', ' ' )?></span> <span class="rub">Р</span></div>

                        <?if ( !empty( $arOffer['discount'] ) ) {?>

                            <div class="product-price-base-wrapper">

                                <div class="product-price-base"><span class="js-price-old-price"><?=number_format( $arOffer['price'], 0, '', ' ' )?></span> <span class="rub">Р</span></div>

                                <div class="product-price-discount"><?=$arOffer['discount']?>%</div>

                            </div>

                        <?}?>

                    </div>

                </div>

                <div class="button product-button-in-basket js-in-basket">В корзину</div>

                <?
                //$Functions -> Pre( $arOffersProperties );
                //$Functions -> Pre( $arOffer );

                ?>

            </form>

        </div>

    </div>

    <div class="product-tabs">

        <div class="product-tabs-buttons">

            <?if ( $arAccessories ) {?>

                <input type="radio" name="tab" id="accessories" checked>
                <label class="product-tabs-label" for="accessories">Комплектующие</label>

            <?}?>

            <input type="radio" name="tab" id="description"<?=( ( $arItem['accessories'] ) ? ' checked' : '' )?>>
            <label class="product-tabs-label" for="description">Описание</label>

        </div>

        <div class="product-tabs-sections">

            <?if ( $arAccessories ) {?>

            <section class="js-accessories-block" data-tab="accessories">

                <?
                $ids = array();

                    foreach ( $arAccessories as $key => $arAccessories ) {

                        $ids[] = $arAccessories['value'];

                    }

                    $arAccessories = $Content -> GetList(
                        array(
                            'name',
                            'picture',
                            'available',
                            'price',
                            'discount',
                            'discount_price'
                        ),
                        array(
                            'iblock_id' => 21,
                            'id' => $ids,
                            'active' => 1
                        ),
                        array(),
                        array(
                            'sort' => 'asc',
                            'id' => 'desc'
                        )
                    );

                    //$Functions -> Pre( $arAccessories );

                    if ( $arAccessories['items'] ) {

                        foreach ( $arAccessories['items'] as $key => $accessories ) {

                            if ( empty( $accessories['discount_price'] ) ) continue?>

                            <div class="accessories-item">

                                <div class="accessories-name"><?=$accessories['name']?></div>

                                <div class="accessories-available"><?=$accessories['available']['name']?></div>

                                <div class="accessories-picture"><img src="<?=$accessories['picture']['path']?>" alt="<?=$accessories['name']?>"></div>

                                <div class="accessories-price">

                                    <?if ( !$accessories['discount'] ) {?>
                                        <div class="accessories-price-base"><?=number_format( $accessories['discount_price'], 0, '', ' ' )?> <span class="rub rub-16">Р</span></div>
                                    <?} else {?>

                                        <div class="accessories-price-base accessories-price-old"><?=number_format( $accessories['price'], 0, '', ' ' )?> <span class="rub rub-16">Р</span></div>
                                        <div class="accessories-price-discount"><?=number_format( $accessories['discount_price'], 0, '', ' ' )?> <span class="rub rub-16">Р</span></div>

                                    <?}?>

                                </div>

                                <div class="accessories-basket">

                                    <svg class="js-in-basket accessories-in-basket" data-type="accessories" data-item="<?=$accessories['id']?>" xmlns="http://www.w3.org/2000/svg" width="40.073" height="39" viewBox="0 0 40.073 39"><g transform="translate(0 -6.028)"><g transform="translate(0 6.028)"><g transform="translate(0 0)"><path d="M100.837,350.322a4.184,4.184,0,1,0,4.183,4.184A4.183,4.183,0,0,0,100.837,350.322Z" transform="translate(-88.054 -319.689)"/><path d="M299.368,350.322a4.184,4.184,0,1,0,4.184,4.184A4.183,4.183,0,0,0,299.368,350.322Z" transform="translate(-268.921 -319.689)"/><path d="M39.883,12.257a1.162,1.162,0,0,0-.744-.372L8.878,11.467,8.042,8.91a4.277,4.277,0,0,0-4-2.882H.93a.93.93,0,0,0,0,1.859H4.044A2.417,2.417,0,0,1,6.275,9.514l5.9,17.8-.465,1.069a4.462,4.462,0,0,0,.418,4.044,4.323,4.323,0,0,0,3.486,1.952H33.7a.93.93,0,1,0,0-1.859H15.619a2.371,2.371,0,0,1-1.952-1.116,2.556,2.556,0,0,1-.232-2.231l.372-.837,19.57-2.045a5.113,5.113,0,0,0,4.416-3.9l2.231-9.343A.79.79,0,0,0,39.883,12.257Z" transform="translate(0 -6.028)"/></g></g></g></svg>

                                </div>

                            </div>

                        <?}

                    }?>

            </section>

            <?}?>

            <section class="product-tabs-sections-content" data-tab="description">
                <?=$arItem['description']?>
            </section>

        </div>

    </div>

<?}*/