$(document).ready(function() {
    $('#frm').on('submit', function (e) {
        e.preventDefault();
        var title = $('#title').val();
        var body = $('#body').val();
        var published_at = $('#published_at').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('[name="_token"]').val()
            }
        });
        $.ajax({
            type: "POST",
            url: 'http://localhost/laravel-5/public/articles',
            dataType: 'JSON',
            data: {title: title, body: body, published_at: published_at},
            success: function( data ) {

                //console.log(data);

                if(data.status == 'success') {
                    //alert(data.msg);
                    $("#ajaxResponse").empty();
                    $("#ajaxResponse").append(data.msg);
                    setInterval(function () {
                        window.location.replace("http://localhost/laravel-5/public/articles");
                    }, 3000);

                } else {
                    alert('error');
                    console.log(data.msg);
                }
            }
        });
    });
});