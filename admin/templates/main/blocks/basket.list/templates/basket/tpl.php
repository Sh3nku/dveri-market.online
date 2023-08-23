<div class="basket-wrapper">

    <?if ( $arResult['items'] ) {?>

        <h2>Корзина</h2>

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

                <div class="basket__item">
                    <div class="basket__item-info">
                        <div class="basket__item-picture">
                            <img src="<?=$preview_picture?>">
                        </div>

                        <div class="basket__item-description">
                            <div class="basket__item-name">
                                <?=$arItem['element']['name']?>
                                <span class="basket__item-offer__name"><?=preg_replace( '/<br>/', ' ', $arItem['element']['model'] ?? $arItem['offer']['name'] )?></span>
                            </div>

                            <?if ( is_array( $arItem['properties'] ) && !empty( $arItem['properties'] ) ) {?>
                                <div class="basket__item-properties">

                                    <?$arProperties = array();

                                    foreach ( $arItem['properties'] as $arProperty ) {
                                        $property_name = $arProperty['name'];
                                        $arProperties[] = $arProperty['name'] . ': ' . $arProperty['values'][0]['name'];
                                    }

                                    echo implode( ', ', $arProperties )?>

                                </div>
                            <?}?>
                        </div>
                    </div>

                    <div class="basket__item-prices">
                        <div class="basket__item-prices__header">
                            <div>Цена за шт.</div>
                            <div>Кол-во</div>
                            <div>Сумма</div>
                        </div>

                        <?$arPrice = !empty( $arItem['offer'] ) ? $arItem['offer'] : $arItem['element']?>

                        <div class="basket__item-prices__body">
                            <div>
                                <?if ( !empty( $arPrice['discount'] ) ) {?>
                                    <div class="basket__item-prices__old"><?=$Functions -> NumberFormat( $arPrice['price'], 2, '.', ' ' )?>₽</div>
                                <?}?>

                                <div class="basket__item-prices__base"><?=$Functions -> NumberFormat( $arPrice['discount_price'], 2, '.', ' ' )?>₽</div>
                            </div>

                            <div>
                                <div class="basket__item-count">
                                    <div class="basket__item-count__minus | js-basket-button" data-action="minus"></div>

                                    <input
                                            class="basket__item-count__input"
                                            type="text"
                                            name="product_count"
                                            value="<?=$arItem['count']?>"
                                            data-item="<?=$arItem['id']?>"
                                            readonly
                                    >

                                    <div class="basket__item-count__plus | js-basket-button" data-action="plus"></div>
                                </div>
                            </div>

                            <div>
                                <?if ( !empty( $arPrice['discount'] ) ) {?>
                                    <div data-item="<?=$arItem['id']?>"  class="basket__item-prices__old"><?=$Functions -> NumberFormat( $arPrice['price'] * $arItem['count'], 2, '.', ' ' )?>₽</div>
                                <?}?>

                                <div data-item="<?=$arItem['id']?>"  class="basket__item-prices__base"><?=$Functions -> NumberFormat( $arPrice['discount_price'] * $arItem['count'], 2, '.', ' ' )?>₽</div>
                            </div>
                        </div>
                    </div>

                    <div class="basket__item-remove | basket-button-delete" data-item="<?=$arItem['id']?>">
                        <svg class="_icon-close-modal">
                            <use xlink:href="/admin/templates/main/images/icons/close-modal.svg#close-modal"></use>
                        </svg>
                    </div>
                </div>

                <?
                $summ += $arPrice['price'] * $arItem['count'];
                $summ_with_discount += $arPrice['discount_price'] * $arItem['count'];

            }?>

        </div>

        <div class="basket-footer">

            <div class="basket-footer-total">

                <div class="basket-footer-text">Итоговая цена:</div>

                <?if ( $summ === $summ_with_discount ) {?>

                    <div id="BasketSumm" class="basket-price-base basket-price-base-summ"><?=$Functions -> NumberFormat( $summ, 2, '.', ' ' )?>₽</div>

                <?} else {?>

                    <div id="BasketSumm" class="basket-price-old-summ"><?=$Functions -> NumberFormat( $summ, 2, '.', ' ' )?>₽</div>
                    <div id="BasketSummWithDiscount" class="basket-price-discount-summ"><?=$Functions -> NumberFormat( $summ_with_discount, 2, '.', ' ' )?>₽</div>

                <?}?>

            </div>

            <div class="basket-footer-buttons">

                <a class="button" href="/order/" onclick="close_modal()">Оформить заказ</a>

                <div class="button _black" onclick="close_modal()">Продолжить покупки</div>

            </div>

        </div>

    <?} else {?>

        <div class="center">
            <h2>Ваша корзина пуста</h2>
            <h3>Перейдите в <a href="/katalog/">каталог</a> и выберите товар.</h3>
        </div>

    <?}

    if (
        \User\User::IsAuthorized()
        && in_array( 1, \User\Group::GetGroupId( \User\User::GetId() ) )
    ) {?>
        <h2>Распечатать</h2>
        <form action="/print/basket.php">
            <div class="row">
                <div class="col-s-6">
                    <div class="form-label">Имя</div>
                    <input class="grey" type="text" name="manager">
                </div>

                <div class="col-s-6">
                    <div class="form-label">Контакт</div>
                    <input class="grey" type="text" name="phone">
                </div>

                <div class="col-s-12">
                    <div class="form-label">Комментарий</div>
                    <textarea class="grey" name="comment"></textarea>
                </div>

                <div class="col-s-12">
                    <input class="button" type="submit" value="Распечатать">
                </div>
            </div>
        </form>
    <?}?>

</div>