@section('nav')
    <header>
        <!-- Start navigation -->
        <input type="checkbox" id="nav-toggle">
        <nav class="d-flex flex-column nav">
            <label for="nav-toggle" class="nav-toggle" onclick></label>
            <h4 class="nav__logo mt-5 ml-2 mr-2">
                <p>ШТОРМ.рассылка</p>
            </h4>

            <ul>
                    <a href="{{ route('seeFormContacts') }}" ><li>Создать новую таблицу контактов </li></a>
                    <a href="{{ route('new_mailing') }}"><li>Новая рассылка  </li></a>
                    <a href=""> <li>Настройки</li></a>
                    <a href=""><li>Элемент меню</li></a>
            </ul>
        </nav>
        <!-- End navigation -->
    </header>
@show
