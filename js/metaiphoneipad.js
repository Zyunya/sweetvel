(function(doc) {
    var viewport = document.getElementById('viewport');
    if ( navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/Android/i)) {
        doc.getElementById("viewport").setAttribute("content", "initial-scale=0.88,width=device-width,user-scalable=no");
    } else if ( navigator.userAgent.match(/iPad/i) ) {
        doc.getElementById("viewport").setAttribute("content", "initial-scale=1.1,user-scalable=no");
    }
else {
        doc.getElementById("viewport").setAttribute("content", "initial-scale=0.5,user-scalable=no");
    }
}(document));
