$( function () {

    $( '.order-payments input[name=delivery]' ).on( 'change', function (  ) {

        let val = $( this ).val(),
            address_block = $( '.js-delivery-address' );

        if ( val === 'city' || val === 'region' ) {
            address_block.show();
        } else {
            address_block.hide();
        }

        $.ajax({
            url: '/admin/templates/main/blocks/order/templates/order/helper.php',
            type: 'POST',
            data: { Recalculate: { 'delivery': val } },

            success: function( data ) {

                console.log( data );

                let answer = JSON.parse( data );

                if ( !answer.errors ) {

                    let summ = answer['success']['summ'];

                    $( '.js-order-summ' ).text( summ['price'] );

                    if ( summ['price'] !== summ['discount_price'] ) $( '.js-order-summ-width-discount' ).text( summ['discount_price'] )

                }

            }

        });

    });

    $( '#Order' ).on( 'submit', function ( e ) {

        e.preventDefault();

        let array = $( this ).serializeArray();

        //console.log( array );

        $.ajax({
            url: '/admin/templates/main/blocks/order/templates/order/helper.php',
            type: 'POST',
            data: { OrderSend: array },

            success: function( data ) {

                console.log( data );

                let answer = JSON.parse( data );

                if ( answer['errors'] ) {
                    ShowErrorItems( answer['errors'] );
                } else {
                    location = '/profile/history/' + answer.success.id + '/';
                }

            }

        });

    })

})