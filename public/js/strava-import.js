/*
                 * Ajax function to check images with server and db
                 */
$("span#importActivities").on("click", function () {
    $.ajax({
        type: 'POST',
        url: "/beheer/stravaimport/createimportlog",
        async: true,
        success: function (data) {
            if (data.success === true) {
                createActivitiesArray(data.importId);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });


});

/*
 * Create a array to process
 *
 * @return void
 */
function createActivitiesArray(importId) {
    var activitiesArr = [];
    $("input[name='activity']").each(function (index) {

        console.log(index);

        var activityId = $(this).val();
        //Create object
        var activityArr = [];
        activityArr['activityId'] = activityId;
        activityArr['importId'] = importId;
        //Push object into array with index
        activitiesArr[index] = activityArr;
    });

    console.log(activitiesArr);

    processActivitiesArray(activitiesArr);
}

/*
 * Process the given array
 *
 * @return void
 */
function processActivitiesArray(activitiesArr) {

    if (activitiesArr.length > 0) {
        var activityId = activitiesArr[0]['activityId'];
        var importId = activitiesArr[0]['importId'];
        var activitiesArr = $.grep(activitiesArr, function (e) {
            return e.activityId != activityId;
        });

        processActivitiesArrayAjax(activitiesArr, activityId, importId);
    }
}

function processActivitiesArrayAjax(activitiesArr, activityId, importId) {
    $.ajax({
        type: 'POST',
        data: {
            activityId: activityId,
            importId: importId,
            code: '<?= $code ?>'
        },
        url: "/beheer/stravaimport/addactivity",
        async: true,
        success: function (data) {
            if (data.success === true) {
                $('tr#activity-' + data.activityId).remove();
            } else {
                $('tr#activity-' + data.activityId).addClass('bg-danger');
            }
            processActivitiesArray(activitiesArr);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}