<?php
require_once dirname(__FILE__).'/init.php';

//przekierowanie przeglądarki klienta do akcji 'personList'
//redirectTo('personList');

//przekazanie żądania do obsługi akcji 'personList'
forwardTo('personList');