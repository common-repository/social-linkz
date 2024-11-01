(function ($) {

    //add constant for translations
    const {__} = wp.i18n;

    $(document).ready(function () {
        //sortable social networks
        $(function () {
            $(".social-linkz-sortable").sortable({
                delay: 150 //prevent dragging when clicking
            });
        });

        // Add / Remove `active` class
        $('ul.social-linkz-social-networks.social-linkz-sortable li input[type="checkbox"]').click(function () {
            let $label = $(this).closest('label');

            if ($label.hasClass('active')) {
                $label.removeClass('active');
            } else {
                $label.addClass('active');
            }
        });

        //color picker input
        $(function () {
            $('.social-linkz-color-picker').wpColorPicker();
        });

    });
}(jQuery));
