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
                    return;
                }
            }
            deadlineDialog.Show()
        }
    });

    $(document).on('click', '#apply_button button.apply', function (e) {
        e.preventDefault();
        var action = $('.main-dropdown.main-grid-panel-control').data('value');
        if (action.indexOf('smartcat_connector_translate_') !== -1) {
            deadlineDialog.Show();
        }
    })

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

        var content = '<table><tr><td>'
                    + 'Контент отправлен на перевод';
                    + '</td></tr></table>';

        deadlineDialog = new BX.CDialog({
            title: 'Создание перевода',
            content: content,
            height: 80,
            width: 350,
            resizable: false,
            buttons: [
                {
                    title: 'Ок',
                    className: 'adm-btn-save',
                    action: function () {
                        this.parentWindow.Close();
                    }
                }
            ]
        })
    }
});

function ShowDeadlineDialog(list, action) {
    BX.ajax({
        url: action,
        method: 'GET'
    });

    deadlineDialog.Show();
}