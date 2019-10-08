<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use App\contactTables;
use DB;
use Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\spamMailing;
use App\customClass\testClass;

class emailController extends Controller
{

    private  function translit($value){
        $converter = array(
            'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
            'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
            'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
            'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
            'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
            'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
            'э' => 'e',    'ю' => 'yu',   'я' => 'ya',

            'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',    'Д' => 'D',
            'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',   'З' => 'Z',    'И' => 'I',
            'Й' => 'Y',    'К' => 'K',    'Л' => 'L',    'М' => 'M',    'Н' => 'N',
            'О' => 'O',    'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
            'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',    'Ч' => 'Ch',
            'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => '',     'Ы' => 'Y',    'Ъ' => '',
            'Э' => 'E',    'Ю' => 'Yu',   'Я' => 'Ya',   ' ' => '_',
        );

        $value = strtr($value, $converter);
        return $value;
    }

    public function newEmailTable(Request $request){
        $request->validate([
            'nameTable'=>'alpha_dash|required',
            'file'=>'file|mimes:xls,xlsx,xlm'
        ]);

        $input = $request->all();

        if ($request['nameTable'] == "" or $request['file'] == "") {
            Log::channel('logInfo')->info('При попытке создать базу клиентов произошла ошибка: [Не получено имя таблицы или файл с контактами.]');
            die("Ошибка! Не получено имя таблицы или файл с контактами!");
        }

        $nameTable = $this->translit($request['nameTable']);

        if (!Schema::hasTable($nameTable)) {

            Schema::create($nameTable, function ($table) {
                $table->bigIncrements('id');
                $table->string('company')->nullable();
                $table->string('name')->nullable();
                $table->string('email')->unique();
                $table->boolean('sended')->default('0');
                $table->timestamps();
            });
            Log::channel('logInfo')->info("Создана таблица клиентов: [{$nameTable}]");
        } else {
            Log::channel('logInfo')->info("При попытке создать таблицу клиентов произошла ошибка. Таблица с именем [{$nameTable}] уже существует");
            die("Ошибка! Операция остановлена. \nТаблица с именем [{$nameTable}] уже существует! Используйте другое название.");
        }



        $xls = PHPExcel_IOFactory::load($input['file']->getRealPath());
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();

        //Здесь указываем какие столбцы мы хотим взять из excel таблицы
        $nameColumnExcelArray = ['рабочий email', 'наименование', 'компания'];
        
        //Здесь будут храниться ассоциативный массив с навазнием столбца и его индексом
        $indexColumnExcelArray = [];


        //вычисляем индексы столбцов с наименованиями перечисленными в $nameColumnExcelArray
        for ($i = 0; $i < PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn()); $i++) {
            $excelCellValue = mb_strtolower($sheet->getCellByColumnAndRow($i, 1)->getValue());
            if (in_array($excelCellValue, $nameColumnExcelArray)) {
                $indexColumnExcelArray = array_merge($indexColumnExcelArray, [$excelCellValue => (int) $i]);
            }
        }

        //Получаем данные по контактам
        for ($i = 2; $i <= $sheet->getHighestRow(); $i++) {
            $mail_line = $sheet->getCellByColumnAndRow($indexColumnExcelArray['рабочий email'], $i)->getValue();
            $number_mail = explode(",", preg_replace('/\s/', '', $mail_line));

            $company = $sheet->getCellByColumnAndRow($indexColumnExcelArray['компания'], $i)->getValue();
            $name = $sheet->getCellByColumnAndRow($indexColumnExcelArray['наименование'], $i)->getValue();

            if ($mail_line != "") {
                //Может быть что один контакт имеет несколько email адресов. отправляем в БД несколько email адресов под одним контактом 
                for ($j = 0; $j < count($number_mail); $j++) {
                    try {

                        $newTable = new contactTables($nameTable);
                        $newTable->company = $company;
                        $newTable->name = $name;
                        $newTable->email = trim($number_mail[$j]);
                        if (filter_var(trim($number_mail[$j]), FILTER_VALIDATE_EMAIL) !== false) {
                            $newTable->save();
                        }
                    } catch (QueryException $e) {
                        // nothing
                    }
                }
            }
        }
    }

    public function getMailingPage(){

        //получаем список названий таблиц с клиентами без служебных таблиц (jobs, migrtions) 
        $tables = DB::select("select `TABLE_NAME` as 'Tables_in_mailing' from (SELECT `TABLE_NAME` FROM `information_schema`.`TABLES` WHERE `TABLES`.`TABLE_SCHEMA` = 'mailing') as name WHERE (`TABLE_NAME` != 'jobs' and `TABLE_NAME` !='migrations')");

        //получаем массив навазний файлов шаблонов и убиараем в нём лишние элементы
        $templateNames = array_diff(scandir(resource_path('views/template'), 1), array('..', '.'));

        //убираем из названий шаблонов их расширения
        for ($i = 0; $i < count($templateNames); $i++) {
            $templateNames[$i] = basename($templateNames[$i], '.blade.php');
        }

        return view('new_mailing', ['tablesName' => $tables, 'fileArray' => $templateNames]);
    }

    public function getTemplate($template_name = null){
        $contact=new testClass();

        if (view()->exists("template." . $template_name)) {
            return view("template." . $template_name, compact('contact'));
        } else die("файл шаблона не найден!");
    }

    public function sendMail(Request $request) {
        $request->validate([
            'Sender'=>'required|email',
            'Theme'=>'required|string'
        ]);

        $value = $request->all();

        $sender = $value['Sender'];
        $titleMail = $value['Theme'];

        #Новая фича, выбор региона и время перерыва отправки, не забыть изменить делитель у index
        // $when = now('asia/yekaterinburg')->addMinutes(20);
        $when = now('asia/yekaterinburg');

        Log::channel('logInfo')->info("Инициализирована рассылка сообщений. Таблица БД:[{$value['dbName']}], используемый шаблон: [{$value['templateName']}], Тема сообщений: [{$titleMail}], Отправитель: [{$sender}];");

        $contacts = DB::table($value['dbName'])->where('sended', 0)->get();
        if (count($contacts)==0){
            Log::channel('logInfo')->info("Всем в таблице [{$value['dbName']}] уже были отправлены сообщения.");
            die("По данным клиентов из таблицы [{$value['dbName']}] уже были отправлены сообщения.");
        }
        $lastWhen=$when->addMinutes((count($contacts)/20)*20);
        echo("Первая пачка сообщений будет отправлена в {$when}, последняя в {$lastWhen}");


        #____________Новая фича (Кому отправить тестовое пиьсмо?)_______________

        // Mail::to("aidar@shtorm-its.ru")->later($when, new spamMailing($sender, basename($value['templateName']), $contact, $titleMail));

        #_________________________________________END__________________________________________

        
        
        $index = 1;
        foreach ($contacts as $contact) {
            if ($index % 20 == 0) {
                $when = $when->addMinutes(20);
            }

            //это для тестов, раскоментировать в случае дебага по какой нибудь херне и закоментить отправку которая идёт в очередь
        //    Mail::to($contact)->send( new spamMailing($sender, basename($value['templateName']),$contact , $titleMail));

          //сообщение отправлено в очередь, сделать пометку об отпраке 
            if (Mail::to($contact)->later($when, new spamMailing($sender, basename($value['templateName']), $contact, $titleMail))) {
                DB::table($value['dbName'])->where('id', $contact->id)->update(['sended' => 1]);
            }
            $index++;
        }
        Log::channel('logInfo')->info("Отправлено сообщений в очередь: {$index}");
    }
}
