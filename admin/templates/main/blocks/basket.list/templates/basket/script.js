$( document ).off( '.basket-count-input' ).on( 'keyup', '.basket-count-input', function () {

    let c = $( this ).val();

    if ( c > 99 || c < 0 ) return false;

    basketRecalculate({
        'item': $( this ).data( 'item' ),
        'count': c
    })

});