<?php

if ( $arResult['items'] ) {

    $arItems = $arResult['items'];

    $arResFurnitura = $Content -> GetSectionList(
        array(),
        array(
            'iblock_id' => 27,
            'active' => 1
        ),
        array(),
        array(
            'sort' => 'asc'
        ),
        array(
            'reset_keys' => 'N'
        )
    );

    $arFurnitura = array();

    foreach ( $arResFurnitura['items'] as $arItem ) {

        $arItem['parent_id'] = 9999;

        $arFurnitura[] = $arItem;

    }

    $arItems[] = array(
        'id' => 9999,
        'iblock_id' => 27,
        'name' => 'Фурнитура',
        'children' => $arFurnitura
    );

    $arTagPages = $Content -> GetList(
        array( 'name', 'h1', 'url_chpu', 'bind_section' ),
        array(
            'iblock_id' => 32
        ),
        array(),
        array( 'sort' => 'asc' )
    );

    $arPages = array();

    foreach ( $arTagPages['items'] as $arTagPage ) {

        if ( !empty( $arTagPage['bind_section'] ) ) {

            foreach ( $arTagPage['bind_section'] as $arSection ) {
                $arPages[$arSection['value']][] = array(
                    'name' => $arTagPage['name'],
                    'url_chpu' => $arTagPage['url_chpu']
                );
            }

        }

    }?>

    <div class="catalog-main">
            <?foreach ( $arItems as $arItem ) {

                $iconId = 0;

                switch ( $arItem['id'] ) {
                    case 57:
                        $iconId = 'menu-1';
                        break;
                    case 56:
                        $iconId = 'menu-2';
                        break;
                    case 71:
                        $iconId = 'menu-3';
                        break;
                    case 69:
                        $iconId = 'menu-4';
                        break;
                    case 70:
                        $iconId = 'menu-5';
                        break;
                    case 116:
                        $iconId = 'menu-6';
                        break;
                    case 9999:
                        $iconId = 'menu-7';
                        break;
                    case 124:
                        $iconId = 'menu-8';
                        break;
                }?>


                <div class="catalog-main__item-wrapper">
                    <div class="catalog-main__item">
                        <div class="catalog-main__item-svg">
                            <?if ( !empty( $iconId ) ) {?>
                                <svg class="_icon-<?=$iconId?>">
                                    <use xlink:href="<?=TPL?>/images/icons/<?=$iconId?>.svg#<?=$iconId?>"></use>
                                </svg>
                            <?}?>
                        </div>

                        <div class="catalog-main__item-title">
                            <a href="/katalog/<?=$arItem['code']?>/"><?=$arItem['name']?></a>
                        </div>
                    </div>

                    <?if ( !empty( $arItem['children'] ) ) {?>
                        <ul class="catalog-main__first-lvl__ul">
                            <?foreach ( $arItem['children'] as $arChildren ) {?>
                                <li class="catalog-main__first-lvl__li">
                                    <a class="catalog-main__first-lvl__a" href="/katalog/<?=$arItem['code']?>/<?=$arChildren['code']?>/"><?=$arChildren['name']?></a>

                                    <?if ( !empty( $arPages[$arChildren['id']] ) ) {?>
                                        <ul class="catalog-main__second-lvl__ul">
                                            <?foreach ( $arPages[$arChildren['id']] as $arPage ) {?>
                                                <li class="catalog-main__second-lvl__li">
                                                    <a class="catalog-main__second-lvl__a" href=" <?=$arPage['url_chpu']?>"><?=$arPage['name']?></a>
                                                </li>
                                            <?}?>
                                        </ul>
                                    <?}?>
                                </li>
                            <?}?>
                        </ul>
                    <?}?>
                </div>
            <?}?>
    </div>

    <?//$Functions -> Pre( $arItems );

}