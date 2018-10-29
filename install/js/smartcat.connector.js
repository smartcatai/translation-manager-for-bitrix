var items = [];

var deadlineDialog = false;
var deadlineList = false;
var deadlineAction = false;

$(function () {

    $(document).on('click', '.adm-list-table-footer [name=apply]', function (e) {
        var action = $('select[name=action]').val();
        if (action.indexOf('smartcat_connector_translate_') !== -1) {
            var profile = action.replace('smartcat_connector_translate_', '');
            var existedItems = [];

            $('.adm-list-table input[type=checkbox]:checked').each(function () {
                if (adminListTranslate[profile] && adminListTranslate[profile].indexOf($(this).val()) !== -1) {
                    existedItems.push($(this).val());
                }
            });

            if (existedItems.length > 0) {
                if (!confirm('По элементам ' + existedItems.join(', ') + ' и указанному языку уже есть задания перевода. Создать повторные задания?')) {
                    e.preventDefault();
                }
            }
        }
    });


    if (!deadlineDialog) {
        var today = new Date();
        today.setDate(today.getDate() + 3);
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10)
            dd = '0' + dd;

        if (mm < 10)
            mm = '0' + mm;

        today = dd + '.' + mm + '.' + yyyy;

        var content = '<table><tr><td>';
        content = content + 'Укажите дедлайн: ';
        content = content + '</td><td>';
        content = content + '<input id="DEADLINE" type="text" value="" name="date" onclick="BX.calendar({node: this, field: this, bTime: true, bHideTime: false});">';
        content = content + '</td></td></table>';

        deadlineDialog = new BX.CDialog({
            title: 'Создание заказа',
            content: content,
            height: 80,
            width: 350,
            resizable: false,
            buttons: [
                {
                    title: 'Создать',
                    className: 'adm-btn-save',
                    action: function () {
                        var action = deadlineAction;
                        action += '&deadline=' + document.getElementById('DEADLINE').value;
                        BX.ajax({
                            url: action,
                            method: 'GET'
                        });
                        this.parentWindow.Close();
                    }

                },
                BX.CAdminDialog.btnCancel
            ]
        })
    }
});

function ShowDeadlineDialog(list, action) {
    deadlineList = list;
    deadlineAction = action;

    deadlineDialog.Show();
}