crud.field('type').onChange(function(field) {
    if (field.value === 'short' || field.value === 'recoverable') {
        crud.field('day_hours').show();
        crud.field('every_year').hide();
    } else {
        crud.field('day_hours').hide();
        crud.field('every_year').show();
    }
}).change();
