$( function () {

    $( '#measurementForm' ).on( 'submit', function ( e ) {

        e.stopImmediatePropagation();

        let form = $( this ),
            array = form.serializeArray(),
            button = form.find( 'input[type=submit]' );

        ShowLoaderButton( button );

        $.ajax({
            cache: false,
            url: '/admin/templates/main/blocks/content.form/templates/order-measurement/helper.php',
            type: 'POST',
            data: { formSend: array },

            success: function( data ) {

                HideLoaderButton( button );

                console.log( data );

                let answer = JSON.parse( data );

                if ( answer['errors'] ) {

                    ShowErrorItems( answer['errors'] );

                } else {

                    open_modal(
                        '<div class="p30">'+
                        '<h1>Заявка отправлена</h1>' +
                        '<p class="mb-32">Мы свяжемся с вами в ближайшее время</p>' +
                        '<input class="button" type="submit" value="Закрыть" onclick="close_modal()">' +
                        '</div>',
                        {
                            width: 600
                        }
                    );

                    form.trigger( 'reset' );

                }

            }

        });

        return false;

    });

});