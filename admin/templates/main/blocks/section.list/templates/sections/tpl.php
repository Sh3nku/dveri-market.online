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
        array( 'name', 'h1', 'url_chpu', 'bind_section', 'no_view_in_main_menu' ),
        array(
            'iblock_id' => 32
        ),
        array(),
        array( 'sort' => 'asc' )
    );

    $arPages = array();

    foreach ( $arTagPages['items'] as $arTagPage ) {

        if ( $arTagPage['no_view_in_main_menu']['code'] === 'yes' ) continue;

        if ( !empty( $arTagPage['bind_section'] ) ) {

            foreach ( $arTagPage['bind_section'] as $arSection ) {
                $arPages[$arSection['value']][] = array(
                    'name' => $arTagPage['name'],
                    'url_chpu' => $arTagPage['url_chpu']
                );
            }

        }

    }

    //$Functions -> Pre( $arPages );

    //$Functions -> Pre( $arItems );
    //echo count( $arItems )?>

    <nav class="menu" itemscope itemtype="http://schema.org/SiteNavigationElement">
        <div class="container">
            <div class="menu__main">
                <div class="menu__first-level small-scroll">
                    <ul class="menu__first-level__ul">
                        <?foreach ( $arItems as $arItem ) {?>
                            <li class="menu__first-level__li">
                                <a itemprop="url" class="menu__first-level__a | js-menu-choice <?=empty( $arItem['children'] ) ? 'js-no' : ''?>" href="/katalog/<?=$arItem['code']?>/" data-menu_id="<?=$arItem['id']?>">

                                    <?$iconId = 0;

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

                                    <div class="menu__first-level__svg">
                                        <svg class="_icon-<?=$iconId?>">
                                            <use xlink:href="<?=TPL?>/images/icons/<?=$iconId?>.svg#<?=$iconId?>"></use>
                                        </svg>
                                    </div>

                                    <span itemprop="name"><?=$arItem['name']?></span>

                                    <?if ( !empty( $arItem['children'] ) ) {?>
                                        <div class="menu__first-level__svg-arrow">
                                            <svg class="_icon-arrow-menu-item">
                                                <use xlink:href="<?=TPL?>/images/icons/arrow-menu-item.svg#arrow-menu-item"></use>
                                            </svg>
                                        </div>
                                    <?}?>
                                </a>
                            </li>
                        <?}global $Includes;

                        $Includes::Template(
                            'main:menu',
                            'top',
                            array(
                                'type' => 'top'
                            )
                        )?>
                    </ul>
                </div>
            </div>
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
                                <li class="menu__second-level__li"><a itemprop="url" class="menu__second-level__a" href="/katalog/<?=$arItem['code']?>/"><span itemprop="name">перейти в раздел "<?=$arItem['name']?>"</span></a></li>

                                <?foreach ( $arItem['children'] as $arChildren ) {?>
                                    <li class="menu__second-level__li">
                                        <a itemprop="url" class="menu__second-level__a<?=!empty( $arPages[$arChildren['id']] ) ? ' | js-menu-choice' : ''?>" href="/katalog/<?=$arItem['code']?>/<?=$arChildren['code']?>/" data-menu_id="<?=$arChildren['id']?>">
                                            <span itemprop="name"><?=$arChildren['name']?></span>

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
                                                    <li class="menu__third-level__li"><a itemprop="url" class="menu__third-level__a" href="/katalog/<?=$arItem['code']?>/<?=$arChildren['code']?>/"><span itemprop="name">перейти в раздел "<?=$arChildren['name']?>"</span></a></li>

                                                    <?foreach ( $arPages[$arChildren['id']] as $arPage) {?>
                                                        <li class="menu__third-level__li">
                                                            <a itemprop="url" class="menu__third-level__a" href="<?=$arPage['url_chpu']?>">
                                                                <span itemprop="name"><?=$arPage['name']?></span>
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

<?}