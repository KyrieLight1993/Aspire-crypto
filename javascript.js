// Get the modal
var modal = document.getElementById('id01');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

$( ".change" ).on("click", function() {
    if( $( "body" ).hasClass( "dark" )) {
        $( "body" ).removeClass( "dark" );
        $( ".change" ).text( "OFF" );
    } else {
        $( "body" ).addClass( "dark" );
        $( ".change" ).text( "ON" );
    }
});

function myFunction() {
    var element = document.body;
    element.classList.toggle("dark");
    }