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

    public function __construct(BookRepositoryInterface $bookRepositoryInterface)
    {
        $this->bookRepositoryInterface = $bookRepositoryInterface;
    }
    //
    public function index(Request $request) : ApiResponseClass
    {
        // if(! $request->account->can('publish articles', Book::class)) return ApiResponseClass::accessDenied();

        $data = $this->bookRepositoryInterface->index();
        return ApiResponseClass::ok(BookResource::collection($data));
    }

    public function store(StoreBookRequest $request) : ApiResponseClass
    {

        $details = [
            'name' => $request->name,
            'author' => $request->author,
            'publish_date' => $request->publish_date
        ];
        DB::beginTransaction();
        try {
            $book = $this->bookRepositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::created(new BookResource($book));
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    public function show($id) : ApiResponseClass
    {
        $book = $this->bookRepositoryInterface->getById($id);
        return ApiResponseClass::ok(new BookResource($book));
    }

    public function update(UpdateBookRequest $request, $id) : ApiResponseClass
    {
        $details = [
            'name' => $request->name,
            'author' => $request->author,
            'publish_date' => $request->publish_date
        ];
        DB::beginTransaction();
        try {
            $book = $this->bookRepositoryInterface->update($details, $id);
            DB::commit();
            return $book ? ApiResponseClass::updated(new BookResource($request)) :
                ApiResponseClass::ok(['Record not found, no action taken.']);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    public function destroy($id) : ApiResponseClass
    {
        return $this->bookRepositoryInterface->destroy($id) ?
            ApiResponseClass::deleted('Record has been deleted.') : ApiResponseClass::ok(['Record not found, no action taken.']);
    }
}
