<?php
namespace controllers;

class IndexController
{
    public function index()
    {
        $blog = new \Models\Blog;
        $blogs = $blog->getNew();

        $user = new \Models\User;
        $users = $user->getActiveUsers();

        view('index.index',['blogs'=>$blogs,'users'=>$users]);
    }
}