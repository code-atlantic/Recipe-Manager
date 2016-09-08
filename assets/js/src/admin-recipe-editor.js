var RCPM_Recipe_Editor;
(function ($, editor_vars) {
    'use strict';
    var vars = editor_vars || {
            ingredients: {},
            measurement_units: {
                singular: {},
                plural: {}
            },
            I10n: {
                phase: 'Phase',
                confirm_delete_phase: 'Are you sure you want to delete this phase?'
            }
        },
        I10n = vars.I10n,
        ingredients = vars.ingredients,
        measurement_units = {
            singular: [],
            plural: []
        };

    $.each(vars.measurement_units.singular, function (key, value) {
        measurement_units.singular.push({
            'key': key,
            'value': value,
            'label': value
        });
    });

    $.each(vars.measurement_units.plural, function (key, value) {
        measurement_units.plural.push({
            'key': key,
            'value': value,
            'label': value
        });
    });

    Number.prototype.pad = function (size) {
        var s = String(this);
        while (s.length < (size || 2)) {
            s = "0" + s;
        }
        return s;
    };

    String.prototype.toDecimal = function () {
        var x = String(this);
        if (x.indexOf('/') != -1) {
            var parts = x.split(" "),
                decParts;

            if (parts.length > 1) {
                decParts = parts[1].split("/");
            }
            else {
                decParts = parts[0].split("/");
                parts[0] = 0;
            }
            return parseInt(parts[0], 10) + (parseInt(decParts[0], 10) / parseInt(decParts[1], 10));
        } else {
            return x;
        }
    };

    // Define globally accessible functions for the recipe editor.
    RCPM_Recipe_Editor = {
        close_phase: function () {
            $('.phases .phase').removeClass('active');
            $('.phases .phase-content').slideUp(300).removeClass('open');
        },
        renumber_ingredients: function ($phase) {
            $phase.find('.recipe-ingredients tbody tr').each(function () {
                var $this = $(this),
                    index = $this.parent().children().index($this),
                    originalIndex = $this.data('ingredientindex');

                $this.data('ingredientindex', index);

                $this.find('input, textarea, select').each(function () {
                    this.name = this.name.replace(/\[ingredients]\[([0-9])*]/, "[ingredients][" + index + "]");
                });

            });
        },
        renumber_steps: function ($phase) {
            $phase.find('.recipe-steps tbody tr').each(function () {
                var $this = $(this),
                    index = $this.parent().children().index($this),
                    originalIndex = $this.data('stepindex');

                $this.data('stepindex', index);

                $this.find('input, textarea, select').each(function () {
                    this.name = this.name.replace(/\[steps]\[([0-9])*]/, "[steps][" + index + "]");
                });

            });
        },
        renumber_ingredients_steps: function ($phase) {
            RCPM_Recipe_Editor.renumber_ingredients($phase);
            RCPM_Recipe_Editor.renumber_steps($phase);
        },
        renumber_phases: function () {
            $('.phases .phase').each(function () {
                var $this = $(this),
                    index = $this.parent().children().index($this),
                    number = index + 1,
                    originalIndex = $this.data('phaseindex');

                $this.data('phaseindex', index);
                $this.find('.phase-title').data('target', '#phase-' + number);
                $this.find('.phase-content').attr('id', 'phase-' + number);

                $this.find('.recipe-ingredients, .recipe-steps').find('input, textarea, select').each(function () {
                    this.name = this.name.replace(/recipe_card\[([0-9])*]/, "recipe_card[" + index + "]");
                });

                RCPM_Recipe_Editor.renumber_ingredients_steps($this);

                RCPM_Recipe_Editor.update_phase_title($this);
            });
        },
        update_phase_title: function ($phase) {
            var $title = $phase.find('.phase-name'),
                phaseNumber = $phase.data('phaseindex') + 1,
                text = I10n.phase + ' ' + phaseNumber;

            if ($title.val() !== '') {
                text = text + ': ' + $title.val();
            }
            $phase.find('.phase-title .title').text(text);
        },
        initialize_sortable_tables: function () {
            // Initialize sortable tables.
            $("table.sortable tbody").filter(':not(.ui-sortable)').sortable({
                handle: '.sort-handle .dashicons-menu',
                placeholder: "ui-state-highlight",
                forcePlaceholderSize: true,
                forceHelperSize: true,
                start: function (e, ui) {
                    ui.placeholder.height(ui.item.outerHeight());
                },
                helper: function (e, tr) {
                    var $originals = tr.children(),
                        $helper = tr.clone();

                    $helper.children().each(function (index) {
                        // Set helper cell sizes to match the original sizes
                        $(this).width($originals.eq(index).outerWidth);
                    });
                    return $helper;
                },
                stop: function () {
                    RCPM_Recipe_Editor.renumber_phases();
                }
            });

        },
        initialize_ingredient_autocomplete: function () {

            $(".ingredient-unit-autocomplete").filter(':not(.ui-autocomplete-input)')
                .autocomplete({
                    source: measurement_units.singular,
                    autoFocus: true,
                    select: function (e, ui) {
                        $(e.target).parent().find('.ingredient-unit').val(ui.item.key);
                    },
                    change: function (e, ui) {
                        var $this = $(e.target),
                            val,
                            measure = $this.parents('tr').find('.ingredient-measure').val().toDecimal(),
                            source = measure <= 1 || measure === '' ? measurement_units.singular : measurement_units.plural;

                        if (ui.item) {
                            val = ui.item.key;
                        } else {
                            val = $.grep(source, function (n) {
                                return (n.value.toLowerCase() === $this.val().toLowerCase());
                            });

                            if (val.length) {
                                val = val[0].key;
                            } else {
                                val = '';
                            }
                        }
                        $this.parent().find('.ingredient-unit').val(val);
                    }
                })
                .on('blur', function () {
                    $(this).trigger('autocompleteselect').trigger('autocompletechange');
                });

            $(".ingredient-label").filter(':not(.ui-autocomplete-input)')
                .autocomplete({
                    minLength: 2,
                    source: ingredients,
                    autoFocus: true,
                    select: function (e, ui) {
                        $(e.target).parent().find('.ingredient-id').val(ui.item.ID);
                    },
                    change: function (e, ui) {
                        var $this = $(e.target),
                            val;

                        if (ui.item) {
                            val = ui.item.ID;
                        } else {
                            val = $.grep(ingredients, function (n) {
                                return (n.value === $this.val());
                            });

                            if (val.length) {
                                val = val[0].ID;
                            } else {
                                val = 0;
                            }

                        }
                        $this.parent().find('.ingredient-id').val(val);
                    }
                })
                .on('blur', function () {
                    $(this).trigger('autocompleteselect').trigger('autocompletechange');
                });
        },
        update_ingredient_measure_pluralily: function ($measure) {
            var $unit_autocomplete = $measure.parents('tr').find('.ingredient-unit-autocomplete'),
                $unit = $measure.parents('tr').find('.ingredient-unit'),
                value = $measure.val().toDecimal();

            if (value > 1) {
                $unit_autocomplete.autocomplete("option", "source", measurement_units.plural);
                $unit_autocomplete.val(vars.measurement_units.plural[$unit.val()]);
            } else {
                $unit_autocomplete.autocomplete("option", "source", measurement_units.singular);
                $unit_autocomplete.val(vars.measurement_units.singular[$unit.val()]);
            }

        },
        convert_hhmm_to_seconds: function (hhmm) {
            var time,
                hours,
                minutes;

            if (typeof hhmm === "string") {
                if (Number(hhmm) == hhmm) {
                    return Number(hhmm);
                } else {
                    time = hhmm.split(':');
                    hours = time[0];
                    minutes = time[1];
                    return (hours * 3600) + (minutes * 60);
                }
            }
            return hhmm;
        },
        convert_seconds_to_hhmm: function (seconds) {
            var value = Number(seconds),
                minutes = parseInt(value / 60) % 60,
                hours = parseInt(value / 3600) % 24;
            return hours.pad() + ':' + minutes.pad();
        },
        update_total_time: function () {
            var prep = RCPM_Recipe_Editor.convert_hhmm_to_seconds($('#prep_time').val()),
                cook = RCPM_Recipe_Editor.convert_hhmm_to_seconds($('#cook_time').val()),
                total = prep + cook;

            $('#total_time').val(RCPM_Recipe_Editor.convert_seconds_to_hhmm(total));
        }
    };

    $(document)
        .on('click', '.phase-title', function (e) {
            // Grab current anchor value
            var $this = $(this),
                currentAttrValue = $this.data('target'),
                $phase = $this.parent();

            if ($phase.is('.active')) {
                RCPM_Recipe_Editor.close_phase();
            } else {
                RCPM_Recipe_Editor.close_phase();

                // Add active class to section title
                $phase.addClass('active');
                // Open up the hidden content panel
                $('.phases ' + currentAttrValue).slideDown(300, function () {
                    $('html, body').animate({
                        scrollTop: $this.offset().top - 100
                    }, 300);
                }).addClass('open');
            }

            e.preventDefault();
            e.stopPropagation();
        })
        .on('click', '.add-phase', function (e) {
            var template = _.template($('script#recipe-card-phase-templ').html()),
                index = $('.phases .phase').length,
                data = {
                    'phaseIndex': index,
                    'phaseNumber': index + 1
                };

            $('.phases .phase:last').after(
                template(data)
            );

            $('.phases .phase:last .phase-title').trigger('click');

            $('.phases .phase:last .phase-name').trigger('focus');

            RCPM_Recipe_Editor.renumber_phases();

            RCPM_Recipe_Editor.initialize_sortable_tables();

            RCPM_Recipe_Editor.initialize_ingredient_autocomplete();

            e.preventDefault();
            e.stopPropagation();
        })
        .on('click', '.remove-phase', function (e) {
            if (window.confirm(I10n.confirm_delete_phase)) {
                $(this).parents('.phase:first').slideUp(300, function () {
                    $(this).remove();
                });
            }

            e.preventDefault();
            e.stopPropagation();
        })
        .on('click', '.add-ingredient, .add-step', function (e) {
            var $this = $(this),
                $phase = $this.parents('.phase'),
                template,
                data = {
                    'phaseIndex': $phase.data('phaseIndex')
                };

            if ($this.hasClass('add-step')) {
                template = _.template($('script#recipe-card-step-templ').html());
            } else if ($this.hasClass('add-ingredient')) {
                template = _.template($('script#recipe-card-ingredient-templ').html());
            }

            $this.parents('tr:first').after(
                template(data)
            );

            $this.parents('tr:first').next().find('input, select, textarea').eq(0).trigger('focus');

            RCPM_Recipe_Editor.initialize_ingredient_autocomplete();

            RCPM_Recipe_Editor.renumber_phases();

            e.preventDefault();
            e.stopPropagation();
        })
        .on('click', '.remove-ingredient, .remove-step', function (e) {
            var $this = $(this);

            $this.parents('tr:first').remove();

            RCPM_Recipe_Editor.renumber_phases();

            e.preventDefault();
            e.stopPropagation();
        })
        .on('input', '.phase-name', function () {
            RCPM_Recipe_Editor.update_phase_title($(this).parents('.phase'));
        })
        .on('keypress', '.phase table input,.phase table select,.phase table textarea', function (e) {
            if (e.keyCode === 13 || e.keyCode === 10) {
                if (e.ctrlKey) {
                    $(e.target).parents('tr:first').find('.add-ingredient, .add-step').eq(0).trigger('click');
                }
                if (!$(e.target).is('textarea')) {
                    e.preventDefault();
                }

            }
        })
        .on('keyup', '.phase textarea', function () {
            var $this = $(this);
            while ($this.outerHeight() < this.scrollHeight + parseFloat($this.css("borderTopWidth")) + parseFloat($this.css("borderBottomWidth"))) {
                $this.height($this.height() + 1);
            }
        })
        .on('blur', '.ingredient-measure', function () {
            RCPM_Recipe_Editor.update_ingredient_measure_pluralily($(this));
        })
        .on('blur', '#prep_time, #cook_time', function (e) {
            if ($(e.target).attr('id') !== 'total_time') {
                RCPM_Recipe_Editor.update_total_time();
            }
        })
        .ready(function () {

            // Open first phase on page load.
            $('.phases .phase:first').addClass('active').find('.phase-content').addClass('open').slideDown(0);

            // Renumber phases on page load.
            RCPM_Recipe_Editor.renumber_phases();

            // Initialize sortable tables.
            RCPM_Recipe_Editor.initialize_sortable_tables();

            // Initialize ingredient autocomplete.
            RCPM_Recipe_Editor.initialize_ingredient_autocomplete();

            // Initialize sortable phases.
            $(".phases").sortable({
                handle: '.phase-title .dashicons-menu',
                placeholder: "ui-state-highlight",
                forcePlaceholderSize: true,
                start: function (e, ui) {
                    ui.placeholder.height(ui.item.outerHeight());
                },
                stop: function () {
                    RCPM_Recipe_Editor.renumber_phases();
                }
            });

            // Auto size textarea.
            $('.phase textarea').each(function () {
                var $this = $(this);
                while ($this.outerHeight() < this.scrollHeight + parseFloat($this.css("borderTopWidth")) + parseFloat($this.css("borderBottomWidth"))) {
                    $this.height($this.height() + 1);
                }
            });

            $.widget("ui.timespinner", $.ui.spinner, {
                options: {
                    // minutes
                    step: 60,
                    // hours
                    page: 60,
                    min: 0,
                    max: 86400
                },
                _parse: function (value) {
                    return RCPM_Recipe_Editor.convert_hhmm_to_seconds(value);
                },
                _format: function (value) {
                    return RCPM_Recipe_Editor.convert_seconds_to_hhmm(value);
                }
            });


            $('.timespinner').timespinner({
                stop: function (e) {
                    if ($(e.target).attr('id') !== 'total_time') {
                        RCPM_Recipe_Editor.update_total_time();
                    }
                },
                spin: function (e) {
                    if ($(e.target).attr('id') !== 'total_time') {
                        RCPM_Recipe_Editor.update_total_time();
                    }
                }
            });

        });
}(jQuery, rcpm_recipe_editor_vars));