$(document).ready(function () {

    ClassicEditor
        .create( document.querySelector( '#editor' ), {

            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],

        })
        .catch( error => {
            console.error( error );
        });

    ClassicEditor
        .create( document.querySelector( '#editor2' ), {

            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],

        })
        .catch( error => {
            console.error( error );
        });

    ClassicEditor
        .create( document.querySelector( '#editor3' ), {

            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],

        })
        .catch( error => {
            console.error( error );
        });


});