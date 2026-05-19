
jQuery( function ( $ ) {
	'use strict' ;

	try {
		$( document.body ).on( 'lty-countdown-timer-init' , function ( ) {
			var $countdown_timer = $( '.lty-lottery-countdown-timer' ) ;

			if ( $countdown_timer.length ) {

				$( '.lty-lottery-countdown-timer' ).each( function (  ) {

					var $timer = $( this ) ;

					var interval = setInterval( function ( ) {

						var d = new Date( ) ,
								utc = d.getTime() + ( d.getTimezoneOffset() * 60000 ) ,
								now = new Date( utc ) ,
								end_date = new Date( $timer.data( 'time' ) ).getTime( ) ,
								distance = end_date - now.getTime() ;

						if ( distance < 0 ) {
							//Clear interval.
							clearInterval( interval ) ;
							//After completed the timer reload the page.
							window.location.reload( true ) ;
							return ;
						}

						var days = Math.floor( distance / ( 1000 * 60 * 60 * 24 ) ) ;
						var hours = Math.floor( ( distance % ( 1000 * 60 * 60 * 24 ) ) / ( 1000 * 60 * 60 ) ) ;
						var minutes = Math.floor( ( distance % ( 1000 * 60 * 60 ) ) / ( 1000 * 60 ) ) ;
						var seconds = Math.floor( ( distance % ( 1000 * 60 ) ) / 1000 ) ;

						$timer.find( '#lty_lottery_days' ).html( days ) ;
						$timer.find( '#lty_lottery_hours' ).html( hours ) ;
						$timer.find( '#lty_lottery_minutes' ).html( minutes ) ;
						$timer.find( '#lty_lottery_seconds' ).html( seconds ) ;

						var $data = { days : days , hours : hours , minutes : minutes , seconds : seconds } ;
						$( document.body ).trigger( 'lty-countdown-timer-interval' , [ $data , distance ] ) ;

					} , 1000 ) ;
				} ) ;
			}
		} ) ;
		$( document.body ).trigger( 'lty-countdown-timer-init' ) ;
	} catch ( err ) {
		window.console.log( err ) ;
	}
} ) ;
