function Filter () {

    let array = $( '#Filter' ).serializeArray(),
        params = [];

    $.each( array, function ( k, v ) {

        if ( v.name === 'section_code' || ( v.name === 'page' && v.value === '1' ) || ( v.name === 'order' && v.value === 'default' ) ) return;
        if ( v.value !== '' && v.value !== '0' ) params.push( v.name + '=' + v.value )

    });

    if ( params.length ) {
        window.history.replaceState( '', '', window.location.pathname + '?' + params.join( '&' ) )
    } else {
        window.history.replaceState( '', '', window.location.pathname )
    }

    $.ajax({
        url: location.pathname + location.search,
        type: 'GET',
        cache: false,
        dataType: 'html',
        beforeSend: function () {
            startProgressBar()
        },
        success: function( data ) {

            $( '#catalogResult' ).html( $( data ).find( '#catalogResult' ).html() );
            $( '.checkbox.loader, .radiobox.loader' ).removeClass( 'loader' );

            endProgressBar();
            catalogTagMoreToggle();

        }
    });

}

$( function () {

    $( '.catalog-select-item input' ).on( 'change', function () {

        let w = $( this ).parents( '.catalog-select-wrapper' ),
            n = w.children( '.catalog-select-name' ),
            l = w.children( '.catalog-select-list' ),
            p = n.text();

        if ( !n.attr( 'data-placeholder' ) ) n.attr( 'data-placeholder', p );

        let i = [];

        l.children( '.catalog-select-item' ).each( function () {

            let c = $( this ).children( 'input' ),
                la = $( this ).children( 'label' );

            if ( c.prop( 'checked' ) === true ) i.push( la.text() );

        })

        if ( i.length ) {
            n.text( i.join(', ') )
        } else {
            n.text( n.attr( 'data-placeholder' ) )
        }

    });

    $( '.js-show-filter' ).on( 'click', function () {

        let b = $( 'body' );

        if ( b.hasClass( 'responsive-filter' ) ) {

            b.removeClass( 'responsive-filter bodylock' )

        } else {

            b.addClass( 'responsive-filter bodylock' );

        }

    });

    $( '#Filter' ).on( 'submit', function () {

        Filter();
        return false;

    });

    $( '#Filter .js-filter-range' ).on( 'keyup', function () {

        Filter()

    });

    $( document ).off( 'change', '#Filter input[type=checkbox], #Filter input[type=radio]' ).on( 'change', '#Filter input[type=checkbox], #Filter input[type=radio]', function () {

        $( this ).addClass( 'loader' );
        Filter()

    });

    $( '.product-tabs-buttons input' ).on( 'change', function () {

        let name = $( this ).attr( 'id' );

        $( '.product-tabs-sections section' ).hide();
        $( '.product-tabs-sections section[data-tab=' + name + ']' ).show();

    });

})