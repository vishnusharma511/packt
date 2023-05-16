<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeSenseSearchController extends Controller
{
    private ApiResponse $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function searchBooks(Request $request): Response|array|Application|ResponseFactory
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'search' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->adminSendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }
        try {
            $base_url = env('TYPESENSE_HOST');
            $key = env('TYPESENSE_KEY');
            $searchParameters = array(
                "searches" => array(
                    array(
                        "query_by"  => "title",
                        "sort_by"  => "",
                        "highlight_full_fields" => "title",
                        "collection" => "categories",
                        "q" => $request->search,
                        "facet_by" => "",
                        "page" => isset($request->page) ? $request->page : 1
                    )
                )
            );
            $query = json_encode($searchParameters);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://' . $base_url . '/multi_search?x-typesense-api-key=' . $key,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$query,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
              ));

            $response = curl_exec($curl);
            curl_close($curl);
            $ids = array();
            $items = json_decode($response);
            $data['current_page'] = isset($request->page) ? $request->page : 1;
            if(!isset($items) || $items->results[0]->found == 0) {
                $data['data'] = [];
                $data['last_page'] = 1;
                $data['total'] = !isset($items) ? 'Something with wrong' : $items->results[0]->found;
                return $data;
            } else {
                foreach ($items->results[0]->hits as $key => $result) {
                    array_push($ids, $result->document->id);
                }
            }

            if(isset($request->topic_id)) {
                $topics = $request->topic_id;
            }
            if(isset($request->course_level)) {
                $course_levels = $request->course_level;
            }
            if(($request->topic_id) && ($request->course_level)) {
                $categoryIds = CategoryTopic::whereIn('topic_id', $topics)->pluck('category_id');
                $data['data'] = Category::whereIn('id', $categoryIds)->whereIn('course_level', $course_levels)->whereIn('id', $ids)->get();
            } elseif($request->topic_id) {
                $categoryIds = CategoryTopic::whereIn('topic_id', $topics)->pluck('category_id');
                $data['data'] = Category::whereIn('id', $categoryIds)->whereIn('id', $ids)->get();
            } elseif($request->course_level) {
                $data['data'] = Category::whereIn('course_level', $course_levels)->whereIn('id', $ids)->get();
            } else {
                $data['data'] = Category::whereIn('id', $ids)->get();
            }
            $data['last_page'] = (int) (($items->results[0]->found%10 == 0) ? $items->results[0]->found / 10 : ($items->results[0]->found / 10) + 1);
            $data['total'] = $items->results[0]->found;
            return $data;
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->adminSendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
