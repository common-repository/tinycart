jQuery(function ($) {
    var console = window.console || {log: function (message) {}};

    $('body').on('click', '[data-tinycart-media-account]', function (event) {
        event.preventDefault();

        $.getJSON('https://tinycart.com/items/published/' + $(this).attr('data-tinycart-media-account') + '/', function (data) {
            var html = '<div class="clearfix">';

            $.each(data.items, function (index, item) {
                html += '<div class="tinycart-item">';

                if (item.thumb !== null) {
                    html += '<div class="tinycart-item-media">';
                    html += '<img src="' + item.thumb + '" alt="' + item.name + '">';
                    html += '</div>';
                } else {
                    html += '<div class="tinycart-item-media"><img src="https://via.placeholder.com/180x180" alt="No Image"></div>';
                }

                html += '<div class="tinycart-item-name">' + item.name + '</div>';
                html += '<div class="tinycart-item-price">$' + item.price + '</div>';
                html += '<div class="tinycart-item-insert">';
                html += '<a class="tinycart-btn" data-tinycart-item-id="' + item.pk + '" data-tinycart-item-name="' + item.name + '" href="#">Insert Buy Link</a>';
                html += '</div>';
                html += '</div>';
            });

            if (data.items.length === 0) {
                html += '<div>You haven\'t added any items to Tinycart, yet. Go to <a target="_blank" href="https://tinycart.com/manage/items/list/">Tinycart.com/manage/items/list</a> to add one now.</div>';
            }

            html += '</div>';

            swal({
                titleText: 'Select an Item to sell',
                html: html,
                width: '100%',
                showConfirmButton: false,
                showCancelButton: true
            });
        });
    });

    $('body').on('click', '[data-tinycart-item-id]', function (event) {
        event.preventDefault();

        insert({'id': $(this).attr('data-tinycart-item-id'), 'name': $(this).attr('data-tinycart-item-name')});

        swal.close();
    });

    // Insert an item into the media editor
    function insert(item) {
        wp.media.editor.insert(
            '<a href="#" data-tinycart-click="' + item.id + '">Buy ' + item.name + '</a>'
        );
    }
});
