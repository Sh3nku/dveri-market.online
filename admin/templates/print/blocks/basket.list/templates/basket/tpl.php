<div class="basket-wrapper">

    <?if ( $arResult['items'] ) {?>

        <h2>Ваш заказ</h2>

        <div class="basket">

            <?
            $summ = 0;
            $summ_with_discount = 0;

            for ( $i = 0; $i < count( $arResult['items'] ); $i++ ) {

                $arItem = $arResult['items'][$i];

                $classFiles = new Files();

                $picture_id = $arItem['offer']['picture'] ?? $arItem['element']['picture'];

                $arPicture = $classFiles -> GetFiles(
                    array(),
                    array(
                        'id' => $picture_id
                    ),
                    array(),
                    array()
                );

                $preview_picture = $classFiles -> Resize( $arPicture['items'][0]['path'], 80, 100, false, 'basket_preview_' )?>

                <div class="basket-item">

                    <div class="basket-picture">
                        <img src="<?=$preview_picture?>">
                    </div>

                    <div class="basket-description">

                        <div class="basket-name">

                            <?=$arItem['element']['name']?>
                            <span class="basket-offer-name"><?=$arItem['element']['model'] ?? $arItem['offer']['name']?></span>

                        </div>

                        <?if ( is_array( $arItem['properties'] ) && !empty( $arItem['properties'] ) ) {?>

                            <div class="basket-properties">

                                <?$arProperties = array();

                                foreach ( $arItem['properties'] as $arProperty ) {

                                    $property_name = $arProperty['name'];
                                    $arProperties[] = $arProperty['name'] . ': ' . $arProperty['values'][0]['name'];

                                }

                                echo implode( ', ', $arProperties )?>

                            </div>

                        <?}?>

                    </div>

                    <?if ( $arItem['offer'] ) {
                        $arPrice = $arItem['offer'];
                    } else {
                        $arPrice = $arItem['element'];
                    }?>

                    <div class="basket-price basket-price-piece">

                        <?if ( !$arPrice['discount'] ) {?>

                            <div class="basket-price-base"><?=$Functions -> NumberFormat( $arPrice['discount_price'], 2, '.', ' ' )?>₽</div>

                        <?} else {?>

                            <div class="basket-price-base basket-price-old"><?=$Functions -> NumberFormat( $arPrice['price'], 2, '.', ' ' )?>₽</div>
                            <div class="basket-price-discount"><?=$Functions -> NumberFormat( $arPrice['discount_price'], 2, '.', ' ' )?>₽</div>

                        <?}?>

                    </div>

                    <div class="basket-count">
                        <?=$arItem['count']?>
                    </div>

                    <div class="basket-price">

                        <?if ( !$arPrice['discount'] ) {?>

                            <div id="BasketPrice_<?=$arItem['id']?>" class="basket-price-base"><?=$Functions -> NumberFormat( $arPrice['discount_price'] * $arItem['count'], 2, '.', ' ' )?>₽</div>

                        <?} else {?>

                            <div id="BasketPrice_<?=$arItem['id']?>" class="basket-price-base basket-price-old"><?=$Functions -> NumberFormat( $arPrice['price'] * $arItem['count'], 2, '.', ' ' )?>₽</div>
                            <div id="BasketDiscount_<?=$arItem['id']?>" class="basket-price-discount"><?=$Functions -> NumberFormat( $arPrice['discount_price'] * $arItem['count'], 2, '.', ' ' )?>₽</div>

                        <?}?>

                    </div>

                </div>

                <?
                $summ += $arPrice['price'] * $arItem['count'];
                $summ_with_discount += $arPrice['discount_price'] * $arItem['count'];

            }?>

        </div>

        <div class="basket-footer">

            <div></div>

            <div class="basket-footer-total">

                <div class="basket-footer-text">Итоговая цена</div>

                <div class="basket-footer-price">

                    <?if ( $summ === $summ_with_discount ) {?>

                        <div id="BasketSumm" class="basket-price-base basket-price-base-summ"><?=$Functions -> NumberFormat( $summ, 2, '.', ' ' )?>₽</div>

                    <?} else {?>

                        <div id="BasketSumm" class="basket-price-base basket-price-old basket-price-base-summ basket-price-old-summ"><?=$Functions -> NumberFormat( $summ, 2, '.', ' ' )?>₽</div>
                        <div id="BasketSummWithDiscount" class="basket-price-discount basket-price-discount-summ"><?=$Functions -> NumberFormat( $summ_with_discount, 2, '.', ' ' )?>₽</div>

                    <?}?>

                </div>

            </div>

        </div>

    <?} else {?>

        <div class="center">
            <h2>Ваша корзина пуста</h2>
            <h3>Перейдите в каталог и выберите товар.</h3>
        </div>

    <?}?>

</div>