<?php
include 'konfig.php';
ob_start();
session_start();
if(isset($_POST['login'])){
    $username=mysql_real_escape_string($_POST['user']);
    $password=md5($_POST['pass']);
    $kueri=mysql_query("select * from userblast where username='$username' and password='$password'");
    $k2=mysql_num_rows($kueri);
    if($k2>0){
        $_SESSION['nusablast_login']=true;
        $_SESSION['nusablast_username']=$username;
    }
    else{
        $_SESSION['nusablast_error']="wrong username or password";
    }
}
if(isset($_POST['kirim'])){
    $pengirim="$_POST[nama] <$_POST[pengirim]>";
    $subjek=$_POST['subjek'];
    $pesan=$_POST['pesan'];
    $sumber=$_POST['sumber'];
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
    $headers .= 'From:'. $pengirim . "\r\n";
    if($_POST['tipe']=="1"){
        $penerima=$_POST['penerima'];
        $subjek2=$subjek."(Laporan Pengiriman) - ".$penerima;
        $kueri=mysql_query("select * from subscribe where email='$penerima'");
        $ex=mysql_num_rows($kueri);
        if($ex=="0"){
            $hash=md5($penerima);
            mysql_query("insert into subscribe (email,hash) values ('$penerima','$hash')");
        }
        $pesan.='<div style="margin:30px;text-align:center"><a href="'.$_POST['sumber'].'">klik disini</a> jika anda kesulitan melihat email ini.</div>';
        mail($penerima,$subjek,$pesan,$headers);
       mail($pengirim, $subjek2, $pesan, $headers);
        mysql_query("insert into blastlog (username,email,message) values ('$_SESSION[nusablast_username]','$penerima','$pesan')");
        $_SESSION['nusablast_sent_success']="success send email";
    }
    else{
        $kueri=mysql_query("select * from subscribe");
        while ($k2=mysql_fetch_array($kueri)) {
            $pesan=$_POST['pesan'];
            $pesan.='<div style="max-width:600px;margin-top:30px;margin:0 auto;">
            <div style="margin-top:20px;padding:30px 20px 30px 20px;color:#fff;background-color:#bdbdbd;text-align:right">
                <a style="background-color:#757575;color:#fff;text-decoration:none;padding:10px 30px 10px 30px;margin-right:10px;" href="'.$_POST['sumber'].'">View in browser</a>
                <a style="background-color:rgba(255,0,0,0.5);color:#fff;text-decoration:none;padding:10px 30px 10px 30px;" href="http:/jkt.nusa.net.id/newsletter/unsubscribes.php?hash='.$k2['hash'].'">Unsubscribe</a>
            </div>
        </div>';
        $pesan.='<div style="margin:30px;text-align:center"><a href="'.$_POST['sumber'].'">klik disini</a> jika anda kesulitan melihat email ini.</div>';
            mail($k2['email'],$subjek,$pesan,$headers);
        }
        mysql_query("insert into blastlog (username,email,message) values ('$_SESSION[nusablast_username]','all db','$pesan')");
        $_SESSION['nusablast_sent_success']="success send email";
    }
}?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">

        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
<?php if(!isset($_SESSION['nusablast_login'])):?>
    <style tyle="text/css">
    body{
        background-color:#e0e0e0;
    }
    #header{
        padding-left:20px;
    }
    .trainingCardContainer{
        padding:15px;
    }
    .footerl{
        text-align:left;
    }
    .footerr{
        text-align:right;
    }
    .signincard{
        width:400px;margin:0 auto;padding:20px;
    }
    @media only screen and (max-width : 992px){
        .footerl{
            text-align:center;
        }
        .footerr{
            text-align:center;
        }
    }
    @media only screen and (min-width : 992px){
        .footerl{
            line-height:35px;
        }


    }
    @media only screen and (max-width : 600px){
        .signInCard{
            width:90%;margin:0 5% 0 5%;
        }
    }
    </style>
</head>
<?php if(isset($_SESSION['nusablast_error'])):?>
<script type="text/javascript">
    $(document).ready(function(){
        var error = "<?php echo $_SESSION['nusablast_error'];?>";
        var $toastContent = $("<span><b>"+error+"</b></span>");
        Materialize.toast($toastContent, 10000);
        $('.toast').css('background-color','red');
    });
</script>
    <?php unset($_SESSION['nusablast_error']); ?>
<?php endif;?>
<body>
    <div class="section grey lighten-2" style="padding:90px 0 100px 0;">
        <div class="card center signInCard">
            <h5 style="font-weight:300;font-size:19px;padding:10px 0 10px; 0">Welcome! Sign In</h5>
            <img src="http://groovy.id/beta/img/default-avatar-groovy.png" width="60px"/>
            <form style="margin:10px 0 0 0;" method="post">
                <input type="text" placeholder="Username" name="user">
                <input type="password" placeholder="Password" name="pass">
                <button type="submit" class="blue btn" style="margin:10px 0 10px 0;" name="login">SIGN IN</button>
            </form>
        </div>
    </div>

</body>
</html>


<?php else :?>
<html>
    <head>
        <title>Mailer Newsletter</title>
        <?php if(isset($_SESSION['nusablast_sent_success'])):?>
        <script type="text/javascript">
            $(document).ready(function(){
                var success = "<?php echo $_SESSION['nusablast_sent_success'];?>";
                var $toastContent = $("<span><b>"+success+"</b></span>");
                Materialize.toast($toastContent, 10000);
                $('.toast').css('background-color','#2bbbad');
            });
        </script>
    <?php unset($_SESSION['nusablast_sent_success']); ?>
<?php endif;?>
        <script>
            $(document).ready(function() {
                $('.modal-trigger').leanModal();
                $('select').material_select();
                $('#errorMessage').html('Pilih salah satu Penerima');
                $('.new').hide();
            });
        </script>
        <style>
            body{
                background-color:#FC592E;
            }


        </style>
    </head>
    <body>

        <div class=""><div class="container" style="max-width:700px;padding:50px 0 50px 0;">
            <div class="row white card" style="text-align:center;padding:10px 0 10px 0 ">
                <h4>Email Blast</h4>
            </div>
            <div class="row white card" style="padding:20px 15px 20px 10px ">
                <div class="input-field col s12 m12 l12">
                    <form method="post">
                        <table>
                            <tr>
                                <td style="vertical-align:middle"><span>Penerima</span></td>

                                <td>
                                    <div class="input-field">
                                        <select name="tipe">
                                          <option value="0" disabled selected>Tipe Blast</option>
                                          <option value="1">Customer Baru</option>
                                          <option value="2">Semua Customer</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>


                            <!--show hide-->
                            <tr class="new">
                                <td style="vertical-align:middle"></td>

                                <td><input id="penerima"  style="padding-left:5px;background-color:#eee;" type="email" placeholder="newcustomer@domain.com" name="penerima" /></td>
                            </tr>
                            <!--show hide-->


                            <tr>
                                <td style="vertical-align:middle"><span>Pengirim</span></td>
                                <td><input name="pengirim" style="padding-left:5px;background-color:#eee;" type="email" placeholder="cs@groovy.id" required/></td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle"><span>Nama Pengirim</span></td>
                                <td><input name="nama" style="padding-left:5px;background-color:#eee;" type="text" placeholder="Customer Service Groovy" required/></td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle"><span>Subjek</span></td>
                                <td><input name="subjek" style="padding-left:5px;background-color:#eee;" type="text" placeholder="Selamat Tahun Baru 2017" required/></td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle"><span>Message</span></td>
                                <td><textarea name="pesan" style="padding-left:5px;background-color:#eee;max-height: 100px;overflow-y: scroll" id="textarea1" class="materialize-textarea" required></textarea></td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle"><span>Sumber Laman</span></td>
                                <td><input name="sumber" style="padding-left:5px;background-color:#eee;" type="text" placeholder="http://groovy.id/newsletter/sdf1d818d/tahun-baru-2017" required/></td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle"><span></span></td>
                                <td><a class="btn modal-trigger kirim waves-effect blue waves-light" href="#alert">Kirim
                                <i class="material-icons right">send</i>
                              </a></td>
                            </tr>
                        </table>
                        <div id="all" class="modal">
                                <div class="modal-content">
                                  <h4>Confirm Send</h4>
                                  <p>Apa anda yakin mengirim ke <font color="red">SEMUA EMAIL CUSTOMER</font> yang ada di database?</p>
                                </div>
                                <div class="modal-footer">
                                  <a class="modal-action modal-close waves-effect waves-green btn-flat">Kembali</a>
                                  <button type="submit" class="modal-action modal-close waves-effect waves-green btn-flat" name="kirim">Kirim</button>
                                </div>
                        </div>
                        <div id="new" class="modal">
                                <div class="modal-content">
                                  <h4>Confirm Send</h4>
                                  <p>Apa anda yakin mengirim EMAIL ke <span id="emailPenerima"></span>?</p>
                                </div>
                                <div class="modal-footer">
                                  <a class="modal-action modal-close waves-effect waves-green btn-flat">Kembali</a>
                                  <button type="submit" class="modal-action modal-close waves-effect waves-green btn-flat" name="kirim">Kirim</button>

                                </div>
                        </div>
                        <div id="alert" class="modal">
                                <div class="modal-content">
                                  <h4>Error</h4>
                                  <p><span id="errorMessage"></span></p>
                                </div>
                                <div class="modal-footer">
                                  <a class="modal-action modal-close waves-effect waves-green btn-flat">Kembali</a>

                                </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript">
        $('select').change(function(){
                if($('select').val()=="1"){
                    $('.new').show();
                    $('.kirim').attr('href','#new');
                    $('#penerima').attr('required',true);
                }
                else if($('select').val()=="2"){
                    $('.kirim').attr('href','#all');
                    $('#penerima').attr('required',false);
                    $('.new').hide();
                }
                else{
                    $('#penerima').attr('required',false);
                    $('.new').hide();
                }
            });
        $("#penerima").bind("change input keyup", function() {
            $("#emailPenerima").html("<font color='red'>"+ $(this).val() +"</font>");
        });
        </script>
    </body>
</html>
<?php endif;?>
