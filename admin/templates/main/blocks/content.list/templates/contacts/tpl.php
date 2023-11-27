<?php

if ( $arResult['items'] ) {

    $arItem = $arResult['items'][0];

    $addressString = [];

    if ( !empty( $arItem['country'] ) ) $addressString[] = '<span itemprop="addressCountry">' . $arItem['country'] . '</span>';
    if ( !empty( $arItem['locality'] ) ) $addressString[] = '<span itemprop="addressLocality">' . $arItem['locality'] . '</span>';
    if ( !empty( $arItem['postal'] ) ) $addressString[] = '<span itemprop="postalCode">' . $arItem['postal'] . '</span>';
    if ( !empty( $arItem['street'] ) ) $addressString[] = '<span itemprop="streetAddress">' . $arItem['street'] . '</span>';
    if ( !empty( $arItem['location'] ) ) $addressString[] = '<span>' . $arItem['location'] . '</span>';
    ?>

    <div class="contacts" itemscope itemtype="http://schema.org/Organization">
        <p class="contacts-text" itemprop="name">Двери-маркет</p>
        <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
            <div class="contacts-title">Адрес</div>

            <div class="contacts-text">
                <?=implode( ', ', $addressString )?>
            </div>

            <div class="contacts-title">Телефоны</div>

            <div class="contacts-text">
                <?foreach ( $arItem['phones'] as $arPhone ) {?>
                    <p><a class="_underline" href="tel:<?=$Functions -> PhoneClear( $arPhone['value'] )?>" itemprop="telephone"><?=$arPhone['value']?></a></p>
                <?}?>
            </div>

            <div class="contacts-title">Email</div>

            <div class="contacts-text">
                <a class="_underline" href="mailto:<?=$arItem['emails']?>" itemprop="email"><?=$arItem['emails']?></a>
            </div>
        </div>

        <div class="contacts-title">График работы</div>

        <div class="contacts-text">
            <?foreach ( $arItem['work_time'] as $arWorkTime ) {?>
                <p><?=$arWorkTime['value']?></p>
            <?}?>
        </div>
    </div>

    <div class="contacts-map" id="contactsMap"></div>

    <script>

        ymaps.ready( function () {

            let myMap = new ymaps.Map('contactsMap', {
                center: [<?=$arItem['coords']?>],
                zoom: 16,
                controls: ['zoomControl', 'typeSelector',  'fullscreenControl']
            }, {
                searchControlProvider: 'yandex#search'
            });

            let myPlacemark = new ymaps.Placemark( [<?=$arItem['coords']?>], {
                balloonContent: '<b>Двери Маркет</b><br /><?=$arItem['address']?><br /><?=$arItem['phones'][0]['value']?>'
            },{
                iconLayout: 'default#image',
                iconImageHref: '/admin/templates/main/images/placemark.svg',
                iconImageSize: [48, 64],
                iconImageOffset: [-24, -64]
            });

            myMap.geoObjects.add( myPlacemark );

        });



    </script>

<?}

//$Functions -> Pre( $arResult );