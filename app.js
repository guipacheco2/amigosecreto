var api = {};

function Amigo(){}

Amigo.prototype = {
    getAmigos : function(){
        $.ajax({
            type: 'GET',
            url: 'listar-amigos',
            dataType: 'json'
        }).done(function(data){
            api.html.listarAmigos(data);
        });
    },

    sortear : function(){
        var sorteou = $(this).attr('id');
        $.ajax({
            type: 'POST',
            url: 'sortear-amigo',
            data: { sorteou : sorteou },
            dataType: 'json'
        }).done(function(data){
            api.html.data = data;
            api.html.exibirMsg();
            api.html.destacarAmigo();
        });
    }
};

function Html(){}

Html.prototype = {
    listarAmigos : function(amigos){
        var lista = document.createElement('ul');

        for (var i in amigos) {
            foto = $(document.createElement('img')).attr('src', amigos[i].foto);
            nome = $(document.createElement('span')).html(amigos[i].nome);
            item = $(document.createElement('li')).attr('id', amigos[i].id).append(foto).append(nome);
            $(lista).append(item);
        }
        $('.lista').append(lista);
    },

    exibirMsg : function(){
        $('h1').html(this.data.msg);
    },

    destacarAmigo : function(){
        $('li').hide('fast');
        if(this.data.erro === false){
            $(document.getElementById(this.data.amigo)).show('slow');
        }
    }

};

$(function() {
    api.amigo = new Amigo();
    api.html = new Html();

    $('body').on("dblclick", "li", api.amigo.sortear);

    api.amigo.getAmigos();
});