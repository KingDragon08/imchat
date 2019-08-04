window.header = {
    type: 'header',
    name: 'header',
    style: {background: '#7dbcea', color: '#fff', fontSize: '24px'},
    content: 'Horse',
    afterCreate: function() {
        UF.ajax({
            url: '/wx/getUserInfo',
            success: function(data) {
                UF('header').set({
                    content: 'Horse' + ' - ' + data.name + '【' + data.puid + '】'
                });
            }
        });
    }
}