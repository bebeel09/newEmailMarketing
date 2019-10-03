<?php

namespace App\customClass;

class testClass
{

    var $name, $company, $email;

    function __construct(string $name = "Халитов Айдар Рахимович", string $company="Шторм", string $email="aidar@shtorm-its.ru")
    { 
        $this->name=$name;
        $this->company=$company;
        $this->email=$email;
    }
}

?>