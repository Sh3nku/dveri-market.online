function basketRecalculate ( array ) {

    $.ajax({
        url: '/admin/templates/main/blocks/basket.list/templates/basket/helper.php',
        async: false,
        type: 'POST',
        data: { basketEdit: array },

        success: function( data ) {

            let answer = JSON.parse( data );

            //console.log( answer );

            if ( !answer.errors ) {

                let item = answer.success.item,
                    summ = answer.success.summ;

                $( '#BasketPrice_' + array.item ).text( item.price );
                if ( item.discount_price ) $( '#BasketDiscount_' + array.item ).text( item.discount_price );

                $( '.header-basket-count' ).text( answer.success.basket_count );
                $( '#BasketSumm' ).text( summ.price );

                if ( summ.price !== summ.discount_price ) $( '#BasketSummWithDiscount' ).text( summ.discount_price )

            }

        }
    });

}

$( document ).off( 'click', '.basket-button-delete' ).on( 'click', '.basket-button-delete', function () {

    let obj = $( this ).parents( '.basket-item' );

    $.ajax({
        url: '/admin/templates/main/blocks/basket.list/templates/basket/helper.php',
        async: false,
        type: 'POST',
        data: { basketDelete: $( this ).data( 'item' ) },

        success: function( data ) {

            let answer = JSON.parse( data );

            console.log( answer );

            if ( !answer.errors ) {

                let basket_count = answer.success.basket_count;

                if ( basket_count > 0 ) {

                    obj.remove();

                    let summ = answer.success.summ;

                    $( '#BasketSumm' ).text( summ.price );

                    if ( summ.price !== summ.discount_price ) {
                        $( '#BasketSummWithDiscount' ).text( summ.discount_price )
                    } else {

                        $( '#BasketSummWithDiscount' ).remove();
                        $( '#BasketSumm' ).removeClass( 'basket-price-old-summ basket-price-old' )

                    }

                } else {

                    $( '.basket-wrapper' ).html( '<div class="center"><h2>Ваша корзина пуста</h2><h3>Перейдите в каталог и выберите товар.</h3></div>' );

                }

                $( '.header-basket-count' ).text( basket_count );

            }

        }
    });

    return false;

});

$( document ).off( 'click', '.js-basket-button' ).on( 'click', '.js-basket-button', function () {

    let input = $( this ).parent().children( '.basket-count-input' ),
        c = +input.val(),
        a = $( this ).attr( 'data-action' ),
        n;

    if ( a === 'minus' && ( c - 1 ) > 0 ) {
        input.val( n = c - 1 );
    } else if ( a === 'plus' && ( c + 1 ) < 100 ) {
        input.val( n = c + 1 );
    }

    if ( n > 0 ) {

        basketRecalculate({
            'item': input.data( 'item' ),
            'count': n
        })

    }

});

$( document ).off( '.basket-count-input' ).on( 'keyup', '.basket-count-input', function () {

    let c = $( this ).val();

    if ( c > 99 || c < 0 ) return false;

    basketRecalculate({
        'item': $( this ).data( 'item' ),
        'count': c
    })

});