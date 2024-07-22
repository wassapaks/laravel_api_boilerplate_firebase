<?php

namespace App\Repositories;
use App\Models\Books;
use App\Interfaces\BookRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class BookRepository implements BookRepositoryInterface
{
    public function index(){
        return Cache::remember('books', $minutes='60', function()
        {
            return Books::all();
        });
    }
    
    public function getById($id){
        return Cache::remember("books.{$id}", $minutes='60', function() use($id)
        {
            return Books::findOrFail($id);
        });
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
