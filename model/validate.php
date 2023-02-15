<?php

    function validateLogin($user, $pass)
    {
        if ($user == 'jdoe' && sha1($pass) == '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8') {
            return true;
        } else {
            return false;
        }
    }