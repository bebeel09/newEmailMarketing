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
            <h1>Создать новую таблицу клиентов</h1>
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


        <form class="form" enctype="multipart/form-data" method="POST" action="{{ route('createTableContacts') }}">
        {{ csrf_field() }}
            <div class="form-group row">
                <label for="nameTable" class="col-sm-3 col-form-label">Название новой таблицы</label>
                <div class="col-sm-9">
                    <input name="nameTable" id="nameTable" type="text" class="form-control" placeholder="Название новой таблицы">
                </div>
            </div>

            <div class="form-group d-flex">
                <label for="exampleFormControlFile1 col-3">Выбирите excel файл с данными о клиентах</label>
                <input type="file" name="file" class="form-control-file col-9" id="exampleFormControlFile1">
            </div>

            <div class="form-group row">
                <div class="col-sm-10">
                    <input type="submit" class="btn btn-success" name="" id="">
                </div>
            </div>

        </form>
    </div>
</body>

</html>