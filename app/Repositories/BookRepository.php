<?php

namespace App\Repositories;
use App\Models\Books;
use App\Interfaces\BookRepositoryInterface;

class BookRepository implements BookRepositoryInterface
{
    public function index(){
        return Books::all();
    }
    
    public function getById($id){
        return Books::findOrFail($id);
    }

    public function store(array $data){
        return Books::create($data);
    }

    public function update(array $data, $id){
        return Books::whereId($id)->update($data);
    }

    public function destroy($id){
        Books::destroy($id);
    }

}
