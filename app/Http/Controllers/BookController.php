<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Interfaces\BookRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Policies\BookPolicy as Book;

class BookController extends Controller
{
    private BookRepositoryInterface $bookRepositoryInterface;

    public function __construct(BookRepositoryInterface $bookRepositoryInterface)  {
        $this->bookRepositoryInterface = $bookRepositoryInterface;
    }
    //
    public function index(Request $request)
    {
        // if(! $request->account->can('publish articles', Book::class)) return ApiResponseClass::accessDenied();

        $data = $this->bookRepositoryInterface->index();
        return ApiResponseClass::sendResponse(BookResource::collection($data), '', 200);
    }

    public function store(StoreBookRequest $request){

        $details = [
            'name' => $request->name,
            'author' => $request->author,
            'publish_date' => $request->publish_date
        ];
        DB::beginTransaction();
        try{
            $book = $this->bookRepositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::sendResponse(new BookResource($book),'Product Create Success!',201);
        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    public function show($id) {
        $book = $this->bookRepositoryInterface->getById($id);
        return ApiResponseClass::sendResponse(new BookResource($book),'',200);
    }

    public function update(UpdateBookRequest $request, $id) {
        $details = [
            'name' => $request->name,
            'author' => $request->author,
            'publish_date' => $request->publish_date
        ];
        DB::beginTransaction();
        try{
            $book = $this->bookRepositoryInterface->update($details, $id);
            DB::commit();
            return ApiResponseClass::sendResponse(new BookResource($book),'Product Create Success!',201);
        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
        
    }

    public function destroy($id){
        $this->bookRepositoryInterface->destroy($id);
        return ApiResponseClass::sendResponse('Product Delete Success', '' , 204);

    }

}
