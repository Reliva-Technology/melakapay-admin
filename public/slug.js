$('.agency_id').on('change', function(){
    $('#slug').val($('.agency_id option:selected').text());
});