$(document).ready(function () {
    $('button.removeImport').click(function () {
        var importId = $(this).data('importid');
        $('button#removeLog').data('removelogid', importId);
    });

    $('button#removeLog').click(function () {
        var removelogId = $(this).data('removelogid');
console.log(removelogId);
        if (removelogId != '') {
            $.ajax({
                type: 'POST',
                data: {
                    removelogId: removelogId
                },
                url: "/beheer/stravalog/removeImport",
                async: true,
                success: function (data) {
                    if (data.success === true) {
                        $('#confirmationModal').modal('hide')
                        $('tr#importLogItem' + data.removelogId).slideUp("slow", function () {
                            // Animation complete.
                        });
                    } else {
                        alert(data.errorMessage);
                    }
                }
            });
        }
    });

});
