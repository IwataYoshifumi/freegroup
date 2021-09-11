/* ---------------------------------------------------------------
*	Fit Iframe JavaScript Library Ver.2.0
*	
*	from 2009, created by edo.
*	http://css-eblog.com/
* --------------------------------------------------------------- */

(function() {

var isMSIE = /*@cc_on!@*/false;
var params = {};
var scripts = document.getElementsByTagName( 'script' );
var fn = 'fit_ifr';		//This script file name.

var fitIfr = function() {
	if( !arguments[0] || arguments[0].type === 'load' ) {
		var ifrObjs = document.getElementsByTagName( 'iframe' );
		for( var i=0; i<ifrObjs.length; i++) {
			var ifrObj = ifrObjs[i];
			var doc = ifrObj.contentWindow.document.documentElement;
			var body = ifrObj.contentWindow.document.body;
		
			try {
				var fitHeight = 0;
				if( isMSIE ) {
					if( navigator.userAgent.toLowerCase().replace( /\s/g, '' ).indexOf( 'msie6' ) > -1 ) {
						fitHeight = body.scrollHeight;
					} else {
						fitHeight = doc.scrollHeight;
					}
				} else {
					fitHeight = doc.offsetHeight;
				}
				
				ifrObj.style.height = fitHeight + 'px';
			} catch( e ) {
				/* skip process */
			}
		}
	} else if( arguments[0].charAt(0) == '-' ) {
		var ifrObjs = document.getElementsByTagName( 'iframe' );
		var chkFlg;
		for( var i=0; i<ifrObjs.length; i++) {
			var ifrObj = ifrObjs[i];
			var doc = ifrObj.contentWindow.document.documentElement;
			var body = ifrObj.contentWindow.document.body;
			chkFlg = false;
			
			for( var k=0; k<arguments.length; k++ ) {
				if( ifrObj.id == arguments[k].substring( 1, arguments[k].length ) ) {
					if ( arguments[k].charAt( 0 ) == '-' ) {
						chkFlg = true;
					}
				}
			}
			
			if ( !chkFlg ) {
				try {
					var fitHeight = 0;
					if( isMSIE ) {
						if( navigator.userAgent.toLowerCase().replace( /\s/g, '' ).indexOf( 'msie6' ) > -1 ) {
							fitHeight = body.scrollHeight;
						} else {
							fitHeight = doc.scrollHeight;
						}
					} else {
						fitHeight = doc.offsetHeight;
					}
					
					ifrObj.style.height = fitHeight + 'px';
				} catch( e ) {
					/* skip process */
				}
			}
		}
	} else {
		for( var i=0; i<arguments.length; i++ ) {
			var ifrObj;
			if( document.getElementById( arguments[i] ) ) {
				ifrObj = document.getElementById( arguments[i] );
			} else {
				continue;
			}
			
			var doc = ifrObj.contentWindow.document.documentElement;
			var body = ifrObj.contentWindow.document.body;
			
			try {
				var fitHeight = 0;
				if( isMSIE ) {
					if( navigator.userAgent.toLowerCase().replace( /\s/g, '' ).indexOf( 'msie6' ) > -1 ) {
						fitHeight = body.scrollHeight;
					} else {
						fitHeight = doc.scrollHeight;
					}
				} else {
					fitHeight = doc.offsetHeight;
				}
				
				ifrObj.style.height = fitHeight + 'px';
			} catch( e ) {
				/* skip process */
			}
		}
	}
}

//get script URL parameter.
for ( var i=0; i<scripts.length; i++ ) {
	if( scripts[i].src.indexOf( fn ) != -1 ) {
		scripts[i].src.match( /(?:.*)(?:\?)(.*)/ );
		if( RegExp.$1 ) {
			var a = RegExp.$1.split( '&' );
			for( var k=0; k<a.length; k++ ) {
				var p = a[k].split( '=' );
				params[p[0]] = p[1];
			}
		}
		
		break;
	}
}

//set function to window object.
window.fitIfr = fitIfr;
if( !params.hasOwnProperty( 'auto' ) || params.auto == 1 ) {
	if( window.addEventListener ) {
		window.addEventListener( 'load', fitIfr, false );
	} else {
		window.attachEvent( 'onload', fitIfr );
	}
}

})();