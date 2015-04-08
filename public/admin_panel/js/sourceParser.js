/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#saveSource').click(function(){
        var data = {};
        $.each($(this).closest('form').serializeArray(), function() {
            data[this.name] = this.value;
        });
        $.ajax({
            method: 'POST',
            url: "/panel/parser/source",
            data: data,
            complete : function(jqXHR, textStatus ) {
                if(jqXHR.status == 422) {
                    console.log(jqXHR.responseJSON);
                    //todo erros handler
                }
                if(jqXHR.status == 200) {
                    $('#sourceModal').modal('hide');
                    document.location.reload();
                }
            }
        });
    });
    $('#newsList .archive, #newsList .un-archive').click(function(){
        var $item = $(this).closest('tr');
        var data = {id : $(this).attr('data-news-id'), archive: $(this).attr('data-archive')};
        $.ajax({
            method: 'POST',
            url: "/panel/parser/news/toggle-archive",
            data: data,
            complete : function(jqXHR, textStatus ) {
                if(jqXHR.status == 422) {
                    console.log(jqXHR.responseJSON);
                    //todo erros handler
                }
                if(jqXHR.status == 200) {
                    $item.remove();
                }
            }
        });
    });
    $('.addSource, .editSource').click(function(){$('#sourceId').attr('value', $(this).attr('data-source-id'));});
    /*/Replace this peace of this)*/
    $('.editSource').click(function(){
        var $tr = $(this).closest('tr');
        var $modal = $('#sourceModal');
        $modal.find('[name=sourceUri]').val($tr.find('td:eq(1)').html().trim());
        $modal.find('[name=sourceKeywords]').val($tr.find('td:eq(2)').html().trim());
        $modal.find('[name=sourceActive]').attr('checked', parseInt($tr.find('td:eq(3)').html().trim()) ? true : false);
        $modal.find('[name=sourceType][value=' + $tr.find('td:eq(0)').html().trim() + ']')
            .prop('checked', true);
    });
});