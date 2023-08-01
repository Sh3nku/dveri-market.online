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

    <?//$Functions -> Pre( $arItems );?>

    <?/*
    <nav class="menu">
        <div class="container">

            <div class="menu__second small-scroll">
                <?foreach ( $arItems as $key => $arItem ) {?>
                    <div class="menu__second-level<?=$key == 0 ? ' _active' : ''?>" data-menu_target="<?=$arItem['id']?>">
                        <div class="menu__subtitle">
                            <div class="menu__subtitle__svg-arrow | js-menu-back">
                                <svg class="_icon-arrow-menu-item">
                                    <use xlink:href="<?=TPL?>/images/icons/arrow-menu-item.svg#arrow-menu-item"></use>
                                </svg>
                            </div>
                            <?=$arItem['name']?>
                        </div>

                        <?if ( !empty( $arItem['children'] ) ) {?>
                            <ul class="menu__second-level__ul small-scroll">
                                <li class="menu__second-level__li"><a class="menu__second-level__a" href="/katalog/<?=$arItem['code']?>/"><span >перейти в раздел "<?=$arItem['name']?>"</span></a></li>

                                <?foreach ( $arItem['children'] as $arChildren ) {?>
                                    <li class="menu__second-level__li">
                                        <a class="menu__second-level__a | js-menu-choice" href="/katalog/<?=$arItem['code']?>/<?=$arChildren['code']?>/" data-menu_id="<?=$arChildren['id']?>">
                                            <span><?=$arChildren['name']?></span>

                                            <?if ( !empty( $arPages[$arChildren['id']] ) ) {?>
                                                <div class="menu__first-level__svg-arrow">
                                                    <svg class="_icon-arrow-menu-item">
                                                        <use xlink:href="<?=TPL?>/images/icons/arrow-menu-item.svg#arrow-menu-item"></use>
                                                    </svg>
                                                </div>
                                            <?}?>
                                        </a>

                                        <?if ( !empty( $arPages[$arChildren['id']] ) ) {?>
                                            <div class="menu__third-level" data-menu_target="<?=$arChildren['id']?>">
                                                <div class="menu__subtitle">
                                                    <div class="menu__subtitle__svg-arrow | js-menu-back">
                                                        <svg class="_icon-arrow-menu-item">
                                                            <use xlink:href="<?=TPL?>/images/icons/arrow-menu-item.svg#arrow-menu-item"></use>
                                                        </svg>
                                                    </div>
                                                    <?=$arChildren['name']?>
                                                </div>

                                                <ul class="menu__third-level__ul small-scroll">
                                                    <li class="menu__third-level__li"><a class="menu__third-level__a" href="/katalog/<?=$arItem['code']?>/<?=$arChildren['code']?>/"><span>перейти в раздел "<?=$arChildren['name']?>"</span></a></li>

                                                    <?foreach ( $arPages[$arChildren['id']] as $arPage) {?>
                                                        <li class="menu__third-level__li">
                                                            <a class="menu__third-level__a" href="<?=$arPage['url_chpu']?>">
                                                                <span><?=$arPage['name']?></span>
                                                            </a>
                                                        </li>
                                                    <?}

                                                    $countItems = count( $arPages[$arChildren['id']] );

                                                    if ( $countItems > 5 ) {?>
                                                        <li class="menu__third-level__li _more"><a class="menu__third-level__a js-menu-more" href="#">Ещё <?=$countItems - 5?></a></li>
                                                    <?}?>
                                                </ul>
                                            </div>
                                        <?}?>

                                    </li>
                                <?}?>
                            </ul>
                        <?}?>
                    </div>
                <?}?>
            </div>
        </div>
    </nav>
    */?>

<?}