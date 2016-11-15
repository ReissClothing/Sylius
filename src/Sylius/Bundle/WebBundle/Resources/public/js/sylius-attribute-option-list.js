(function ($, window, document) {
    var $collectionHolder;

    $(document).ready(function () {
        var addValueButton = $('button.add-value');
        $collectionHolder  = getUpdatedCollectionHolder();

        toggleRemoveButton($collectionHolder);

        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        addValueButton.on('click', function(e) {
            e.preventDefault();

            addTagForm($collectionHolder);

            toggleRemoveButton(getUpdatedCollectionHolder());
        });

        // Removing remove buttons if there are less than two or two
        $('ul.values li').each(function() {
            addRemoveAction($(this));
        });
    });

    function addTagForm($collectionHolder) {
        var prototype = $collectionHolder.data('prototype');
        var index     = $collectionHolder.data('index');
        var newForm   = prototype.replace(/__name__/g, index);

        $collectionHolder.data('index', index + 1);

        var newLi =
            $('<li style="list-style:none;"></li>')
                .append('<div class="col-sm-6"><div class="form-group">' + newForm + '</div></div><div class="clear"></div>');
        $collectionHolder.append(newLi);

        addTagFormDeleteLink(newLi);
    }

    function addRemoveAction(li) {
        li.find('.js-opt-list-remove-value').on('click', function(e) {
            e.preventDefault();

            li.remove();

            // There's just one input, and we don't allow to have them all empty, so we'll remove the remove button
            toggleRemoveButton(getUpdatedCollectionHolder());
        });
    }

    function addTagFormDeleteLink(li) {
        li.prepend('<div class="col-sm-2">' +
            '<button type="button" class="close option-list-remove-cross js-opt-list-remove-value" data-dismiss="alert">&times;</button></div>');
        addRemoveAction(li);
    }

    function toggleRemoveButton($collectionHolder) {
        var liSet = $collectionHolder.find('li');

        $.each(liSet.find('.js-opt-list-remove-value'), function() {
            2 >= $collectionHolder.find('li').length ? $(this).hide() : $(this).show();
        });
    }

    function getUpdatedCollectionHolder() {
        $collectionHolder = $('ul.values');

        return $collectionHolder;
    }
})(jQuery, window, document);