/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */

$(function () {
    $('.wishlist_name_display').click(function () {
        var index = this.id.match(/[^_]*$/)[0];
        var input = $('#wishlist_name_input_' + index);
        $(this).hide();

        input.show();
        input.select();
    });

    $('.wishlist_name_input').blur(function () {
        $(this).closest('form').submit();
    });

    $('form.rename').submit(function (e) {
        e.preventDefault();

        var input = $(this).find('.wishlist_name_input');
        var div = $(this).find('.wishlist_name_display');

        var changed;

        if ($.trim(input.val()) == '') {
            input.val(div.html());
            changed = false;
        } else if (input.val() == div.html()) {
            changed = false;
        } else {
            div.html(input.val());
            changed = true;
        }

        input.hide();
        div.show();

        if (changed) {
            $.ajax({
                type: "POST",
                url: $(this).data('url'),
                data: { wishlistId: $(this).data('wishlist-id'), newName: input.val() },
                dataType: 'html',
                success: function (response) {
                    if ($('.alert').length) {
                        $('.alert').remove();
                    }
                    $('.col-md-9').prepend(response);
                }
            });
        }
        return false;
    });

    $('.wishlist_container_products').on('click', 'li', function () {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');

            if ($('.wishlist_item.selected').length == 0) {
                $('.landing_zone').each(function () {
                    $(this).hide();
                });
            }

        } else {
            $(this).addClass('selected');

            var parentUl = $(this).closest(".wishlist_container_products");
            var wishlistId = parentUl.closest('.wishlist_container').data('wishlist-id');

            $('.landing_zone').each(function () {
                if ($(this).closest('.wishlist_container').data('wishlist-id') != wishlistId) {
                    $(this).show();
                }
            });
        }
    });

    $('.landing_zone').on('click', function () {
        $('.landing_zone').hide();

        var arraySelectedProducts = [];
        var wishlistId = $(this).data('wishlist-id');
        var copyUrl = $(this).data('url-copy');
        var productsContainer = $('#wishlist_products_' + wishlistId);

        var productIds = $('#wishlist_container_' + wishlistId + ' li').map(function () {
            return $(this).data('variant-id');
        });

        $('.wishlist_item.selected').each(function () {
            arraySelectedProducts.push($(this).data('variant-id'));

            $(this).removeClass('selected');

            if (productIds.length == 0) {
                $('#empty_wishlist_' + wishlistId).remove();
            }

            if ($.inArray($(this).data('variant-id'), productIds) == -1) {
                //Remove empty wishlist text (if exist)
                productsContainer.find(".empty_wishlist").remove();

                //Clone with data and events: clone(true)
                var elementToAppend = $(this).clone(true);

                elementToAppend.attr("data-variant-id", $(this).data("variant-id"));
                elementToAppend.attr("data-wishlist-id", wishlistId);
                elementToAppend.find('.close.remove_wishlist_item').attr("data-wishlist-id", wishlistId);

                elementToAppend.appendTo(productsContainer);
            }
        });
        ajaxCopyAction(arraySelectedProducts, wishlistId, copyUrl);
    });

    function ajaxCopyAction(arraySelectedProducts, wishlistId, copyUrl) {
        $.ajax({
            type: "POST",
            url: copyUrl,
            data: {
                variantIds: arraySelectedProducts,
                wishlistId: wishlistId
            },
            dataType: 'html',
            success: function (response) {
                if ($('.alert').length) {
                    $('.alert').remove();
                }
                $('.col-md-9').prepend(response);
            }
        });
    }

    $('.close.remove_wishlist_item').on("click", function () {

        var variantId = $(this).data('variant-id');
        var removeUrl = $(this).data('url');
        var wishlistId = $(this).closest('.wishlist_container').data('wishlist-id');
        var emptyHtml = '<div id="empty_wishlist_' + wishlistId + '" class="empty_wishlist">Your wishlist is empty.</div>';

        $(".wishlist_item[data-variant-id='" + variantId + "'][data-wishlist-id='" + wishlistId + "']").remove();

        if ($('#wishlist_products_' + wishlistId + ' > li').length == 0) {
            $('#wishlist_products_' + wishlistId).append(emptyHtml);
        }

        $.ajax({
            type: "POST",
            url: removeUrl,
            data: {
                productVariantId: variantId,
                wishlistId: wishlistId
            },
            dataType: 'html',
            success: function (response) {
                if ($('.alert').length) {
                    $('.alert').remove();
                }
                $('.col-md-9').prepend(response);
            }
        });
    });

    $('.close.remove_wishlist').on('click', function () {

        var removeUrl = $(this).data('url');
        var wishlistId = $(this).closest('.wishlist_container').data('wishlist-id');

        $('#wishlist_form_ul_' + wishlistId).remove();

        $.ajax({
            type: "POST",
            url: removeUrl,
            data: {
                wishlistId: wishlistId
            },
            dataType: 'html',
            success: function (response) {
                if ($('.alert').length) {
                    $('.alert').remove();
                }
                $('.col-md-9').prepend(response);
            }
        });
    });

    $('.add_item_to_wishlist').submit(function (e) {
        e.preventDefault();

        var variant = $(this).data('variant-id');
        var productId = $(this).data('product-id');

        $.ajax({
            type: "POST",
            url: $(this).data('url'),
            data: {
                id: productId,
                variantId: variant
            },
            dataType: "html",
            success: function (response) {
                if ($('.alert').length) {
                    $('.alert').remove();
                }
                $('.col-md-9').prepend(response);
            }
        });
    });

    $('.share-wishlist').on('click', function () {
        var wishlistId = $(this).data('wishlist-id');
        var buttons = $('.modal.fade.share-wishlist').find('a');

        buttons.data('wishlist-id', wishlistId);

        $.ajax({
            type: "POST",
            url: $(this).data('loading-url'),
            data: {
                wishlistId: $(this).data('wishlist-id')
            },
            dataType: "json",
            success: function (response) {
                showInModal(response.text);
            }
        });
    });

    $('.modal.fade.share-wishlist').on('blur', function () {
        showLoadingModal();
        $('.modal-backdrop.in').remove();
    });

    $('.unshare').on('click', function () {

        showLoadingModal();

        $.ajax({
            type: "POST",
            url: $(this).data('url'),
            data: {
                wishlistId: $(this).data('wishlist-id')
            },
            dataType: "json",
            success: function (response) {
                showInModal(response.text);
            }
        });
    });

    $('.share').on('click', function () {

        showLoadingModal();

        $.ajax({
            type: "POST",
            url: $(this).data('url'),
            data: {
                wishlistId: $(this).data('wishlist-id')
            },
            dataType: "json",
            success: function (response) {
                showInModal(response.text);
            }
        });
    })

    function showLoadingModal() {
        var divText = $('.modal.fade.share-wishlist').find('#modal-content-text');
        divText.text("Loading, please wait...");
    }

    function showInModal(text) {
        var divText = $('.modal.fade.share-wishlist').find('#modal-content-text');
        divText.text(text);
    }
});