<?//$Functions -> Pre( $arResult );

if ( !empty( $arResult ) ) {?>
    <ul class="bottom-menu">
        <?for ( $i = 0; $i < count( $arResult ); $i++ ) {

            $arItem = $arResult[$i]?>

            <li class="bottom-menu__li">
                <a class="bottom-menu__a" href="<?=$arItem['path']?>"><?=$arItem['h1']?></a>
            </li>

        <?}?>
    </ul>
<?}