$(document).ready(function () {
    if(!(localStorage.getItem('ClosePosrcad'))){
        $('#postcard').addClass('show');
        $('.postcard_wrap__close').on('click', function () {
            document.cookie = "ClosePosrcad=1;path=/;max-age=2,628e+6";
            localStorage.setItem('ClosePosrcad', 1);
            $(this).parents().find('#postcard').removeClass('show');
        })
    }else{
        $('#postcard').addClass('none-visible');
    }
});
