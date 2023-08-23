<div class="order">
    <?if ( !empty( $arResult['basket_list'] ) ) {?>

        <h2>Состав заказа</h2>

        <div class="basket _order">

            <?
            $summ = 0;
            $summ_with_discount = 0;

            foreach ( $arResult['basket_list'] as $key => $arItem ) {

                //$Functions -> Pre( $arItem );

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

                $preview_picture = $classFiles -> Resize( $arPicture['items'][0]['path'], 67, 84, false, 'basket_preview_' )?>

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
                                    <div data-item="<?=$arItem['id']?>" class="basket__item-prices__old"><?=$Functions -> NumberFormat( $arPrice['price'] * $arItem['count'], 2, '.', ' ' )?>₽</div>
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

                <?//$Functions -> Pre($arItem);

                $summ += $arPrice['price'] * $arItem['count'];
                $summ_with_discount += $arPrice['discount_price'] * $arItem['count'];

            }?>

        </div>

        <form class="order__form" id="Order" autocomplete="off">

            <h2>Контактные данные</h2>

            <div class="row">
                <div class="col-m-4">
                    <div class="form-input">
                        <input type="text" name="last_name" placeholder="Фамилия">
                    </div>
                </div>

                <div class="col-m-4">
                    <div class="form-input">
                        <input type="text" name="first_name" placeholder="Имя">
                    </div>
                </div>

                <div class="col-m-4">
                    <div class="form-input">
                        <input type="text" name="middle_name" placeholder="Отчество">
                    </div>
                </div>

                <div class="col-m-6">
                    <div class="form-input">
                        <input type="text" name="email" placeholder="E-mail" data-error="email">
                    </div>
                </div>

                <div class="col-m-6">
                    <div class="form-input">
                        <input type="tel" name="phone" placeholder="Телефон" data-error="phone">
                    </div>
                </div>
            </div>

            <?if ( !empty( $arResult['payments'] ) ) {?>

                <div class="form-block" data-error="payment">
                    <h2>Способы оплаты</h2>
                </div>

                <div class="order-payments">

                    <div class="row">

                        <?foreach ( $arResult['payments'] as $key => $arPayment ) {?>

                            <div class="col-m-4">

                                <input class="_big" type="radio" name="payment" id="Payment_<?=$arPayment['code']?>" value="<?=$arPayment['code']?>">
                                <label for="Payment_<?=$arPayment['code']?>">
                                    <?=$arPayment['name']?>
                                </label>
                                <?=( ( !empty( $arPayment['description'] ) ) ? '<div class="product-property-radio-description">' . $arPayment['description'] . '</div>' : '' )?>

                            </div>

                        <?}?>

                    </div>

                </div>

            <?}

            if ( !empty( $arResult['deliveries'] ) ) {?>

                <div class="form-block" data-error="delivery">
                    <h2>Способы доставки</h2>
                </div>

                <div class="order-payments">

                    <div class="row">

                        <?foreach ( $arResult['deliveries'] as $key => $arDelivery ) {?>

                            <div class="col-m-4">

                                <input class="_big" type="radio" name="delivery" id="Delivery_<?=$arDelivery['code']?>" value="<?=$arDelivery['code']?>">
                                <label for="Delivery_<?=$arDelivery['code']?>">
                                    <?=$arDelivery['name'] . ( ( $arDelivery['price'] > 0 ) ? ' ' . $Functions -> NumberFormat( $arDelivery['price'], 2, '.', ' ' ) . ' р.' : '' )?>
                                </label>

                                <?=( ( !empty( $arDelivery['description'] ) ) ? '<div class="product-property-radio-description">' . $arDelivery['description'] . '</div>' : '' )?>

                            </div>

                        <?}?>

                    </div>

                </div>

            <?}?>

            <div class="js-delivery-address" style="display: none">
                <h2>Адрес доставки</h2>

                <div class="row">
                    <div class="col-s-6">
                        <div class="form-input">
                            <input type="text" name="city" placeholder="Город">
                        </div>
                    </div>

                    <div class="col-s-6">
                        <div class="form-input">
                            <input type="text" name="street" placeholder="Улица">
                        </div>
                    </div>

                    <div class="col-s-6">
                        <div class="form-input">
                            <input type="text" name="house" placeholder="Дом">
                        </div>
                    </div>

                    <div class="col-s-6">
                        <div class="form-input">
                            <input type="text" name="flat" placeholder="Квартира">
                        </div>
                    </div>
                </div>
            </div>

            <h2>Комментарий</h2>

            <textarea name="comment" placeholder="Комментарий к заказу"></textarea>

            <div class="basket-footer">
                <div class="basket-footer-total">
                    <div class="basket-footer-text">Итоговая цена:</div>

                    <?if ( $summ === $summ_with_discount ) {?>

                        <div id="BasketSumm" class="basket-price-base basket-price-base-summ js-order-summ"><?=$Functions -> NumberFormat( $summ, 2, '.', ' ' )?>₽</div>

                    <?} else {?>

                        <div id="BasketSumm" class="basket-price-old-summ js-order-summ"><?=$Functions -> NumberFormat( $summ, 2, '.', ' ' )?>₽</div>
                        <div id="BasketSummWithDiscount" class="basket-price-discount-summ js-order-summ-width-discount"><?=$Functions -> NumberFormat( $summ_with_discount, 2, '.', ' ' )?>₽</div>

                    <?}?>
                </div>

                <div class="basket-footer-buttons">

                    <input type="submit" class="button" value="Оформить заказ">
                    <a href="/katalog/" class="button _black">Продолжить покупки</a>

                </div>
            </div>
        </form>

    <?} else {?>
        <h3>Перейдите в <a href="/katalog/">каталог</a> и выберите товар.</h3>
    <?}?>
</div>

<?//$Functions -> Pre( $arResult );