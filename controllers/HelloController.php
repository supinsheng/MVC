<?php

    namespace controllers;

    use Models\Hello;

    class HelloController {

        public function hello(){

            $hello = new Hello;

            $name = $hello->sayHello();

            view("hello.index",['name'=>$name]);
        }
    }
