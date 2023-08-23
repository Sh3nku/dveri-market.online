<?php

if ( $arResult['items'] ) {?>

    <div class="swiper slider" id="MainSlider">
        <div class="swiper-wrapper">

            <?foreach ( $arResult['items'] as $key => $arItem ) {

                $is_picture = false;
                if ( empty( $arItem['bind'] ) ) $is_picture = true;

                if ( $is_picture === false ) {

                    $arResItem = $Content -> GetListWithOffers(
                        array(
                            'name',
                            'code',
                            'offer_name',
                            'offer_code',
                            'offer_picture',
                            'offer_price',
                            'offer_discount_price'
                        ),
                        array(
                            'iblock_id' => 19,
                            'id' => $arItem['bind']
                        )
                    );

                    if ( empty( $arResItem['items'] ) ) continue;

                    $arProduct = $arResItem['items'][0];
                    $arOffer = $arResItem['items'][0]['offers'][0];

                    $arSections = array();

                    if ( $arProduct['sections'] ) {

                        for ( $ii = 0; $ii < count( $arProduct['sections'] ); $ii++ ) {

                            $arSection = $arProduct['sections'][$ii];

                            $arSections[$arSection['id']] = array(
                                'id' => $arSection['id'],
                                'name' => $arSection['name'],
                                'code' => $arSection['code'],
                                'parent_id' => $arSection['parent_id']
                            );

                        }

                    }

                    $arSectionTree = $Functions -> BuildTree( $arSections );

                    $url = '/katalog/' . $arSectionTree[0]['code'] . '/' . ( ( $arSectionTree[0]['children'][0]['code'] ) ? $arSectionTree[0]['children'][0]['code'] . '/' : '' ) . $arProduct['code'] . '/?offer_code=' . $arOffer['code'];

                } else {
                    $url = $arItem['link'];
                }?>

                <div class="swiper-slide">
                    <a href="<?=$url?>"<?=( ( str_contains( $url, 'http' ) ) ? ' target="_blank"' : '' )?>>
                        <div class="slide<?=( ( $is_picture === true ) ? ' is-picture' : '' )?>"<?=( ( $is_picture === true ) ? ' style="background-image:url(' . $arItem['picture']['path'] . ')"' : '' )?>>

                            <?if ( $is_picture === false ) {?>

                                <div class="slide-content">
                                    <div class="slide-name"><?=$arProduct['name']?></div>
                                    <div class="slide-text"><?=$arItem['text']?></div>
                                    <div class="slide-prices">
                                        <?=( $arOffer['discount_price'] != $arOffer['price'] ) ? '<div class="slide-price-old">' . $arOffer['price'] . ' <span class="rub">Р</span></div>' : '' ?>
                                        <div class="slide-price"><?=$arOffer['discount_price']?> <span class="rub">Р</span></div>
                                    </div>
                                </div>

                                <div class="slide-picture">
                                    <img src="<?=$arOffer['picture']['path']?>" alt="<?=$arProduct['name'] . ' ' . $arProduct['offer_name']?>">
                                </div>

                            <?}?>

                        </div>
                    </a>
                </div>

            <?}?>

        </div>

        <div class="swiper-button-next js-slider-next"></div>
        <div class="swiper-button-prev js-slider-prev"></div>

    </div>

    <script>
        new Swiper(
            '#MainSlider', {
                navigation: {
                    nextEl: '.js-slider-next',
                    prevEl: '.js-slider-prev',
                },
                autoplay: {
                    delay: 3000,
                },
                loop: true
            }
        );
    </script>

<?}