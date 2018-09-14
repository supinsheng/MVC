<?php
namespace controllers;

class IndexController
{
    public function index()
    {
        $blog = new \Models\Blog;
        $blogs = $blog->getNew();
        view('index.index',['blogs'=>$blogs]);
    }
}