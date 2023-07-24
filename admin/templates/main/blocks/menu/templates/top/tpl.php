<?//$Functions -> Pre( $arResult );

if ( !empty( $arResult ) ) {?>
    <li class="menu__first-level__li _separator"></li>
    <?for ( $i = 0; $i < count( $arResult ); $i++ ) {

        $arItem = $arResult[$i]?>

        <li class="menu__first-level__li">
            <a class="menu__first-level__a" href="<?=$arItem['path']?>"><?=$arItem['h1']?></a>
        </li>

    <?}
}