;/**
 * Object cart controller
 * @type Function|@exp;app@pro;publ
 */
var FlObjectCard = (function () {
    var app = {
        targets: {
            tpl_tile_item: '#card__item_view__tile',
            image_place: '.cart_image_added',
            current: '',
            albums: '#albums_accordion'
        },
        init: function () {
            this.setUpListeners();
            this.links.setUpListeners();
            this.sortSpiner();

            // extend FlUpload.uploadAbortSuccess - remove from gallery
            if (typeof FlUpload === 'object')
                FlUpload.extends.uploadAbortSuccess(app.uploadAbort);
        },
        setUpListeners: function () {
            $('.delete_item').off('click').on('click', app.removeItem);
            $('.tile_galery__remove_item').off('click').on('click', app.removeItem);
            $('.tile_galery__edit_item').off('click').on('click', app.editItem);
            $('.tile_galery .item_settings .hidebox').off('click').on('click', function (e) {
                $(this).parents('.item_settings').hide();
            });
            $('.tile_galery .js-copy, .album .js-copy').off('click').on('click', function (e) {
                var c = $(this).val();
                $(this).siblings('.js-copy-btn').show();
                $(this).siblings('.js-copy-btn').off('click').on('click', function (ev) {
                    var t = $("<input>");
                    $("body").append(t);
                    t.val(c).select();
                    document.execCommand("copy");
                    t.remove();
                    $(this).hide(200);
                    $(this).parents('.item_settings').hide(500);
                });
            });
//            $('.tile_galery .js-copy, .album .js-copy').off('focusout').on('focusout', function () {
//                $(this).siblings('.js-copy-btn').hide();
//            });

            $(document).off('mouseup').on('mouseup', function (e) {
                // событие клика по веб-документу
                var el = $(".js-copy-btn");
                if (!el.is(e.target) // если клик был не по нашему блоку
                        && el.has(e.target).length === 0) { // и не по его дочерним элементам
                    el.hide(); // скрываем его
                }
            });

            // album controls
            $('.album .edit_item').off('click').on('click', app.albums.editItem);
            $('.album .delete_item').off('click').on('click', app.albums.removeItem);
            // init sort albums
            $('.albums_container').sortable({
                axis: "y"
//                handle: '.album_move'
            }).bind('sortupdate', app.albums.sortList);

            // init sort image in album
            $('.tile_handles').sortable({
                handle: '.item_control'
            }).bind('sortupdate', app.sortList);
            // save albums sort
            $('#albums_sort_save').off('click').on('click', app.albums.saveSort);
            // save current album
            $('[data-album-action="save"]').off('click').on('click', app.albums.save);

        },
        links: {
            setUpListeners: function () {
                // add links
                $('.js-form-links [data-add]').off('click').on('click', app.links.add);
                $('.form_links .delete_item').off('click').on('click', app.links.removeItem);
                this.refreshBtns();
            },
            add: function (e) {

                var prefix = 'add_', self = app.links;
                var tpl = doT.template($('#tpl__form_links').html());
                var target = $('.form_links:last');
                var id = target.data('id');

                id = id === undefined ? prefix + '-1' : id;
                if (!target.length)
                    target = $('[name="documents"]');

                if (String(id).search(prefix) !== -1) {
                    id = Number(id.split(prefix).join(''));
                }
                target.after(tpl({link_id: prefix + (++id)}));
                self.setUpListeners();
                self.refreshBtns();

            },
            removeItem: function (e) {
                if ($('.form_links .delete_item').length > 1) {
                    $(this).parents('.form_links').remove();
                } else {
                    $(this).parents('.form_links').find(':input').each(function () {
                        $(this).val('');
                    });
                }
                app.links.refreshBtns();
            },
            refreshBtns: function () {
                $('.form_links').find('.add_item').hide();
                $('.form_links:last').find('.add_item').show();
            }
        },
        uploadAbort: function (fileId) {
            $('[name="files[]"][value="' + fileId + '"]').parents('li').remove();
        },
        albums: {
            removeItem: function (e) {
                e.preventDefault();
                var album = $(this).parents('.album'), url, id, isActive = album.hasClass('active');
                if (!confirm("Альбом будет удален безвозвратно. Вы уверены?") || !(id = album.data('id')))
                    return false;
                url = location.protocol + '//' + location.hostname + '/admin/ajax/delete_image_album/' + id;
                FlDashboardForm.toggleLoader();
                // ajax remove album
                $.getJSON(url, {}, function (data) {
                    FlDashboardForm.toggleLoader();
                    if (data.success) {
                        // удаляем из списка, контент, и прячем контролы если нужно
                        album.remove();
                        $('#album_' + id).remove();
                        if (isActive || !$('.album').length)
                            $('[data-album-controls]').hide();


                    } else {
                        var msg = 'Не удалось удалить альбом. Что-то пошло не так.';
                        FlDashboardForm.toggleGlobalMessage(msg, FlDashboardForm.globalMessageTypes.danger);
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    }
                });

                return false;
            },
            editItem: function (e) {
                var album = $(this).parents('.album');
                album.find('.panel-footer .item_settings').show();
                album.find('.collapsed').trigger('click');
            },
            sortList: function (e, ui) {
                ui.item.parents('.albums_container').find('.album').each(function (a, b) {
                    // set current sort index
                    $(b).find('[name="sort"]').val(a);
                });
                if (ui.item.hasClass('album')) {
                    $('#albums_sort_save_complete').hide();
                    $('#albums_sort_save').fadeIn();
                }
            },
            saveSort: function (e) {
                var albums = [], album, url, btn;
                btn = $(this);
                $('.album').each(function () {
                    album = {
                        image_album_id: $(this).data('id'),
                        sort: $(this).find('[name="sort"]').val()
                    };
                    albums.push(album);
                });
                url = location.protocol + '//' + location.hostname + '/admin/ajax/sort_albums/';
                FlDashboardForm.toggleLoader();

                // ajax save sort albums
                $.post(url, {albums: albums}, function (response) {
                    FlDashboardForm.toggleLoader();
                    if (response.success) {
                        btn.hide();
                        $('#albums_sort_save_complete').fadeIn();
                    } else {
                        var msg = 'Не удалось изменить порядок альбомов. Что-то пошло не так.';
                        FlDashboardForm.toggleGlobalMessage(msg, FlDashboardForm.globalMessageTypes.danger);
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    }
                }, 'json');
            },
            save: function (e) {
                var form = $(app.targets.albums + ' .active form');

                if (!form)
                    return;

                if (!form.find('[name="name"]').val()) {
                    alert('Название альбома не может быть пустым!');
                    return;
                }


                form.submit();
            }
        },
        removeItem: function (e) {

            if ($(this).data('warning')) {
                if (!confirm("Подтвердите удаление!"))
                    return false;
            }


            if ($(this).parents('li').length)
                $(this).parents('li').remove();
            else if ($(this).parents('tr').length)
                $(this).parents('tr').remove();
        },
        editItem: function (e) {
            $(this).parents('li').find('.item_settings').toggle();
        },
        sortSpiner: function () {
            if ($('.tile_handles [name^="sort"]').length)
                $('.tile_handles [name^="sort"]').spinner({min: 0});
        },
        /**
         * Plugin HTML5 Sortable
         * Triggered when the user stopped sorting and the DOM position has changed.
         * @param {object} e - event
         * @param {object} ui - ui.item contains the current dragged element.
         * @returns {undefined}
         */
        sortList: function (e, ui) {
            ui.item.parents('.tile_handles').find('li').each(function (a, b) {
                // set current sort index
                $(b).find('[name^="sort"]').val(a);
            });
        },
        defineActiveAlbum: function () {
            if (!$(this.targets.albums).length) {
                this.targets.current = this.targets.image_place;
                return true;
            }

            if ($(this.targets.albums + ' .active').length) {
                this.targets.current = this.targets.albums + ' .active ' + this.targets.image_place;
                return true;
            } else {
                alert('Выберите альбом для вставки фото!');
                return false;
            }
        },
        view: {
            renderTile: function (data, target) {
                if (!app.defineActiveAlbum()) {
                    console.log('Active album not found');
                    return false;
                }

                var target = target === undefined ? $(app.targets.current) : $(target), tpl;

                tpl = doT.template($(app.targets.tpl_tile_item).html());
                for (var k in data) {
                    // only uniq
                    if (!target.find('[name="files[]"][value="' + data[k].file_id + '"]').length)
                        target.append(tpl(data[k]));
                }
                app.setUpListeners();
                app.sortSpiner();
                if (typeof app.onRenderTile === 'function')
                    app.onRenderTile(data);

            }
        },
        publ: {
            renderTile: function (data, target) {
                app.view.renderTile(data, target);
            },
            onRenderTile: function (callback) {
                if (typeof callback === 'function')
                    app.onRenderTile = callback;
            }
        }
    };
    app.init();
    return app.publ;
}());