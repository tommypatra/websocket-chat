<!DOCTYPE html>
<html>
<head>
    <title>Obrolan</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
    .itsme {
        text-align: right;
    }
    .notme {
        text-align: left;
    }

</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://kit.fontawesome.com/279be85252.js" crossorigin="anonymous"></script>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('chat') }}"><i class="fa-regular fa-comments"></i> Obrolan Sederhana</a>
        <div class="navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('chat') }}"><i class="fa-solid fa-house-signal"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:;" onclick="modalGrupBaru();"><i class="fa-solid fa-users-rectangle"></i> Grup Baru</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}"><i class="fa-solid fa-power-off"></i> Keluar</a>
                </li>
            </ul>
        </div>

        <div class="d-flex">
            <form id="cari">
                {{-- <input class="form-control me-2" type="search" placeholder="Cari" aria-label="Cari"> --}}
                <select class="form-control" name="teman" style="width:200px" id="teman" required></select>
            </form>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <!-- Daftar Kontak (User) di Sebelah Kiri -->
        <div class="col-md-12">
            Selamat datang, {{ auth()->user()->name }}
        </div>
            <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-clipboard-list"></i> Daftar Kontak/ Grup
                </div>
                <ul class="list-group list-group-flush" id="daftar-data">
                </ul>
            </div>
        </div>
        <!-- Container Obrolan di Sebelah Kanan -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header" id="judul-obrolan">
                    <i class="fa-regular fa-comments"></i> Obrolan
                </div>
                <div class="card-body">
                    <!-- Daftar Chat -->
                    <div class="chat-messages" style="max-height: 350px; overflow-y: scroll;">                        
                        <!-- Tambahkan pesan obrolan lainnya sesuai kebutuhan -->
                    </div>

                    <!-- Isian Pesan -->
                    <div class="input-group mt-3">
                        <input type="text" class="form-control" id="isi-pesan" placeholder="Ketik pesan...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" id="kirim-pesan" onclick="kirimPesan();"><i class="fa-regular fa-paper-plane"></i> Kirim</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- MULAI MODAL -->
<div class="modal fade modal" tabindex="-1" role="dialog" id="myModal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <form id="myForm">
            @csrf
            <input type="hidden" name="id" id="id" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-label">Form Grup</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body ">
                    <div class="row">
						<div class="col-lg-12 mb-3">
                            <label class="form-label">Nama Grup</label>
                            <input name="grup" id="grup" type="text" class="form-control" required>
                        </div>
						<div class="col-lg-12 mb-3">
                            <label class="form-label">Cari Akun</label>
                            <select class="form-control" name="users[]" style="width:100%" id="users" required multiple></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- AKHIR MODAL -->
<audio id="notificationSound" src="{{ asset('notif.mp3') }}"></audio>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="{{ asset('js/app.js') }}"></script>
<script>
    var vUserId={{ $user->id }};
    var vUserIdObrolan;
    var vRuangObrolanId;
    var vJoinChannels=[];
    var usersInChannel=[];

    function showNotif(user_id){
        var endpoint = '{{ route("notif", ["user_id" => ":user_id1"]) }}';
        $.get(endpoint.replace(':user_id1', user_id), function(respon) {
            // if(respon.length>0){
            updateNotif(respon);
            // }
        });
    }

    function updateStatus(users){
        $('#daftar-data li').each(function() {
            var $statusUser = $(this).find('.status-user');
            var tmpId=$(this).attr('data-id');
            $statusUser.html(`<span class="badge badge-pill badge-danger">offline</span>`);
            users.forEach(function(user) {
                if(user.id==tmpId){
                    $statusUser.html(`<span class="badge badge-pill badge-success">online</span>`);
                }
            });        
        });
        showNotif(vUserId);
    }

    function updateNotif(data){
        // console.log('update notif')
        $('#daftar-data li').each(function() {
            var $notifEl = $(this).find('.notif-data');
            var tmpId=$(this).attr('data-id');
            var type=$(this).attr('data-type');
            $notifEl.html('');
            data.forEach(function(notif) {
                
                if(notif.user_id==tmpId && type==notif.type){
                    $notifEl.html(`<span class="badge badge-pill badge-danger">${notif.jumlah}</span>`);
                }
            });        
        });
    }

    //untuk channel per user agar dapat notif
    joinChannelUserNotif(vUserId);
    function joinChannelUserNotif(user_id){
        let tmpUserChannelName='notif.user.'+user_id;
        Echo.private(tmpUserChannelName)
            .listen('NotifUserEvent', (e) => {
                console.log(e);
                if(e.user_id==vUserId){
                    switch(e.event) {
                        case 'notif':
                            showNotif(e.user_id);
                            break;
                        case 'grupbaru':
                            contactAppend(e.data);
                            break;
                        default:
                            alert('tidak ada aksi');
                    }
                }
            });    
    }

    //untuk listen general status aktif atau tidak aktif saja
    if(!vJoinChannels['status.user']){
        Echo.join(`status.user`)
            .here((users)=>{
                vJoinChannels['status.user']=true;
                usersInChannel = users;
                updateStatus(usersInChannel);
            })
            .joining((user)=>{
                usersInChannel.push(user);
                updateStatus(usersInChannel);
            })
            .leaving((user)=>{
                usersInChannel = usersInChannel.filter(u => u.id !== user.id);
                updateStatus(usersInChannel);
            })
            .listen('AktifEvent', (e) => {
                showNotif(vUserId); 
            });    
    }

    var vEnpTeman = '{{ route("get.all", ["user_id" => ":user_id"]) }}';
    $('#users').select2({
        placeholder: 'Pilih pengguna', 
        minimumInputLength: 2,
        ajax: {
            url: vEnpTeman.replace(':user_id', vUserId),
            dataType: 'json',
            delay: 250,
            type:'get',
            data: function(params) {
                return {
                    cariuser: params.term, 
                };
            },
            processResults: function(data) {
                var formattedData = data.map(function(user) {
                    return { id: user.id, text: user.name };
                });

                return {
                    results: formattedData
                };            
            },
            cache: true
        }
    });

    $('#teman').select2({
        placeholder: 'cari dan pilih user', 
        minimumInputLength: 2,
        ajax: {
            url: '{{ route("cari.orang") }}',
            dataType: 'json',
            delay: 250,
            type:'post',
            data: function(params) {
                return {
                    cari: params.term, 
                    user_id: vUserId, 
                };
            },
            processResults: function(data) {
                var formattedData = data.map(function(user) {
                    return { id: user.id, text: user.name };
                });

                return {
                    results: formattedData
                };            
            },
            cache: true
        }
    });

    $('#teman').on('select2:select', function (e) {
        var selectedData = e.params.data;
        var userId = selectedData.id;
        var username = selectedData.text;

        if(confirm("yakin akan menjadikan sebagai teman ?")){
            $.post("{{ route('jadikan.teman') }}",
            {
                user_id1: vUserId,
                user_id2: userId
            },
            function(respon, status){
                if(respon.status){
                    loadFriends();
                    showNotif(vUserId);
                }
            });                        
        }
        $('#teman').val(null).trigger('change');
    });

    $("#myForm").submit(function(e){
        e.preventDefault();
        $.ajax({
            url : '{{ route("grup.simpan") }}',
            type : 'POST',
            data : $(this).serialize(),
            dataType:'json',
            success : function(respon) {
                if(respon.status){  
                    contactAppend(respon.data);
                    $('#myModal').modal('toggle');
                }
            }
        });            

    })


    // Tangani perubahan nilai pada Select2
    $('#selectUser').on('change', function() {
        var selectedValues = $(this).val(); // Mendapatkan nilai yang dipilih
        console.log('Pengguna yang dipilih: ', selectedValues);
    });

    defObrolan();

    function scrollToBottom() {
        var chatMessages = document.querySelector(".chat-messages");
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    scrollToBottom();

    loadFriends();

    function contactAppend(alldata){
        var vicon=`<i class="fa-regular fa-user"></i>`;
        var vfunction='cekRuangObrolanUser';
        var vstatus=`
            <div class="status-user"><span class="badge badge-pill badge-danger">offline</span></div>
        `;
        alldata.forEach(function(data) {
            if(data.type!=='user'){
                vicon=`<i class="fa-solid fa-people-group"></i>`;
                vfunction='cekRuangObrolanGrup';
                vstatus=``;
            }
            $('#daftar-data').append(`
                <li class="list-group-item" data-id="${data.id}" data-type="${data.type}">
                    ${vicon}
                    <a href="javascript:;" onClick="${vfunction}(${data.id},'${data.name}');">
                        ${data.name}
                    </a>                     
                    <span class="notif-data"></span>
                    ${vstatus}                        
                </li>
            `);
        });        
    }

    function loadFriends(){
        var endpoint = '{{ route("get.all", ["user_id" => ":user_id"]) }}';
        $("#daftar-data li").remove();
        $.get(endpoint.replace(':user_id', vUserId), function(alldata) {
            contactAppend(alldata);    
        });        
    }

    $("#isi-pesan").on("keydown", function (event) {
        if (event.key === "Enter") {
            kirimPesan();
        }
    });

    function kirimPesan(){
        var isiPesan=$("#isi-pesan").val();
        if(isiPesan!=='' && vUserIdObrolan!==''){
            $.post("{{ route('kirim.obrolan') }}",
            {
                ruang_obrolan_id: vRuangObrolanId,
                user_id: vUserId,
                user_id_obrolan:vUserIdObrolan,
                pesan: $("#isi-pesan").val()
            },
            function(respon, status){
                if(respon.obrolan_id>0){
                    $("#isi-pesan").val("");
                }
            });
            scrollToBottom();
        }               
    }

    function defObrolan(){
        $(".chat-messages").empty();
        $(".chat-messages").html(`<div class="message">tidak ada obrolan</div>`);
    }

    function modalGrupBaru(){
        $('#myForm')[0].reset();
        $('#id').val('');
        $('#users').val(null).trigger('change');
        $("#myModal").modal('show');
    }

    function updateStatusBaca(user_id,ruang_obrolan_id){
        $.post("{{ route('update.status.baca') }}",
        {
            user_id: user_id,
            ruang_obrolan_id: ruang_obrolan_id
        },
        function(respon, status){
            var jumUpd = parseInt(respon.data, 0);
            if(jumUpd>0){
                // console.log('cari notif setelah diupdate')
                showNotif(user_id);
            }
        });               
        
    }

    function initOborlan(ruang_obrolan_id){
        defObrolan();
        var endpoint = '{{ route("daftar.obrolan", ["ruang_obrolan_id" => ":ruang_obrolan_id"]) }}';
        $.get(endpoint.replace(':ruang_obrolan_id', ruang_obrolan_id), function(respon) {
            if (respon.length > 0) {
                $(".chat-messages").empty();
                respon.forEach(function(obrolan) {
                    var userName = 'Anda';
                    var cssClass='itsme';
                    if (obrolan.user_id !== vUserId){
                        userName = obrolan.user.name;
                        cssClass='notme';
                    }
                    $(".chat-messages").append(`<div class='message ${cssClass} ml-2 mr-2'>${userName}: ${obrolan.pesan}</div>`);
                });
                scrollToBottom();
                updateStatusBaca(vUserId,ruang_obrolan_id);
            }
        });
    }

    function cekRuangObrolanUser(user_id,nama){
        $("#judul-obrolan").html("Obrolan dengan <b>akun "+nama+"</b>");
        vUserIdObrolan=user_id;
        joinChannelUserNotif(user_id);
        
        $.post("{{ route('cari.ruang.obrolan') }}",
        {
            user1: user_id,
            user2: vUserId
        },
        function(respon, status){
            vRuangObrolanId=respon.ruang_obrolan_id;
            initOborlan(vRuangObrolanId);
            let channelName=`ruang.obrolan.${vRuangObrolanId}`;
            if(!vJoinChannels[channelName])
                Echo.join(`ruang.obrolan.${vRuangObrolanId}`)
                    .here((users)=>{
                        vJoinChannels
                        vJoinChannels[channelName]=true;
                    })
                    .listen('ChatEvent', (e) => {
                        console.log('obrolan listen');
                        var userName='Anda';
                        var cssClass='itsme';
                        if(e.user.id!==vUserId){
                            userName=e.user.name;              
                            cssClass='notme';              
                        }
                        $(".chat-messages").append(`<div class='message ${cssClass} ml-2 mr-2'>${userName}: ${e.message}</div>`);
                        scrollToBottom();
                        updateStatusBaca(vUserId,vRuangObrolanId);
                    });

        });               
    }

    function cekRuangObrolanGrup(user_id,nama){
        $("#judul-obrolan").html("Obrolan dalam <b>grup "+nama+"</b>");
        vUserIdObrolan=user_id;
        joinChannelUserNotif(user_id);
        
        $.post("{{ route('cari.ruang.obrolan') }}",
        {
            user1: user_id,
            user2: vUserId
        },
        function(respon, status){
            vRuangObrolanId=respon.ruang_obrolan_id;
            initOborlan(vRuangObrolanId);
            let channelName=`ruang.obrolan.${vRuangObrolanId}`;
            if(!vJoinChannels[channelName])
                Echo.join(`ruang.obrolan.${vRuangObrolanId}`)
                    .here((users)=>{
                        vJoinChannels
                        vJoinChannels[channelName]=true;
                    })
                    .listen('ChatEvent', (e) => {
                        console.log('obrolan listen');
                        var userName='Anda';
                        var cssClass='itsme';
                        if(e.user.id!==vUserId){
                            userName=e.user.name;              
                            cssClass='notme';              
                        }
                        $(".chat-messages").append(`<div class='message ${cssClass} ml-2 mr-2'>${userName}: ${e.message}</div>`);
                        scrollToBottom();
                        updateStatusBaca(vUserId,vRuangObrolanId);
                    });

        });               
    }

</script>

</body>
</html>
