/**
 *  Events (on document.body):
 *      'multi_form_group.on_add'    --> after a new group has been inserted into the dom
 *      'multi_form_group.on_remove' --> after group has been removed from the dom
 */

(function () {
    var formGroups = {};

    function init() {
        // Register form groups with ids.
        $('.multi_form_group').each(function (i, e) {
            var group = $(e);
            var id = parseInt(group.attr('class').replace('multi_form_group multi_form_group__', ''));

            // make sure the last group (of the same id) will be accessed
            formGroups[id] = group;
        });

        //  Add the ui controls to add / remove groups.
        $.each(formGroups, function (id, e) {
            var groups = $('.multi_form_group__' + id);
            groups.each(function (i, e) {
                parseSimpleTokens($(e), i + 1);
            });
            var size = groups.length;

            var container = $('<div class="multi_form_control" data-multi_form_id="' + id + '"></div>');
            container.insertAfter(e);

            // control 'remove'
            var controlRemove = $('<a href="#" class="multi_form_control multi_form_control__remove" data-multi_form_id="' + id + '">' + multi_form_control_remove_label + '</a>');
            if (size <= 1) {
                controlRemove.hide();
            }
            container.append(controlRemove);

            // control 'add'
            container.append($('<a href="#" class="multi_form_control multi_form_control__add" data-multi_form_id="' + id + '">' + multi_form_control_add_label + '</a>'));

            // helper: group size
            container.append($('<input type="hidden" name="multi_form_size__' + id + '" value="' + size + '">'));
        });

        $('.multi_form_control__add').click(onAdd);
        $('.multi_form_control__remove').click(onRemove);
    }

    /**
     * Handle adding a new group.
     *
     * @param e click event
     */
    function onAdd(e) {
        e.preventDefault();

        var id = parseInt($(this).attr('data-multi_form_id'));

        // increase size
        var sizeField = $('input[name="multi_form_size__' + id + '"]');
        var newSize = parseInt(sizeField.val()) + 1;
        sizeField.val(newSize);

        // add group
        var $newGroup = getNewGroup(id);
        parseSimpleTokens($newGroup, newSize);

        formGroups[id].after($newGroup.hide());
        $newGroup.show('slow');
        formGroups[id] = $newGroup;


        // enable delete control
        $(this).siblings('.multi_form_control__remove').show();

        // trigger event
        $(document.body).trigger('multi_form_group.on_add', ['multi_form_group__' + id, $newGroup]);
    }

    /**
     * Handle removing a group.
     *
     * @param e click event
     */
    function onRemove(e) {
        e.preventDefault();

        var id = parseInt($(this).attr('data-multi_form_id'));

        var groups = $('.multi_form_group__' + id).toArray();
        var $lastGroup = $(groups.pop());
        $lastGroup.hide('slow', function () {
            $lastGroup.remove();
        });
        formGroups[id] = $(groups.pop());

        // decrease size
        var sizeField = $('input[name="multi_form_size__' + id + '"]');
        sizeField.val(parseInt(sizeField.val()) - 1);

        // do not show control if only element is left
        if (0 === groups.length) {
            $(this).hide();
        }

        // trigger event
        $(document.body).trigger('multi_form_group.on_remove', ['multi_form_group__' + id, $lastGroup]);
    }

    /**
     * Build a new group for a given group id.
     *
     * @param id
     * @returns {*}
     */
    function getNewGroup(id) {
        var $prototype = formGroups[id].clone();

        // change identifier and clear values
        $prototype.find('input, select, textarea').each(function (i, e) {
                var element = $(e);
                element.attr('id', getNextIdentifier(element.attr('id')));
                element.attr('name', getNextIdentifier(element.attr('name')));
                element.val('');
            }
        );
        $prototype.find('label').each(function (i, e) {
                var element = $(e);
                element.attr('for', getNextIdentifier(element.attr('for')));
            }
        );

        // remove errors
        $prototype.find('p.error').remove();
        $prototype.find('.error').removeClass('error');

        return $prototype;
    }

    /**
     * Helper: replace ##token## placeholders
     *
     * @param $element
     * @param index
     */
    function parseSimpleTokens($element, index) {
        $element.find('label, legend').each(function (i, e) {
                var element = $(e);
                var source;

                // save original token
                if (typeof element.attr('data-multi_form_token') !== 'undefined') {
                    source = element.attr('data-multi_form_token');
                } else {
                    source = element.html();
                    element.attr('data-multi_form_token', element.html());
                }

                // replace
                element.html(source.replace('##nr##', index));
            }
        );
    }

    /**
     * Helper: replace identifier suffix (__%n) with next one (__%n+1).
     *
     * @param identifier
     * @returns string new identigier
     */
    function getNextIdentifier(identifier) {
        if (!identifier) {
            return '';
        }

        var parts = identifier.split('__');
        parts.push(parseInt(parts.pop()) + 1);
        return parts.join('__');
    }

    // run
    $(document).ready(function () {
        init();
    });
})();