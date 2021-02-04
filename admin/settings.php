<?php
if(!defined('AJAX') && !defined('VAR2')) {
    die('Security');
}

define('VAR3', TRUE);

$app = [
    'name' => 'sliderScript',
    'timeZone' => 'Europe/Istanbul',
    'themeColor' => 'orange'
];

$databaseSettings = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'databaseName' => 'sliderscript'
];

$existPages = [
    'home',
    'slider-contents',
    'slider-settings',
    'add-slider-content',
    'edit-slider-content'
];

$animations = [
    'zoomIn' => 'Yaklaştır',
    'zoomInDown' => 'Aşağı Yaklaştır',
    'zoomInLeft' => 'Soldan Yaklaştır',
    'zoomInRight' => 'Sağdan Yaklaştır',
    'zoomInUp' => 'Yaklaştır',
    'slideInDown' => 'Aşağı Kaydır',
    'slideInLeft' => 'Soldan Kaydır',
    'slideInRight' => 'Sağdan Kaydır',
    'slideInUp' => 'Yukarı Kaydır',
    'fadeIn' => 'Yavaşça Göründür',
    'fadeInDown' => 'Yavaşça Aşağıya Göründür',
    'fadeInLeft' => 'Yavaşça Soldan Göründür',
    'fadeInRight' => 'Yavaşça Sağdan Göründür',
    'fadeInUp' => 'Yavaşça Yukarıya Göründür',
    'bounceIn' => 'Zıplat',
    'bounceInDown' => 'Aşağıya Zıplat',
    'bounceInLeft' => 'Soldan Zıplat',
    'bounceInRight' => 'Sağdan Zıplat',
    'bounceInUp' => 'Yukarıya Zıplat'
];

$fonts = [
    'Roboto' => 'sans-serif',
    'Open Sans' => 'sans-serif',
    'Lato' => 'sans-serif',
    'Slabo 27px' => 'serif',
    'Oswald' => 'sans-serif',
    'Soruce Sans Pro' => 'sans-serif',
    'Montserrat' => 'sans-serif',
    'Raleway' => 'sans-serif',
    'PT Sans' => 'sans-serif',
    'Lora' => 'serif',
    'Nato Sans' => 'sans-serif',
    'Nunito Sans' => 'sans-serif',
    'Concert One' => 'sans-serif',
    'Prompt' => 'sans-serif',
    'Work Sans' => 'sans-serif'
];