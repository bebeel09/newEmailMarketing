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
use App\Jobs\SendEmailProcess;

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

    public function newEmailTable(Request $request)
    {
        $input = $request->all();

        if($request['nameTable']=="" or $request['file']==""){
            Log::channel('logInfo')->info('При попытке создать базу клиентов произошла ошибка [Не получено имя таблицы или файл с контактами.]');
            die("Ошибка! Не получено имя таблицы или файл с контактами!");
        }

        $nameTable =$this->translit($request['nameTable']);

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
            
        } else{
            Log::channel('logInfo')->info("При попытке создать базу клиентов произошла ошибка. Таблица с именем [{$nameTable}] уже существует");
            die("Ошибка! Операция остановлена. \nТаблица с именем [{$nameTable}] уже существует! Используйте другое название.");
        } 



        $xls = PHPExcel_IOFactory::load($input['file']->getRealPath());
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();

        $nameColumnExcelArray=['рабочий email','наименование', 'компания'];
        $indexColumnExcelArray=[];


        //вычисляем индексы столбцов с наименованиями перечисленными в $nameColumnExcelArray
        for($i=0;$i<PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn()); $i++){
            $excelCellValue=mb_strtolower($sheet->getCellByColumnAndRow($i,1)->getValue());
            if (in_array($excelCellValue, $nameColumnExcelArray)) {
                $indexColumnExcelArray= array_merge($indexColumnExcelArray,[$excelCellValue=>(int)$i]);
            }
        }
        
        //Получаем данные по контактам
        for ($i = 2; $i <= $sheet->getHighestRow(); $i++) {
            $mail_line = $sheet->getCellByColumnAndRow($indexColumnExcelArray['рабочий email'], $i)->getValue();
            $number_mail = explode(", ", $mail_line);

            $company = $sheet->getCellByColumnAndRow($indexColumnExcelArray['компания'], $i)->getValue();
            $name = $sheet->getCellByColumnAndRow($indexColumnExcelArray['наименование'], $i)->getValue();

            //Может быть что один контакт имеет несколько email адресов. отправляем в БД несколько email адресов под одним контактом 
            for ($j = 0; $j < count($number_mail); $j++) {
                $newTable = new contactTables($nameTable);

                $newTable->company = $company;
                $newTable->name = $name;
                $newTable->email = trim($number_mail[$j]);

                $newTable->save();
            }
        }
    }

    public function getMailingPage()
    {

        $tables = DB::select('SHOW TABLES');

        $templateNames = array_diff(scandir(resource_path('views/template'), 1), array('..', '.'));

        for ($i = 0; $i < count($templateNames); $i++) {
            $templateNames[$i] = basename($templateNames[$i], '.blade.php');
        }

        return view('new_mailing', ['tablesName' => $tables, 'fileArray' => $templateNames]);
    }

    public function getTemplate($template_name = null)
    {
        if (view()->exists("template." . $template_name)) {
            return view("template." . $template_name);
        } else die("файл шаблона не найден!");
    }

    public function sendMail(Request $request)
    {
       
        $value = $request->all();
        Log::channel('logInfo')->info("Инициализирована рассылка сообщений. Таблица БД:[{$value['dbName']}], используемый шаблон: [{$value['templateName']}]");
        $contacts = DB::table($value['dbName'])->where('sended',0)->limit(25)->get();
                
            foreach ($contacts as $contact) {
                dump($contact);
              

                //если сообщение отправлено сделать пометку об отпраке в бд
                if(!Mail::send('template.' . basename($value['templateName']), ['contact'=>$contact], function($message) use ($contact) {
                    $message->from('info@hitechsvarka.ru')->to($contact->email)->subject('Приглашаем Вас на Семинар дилеров компании TELWIN в России 2019');
                    
                })){
                    DB::table($value['dbName'])->where('id',$contact->id)->update(['sended'=>1]);
                }
            }
    }
}
