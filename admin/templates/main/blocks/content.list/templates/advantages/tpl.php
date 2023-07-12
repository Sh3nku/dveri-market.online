<?php

if ( $arResult['items'] ) {?>

    <div class="container">
        <div class="row">
            <?foreach ( $arResult['items'] as $key => $arItem ) {?>
                <div class="col-x-6 advantage-col">
                    <div class="advantage">
                        <div class="advantage-title">
                            <div class="advantage-title-svg"><?=$arItem['svg']?></div>
                            <div class="advantage-title-text"><?=$arItem['name']?></div>
                        </div>

                        <div class="advantage-text"><?=$arItem['description']?></div>
                    </div>
                </div>
            <?}?>
        </div>
    </div>

<?}