<?php

include_once '../config.php';
include_once '../session.php';

session_destroy();
header('Location: login.php');