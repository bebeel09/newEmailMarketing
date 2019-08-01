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
            <h1>FAQ</h1>
        </div>

        <div class="article">
            <div class="title pl-4">
                <h3>Что нужно знать!</h3>
            </div>
            <div class="description pl-4">
                <p class="mb-2">В форме создания новой таблицы клиентов, при задании имени таблицы, следует писать
                    русскими
                    символами, допускаюся тирэ, нижние подчёркивание, числа и буквы. Имя таблицы должно как то
                    охарактеризовывать тему предстоящей рассылки, что бы в дальнейшем не запутаться. Если попытаться ввести что то кроме
                    перечисленных вариантов, будет показано сообщение о допустимых символах и таблица не будет
                    создана.<br>
                    Так же следует выбрать файл формата xls,xlsx,xlm содержащий контактный данные о клиентах. В случае
                    его отсутствия будет выдано соответсвующие сообщение.
                </p>
                <p>В форме "Новая рассылка" следует выбрать название таблицы с контактными данными, указать название
                    шаблона (выбранный шаблон отобразиться в окне ниже), указать адрес отправителя и тему отправляемого
                    письма. При отсутсвии одного из этих пунктов будет показано соответствующие сообщение.</p>
            </div>
        </div>

        <div class="article">
                <div class="title pl-4">
                    <h3>Динамичные шаблоны писем!</h3>
                </div>
                <div class="description pl-4">
                    <p class="mb-2">Для того что бы в письмо при отправке подставлялись персональные данные клиента, необходимо выполнить следущие условия:
                    </p>
                    <ul>
                        <li>Файл шаблона письма должен иметь расширение .blade.php</li>
                        <li>Иметь, при желании, в теле письма такие части кода на соответствующих местах: 
                            <ul>
                                   <li>{\{ $contact->company }\} - название компании клиента, будет вставлено при наличии</li>
                                   <li>{\{ $contact->name }\} - имя клиента, будет вставлено при наличии</li>
                            </ul>
                        </li>
                        <li>Наличие некоторой логики на случай если перечисленные контактные данные окажутся пустыми</li>
                    </ul>
                </div>
            </div>

        <div class="article">
            <div class="title pl-4">
                <h3>Названия колонок в таблицах которые должны быть! </h3>
            </div>
            <div class="description pl-4">
                <p class="mb-2">В таблицах содержащие выгрузку контактных данных о клиентах должны в обязательном
                    порядке присутствовать такие столбцы:</p>
                <ul>
                    <li><b>рабочий email*</b></li>
                    <li><b>наименование</b></li>
                    <li><b>компания</b></li>
                    <li style="font-size: 12px;">* - столбец обязательно должен содержать данные</li>
                </ul>
                <p>Сами названия столбцов должны быть написаны <b>на русском языке</b> . <b>Регистр</b> символов <b>не
                        имеет значения</b> , т.е. если название столбца будет написано как "<b>наИмеНованиЕ</b>", то это
                    <b>допустимый вариант</b>.</p>
                <p>В полях с email-ом могут находиться несколько email адресов, следует проверить что идёт перечесление
                    через запятую, иначе программа не сможет понять что их несколько и создаст одну запсь в бд с
                    некорректным email адресом. </p>
            </div>
        </div>

        <div class="article">
            <div class="title pl-4">
                <h3>Базовые шаблоны форм.</h3>
            </div>
            <div class="description pl-4">
                <p>Так как в Laravel используется шаблонизатор blade то в коде формы можно встерить конструкцию
                    {\{  csrf_field() }\}, такая конструкция используется для генерации token кода для POST запросов.
                    Подробнее об этом <a href="https://laravel.com/docs/5.8/csrf" target="_blank">в документации к
                        фреймворку Laravel</a></p>
                <p>Естественно вам придётся использовать собственные стили офоромления, так как эти шаблоны указаны
                    здесь как работоспособная база.</p>
            </div>

            <div class="description pl-4">
                <p class="mb-2">
                    <h4>Форма "Создания новой таблицы клиентов":</h4>
                </p>
                <code>
                    <xmp>
                        <form class="form" enctype="multipart/form-data" method="POST"
                            action="{{ route('createTableContacts') }}">
                            {{ csrf_field() }}
                            <div class="form-group row">
                                <label for="nameTable" class="col-sm-3 col-form-label">Название новой таблицы</label>
                                <div class="col-sm-9">
                                    <input name="nameTable" id="nameTable" type="text" class="form-control"
                                        placeholder="Название новой таблицы">
                                </div>
                            </div>

                            <div class="form-group d-flex">
                                <label for="exampleFormControlFile1 col-3">Выбирите excel файл с данными о
                                    клиентах</label>
                                <input type="file" name="file" class="form-control-file col-9"
                                    id="exampleFormControlFile1">
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <input type="submit" class="btn btn-success" name="" id="">
                                </div>
                            </div>

                        </form>
                    </xmp>
                </code>
            </div>
            <hr>
            <div class="description pl-4">
                <p class="mb-2">
                    <h4>Форма "Новая рассылка":</h4>
                </p>
                <code>
                    <xmp>
                        <form class="form" method="POST" action="{{ route('sendMail')}}">
                            {\{ csrf_field() }\}
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-3 col-form-label">Выбирите таблицу
                                    клитенов</label>
                                <div class="col-sm-9">
                                    <select name="dbName" change="selectMail" id="inputState" class="form-control">
                                        <\?php
                                            foreach($tablesName as $name ){
                                                 echo "<option>".$name->Tables_in_mailing."</option>";
                                            }
                                        \?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-3 col-form-label">Выбирите шаблон письма для
                                    отправки</label>
                                <div class="col-sm-9">
                                    <select name="templateName" id="selectID" change="selectMail" id="inputState"
                                        class="form-control">
                                        <\?php
                                            foreach($fileArray as $fileMeta ){
                                            $path=route('seeTemplate',$fileMeta);
                                            echo "<option value=".$path.">".$fileMeta."</option>";
                                        }
                                        ?\>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-3 col-form-label">Отправитель</label>
                                <div class="col-sm-9">
                                    <input type="text" name="Sender" class="form-control"
                                        placeholder="aidar@shtorm-its.ru">
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
                                <iframe src="{\{ route('seeTemplate',$fileArray[0]) }\}" id="frame" frameborder="0"
                                    class="col-12" height="1000px"></iframe>
                            </div>

                            <div class="form-group row mt-2 ">
                                <div class="col-sm-10">
                                    <input type="submit" class="btn btn-success " name="" id="" value="Начать рассылку">
                                </div>
                            </div>

                        </form>

                        <script>
                            var selectInput = document.getElementById('selectID');


                            function selectMail() {
                                var iframe = document.getElementById('frame');
                                var selected_mail = selectInput.options[selectInput.selectedIndex].value;
                                iframe.setAttribute('src', selected_mail);
                            }


                            selectInput.addEventListener("change", selectMail);
                        </script>
                    </xmp>
                </code>
            </div>


        </div>
    </div>
</body>

</html>