<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    private ApiResponse $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function addBook(Request $request): Response|Application|ResponseFactory
    {
        if (Auth::check()){
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'author' => 'required|string',
                'publication_date' => 'required|date',
                'isbn' =>'required|string',
                'genre' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->adminSendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $category = Book::where('title', $request->title)->first();
            if ($category) {
                return $this->apiResponse->adminSendResponse(403, "Book Already Exists", null);
            }

            try {
                $book = Book::create($request->all());
                return $this->apiResponse->adminSendResponse(200, "Book added successfully", $book);
            } catch (\Exception $e) {
                return $this->apiResponse->adminSendResponse(500, $e->getMessage(), $e->getTraceAsString());
            }
        } else {
            return $this->apiResponse->adminSendResponse(401, 'User unauthorized', null);
        }
    }

    public function editBook(Request $request): Response|Application|ResponseFactory
    {
        if (Auth::check()){
            $validator = Validator::make($request->all(), [
                'id' => 'required|string',
                'title' => 'required|string',
                'author' => 'required|string',
                'publication_date' => 'required|date',
                'isbn' =>'required|string',
                'genre' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->adminSendResponse(400, 'Parameters missing.', $validator->errors());
            }

            try {
                $findBook = Book::where('id' , $request->id);
                if ($findBook->first()) {
                    $findBook->update($request->all());
                    return $this->apiResponse->adminSendResponse(200, 'Book Update Successfully', $findBook->first());
                }
                return $this->apiResponse->adminSendResponse(404, 'Book not found', null);
            } catch (\Exception $e) {
                return $this->apiResponse->adminSendResponse(500, $e->getMessage(), $e->getTraceAsString());
            }
        } else {
            return $this->apiResponse->adminSendResponse(401, 'User unauthorized', null);
        }
    }

    public function deleteBook(Request $request): Response|Application|ResponseFactory
    {
        if(Auth::check()){
            $validator = Validator::make($request->all(), [
                'id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->adminSendResponse(400, 'Parameters missing.', $validator->errors());
            }

            try {
                $findBook = Book::where('id' , $request->id);
                if ($findBook->first()) {
                    $findBook->delete();
                    return $this->apiResponse->adminSendResponse(200, 'Book Delete Successfully', $findBook->first());
                }
                return $this->apiResponse->adminSendResponse(404, 'Book not found', null);
            } catch (\Exception $e) {
                return $this->apiResponse->adminSendResponse(500, $e->getMessage(), $e->getTraceAsString());
            }
        } else {
            return $this->apiResponse->adminSendResponse(401, 'User unauthorized', null);
        }
    }
}
