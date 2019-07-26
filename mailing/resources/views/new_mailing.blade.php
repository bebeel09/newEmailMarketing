@extends('maket.navigation')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="css/main.css">
    <script src="js/build.js"></script>
</head>

<body>
    @section('nav')

    <div class="container background h-100">

        <div class="title p-3 mb-3">
            <h1>Новая рассылка</h1>
        </div>
        <div>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <form class="form" method="POST" action="{{ route('sendMail')}}">
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="inputEmail3" class="col-sm-3 col-form-label">Выбирите таблицу клитенов</label>
                <div class="col-sm-9">
                    <select name="dbName" change="selectMail" id="inputState" class="form-control">
                        <?php
                            foreach($tablesName as $name ){
                                 echo "<option>".$name->Tables_in_mailing."</option>";
                            }
                            ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="inputEmail3" class="col-sm-3 col-form-label">Выбирите шаблон письма для отправки</label>
                <div class="col-sm-9">
                    <select name="templateName" id="selectID" change="selectMail" id="inputState" class="form-control">
                        <?php
                            foreach($fileArray as $fileMeta ){
                                $path=route('seeTemplate',$fileMeta);
                                 echo "<option value=".$path.">".$fileMeta."</option>";
                            }
                            ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="inputEmail3" class="col-sm-3 col-form-label">Отправитель</label>
                <div class="col-sm-9">
                    <input type="text" name="Sender" class="form-control" placeholder="aidar@shtorm-its.ru">
                </div>
            </div>

            <div class="form-group row">
                <label for="inputEmail3" class="col-sm-3 col-form-label">Тема письма</label>
                <div class="col-sm-9">
                    <input type="text" name="Theme" class="form-control"
                        placeholder="Получи самые выгодные предложения первым!!">
                </div>
            </div>

            <div class="frame">
                <iframe src="{{ route('seeTemplate',$fileArray[0]) }}" id="frame" frameborder="0" class="col-12"
                    height="1000px"></iframe>
            </div>

            <div class="form-group row mt-2 ">
                <div class="col-sm-10">
                    <input type="submit" class="btn btn-success " name="" id="" value="Начать рассылку">
                </div>
            </div>

        </form>
    </div>

</body>
<script>
var selectInput = document.getElementById('selectID');


function selectMail() {
    var iframe = document.getElementById('frame');
    var selected_mail = selectInput.options[selectInput.selectedIndex].value;
    iframe.setAttribute('src', selected_mail);
}


selectInput.addEventListener("change", selectMail);
</script>

</html>